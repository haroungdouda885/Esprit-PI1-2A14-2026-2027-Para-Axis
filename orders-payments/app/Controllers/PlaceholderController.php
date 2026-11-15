<?php

namespace App\Controllers;

/**
 * Empty pages reserved for the other team members' modules.
 * They render the shared layout with an empty content area until each module is integrated.
 */
class PlaceholderController extends Controller
{
    private const FRONT_PAGES = [
        'home' => 'Home', 'health-advice' => 'Health advice', 'quiz' => 'Quiz', 'events' => 'Events', 'support' => 'Support',
    ];

    private const BACK_PAGES = [
        'overview' => 'Overview', 'admin' => 'Support tickets', 'categories' => 'Categories', 'products' => 'Products',
        'articles' => 'Articles', 'comments' => 'Comments', 'quizzes' => 'Quizzes', 'quiz-questions' => 'Quiz questions',
        'events-admin' => 'Events', 'registrations' => 'Registrations',
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
        $this->requireAdmin();
        $this->page   = $this->currentKey(self::BACK_PAGES, 'admin');
        $this->title  = self::BACK_PAGES[$this->page] . " | Par'Axis Back office";
        $this->layout = 'layout';
        $this->render('placeholder');
    }

    private function currentKey(array $pages, string $default): string
    {
        $key = $_GET['page'] ?? $default;
        return isset($pages[$key]) ? $key : $default;
    }
}
