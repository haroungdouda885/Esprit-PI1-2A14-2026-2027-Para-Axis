<?php
/**
 * Front-office layout - assembles the reusable blocks of app/Views/includes/
 * in the original display order and injects the page content ($content).
 *
 * Variables (provided by Controller::render()) :
 *   $title, $currentPage, $content, $extraScripts (optional)
 */
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/spinner.php';
require_once __DIR__ . '/includes/navbar.php';

echo $content;

require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
