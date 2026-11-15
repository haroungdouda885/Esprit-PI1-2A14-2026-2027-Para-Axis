<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\AuthController;
use App\Controllers\BagController;
use App\Controllers\MyOrdersController;
use App\Controllers\OrdersController;
use App\Controllers\PlaceholderController;
use App\Controllers\ShopController;

return [
    // ---------- Front office : Orders and payments (my module) ----------
    'shop'      => [ShopController::class, 'index'],
    'bag-add'   => [ShopController::class, 'add'],
    'bag-demo'  => [ShopController::class, 'demo'],
    'bag'       => [BagController::class, 'index'],
    'bag-update' => [BagController::class, 'update'],
    'checkout'  => [BagController::class, 'checkout'],
    'my-orders' => [MyOrdersController::class, 'index'],
    'order-cancel' => [MyOrdersController::class, 'cancel'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Orders and payments (my module) ----------
    'orders'         => [OrdersController::class, 'index'],
    'order-status'   => [OrdersController::class, 'status'],
    'payment-status' => [OrdersController::class, 'payment'],
    'order-delete'   => [OrdersController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'quiz'          => [PlaceholderController::class, 'front'],
    'events'        => [PlaceholderController::class, 'front'],
    'support'       => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'       => [PlaceholderController::class, 'back'],
    'admin'          => [PlaceholderController::class, 'back'],
    'categories'     => [PlaceholderController::class, 'back'],
    'products'       => [PlaceholderController::class, 'back'],
    'articles'       => [PlaceholderController::class, 'back'],
    'comments'       => [PlaceholderController::class, 'back'],
    'quizzes'        => [PlaceholderController::class, 'back'],
    'quiz-questions' => [PlaceholderController::class, 'back'],
    'events-admin'   => [PlaceholderController::class, 'back'],
    'registrations'  => [PlaceholderController::class, 'back'],
];
