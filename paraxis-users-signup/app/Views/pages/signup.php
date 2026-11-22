<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> YOUR PAR'AXIS ACCOUNT</span>
<?php if ($done): ?>
        <h1>Check your inbox.</h1>
        <p class="auth-intro">Your account is created. We sent a confirmation link to <strong><?= e($email) ?></strong>. Confirm your email to be able to place orders.</p>
        <?php if (!empty($mailFailed)): ?>
            <p class="auth-intro dev-note">We could not send the email right now. You can ask for a new one from the <a href="index.php?page=verify-email">confirmation page</a>.<?php if (!empty($mailError)): ?><br><small>Reason: <?= e($mailError) ?></small><?php endif; ?></p>
        <?php endif; ?>
        <a class="primary-btn auth-submit" href="index.php?page=<?= e($next) ?>">Continue <b>↗</b></a>
<?php else: ?>
        <h1>Create your account.</h1>
        <p class="auth-intro">Join Par'Axis and make your everyday care simpler.</p>
        <form class="auth-form" action="index.php?page=signup" method="post">
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <?= \App\Security::csrfField() ?>
            <label for="name">Full name</label>
            <input id="name" name="name" autocomplete="name" value="<?= e($name) ?>">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>">
            <label for="phone">Phone number</label>
            <input id="phone" name="phone" type="tel" autocomplete="tel" placeholder="22 123 456" value="<?= e($phone) ?>">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
            <small class="field-hint">At least 8 characters, with a letter and a number.</small>
            <label for="confirmPassword">Confirm password</label>
            <input id="confirmPassword" name="confirmPassword" type="password" autocomplete="new-password">
            <?= \App\Security::captchaWidget() ?>
            <button class="primary-btn auth-submit" type="submit">Create account <b>↗</b></button>
        </form>
        <p class="auth-switch">Already have an account? <a href="index.php?page=login<?= $next !== 'verify-email' ? '&amp;next=' . e($next) : '' ?>">Log in</a>.</p>
        <p class="auth-message error" role="status"><?= e($message) ?></p>
<?php endif; ?>
    </section>
</main>
