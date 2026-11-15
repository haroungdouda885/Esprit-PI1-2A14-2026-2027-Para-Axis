<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends Controller
{
    protected string $page   = 'login';
    protected string $title  = "Login | Par'Axis";
    protected string $layout = 'front-layout';

    public function login(): void
    {
        $message = isset($_GET['admin']) ? 'Back office access: log in with an admin account.' : '';
        $error   = false;
        $email   = '';

        if ($this->isPost()) {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $message = 'Please fill in your email and password.';
                $error   = true;
            } elseif ($user = User::check($email, $password)) {
                loginUser($user);
                // admins are sent to the back office, customers to the site
                $this->redirect($user['role'] === 'admin' ? 'products' : 'shop');
            } else {
                $message = 'Incorrect email or password.';
                $error   = true;
            }
        }

        $this->render('login', ['message' => $message, 'error' => $error, 'email' => $email]);
    }

    public function signup(): void
    {
        $this->title = "Sign up | Par'Axis";
        $message = '';
        $name = $email = '';

        if ($this->isPost()) {
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirmPassword'] ?? '';

            if (mb_strlen($name) < 2) {
                $message = 'Your name must have at least 2 characters.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Please enter a valid email address.';
            } elseif (strlen($password) < 8) {
                $message = 'The password must have at least 8 characters.';
            } elseif ($password !== $confirm) {
                $message = 'The passwords do not match.';
            } elseif (User::findByEmail($email)) {
                $message = 'An account with this email already exists. Please log in.';
            } else {
                loginUser(User::create($name, $email, $password)); // role is ALWAYS customer
                $this->redirect('shop');
            }
        }

        $this->render('signup', ['message' => $message, 'name' => $name, 'email' => $email]);
    }

    public function logout(): void
    {
        unset($_SESSION['demo_user']); // sign-up accounts stay in the session so you can log in again
        $this->redirect('shop');
    }
}
