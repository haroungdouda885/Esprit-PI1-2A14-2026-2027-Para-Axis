<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">CATALOG MANAGEMENT</span>
        <h1>Categories <span>(<?= count($categories) ?>)</span></h1>
        <p>All the categories of the shop.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New category</h3></div>
    <form method="post" action="index.php?page=category-create" class="article-form">
        <div class="form-two">
            <label>Name<input name="name" required placeholder="e.g. Baby care"></label>
            <label>Description<input name="description" placeholder="Baby care essentials"></label>
        </div>
        <button type="submit" class="dark-btn">Add category ↗</button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All categories</h3>
        <input class="table-search" data-search="#categoriesTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="categoriesTable">
            <thead>
                <tr><th>ID</th><th>NAME</th><th>DESCRIPTION</th><th>PRODUCTS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): $id = (int) $c['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($c['name']) ?></strong></td>
                        <td class="wrap-cell"><?= e($c['description']) ?></td>
                        <td><?= (int) $c['product_count'] ?></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=category-delete" onsubmit="return confirm('Delete this category and all its products? This cannot be undone.');">
                                <input type="hidden" name="category_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="5">No categories yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
