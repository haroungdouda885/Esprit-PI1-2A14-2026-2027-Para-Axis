<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Payment;

/** Front office: the customer's order history + cancellation of pending / processing orders. */
class MyOrdersController extends Controller
{
    protected string $page   = 'my-orders';
    protected string $title  = "My orders | Par'Axis";
    protected string $layout = 'front-layout';

    public function __construct()
    {
        $this->blockAdmin();
        if (!currentUser()) {
            $this->redirect('login', ['next' => 'my-orders']);
        }
    }

    public function index(): void
    {
        $user   = currentUser();
        $orders = Order::forUser((int) $user['id']);
        foreach ($orders as &$o) {
            $o['payment'] = Payment::forOrder((int) $o['id']); // join payment.order_id -> order.id
        }
        unset($o);

        $this->render('my-orders', [
            'user'    => $user,
            'orders'  => $orders,
            'ordered' => isset($_GET['ordered']),
            'flash'   => takeFlash(),
        ]);
    }

    public function cancel(): void
    {
        $user  = currentUser();
        $order = Order::find((int) ($_POST['order_id'] ?? 0));

        // check again ownership and status at action time: never trust the button alone
        if (!$this->isPost() || ($user['role'] ?? '') !== 'customer') {
            flash('Sign in with your customer account to cancel an order.', true);
        } elseif (!$order || (int) ($order['user_id'] ?? 0) !== (int) $user['id']) {
            flash('Order not found in your account.', true);
        } elseif (!Order::isCancellable($order)) {
            flash('This order can no longer be cancelled.', true);
        } else {
            // restore each purchased quantity once, before marking the order cancelled
            Order::moveStock($order, +1);

            $payment = Payment::forOrder((int) $order['id']);
            if ($payment) {
                $wasPaid = $payment['status'] === 'paid';
                Payment::update((int) $payment['id'], $wasPaid
                    ? ['status' => 'refunded', 'refunded_at' => date('Y-m-d H:i')]
                    : ['status' => 'failed']);
            }
            Order::update((int) $order['id'], ['status' => 'cancelled', 'cancelled_at' => date('Y-m-d H:i')]);
            flash('Order #' . $order['id'] . ' cancelled.');
        }
        $this->redirect('my-orders');
    }
}
