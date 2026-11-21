<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — Login, logout, sessions, remember me (member 3).
 */
use App\Controllers\LoginController;

return [
    // ---------- My part: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
