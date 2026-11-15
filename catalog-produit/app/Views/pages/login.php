<main class="auth-wrap">
    <section class="auth-card">
        <div class="auth-mark">✿</div>
        <span class="eyebrow"><i></i> YOUR PARASANTÉ ACCOUNT</span>
        <h1>Welcome back.</h1>
        <p class="auth-intro">Log in as a customer or as an admin (admins are sent to the back office).</p>
        <form class="auth-form" action="index.php?page=login" method="post">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password">
            <button class="primary-btn auth-submit" type="submit">Log in <b>↗</b></button>
        </form>
        <p class="auth-switch">New to ParaSanté? <a href="index.php?page=signup">Create an account</a>.</p>
        <p class="auth-message <?= $error ? 'error' : '' ?>" role="status"><?= e($message) ?></p>
        <a class="auth-back" href="index.php?page=shop">← Back to home</a>
    </section>
</main>
