<?php
/**
 * Front controller (single entry point).
 * URL format: index.php?page=<page key>
 */
session_start();

// Small autoloader: App\Controllers\SupportController -> app/Controllers/SupportController.php
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

$routes = require __DIR__ . '/app/routes.php';
$page   = $_GET['page'] ?? 'support';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'support';
}

[$class, $method] = $routes[$page];
(new $class())->$method();
