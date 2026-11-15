<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;

/** Back office: view registrations (filter by event / status), cancel / re-register, delete. */
class RegistrationsController extends Controller
{
    protected string $page   = 'registrations';
    protected string $title  = "Registrations | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $eventId = (int) ($_GET['event'] ?? 0);
        $status  = $_GET['status'] ?? 'All';
        if (!in_array($status, ['All', 'registered', 'cancelled'], true)) {
            $status = 'All';
        }

        $registrations = EventRegistration::filter($eventId, $status);
        foreach ($registrations as &$r) {
            $r['event'] = Event::find((int) $r['event_id']); // join event_id -> event
        }
        unset($r);

        $this->render('registrations', [
            'registrations' => $registrations,
            'events'        => Event::all(),
            'eventId'       => $eventId,
            'status'        => $status,
            'flash'         => takeFlash(),
        ]);
    }

    /** Cancel <-> re-register (re-registering checks the capacity first) */
    public function toggle(): void
    {
        if ($this->isPost()) {
            $r  = EventRegistration::find((int) ($_POST['registration_id'] ?? 0));
            $ev = $r ? Event::find((int) $r['event_id']) : null;

            if ($r) {
                if ($r['status'] === 'cancelled' && $ev && Event::isFull($ev)) {
                    flash('This event is full', true);
                } else {
                    EventRegistration::update((int) $r['id'], ['status' => $r['status'] === 'registered' ? 'cancelled' : 'registered']);
                    flash('Registration updated');
                }
            }
        }
        $this->redirect('registrations', $this->filters());
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            EventRegistration::delete((int) ($_POST['registration_id'] ?? 0));
            flash('Registration deleted');
        }
        $this->redirect('registrations', $this->filters());
    }

    /** Keep the current filters after an action */
    private function filters(): array
    {
        return array_filter([
            'event'  => (int) ($_POST['event'] ?? 0) ?: null,
            'status' => ($_POST['status'] ?? 'All') !== 'All' ? $_POST['status'] : null,
        ]);
    }
}
