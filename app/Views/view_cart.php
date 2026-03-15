<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Cart</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/cart.css') ?>" />
</head>
<body>

  <?php
  // Start session
  //session_start();
  
  // Include database configuration
  //require_once 'config/database.php';
  
  // Initialize cart in session if it doesn't exist
  if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
  }
  
  // Handle AJAX requests for cart updates
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
      header('Content-Type: application/json');
      
      $response = ['success' => false, 'message' => '', 'cart' => $_SESSION['cart']];
      
      switch ($_POST['action']) {
          case 'update_qty':
              $id = (int)$_POST['id'];
              $qty = (int)$_POST['qty'];
              
              if (isset($_SESSION['cart'][$id])) {
                  $_SESSION['cart'][$id]['qty'] = max(1, min(99, $qty));
                  $response['success'] = true;
                  $response['message'] = 'Quantity updated';
              }
              break;
              
          case 'remove_item':
              $id = (int)$_POST['id'];
              
              if (isset($_SESSION['cart'][$id])) {
                  unset($_SESSION['cart'][$id]);
                  $response['success'] = true;
                  $response['message'] = 'Item removed';
              }
              break;
              
          case 'get_cart':
              $response['success'] = true;
              $response['cart'] = $_SESSION['cart'];
              break;
      }
      
      // Calculate totals for response
      $response['cart_count'] = array_sum(array_column($_SESSION['cart'], 'qty'));
      $response['cart_total'] = array_reduce($_SESSION['cart'], function($sum, $item) {
          return $sum + ($item['price'] * $item['qty']);
      }, 0);
      
      echo json_encode($response);
      exit();
  }
  
  // Get cart data for display
  $cart_items = $_SESSION['cart'];
  $cart_count = array_sum(array_column($cart_items, 'qty'));
  $cart_total = array_reduce($cart_items, function($sum, $item) {
      return $sum + ($item['price'] * $item['qty']);
  }, 0);
  
  // Sample product data (in a real app, this would come from database)
  $products = [
      1 => ['name' => 'VINTAGE DENIM JACKET', 'category' => 'APPAREL', 'variant' => 'LARGE'],
      2 => ['name' => 'LEATHER CROSSBODY BAG', 'category' => 'ACCESSORIES', 'variant' => 'BROWN'],
      3 => ['name' => 'VINYL RECORD PLAYER', 'category' => 'ELECTRONICS', 'variant' => 'RETRO'],
      4 => ['name' => 'HANDMADE CERAMIC VASE', 'category' => 'HOME', 'variant' => 'SMALL'],
      5 => ['name' => 'WOODEN SUNGLASSES', 'category' => 'ACCESSORIES', 'variant' => 'DARK'],
  ];
  
  // Merge cart items with product data
  $cart_display = [];
  foreach ($cart_items as $id => $item) {
      if (isset($products[$id])) {
          $cart_display[$id] = array_merge($products[$id], $item);
      }
  }
  ?>

  <div class="cart-title-bar">
    <h1 class="cart-title">CART</h1>
  </div>

  <div class="cart-body">
    <div class="cart-items" id="cartItems">
      <?php if (empty($cart_display)): ?>
      <div class="cart-empty">
        <p>Your cart is empty.</p>
        <a href="catalog.php" class="empty-link">Continue Shopping →</a>
      </div>
      <?php else: ?>
        <?php foreach ($cart_display as $id => $item): ?>
        <div class="cart-item" data-id="<?php echo $id; ?>">
          <div class="item-img">
            <svg width="100%" height="100%" viewBox="0 0 200 200" preserveAspectRatio="none" fill="none">
              <rect x="1" y="1" width="198" height="198" stroke="#b8b4aa" stroke-width="1.5"/>
              <circle cx="145" cy="65" r="22" stroke="#b8b4aa" stroke-width="1.5"/>
              <polyline points="10,170 70,90 115,140 150,100 198,170" stroke="#b8b4aa" stroke-width="1.5" fill="none"/>
            </svg>
          </div>
          <div class="item-details">
            <div class="item-top-row">
              <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
              <button class="item-remove" data-id="<?php echo $id; ?>" aria-label="Remove">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
            <p class="item-price">₱<?php echo number_format($item['price']); ?></p>
            <div class="item-tags">
              <span class="item-tag"><?php echo htmlspecialchars($item['category']); ?></span>
              <span class="item-tag"><?php echo htmlspecialchars($item['variant']); ?></span>
            </div>
            <div class="item-qty-row">
              <span class="item-qty-label">Quantity:</span>
              <div class="item-qty-ctrl">
                <button class="item-qty-btn qty-minus" data-id="<?php echo $id; ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                </button>
                <span class="item-qty-val" id="qty-<?php echo $id; ?>"><?php echo $item['qty']; ?></span>
                <button class="item-qty-btn qty-plus" data-id="<?php echo $id; ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        <?php if (!next($cart_display)): ?>
        <hr class="item-divider"/>
        <?php endif; ?>
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
        <span class="summary-total-val" id="cartTotal">₱<?php echo number_format($cart_total); ?></span>
      </div>
      <hr class="summary-divider"/>

      <p class="summary-info">
        Shipping and taxes are calculated at checkout. Free delivery on orders above ₱5,000 within Metro Manila.
      </p>

      <?php if (!empty($cart_display)): ?>
      <a href="checkout.php" class="summary-checkout-btn" style="text-align:center;text-decoration:none;display:block;">PROCEED TO CHECKOUT</a>
      <?php else: ?>
      <button class="summary-checkout-btn" disabled style="text-align:center;text-decoration:none;display:block;opacity:0.5;cursor:not-allowed;">PROCEED TO CHECKOUT</button>
      <?php endif; ?>
    </aside>
  </div>
    <script src="<?= base_url('/public/js/login.js') ?>"></script>
</body>
</html>
<?= $this->endSection() ?>