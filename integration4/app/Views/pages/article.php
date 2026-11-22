<main>
    <section class="section wrap">
        <a class="back-link" href="index.php?page=health-advice">← All articles</a>

        <article class="form-card">
            <div class="card-emoji"><?= e($article['emoji']) ?></div>
            <h2><?= e($article['title']) ?></h2>
            <small><?= e($article['author']) ?> · <?= e($article['created_at']) ?></small>
            <p class="article-body"><?= nl2br(e($article['content'])) ?></p>
            <?php if (!empty($event)): ?>
                <p class="quiz-product">📅 Presented at the event <strong><?= e($event['title']) ?></strong> — <?= e(fmtDate($event['event_date'])) ?>, <?= e($event['location']) ?>.
                    <a class="link-tag" href="index.php?page=events&amp;filter=all&amp;q=<?= urlencode($event['title']) ?>">See the event →</a></p>
            <?php endif; ?>
        </article>

        <section class="form-card">
            <h3>Comments (<?= count($comments) ?>)</h3>
            <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <strong><?= e($c['author']) ?></strong> <small><?= e($c['created_at']) ?></small>
                    <p><?= nl2br(e($c['content'])) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (!$comments): ?>
                <p class="empty">Be the first to comment.</p>
            <?php endif; ?>
        </section>

        <?php if (isAdmin()): ?>
            <p class="read-only">Admin view: comments are managed in the back office (Comments).</p>
        <?php elseif ($user): ?>
            <form class="form-card" method="post" action="index.php?page=add-comment">
                <h3>Leave a comment</h3>
                <?php if ($sent): ?>
                    <p class="ok">Thank you! Your comment has been posted.</p>
                <?php endif; ?>
                <input type="hidden" name="article_id" value="<?= (int) $article['id'] ?>">
                <label>Your comment<textarea name="content" required rows="3" placeholder="Share your thoughts…"></textarea></label>
                <button class="primary-btn" type="submit">Post comment ↗</button>
            </form>
        <?php else: ?>
            <p><a class="back-link" href="index.php?page=login&amp;next=health-advice">Log in</a> to post a comment.</p>
        <?php endif; ?>
    </section>
</main>
