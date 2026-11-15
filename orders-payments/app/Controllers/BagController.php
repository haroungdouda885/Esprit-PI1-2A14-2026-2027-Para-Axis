<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Payment;
use App\Models\Product;

/**
 * Front office: shopping bag + checkout (order + order lines + payment).
 * All payments are SIMULATED: no bank, no payment provider, card numbers are never stored.
 */
class BagController extends Controller
{
    protected string $page   = 'bag';
    protected string $title  = "Bag | Par'Axis";
    protected string $layout = 'front-layout';

    /** Demo card that is always declined */
    private const DECLINED_CARD = '4000000000000002';

    public function __construct()
    {
        $this->blockAdmin();
        Cart::initDemoOnce();
    }

    public function index(array $data = []): void
    {
        $this->render('bag', $data + [
            'lines'   => Cart::lines(),
            'total'   => Cart::total(),
            'user'    => currentUser(),
            'methods' => Payment::METHODS,
            'flash'   => takeFlash(),
            'address' => '',
            'method'  => 'card',
        ], 'bag-scripts');
    }

    /** + / − buttons of a bag line */
    public function update(): void
    {
        $product = Product::find((int) ($_POST['product_id'] ?? 0));

        if ($this->isPost() && $product) {
            $next = Cart::quantity((int) $product['id']) + (int) ($_POST['delta'] ?? 0);
            if ($next > $product['stock']) {
                flash('Only ' . $product['stock'] . ' left in stock', true);
            } else {
                Cart::set((int) $product['id'], $next);
            }
        }
        $this->redirect('bag');
    }

    public function checkout(): void
    {
        $user = currentUser();
        if (!$this->isPost()) {
            $this->redirect('bag');
        }
        if (!$user) {
            $this->redirect('login', ['next' => 'bag']);
        }

        $lines   = Cart::lines();
        $address = trim($_POST['address'] ?? '');
        $method  = $_POST['method'] ?? 'card';

        $error = $this->checkoutError($lines, $address, $method);
        if ($error) {
            // show the bag again with the message (address and method are kept, card fields are not)
            $this->index(['error' => $error, 'address' => $address, 'method' => $method]);
            return;
        }

        $total = Cart::total();
        $now   = date('Y-m-d H:i');
        $items = array_map(fn($l) => [
            'product_id' => (int) $l['product']['id'],
            'name'       => $l['product']['name'],
            'price'      => (float) $l['product']['price'],
            'qty'        => $l['qty'],
            'line_total' => $l['line_total'],
        ], $lines);

        // 1. Order
        $order = Order::insert([
            'user_id'          => $user['id'],
            'customer_name'    => $user['name'],
            'customer_email'   => $user['email'],
            'shipping_address' => $address,
            'items'            => $items,
            'total'            => $total,
            'status'           => 'pending',
            'created_at'       => $now,
        ]);

        // 2. Order lines (order_line.order_id -> order.id)
        foreach ($items as $i) {
            OrderLine::insert([
                'order_id'     => $order['id'],
                'product_id'   => $i['product_id'],
                'product_name' => $i['name'],
                'unit_price'   => $i['price'],
                'quantity'     => $i['qty'],
                'line_total'   => $i['line_total'],
            ]);
        }

        // 3. Payment: demo card = paid, cash on delivery / bank transfer = pending
        $paid = $method === 'card';
        Payment::insert([
            'order_id'              => $order['id'],
            'amount'                => $total,
            'method'                => $method,
            'status'                => $paid ? 'paid' : 'pending',
            'paid_at'               => $paid ? $now : null,
            'transaction_reference' => $paid ? 'DEMO-' . time() : null,
        ]);

        // 4. Stock goes down, bag is emptied
        Order::moveStock($order, -1);
        Cart::clear();

        $this->redirect('my-orders', ['ordered' => $order['id']]);
    }

    /** Returns an error message, or null when the order can be placed */
    private function checkoutError(array $lines, string $address, string $method): ?string
    {
        if (!$lines) {
            return 'Your bag is empty';
        }
        if ($address === '') {
            return 'Enter a delivery address';
        }
        if (!isset(Payment::METHODS[$method])) {
            return 'Choose a payment method';
        }
        foreach ($lines as $l) {
            if ($l['product']['stock'] < $l['qty']) {
                return 'Insufficient stock for ' . $l['product']['name'];
            }
        }
        if ($method === 'card') {
            $number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
            if (!$this->validCard($number, trim($_POST['expiry'] ?? ''), trim($_POST['cvv'] ?? ''))) {
                return 'Enter valid demo card details and a future expiry date';
            }
            if ($number === self::DECLINED_CARD) {
                return 'Demo payment declined. Try 4242 4242 4242 4242.';
            }
        }
        return null;
    }

    /** 16 digits + Luhn check, MM/YY not in the past, CVV of 3 or 4 digits */
    private function validCard(string $number, string $expiry, string $cvv): bool
    {
        if (!preg_match('/^\d{16}$/', $number) || !preg_match('/^\d{3,4}$/', $cvv)
            || !preg_match('/^(\d{2})\/(\d{2})$/', $expiry, $m)) {
            return false;
        }

        $sum = 0;
        $double = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];
            if ($double) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
            $double = !$double;
        }

        $month = (int) $m[1];
        $year  = 2000 + (int) $m[2];
        $now   = (int) date('Y') * 12 + (int) date('n');

        return $sum % 10 === 0 && $month >= 1 && $month <= 12 && $year * 12 + $month >= $now;
    }
}
