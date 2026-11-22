  <!-- ========= All Javascript files linkup ======== -->
  <script src="app/Views/assets/js/main.js?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>

  <?php if (!empty($extraScripts)): ?>
    <?= $extraScripts ?>
  <?php endif; ?>
</body>
</html>
