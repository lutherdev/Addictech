<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Checkout</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/checkout.css') ?>" />
</head>
<body>

  <?php
  $user        = $user ?? [];
  $cart_items  = $cart_items ?? [];
  $subtotal    = $subtotal ?? 0;
  $is_buy_now  = $is_buy_now ?? false;
  ?>

  <?php if (session()->getFlashdata('error')): ?>
  <div class="checkout-error">
    <?= esc(session()->getFlashdata('error')) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= base_url('orders/place') ?>" id="checkoutForm">
    <?= csrf_field() ?>

    <div class="checkout-body">
      <div class="checkout-left">

        <section class="checkout-section">
          <h2 class="section-heading">1. SHIPPING INFORMATION</h2>
          <hr class="section-rule"/>

          <div class="form-group">
            <label class="form-label" for="fullName">Full Name *</label>
            <input type="text" id="fullName" name="full_name" class="form-input"
                   value="<?= esc(old('full_name', trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')))) ?>"
                   autocomplete="name" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="address">Address *</label>
            <input type="text" id="address" name="delivery_address" class="form-input"
                   value="<?= esc(old('delivery_address', $user['address'] ?? '')) ?>"
                   autocomplete="street-address" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="postal">Postal Code *</label>
              <input type="text" id="postal" name="postal_code" class="form-input"
                     value="<?= esc(old('postal_code', $user['postal_code'] ?? '')) ?>"
                     autocomplete="postal-code" required />
            </div>
            <div class="form-group">
              <label class="form-label" for="city">City *</label>
              <input type="text" id="city" name="city" class="form-input"
                     value="<?= esc(old('city', $user['city'] ?? '')) ?>"
                     autocomplete="address-level2" required />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="orderNote">Order Notes (Optional)</label>
            <textarea id="orderNote" name="notes" class="form-input" rows="2"
                      placeholder="Special instructions for delivery..."><?= esc(old('notes')) ?></textarea>
          </div>
        </section>

        <section class="checkout-section">
          <h2 class="section-heading">2. DELIVERY METHOD</h2>
          <hr class="section-rule"/>

          <label class="shipping-option" id="optStandard">
            <input type="radio" name="delivery_method" value="standard"
                   <?= old('delivery_method', 'standard') === 'standard' ? 'checked' : '' ?>
                   onchange="updateShipping()" />
            <span class="shipping-radio"></span>
            <div class="shipping-info">
              <span class="shipping-name">Standard Delivery</span>
              <span class="shipping-eta">Estimated: 3–5 Business Days</span>
            </div>
            <span class="shipping-price">₱150.00</span>
          </label>

          <label class="shipping-option" id="optExpress">
            <input type="radio" name="delivery_method" value="express"
                   <?= old('delivery_method') === 'express' ? 'checked' : '' ?>
                   onchange="updateShipping()" />
            <span class="shipping-radio"></span>
            <div class="shipping-info">
              <span class="shipping-name">Express Delivery</span>
              <span class="shipping-eta">Estimated: 1–2 Business Days</span>
            </div>
            <span class="shipping-price">₱250.00</span>
          </label>

          <label class="shipping-option" id="optPickup">
            <input type="radio" name="delivery_method" value="pickup"
                   <?= old('delivery_method') === 'pickup' ? 'checked' : '' ?>
                   onchange="updateShipping()" />
            <span class="shipping-radio"></span>
            <div class="shipping-info">
              <span class="shipping-name">Store Pickup</span>
              <span class="shipping-eta">Ready in 1 Business Day</span>
            </div>
            <span class="shipping-price">FREE</span>
          </label>
        </section>

        <section class="checkout-section">
          <h2 class="section-heading">3. PAYMENT METHOD</h2>
          <hr class="section-rule"/>

          <div class="payment-options">
            <button type="button" class="payment-btn <?= old('payment_method', 'cod') === 'cod' ? 'active' : '' ?>"
                    data-method="cod" onclick="selectPayment(this)">COD</button>
            <button type="button" class="payment-btn <?= old('payment_method') === 'gcash' ? 'active' : '' ?>"
                    data-method="gcash" onclick="selectPayment(this)">GCASH</button>
            <button type="button" class="payment-btn <?= old('payment_method') === 'bank_transfer' ? 'active' : '' ?>"
                    data-method="bank_transfer" onclick="selectPayment(this)">ONLINE BANK</button>
            <button type="button" class="payment-btn <?= old('payment_method') === 'credit_card' ? 'active' : '' ?>"
                    data-method="credit_card" onclick="selectPayment(this)">CREDIT CARD</button>
          </div>
          <input type="hidden" name="payment_method" id="paymentMethod"
                 value="<?= esc(old('payment_method', 'cod')) ?>" />
        </section>

      </div>

      <aside class="checkout-summary">
        <h2 class="summary-heading">ORDER SUMMARY</h2>

        <div class="summary-items">
          <?php foreach ($cart_items as $item): ?>
          <div class="summary-line">
            <span><?= esc($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
            <span>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <hr class="summary-rule"/>

        <div class="summary-line">
          <span>Subtotal</span>
          <span>₱<?= number_format($subtotal, 2) ?></span>
        </div>

        <div class="summary-line">
          <span>Shipping</span>
          <span id="summaryShipping">₱150.00</span>
        </div>

        <hr class="summary-rule"/>

        <div class="summary-line summary-total">
          <span>Total</span>
          <span id="summaryTotal">₱<?= number_format($subtotal + 150, 2) ?></span>
        </div>

        <button type="submit" class="complete-btn" id="completeBtn">
          COMPLETE PURCHASE
        </button>
      </aside>

    </div>
  </form>

  <script>
    const SUBTOTAL = <?= $subtotal ?>;

    const SHIPPING_RATES = {
      standard: 150,
      express:  250,
      pickup:   0
    };

    function updateShipping() {
      const method = document.querySelector('input[name="delivery_method"]:checked')?.value ?? 'standard';
      const fee    = SHIPPING_RATES[method] ?? 150;

      document.getElementById('summaryShipping').textContent =
        fee === 0 ? 'FREE' : '₱' + fee.toLocaleString('en-PH', { minimumFractionDigits: 2 });

      document.getElementById('summaryTotal').textContent =
        '₱' + (SUBTOTAL + fee).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    }

    function selectPayment(btn) {
      document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('paymentMethod').value = btn.dataset.method;
    }
  </script>

  <script src="<?= base_url('/public/js/checkout.js') ?>"></script>

</body>
<?= $this->endSection() ?>