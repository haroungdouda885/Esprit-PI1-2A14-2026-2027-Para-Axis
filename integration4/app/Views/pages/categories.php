<?php use App\Models\Category; ?>
<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">CATALOG &amp; DONATIONS</span>
        <h1>Categories <span>(<?= count($categories) ?>)</span></h1>
        <p>Product categories (shop) and association categories (donations), on the same page.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<?php $v = $edit ?? ['type' => $type ?: 'product', 'name' => '', 'emoji' => '', 'description' => '']; $vType = Category::typeOf($v); ?>
<div class="panel">
    <div class="panel-head">
        <h3><?= $edit ? 'Edit category #' . (int) $edit['id'] : 'New category' ?></h3>
        <?php if ($edit): ?><a class="action-btn" href="index.php?page=categories">Cancel edit</a><?php endif; ?>
    </div>
    <form method="post" action="index.php?page=category-create" class="article-form">
        <?php if ($edit): ?><input type="hidden" name="category_id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
        <div class="type-choice">
            <span>Category for</span>
            <label><input type="radio" name="type" value="product" <?= $vType === 'product' ? 'checked' : '' ?>> 🛍️ Products (shop)</label>
            <label><input type="radio" name="type" value="association" <?= $vType === 'association' ? 'checked' : '' ?>> 🤝 Associations (donations)</label>
        </div>
        <div class="form-two">
            <label>Name<input name="name" required minlength="2" value="<?= e($v['name']) ?>" placeholder="e.g. Baby care / Education"></label>
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
        <div class="type-tabs">
            <?php foreach (['' => 'All'] + Category::TYPES as $key => $label): ?>
                <a class="type-tab <?= $type === $key ? 'active' : '' ?>" href="index.php?page=categories<?= $key !== '' ? '&amp;type=' . $key : '' ?>"><?= e($label) ?> <span><?= (int) $counts[$key] ?></span></a>
            <?php endforeach; ?>
        </div>
        <input class="table-search" data-search="#categoriesTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="categoriesTable">
            <thead>
                <tr><th>ID</th><th>NAME</th><th>TYPE</th><th>DESCRIPTION</th><th>USED BY</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): $id = (int) $c['id']; $isAssoc = $c['type'] === 'association'; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e(($c['emoji'] ?? '') . ' ' . $c['name']) ?></strong></td>
                        <td><span class="status <?= $isAssoc ? 'shipped' : 'processing' ?>"><?= $isAssoc ? 'Association' : 'Product' ?></span></td>
                        <td class="wrap-cell"><?= e($c['description']) ?></td>
                        <td>
                            <?php if ($isAssoc): ?>
                                <a href="index.php?page=associations"><?= (int) $c['item_count'] ?> association<?= $c['item_count'] === 1 ? '' : 's' ?></a>
                            <?php else: ?>
                                <a href="index.php?page=products"><?= (int) $c['item_count'] ?> product<?= $c['item_count'] === 1 ? '' : 's' ?></a>
                            <?php endif; ?>
                        </td>
                        <td class="row-actions">
                            <a class="action-btn" href="index.php?page=categories&amp;edit=<?= $id ?>">Edit</a>
                            <form method="post" action="index.php?page=category-delete"
                                  onsubmit="return confirm('<?= $isAssoc ? 'Delete this association category?' : 'Delete this category and all its products? This cannot be undone.' ?>');">
                                <input type="hidden" name="category_id" value="<?= $id ?>">
                                <input type="hidden" name="type_filter" value="<?= e($type) ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="6">No categories yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
