<?php
/**
 * Front-office navbar of this part of the Users module.
 * Expects $currentPage (defined before the include) to add the .active class.
 */
$frontNav = [
    'login' => 'Log in',
];

/** Returns 'active' when $page is the page currently displayed. */
function frontActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active' : '';
}
?>
<!-- Navbar Start -->
<div class="announcement">Users module <span>✦</span> Login, logout, sessions, remember me</div>
<header class="site-header">
  <div class="wrap nav">
    <a class="logo" href="index.php?page=login"><span class="logo-icon">✿</span> Par'Axis</a>
    <button class="hamburger" id="navToggle" aria-label="Toggle navigation">☰</button>
    <nav id="mainNav" aria-label="Main navigation">
      <?php foreach ($frontNav as $key => $label): ?>
        <a href="index.php?page=<?= htmlspecialchars($key) ?>" class="<?= frontActive($key, $currentPage) ?>"><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="nav-right">
      <?php if (currentUser()): ?>
        <span class="account-link"><?= e(currentUser()['name']) ?></span>
        <a class="account-link" href="index.php?page=logout">Log out</a>
      <?php else: ?>
        <a class="account-link <?= frontActive('login', $currentPage) ?>" href="index.php?page=login">Log in</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<!-- Navbar End -->
