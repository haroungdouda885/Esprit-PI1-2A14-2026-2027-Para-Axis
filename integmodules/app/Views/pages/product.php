<main>
    <section class="section wrap">
        <a class="back-link" href="index.php?page=shop">← All products</a>

        <article class="form-card">
            <div class="product-image" style="background:<?= e($product['color'] ?? '#e8f1e9') ?>"><span><?= e($product['emoji']) ?></span></div>
            <div class="product-label"><?= e($category['name'] ?? '') ?></div>
            <h2><?= e($product['name']) ?></h2>
            <small><?= e($product['brand']) ?></small>
            <p class="article-body"><?= nl2br(e($product['description'])) ?></p>
            <div class="product-bottom">
                <span class="price"><?= money($product['price']) ?></span>
                <?php if ($product['stock'] <= 0): ?>
                    <small class="out">Out of stock</small>
                <?php elseif ($product['stock'] <= \App\Models\Product::LOW_STOCK): ?>
                    <span class="badge">Only <?= (int) $product['stock'] ?> left</span>
                <?php else: ?>
                    <span class="badge ok">In stock</span>
                <?php endif; ?>
            </div>

            <!-- Orders and payments (member 3): add to bag from the product page -->
            <div class="product-page-actions">
                <?php if ($product['stock'] > 0): ?>
                    <form method="post" action="index.php?page=bag-add" data-ajax-add>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="from" value="product">
                        <button class="primary-btn" type="submit">Add to bag</button>
                    </form>
                <?php endif; ?>
                <a class="back-link <?= $inBag > 0 ? '' : 'hidden' ?>" id="inBagLink" href="index.php?page=bag">In your bag: <span><?= (int) $inBag ?></span> → View bag</a>
            </div>
        </article>
    </section>
</main>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
