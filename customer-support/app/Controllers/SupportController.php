<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;

class SupportController extends Controller
{
    protected string $page   = 'support';
    protected string $title  = "Support | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $user    = $_SESSION['demo_user'] ?? null;
        $tickets = [];

        if ($user) {
            foreach (array_reverse(Ticket::forCustomer($user['email'] ?? '')) as $ticket) {
                $ticket['replies'] = TicketReply::forTicket((int) $ticket['id']);
                $tickets[] = $ticket;
            }
        }

        $this->render('support', ['user' => $user, 'tickets' => $tickets]);
    }

    public function create(): void
    {
        if ($this->isPost() && isset($_SESSION['demo_user'])) {
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');
            if ($subject !== '' && $message !== '') {
                Ticket::create(['subject' => $subject, 'message' => $message]);
            }
        }
        $this->redirect('support');
    }

    public function reply(): void
    {
        if ($this->isPost() && isset($_SESSION['demo_user'])) {
            $ticketId = (int) ($_POST['ticket_id'] ?? 0);
            $message  = trim($_POST['message'] ?? '');
            $ticket   = Ticket::find($ticketId);

            if ($ticket
                && ($ticket['customer_email'] ?? '') === ($_SESSION['demo_user']['email'] ?? '')
                && ($ticket['status'] ?? 'open') !== 'closed'
                && $message !== '') {
                TicketReply::add($ticketId, $message, 'customer');
            }
        }
        $this->redirect('support');
    }
}
