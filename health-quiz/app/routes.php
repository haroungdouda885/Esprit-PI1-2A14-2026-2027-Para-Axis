<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\AuthController;
use App\Controllers\HealthQuizController;
use App\Controllers\PlaceholderController;
use App\Controllers\QuizQuestionsController;
use App\Controllers\QuizzesController;

return [
    // ---------- Front office : Health quiz (my module) ----------
    'quiz'        => [HealthQuizController::class, 'index'],
    'quiz-play'   => [HealthQuizController::class, 'show'],
    'quiz-submit' => [HealthQuizController::class, 'submit'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Health quiz (my module) ----------
    'quizzes'         => [QuizzesController::class, 'index'],
    'quiz-create'     => [QuizzesController::class, 'create'],
    'quiz-status'     => [QuizzesController::class, 'status'],
    'quiz-delete'     => [QuizzesController::class, 'delete'],
    'quiz-questions'  => [QuizQuestionsController::class, 'index'],
    'question-create' => [QuizQuestionsController::class, 'create'],
    'question-delete' => [QuizQuestionsController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'shop'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'events'        => [PlaceholderController::class, 'front'],
    'support'       => [PlaceholderController::class, 'front'],
    'bag'           => [PlaceholderController::class, 'front'],
    'my-orders'     => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'      => [PlaceholderController::class, 'back'],
    'admin'         => [PlaceholderController::class, 'back'],
    'categories'    => [PlaceholderController::class, 'back'],
    'products'      => [PlaceholderController::class, 'back'],
    'orders'        => [PlaceholderController::class, 'back'],
    'articles'      => [PlaceholderController::class, 'back'],
    'comments'      => [PlaceholderController::class, 'back'],
    'events-admin'  => [PlaceholderController::class, 'back'],
    'registrations' => [PlaceholderController::class, 'back'],
];
