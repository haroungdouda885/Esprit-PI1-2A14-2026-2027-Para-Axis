<?php

namespace App\Controllers;

use App\Models\Association;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\Product;

/** Back office (member 3): list of the donations, change the status, delete. */
class DonationsAdminController extends Controller
{
    protected string $page   = 'donations-admin';
    protected string $title  = "Donations | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin();
    }

    /** READ: filters association / type / status + search */
    public function index(): void
    {
        $filters = $this->filters();
        $rows = Donation::filter($filters['association'], $filters['type'], $filters['status'], $filters['q']);
        foreach ($rows as &$d) {
            $d['association_name'] = Association::find((int) $d['association_id'])['name'] ?? 'Deleted association';
            $d['items']            = DonationItem::detailed((int) $d['id']);
        }
        unset($d);

        $all = Donation::all();
        $counted = array_filter($all, fn($d) => in_array($d['status'], Donation::COUNTED, true));
        $stats = [
            'total'    => count($all),
            'pending'  => count(Donation::where('status', 'pending')),
            'money'    => round(array_sum(array_map(fn($d) => (float) $d['amount'], array_filter($counted, fn($d) => $d['type'] === 'money'))), 2),
            'products' => round(array_sum(array_map(fn($d) => (float) $d['amount'], array_filter($counted, fn($d) => $d['type'] === 'product'))), 2),
        ];

        $this->render('donations-admin', [
            'rows'         => $rows,
            'stats'        => $stats,
            'filters'      => $filters,
            'associations' => Association::all(),
            'flash'        => takeFlash(),
        ]);
    }

    /**
     * UPDATE the status. Rules:
     *  - a delivered donation cannot be cancelled (the products are already given);
     *  - cancelling puts the reserved products back in stock, un-cancelling takes them again (if there is enough stock).
     */
    public function status(): void
    {
        $d      = Donation::find((int) ($_POST['donation_id'] ?? 0));
        $status = $_POST['status'] ?? '';

        if ($this->isPost() && $d && in_array($status, Donation::STATUSES, true) && $status !== $d['status']) {
            if ($d['status'] === 'delivered' && $status === 'cancelled') {
                flash('A delivered donation cannot be cancelled.', true);
            } elseif ($d['status'] === 'cancelled' && $d['type'] === 'product' && !$this->stockAvailable((int) $d['id'])) {
                flash('Not enough stock to re-open this product donation.', true);
            } else {
                if ($status === 'cancelled') {
                    Donation::moveStock((int) $d['id'], +1);
                } elseif ($d['status'] === 'cancelled') {
                    Donation::moveStock((int) $d['id'], -1);
                }
                Donation::update((int) $d['id'], ['status' => $status]);
                flash('Donation #' . $d['id'] . ' → ' . $status);
            }
        }
        $this->redirect('donations-admin', $this->filtersFromPost());
    }

    /** DELETE the donation and its items; reserved (not delivered) products go back in stock */
    public function delete(): void
    {
        if ($this->isPost() && ($d = Donation::find((int) ($_POST['donation_id'] ?? 0)))) {
            if (in_array($d['status'], ['pending', 'confirmed'], true)) {
                Donation::moveStock((int) $d['id'], +1);
            }
            Donation::deleteWithItems((int) $d['id']);
            flash('Donation deleted');
        }
        $this->redirect('donations-admin', $this->filtersFromPost());
    }

    private function stockAvailable(int $donationId): bool
    {
        foreach (DonationItem::forDonation($donationId) as $i) {
            if ((int) (Product::find((int) $i['product_id'])['stock'] ?? 0) < (int) $i['quantity']) {
                return false;
            }
        }
        return true;
    }

    private function filters(): array
    {
        $type   = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        return [
            'association' => (int) ($_GET['association'] ?? 0),
            'type'        => isset(Donation::TYPES[$type]) ? $type : '',
            'status'      => in_array($status, Donation::STATUSES, true) ? $status : '',
            'q'           => trim($_GET['q'] ?? ''),
        ];
    }

    /** keeps the filters of the list after a POST action */
    private function filtersFromPost(): array
    {
        return array_filter([
            'association' => (int) ($_POST['f_association'] ?? 0) ?: null,
            'type'        => $_POST['f_type'] ?? null,
            'status'      => $_POST['f_status'] ?? null,
            'q'           => $_POST['f_q'] ?? null,
        ], fn($v) => $v !== null && $v !== '');
    }
}
