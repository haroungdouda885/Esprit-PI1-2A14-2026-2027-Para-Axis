<?php use App\Models\Event; ?>
<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">EVENTS &amp; WORKSHOPS</span>
                <h2>Learn, meet, <em>feel better.</em></h2>
            </div>
            <p>Free consultations and small workshops<br>with our pharmacists.</p>
        </div>

        <div class="toolbar">
            <div class="filters">
                <?php foreach (['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All events'] as $key => $label): ?>
                    <a class="filter <?= $filter === $key ? 'active' : '' ?>"
                       href="index.php?page=events&amp;filter=<?= $key ?><?= $q !== '' ? '&amp;q=' . urlencode($q) : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
            <div class="toolbar-right">
                <a class="text-link" href="index.php?page=my-registrations">My registrations <span>→</span></a>
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="events">
                    <input type="hidden" name="filter" value="<?= e($filter) ?>">
                    <input class="search" name="q" value="<?= e($q) ?>" placeholder="Search events..." aria-label="Search events">
                </form>
            </div>
        </div>

        <div class="product-grid event-grid">
            <?php foreach ($events as $ev): $id = (int) $ev['id']; $cap = (int) $ev['capacity']; ?>
                <article class="product-card event-card">
                    <div class="product-image" style="background:<?= Event::colorOf($ev) ?>"><span><?= Event::emojiOf($ev) ?></span></div>
                    <div class="event-date"><?= e(fmtDate($ev['event_date'])) ?></div>
                    <h3><?= e($ev['title']) ?></h3>
                    <p class="product-desc"><?= e($ev['description']) ?></p>
                    <div class="event-meta">📍 <?= e($ev['location']) ?></div>
                    <div class="seats <?= $ev['full'] ? 'full' : '' ?>"><i style="width:<?= min(100, (int) round($ev['taken'] / $cap * 100)) ?>%"></i></div>
                    <div class="seat-text">
                        <span><?= $ev['taken'] ?> / <?= $cap ?> seats taken</span>
                        <span><?= $ev['full'] ? 'No seat left' : ($cap - $ev['taken']) . ' left' ?></span>
                    </div>
                    <div class="product-bottom">
                        <?php if ($ev['past']): ?>
                            <small class="out">Event finished</small>
                        <?php elseif ($ev['isMine']): ?>
                            <span class="badge ok">✓ You are registered</span>
                        <?php elseif ($ev['full']): ?>
                            <span class="badge off">Full</span>
                        <?php elseif (!$user): ?>
                            <a class="primary-btn" href="index.php?page=login&amp;next=events">Register <b>↗</b></a>
                        <?php else: ?>
                            <button type="button" class="primary-btn" data-register="<?= $id ?>"
                                    data-title="<?= e($ev['title']) ?>"
                                    data-when="<?= e(fmtDate($ev['event_date']) . ' · ' . $ev['location']) ?>">Register <b>↗</b></button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$events): ?>
                <div class="empty-results">No events found. Try another filter or search.</div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php if ($user): ?>
    <!-- Registration form (opened by the "Register" buttons) -->
    <div class="modal-back" id="regModal">
        <form class="modal-box" method="post" action="index.php?page=event-register">
            <input type="hidden" name="event_id" id="regEvent">
            <div class="row"><h3>Register to an event</h3><button type="button" id="regClose" class="link-btn">✕</button></div>
            <p class="reg-event"><strong id="regTitle"></strong><br><small id="regWhen"></small></p>
            <label>Full name<input name="name" required minlength="2" value="<?= e($user['name']) ?>"></label>
            <label>Email address<input name="email" type="email" required value="<?= e($user['email']) ?>"></label>
            <label>Phone<input name="phone" type="tel" required placeholder="e.g. 22 123 456"></label>
            <button class="primary-btn" type="submit">Confirm registration ↗</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
