<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">THE COLLECTION</span>
                <h2>Find your <em>feel-good.</em></h2>
            </div>
            <p>Little things that make a big difference in your everyday routine.<br>Add them to your bag and complete a simulated checkout.</p>
        </div>
        <p class="demo-note">Testing shortcut: a sample bag is loaded automatically on your first visit. Use “Reload demo bag” to restore it.</p>

        <div class="shop-tools">
            <div class="filters">
                <a class="filter <?= $categoryId ? '' : 'active' ?>" href="index.php?page=shop<?= $search !== '' ? '&amp;q=' . urlencode($search) : '' ?>">All products</a>
                <?php foreach ($categories as $id => $name): ?>
                    <a class="filter <?= $categoryId === $id ? 'active' : '' ?>" href="index.php?page=shop&amp;category=<?= $id ?><?= $search !== '' ? '&amp;q=' . urlencode($search) : '' ?>"><?= e($name) ?></a>
                <?php endforeach; ?>
            </div>
            <form class="shop-search" method="get" action="index.php">
                <input type="hidden" name="page" value="shop">
                <?php if ($categoryId): ?><input type="hidden" name="category" value="<?= (int) $categoryId ?>"><?php endif; ?>
                <input class="search" name="q" value="<?= e($search) ?>" placeholder="Search products…" aria-label="Search products">
            </form>
            <form method="post" action="index.php?page=bag-demo">
                <button class="demo-btn" type="submit">Reload demo bag</button>
            </form>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <article class="product-card">
                    <!-- Catalog (member 2): the card opens the product page -->
                    <a class="product-link" href="index.php?page=product&amp;id=<?= (int) $p['id'] ?>">
                        <div class="product-image" style="background:<?= e($p['color'] ?: '#e8f1e9') ?>"><span><?= e($p['emoji'] ?: '•') ?></span></div>
                        <div class="product-label"><?= e($categories[(int) $p['category_id']] ?? '') ?></div>
                        <h3><?= e($p['name']) ?></h3>
                        <p class="product-desc"><?= e(mb_strimwidth($p['description'], 0, 70, '…')) ?></p>
                    </a>
                    <div class="product-bottom">
                        <span class="price"><?= money($p['price']) ?></span>
                        <?php if ($p['stock'] > 0): ?>
                            <!-- Orders (member 3): add one unit to the bag -->
                            <form method="post" action="index.php?page=bag-add">
                                <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="category" value="<?= (int) $categoryId ?>">
                                <input type="hidden" name="q" value="<?= e($search) ?>">
                                <button class="add-btn" type="submit" aria-label="Add <?= e($p['name']) ?>">+</button>
                            </form>
                        <?php else: ?>
                            <small class="out">Out of stock</small>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <p class="empty-results">No products found. Try another search.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
