<?php

namespace App\Controllers;

class AuthController extends Controller
{
    protected string $page   = 'login';
    protected string $title  = "Login | Par'Axis";
    protected string $layout = 'front-layout';

    public function login(): void
    {
        if ($this->isPost()) {
            $role = ($_POST['role'] ?? 'customer') === 'admin' ? 'admin' : 'customer';
            $_SESSION['demo_user'] = [
                'name'  => trim($_POST['name'] ?? 'Demo Customer'),
                'email' => trim($_POST['email'] ?? 'customer@example.test'),
                'role'  => $role,
            ];
            $this->redirect($role === 'admin' ? 'admin' : 'support');
        }

        $this->render('login');
    }

    public function logout(): void
    {
        unset($_SESSION['demo_user']);
        $this->redirect('login');
    }
}
