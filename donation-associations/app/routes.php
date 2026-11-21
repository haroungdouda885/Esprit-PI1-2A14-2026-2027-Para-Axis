<?php
/**
 * Routing table: page key => [Controller class, method]
 * To add a page: create its view + controller, then add (or point) its line here.
 */
use App\Controllers\AssociationCategoriesController;
use App\Controllers\AssociationsController;
use App\Controllers\AuthController;
use App\Controllers\DonationsAdminController;
use App\Controllers\DonationsController;
use App\Controllers\PlaceholderController;

return [
    // ---------- Front office : Donations & associations (my module) ----------
    'donations'       => [DonationsController::class, 'index'],
    'donate'          => [DonationsController::class, 'donate'],
    'my-donations'    => [DonationsController::class, 'mine'],
    'donation-cancel' => [DonationsController::class, 'cancel'],

    // ---------- Authentication ----------
    'login'  => [AuthController::class, 'login'],
    'signup' => [AuthController::class, 'signup'],
    'logout' => [AuthController::class, 'logout'],

    // ---------- Back office : Donations & associations (my module) ----------
    'associations'                => [AssociationsController::class, 'index'],
    'association-save'            => [AssociationsController::class, 'save'],
    'association-toggle'          => [AssociationsController::class, 'toggle'],
    'association-delete'          => [AssociationsController::class, 'delete'],
    'donations-admin'             => [DonationsAdminController::class, 'index'],
    'donation-status'             => [DonationsAdminController::class, 'status'],
    'donation-delete'             => [DonationsAdminController::class, 'delete'],
    'association-categories'      => [AssociationCategoriesController::class, 'index'],
    'association-category-save'   => [AssociationCategoriesController::class, 'save'],
    'association-category-delete' => [AssociationCategoriesController::class, 'delete'],

    // ---------- Front office : other team members' modules (empty for now) ----------
    'home'          => [PlaceholderController::class, 'front'],
    'shop'          => [PlaceholderController::class, 'front'],
    'health-advice' => [PlaceholderController::class, 'front'],
    'quiz'          => [PlaceholderController::class, 'front'],
    'events'        => [PlaceholderController::class, 'front'],
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
    'events-admin'   => [PlaceholderController::class, 'back'],
    'registrations'  => [PlaceholderController::class, 'back'],
];
