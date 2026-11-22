<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">HEALTH ADVICE</span>
                <h2>Tips from our <em>pharmacists.</em></h2>
            </div>
            <p>Read, learn and share your thoughts.</p>
        </div>

        <div class="card-grid">
            <?php foreach ($articles as $a): ?>
                <a class="card" href="index.php?page=article&amp;id=<?= (int) $a['id'] ?>">
                    <div class="card-emoji"><?= e($a['emoji']) ?></div>
                    <h3><?= e($a['title']) ?></h3>
                    <p><?= e(mb_strimwidth($a['content'], 0, 110, '…')) ?></p>
                    <small><?= e($a['author']) ?> · <?= e($a['created_at']) ?></small>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if (!$articles): ?>
            <p class="empty">No article published yet.</p>
        <?php endif; ?>
    </section>
</main>
