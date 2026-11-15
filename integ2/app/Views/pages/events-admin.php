<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">EVENTS &amp; WORKSHOPS</span>
        <h1>Events <span>(<?= count($events) ?>)</span></h1>
        <p>All the events and workshops, with their seats and status.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== stats start ========== -->
<div class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span>Events</span></div><strong><?= $stats['events'] ?></strong><div class="stat-foot neutral"><?= $stats['finished'] ?> finished</div></article>
    <article class="stat-card"><div class="stat-top"><span>Upcoming</span></div><strong><?= $stats['upcoming'] ?></strong><div class="stat-foot neutral">visible and not started</div></article>
    <article class="stat-card"><div class="stat-top"><span>Registrations</span></div><strong><?= $stats['taken'] ?></strong><div class="stat-foot neutral"><?= $stats['cancelled'] ?> cancelled</div></article>
    <article class="stat-card"><div class="stat-top"><span>Seats filled</span></div><strong><?= $stats['filled'] ?>%</strong><div class="stat-foot neutral"><?= $stats['taken'] ?> of <?= $stats['seats'] ?> seats</div></article>
</div>
<!-- ========== stats end ========== -->

<!-- ========== form start ========== -->
<?php $v = $edit ?? ['title' => '', 'description' => '', 'location' => '', 'event_date' => '', 'capacity' => 20, 'active' => 1]; ?>
<div class="panel">
    <div class="panel-head">
        <h3><?= $edit ? 'Edit event #' . (int) $edit['id'] : 'New event' ?></h3>
        <?php if ($edit): ?><a class="action-btn" href="index.php?page=events-admin">Cancel edit</a><?php endif; ?>
    </div>
    <form method="post" action="index.php?page=event-save" class="article-form">
        <?php if ($edit): ?><input type="hidden" name="event_id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
        <div class="form-two">
            <label>Title<input name="title" required value="<?= e($v['title']) ?>" placeholder="e.g. Free skin consultation day"></label>
            <label>Location<input name="location" required value="<?= e($v['location']) ?>" placeholder="ParaSanté store, Tunis"></label>
        </div>
        <div class="form-two">
            <label>Date and time<input name="event_date" type="datetime-local" required value="<?= e($v['event_date']) ?>"></label>
            <label>Capacity (seats)<input name="capacity" type="number" min="1" step="1" required value="<?= (int) $v['capacity'] ?>"></label>
        </div>
        <label>Description<textarea name="description" rows="3" placeholder="Describe the event…"><?= e($v['description']) ?></textarea></label>
        <label class="check-label"><input type="checkbox" name="active" <?= (int) $v['active'] === 1 ? 'checked' : '' ?>> Visible to customers</label>
        <button type="submit" class="dark-btn"><?= $edit ? 'Save changes ↗' : 'Create event ↗' ?></button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <div>
            <h3>All events</h3>
            <p>Deleting an event also deletes its registrations.</p>
        </div>
        <input class="table-search" data-search="#eventsTable" placeholder="Search…">
    </div>
    <div class="table-scroll">
        <table id="eventsTable">
            <thead>
                <tr><th>ID</th><th>EVENT</th><th>DATE</th><th>LOCATION</th><th>SEATS</th><th>STATUS</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): $id = (int) $ev['id']; [$label, $cls] = $ev['status']; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td class="wrap-cell"><strong><?= e($ev['title']) ?></strong><br><small><?= e($ev['description']) ?></small></td>
                        <td><?= e(fmtDate($ev['event_date'])) ?></td>
                        <td><?= e($ev['location']) ?></td>
                        <td><?= $ev['taken'] ?> / <?= (int) $ev['capacity'] ?></td>
                        <td><span class="status <?= $cls ?>"><?= $label ?></span></td>
                        <td class="row-actions">
                            <a class="action-btn" href="index.php?page=registrations&amp;event=<?= $id ?>">Registrations</a>
                            <a class="action-btn" href="index.php?page=events-admin&amp;edit=<?= $id ?>">Edit</a>
                            <form method="post" action="index.php?page=event-delete" onsubmit="return confirm('Delete this event and all its registrations?');">
                                <input type="hidden" name="event_id" value="<?= $id ?>">
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$events): ?>
                    <tr><td colspan="7">No event yet. Use the form above to create the first one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
