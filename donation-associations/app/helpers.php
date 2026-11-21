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

/** Save the user in the session (without the password) */
function loginUser(array $u): void
{
    $_SESSION['demo_user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
}

/** true when the logged-in user is an admin */
function isAdmin(): bool
{
    return (currentUser()['role'] ?? '') === 'admin';
}

/** Date shown in the tables: "Nov 21, 2026, 14:30" */
function fmtDate(?string $date): string
{
    return $date ? date('M j, Y, H:i', strtotime($date)) : '—';
}

/** One message shown once on the next page (toast) */
function flash(string $message, bool $error = false): void
{
    $_SESSION['flash'] = ['message' => $message, 'error' => $error];
}

function takeFlash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
