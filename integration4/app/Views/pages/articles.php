<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">HEALTH ADVICE</span>
        <h1>Articles <span>(<?= count($articles) ?>)</span></h1>
        <p>All the articles of the module.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New article</h3></div>
    <form method="post" action="index.php?page=article-create" class="article-form">
        <div class="form-two">
            <label>Title<input name="title" required placeholder="e.g. Taking care of your hair in winter"></label>
            <label>Author<input name="author" placeholder="Pharmacy team"></label>
        </div>
        <div class="form-two">
            <label>Emoji<input name="emoji" maxlength="4" placeholder="📝"></label>
            <label>Presented at the event (optional)
                <select name="event_id">
                    <option value="0">— No event —</option>
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= (int) $ev['id'] ?>"><?= e($ev['title'] . ' · ' . fmtDate($ev['event_date'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="form-two">
            <label class="check-label"><input type="checkbox" name="published" value="1" checked> Publish now</label>
        </div>
        <label>Content<textarea name="content" required rows="4" placeholder="Write the advice…"></textarea></label>
        <button type="submit" class="dark-btn">Add article ↗</button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All articles</h3>
        <input class="table-search" data-search="#articlesTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="articlesTable">
            <thead>
                <tr><th>ID</th><th>TITLE</th><th>AUTHOR</th><th>EVENT</th><th>STATUS</th><th>COMMENTS</th><th>DATE</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $a): $id = (int) $a['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($a['emoji']) ?> <?= e($a['title']) ?></strong></td>
                        <td><?= e($a['author']) ?></td>
                        <td><?= $a['event'] ? e($a['event']['title']) : '—' ?></td>
                        <td>
                            <?php if ($a['published']): ?>
                                <span class="status delivered">Published</span>
                            <?php else: ?>
                                <span class="status pending">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $a['comment_count'] ?></td>
                        <td><?= e($a['created_at']) ?></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=article-status">
                                <input type="hidden" name="article_id" value="<?= $id ?>">
                                <label class="sr-only" for="status-<?= $id ?>">Article status</label>
                                <select id="status-<?= $id ?>" name="published" onchange="this.form.submit()">
                                    <option value="1" <?= $a['published'] ? 'selected' : '' ?>>Published</option>
                                    <option value="0" <?= $a['published'] ? '' : 'selected' ?>>Draft</option>
                                </select>
                            </form>
                            <form method="post" action="index.php?page=article-delete" onsubmit="return confirm('Delete this article and all its comments? This cannot be undone.');">
                                <input type="hidden" name="article_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$articles): ?>
                    <tr><td colspan="8">No articles yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
