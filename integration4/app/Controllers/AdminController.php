<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;

/** BACK office — Support tickets (member 1). Admins only. */
class AdminController extends Controller
{
    protected string $page   = 'admin';
    protected string $title  = "Support tickets | Par'Axis Back office";
    protected string $layout = 'layout';

    public function index(): void
    {
        $this->requireAdmin();

        $tickets = [];
        foreach (array_reverse(Ticket::all()) as $ticket) {
            $ticket['replies'] = TicketReply::forTicket((int) ($ticket['id'] ?? 0));
            $ticket['product'] = Ticket::productOf($ticket); // Ticket "concerne" 0..1 Product
            $tickets[] = $ticket;
        }

        $this->render('admin', ['tickets' => $tickets]);
    }

    public function reply(): void
    {
        $this->requireAdmin();
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
        $this->requireAdmin();
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
        $this->requireAdmin();
        if ($this->isPost()) {
            Ticket::deleteWithReplies((int) ($_POST['ticket_id'] ?? 0));
        }
        $this->redirect('admin');
    }
}
