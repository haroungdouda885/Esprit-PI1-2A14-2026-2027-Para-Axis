<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\PlaceholderController;
use App\Controllers\ProductsController;
use App\Controllers\ShopController;

return [
    // ---------- Front office : Catalog (my module) ----------
    'shop'    => [ShopController::class, 'index'],
    'product' => [ShopController::class, 'show'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Catalog (my module) ----------
    'categories'      => [CategoriesController::class, 'index'],
    'category-create' => [CategoriesController::class, 'create'],
    'category-delete' => [CategoriesController::class, 'delete'],
    'products'        => [ProductsController::class, 'index'],
    'product-create'  => [ProductsController::class, 'create'],
    'product-stock'   => [ProductsController::class, 'stock'],
    'product-delete'  => [ProductsController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'quiz'          => [PlaceholderController::class, 'front'],
    'events'        => [PlaceholderController::class, 'front'],
    'support'       => [PlaceholderController::class, 'front'],
    'bag'           => [PlaceholderController::class, 'front'],
    'my-orders'     => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'       => [PlaceholderController::class, 'back'],
    'admin'          => [PlaceholderController::class, 'back'],
    'orders'         => [PlaceholderController::class, 'back'],
    'articles'       => [PlaceholderController::class, 'back'],
    'comments'       => [PlaceholderController::class, 'back'],
    'quizzes'        => [PlaceholderController::class, 'back'],
    'quiz-questions' => [PlaceholderController::class, 'back'],
    'events-admin'   => [PlaceholderController::class, 'back'],
    'registrations'  => [PlaceholderController::class, 'back'],
];
