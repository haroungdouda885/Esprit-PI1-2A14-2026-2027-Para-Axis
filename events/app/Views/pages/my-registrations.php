<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">YOUR EVENTS</span>
                <h2>My <em>registrations.</em></h2>
            </div>
            <p>Follow your registrations<br>and cancel one if your plans change.</p>
        </div>
        <a class="back-link" href="index.php?page=events">← Back to events</a>

        <?php if (!$user): ?>
            <div class="empty-box">Please <a href="index.php?page=login&amp;next=my-registrations">log in</a> to see your registrations.</div>
        <?php elseif (!$rows): ?>
            <div class="empty-box">You have no registration yet. <a href="index.php?page=events">Browse the events</a>.</div>
        <?php else: ?>
            <div class="my-table">
                <table class="tbl">
                    <thead>
                        <tr><th>EVENT</th><th>DATE</th><th>LOCATION</th><th>STATUS</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): $on = $r['status'] === 'registered'; ?>
                            <tr>
                                <td><strong><?= e($r['event']['title']) ?></strong></td>
                                <td><?= e(fmtDate($r['event']['event_date'])) ?></td>
                                <td><?= e($r['event']['location']) ?></td>
                                <td><span class="badge <?= $on ? 'ok' : 'off' ?>"><?= $on ? 'Registered' : 'Cancelled' ?></span></td>
                                <td>
                                    <?php if ($on && !$r['past']): ?>
                                        <form method="post" action="index.php?page=registration-cancel" onsubmit="return confirm('Cancel this registration? Your seat will be released.');">
                                            <input type="hidden" name="registration_id" value="<?= (int) $r['id'] ?>">
                                            <button type="submit" class="cancel-btn">Cancel registration</button>
                                        </form>
                                    <?php elseif ($on): ?>
                                        <small>Finished</small>
                                    <?php elseif (!$r['past']): ?>
                                        <a class="link-btn" href="index.php?page=events">Register again</a>
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
