<?php

namespace App\Controllers;

use App\Models\Association;
use App\Models\Category;
use App\Models\Donation;

/** Back office (member 3): CRUD of the associations (sponsors / partners that receive the donations). */
class AssociationsController extends Controller
{
    protected string $page   = 'associations';
    protected string $title  = "Associations | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin();
    }

    /** READ: list + statistics; ?edit=ID opens the form with the association values */
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $associations = array_reverse(Association::all());
        foreach ($associations as &$a) {
            $a['received'] = Association::totalReceived((int) $a['id']);
            $a['count']    = Association::donationCount((int) $a['id']);
        }
        unset($a);

        $counted = array_filter(Donation::all(), fn($d) => in_array($d['status'], Donation::COUNTED, true));
        $stats = [
            'total'    => count($associations),
            'active'   => count(array_filter($associations, fn($a) => (int) $a['active'] === 1)),
            'received' => round(array_sum(array_map(fn($d) => (float) $d['amount'], $counted)), 2),
            'donors'   => count(array_unique(array_map(fn($d) => mb_strtolower($d['donor_email']), Donation::all()))),
        ];

        $this->render('associations', [
            'associations' => $associations,
            'stats'        => $stats,
            'edit'         => isset($_GET['edit']) ? Association::find((int) $_GET['edit']) : null,
            'categories'   => Category::ofType('association'),
            'flash'        => takeFlash(),
        ]);
    }

    /** CREATE (no association_id) or UPDATE (association_id) */
    public function save(): void
    {
        if ($this->isPost()) {
            $id   = (int) ($_POST['association_id'] ?? 0);
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category_id' => (int) ($_POST['category_id'] ?? 0),
                'email'       => trim($_POST['email'] ?? ''),
                'phone'       => trim($_POST['phone'] ?? ''),
                'address'     => trim($_POST['address'] ?? ''),
                'logo'        => trim($_POST['logo'] ?? ''),
                'active'      => isset($_POST['active']) ? 1 : 0,
            ];

            $error = null;
            if (mb_strlen($data['name']) < 3) {
                $error = 'The name must have at least 3 characters.';
            } elseif (!Category::findOfType($data['category_id'], 'association')) {
                $error = 'Please choose a category (add association categories in Categories).';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email.';
            } elseif ($data['phone'] !== '' && !preg_match('/^[0-9 +]{8,15}$/', $data['phone'])) {
                $error = 'The phone must contain 8 to 15 digits.';
            } elseif ($data['logo'] !== '' && !filter_var($data['logo'], FILTER_VALIDATE_URL)) {
                $error = 'The logo must be a link (https://...) or stay empty.';
            } elseif (mb_strlen($data['description']) < 10) {
                $error = 'The description must have at least 10 characters.';
            } else {
                foreach (Association::all() as $other) { // name must be unique
                    if ((int) $other['id'] !== $id && mb_strtolower($other['name']) === mb_strtolower($data['name'])) {
                        $error = 'An association with this name already exists.';
                    }
                }
            }
            if ($error) {
                flash($error, true);
                $this->redirect('associations', $id ? ['edit' => $id] : []);
            }

            if ($id && Association::find($id)) {
                Association::update($id, $data);
                flash('Association updated');
            } else {
                Association::insert($data + ['created_at' => date('Y-m-d H:i:s')]);
                flash('Association created');
            }
        }
        $this->redirect('associations');
    }

    /** Show / hide the association in the front office */
    public function toggle(): void
    {
        if ($this->isPost() && ($a = Association::find((int) ($_POST['association_id'] ?? 0)))) {
            Association::update((int) $a['id'], ['active' => (int) $a['active'] === 1 ? 0 : 1]);
            flash((int) $a['active'] === 1 ? 'Association hidden' : 'Association visible');
        }
        $this->redirect('associations');
    }

    /** DELETE: only an association without donations (otherwise hide it, the history is kept) */
    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) ($_POST['association_id'] ?? 0);
            if (Association::donationCount($id) > 0) {
                flash('This association has received donations: hide it instead of deleting it.', true);
            } else {
                Association::delete($id);
                flash('Association deleted');
            }
        }
        $this->redirect('associations');
    }
}
