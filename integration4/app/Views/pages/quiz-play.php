<main>
    <section class="section wrap">
        <a class="back-link" href="index.php?page=quiz">← All quizzes</a>

        <?php if (!$questions): ?>
            <p class="empty">This quiz has no questions yet.</p>
        <?php else: ?>
            <form class="form-card" method="post" action="index.php?page=quiz-submit">
                <div class="card-emoji"><?= e($quiz['emoji']) ?></div>
                <h2><?= e($quiz['title']) ?></h2>
                <small><?= e($quiz['category']) ?> · <?= count($questions) ?> question<?= count($questions) == 1 ? '' : 's' ?></small>
                <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">

                <?php foreach ($questions as $i => $q): ?>
                    <fieldset>
                        <legend><?= $i + 1 ?>. <?= e($q['question']) ?></legend>
                        <?php foreach (['a', 'b', 'c'] as $o): ?>
                            <label class="opt">
                                <input type="radio" name="answers[<?= (int) $q['id'] ?>]" value="<?= $o ?>" required>
                                <?= e($q['option_' . $o]) ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>

                <?php if (isAdmin()): ?>
                    <p class="read-only">Admin view: you can read the questions, the quiz cannot be submitted.</p>
                <?php else: ?>
                    <button class="primary-btn" type="submit">See my score ↗</button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </section>
</main>
