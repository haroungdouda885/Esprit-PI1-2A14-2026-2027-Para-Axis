<?php
/**
 * Front-office navbar.
 * Expects $currentPage (defined before the include) to add the .active class.
 */
$frontNav = [
    'home'          => 'Home',
    'shop'          => 'Shop',
    'health-advice' => 'Health advice',
    'quiz'          => 'Quiz',
    'events'        => 'Events',
    'my-orders'     => 'My orders',
    'support'       => 'Support',
];

/** Returns 'active' when $page is the page currently displayed. */
function frontActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active' : '';
}
?>
<!-- Navbar Start -->
<div class="announcement">Free delivery on orders over 100 TND <span>✦</span> Your everyday wellness store</div>
<header class="site-header">
  <div class="wrap nav">
    <a class="logo" href="index.php?page=home"><span class="logo-icon">✿</span> Par'Axis</a>
    <button class="hamburger" id="navToggle" aria-label="Toggle navigation">☰</button>
    <nav id="mainNav" aria-label="Main navigation">
      <?php foreach ($frontNav as $key => $label): ?>
        <a href="index.php?page=<?= htmlspecialchars($key) ?>" class="<?= frontActive($key, $currentPage) ?>"><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="nav-right">
      <a class="cart-btn" href="index.php?page=bag">Bag <span>0</span></a>
      <?php if (isset($_SESSION['demo_user'])): ?>
        <a class="account-link" href="index.php?page=logout">Log out</a>
      <?php else: ?>
        <a class="account-link <?= frontActive('login', $currentPage) ?>" href="index.php?page=login">Log in</a>
      <?php endif; ?>
      <?php if (($_SESSION['demo_user']['role'] ?? '') === 'admin'): ?>
        <a class="admin-shortcut" href="index.php?page=articles">Back office ↗</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<!-- Navbar End -->
