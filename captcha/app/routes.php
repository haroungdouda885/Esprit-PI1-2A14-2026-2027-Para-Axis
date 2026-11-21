<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — CAPTCHA + security (member 5).
 */
use App\Controllers\CaptchaController;
use App\Controllers\LoginController;

return [
    // ---------- My part: CAPTCHA image (users member 5) ----------
    'captcha' => [CaptchaController::class, 'image'],

    // ---------- Shared base: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
