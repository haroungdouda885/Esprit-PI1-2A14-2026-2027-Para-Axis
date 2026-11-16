<?php use App\Models\Product; ?>
<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">CATALOG MANAGEMENT</span>
        <h1>Products <span>(<?= count($products) ?>)</span></h1>
        <p>All the products of the shop, with their category, price and stock.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New product</h3></div>
    <?php if (!$categories): ?>
        <p>Create a category first in <a href="index.php?page=categories">Categories</a>.</p>
    <?php else: ?>
        <form method="post" action="index.php?page=product-create" class="article-form">
            <div class="form-two">
                <label>Product name<input name="name" required placeholder="e.g. Repair Hand Cream"></label>
                <label>Category
                    <select name="category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="form-two">
                <label>Price (TND)<input name="price" type="number" step="0.001" min="0" required placeholder="19.900"></label>
                <label>Stock<input name="stock" type="number" step="1" min="0" required placeholder="10"></label>
            </div>
            <div class="form-two">
                <label>Brand<input name="brand" placeholder="Par'Axis"></label>
                <label>Emoji icon<input name="emoji" maxlength="4" placeholder="🧴"></label>
            </div>
            <label>Description<textarea name="description" rows="3" placeholder="Describe the product…"></textarea></label>
            <button type="submit" class="dark-btn">Add product ↗</button>
        </form>
    <?php endif; ?>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All products</h3>
        <input class="table-search" data-search="#productsTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="productsTable">
            <thead>
                <tr><th>ID</th><th>PRODUCT</th><th>CATEGORY</th><th>PRICE</th><th>STATUS</th><th>STOCK</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): $id = (int) $p['id']; [$label, $cls] = Product::stockStatus((int) $p['stock']); ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($p['emoji']) ?> <?= e($p['name']) ?></strong><br><small><?= e($p['brand']) ?></small></td>
                        <td><?= e($p['category_name']) ?></td>
                        <td><?= number_format((float) $p['price'], 3) ?> TND</td>
                        <td><span class="status <?= $cls ?>"><?= $label ?></span></td>
                        <td>
                            <form method="post" action="index.php?page=product-stock">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <label class="sr-only" for="stock-<?= $id ?>">Stock</label>
                                <input id="stock-<?= $id ?>" class="stock-input" type="number" name="stock" min="0" step="1"
                                       value="<?= (int) $p['stock'] ?>" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=product-delete" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="7">No products yet. Use the form above to add the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->
