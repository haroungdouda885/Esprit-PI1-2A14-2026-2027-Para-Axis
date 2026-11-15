<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\ArticlesController;
use App\Controllers\AuthController;
use App\Controllers\CommentsController;
use App\Controllers\HealthAdviceController;
use App\Controllers\PlaceholderController;

return [
    // ---------- Front office : Health advice (my module) ----------
    'health-advice' => [HealthAdviceController::class, 'index'],
    'article'       => [HealthAdviceController::class, 'show'],
    'add-comment'   => [HealthAdviceController::class, 'comment'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Health advice (my module) ----------
    'articles'       => [ArticlesController::class, 'index'],
    'article-create' => [ArticlesController::class, 'create'],
    'article-status' => [ArticlesController::class, 'status'],
    'article-delete' => [ArticlesController::class, 'delete'],
    'comments'       => [CommentsController::class, 'index'],
    'comment-delete' => [CommentsController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'    => [PlaceholderController::class, 'front'],
    'shop'    => [PlaceholderController::class, 'front'],
    'quiz'    => [PlaceholderController::class, 'front'],
    'events'  => [PlaceholderController::class, 'front'],
    'support' => [PlaceholderController::class, 'front'],
    'bag'     => [PlaceholderController::class, 'front'],
    'my-orders' => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'      => [PlaceholderController::class, 'back'],
    'admin'         => [PlaceholderController::class, 'back'],
    'categories'    => [PlaceholderController::class, 'back'],
    'products'      => [PlaceholderController::class, 'back'],
    'orders'        => [PlaceholderController::class, 'back'],
    'quizzes'       => [PlaceholderController::class, 'back'],
    'quiz-questions' => [PlaceholderController::class, 'back'],
    'events-admin'  => [PlaceholderController::class, 'back'],
    'registrations' => [PlaceholderController::class, 'back'],
];
