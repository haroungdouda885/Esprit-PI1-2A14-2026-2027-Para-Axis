<?php
/* Small utility functions shared by the views and controllers. */

/** 'active' if this link is the current page */
function isActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active' : '';
}

/** 'active' if the current page is one of the pages of a menu group */
function groupIsActive(array $pages, string $currentPage): string
{
    return in_array($currentPage, $pages, true) ? 'active' : '';
}

/** Escape text before printing it */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Logged-in user, or null */
function currentUser(): ?array
{
    return $_SESSION['demo_user'] ?? null;
}

/** MEMBER 3 - Save the user in the session (without the password). A new session id is created at every login. */
function loginUser(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['demo_user'] = [
        'id'          => (int) $u['id'],
        'name'        => $u['name'],
        'email'       => $u['email'],
        'phone'       => $u['phone'] ?? '',
        'role'        => $u['role'],
        'is_verified' => (int) ($u['is_verified'] ?? 0),
    ];
}

/**
 * MEMBER 3 - Log out. Only the user is removed from the session: the other modules (cart, tickets...)
 * still keep their data in the session until they move to the database, so we do NOT call session_destroy().
 */
function logoutUser(): void
{
    unset($_SESSION['demo_user']);
    session_regenerate_id(true);
}

/** MEMBER 2 - true when the logged-in user confirmed his email (Orders can use it to block checkout) */
function isVerified(): bool
{
    return !empty(currentUser()['is_verified']);
}

/** true when the logged-in user is an admin */
function isAdmin(): bool
{
    return (currentUser()['role'] ?? '') === 'admin';
}

/** 18.9 -> "18.900 TND" (Catalog / Orders) */
function money($amount): string
{
    return number_format((float) $amount, 3, '.', ' ') . ' TND';
}

/** "2026-12-05T10:00" -> "Dec 5, 2026, 10:00" (Events) */
function fmtDate(?string $date): string
{
    return $date ? date('M j, Y, H:i', strtotime($date)) : '—';
}

/** One-time message shown on the next page (after a redirect) */
function flash(string $message, bool $error = false): void
{
    $_SESSION['flash'] = ['message' => $message, 'error' => $error];
}

/** Reads (and removes) the one-time message */
function takeFlash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** MEMBER 1 - Reads a value of app/config.php with dots: config('db.host'), config('recaptcha.site_key') */
function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }
    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** MEMBER 2 - "http://localhost/paraxis" (used to build the links sent by email) */
function baseUrl(): string
{
    $url = rtrim((string) config('app.url'), '/');
    if ($url === '') { // automatic: http://localhost/<this folder> (works in htdocs and with php -S)
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $dir   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $url   = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $dir;
    }
    return $url;
}

/** Page to open after login / sign up (?next=...), otherwise the shop. Only these pages are allowed. */
function safeNext(): string
{
    $allowed = ['bag', 'my-orders', 'events', 'my-registrations', 'support', 'health-advice', 'quiz', 'my-account'];
    $next    = $_POST['next'] ?? $_GET['next'] ?? '';
    return in_array($next, $allowed, true) ? $next : 'my-account';
}

/** "22 123 456", "+216 22123456", "0021622123456" -> "+21622123456". Returns null if it is not a Tunisian number. */
function normalizeTnPhone(string $raw): ?string
{
    $digits = preg_replace('/[\s.\-()]/', '', $raw);
    return preg_match('/^(?:\+216|00216)?([2-579]\d{7})$/', $digits, $m) ? '+216' . $m[1] : null;
}

/** true in development: dev_show_links is on, OR the site is opened from this PC (localhost). Used to show why an email failed. */
function showDebug(): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return (bool) config('app.dev_show_links') || in_array($ip, ['127.0.0.1', '::1'], true);
}
