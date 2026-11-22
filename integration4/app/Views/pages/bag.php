<main>
    <section class="section wrap">
        <a class="back-link" href="index.php?page=shop">← Continue shopping</a>
        <div class="section-heading">
            <div>
                <span class="eyebrow">YOUR BAG</span>
                <h2>Bag <em id="bagHeadCount">(<?= array_sum(array_column($lines, 'qty')) ?>)</em></h2>
            </div>
            <p>Demo only — no real payment is processed.</p>
        </div>

        <div class="bag-grid">
            <!-- ===== Bag lines ===== -->
            <section class="form-card" id="bagLines">
                <h3>Products</h3>
                <?php foreach ($lines as $l): $p = $l['product']; ?>
                    <div class="cart-line">
                        <div class="cart-emoji"><?= e($p['emoji'] ?: '•') ?></div>
                        <div class="cart-line-info">
                            <strong><?= e($p['name']) ?></strong>
                            <small><?= money($p['price']) ?> · <?= money($l['line_total']) ?></small>
                        </div>
                        <div class="qty">
                            <form method="post" action="index.php?page=bag-update" data-ajax-update>
                                <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="delta" value="-1">
                                <button type="submit" aria-label="Remove one">−</button>
                            </form>
                            <span><?= (int) $l['qty'] ?></span>
                            <form method="post" action="index.php?page=bag-update" data-ajax-update>
                                <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="delta" value="1">
                                <button type="submit" aria-label="Add one">+</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$lines): ?>
                    <p class="empty">Your bag is empty. <a href="index.php?page=shop">Visit the shop</a>.</p>
                <?php endif; ?>
                <div class="total"><span>Subtotal</span><strong><?= money($total) ?></strong></div>
            </section>

            <!-- ===== Checkout ===== -->
            <?php if (!$lines): ?>
                <section class="form-card"><h3>Checkout</h3><p class="empty">Add products to your bag first.</p></section>
            <?php elseif (!$user): ?>
                <section class="form-card">
                    <h3>Checkout</h3>
                    <p>Log in with your customer account to place the order.</p>
                    <a class="primary-btn" href="index.php?page=login&amp;next=bag">Log in to checkout ↗</a>
                    <p><small>Demo customer: customer@paraxis.demo / Customer123!</small></p>
                </section>
            <?php else: ?>
                <form class="form-card" method="post" action="index.php?page=checkout" autocomplete="off">
                    <h3>Checkout</h3>
                    <?php if (!empty($error)): ?>
                        <p class="form-card error-box" role="alert"><?= e($error) ?></p>
                    <?php endif; ?>
                    <label>Delivery address<input name="address" required placeholder="Street, city" value="<?= e($address) ?>"></label>
                    <label>Payment method
                        <select name="method" id="paymentMethod">
                            <?php foreach ($methods as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $key === $method ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div id="paymentFields" class="<?= $method === 'card' ? '' : 'hidden' ?>"<?= $method === 'card' ? '' : ' style="display:none"' ?>>
                        <div class="payment-test"><b>Test gateway only.</b> Use card number <b>4242 4242 4242 4242</b>, any future MM/YY expiry and any 3-digit CVV. Use 4000 0000 0000 0002 to test a declined payment. These values are not sent to a payment provider or stored.</div>
                        <label>Card number<input name="card_number" inputmode="numeric" maxlength="19" placeholder="4242 4242 4242 4242"></label>
                        <div class="row">
                            <label>Expiry<input name="expiry" placeholder="MM/YY" maxlength="5"></label>
                            <label>CVV<input name="cvv" inputmode="numeric" maxlength="4" placeholder="123"></label>
                        </div>
                    </div>
                    <p id="cashNote" class="payment-test"<?= $method === 'card' ? ' style="display:none"' : '' ?>>You pay <b>in cash when you receive</b> your order. No card details needed.</p>
                    <p class="row"><span>Total</span><strong id="checkoutTotal"><?= money($total) ?></strong></p>
                    <button class="primary-btn" type="submit" id="confirmBtn"><?= $method === 'card' ? 'Confirm order and payment ↗' : 'Confirm order ↗' ?></button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php if ($flash): ?>
    <div class="toast show <?= $flash['error'] ? 'bad' : '' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
