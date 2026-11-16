<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">HEALTH QUIZ</span>
                <h2>Test your <em>knowledge.</em></h2>
            </div>
            <p>Short quizzes to learn while you play.</p>
        </div>

        <div class="card-grid">
            <?php foreach ($quizzes as $q): ?>
                <a class="card" href="index.php?page=quiz-play&amp;id=<?= (int) $q['id'] ?>">
                    <div class="card-emoji"><?= e($q['emoji']) ?></div>
                    <h3><?= e($q['title']) ?></h3>
                    <p><?= e(mb_strimwidth($q['description'], 0, 110, '…')) ?></p>
                    <small><?= e($q['category']) ?> · <?= (int) $q['question_count'] ?> question<?= $q['question_count'] == 1 ? '' : 's' ?></small>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if (!$quizzes): ?>
            <p class="empty">No quiz available yet.</p>
        <?php endif; ?>
    </section>
</main>
