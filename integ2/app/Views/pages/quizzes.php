<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">HEALTH QUIZ</span>
        <h1>Quizzes <span>(<?= count($quizzes) ?>)</span></h1>
        <p>All the quizzes of the module.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New quiz</h3></div>
    <form method="post" action="index.php?page=quiz-create" class="article-form">
        <div class="form-two">
            <label>Title<input name="title" required placeholder="e.g. Healthy hair in winter"></label>
            <label>Category<input name="category" placeholder="General"></label>
        </div>
        <div class="form-two">
            <label>Emoji<input name="emoji" maxlength="4" placeholder="🧠"></label>
            <label class="check-label"><input type="checkbox" name="active" value="1" checked> Active now</label>
        </div>
        <label>Description<textarea name="description" rows="3" placeholder="What is this quiz about?"></textarea></label>
        <button type="submit" class="dark-btn">Add quiz ↗</button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All quizzes</h3>
        <input class="table-search" data-search="#quizzesTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="quizzesTable">
            <thead>
                <tr><th>ID</th><th>TITLE</th><th>CATEGORY</th><th>STATUS</th><th>QUESTIONS</th><th>DATE</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $q): $id = (int) $q['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($q['emoji']) ?> <?= e($q['title']) ?></strong></td>
                        <td><?= e($q['category']) ?></td>
                        <td>
                            <?php if ($q['active']): ?>
                                <span class="status delivered">Active</span>
                            <?php else: ?>
                                <span class="status pending">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $q['question_count'] ?></td>
                        <td><?= e($q['created_at']) ?></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=quiz-status">
                                <input type="hidden" name="quiz_id" value="<?= $id ?>">
                                <label class="sr-only" for="status-<?= $id ?>">Quiz status</label>
                                <select id="status-<?= $id ?>" name="active" onchange="this.form.submit()">
                                    <option value="1" <?= $q['active'] ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= $q['active'] ? '' : 'selected' ?>>Inactive</option>
                                </select>
                            </form>
                            <form method="post" action="index.php?page=quiz-delete" onsubmit="return confirm('Delete this quiz and all its questions? This cannot be undone.');">
                                <input type="hidden" name="quiz_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$quizzes): ?>
                    <tr><td colspan="7">No quizzes yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
