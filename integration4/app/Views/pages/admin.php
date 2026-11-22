<div class="page-title">
  <div><span class="kicker">CUSTOMER SUPPORT</span><h1>Support tickets</h1><p>Manage requests and keep every customer conversation in one place.</p></div>
  <div class="ticket-total"><?= count($tickets) ?> ticket<?= count($tickets) === 1 ? '' : 's' ?></div>
</div>
<div class="admin-ticket-list">
<?php foreach ($tickets as $t): $ticketId=(int)($t['id'] ?? 0); $replies=$t['replies']; $status=$t['status'] ?? 'open'; ?>
  <article class="admin-ticket-card">
    <header class="admin-ticket-head">
      <div class="admin-ticket-identity">
        <span class="ticket-number">#<?= $ticketId ?></span>
        <div><h2><?= htmlspecialchars($t['subject'] ?? 'Support request') ?></h2><p><?= htmlspecialchars($t['customer_name'] ?? 'Customer') ?> <span>·</span> <?= htmlspecialchars($t['customer_email'] ?? '') ?></p><?php if (!empty($t['product'])): ?><span class="link-note">Product: <?= htmlspecialchars($t['product']['emoji'] . ' ' . $t['product']['name']) ?></span><?php endif; ?></div>
      </div>
      <div class="admin-ticket-tools">
        <form method="post" action="index.php?page=ticket-status" class="status-form">
          <input type="hidden" name="ticket_id" value="<?= $ticketId ?>">
          <label class="sr-only" for="status-<?= $ticketId ?>">Ticket status</label>
          <select id="status-<?= $ticketId ?>" name="status" class="ticket-status-select" onchange="this.form.submit()">
            <option value="open" <?= $status==='open'?'selected':'' ?>>Open</option>
            <option value="answered" <?= $status==='answered'?'selected':'' ?>>Answered</option>
            <option value="closed" <?= $status==='closed'?'selected':'' ?>>Closed</option>
          </select>
        </form>
        <form method="post" action="index.php?page=ticket-delete" onsubmit="return confirm('Delete this ticket and its entire conversation? This cannot be undone.');">
          <input type="hidden" name="ticket_id" value="<?= $ticketId ?>">
          <button type="submit" class="delete-ticket-btn">Delete ticket</button>
        </form>
      </div>
    </header>
    <div class="admin-ticket-body">
      <section class="original-message">
        <div class="message-avatar customer-avatar"><?= htmlspecialchars(strtoupper(substr($t['customer_name'] ?? 'C',0,1))) ?></div>
        <div class="original-message-content"><div class="message-meta"><strong><?= htmlspecialchars($t['customer_name'] ?? 'Customer') ?></strong><span>Original request · <?= htmlspecialchars($t['created_at'] ?? '') ?></span></div><p><?= nl2br(htmlspecialchars($t['message'] ?? '')) ?></p></div>
      </section>
      <section class="admin-conversation">
        <div class="conversation-heading"><h3>Conversation</h3><span><?= count($replies) ?> repl<?= count($replies)===1?'y':'ies' ?></span></div>
        <div class="admin-chat-thread">
          <?php if (!$replies): ?><div class="conversation-empty">No replies yet. Send the first answer to the customer.</div><?php endif; ?>
          <?php foreach ($replies as $r): $isAdmin=($r['author_role'] ?? '')==='admin'; ?>
            <div class="chat-line <?= $isAdmin?'from-support':'from-customer' ?>">
              <div class="chat-avatar <?= $isAdmin?'support-avatar':'customer-avatar' ?>"><?= $isAdmin?'S':'C' ?></div>
              <div class="chat-message-wrap"><div class="chat-message-meta"><strong><?= $isAdmin?'Support team':'Customer' ?></strong><span><?= htmlspecialchars($r['created_at'] ?? '') ?></span></div><div class="chat-bubble"><?= nl2br(htmlspecialchars($r['message'] ?? '')) ?></div></div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if ($status !== 'closed'): ?>
          <form method="post" action="index.php?page=admin-reply" class="admin-reply-form">
            <input type="hidden" name="ticket_id" value="<?= $ticketId ?>">
            <label for="admin-reply-<?= $ticketId ?>">Reply to customer</label>
            <div class="admin-reply-compose"><textarea id="admin-reply-<?= $ticketId ?>" name="message" required rows="3" placeholder="Write a helpful response…"></textarea><button type="submit" class="dark-btn">Send reply <span>↗</span></button></div>
          </form>
        <?php else: ?><p class="ticket-closed-note">This ticket is closed. Change its status to Open or Answered to reply again.</p><?php endif; ?>
      </section>
    </div>
  </article>
<?php endforeach; ?>
<?php if (!$tickets): ?><div class="empty-ticket-state"><div>✉</div><h2>No support tickets yet</h2><p>Customer requests will appear here when they submit a ticket.</p></div><?php endif; ?>
</div>
