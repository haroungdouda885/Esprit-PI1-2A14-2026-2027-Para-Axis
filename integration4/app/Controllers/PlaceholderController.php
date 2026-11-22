<?php

namespace App\Controllers;

/**
 * Empty back-office page (Overview): all six modules and the Home page are integrated, only this page is left to build.
 * It renders the shared layout with an empty content area.
 */
class PlaceholderController extends Controller
{
    private const BACK_PAGES = [
        'overview' => 'Overview',
    ];

    public function back(): void
    {
        $this->requireAdmin();
        $this->page   = $this->currentKey(self::BACK_PAGES, 'overview');
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
