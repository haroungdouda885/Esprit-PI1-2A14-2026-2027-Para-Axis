<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — Forgot / reset password (member 4).
 */
use App\Controllers\LoginController;
use App\Controllers\PasswordResetController;

return [
    // ---------- My part: forgot / reset password (users member 4) ----------
    'forgot-password' => [PasswordResetController::class, 'forgot'],
    'reset-password'  => [PasswordResetController::class, 'reset'],

    // ---------- Shared base: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
