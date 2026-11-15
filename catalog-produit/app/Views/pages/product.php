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
                <span class="price"><?= number_format((float) $product['price'], 3) ?> TND</span>
                <?php if ($product['stock'] <= 0): ?>
                    <small class="out">Out of stock</small>
                <?php elseif ($product['stock'] <= \App\Models\Product::LOW_STOCK): ?>
                    <span class="badge">Only <?= (int) $product['stock'] ?> left</span>
                <?php else: ?>
                    <span class="badge ok">In stock</span>
                <?php endif; ?>
            </div>
        </article>
    </section>
</main>
