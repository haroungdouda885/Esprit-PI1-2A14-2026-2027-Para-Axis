<?php
/**
 * Front controller (single entry point) for the front office AND the back office.
 * URL format: index.php?page=<page key>
 */
// MEMBER 5 - safer session cookie (not readable by JavaScript, not sent from other sites)
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// Autoloader: App\Controllers\LoginController -> app/Controllers/LoginController.php
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

// MEMBER 3 - log the user back in if he ticked "Remember me" (does nothing without the cookie)
\App\Auth::restore();

$routes = require __DIR__ . '/app/routes.php';
$page   = $_GET['page'] ?? 'signup';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'signup';
}

[$class, $method] = $routes[$page];
(new $class())->$method();
