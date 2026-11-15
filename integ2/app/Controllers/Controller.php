<?php

namespace App\Controllers;

abstract class Controller
{
    /** Key of the current page, used by the sidebar / navbar for the active state */
    protected string $page = 'events';

    /** Title displayed in <title> */
    protected string $title = "Par'Axis";

    /** Layout used: 'layout' (back office) or 'front-layout' (front office) */
    protected string $layout = 'front-layout';

    /**
     * Renders a content view (app/Views/pages/{$view}.php) inside the common layout.
     *
     * @param string      $view        name of the view file (without .php) in Views/pages
     * @param array       $data        variables to extract into the content view
     * @param string|null $scriptsView name of a page-specific script file (without .php) — optional
     */
    protected function render(string $view, array $data = [], ?string $scriptsView = null): void
    {
        $content      = $this->capture(__DIR__ . '/../Views/pages/' . $view . '.php', $data);
        $extraScripts = $scriptsView
            ? $this->capture(__DIR__ . '/../Views/pages/' . $scriptsView . '.php', $data)
            : '';

        $title       = $this->title;
        $currentPage = $this->page;

        require_once __DIR__ . '/../Views/' . $this->layout . '.php';
    }

    /** Runs a view file and returns its HTML instead of printing it. */
    protected function capture(string $__file, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $__file;
        return ob_get_clean();
    }

    /** Redirects to another page of the application. */
    protected function redirect(string $page, array $params = []): void
    {
        $query = $params ? '&' . http_build_query($params) : '';
        header('Location: index.php?page=' . $page . $query);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Back office = admins only: anyone else is sent to the login page. */
    protected function requireAdmin(): void
    {
        if ((currentUser()['role'] ?? '') !== 'admin') {
            $this->redirect('login', ['admin' => 1]);
        }
    }
}
