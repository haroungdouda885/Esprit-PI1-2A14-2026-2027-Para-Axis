<?php
/**
 * Front controller (single entry point) for the front office AND the back office.
 * URL format: index.php?page=<page key>
 */
// MEMBER 5 - safer session cookie (not readable by JavaScript, not sent from other sites)
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// Autoloader: App\Controllers\ShopController -> app/Controllers/ShopController.php
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
$page   = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'home';
}

// The admin can LOOK at the whole front office (shop, events, donations…) but never act as a customer:
// every customer action below is refused for him, even if a form is sent by hand.
const CUSTOMER_ACTIONS = [
    'bag-add', 'bag', 'bag-update', 'checkout', 'my-orders', 'order-cancel',   // Orders (member 3)
    'donate', 'my-donations', 'donation-cancel',                                // Donations (member 3)
    'add-comment',                                                              // Health advice (member 4)
    'quiz-submit',                                                              // Quiz (member 5)
    'event-register', 'my-registrations', 'registration-cancel',                // Events (member 6)
    'create-ticket', 'customer-reply',                                          // Support (member 1)
];
if (isAdmin() && in_array($page, CUSTOMER_ACTIONS, true)) {
    flash('Admin view is read-only: customer actions are disabled for admins.', true);
    $back = parse_url($_SERVER['HTTP_REFERER'] ?? '');
    $same = ($back['host'] ?? '') === ($_SERVER['HTTP_HOST'] ?? '') || ($back['host'] ?? '') === parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
    header('Location: ' . ($same && !empty($back['query']) ? 'index.php?' . $back['query'] : 'index.php?page=home'));
    exit;
}

[$class, $method] = $routes[$page];
(new $class())->$method();
