<?php use App\Models\Association; ?>
<main>
    <section class="section wrap">
        <div class="section-heading">
            <div>
                <span class="eyebrow">DONATIONS &amp; ASSOCIATIONS</span>
                <h2>Give a little, <em>care a lot.</em></h2>
            </div>
            <p>Support our partner associations<br>with money or care products.</p>
        </div>

        <div class="toolbar">
            <div class="filters">
                <a class="filter <?= $category === 0 ? 'active' : '' ?>" href="index.php?page=donations<?= $q !== '' ? '&amp;q=' . urlencode($q) : '' ?>">All causes</a>
                <?php foreach ($categories as $c): ?>
                    <a class="filter <?= $category === (int) $c['id'] ? 'active' : '' ?>"
                       href="index.php?page=donations&amp;category=<?= (int) $c['id'] ?><?= $q !== '' ? '&amp;q=' . urlencode($q) : '' ?>"><?= e(($c['emoji'] ?? '') . ' ' . $c['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="toolbar-right">
                <?php if (!isAdmin()): ?><a class="text-link" href="index.php?page=my-donations">My donations <span>→</span></a><?php endif; ?>
                <form method="get" action="index.php">
                    <input type="hidden" name="page" value="donations">
                    <input type="hidden" name="category" value="<?= $category ?>">
                    <input class="search" name="q" value="<?= e($q) ?>" placeholder="Search associations..." aria-label="Search associations">
                </form>
            </div>
        </div>

        <div class="product-grid event-grid">
            <?php foreach ($associations as $a): $id = (int) $a['id']; ?>
                <article class="product-card event-card">
                    <div class="product-image" style="background:<?= Association::colorOf($a) ?>">
                        <?php if (!empty($a['logo'])): ?>
                            <img class="assoc-logo" src="<?= e($a['logo']) ?>" alt="<?= e($a['name']) ?>">
                        <?php else: ?>
                            <span><?= Association::emojiOf($a) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="event-date"><?= e(Association::categoryName($a)) ?></div>
                    <h3><?= e($a['name']) ?></h3>
                    <p class="product-desc"><?= e($a['description']) ?></p>
                    <div class="event-meta">📍 <?= e($a['address']) ?></div>
                    <div class="seat-text">
                        <span><strong><?= number_format($a['received'], 2) ?> TND</strong> received</span>
                        <span><?= $a['count'] ?> donation<?= $a['count'] === 1 ? '' : 's' ?></span>
                    </div>
                    <div class="product-bottom">
                        <?php if (isAdmin()): ?>
                            <small class="read-only">View only</small>
                        <?php elseif (!$user): ?>
                            <a class="primary-btn" href="index.php?page=login&amp;next=donations">Donate <b>↗</b></a>
                        <?php else: ?>
                            <button type="button" class="primary-btn" data-donate="<?= $id ?>" data-name="<?= e($a['name']) ?>"
                                    data-cause="<?= e(Association::categoryName($a)) ?>">Donate <b>↗</b></button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if (!$associations): ?>
                <div class="empty-results">No association found. Try another cause or search.</div>
            <?php endif; ?>
        </div>

        <?php if ($latest): ?>
            <div class="donation-wall">
                <h3>Latest donations</h3>
                <ul>
                    <?php foreach ($latest as $d): ?>
                        <li>
                            <strong><?= e(\App\Models\Donation::publicName($d)) ?></strong>
                            gave <?= $d['type'] === 'money' ? number_format((float) $d['amount'], 2) . ' TND' : 'care products' ?>
                            to <strong><?= e($d['association']) ?></strong>
                            <small><?= e(fmtDate($d['created_at'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php if ($user && !isAdmin()): ?>
    <!-- Donation form (opened by the "Donate" buttons) -->
    <div class="modal-back" id="donModal">
        <form class="modal-box donation-box" method="post" action="index.php?page=donate">
            <input type="hidden" name="association_id" id="donAssoc">
            <div class="row"><h3>Make a donation</h3><button type="button" id="donClose" class="link-btn">✕</button></div>
            <p class="reg-event"><strong id="donName"></strong><br><small id="donCause"></small></p>

            <div class="type-switch">
                <label><input type="radio" name="type" value="money" checked> 💰 Money</label>
                <label><input type="radio" name="type" value="product"> 🧴 Products</label>
            </div>

            <div data-part="money">
                <label>Amount (TND)<input name="amount" type="number" min="1" max="10000" step="0.5" value="20"></label>
            </div>
            <div data-part="product" hidden>
                <p class="hint">Choose the products and the quantity. We deliver them to the association.</p>
                <div class="give-list">
                    <?php foreach ($products as $p): ?>
                        <label class="give-line">
                            <span><?= e($p['emoji']) ?> <?= e($p['name']) ?><small><?= number_format((float) $p['price'], 2) ?> TND · <?= (int) $p['stock'] ?> in stock</small></span>
                            <input type="number" name="qty[<?= (int) $p['id'] ?>]" min="0" max="<?= (int) $p['stock'] ?>" value="0" data-price="<?= (float) $p['price'] ?>">
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="give-total">Value: <strong id="donTotal">0.00</strong> TND</p>
            </div>

            <div class="form-two-front">
                <label>Full name<input name="donor_name" required minlength="2" value="<?= e($user['name']) ?>"></label>
                <label>Email<input name="donor_email" type="email" required value="<?= e($user['email']) ?>"></label>
            </div>
            <label>Message (optional)<textarea name="message" rows="2" maxlength="500" placeholder="A few words for the association…"></textarea></label>
            <label class="opt"><input type="checkbox" name="anonymous"> Stay anonymous in the public list</label>
            <button class="primary-btn" type="submit">Confirm my donation ↗</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
