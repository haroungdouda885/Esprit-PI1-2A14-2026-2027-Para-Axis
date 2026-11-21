<?php use App\Models\Donation; ?>
<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">YOUR DONATIONS</span>
                <h2>My <em>donations.</em></h2>
            </div>
            <p>Follow your donations<br>and cancel one while it is pending.</p>
        </div>
        <a class="back-link" href="index.php?page=donations">← Back to associations</a>

        <?php if (!$user): ?>
            <div class="empty-box">Please <a href="index.php?page=login&amp;next=my-donations">log in</a> to see your donations.</div>
        <?php elseif (!$rows): ?>
            <div class="empty-box">You have not made any donation yet. <a href="index.php?page=donations">See the associations</a>.</div>
        <?php else: ?>
            <div class="my-table">
                <table class="tbl">
                    <thead>
                        <tr><th>ASSOCIATION</th><th>DATE</th><th>DONATION</th><th>VALUE</th><th>STATUS</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $d): ?>
                            <tr>
                                <td><strong><?= e($d['association']['name'] ?? 'Deleted association') ?></strong></td>
                                <td><?= e(fmtDate($d['created_at'])) ?></td>
                                <td>
                                    <?php if ($d['type'] === 'money'): ?>
                                        💰 Money
                                    <?php else: ?>
                                        <?php foreach ($d['items'] as $i): ?>
                                            <div><?= e($i['product']['emoji']) ?> <?= e($i['product']['name']) ?> × <?= (int) $i['quantity'] ?></div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if ((int) $d['anonymous'] === 1): ?><small class="muted-note">Anonymous</small><?php endif; ?>
                                </td>
                                <td><?= number_format((float) $d['amount'], 2) ?> TND</td>
                                <td><span class="badge <?= $d['status'] === 'cancelled' ? 'off' : ($d['status'] === 'pending' ? 'warn' : 'ok') ?>"><?= ucfirst(e($d['status'])) ?></span></td>
                                <td>
                                    <?php if (in_array($d['status'], Donation::CANCELLABLE, true)): ?>
                                        <form method="post" action="index.php?page=donation-cancel" onsubmit="return confirm('Cancel this donation?');">
                                            <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                                            <button type="submit" class="cancel-btn">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
