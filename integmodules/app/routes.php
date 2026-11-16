<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 * Integrated modules: Customer support (member 1), Catalog (member 2), Orders and payments (member 3),
 * Health advice (member 4), Health quiz (member 5), Events & workshops (member 6).
 */
use App\Controllers\AdminController;
use App\Controllers\ArticlesController;
use App\Controllers\AuthController;
use App\Controllers\BagController;
use App\Controllers\CategoriesController;
use App\Controllers\CommentsController;
use App\Controllers\EventsAdminController;
use App\Controllers\EventsController;
use App\Controllers\HealthAdviceController;
use App\Controllers\HealthQuizController;
use App\Controllers\HomeController;
use App\Controllers\MyOrdersController;
use App\Controllers\OrdersController;
use App\Controllers\PlaceholderController;
use App\Controllers\ProductsController;
use App\Controllers\QuizQuestionsController;
use App\Controllers\QuizzesController;
use App\Controllers\RegistrationsController;
use App\Controllers\ShopController;
use App\Controllers\SupportController;

return [
    // ---------- Front office : Home (shared) ----------
    'home' => [HomeController::class, 'index'],

    // ---------- Front office : Customer support (member 1) ----------
    'support'        => [SupportController::class, 'index'],
    'create-ticket'  => [SupportController::class, 'create'],
    'customer-reply' => [SupportController::class, 'reply'],

    // ---------- Front office : Catalog (member 2) ----------
    'shop'    => [ShopController::class, 'index'],
    'product' => [ShopController::class, 'show'],

    // ---------- Front office : Orders and payments (member 3) ----------
    'bag-add'      => [ShopController::class, 'add'],
    'bag'          => [BagController::class, 'index'],
    'bag-update'   => [BagController::class, 'update'],
    'checkout'     => [BagController::class, 'checkout'],
    'my-orders'    => [MyOrdersController::class, 'index'],
    'order-cancel' => [MyOrdersController::class, 'cancel'],

    // ---------- Front office : Health advice (member 4) ----------
    'health-advice' => [HealthAdviceController::class, 'index'],
    'article'       => [HealthAdviceController::class, 'show'],
    'add-comment'   => [HealthAdviceController::class, 'comment'],

    // ---------- Front office : Health quiz (member 5) ----------
    'quiz'        => [HealthQuizController::class, 'index'],
    'quiz-play'   => [HealthQuizController::class, 'show'],
    'quiz-submit' => [HealthQuizController::class, 'submit'],

    // ---------- Front office : Events & workshops (member 6) ----------
    'events'              => [EventsController::class, 'index'],
    'event-register'      => [EventsController::class, 'register'],
    'my-registrations'    => [EventsController::class, 'mine'],
    'registration-cancel' => [EventsController::class, 'cancel'],

    // ---------- Authentication (shared) ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Support tickets (member 1) ----------
    'admin'         => [AdminController::class, 'index'],
    'admin-reply'   => [AdminController::class, 'reply'],
    'ticket-status' => [AdminController::class, 'status'],
    'ticket-delete' => [AdminController::class, 'delete'],

    // ---------- Back office : Catalog (member 2) ----------
    'categories'      => [CategoriesController::class, 'index'],
    'category-create' => [CategoriesController::class, 'create'],
    'category-delete' => [CategoriesController::class, 'delete'],
    'products'        => [ProductsController::class, 'index'],
    'product-create'  => [ProductsController::class, 'create'],
    'product-stock'   => [ProductsController::class, 'stock'],
    'product-delete'  => [ProductsController::class, 'delete'],

    // ---------- Back office : Orders and payments (member 3) ----------
    'orders'         => [OrdersController::class, 'index'],
    'order-status'   => [OrdersController::class, 'status'],
    'payment-status' => [OrdersController::class, 'payment'],
    'order-delete'   => [OrdersController::class, 'delete'],

    // ---------- Back office : Health advice (member 4) ----------
    'articles'       => [ArticlesController::class, 'index'],
    'article-create' => [ArticlesController::class, 'create'],
    'article-status' => [ArticlesController::class, 'status'],
    'article-delete' => [ArticlesController::class, 'delete'],
    'comments'       => [CommentsController::class, 'index'],
    'comment-delete' => [CommentsController::class, 'delete'],

    // ---------- Back office : Health quiz (member 5) ----------
    'quizzes'         => [QuizzesController::class, 'index'],
    'quiz-create'     => [QuizzesController::class, 'create'],
    'quiz-status'     => [QuizzesController::class, 'status'],
    'quiz-delete'     => [QuizzesController::class, 'delete'],
    'quiz-questions'  => [QuizQuestionsController::class, 'index'],
    'question-create' => [QuizQuestionsController::class, 'create'],
    'question-delete' => [QuizQuestionsController::class, 'delete'],

    // ---------- Back office : Events & workshops (member 6) ----------
    'events-admin'        => [EventsAdminController::class, 'index'],
    'event-save'          => [EventsAdminController::class, 'save'],
    'event-delete'        => [EventsAdminController::class, 'delete'],
    'registrations'       => [RegistrationsController::class, 'index'],
    'registration-toggle' => [RegistrationsController::class, 'toggle'],
    'registration-delete' => [RegistrationsController::class, 'delete'],

    // ---------- Back office : shared page not built yet (empty for now) ----------
    'overview' => [PlaceholderController::class, 'back'],
];
