<?php

namespace App\Controllers;

/**
 * Empty pages reserved for the other team members' modules.
 * They render the shared layout with an empty content area until each module is integrated.
 */
class PlaceholderController extends Controller
{
    private const FRONT_PAGES = [
        'home' => 'Home', 'shop' => 'Shop', 'health-advice' => 'Health advice',
        'quiz' => 'Quiz', 'events' => 'Events', 'bag' => 'Bag', 'my-orders' => 'My orders',
    ];

    private const BACK_PAGES = [
        'overview' => 'Overview', 'categories' => 'Categories', 'products' => 'Products',
        'orders' => 'Orders', 'articles' => 'Articles',
        'comments' => 'Comments', 'quizzes' => 'Quizzes', 'quiz-questions' => 'Quiz questions', 'events-admin' => 'Events',
        'registrations' => 'Registrations',
    ];

    public function front(): void
    {
        $this->page   = $this->currentKey(self::FRONT_PAGES, 'home');
        $this->title  = self::FRONT_PAGES[$this->page] . " | Par'Axis";
        $this->layout = 'front-layout';
        $this->render('placeholder');
    }

    public function back(): void
    {
        $this->page   = $this->currentKey(self::BACK_PAGES, 'overview');
        $this->title  = self::BACK_PAGES[$this->page] . ' | Back office';
        $this->layout = 'layout';
        $this->render('placeholder');
    }

    private function currentKey(array $pages, string $default): string
    {
        $key = $_GET['page'] ?? $default;
        return isset($pages[$key]) ? $key : $default;
    }
}
