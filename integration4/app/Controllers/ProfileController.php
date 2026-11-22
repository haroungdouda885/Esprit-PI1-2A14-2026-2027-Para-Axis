<?php

namespace App\Controllers;

use App\Auth;
use App\Mailer;
use App\Models\Address;
use App\Models\AuthToken;
use App\Models\User;
use App\Security;

/**
 * MEMBER 6 - "My account": profile, change password, delivery addresses, delete account.
 * Routes: my-account, profile-update, change-password, address-save, address-default, address-delete, account-delete
 * (every route except my-account only accepts POST + CSRF token and then goes back to my-account)
 */
class ProfileController extends Controller
{
    protected string $page   = 'my-account';
    protected string $title  = "My account | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $user   = $this->requireLogin();
        $userId = (int) $user['id'];
        $editId = (int) ($_GET['edit'] ?? 0);

        $this->render('my-account', [
            'user'         => $user,
            'addresses'    => Address::forUser($userId),
            'edit'         => $editId ? Address::find($editId, $userId) : null,
            'governorates' => Address::GOVERNORATES,
            'flash'        => takeFlash(),
        ]);
    }

    public function update(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();
        $id    = (int) $user['id'];
        $name  = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = normalizeTnPhone(trim($_POST['phone'] ?? ''));

        if (mb_strlen($name) < 2) {
            $this->fail('Your name must have at least 2 characters.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Please enter a valid email address.');
        }
        if ($phone === null) {
            $this->fail('Please enter a valid Tunisian phone number (8 digits, e.g. 22 123 456).');
        }
        if (User::emailTaken($email, $id)) {
            $this->fail('Another account already uses this email.');
        }

        $emailChanged = $email !== $user['email'];
        User::updateProfile($id, $name, $email, $phone);

        $message = 'Your profile was updated.';
        if ($emailChanged) {
            // new email = must be confirmed again (member 2's verification flow)
            $token = bin2hex(random_bytes(32));
            User::setVerifyToken($id, $token);
            $link = baseUrl() . '/index.php?page=verify-email&token=' . $token;
            Mailer::send($email, "Confirm your new Par'Axis email", Mailer::template(
                'Confirm your new email', 'Click the button to confirm this email address.', 'Confirm my email', $link
            ));
            $message .= ' Please confirm your new email address: we sent you a link.';
        }

        loginUser(User::findById($id)); // refresh the data kept in the session
        flash($message);
        $this->redirect('my-account');
    }

    public function password(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['password'] ?? '';
        $confirm = $_POST['confirmPassword'] ?? '';

        if (!password_verify($current, $user['password_hash'])) {
            $this->fail('Your current password is not correct.');
        }
        if ($err = Security::passwordError($new, $confirm)) {
            $this->fail($err);
        }

        User::updatePassword((int) $user['id'], $new);
        AuthToken::deleteForUser((int) $user['id']); // other "remembered" devices must log in again
        Mailer::sendPasswordChanged($user['email'], $user['name']);
        Auth::forget();
        flash('Your password was changed.');
        $this->redirect('my-account');
    }

    public function saveAddress(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();
        $userId = (int) $user['id'];
        $id     = (int) ($_POST['id'] ?? 0);

        $street = trim($_POST['street'] ?? '');
        $city   = trim($_POST['city'] ?? '');
        $gov    = trim($_POST['governorate'] ?? '');
        $postal = trim($_POST['postal_code'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');
        $phoneOk = $phone === '' ? null : normalizeTnPhone($phone);

        if (mb_strlen($street) < 5) {
            $this->fail('Please enter the street and number of your address.');
        }
        if (mb_strlen($city) < 2) {
            $this->fail('Please enter your city.');
        }
        if (!in_array($gov, Address::GOVERNORATES, true)) {
            $this->fail('Please choose a governorate.');
        }
        if ($postal !== '' && !preg_match('/^\d{4}$/', $postal)) {
            $this->fail('The postal code must have 4 digits.');
        }
        if ($phone !== '' && $phoneOk === null) {
            $this->fail('Please enter a valid Tunisian phone number for this address.');
        }

        $data = [
            'street' => $street, 'city' => $city, 'governorate' => $gov,
            'postal_code' => $postal === '' ? null : $postal, 'phone' => $phoneOk,
        ];

        if ($id) {
            if (!Address::find($id, $userId)) {
                $this->fail('Address not found.');
            }
            Address::update($id, $userId, $data);
            flash('Address updated.');
        } else {
            if (Address::countForUser($userId) >= Address::MAX_PER_USER) {
                $this->fail('You can save up to ' . Address::MAX_PER_USER . ' addresses.');
            }
            Address::create($userId, $data);
            flash('Address added.');
        }
        $this->redirect('my-account');
    }

    public function setDefaultAddress(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();
        Address::setDefault((int) ($_POST['id'] ?? 0), (int) $user['id']);
        flash('Default address changed.');
        $this->redirect('my-account');
    }

    public function deleteAddress(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();
        Address::delete((int) ($_POST['id'] ?? 0), (int) $user['id']);
        flash('Address deleted.');
        $this->redirect('my-account');
    }

    public function deleteAccount(): void
    {
        $user = $this->requireLogin();
        $this->requirePost();

        if ($user['role'] === 'admin') {
            $this->fail('An admin account cannot be deleted here.');
        }
        if (!password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            $this->fail('Wrong password: your account was not deleted.');
        }

        User::delete((int) $user['id']); // addresses and tokens are removed by ON DELETE CASCADE
        Auth::forget();
        logoutUser();
        flash('Your account was deleted.');
        $this->redirect('login');
    }

    // ---------- helpers ----------

    /** Must be logged in: returns the user row from the database */
    private function requireLogin(): array
    {
        $u = currentUser();
        if (!$u) {
            $this->redirect('login', ['next' => 'my-account']);
        }
        $row = User::findById((int) $u['id']);
        if (!$row) { // account deleted or blocked meanwhile
            logoutUser();
            $this->redirect('login');
        }
        return $row;
    }

    /** Only POST requests with a valid CSRF token are accepted */
    private function requirePost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('my-account');
        }
        if ($err = Security::checkForm(false)) {
            $this->fail($err);
        }
    }

    /** Shows an error on the account page and stops */
    private function fail(string $message): void
    {
        flash($message, true);
        $this->redirect('my-account');
    }
}
