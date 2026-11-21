<?php use App\Models\Donation; ?>
<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">DONATIONS &amp; ASSOCIATIONS</span>
        <h1>Donations <span>(<?= $stats['total'] ?>)</span></h1>
        <p>Confirm the donations, mark the products as delivered to the association, or cancel.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== stats start ========== -->
<div class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span>Donations</span></div><strong><?= $stats['total'] ?></strong><div class="stat-foot neutral">all statuses</div></article>
    <article class="stat-card"><div class="stat-top"><span>Pending</span></div><strong><?= $stats['pending'] ?></strong><div class="stat-foot neutral">to confirm</div></article>
    <article class="stat-card"><div class="stat-top"><span>Money received</span></div><strong><?= number_format($stats['money'], 2) ?></strong><div class="stat-foot neutral">TND</div></article>
    <article class="stat-card"><div class="stat-top"><span>Products given</span></div><strong><?= number_format($stats['products'], 2) ?></strong><div class="stat-foot neutral">TND of products</div></article>
</div>
<!-- ========== stats end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <div>
            <h3>All donations</h3>
            <p>Cancelling a product donation puts the products back in stock. A delivered donation cannot be cancelled.</p>
        </div>
        <form class="filter-form donation-filters" method="get" action="index.php">
            <input type="hidden" name="page" value="donations-admin">
            <select name="association" onchange="this.form.submit()" aria-label="Association">
                <option value="0">All associations</option>
                <?php foreach ($associations as $a): ?>
                    <option value="<?= (int) $a['id'] ?>" <?= $filters['association'] === (int) $a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="type" onchange="this.form.submit()" aria-label="Type">
                <option value="">All types</option>
                <?php foreach (Donation::TYPES as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" onchange="this.form.submit()" aria-label="Status">
                <option value="">All statuses</option>
                <?php foreach (Donation::STATUSES as $s): ?>
                    <option value="<?= e($s) ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
            <input class="table-search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search donor…">
        </form>
    </div>
    <?php $keep = function () use ($filters) { ?>
        <input type="hidden" name="f_association" value="<?= (int) $filters['association'] ?>">
        <input type="hidden" name="f_type" value="<?= e($filters['type']) ?>">
        <input type="hidden" name="f_status" value="<?= e($filters['status']) ?>">
        <input type="hidden" name="f_q" value="<?= e($filters['q']) ?>">
    <?php }; ?>
    <div class="table-scroll">
        <table id="donationsTable">
            <thead>
                <tr><th>ID</th><th>DONOR</th><th>ASSOCIATION</th><th>DONATION</th><th>VALUE</th><th>DATE</th><th>STATUS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $d): $id = (int) $d['id']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($d['donor_name']) ?></strong><?= (int) $d['anonymous'] === 1 ? ' <small>(anonymous)</small>' : '' ?><br><small><?= e($d['donor_email']) ?></small></td>
                        <td><?= e($d['association_name']) ?></td>
                        <td class="wrap-cell">
                            <?php if ($d['type'] === 'money'): ?>
                                💰 Money
                            <?php else: ?>
                                <?php foreach ($d['items'] as $i): ?>
                                    <div><?= e($i['product']['emoji']) ?> <?= e($i['product']['name']) ?> × <?= (int) $i['quantity'] ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if ($d['message'] !== ''): ?><small>“<?= e($d['message']) ?>”</small><?php endif; ?>
                        </td>
                        <td><?= number_format((float) $d['amount'], 2) ?> TND</td>
                        <td><?= e(fmtDate($d['created_at'])) ?></td>
                        <td>
                            <form method="post" action="index.php?page=donation-status">
                                <input type="hidden" name="donation_id" value="<?= $id ?>">
                                <?php $keep(); ?>
                                <span class="status <?= Donation::pill($d['status']) ?>"><?= e($d['status']) ?></span>
                                <select name="status" onchange="this.form.submit()" aria-label="Change the status">
                                    <?php foreach (Donation::STATUSES as $s): ?>
                                        <option <?= $s === $d['status'] ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=donation-delete" onsubmit="return confirm('Delete this donation?');">
                                <input type="hidden" name="donation_id" value="<?= $id ?>">
                                <?php $keep(); ?>
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="8">No donation matches these filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
