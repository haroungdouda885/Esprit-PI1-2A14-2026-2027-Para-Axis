<?php

namespace App\Controllers;

use App\Models\Association;
use App\Models\Category;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\Product;

/**
 * Front office of the Donations module (member 3):
 * browse the associations, give money or products, my donations, cancel a pending donation.
 */
class DonationsController extends Controller
{
    protected string $page   = 'donations';
    protected string $title  = "Donations | Par'Axis";
    protected string $layout = 'front-layout';

    /** Associations list: category filter + search, and the products that can be given */
    public function index(): void
    {
        $category = (int) ($_GET['category'] ?? 0);
        if (!Category::findOfType($category, 'association')) {
            $category = 0;
        }
        $q = trim($_GET['q'] ?? '');

        $associations = Association::search($category, $q);
        foreach ($associations as &$a) {
            $a['received'] = Association::totalReceived((int) $a['id']);
            $a['count']    = Association::donationCount((int) $a['id']);
        }
        unset($a);

        // latest confirmed / delivered donations (public wall, anonymous donors hidden)
        $latest = array_slice(array_values(array_filter(Donation::filter(), fn($d) => in_array($d['status'], Donation::COUNTED, true))), 0, 5);
        foreach ($latest as &$d) {
            $d['association'] = Association::find((int) $d['association_id'])['name'] ?? 'Association';
        }
        unset($d);

        $this->render('donations', [
            'associations' => $associations,
            'category'     => $category,
            'categories'   => Category::ofType('association'),
            'q'            => $q,
            'user'         => currentUser(),
            'products'     => array_values(array_filter(Product::all(), fn($p) => (int) $p['stock'] > 0)),
            'latest'       => $latest,
            'flash'        => takeFlash(),
        ], 'donations-scripts');
    }

    /** Creates a Donation (+ its DonationItem rows for a product donation) */
    public function donate(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->redirect('login', ['next' => 'donations']);
        }
        if (!$this->isPost()) {
            $this->redirect('donations');
        }

        $association = Association::find((int) ($_POST['association_id'] ?? 0));
        $type        = $_POST['type'] ?? '';
        $name        = trim($_POST['donor_name'] ?? '');
        $email       = trim($_POST['donor_email'] ?? '');
        $message     = trim($_POST['message'] ?? '');

        if (!$association || (int) $association['active'] !== 1) {
            $this->back('This association is not available.');
        }
        if (!isset(Donation::TYPES[$type])) {
            $this->back('Please choose what you want to give: money or products.');
        }
        if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->back('Please fill in your name and a valid email.');
        }
        if (mb_strlen($message) > 500) {
            $this->back('Your message is too long (500 characters max).');
        }

        $items  = [];
        $amount = 0.0;
        if ($type === 'money') {
            $amount = round((float) str_replace(',', '.', (string) ($_POST['amount'] ?? '0')), 2);
            if ($amount < 1 || $amount > 10000) {
                $this->back('The amount must be between 1 and 10 000 TND.');
            }
        } else {
            foreach ((array) ($_POST['qty'] ?? []) as $productId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) {
                    continue;
                }
                $product = Product::find((int) $productId);
                if (!$product) {
                    $this->back('A product of your donation does not exist anymore.');
                }
                if ($qty > (int) $product['stock']) {
                    $this->back('Only ' . (int) $product['stock'] . ' "' . $product['name'] . '" left in stock.');
                }
                $items[] = ['product_id' => (int) $product['id'], 'quantity' => $qty];
                $amount += $qty * (float) $product['price'];
            }
            if (!$items) {
                $this->back('Choose at least one product and its quantity.');
            }
        }

        $donation = Donation::insert([
            'user_id'        => (int) $user['id'],
            'association_id' => (int) $association['id'],
            'donor_name'     => $name,
            'donor_email'    => $email,
            'type'           => $type,
            'amount'         => round($amount, 2),
            'message'        => $message,
            'anonymous'      => isset($_POST['anonymous']) ? 1 : 0,
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
        foreach ($items as $item) {
            DonationItem::insert(['donation_id' => (int) $donation['id']] + $item);
        }
        Donation::moveStock((int) $donation['id'], -1); // the given products are reserved

        flash('Thank you! Your donation to ' . $association['name'] . ' is registered.');
        $this->redirect('my-donations');
    }

    /** My donations (the "Donations" menu stays active) */
    public function mine(): void
    {
        $this->title = "My donations | Par'Axis";
        $user = currentUser();
        $rows = [];
        if ($user) {
            foreach (Donation::forUser((int) $user['id']) as $d) {
                $d['association'] = Association::find((int) $d['association_id']);
                $d['items']       = DonationItem::detailed((int) $d['id']);
                $rows[] = $d;
            }
        }
        $this->render('my-donations', ['user' => $user, 'rows' => $rows, 'flash' => takeFlash()]);
    }

    /** The donor cancels a pending donation: the reserved products go back in stock */
    public function cancel(): void
    {
        $user = currentUser();
        if ($this->isPost() && $user) {
            $d = Donation::find((int) ($_POST['donation_id'] ?? 0));
            if (!$d || (int) ($d['user_id'] ?? 0) !== (int) $user['id']) {
                flash('Donation not found in your account.', true);
            } elseif (!in_array($d['status'], Donation::CANCELLABLE, true)) {
                flash('Only a pending donation can be cancelled.', true);
            } else {
                Donation::update((int) $d['id'], ['status' => 'cancelled']);
                Donation::moveStock((int) $d['id'], +1);
                flash('Donation cancelled');
            }
        }
        $this->redirect('my-donations');
    }

    private function back(string $error): void
    {
        flash($error, true);
        $this->redirect('donations');
    }
}
