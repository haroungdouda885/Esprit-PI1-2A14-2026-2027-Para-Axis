<?php use App\Models\Association; ?>
<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">DONATIONS &amp; ASSOCIATIONS</span>
        <h1>Associations <span>(<?= count($associations) ?>)</span></h1>
        <p>The partner associations (sponsors) that receive the donations of our customers.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== stats start ========== -->
<div class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span>Associations</span></div><strong><?= $stats['total'] ?></strong><div class="stat-foot neutral"><?= $stats['active'] ?> visible</div></article>
    <article class="stat-card"><div class="stat-top"><span>Received</span></div><strong><?= number_format($stats['received'], 2) ?></strong><div class="stat-foot neutral">TND (confirmed + delivered)</div></article>
    <article class="stat-card"><div class="stat-top"><span>Donors</span></div><strong><?= $stats['donors'] ?></strong><div class="stat-foot neutral">different emails</div></article>
    <article class="stat-card"><div class="stat-top"><span>Hidden</span></div><strong><?= $stats['total'] - $stats['active'] ?></strong><div class="stat-foot neutral">not shown to customers</div></article>
</div>
<!-- ========== stats end ========== -->

<!-- ========== form start ========== -->
<?php $v = $edit ?? ['name' => '', 'description' => '', 'category_id' => 0, 'email' => '', 'phone' => '', 'address' => '', 'logo' => '', 'active' => 1]; ?>
<div class="panel">
    <div class="panel-head">
        <h3><?= $edit ? 'Edit association #' . (int) $edit['id'] : 'New association' ?></h3>
        <?php if ($edit): ?><a class="action-btn" href="index.php?page=associations">Cancel edit</a><?php endif; ?>
    </div>
    <form method="post" action="index.php?page=association-save" class="article-form">
        <?php if ($edit): ?><input type="hidden" name="association_id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
        <div class="form-two">
            <label>Name<input name="name" required minlength="3" value="<?= e($v['name']) ?>" placeholder="e.g. Sourire d'Enfant"></label>
            <label>Category <a class="small-link" href="index.php?page=categories&amp;type=association">(manage)</a>
                <select name="category_id" required>
                    <option value="">Choose…</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) ($v['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e(($c['emoji'] ?? '') . ' ' . $c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="form-two">
            <label>Email<input name="email" type="email" required value="<?= e($v['email']) ?>" placeholder="contact@association.tn"></label>
            <label>Phone<input name="phone" value="<?= e($v['phone']) ?>" placeholder="71 123 456"></label>
        </div>
        <div class="form-two">
            <label>Address<input name="address" value="<?= e($v['address']) ?>" placeholder="City"></label>
            <label>Logo (link, optional)<input name="logo" type="url" value="<?= e($v['logo']) ?>" placeholder="https://…/logo.png"></label>
        </div>
        <label>Description<textarea name="description" rows="3" required minlength="10" placeholder="What does the association do?"><?= e($v['description']) ?></textarea></label>
        <label class="check-label"><input type="checkbox" name="active" <?= (int) $v['active'] === 1 ? 'checked' : '' ?>> Visible to customers</label>
        <button type="submit" class="dark-btn"><?= $edit ? 'Save changes ↗' : 'Create association ↗' ?></button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <div>
            <h3>All associations</h3>
            <p>An association that has received donations cannot be deleted: hide it instead.</p>
        </div>
        <input class="table-search" data-search="#associationsTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="associationsTable">
            <thead>
                <tr><th>ID</th><th>ASSOCIATION</th><th>CATEGORY</th><th>CONTACT</th><th>DONATIONS</th><th>RECEIVED</th><th>STATUS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $a): $id = (int) $a['id']; $on = (int) $a['active'] === 1; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td class="wrap-cell"><strong><?= Association::emojiOf($a) ?> <?= e($a['name']) ?></strong><br><small><?= e($a['description']) ?></small></td>
                        <td><?= e(Association::categoryName($a)) ?></td>
                        <td><?= e($a['email']) ?><br><small><?= e($a['phone']) ?> · <?= e($a['address']) ?></small></td>
                        <td><a href="index.php?page=donations-admin&amp;association=<?= $id ?>"><?= $a['count'] ?></a></td>
                        <td><?= number_format($a['received'], 2) ?> TND</td>
                        <td><span class="status <?= $on ? 'delivered' : 'shipped' ?>"><?= $on ? 'Visible' : 'Hidden' ?></span></td>
                        <td class="row-actions">
                            <a class="action-btn" href="index.php?page=associations&amp;edit=<?= $id ?>">Edit</a>
                            <form method="post" action="index.php?page=association-toggle">
                                <input type="hidden" name="association_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn"><?= $on ? 'Hide' : 'Show' ?></button>
                            </form>
                            <form method="post" action="index.php?page=association-delete" onsubmit="return confirm('Delete this association?');">
                                <input type="hidden" name="association_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$associations): ?>
                    <tr><td colspan="8">No association yet. Use the form above to create the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
