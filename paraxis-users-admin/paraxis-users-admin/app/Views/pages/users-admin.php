<!-- ========== title-wrapper start ========== -->
<div class="page-title">
    <div>
        <span class="kicker">USER MANAGEMENT</span>
        <h1>Users <span>(<?= (int) $counts['total'] ?>)</span></h1>
        <p>Customers and admins of Par'Axis: search, block, change the role or create an account.</p>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- ========== stats start ========== -->
<div class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span>Customers</span></div><strong><?= (int) $counts['customers'] ?></strong><div class="stat-foot neutral">registered accounts</div></article>
    <article class="stat-card"><div class="stat-top"><span>Admins</span></div><strong><?= (int) $counts['admins'] ?></strong><div class="stat-foot neutral">back office access</div></article>
    <article class="stat-card"><div class="stat-top"><span>Blocked</span></div><strong><?= (int) $counts['blocked'] ?></strong><div class="stat-foot neutral">cannot log in</div></article>
    <article class="stat-card"><div class="stat-top"><span>Email not confirmed</span></div><strong><?= (int) $counts['unverified'] ?></strong><div class="stat-foot neutral">cannot place orders</div></article>
</div>
<!-- ========== stats end ========== -->

<!-- ========== form start ========== -->
<div class="panel">
    <div class="panel-head"><h3>New user</h3></div>
    <form method="post" action="index.php?page=user-create" class="article-form">
        <?= \App\Security::csrfField() ?>
        <div class="form-two">
            <label>Full name<input name="name" required placeholder="e.g. Amel Ben Salah"></label>
            <label>Email<input name="email" type="email" required placeholder="amel@example.com"></label>
        </div>
        <div class="form-two">
            <label>Phone (optional)<input name="phone" type="tel" placeholder="22 123 456"></label>
            <label>Temporary password<input name="password" type="password" required autocomplete="new-password" placeholder="8+ characters, letter and number"></label>
        </div>
        <label>Role
            <select name="role">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
        </label>
        <button type="submit" class="dark-btn">Create user ↗</button>
    </form>
</div>
<!-- ========== form end ========== -->

<!-- ========== tables-wrapper start ========== -->
<div class="panel table-panel">
    <div class="panel-head">
        <h3>All users</h3>
        <form class="filter-form users-filter" method="get" action="index.php">
            <input type="hidden" name="page" value="users-admin">
            <input class="table-search" name="q" value="<?= e($q) ?>" placeholder="Name, email or phone…">
            <select name="role" onchange="this.form.submit()" aria-label="Filter by role">
                <option value="">All roles</option>
                <option value="customer" <?= $role === 'customer' ? 'selected' : '' ?>>Customers</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admins</option>
            </select>
            <select name="status" onchange="this.form.submit()" aria-label="Filter by status">
                <option value="">Any status</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="blocked" <?= $status === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                <option value="unverified" <?= $status === 'unverified' ? 'selected' : '' ?>>Email not confirmed</option>
            </select>
            <button type="submit" class="action-btn">Search</button>
            <?php if ($q !== '' || $role !== '' || $status !== ''): ?>
                <a class="action-btn" href="index.php?page=users-admin">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-scroll">
        <table id="usersTable">
            <thead>
                <tr><th>ID</th><th>USER</th><th>PHONE</th><th>ROLE</th><th>STATUS</th><th>JOINED</th><th>ACTIONS</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): $id = (int) $u['id']; $isMe = $id === $me; ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><strong><?= e($u['name']) ?></strong><?= $isMe ? ' <small>(you)</small>' : '' ?><br><small><?= e($u['email']) ?></small></td>
                        <td><?= e($u['phone'] ?: '—') ?></td>
                        <td>
                            <?php if ($isMe): ?>
                                <span class="u-pill dark"><?= e(ucfirst($u['role'])) ?></span>
                            <?php else: ?>
                                <form method="post" action="index.php?page=user-role">
                                    <?= \App\Security::csrfField() ?>
                                    <input type="hidden" name="user_id" value="<?= $id ?>">
                                    <label class="sr-only" for="role-<?= $id ?>">Role</label>
                                    <select id="role-<?= $id ?>" name="role" onchange="this.form.submit()">
                                        <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['is_blocked']): ?><span class="u-pill bad">Blocked</span><?php else: ?><span class="u-pill ok">Active</span><?php endif; ?>
                            <?php if (!$u['is_verified']): ?><span class="u-pill warn">Email not confirmed</span><?php endif; ?>
                        </td>
                        <td><?= e(substr((string) $u['created_at'], 0, 10)) ?></td>
                        <td class="row-actions">
                            <?php if (!$u['is_verified']): ?>
                                <form method="post" action="index.php?page=user-verify">
                                    <?= \App\Security::csrfField() ?>
                                    <input type="hidden" name="user_id" value="<?= $id ?>">
                                    <button type="submit" class="action-btn">Confirm email</button>
                                </form>
                            <?php endif; ?>
                            <?php if (!$isMe): ?>
                                <form method="post" action="index.php?page=user-block">
                                    <?= \App\Security::csrfField() ?>
                                    <input type="hidden" name="user_id" value="<?= $id ?>">
                                    <button type="submit" class="action-btn"><?= $u['is_blocked'] ? 'Unblock' : 'Block' ?></button>
                                </form>
                                <form method="post" action="index.php?page=user-delete" onsubmit="return confirm('Delete this account for good? Its saved addresses are deleted too.');">
                                    <?= \App\Security::csrfField() ?>
                                    <input type="hidden" name="user_id" value="<?= $id ?>">
                                    <button type="submit" class="action-btn danger">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="7">No user matches this search.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========== tables-wrapper end ========== -->

<?php if ($flash): ?>
    <div class="admin-toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
