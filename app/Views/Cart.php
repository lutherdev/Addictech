<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>addictech – Cart</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/cart.css" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>

  <?php
  // Start session
  session_start();
  
  // Include database configuration
  require_once 'config/database.php';
  
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

  <nav class="navbar">
    <div class="nav-left">
      <a href="index.php" class="brand">addictech</a>
    </div>
    <div class="nav-links">
      <a href="index.php" class="nav-link">HOME</a>
      <a href="about.php" class="nav-link">ABOUT</a>
      <a href="catalog.php" class="nav-link">CATALOG</a>
      <a href="contact.php" class="nav-link">CONTACT</a>
    </div>
    <div class="nav-icons">
      <button class="icon-btn" aria-label="Wishlist">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      <a href="cart.php" class="icon-btn cart-icon-wrap" aria-label="Cart">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <?php if ($cart_count > 0): ?>
        <span class="cart-badge" id="cartBadge"><?php echo $cart_count; ?></span>
        <?php else: ?>
        <span class="cart-badge" id="cartBadge" style="display:none">0</span>
        <?php endif; ?>
      </a>
      <a href="account.php" class="icon-btn" aria-label="Account">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </a>
    </div>
  </nav>

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

  <style>
    .summary-checkout-btn:disabled {
      background: none;
      border: 1.5px solid var(--text-muted);
      color: var(--text-muted);
    }
  </style>

  <script>
    /* ── CART FUNCTIONS WITH AJAX ── */
    
    function updateCartDisplay() {
      // Fetch latest cart data from server
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_cart'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateBadge(data.cart_count);
          updateTotal(data.cart_total);
          
          // If cart is empty, reload to show empty state
          if (data.cart_count === 0) {
            location.reload();
          }
        }
      });
    }

    function updateBadge(count) {
      const badge = document.getElementById('cartBadge');
      if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
    }

    function updateTotal(total) {
      document.getElementById('cartTotal').textContent = '₱' + total.toLocaleString();
    }

    function updateQuantity(id, newQty) {
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_qty&id=${id}&qty=${newQty}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById(`qty-${id}`).textContent = newQty;
          updateBadge(data.cart_count);
          updateTotal(data.cart_total);
        }
      });
    }

    function removeItem(id) {
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove_item&id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove item from DOM
          const item = document.querySelector(`.cart-item[data-id="${id}"]`);
          if (item) {
            item.remove();
            
            // Check if cart is empty
            if (data.cart_count === 0) {
              location.reload();
            } else {
              updateBadge(data.cart_count);
              updateTotal(data.cart_total);
            }
          }
        }
      });
    }

    /* ── EVENT LISTENERS ── */
    document.addEventListener('DOMContentLoaded', function() {
      // Quantity plus buttons
      document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const currentQty = parseInt(document.getElementById(`qty-${id}`).textContent);
          const newQty = Math.min(currentQty + 1, 99);
          updateQuantity(id, newQty);
        });
      });

      // Quantity minus buttons
      document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const currentQty = parseInt(document.getElementById(`qty-${id}`).textContent);
          const newQty = Math.max(currentQty - 1, 1);
          updateQuantity(id, newQty);
        });
      });

      // Remove buttons
      document.querySelectorAll('.item-remove').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          removeItem(id);
        });
      });

      // Save order note to localStorage (optional)
      const orderNote = document.getElementById('orderNote');
      if (orderNote) {
        // Load saved note
        const savedNote = localStorage.getItem('order_note');
        if (savedNote) {
          orderNote.value = savedNote;
        }
        
        // Save note on change
        orderNote.addEventListener('input', () => {
          localStorage.setItem('order_note', orderNote.value);
        });
      }
    });
  </script>

</body>
</html>