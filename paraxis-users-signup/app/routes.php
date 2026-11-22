<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — Sign up + email verification (member 2).
 */
use App\Controllers\LoginController;
use App\Controllers\SignupController;

return [
    // ---------- My part: sign up + email verification (users member 2) ----------
    'signup'              => [SignupController::class, 'signup'],
    'verify-email'        => [SignupController::class, 'verify'],
    'resend-verification' => [SignupController::class, 'resend'],

    // ---------- Shared base: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
