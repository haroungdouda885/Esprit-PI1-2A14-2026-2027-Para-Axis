<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> PASSWORD HELP</span>
<?php if (!$valid): ?>
        <h1>Link not valid.</h1>
        <p class="auth-intro">This reset link is wrong, expired or already used. Ask for a new one.</p>
        <a class="primary-btn auth-submit" href="index.php?page=forgot-password">Ask for a new link <b>↗</b></a>
<?php else: ?>
        <h1>Choose a new password.</h1>
        <p class="auth-intro">At least 8 characters, with a letter and a number.</p>
        <form class="auth-form" action="index.php?page=reset-password" method="post">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <?= \App\Security::csrfField() ?>
            <label for="password">New password</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
            <label for="confirmPassword">Confirm new password</label>
            <input id="confirmPassword" name="confirmPassword" type="password" autocomplete="new-password">
            <button class="primary-btn auth-submit" type="submit">Save password <b>↗</b></button>
        </form>
        <p class="auth-message error" role="status"><?= e($message) ?></p>
<?php endif; ?>
        <a class="auth-back" href="index.php?page=login">← Back to log in</a>
    </section>
</main>
