<?php

namespace App\Controllers;

/**
 * Home page of the front office (shared): hero "Your daily dose of well-being".
 */
class HomeController extends Controller
{
    protected string $page   = 'home';
    protected string $title  = "Par'Axis | Your everyday wellness store";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $this->render('home');
    }
}
