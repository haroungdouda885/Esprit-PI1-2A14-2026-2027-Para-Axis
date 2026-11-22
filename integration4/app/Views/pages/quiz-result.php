<main>
    <section class="section wrap">
        <a class="back-link" href="index.php?page=quiz">← All quizzes</a>

        <div class="form-card">
            <div class="card-emoji"><?= e($quiz['emoji']) ?></div>
            <h2>Your score: <?= (int) $score ?> / <?= count($results) ?></h2>
            <small><?= e($quiz['title']) ?></small>
            <ul class="result">
                <?php foreach ($results as $r): ?>
                    <li class="<?= $r['ok'] ? 'good' : 'bad' ?>">
                        <?= e($r['question']) ?> — <?= $r['ok'] ? 'Correct' : 'Answer: ' . e($r['answer']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($product)): ?>
                <p class="quiz-product">This quiz is about <strong><?= e($product['emoji'] . ' ' . $product['name']) ?></strong>.
                    <a class="link-tag" href="index.php?page=product&amp;id=<?= (int) $product['id'] ?>">See the product →</a></p>
            <?php endif; ?>
            <p>
                <a class="back-link" href="index.php?page=quiz-play&amp;id=<?= (int) $quiz['id'] ?>">↻ Try again</a>
            </p>
        </div>
    </section>
</main>
