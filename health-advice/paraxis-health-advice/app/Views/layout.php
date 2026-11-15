<?php
/**
 * Layout principal (back office) - inclut tous les blocs communs et injecte le contenu
 * propre à chaque page ($content) au milieu.
 *
 * Variables attendues (fournies par Controller::render()) :
 *   $title         string - titre de l'onglet <title>
 *   $currentPage   string - clé de la page courante (pour la sidebar)
 *   $content       string - HTML de la page (déjà rendu)
 *   $extraScripts  string - JS spécifique à la page (optionnel)
 */
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
  <!-- ======== sidebar-nav end =========== -->

  <!-- ======== main-wrapper start =========== -->
  <main class="main-area main-wrapper">
    <?php require_once __DIR__ . '/partials/header.php'; ?>

    <div class="content"><?= $content ?></div>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>
  </main>
  <!-- ======== main-wrapper end =========== -->
<?php require_once __DIR__ . '/partials/scripts.php'; ?>
