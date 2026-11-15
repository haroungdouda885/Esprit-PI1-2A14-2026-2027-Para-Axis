<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">THE COLLECTION</span>
                <h2>Find your <em>feel-good.</em></h2>
            </div>
            <p>Little things that make a big difference<br>in your everyday routine.</p>
        </div>

        <div class="toolbar">
            <div class="filters">
                <a class="filter <?= $categoryId === 0 ? 'active' : '' ?>" href="index.php?page=shop<?= $q !== '' ? '&amp;q=' . urlencode($q) : '' ?>">All products</a>
                <?php foreach ($categories as $c): $cid = (int) $c['id']; ?>
                    <a class="filter <?= $categoryId === $cid ? 'active' : '' ?>"
                       href="index.php?page=shop&amp;category=<?= $cid ?><?= $q !== '' ? '&amp;q=' . urlencode($q) : '' ?>"><?= e($c['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <form method="get" action="index.php">
                <input type="hidden" name="page" value="shop">
                <?php if ($categoryId): ?>
                    <input type="hidden" name="category" value="<?= $categoryId ?>">
                <?php endif; ?>
                <input class="search" name="q" value="<?= e($q) ?>" placeholder="Search products..." aria-label="Search products">
            </form>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <a class="product-card" href="index.php?page=product&amp;id=<?= (int) $p['id'] ?>">
                    <div class="product-image" style="background:<?= e($p['color'] ?? '#e8f1e9') ?>"><span><?= e($p['emoji']) ?></span></div>
                    <div class="product-label"><?= e($names[$p['category_id']] ?? '') ?></div>
                    <h3><?= e($p['name']) ?></h3>
                    <p class="product-desc"><?= e(mb_strimwidth($p['description'], 0, 70, '…')) ?></p>
                    <div class="product-bottom">
                        <span class="price"><?= number_format((float) $p['price'], 3) ?> TND</span>
                        <?php if ($p['stock'] > 0): ?>
                            <span class="add-btn" aria-hidden="true">+</span>
                        <?php else: ?>
                            <small class="out">Out of stock</small>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <div class="empty-results">No products found. Try another search.</div>
            <?php endif; ?>
        </div>
    </section>
</main>
