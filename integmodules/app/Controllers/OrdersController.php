<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Payment;

/** Back office: orders and payments (status of the order, status of the payment, delete). */
class OrdersController extends Controller
{
    protected string $page   = 'orders';
    protected string $title  = "Orders | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $filter = $_GET['status'] ?? 'All';
        if (!in_array($filter, Order::STATUSES, true)) {
            $filter = 'All';
        }

        $orders = array_reverse(Order::all()); // newest first
        if ($filter !== 'All') {
            $orders = array_values(array_filter($orders, fn($o) => $o['status'] === $filter));
        }
        foreach ($orders as &$o) {
            $o['payment'] = Payment::forOrder((int) $o['id']); // join payment.order_id -> order.id
        }
        unset($o);

        $this->render('orders', [
            'orders'      => $orders,
            'filter'      => $filter,
            'statuses'    => Order::STATUSES,
            'payStatuses' => Payment::STATUSES,
            'pending'     => count(Order::where('status', 'pending')),
            'flash'       => takeFlash(),
        ]);
    }

    /** Order status. Cancelling puts the items back in stock, un-cancelling takes them again. */
    public function status(): void
    {
        $order  = Order::find((int) ($_POST['order_id'] ?? 0));
        $status = $_POST['status'] ?? '';

        if ($this->isPost() && $order && in_array($status, Order::STATUSES, true) && $status !== $order['status']) {
            if ($status === 'cancelled') {
                Order::moveStock($order, +1);
            } elseif ($order['status'] === 'cancelled') {
                Order::moveStock($order, -1);
            }
            Order::update((int) $order['id'], ['status' => $status]);
            flash('Order #' . $order['id'] . ' → ' . $status);
        }
        $this->back();
    }

    public function payment(): void
    {
        $payment = Payment::find((int) ($_POST['payment_id'] ?? 0));
        $status  = $_POST['status'] ?? '';

        if ($this->isPost() && $payment && in_array($status, Payment::STATUSES, true)) {
            $data = ['status' => $status];
            if ($status === 'paid') {
                $data['paid_at'] = date('Y-m-d H:i');
            }
            Payment::update((int) $payment['id'], $data);
            flash('Payment updated');
        }
        $this->back();
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            Order::deleteWithChildren((int) ($_POST['order_id'] ?? 0));
            flash('Order deleted');
        }
        $this->back();
    }

    /** Back to the list, keeping the status filter */
    private function back(): void
    {
        $filter = $_POST['filter'] ?? 'All';
        $this->redirect('orders', in_array($filter, Order::STATUSES, true) ? ['status' => $filter] : []);
    }
}
