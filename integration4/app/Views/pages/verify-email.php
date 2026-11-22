<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> EMAIL CONFIRMATION</span>
<?php if ($state === 'success'): ?>
        <h1>Email confirmed.</h1>
        <p class="auth-intro">Thank you! Your account is now active and you can place orders.</p>
        <a class="primary-btn auth-submit" href="index.php?page=shop">Go to the shop <b>↗</b></a>
<?php elseif ($state === 'invalid'): ?>
        <h1>Link not valid.</h1>
        <p class="auth-intro">This confirmation link is wrong or was already used. Log in and ask for a new one if your account is still not confirmed.</p>
        <a class="primary-btn auth-submit" href="index.php?page=login">Log in <b>↗</b></a>
<?php else: ?>
        <h1>Please confirm your email.</h1>
        <p class="auth-intro">Your email address is not confirmed yet. Open the link we sent you, or ask for a new one.</p>
        <form class="auth-form" action="index.php?page=resend-verification" method="post">
            <?= \App\Security::csrfField() ?>
            <button class="primary-btn auth-submit" type="submit">Send the email again <b>↗</b></button>
        </form>
        <?php if (!empty($flash)): ?>
            <p class="auth-message <?= !empty($flash['error']) ? 'error' : '' ?>" role="status"><?= e($flash['message']) ?></p>
        <?php endif; ?>
<?php endif; ?>
        <a class="auth-back" href="index.php?page=shop">← Back to the shop</a>
    </section>
</main>
