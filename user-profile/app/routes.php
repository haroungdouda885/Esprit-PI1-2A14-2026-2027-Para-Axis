<?php
/**
 * Routing table: page key => [Controller class, method]
 * Users module — My account: profile, password, addresses, delete account (member 6).
 */
use App\Controllers\LoginController;
use App\Controllers\ProfileController;
use App\Controllers\SignupController;

return [
    // ---------- My part: My account (users member 6) ----------
    'my-account'      => [ProfileController::class, 'index'],
    'profile-update'  => [ProfileController::class, 'update'],
    'change-password' => [ProfileController::class, 'password'],
    'address-save'    => [ProfileController::class, 'saveAddress'],
    'address-default' => [ProfileController::class, 'setDefaultAddress'],
    'address-delete'  => [ProfileController::class, 'deleteAddress'],
    'account-delete'  => [ProfileController::class, 'deleteAccount'],

    // ---------- Used when the email is changed: link to confirm the new email (from member 2) ----------
    'verify-email'        => [SignupController::class, 'verify'],
    'resend-verification' => [SignupController::class, 'resend'],

    // ---------- Shared base: login / logout (users member 3) ----------
    'login'  => [LoginController::class, 'login'],
    'logout' => [LoginController::class, 'logout'],

];
