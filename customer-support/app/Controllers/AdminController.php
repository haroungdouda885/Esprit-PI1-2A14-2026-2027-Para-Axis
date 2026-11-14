<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;

class AdminController extends Controller
{
    protected string $page   = 'admin';
    protected string $title  = 'Support tickets | Back office';
    protected string $layout = 'layout';

    public function index(): void
    {
        $tickets = [];
        foreach (array_reverse(Ticket::all()) as $ticket) {
            $ticket['replies'] = TicketReply::forTicket((int) ($ticket['id'] ?? 0));
            $tickets[] = $ticket;
        }

        $this->render('admin', ['tickets' => $tickets]);
    }

    public function reply(): void
    {
        if ($this->isPost()) {
            $id      = (int) ($_POST['ticket_id'] ?? 0);
            $message = trim($_POST['message'] ?? '');
            if ($message !== '' && Ticket::find($id)) {
                TicketReply::add($id, $message, 'admin');
                Ticket::updateStatus($id, 'answered');
            }
        }
        $this->redirect('admin');
    }

    public function status(): void
    {
        if ($this->isPost()) {
            $status = $_POST['status'] ?? 'open';
            if (!in_array($status, ['open', 'answered', 'closed'], true)) {
                $status = 'open';
            }
            Ticket::updateStatus((int) ($_POST['ticket_id'] ?? 0), $status);
        }
        $this->redirect('admin');
    }

    public function delete(): void
    {
        if (($_SESSION['demo_user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->redirect('support');
        }
        if ($this->isPost()) {
            Ticket::delete((int) ($_POST['ticket_id'] ?? 0));
        }
        $this->redirect('admin');
    }
}
