<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Ticket;
use App\Models\TicketReply;

/** FRONT office — Customer support (member 1) */
class SupportController extends Controller
{
    protected string $page   = 'support';
    protected string $title  = "Support | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $user    = currentUser();
        $tickets = [];

        if ($user) {
            foreach (array_reverse(Ticket::forCustomer($user['email'] ?? '')) as $ticket) {
                $ticket['replies'] = TicketReply::forTicket((int) $ticket['id']);
                $ticket['product'] = Ticket::productOf($ticket);
                $tickets[] = $ticket;
            }
        }

        $this->render('support', [
            'user'     => $user,
            'tickets'  => $tickets,
            'products' => Product::all(),
            'selected' => (int) ($_GET['product'] ?? 0), // "Ask a question" link of a product page
        ]);
    }

    public function create(): void
    {
        $user = currentUser();
        if ($this->isPost() && $user) {
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $productId = (int) ($_POST['product_id'] ?? 0);
            $productId = $productId && Product::find($productId) ? $productId : null; // optional, must exist
            if ($subject !== '' && $message !== '') {
                Ticket::open($user, $subject, $message, $productId);
            }
        }
        $this->redirect('support');
    }

    public function reply(): void
    {
        $user = currentUser();
        if ($this->isPost() && $user) {
            $ticketId = (int) ($_POST['ticket_id'] ?? 0);
            $message  = trim($_POST['message'] ?? '');
            $ticket   = Ticket::find($ticketId);

            // a customer can only answer his own ticket, and only while it is not closed
            if ($ticket
                && ($ticket['customer_email'] ?? '') === ($user['email'] ?? '')
                && ($ticket['status'] ?? 'open') !== 'closed'
                && $message !== '') {
                TicketReply::add($ticketId, $message, 'customer');
            }
        }
        $this->redirect('support');
    }
}
