<main>
  <section class="section wrap">
    <div class="section-heading">
      <div><span class="eyebrow">CUSTOMER SUPPORT</span><h2>How can we <em>help?</em></h2></div>
      <p>Open a ticket, read the support team's answers, and continue the conversation.</p>
    </div>
    <div class="narrow">
<?php if (!$user): ?>
      <div class="form-card"><p>Please <a href="index.php?page=login&amp;next=support">log in</a> to open a support ticket and follow its answers.</p></div>
<?php else: ?>
      <form class="form-card" method="post" action="index.php?page=create-ticket">
        <h3>New ticket</h3>
        <label>Subject<input name="subject" required placeholder="e.g. My order has not arrived"></label>
        <label>Describe your issue<textarea name="message" required rows="4"></textarea></label>
        <button class="primary-btn" type="submit">Submit ticket ↗</button>
      </form>

      <h3>My tickets</h3>
  <?php foreach ($tickets as $t): $id = (int) $t['id']; $replies = $t['replies']; ?>
      <div class="form-card">
        <div class="row">
          <strong>#<?= $id ?> · <?= htmlspecialchars($t['subject'] ?? 'Support request') ?></strong>
          <span class="badge <?= ($t['status'] ?? '') === 'answered' ? 'ok' : '' ?>"><?= htmlspecialchars($t['status'] ?? 'open') ?></span>
        </div>
        <small>Submitted <?= htmlspecialchars($t['created_at'] ?? '') ?></small>
        <div class="thread customer"><small>Your message</small><p><?= nl2br(htmlspecialchars($t['message'] ?? '')) ?></p></div>

        <h4>Conversation</h4>
    <?php if (!$replies): ?>
        <p><small>No answer from the support team yet. Replies will appear here.</small></p>
    <?php endif; ?>
    <?php foreach ($replies as $r): $fromAdmin = ($r['author_role'] ?? '') === 'admin'; ?>
        <div class="thread <?= $fromAdmin ? 'admin' : 'customer' ?>">
          <small><?= $fromAdmin ? 'Support team' : 'You' ?> · <?= htmlspecialchars($r['created_at'] ?? '') ?></small>
          <p><?= nl2br(htmlspecialchars($r['message'] ?? '')) ?></p>
        </div>
    <?php endforeach; ?>

    <?php if (($t['status'] ?? 'open') !== 'closed'): ?>
        <form method="post" action="index.php?page=customer-reply">
          <input type="hidden" name="ticket_id" value="<?= $id ?>">
          <label for="reply-<?= $id ?>">Your reply</label>
          <textarea id="reply-<?= $id ?>" name="message" required rows="2" placeholder="Write a reply to the support team…"></textarea>
          <button class="primary-btn" type="submit">Send reply ↗</button>
        </form>
    <?php else: ?>
        <p><small>This ticket is closed. Please create a new ticket if you need more help.</small></p>
    <?php endif; ?>
      </div>
  <?php endforeach; ?>

  <?php if (!$tickets): ?>
      <div class="form-card"><p>You haven't submitted any tickets yet. Use the form above to contact our support team.</p></div>
  <?php endif; ?>
<?php endif; ?>
    </div>
  </section>
</main>
