<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — Back office: user management (member 1).
 */
use App\Controllers\LoginController;
use App\Controllers\UsersAdminController;

return [
    // ---------- My part: back office user management (users member 1) ----------
    'users-admin' => [UsersAdminController::class, 'index'],
    'user-create' => [UsersAdminController::class, 'create'],
    'user-block'  => [UsersAdminController::class, 'block'],
    'user-role'   => [UsersAdminController::class, 'role'],
    'user-verify' => [UsersAdminController::class, 'verify'],
    'user-delete' => [UsersAdminController::class, 'delete'],

    // ---------- Shared base: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
