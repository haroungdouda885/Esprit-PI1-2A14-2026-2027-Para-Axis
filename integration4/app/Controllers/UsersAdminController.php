<?php

namespace App\Controllers;

use App\Models\AuthToken;
use App\Models\User;
use App\Security;

/**
 * MEMBER 1 - BACK office: user management (admins only).
 * Routes: users-admin (list + search + filters), user-create, user-block, user-role, user-verify, user-delete
 * Every action is a POST with a CSRF token, then goes back to the list.
 */
class UsersAdminController extends Controller
{
    protected string $page   = 'users-admin';
    protected string $title  = "Users | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $q      = trim($_GET['q'] ?? '');
        $role   = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        $this->render('users-admin', [
            'users'  => User::search($q, is_string($role) ? $role : '', is_string($status) ? $status : ''),
            'counts' => User::counts(),
            'q'      => $q,
            'role'   => $role,
            'status' => $status,
            'me'     => (int) currentUser()['id'],
            'flash'  => takeFlash(),
        ]);
    }

    /** New account created by an admin (customer or admin) */
    public function create(): void
    {
        $this->requirePost();
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $phoneOk  = $phone === '' ? null : normalizeTnPhone($phone);
        $role     = $_POST['role'] ?? 'customer';
        $password = $_POST['password'] ?? '';

        if (mb_strlen($name) < 2) {
            $this->fail('The name must have at least 2 characters.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Please enter a valid email address.');
        }
        if ($phone !== '' && $phoneOk === null) {
            $this->fail('Please enter a valid Tunisian phone number.');
        }
        if (!in_array($role, ['customer', 'admin'], true)) {
            $this->fail('Invalid role.');
        }
        if ($err = Security::passwordError($password, $password)) { // no confirmation field here
            $this->fail($err);
        }
        if (User::emailTaken($email)) {
            $this->fail('An account with this email already exists.');
        }

        try {
            User::createWithRole($name, $email, $password, $phoneOk, $role);
        } catch (\PDOException $e) {
            $this->fail('An account with this email already exists.');
        }
        flash('User created: ' . $name . ' (' . $role . ').');
        $this->redirect('users-admin');
    }

    public function block(): void
    {
        $u = $this->target();
        if ((int) $u['id'] === $this->me()) {
            $this->fail('You cannot block your own account.');
        }
        $block = !$u['is_blocked'];
        User::setBlocked((int) $u['id'], $block);
        if ($block) {
            AuthToken::deleteForUser((int) $u['id']); // "remember me" devices stop working
        }
        flash($u['name'] . ($block ? ' was blocked.' : ' was unblocked.'));
        $this->redirect('users-admin');
    }

    public function role(): void
    {
        $u    = $this->target();
        $role = $_POST['role'] ?? '';

        if (!in_array($role, ['customer', 'admin'], true)) {
            $this->fail('Invalid role.');
        }
        if ((int) $u['id'] === $this->me()) {
            $this->fail('You cannot change your own role.');
        }
        if ($u['role'] === 'admin' && $role !== 'admin' && User::countAdmins() <= 1) {
            $this->fail('There must always be at least one admin.');
        }
        User::setRole((int) $u['id'], $role);
        flash($u['name'] . ' is now ' . $role . '.');
        $this->redirect('users-admin');
    }

    public function verify(): void
    {
        $u = $this->target();
        User::markVerified((int) $u['id']);
        flash('Email of ' . $u['name'] . ' marked as confirmed.');
        $this->redirect('users-admin');
    }

    public function delete(): void
    {
        $u = $this->target();
        if ((int) $u['id'] === $this->me()) {
            $this->fail('You cannot delete your own account.');
        }
        if ($u['role'] === 'admin' && User::countAdmins() <= 1) {
            $this->fail('There must always be at least one admin.');
        }
        User::delete((int) $u['id']); // addresses and tokens are removed by ON DELETE CASCADE
        flash('Account of ' . $u['name'] . ' deleted.');
        $this->redirect('users-admin');
    }

    // ---------- helpers ----------

    private function me(): int
    {
        return (int) (currentUser()['id'] ?? 0);
    }

    /** POST + CSRF only (anything else goes back to the list) */
    private function requirePost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('users-admin');
        }
        if ($err = Security::checkForm(false)) {
            $this->fail($err);
        }
    }

    /** The user targeted by the button (field user_id) */
    private function target(): array
    {
        $this->requirePost();
        $user = User::findById((int) ($_POST['user_id'] ?? 0));
        if (!$user) {
            $this->fail('User not found.');
        }
        return $user;
    }

    private function fail(string $message): void
    {
        flash($message, true);
        $this->redirect('users-admin');
    }
}
