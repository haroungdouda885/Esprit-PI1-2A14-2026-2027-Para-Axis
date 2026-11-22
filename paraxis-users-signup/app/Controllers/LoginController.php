<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use App\Security;

/**
 * MEMBER 3 - Login / logout.
 * Routes: login, logout
 */
class LoginController extends Controller
{
    protected string $page   = 'login';
    protected string $title  = "Login | Par'Axis";
    protected string $layout = 'front-layout';

    public function login(): void
    {
        $message = isset($_GET['admin']) ? 'Back office access: log in with an admin account.' : '';
        $error   = false;
        $email   = '';
        $next    = safeNext();

        // message coming from another page (e.g. "Password updated" after a reset)
        if ($flash = takeFlash()) {
            $message = $flash['message'];
            $error   = !empty($flash['error']);
        }

        if ($this->isPost()) {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $error    = true;

            if ($err = Security::checkForm()) {
                $message = $err;
            } elseif ($email === '' || $password === '') {
                $message = 'Please fill in your email and password.';
            } elseif (Security::isLockedOut($email)) {
                $message = 'Too many failed attempts. Please try again in ' . Security::lockoutMinutes() . ' minutes.';
            } elseif ($user = User::check($email, $password)) {
                if ($user['is_blocked']) {
                    $message = 'This account has been blocked. Please contact support.';
                } else {
                    Security::clearAttempts($email);
                    loginUser($user);
                    if (!empty($_POST['remember'])) {
                        Auth::remember((int) $user['id']);
                    }
                    // admins are sent to the back office, customers back to the page they came from (shop by default)
                    $this->redirect($user['role'] === 'admin' ? 'verify-email' : $next);
                }
            } else {
                Security::recordFailure($email);
                $message = 'Incorrect email or password.';
            }
        }

        $this->render('login', ['message' => $message, 'error' => $error, 'email' => $email, 'next' => $next]);
    }

    public function logout(): void
    {
        Auth::forget();
        logoutUser();
        $this->redirect('login');
    }
}
