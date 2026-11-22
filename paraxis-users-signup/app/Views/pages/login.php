<main class="auth-wrap">
<?php if ($u = currentUser()): ?>
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> YOUR PAR'AXIS ACCOUNT</span>
        <h1>You are logged in.</h1>
        <table class="status-table">
            <tr><th>Name</th><td><?= e($u['name']) ?></td></tr>
            <tr><th>Email</th><td><?= e($u['email']) ?></td></tr>
            <tr><th>Role</th><td><?= e($u['role']) ?></td></tr>
            <tr><th>Email confirmed</th><td><?= !empty($u['is_verified']) ? '✅ yes' : '❌ no' ?></td></tr>
        </table>
        <a class="primary-btn auth-submit" href="index.php?page=logout">Log out <b>↗</b></a>
    </section>
<?php else: ?>
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> YOUR PAR'AXIS ACCOUNT</span>
        <h1>Welcome back.</h1>
        <p class="auth-intro">Log in as a customer or as an admin (admins are sent to the back office).</p>
        <form class="auth-form" action="index.php?page=login" method="post">
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <?= \App\Security::csrfField() ?>
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password">
            <div class="auth-row">
                <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
            </div>
            <?= \App\Security::captchaWidget() ?>
            <button class="primary-btn auth-submit" type="submit">Log in <b>↗</b></button>
        </form>
        <p class="auth-switch">New to Par'Axis? <a href="index.php?page=signup<?= $next !== 'verify-email' ? '&amp;next=' . e($next) : '' ?>">Create an account</a>.</p>
        <p class="auth-message <?= $error ? 'error' : '' ?>" role="status"><?= e($message) ?></p>
    </section>
<?php endif; ?>
</main>
