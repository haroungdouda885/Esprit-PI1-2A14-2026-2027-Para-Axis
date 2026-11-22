<?php

namespace App\Controllers;

use App\Mailer;
use App\Models\AuthToken;
use App\Models\PasswordReset;
use App\Models\User;
use App\Security;

/**
 * MEMBER 4 - Forgot / reset password.
 * Routes: forgot-password (asks for the email), reset-password (page opened from the email link)
 */
class PasswordResetController extends Controller
{
    protected string $page   = 'login';
    protected string $title  = "Forgot password | Par'Axis";
    protected string $layout = 'front-layout';

    public function forgot(): void
    {
        $message = '';
        $error   = false;
        $email   = '';
        $sent    = false;

        if ($this->isPost()) {
            $email = trim($_POST['email'] ?? '');

            if ($err = Security::checkForm()) {
                $message = $err;
                $error   = true;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Please enter a valid email address.';
                $error   = true;
            } else {
                $user = User::findByEmail($email);
                if ($user && !$user['is_blocked']) {
                    $token = bin2hex(random_bytes(32));
                    PasswordReset::create((int) $user['id'], hash('sha256', $token), 60); // valid 60 minutes
                    $link = baseUrl() . '/index.php?page=reset-password&token=' . $token;
                    Mailer::send($user['email'], "Reset your Par'Axis password", Mailer::template(
                        'Reset your password',
                        'We received a request to reset your password. This link is valid for 60 minutes and can be used once.',
                        'Choose a new password',
                        $link
                    ));
                    // if sending fails the visitor still gets the same answer (never reveal who has an account);
                    // the reason is written in the PHP error log by Mailer
                }
                // Same answer whether the email exists or not (do not reveal who has an account)
                $sent = true;
            }
        }

        $this->render('forgot-password', [
            'message' => $message, 'error' => $error, 'email' => $email, 'sent' => $sent,
        ]);
    }

    public function reset(): void
    {
        $this->title = "New password | Par'Axis";
        $token = $_POST['token'] ?? $_GET['token'] ?? '';
        $token = is_string($token) ? $token : '';
        $row   = $token !== '' ? PasswordReset::findValid(hash('sha256', $token)) : null;
        $message = '';

        if ($row && $this->isPost()) {
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirmPassword'] ?? '';

            if ($err = Security::checkForm(false)) {
                $message = $err;
            } elseif ($err = Security::passwordError($password, $confirm)) {
                $message = $err;
            } else {
                $userId = (int) $row['user_id'];
                User::updatePassword($userId, $password);
                PasswordReset::markUsed((int) $row['id']);
                AuthToken::deleteForUser($userId); // "remember me" devices must log in again
                if ($owner = User::findById($userId)) {
                    Mailer::sendPasswordChanged($owner['email'], $owner['name']); // warns the owner if it was not him
                }
                flash('Your password was updated. You can log in now.');
                $this->redirect('login');
            }
        }

        $this->render('reset-password', ['valid' => $row !== null, 'token' => $token, 'message' => $message]);
    }
}
