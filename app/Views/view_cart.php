<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Cart</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/cart.css') ?>" />
</head>

<?php
$cart_items = $cart_items ?? [];
$total      = $total ?? 0;
?>

<?php if (session()->getFlashdata('success')) : ?>
  <div class="cart-alert cart-alert-success">
    <?= session()->getFlashdata('success') ?>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
  <div class="cart-alert cart-alert-error">
    <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>

<div class="cart-title-bar">
  <h1 class="cart-title">CART</h1>
</div>

<div class="cart-body">
  <div class="cart-items" id="cartItems">

    <?php if (empty($cart_items)) : ?>
      <div class="cart-empty">
        <p>Your cart is empty.</p>
        <a href="<?= base_url('catalog') ?>" class="empty-link">Continue Shopping →</a>
      </div>
    <?php else : ?>
      <?php foreach ($cart_items as $item) : ?>
        <div class="cart-item" data-id="<?= $item['id'] ?>">
          <div class="item-img">
            <svg width="100%" height="100%" viewBox="0 0 200 200" preserveAspectRatio="none" fill="none">
              <rect x="1" y="1" width="198" height="198" stroke="#b8b4aa" stroke-width="1.5"/>
              <circle cx="145" cy="65" r="22" stroke="#b8b4aa" stroke-width="1.5"/>
              <polyline points="10,170 70,90 115,140 150,100 198,170" stroke="#b8b4aa" stroke-width="1.5" fill="none"/>
            </svg>
          </div>
          <div class="item-details">
            <div class="item-top-row">
              <p class="item-name"><?= esc($item['name']) ?></p>
              <a href="<?= base_url('cart/remove/' . $item['id']) ?>" class="item-remove" aria-label="Remove">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </a>
            </div>
            <p class="item-price">₱<?= number_format($item['price']) ?></p>
            <div class="item-tags">
              <span class="item-tag"><?= esc($item['category']) ?></span>
              <?php if (!empty($item['variant'])) : ?>
                <span class="item-tag"><?= esc($item['variant']) ?></span>
              <?php endif; ?>
            </div>
            <div class="item-qty-row">
              <span class="item-qty-label">Quantity:</span>
              <div class="item-qty-ctrl">
                <form method="POST" action="<?= base_url('cart/update/' . $item['id']) ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>"
                          class="item-qty-btn qty-minus" <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                  </button>
                </form>
                <span class="item-qty-val"><?= $item['quantity'] ?></span>
                <form method="POST" action="<?= base_url('cart/update/' . $item['id']) ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>"
                          class="item-qty-btn qty-plus">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <circle cx="12" cy="12" r="10"/>
                      <line x1="12" y1="8" x2="12" y2="16"/>
                      <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <hr class="item-divider"/>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <aside class="cart-summary">
    <div class="summary-note-section">
      <label class="summary-label" for="orderNote">Add Note to your Order</label>
      <textarea class="summary-note" id="orderNote" placeholder="Any special instructions…"></textarea>
    </div>

    <div class="summary-total-row">
      <span class="summary-total-label">Total:</span>
      <span class="summary-total-val">₱<?= number_format($total) ?></span>
    </div>
    <hr class="summary-divider"/>

    <p class="summary-info">
      Shipping and taxes are calculated at checkout.
    </p>

    <?php if (!empty($cart_items)) : ?>
      <a href="<?= base_url('checkout') ?>" class="summary-checkout-btn">
        PROCEED TO CHECKOUT
      </a>
    <?php else : ?>
      <button class="summary-checkout-btn" disabled style="opacity:0.5;cursor:not-allowed;">
        PROCEED TO CHECKOUT
      </button>
    <?php endif; ?>
  </aside>
</div>

<?= $this->endSection() ?>