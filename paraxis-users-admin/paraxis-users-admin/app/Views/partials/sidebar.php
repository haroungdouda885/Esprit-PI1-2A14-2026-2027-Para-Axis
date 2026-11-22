<?php
/**
 * Back-office sidebar.
 * Expects $currentPage (defined before the include) to highlight the active link.
 */
$adminNav = [
    'USERS (member 1)' => ['users-admin' => 'Users'],
];

/** Returns 'active' when $page is the page currently displayed. */
function adminActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active' : '';
}

/** Returns the label of the current page (used by the header breadcrumb). */
function adminPageLabel(array $adminNav, string $currentPage): string
{
    foreach ($adminNav as $items) {
        if (isset($items[$currentPage])) {
            return $items[$currentPage];
        }
    }
    return 'Users';
}
?>
<!-- ======== sidebar-nav start =========== -->
<aside class="sidebar" id="sidebar">
  <a class="admin-logo" href="index.php?page=users-admin"><span>✿</span> Par'Axis</a>
  <nav class="admin-navigation" aria-label="Back office navigation">
    <?php foreach ($adminNav as $group => $items): ?>
      <div class="side-label"><?= htmlspecialchars($group) ?></div>
      <?php foreach ($items as $key => $label): ?>
        <a class="side-link <?= adminActive($key, $currentPage) ?>" href="index.php?page=<?= htmlspecialchars($key) ?>"><b>•</b><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-actions">
    <div class="side-label">STORE</div>
    <a class="side-link" href="index.php?page=logout"><b>⎋</b>Log out</a>
  </div>
  <div class="sidebar-bottom">
    <div class="admin-avatar">AD</div>
    <div><strong><?= htmlspecialchars(currentUser()['name'] ?? 'Demo User') ?></strong><small>Back office</small></div>
    <span class="online-dot"></span>
  </div>
</aside>
