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
            <p>
                <a class="back-link" href="index.php?page=quiz-play&amp;id=<?= (int) $quiz['id'] ?>">↻ Try again</a>
            </p>
        </div>
    </section>
</main>
