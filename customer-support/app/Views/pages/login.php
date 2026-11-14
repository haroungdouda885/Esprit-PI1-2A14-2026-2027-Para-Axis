<main class="auth-wrap">
  <section class="auth-card">
    <div class="auth-mark">✿</div>
    <span class="eyebrow"><i></i> YOUR PARASANTÉ ACCOUNT</span>
    <h1>Welcome back.</h1>
    <p class="auth-intro">Demo access: choose customer or admin. No real user table is connected yet.</p>
    <form method="post" action="index.php?page=login" class="auth-form">
      <label for="name">Name</label>
      <input id="name" name="name" value="Demo User" required>
      <label for="email">Email address</label>
      <input id="email" name="email" type="email" value="customer@example.test" required>
      <label for="role">Demo role</label>
      <select id="role" name="role">
        <option value="customer">Customer</option>
        <option value="admin">Support admin</option>
      </select>
      <button class="primary-btn auth-submit" type="submit">Continue <b>↗</b></button>
    </form>
    <a class="auth-back" href="index.php?page=support">← Back to support</a>
  </section>
</main>
