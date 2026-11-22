<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">MY ACCOUNT</span>
                <h2>My <em>orders.</em></h2>
            </div>
            <p>Follow your orders and their payment.<br>Pending or processing orders can be cancelled.</p>
        </div>

        <?php if ($ordered): ?>
            <div class="form-card ok-box">✓ Order placed! You can follow it below.</div>
        <?php endif; ?>

        <div class="form-card">
            <h3>My profile</h3>
            <p><b><?= e($user['name']) ?></b><br><?= e($user['email']) ?><br><small>Role: <?= e($user['role']) ?></small></p>
        </div>

        <?php if ($orders): ?>
            <div class="order-table-wrap">
                <table class="tbl">
                    <tr><th>Order</th><th>Date</th><th>Items</th><th>Total</th><th>Order status</th><th>Payment</th><th>Delivery address</th><th>Action</th></tr>
                    <?php foreach ($orders as $o): $p = $o['payment']; ?>
                        <tr>
                            <td>#<?= (int) $o['id'] ?></td>
                            <td><?= e($o['created_at']) ?></td>
                            <td>
                                <?php foreach ($o['items'] as $i): ?>
                                    <?= e($i['name']) ?> ×<?= (int) $i['qty'] ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td><?= money($o['total']) ?></td>
                            <td><span class="badge <?= $o['status'] === 'cancelled' ? 'bad' : '' ?>"><?= e($o['status']) ?></span></td>
                            <td>
                                <?php if ($p): ?>
                                    <?= e(str_replace('_', ' ', $p['method'])) ?> · <span class="badge <?= $p['status'] === 'paid' ? 'ok' : '' ?>"><?= e($p['status']) ?></span>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td><?= e($o['shipping_address'] ?: '—') ?></td>
                            <td>
                                <?php if (in_array($o['status'], ['pending', 'processing'], true)): ?>
                                    <form method="post" action="index.php?page=order-cancel" onsubmit="return confirm('Cancel order #<?= (int) $o['id'] ?>? This action cannot be undone.');">
                                        <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                                        <button type="submit" class="cancel-order-btn">Cancel order</button>
                                    </form>
                                <?php elseif ($o['status'] === 'cancelled'): ?>
                                    <span class="order-no-cancel">Cancelled</span>
                                <?php else: ?>
                                    <span class="order-no-cancel">Not cancellable</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php else: ?>
            <p class="empty">You have no order yet. <a href="index.php?page=shop">Visit the shop</a>.</p>
        <?php endif; ?>
    </section>
</main>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
