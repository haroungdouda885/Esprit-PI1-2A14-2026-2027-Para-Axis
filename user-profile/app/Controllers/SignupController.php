<?php

namespace App\Controllers;

use App\Mailer;
use App\Models\User;
use App\Security;

/**
 * MEMBER 2 - Sign up + email verification.
 * Routes: signup, verify-email (link received by email), resend-verification
 */
class SignupController extends Controller
{
    protected string $page   = 'login';
    protected string $title  = "Sign up | Par'Axis";
    protected string $layout = 'front-layout';

    public function signup(): void
    {
        $message = '';
        $name = $email = $phone = '';
        $next    = safeNext();
        $done    = false;
        $mailFailed = false;

        if ($this->isPost()) {
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');          // what the person typed (shown again if there is an error)
            $phoneOk  = normalizeTnPhone($phone);          // "+21622123456" or null
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirmPassword'] ?? '';

            if ($err = Security::checkForm()) {
                $message = $err;
            } elseif (mb_strlen($name) < 2) {
                $message = 'Your name must have at least 2 characters.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Please enter a valid email address.';
            } elseif ($phoneOk === null) {
                $message = 'Please enter a valid Tunisian phone number (8 digits, e.g. 22 123 456).';
            } elseif ($err = Security::passwordError($password, $confirm)) {
                $message = $err;
            } elseif (User::findByEmail($email)) {
                $message = 'An account with this email already exists. Please log in.';
            } else {
                try {
                    $token = bin2hex(random_bytes(32));
                    $user  = User::create($name, $email, $password, $phoneOk, $token); // role is ALWAYS customer
                    $link  = baseUrl() . '/index.php?page=verify-email&token=' . $token;
                    $mailFailed = !Mailer::send($email, "Confirm your Par'Axis account", Mailer::template(
                        'Welcome to Par\'Axis, ' . $user['name'] . '!',
                        'Please confirm your email address to activate your account and place orders.',
                        'Confirm my email',
                        $link
                    ));
                    loginUser($user);
                    $done    = true;
                } catch (\PDOException $e) {
                    // the UNIQUE index on users.email caught a double click / simultaneous sign up
                    $message = 'An account with this email already exists. Please log in.';
                }
            }
        }

        $this->render('signup', [
            'message' => $message, 'name' => $name, 'email' => $email, 'phone' => $phone,
            'next' => $next, 'done' => $done, 'mailFailed' => $mailFailed,
            'mailError' => ($mailFailed && showDebug()) ? Mailer::$lastError : '',
        ]);
    }

    /** Page opened from the email link (?token=...), or from the site when the account is still unverified */
    public function verify(): void
    {
        $this->title = "Confirm your email | Par'Axis";
        $token = $_GET['token'] ?? '';
        $flash = takeFlash();

        if (is_string($token) && $token !== '') {
            $userId = User::verifyEmail($token);
            if ($userId !== null && (int) (currentUser()['id'] ?? 0) === $userId) {
                $_SESSION['demo_user']['is_verified'] = 1; // the person is logged in: unlock orders right away
            }
            $state = $userId !== null ? 'success' : 'invalid';
        } elseif (currentUser() && !isVerified()) {
            $state = 'pending';
        } else {
            $this->redirect('my-account');
        }

        $this->render('verify-email', ['state' => $state, 'flash' => $flash]);
    }

    /** POST: sends a new confirmation email to the logged-in, unverified user */
    public function resend(): void
    {
        $u = currentUser();
        if (!$u || !$this->isPost()) {
            $this->redirect('login');
        }
        if ($err = Security::checkForm(false)) {
            flash($err, true);
            $this->redirect('verify-email');
        }
        $row = User::findById((int) $u['id']);
        if ($row && !$row['is_verified']) {
            $token = bin2hex(random_bytes(32));
            User::setVerifyToken((int) $row['id'], $token);
            $link = baseUrl() . '/index.php?page=verify-email&token=' . $token;
            $sent = Mailer::send($row['email'], "Confirm your Par'Axis account", Mailer::template(
                'Confirm your email', 'Click the button to activate your account.', 'Confirm my email', $link
            ));
            if ($sent) {
                flash('A new confirmation email was sent to ' . $row['email'] . '.');
            } else {
                flash('Sorry, the email could not be sent right now. Please try again in a few minutes.'
                    . (showDebug() ? ' Reason: ' . Mailer::$lastError : ''), true);
            }
        }
        $this->redirect('verify-email');
    }
}
