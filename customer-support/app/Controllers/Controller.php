<?php

namespace App\Controllers;

abstract class Controller
{
    /** Clé de la page courante, utilisée par la sidebar / navbar pour l'état actif */
    protected string $page = 'support';

    /** Titre affiché dans <title> */
    protected string $title = "Par'Axis";

    /** Layout utilisé : 'layout' (back office) ou 'front-layout' (front office) */
    protected string $layout = 'layout';

    /**
     * Rend une vue de contenu (app/Views/pages/{$view}.php) dans le layout commun.
     *
     * @param string      $view        nom du fichier de vue (sans .php) dans Views/pages
     * @param array       $data        variables à extraire dans la vue de contenu
     * @param string|null $scriptsView nom du fichier de script spécifique (sans .php) — optionnel
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

    /** Exécute une vue et renvoie son HTML au lieu de l'afficher. */
    protected function capture(string $__file, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $__file;
        return ob_get_clean();
    }

    /** Redirige vers une autre page de l'application. */
    protected function redirect(string $page): void
    {
        header('Location: index.php?page=' . $page);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
