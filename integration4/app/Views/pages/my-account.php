<main class="account-wrap">
    <header class="account-head">
        <span class="eyebrow"><i></i> MY ACCOUNT</span>
        <h1>Hello, <?= e($user['name']) ?>.</h1>
        <p class="auth-intro">
            <?= e($user['email']) ?>
            <?php if ($user['is_verified']): ?>
                <span class="badge ok">Email confirmed</span>
            <?php else: ?>
                <span class="badge warn">Email not confirmed</span>
                <a href="index.php?page=verify-email">Confirm it</a>
            <?php endif; ?>
        </p>
        <?php if (!empty($flash)): ?>
            <p class="account-flash <?= !empty($flash['error']) ? 'error' : '' ?>" role="status"><?= e($flash['message']) ?></p>
        <?php endif; ?>
    </header>

    <div class="account-grid">
        <!-- Profile -->
        <section class="account-card">
            <h2>Personal information</h2>
            <form class="auth-form" action="index.php?page=profile-update" method="post">
                <?= \App\Security::csrfField() ?>
                <label for="name">Full name</label>
                <input id="name" name="name" value="<?= e($user['name']) ?>">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="<?= e($user['email']) ?>">
                <small class="field-hint">If you change it, you will have to confirm the new address.</small>
                <label for="phone">Phone number</label>
                <input id="phone" name="phone" type="tel" value="<?= e($user['phone'] ?? '') ?>" placeholder="22 123 456">
                <button class="primary-btn auth-submit" type="submit">Save changes <b>↗</b></button>
            </form>
        </section>

        <!-- Change password -->
        <section class="account-card">
            <h2>Change password</h2>
            <form class="auth-form" action="index.php?page=change-password" method="post">
                <?= \App\Security::csrfField() ?>
                <label for="current_password">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password">
                <label for="new_password">New password</label>
                <input id="new_password" name="password" type="password" autocomplete="new-password">
                <small class="field-hint">At least 8 characters, with a letter and a number.</small>
                <label for="new_confirm">Confirm new password</label>
                <input id="new_confirm" name="confirmPassword" type="password" autocomplete="new-password">
                <button class="primary-btn auth-submit" type="submit">Change password <b>↗</b></button>
            </form>
        </section>
    </div>

    <!-- Addresses -->
    <section class="account-card wide">
        <h2>Delivery addresses</h2>
        <?php if (!$addresses): ?>
            <p class="auth-intro">You have no saved address yet. Add one below: it will be ready at checkout.</p>
        <?php endif; ?>

        <div class="address-list">
            <?php foreach ($addresses as $a): ?>
                <article class="address-item <?= $a['is_default'] ? 'is-default' : '' ?>">
                    <div>
                        <strong><?= e($a['street']) ?></strong>
                        <?php if ($a['is_default']): ?><span class="badge ok">Default</span><?php endif; ?>
                        <p><?= e($a['city']) ?>, <?= e($a['governorate']) ?><?= $a['postal_code'] ? ' ' . e($a['postal_code']) : '' ?></p>
                        <?php if ($a['phone']): ?><p><?= e($a['phone']) ?></p><?php endif; ?>
                    </div>
                    <div class="address-actions">
                        <a href="index.php?page=my-account&amp;edit=<?= (int) $a['id'] ?>#address-form">Edit</a>
                        <?php if (!$a['is_default']): ?>
                            <form action="index.php?page=address-default" method="post">
                                <?= \App\Security::csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <button type="submit" class="link-btn">Make default</button>
                            </form>
                        <?php endif; ?>
                        <form action="index.php?page=address-delete" method="post" onsubmit="return confirm('Delete this address?');">
                            <?= \App\Security::csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                            <button type="submit" class="link-btn danger">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <h3 id="address-form"><?= $edit ? 'Edit this address' : 'Add an address' ?></h3>
        <form class="auth-form address-form" action="index.php?page=address-save" method="post">
            <?= \App\Security::csrfField() ?>
            <input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
            <label for="street">Street and number</label>
            <input id="street" name="street" value="<?= e($edit['street'] ?? '') ?>">
            <label for="city">City</label>
            <input id="city" name="city" value="<?= e($edit['city'] ?? '') ?>">
            <label for="governorate">Governorate</label>
            <select id="governorate" name="governorate">
                <option value="">Choose...</option>
                <?php foreach ($governorates as $g): ?>
                    <option value="<?= e($g) ?>" <?= ($edit['governorate'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="postal_code">Postal code (optional)</label>
            <input id="postal_code" name="postal_code" inputmode="numeric" maxlength="4" value="<?= e($edit['postal_code'] ?? '') ?>">
            <label for="addr_phone">Phone for the delivery (optional)</label>
            <input id="addr_phone" name="phone" type="tel" value="<?= e($edit['phone'] ?? '') ?>" placeholder="22 123 456">
            <button class="primary-btn auth-submit" type="submit"><?= $edit ? 'Save address' : 'Add address' ?> <b>↗</b></button>
            <?php if ($edit): ?><a class="auth-back" href="index.php?page=my-account">Cancel</a><?php endif; ?>
        </form>
    </section>

    <!-- Delete account -->
    <?php if ($user['role'] !== 'admin'): ?>
    <section class="account-card wide danger-zone">
        <h2>Delete my account</h2>
        <p class="auth-intro">This removes your account and your saved addresses. It cannot be undone.</p>
        <form class="auth-form" action="index.php?page=account-delete" method="post" onsubmit="return confirm('Delete your account for good?');">
            <?= \App\Security::csrfField() ?>
            <label for="del_password">Type your password to confirm</label>
            <input id="del_password" name="password" type="password" autocomplete="current-password">
            <button class="primary-btn auth-submit danger-btn" type="submit">Delete my account</button>
        </form>
    </section>
    <?php endif; ?>
</main>
