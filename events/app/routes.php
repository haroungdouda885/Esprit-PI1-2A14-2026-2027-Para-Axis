<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\AuthController;
use App\Controllers\EventsAdminController;
use App\Controllers\EventsController;
use App\Controllers\PlaceholderController;
use App\Controllers\RegistrationsController;

return [
    // ---------- Front office : Events & workshops (my module) ----------
    'events'              => [EventsController::class, 'index'],
    'event-register'      => [EventsController::class, 'register'],
    'my-registrations'    => [EventsController::class, 'mine'],
    'registration-cancel' => [EventsController::class, 'cancel'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Events & workshops (my module) ----------
    'events-admin'        => [EventsAdminController::class, 'index'],
    'event-save'          => [EventsAdminController::class, 'save'],
    'event-delete'        => [EventsAdminController::class, 'delete'],
    'registrations'       => [RegistrationsController::class, 'index'],
    'registration-toggle' => [RegistrationsController::class, 'toggle'],
    'registration-delete' => [RegistrationsController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'shop'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'quiz'          => [PlaceholderController::class, 'front'],
    'support'       => [PlaceholderController::class, 'front'],
    'bag'           => [PlaceholderController::class, 'front'],
    'my-orders'     => [PlaceholderController::class, 'front'],

    // ---------- Back office : other team members' modules (empty for now) ----------
    'overview'       => [PlaceholderController::class, 'back'],
    'admin'          => [PlaceholderController::class, 'back'],
    'categories'     => [PlaceholderController::class, 'back'],
    'products'       => [PlaceholderController::class, 'back'],
    'orders'         => [PlaceholderController::class, 'back'],
    'articles'       => [PlaceholderController::class, 'back'],
    'comments'       => [PlaceholderController::class, 'back'],
    'quizzes'        => [PlaceholderController::class, 'back'],
    'quiz-questions' => [PlaceholderController::class, 'back'],
];
