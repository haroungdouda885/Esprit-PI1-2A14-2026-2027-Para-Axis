<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">MODERATION</span>
        <h1>Comments <span>(<?= count($comments) ?>)</span></h1>
        <p>Comments posted on the articles. Remove the ones that are not appropriate.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All comments</h3>
        <input class="table-search" data-search="#commentsTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="commentsTable">
            <thead>
                <tr><th>ID</th><th>ARTICLE</th><th>AUTHOR</th><th>COMMENT</th><th>DATE</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $c): $id = (int) $c['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= e($c['article_title']) ?></td>
                        <td><strong><?= e($c['author']) ?></strong></td>
                        <td class="wrap-cell"><?= e($c['content']) ?></td>
                        <td><?= e($c['created_at']) ?></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=comment-delete" onsubmit="return confirm('Delete this comment? This cannot be undone.');">
                                <input type="hidden" name="comment_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$comments): ?>
                    <tr><td colspan="6">No comments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
