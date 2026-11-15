<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">SALES MANAGEMENT</span>
        <h1>Orders and payments <span>(<?= count($orders) ?>)</span></h1>
        <p><?= (int) $pending ?> pending order(s).</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <div>
            <h3>All orders</h3>
            <p>Change the order status or the payment status with the dropdowns. Cancelling an order puts the items back in stock.</p>
        </div>
        <div class="row-actions">
            <input class="table-search" data-search="#ordersTable" placeholder="Search…">
            <form class="filter-form" method="get" action="index.php">
                <input type="hidden" name="page" value="orders">
                <label class="sr-only" for="orderFilter">Filter by status</label>
                <select id="orderFilter" name="status" onchange="this.form.submit()">
                    <?php foreach (array_merge(['All'], $statuses) as $s): ?>
                        <option value="<?= e($s) ?>" <?= $s === $filter ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    <div class="table-scroll">
        <table id="ordersTable">
            <thead>
                <tr><th>ORDER</th><th>CUSTOMER</th><th>DATE</th><th>ITEMS</th><th>TOTAL</th><th>ORDER STATUS</th><th>PAYMENT</th><th>PAYMENT STATUS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): $id = (int) $o['id']; $p = $o['payment']; ?>
                    <tr>
                        <td><strong>#<?= $id ?></strong></td>
                        <td><?= e($o['customer_name']) ?><br><small><?= e($o['customer_email']) ?></small></td>
                        <td><?= e($o['created_at']) ?></td>
                        <td class="wrap-cell">
                            <?php foreach ($o['items'] as $i): ?>
                                <?= e($i['name']) ?> ×<?= (int) $i['qty'] ?><br>
                            <?php endforeach; ?>
                            <?= $o['items'] ? '' : '—' ?>
                        </td>
                        <td><strong><?= money($o['total']) ?></strong></td>
                        <td>
                            <form method="post" action="index.php?page=order-status">
                                <input type="hidden" name="order_id" value="<?= $id ?>">
                                <input type="hidden" name="filter" value="<?= e($filter) ?>">
                                <label class="sr-only" for="ost-<?= $id ?>">Order status</label>
                                <select id="ost-<?= $id ?>" name="status" onchange="this.form.submit()">
                                    <?php foreach ($statuses as $s): ?>
                                        <option <?= $s === $o['status'] ? 'selected' : '' ?>><?= e($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?= $p ? e(str_replace('_', ' ', $p['method'])) : '—' ?></td>
                        <td>
                            <?php if ($p): ?>
                                <form method="post" action="index.php?page=payment-status">
                                    <input type="hidden" name="payment_id" value="<?= (int) $p['id'] ?>">
                                    <input type="hidden" name="filter" value="<?= e($filter) ?>">
                                    <label class="sr-only" for="pst-<?= $id ?>">Payment status</label>
                                    <select id="pst-<?= $id ?>" name="status" onchange="this.form.submit()">
                                        <?php foreach ($payStatuses as $s): ?>
                                            <option <?= $s === $p['status'] ? 'selected' : '' ?>><?= e($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=order-delete" onsubmit="return confirm('Delete this order and its payment?');">
                                <input type="hidden" name="order_id" value="<?= $id ?>">
                                <input type="hidden" name="filter" value="<?= e($filter) ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$orders): ?>
                    <tr><td colspan="9">No order.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
