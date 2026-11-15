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
