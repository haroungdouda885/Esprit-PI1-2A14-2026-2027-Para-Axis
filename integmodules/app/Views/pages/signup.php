<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> YOUR PAR'AXIS ACCOUNT</span>
        <h1>Create your account.</h1>
        <p class="auth-intro">Join Par'Axis and make your everyday care simpler.</p>
        <form class="auth-form" action="index.php?page=signup" method="post">
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <label for="name">Full name</label>
            <input id="name" name="name" value="<?= e($name) ?>">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="<?= e($email) ?>">
            <label for="password">Password</label>
            <input id="password" name="password" type="password">
            <label for="confirmPassword">Confirm password</label>
            <input id="confirmPassword" name="confirmPassword" type="password">
            <button class="primary-btn auth-submit" type="submit">Create account <b>↗</b></button>
        </form>
        <p class="auth-switch">Already have an account? <a href="index.php?page=login<?= $next !== 'shop' ? '&amp;next=' . e($next) : '' ?>">Log in</a>.</p>
        <p class="auth-message error" role="status"><?= e($message) ?></p>
        <a class="auth-back" href="index.php?page=shop">← Back to the shop</a>
    </section>
</main>
