<?php
/**
 * Routing table: page key => [Controller class, method]
 */
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\PlaceholderController;
use App\Controllers\SupportController;

return [
    // ---------- Front office : Customer Support (my module) ----------
    'support'        => [SupportController::class, 'index'],
    'create-ticket'  => [SupportController::class, 'create'],
    'customer-reply' => [SupportController::class, 'reply'],

    // ---------- Authentication (demo) ----------
    'login'  => [AuthController::class, 'login'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Support tickets (my module) ----------
    'admin'         => [AdminController::class, 'index'],
    'admin-reply'   => [AdminController::class, 'reply'],
    'ticket-status' => [AdminController::class, 'status'],
    'ticket-delete' => [AdminController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'shop'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'quiz'          => [PlaceholderController::class, 'front'],
    'events'        => [PlaceholderController::class, 'front'],
    'bag'           => [PlaceholderController::class, 'front'],
    'my-orders'     => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'      => [PlaceholderController::class, 'back'],
    'categories'    => [PlaceholderController::class, 'back'],
    'products'      => [PlaceholderController::class, 'back'],
    'orders'        => [PlaceholderController::class, 'back'],
    'articles'      => [PlaceholderController::class, 'back'],
    'comments'      => [PlaceholderController::class, 'back'],
    'quizzes'       => [PlaceholderController::class, 'back'],
    'quiz-questions' => [PlaceholderController::class, 'back'],
    'events-admin'  => [PlaceholderController::class, 'back'],
    'registrations' => [PlaceholderController::class, 'back'],
];
