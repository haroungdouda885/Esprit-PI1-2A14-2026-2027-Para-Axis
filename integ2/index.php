<?php
/**
 * Front controller (single entry point) for the front office AND the back office.
 * URL format: index.php?page=<page key>
 */
session_start();

// Autoloader: App\Controllers\EventsController -> app/Controllers/EventsController.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/app/helpers.php';

$routes = require __DIR__ . '/app/routes.php';
$page   = $_GET['page'] ?? 'events';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'events';
}

[$class, $method] = $routes[$page];
(new $class())->$method();
