<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">HEALTH QUIZ</span>
        <h1>Quiz questions <span>(<?= count($questions) ?>)</span></h1>
        <p>Questions of every quiz, with three options and the correct answer.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New question</h3></div>
    <?php if (!$quizzes): ?>
        <p>Create a quiz first in <a href="index.php?page=quizzes">Quizzes</a>.</p>
    <?php else: ?>
        <form method="post" action="index.php?page=question-create" class="article-form">
            <div class="form-two">
                <label>Quiz
                    <select name="quiz_id" required>
                        <?php foreach ($quizzes as $qz): ?>
                            <option value="<?= (int) $qz['id'] ?>"><?= e($qz['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Correct option
                    <select name="correct_option" required>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                    </select>
                </label>
            </div>
            <label>Question<input name="question" required placeholder="e.g. How much water should you drink a day?"></label>
            <div class="form-two">
                <label>Option A<input name="option_a" required></label>
                <label>Option B<input name="option_b" required></label>
            </div>
            <label>Option C<input name="option_c" required></label>
            <button type="submit" class="dark-btn">Add question ↗</button>
        </form>
    <?php endif; ?>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All questions</h3>
        <input class="table-search" data-search="#questionsTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="questionsTable">
            <thead>
                <tr><th>ID</th><th>QUIZ</th><th>QUESTION</th><th>A / B / C</th><th>CORRECT</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): $id = (int) $q['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= e($q['quiz_title']) ?></td>
                        <td class="wrap-cell"><strong><?= e($q['question']) ?></strong></td>
                        <td class="wrap-cell"><?= e($q['option_a']) ?> / <?= e($q['option_b']) ?> / <?= e($q['option_c']) ?></td>
                        <td><span class="status delivered"><?= e(strtoupper($q['correct_option'])) ?></span></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=question-delete" onsubmit="return confirm('Delete this question? This cannot be undone.');">
                                <input type="hidden" name="question_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$questions): ?>
                    <tr><td colspan="6">No questions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
