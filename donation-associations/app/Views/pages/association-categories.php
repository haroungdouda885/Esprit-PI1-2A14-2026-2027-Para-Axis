<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">DONATIONS &amp; ASSOCIATIONS</span>
        <h1>Association categories <span>(<?= count($categories) ?>)</span></h1>
        <p>The categories used to classify the associations (filters of the Donations page).</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<?php $v = $edit ?? ['name' => '', 'emoji' => '', 'description' => '']; ?>
<div class="panel">
    <div class="panel-head">
        <h3><?= $edit ? 'Edit category #' . (int) $edit['id'] : 'New category' ?></h3>
        <?php if ($edit): ?><a class="action-btn" href="index.php?page=association-categories">Cancel edit</a><?php endif; ?>
    </div>
    <form method="post" action="index.php?page=association-category-save" class="article-form">
        <?php if ($edit): ?><input type="hidden" name="category_id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
        <div class="form-two">
            <label>Name<input name="name" required minlength="2" value="<?= e($v['name']) ?>" placeholder="e.g. Education"></label>
            <label>Emoji (optional)<input name="emoji" maxlength="4" value="<?= e($v['emoji'] ?? '') ?>" placeholder="📚"></label>
        </div>
        <label>Description<input name="description" value="<?= e($v['description']) ?>" placeholder="A short description"></label>
        <button type="submit" class="dark-btn"><?= $edit ? 'Save changes ↗' : 'Add category ↗' ?></button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <div>
            <h3>All association categories</h3>
            <p>A category used by associations cannot be deleted.</p>
        </div>
        <input class="table-search" data-search="#assocCategoriesTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="assocCategoriesTable">
            <thead>
                <tr><th>ID</th><th>NAME</th><th>DESCRIPTION</th><th>ASSOCIATIONS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): $id = (int) $c['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e(($c['emoji'] ?? '') . ' ' . $c['name']) ?></strong></td>
                        <td class="wrap-cell"><?= e($c['description']) ?></td>
                        <td><a href="index.php?page=associations"><?= (int) $c['item_count'] ?></a></td>
                        <td class="row-actions">
                            <a class="action-btn" href="index.php?page=association-categories&amp;edit=<?= $id ?>">Edit</a>
                            <form method="post" action="index.php?page=association-category-delete" onsubmit="return confirm('Delete this category?');">
                                <input type="hidden" name="category_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="5">No category yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
