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

// The admin has no shop window: Shop, My orders and Bag are hidden (the back office is used instead)
$isAdmin = isAdmin();
if ($isAdmin) {
    unset($frontNav['shop'], $frontNav['my-orders']);
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
      <?php if (!$isAdmin): ?>
        <a class="cart-btn <?= frontActive('bag', $currentPage) ?>" href="index.php?page=bag">Bag <span id="bagCount"><?= \App\Models\Cart::count() ?></span></a>
      <?php endif; ?>
      <?php if (currentUser()): ?>
        <a class="account-link" href="index.php?page=logout">Log out</a>
      <?php else: ?>
        <a class="account-link <?= frontActive('login', $currentPage) ?>" href="index.php?page=login">Log in</a>
      <?php endif; ?>
      <?php if ($isAdmin): ?>
        <a class="admin-shortcut" href="index.php?page=orders">Back office ↗</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<!-- Navbar End -->
