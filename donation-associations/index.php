<?php
/**
 * Front controller (single entry point) for the front office AND the back office.
 * URL format: index.php?page=<page key>
 */
session_start();

// Autoloader: App\Controllers\DonationsController -> app/Controllers/DonationsController.php
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
$page   = $_GET['page'] ?? 'donations';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'donations';
}

// The admin can LOOK at the donations page but never act as a donor (refused even if a form is sent by hand)
if (isAdmin() && in_array($page, ['donate', 'my-donations', 'donation-cancel'], true)) {
    flash('Admin view is read-only: donating is disabled for admins.', true);
    header('Location: index.php?page=donations');
    exit;
}

[$class, $method] = $routes[$page];
(new $class())->$method();
