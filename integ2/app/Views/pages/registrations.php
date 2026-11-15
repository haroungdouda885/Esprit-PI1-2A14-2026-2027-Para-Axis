<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">EVENTS &amp; WORKSHOPS</span>
        <h1>Registrations <span>(<?= count($registrations) ?>)</span></h1>
        <p>Cancelling a registration releases the seat. Re-registering checks the capacity first.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3><?= count($registrations) ?> registration<?= count($registrations) === 1 ? '' : 's' ?></h3>
        <form method="get" action="index.php" class="row-actions">
            <input type="hidden" name="page" value="registrations">
            <select name="event" onchange="this.form.submit()" aria-label="Filter by event">
                <option value="0">All events</option>
                <?php foreach ($events as $ev): ?>
                    <option value="<?= (int) $ev['id'] ?>" <?= (int) $ev['id'] === $eventId ? 'selected' : '' ?>><?= e($ev['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" onchange="this.form.submit()" aria-label="Filter by status">
                <?php foreach (['All', 'registered', 'cancelled'] as $s): ?>
                    <option <?= $s === $status ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="table-scroll">
        <table id="registrationsTable">
            <thead>
                <tr><th>ID</th><th>EVENT</th><th>PARTICIPANT</th><th>PHONE</th><th>STATUS</th><th>DATE</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $r): $id = (int) $r['id']; $on = $r['status'] === 'registered'; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($r['event']['title'] ?? '—') ?></strong><br><small><?= $r['event'] ? e(fmtDate($r['event']['event_date'])) : '' ?></small></td>
                        <td><strong><?= e($r['participant_name']) ?></strong><br><small><?= e($r['participant_email']) ?></small></td>
                        <td><?= e($r['phone'] ?: '—') ?></td>
                        <td><span class="status <?= $on ? 'delivered' : 'pending' ?>"><?= e($r['status']) ?></span></td>
                        <td><?= e(fmtDate($r['created_at'] ?? null)) ?></td>
                        <td class="row-actions">
                            <form method="post" action="index.php?page=registration-toggle">
                                <input type="hidden" name="registration_id" value="<?= $id ?>">
                                <input type="hidden" name="event" value="<?= $eventId ?>">
                                <input type="hidden" name="status" value="<?= e($status) ?>">
                                <button type="submit" class="action-btn"><?= $on ? 'Cancel' : 'Re-register' ?></button>
                            </form>
                            <form method="post" action="index.php?page=registration-delete" onsubmit="return confirm('Delete this registration?');">
                                <input type="hidden" name="registration_id" value="<?= $id ?>">
                                <input type="hidden" name="event" value="<?= $eventId ?>">
                                <input type="hidden" name="status" value="<?= e($status) ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$registrations): ?>
                    <tr><td colspan="7">No registration for this filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
