<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> PASSWORD HELP</span>
<?php if ($sent): ?>
        <h1>Check your inbox.</h1>
        <p class="auth-intro">If an account exists for <strong><?= e($email) ?></strong>, we sent a link to choose a new password. It is valid for 60 minutes.</p>
        <a class="primary-btn auth-submit" href="index.php?page=login">Back to log in <b>↗</b></a>
<?php else: ?>
        <h1>Forgot your password?</h1>
        <p class="auth-intro">Enter the email of your account and we will send you a link to choose a new one.</p>
        <form class="auth-form" action="index.php?page=forgot-password" method="post">
            <?= \App\Security::csrfField() ?>
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>">
            <?= \App\Security::captchaWidget() ?>
            <button class="primary-btn auth-submit" type="submit">Send the link <b>↗</b></button>
        </form>
        <p class="auth-switch">Remembered it? <a href="index.php?page=login">Log in</a>.</p>
        <p class="auth-message <?= $error ? 'error' : '' ?>" role="status"><?= e($message) ?></p>
<?php endif; ?>
        <a class="auth-back" href="index.php?page=shop">← Back to the shop</a>
    </section>
</main>
