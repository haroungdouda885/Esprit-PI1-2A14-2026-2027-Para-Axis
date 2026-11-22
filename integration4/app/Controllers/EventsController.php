<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\EventRegistration;

/** Front office of the Events module: browse events, register, my registrations, cancel a registration. */
class EventsController extends Controller
{
    protected string $page   = 'events';
    protected string $title  = "Events | Par'Axis";
    protected string $layout = 'front-layout';

    /** Browse events: filter Upcoming / Past / All + search */
    public function index(): void
    {
        $filter = $_GET['filter'] ?? 'upcoming';
        if (!in_array($filter, ['upcoming', 'past', 'all'], true)) {
            $filter = 'upcoming';
        }
        $q    = trim($_GET['q'] ?? '');
        $user = currentUser();

        $events = Event::search($filter, $q);
        foreach ($events as &$ev) {
            $id           = (int) $ev['id'];
            $ev['taken']  = Event::taken($id);
            $ev['full']   = $ev['taken'] >= (int) $ev['capacity'];
            $ev['past']   = Event::isPast($ev);
            $mine         = $user ? EventRegistration::mine($id, (int) $user['id']) : null;
            $ev['isMine'] = $mine && $mine['status'] === 'registered';
            $ev['articles'] = Article::forEvent($id); // Article "est présenté par" Event
        }
        unset($ev);

        $this->render('events', [
            'events' => $events,
            'filter' => $filter,
            'q'      => $q,
            'user'   => $user,
            'flash'  => takeFlash(),
        ], 'events-scripts');
    }

    /** Register to an event (creates an Event_Registration row, or re-activates a cancelled one) */
    public function register(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->redirect('login', ['next' => 'events']);
        }

        if ($this->isPost()) {
            $ev = Event::find((int) ($_POST['event_id'] ?? 0));
            $row = [
                'participant_name'  => trim($_POST['name'] ?? ''),
                'participant_email' => trim($_POST['email'] ?? ''),
                'phone'             => trim($_POST['phone'] ?? ''),
            ];

            if (!$ev || (int) $ev['active'] !== 1) {
                flash('Event not found.', true);
            } elseif (mb_strlen($row['participant_name']) < 2 || !filter_var($row['participant_email'], FILTER_VALIDATE_EMAIL) || $row['phone'] === '') {
                flash('Please fill in your name, a valid email and your phone.', true);
            } elseif (Event::isPast($ev)) {
                flash('This event is already finished', true);
            } elseif (Event::isFull($ev)) {
                flash('Sorry, this event is full', true);
            } else {
                $old = EventRegistration::mine((int) $ev['id'], (int) $user['id']);
                if ($old && $old['status'] === 'registered') {
                    flash('You are already registered', true);
                } else {
                    $row += ['event_id' => (int) $ev['id'], 'user_id' => (int) $user['id'], 'status' => 'registered'];
                    if ($old) {
                        EventRegistration::update((int) $old['id'], $row);
                    } else {
                        EventRegistration::insert($row + ['created_at' => date('Y-m-d H:i:s')]);
                    }
                    flash('Registration confirmed. See you there!');
                }
            }
        }
        $this->redirect('events');
    }

    /** My registrations (the "Events" menu stays active) */
    public function mine(): void
    {
        $this->title = "My registrations | Par'Axis";
        $user = currentUser();
        $rows = [];

        if ($user) {
            foreach (EventRegistration::forUser((int) $user['id']) as $r) {
                $ev = Event::find((int) $r['event_id']); // join event_id -> event
                if ($ev) {
                    $r['event'] = $ev;
                    $r['past']  = Event::isPast($ev);
                    $rows[] = $r;
                }
            }
        }

        $this->render('my-registrations', ['user' => $user, 'rows' => $rows, 'flash' => takeFlash()]);
    }

    /** Cancel a registration: the seat is released */
    public function cancel(): void
    {
        $user = currentUser();
        if ($this->isPost() && $user) {
            $r  = EventRegistration::find((int) ($_POST['registration_id'] ?? 0));
            $ev = $r ? Event::find((int) $r['event_id']) : null;

            if (!$r || (int) ($r['user_id'] ?? 0) !== (int) $user['id']) {
                flash('Registration not found in your account.', true);
            } elseif ($r['status'] !== 'registered' || !$ev || Event::isPast($ev)) {
                flash('This registration can no longer be cancelled.', true);
            } else {
                EventRegistration::update((int) $r['id'], ['status' => 'cancelled']);
                flash('Registration cancelled');
            }
        }
        $this->redirect('my-registrations');
    }
}
