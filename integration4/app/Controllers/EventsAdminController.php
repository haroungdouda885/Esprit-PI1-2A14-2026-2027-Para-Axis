<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\EventRegistration;

/** Back office: events list + statistics, create / edit / delete an event. */
class EventsAdminController extends Controller
{
    protected string $page   = 'events-admin';
    protected string $title  = "Events | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $events = Event::all();
        usort($events, fn($a, $b) => strtotime($b['event_date']) <=> strtotime($a['event_date'])); // latest date first
        foreach ($events as &$ev) {
            $ev['taken'] = Event::taken((int) $ev['id']);
            $ev['status'] = Event::status($ev);
        }
        unset($ev);

        $taken = EventRegistration::countRegistered();
        $seats = array_sum(array_map(fn($e) => (int) $e['capacity'], $events));
        $stats = [
            'events'    => count($events),
            'finished'  => count(array_filter($events, fn($e) => Event::isPast($e))),
            'upcoming'  => count(array_filter($events, fn($e) => (int) $e['active'] === 1 && !Event::isPast($e))),
            'taken'     => $taken,
            'cancelled' => count(EventRegistration::all()) - $taken,
            'seats'     => $seats,
            'filled'    => $seats ? round($taken / $seats * 100) : 0,
        ];

        // ?edit=ID opens the form with the event values
        $edit = isset($_GET['edit']) ? Event::find((int) $_GET['edit']) : null;

        $this->render('events-admin', [
            'events' => $events,
            'stats'  => $stats,
            'edit'   => $edit,
            'flash'  => takeFlash(),
        ]);
    }

    /** Create (no event_id) or edit (event_id) an event */
    public function save(): void
    {
        if ($this->isPost()) {
            $id   = (int) ($_POST['event_id'] ?? 0);
            $data = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'location'    => trim($_POST['location'] ?? ''),
                'event_date'  => trim($_POST['event_date'] ?? ''),
                'capacity'    => (int) ($_POST['capacity'] ?? 0),
                'active'      => isset($_POST['active']) ? 1 : 0,
            ];

            if ($data['title'] === '' || $data['location'] === '' || !strtotime($data['event_date']) || $data['capacity'] < 1) {
                flash('Please fill in the title, location, date and a capacity of at least 1.', true);
                $this->redirect('events-admin', $id ? ['edit' => $id] : []);
            }
            // an admin cannot set a capacity lower than the seats already taken
            if ($id && $data['capacity'] < Event::taken($id)) {
                flash('Capacity cannot be lower than the ' . Event::taken($id) . ' seats already taken.', true);
                $this->redirect('events-admin', ['edit' => $id]);
            }

            if ($id && Event::find($id)) {
                Event::update($id, $data);
            } else {
                Event::insert($data + ['created_at' => date('Y-m-d H:i:s')]);
            }
            flash('Event saved');
        }
        $this->redirect('events-admin');
    }

    /** Deletes the event and all its registrations */
    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) ($_POST['event_id'] ?? 0);
            Article::detachEvent($id); // articles stay, without event (ON DELETE SET NULL)
            Event::deleteWithRegistrations($id);
            flash('Event deleted');
        }
        $this->redirect('events-admin');
    }
}
