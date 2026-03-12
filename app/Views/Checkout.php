<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>addictech – Checkout</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/checkout.css" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>

  <?php
  // Start session
  session_start();
  
  // Include database configuration
  require_once 'config/database.php';
  
  // Check if cart is empty
  if (empty($_SESSION['cart'])) {
      header('Location: cart.php');
      exit();
  }
  
  // Get cart items
  $cart_items = $_SESSION['cart'];
  $cart_count = array_sum(array_column($cart_items, 'qty'));
  $cart_subtotal = array_reduce($cart_items, function($sum, $item) {
      return $sum + ($item['price'] * $item['qty']);
  }, 0);
  
  // Get current user if logged in
  $user = null;
  $is_logged_in = false;
  if (isset($_SESSION['user_id'])) {
      $is_logged_in = true;
      $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $result = $stmt->get_result();
      $user = $result->fetch_assoc();
  }
  
  // Handle order submission
  $order_placed = false;
  $order_error = '';
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
      
      // Validate required fields
      $full_name = trim($_POST['full_name'] ?? '');
      $address = trim($_POST['address'] ?? '');
      $postal = trim($_POST['postal'] ?? '');
      $city = trim($_POST['city'] ?? '');
      $shipping_method = $_POST['shipping_method'] ?? 'standard';
      $payment_method = $_POST['payment_method'] ?? 'ewallet';
      
      $errors = [];
      if (empty($full_name)) $errors[] = 'full_name';
      if (empty($address)) $errors[] = 'address';
      if (empty($postal)) $errors[] = 'postal';
      if (empty($city)) $errors[] = 'city';
      
      if (empty($errors)) {
          // Calculate shipping cost
          $shipping_cost = ($shipping_method === 'express') ? 100 : 50;
          $total_amount = $cart_subtotal + $shipping_cost;
          
          // Start transaction
          $conn->begin_transaction();
          
          try {
              // Insert main order
              $user_id = $is_logged_in ? $_SESSION['user_id'] : null;
              $order_note = trim($_POST['order_note'] ?? '');
              
              $stmt = $conn->prepare("
                  INSERT INTO orders (
                      user_id, full_name, address, postal_code, city, 
                      shipping_method, payment_method, subtotal, shipping_cost, 
                      total, order_note, status, created_at
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
              ");
              
              $stmt->bind_param(
                  "issssssddds",
                  $user_id, $full_name, $address, $postal, $city,
                  $shipping_method, $payment_method, $cart_subtotal, 
                  $shipping_cost, $total_amount, $order_note
              );
              
              $stmt->execute();
              $order_id = $conn->insert_id;
              
              // Insert order items
              $item_stmt = $conn->prepare("
                  INSERT INTO order_items (
                      order_id, product_id, product_name, 
                      quantity, price, subtotal
                  ) VALUES (?, ?, ?, ?, ?, ?)
              ");
              
              foreach ($cart_items as $item) {
                  $item_subtotal = $item['price'] * $item['qty'];
                  $item_stmt->bind_param(
                      "iisidd",
                      $order_id, $item['id'], $item['name'],
                      $item['qty'], $item['price'], $item_subtotal
                  );
                  $item_stmt->execute();
              }
              
              // If user is logged in, update their profile with new address
              if ($is_logged_in) {
                  $update_stmt = $conn->prepare("
                      UPDATE users 
                      SET address = ?, city = ?, postal_code = ? 
                      WHERE id = ?
                  ");
                  $update_stmt->bind_param("sssi", $address, $city, $postal, $user_id);
                  $update_stmt->execute();
              }
              
              // Commit transaction
              $conn->commit();
              
              // Clear cart
              $_SESSION['cart'] = [];
              
              // Store order ID for confirmation page
              $_SESSION['last_order_id'] = $order_id;
              $order_placed = true;
              
          } catch (Exception $e) {
              $conn->rollback();
              $order_error = 'An error occurred while processing your order. Please try again.';
          }
      } else {
          $order_error = 'Please fill in all required fields.';
      }
  }
  
  // Redirect to confirmation if order placed
  if ($order_placed) {
      header('Location: order-confirmation.php?id=' . $_SESSION['last_order_id']);
      exit();
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
      <a href="wishlist.php" class="icon-btn" aria-label="Wishlist">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </a>
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

  <?php if (!empty($order_error)): ?>
  <div class="checkout-error">
    <?php echo htmlspecialchars($order_error); ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="" id="checkoutForm">
    <div class="checkout-body">
      <div class="checkout-left">

        <section class="checkout-section">
          <h2 class="section-heading">1. SHIPPING INFORMATION</h2>
          <hr class="section-rule"/>

          <div class="form-group">
            <label class="form-label" for="fullName">Full Name *</label>
            <input type="text" id="fullName" name="full_name" class="form-input" 
                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ($user ? trim($user['first_name'] . ' ' . $user['last_name']) : '')); ?>" 
                   autocomplete="name" required/>
          </div>

          <div class="form-group">
            <label class="form-label" for="address">Address *</label>
            <input type="text" id="address" name="address" class="form-input" 
                   value="<?php echo htmlspecialchars($_POST['address'] ?? ($user['address'] ?? '')); ?>" 
                   autocomplete="street-address" required/>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="postal">Postal Code *</label>
              <input type="text" id="postal" name="postal" class="form-input" 
                     value="<?php echo htmlspecialchars($_POST['postal'] ?? ($user['postal_code'] ?? '')); ?>" 
                     autocomplete="postal-code" required/>
            </div>
            <div class="form-group">
              <label class="form-label" for="city">City *</label>
              <input type="text" id="city" name="city" class="form-input" 
                     value="<?php echo htmlspecialchars($_POST['city'] ?? ($user['city'] ?? '')); ?>" 
                     autocomplete="address-level2" required/>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="orderNote">Order Notes (Optional)</label>
            <textarea id="orderNote" name="order_note" class="form-input" rows="2" 
                      placeholder="Special instructions for delivery..."><?php echo htmlspecialchars($_POST['order_note'] ?? ''); ?></textarea>
          </div>
        </section>

        <section class="checkout-section">
          <h2 class="section-heading">2. SHIPPING METHOD</h2>
          <hr class="section-rule"/>

          <label class="shipping-option" id="optStandard">
            <input type="radio" name="shipping_method" value="standard" 
                   <?php echo (!isset($_POST['shipping_method']) || $_POST['shipping_method'] === 'standard') ? 'checked' : ''; ?> 
                   onchange="updateShipping()"/>
            <span class="shipping-radio"></span>
            <div class="shipping-info">
              <span class="shipping-name">Standard Delivery</span>
              <span class="shipping-eta">Estimated: 3-5 Business Days</span>
            </div>
            <span class="shipping-price">₱50.00</span>
          </label>

          <label class="shipping-option" id="optExpress">
            <input type="radio" name="shipping_method" value="express" 
                   <?php echo ($_POST['shipping_method'] ?? '') === 'express' ? 'checked' : ''; ?> 
                   onchange="updateShipping()"/>
            <span class="shipping-radio"></span>
            <div class="shipping-info">
              <span class="shipping-name">Express Delivery</span>
              <span class="shipping-eta">Estimated: 1-2 Business Days</span>
            </div>
            <span class="shipping-price">₱100.00</span>
          </label>
        </section>

        <section class="checkout-section">
          <h2 class="section-heading">3. PAYMENT METHOD</h2>
          <hr class="section-rule"/>

          <div class="payment-options">
            <button type="button" class="payment-btn <?php echo (!isset($_POST['payment_method']) || $_POST['payment_method'] === 'ewallet') ? 'active' : ''; ?>" 
                    data-method="ewallet" onclick="selectPayment(this)">E-WALLET</button>
            <button type="button" class="payment-btn <?php echo ($_POST['payment_method'] ?? '') === 'bank' ? 'active' : ''; ?>" 
                    data-method="bank" onclick="selectPayment(this)">ONLINE BANK</button>
            <button type="button" class="payment-btn <?php echo ($_POST['payment_method'] ?? '') === 'cod' ? 'active' : ''; ?>" 
                    data-method="cod" onclick="selectPayment(this)">COD</button>
          </div>
          <input type="hidden" name="payment_method" id="paymentMethod" 
                 value="<?php echo htmlspecialchars($_POST['payment_method'] ?? 'ewallet'); ?>"/>
        </section>

        <?php if (!$is_logged_in): ?>
        <div class="checkout-login-prompt">
          <p>Already have an account? <a href="login.php?redirect=checkout.php">Login</a> for faster checkout.</p>
        </div>
        <?php endif; ?>

      </div>

      <aside class="checkout-summary">
        <h2 class="summary-heading">ORDER SUMMARY</h2>

        <div class="summary-items" id="summaryItems">
          <?php foreach ($cart_items as $item): ?>
          <div class="summary-line">
            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['qty']; ?>)</span>
            <span>₱<?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <hr class="summary-rule"/>

        <div class="summary-line">
          <span>Subtotal</span>
          <span>₱<?php echo number_format($cart_subtotal, 2); ?></span>
        </div>

        <div class="summary-line">
          <span>Shipping</span>
          <span id="summaryShipping">₱50.00</span>
        </div>

        <hr class="summary-rule"/>

        <div class="summary-line summary-total">
          <span>Total</span>
          <span id="summaryTotal">₱<?php echo number_format($cart_subtotal + 50, 2); ?></span>
        </div>

        <button type="submit" name="place_order" class="complete-btn" id="completeBtn">
          COMPLETE PURCHASE
        </button>
      </aside>
    </div>
  </form>

  <!-- Order Confirmation Modal (optional, but we'll use separate page instead) -->
  <style>
    .checkout-error {
      background: #fdecea;
      color: #c0392b;
      padding: 1rem;
      text-align: center;
      font-family: var(--font-body);
      border-bottom: 1px solid #e74c3c;
      margin-bottom: 2rem;
    }
    
    .checkout-login-prompt {
      margin-top: 2rem;
      padding: 1rem;
      background: #f8f6f2;
      font-family: var(--font-body);
      text-align: center;
    }
    
    .checkout-login-prompt a {
      color: var(--text);
      text-decoration: underline;
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      display: block;
      font-family: var(--font-body);
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 0.3rem;
    }
    
    .form-input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border);
      padding: 0.5rem 0;
      font-family: var(--font-body);
      font-size: 0.9rem;
      color: var(--text);
      outline: none;
    }
    
    .form-input:focus {
      border-bottom-color: var(--text);
    }
    
    textarea.form-input {
      resize: vertical;
      min-height: 60px;
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    
    .section-heading {
      font-family: var(--font-heading);
      font-weight: 400;
      letter-spacing: 0.1em;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }
    
    .section-rule {
      border: none;
      border-top: 1px solid var(--border);
      margin: 0.5rem 0 2rem;
    }
    
    .shipping-option {
      display: flex;
      align-items: center;
      padding: 1rem;
      border: 1px solid var(--border);
      margin-bottom: 0.5rem;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .shipping-option.selected {
      border-color: var(--text);
      background: #f8f6f2;
    }
    
    .shipping-option input[type="radio"] {
      width: 18px;
      height: 18px;
      margin-right: 1rem;
    }
    
    .shipping-info {
      flex: 1;
    }
    
    .shipping-name {
      display: block;
      font-family: var(--font-body);
      font-weight: 500;
      font-size: 0.9rem;
    }
    
    .shipping-eta {
      font-family: var(--font-body);
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    
    .shipping-price {
      font-family: var(--font-body);
      font-weight: 500;
    }
    
    .payment-options {
      display: flex;
      gap: 1rem;
    }
    
    .payment-btn {
      flex: 1;
      padding: 0.8rem;
      background: transparent;
      border: 1px solid var(--border);
      font-family: var(--font-body);
      font-weight: 500;
      letter-spacing: 0.05em;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .payment-btn.active {
      background: var(--text);
      color: var(--bg);
      border-color: var(--text);
    }
    
    .checkout-summary {
      background: #f8f6f2;
      padding: 2rem;
      height: fit-content;
      position: sticky;
      top: 2rem;
    }
    
    .summary-heading {
      font-family: var(--font-heading);
      font-weight: 400;
      letter-spacing: 0.1em;
      font-size: 1.2rem;
      margin-bottom: 1.5rem;
    }
    
    .summary-line {
      display: flex;
      justify-content: space-between;
      font-family: var(--font-body);
      font-size: 0.9rem;
      margin-bottom: 0.8rem;
    }
    
    .summary-total {
      font-family: var(--font-heading);
      font-weight: 600;
      font-size: 1.2rem;
      margin-top: 1rem;
    }
    
    .summary-rule {
      border: none;
      border-top: 1px solid var(--border);
      margin: 1rem 0;
    }
    
    .complete-btn {
      width: 100%;
      background: var(--text);
      color: var(--bg);
      border: none;
      padding: 1rem;
      font-family: var(--font-body);
      font-weight: 500;
      letter-spacing: 0.15em;
      font-size: 0.9rem;
      cursor: pointer;
      transition: opacity 0.2s;
      margin-top: 2rem;
    }
    
    .complete-btn:hover {
      opacity: 0.8;
    }
    
    @media (max-width: 768px) {
      .checkout-body {
        grid-template-columns: 1fr;
        padding: 1rem;
      }
      
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .payment-options {
        flex-direction: column;
      }
    }
  </style>

  <script>
    // Pass PHP data to JavaScript
    const cartSubtotal = <?php echo $cart_subtotal; ?>;
    let shippingCost = 50;

    function updateShipping() {
      const selected = document.querySelector('input[name="shipping_method"]:checked');
      shippingCost = selected ? (selected.value === 'express' ? 100 : 50) : 50;
      
      document.getElementById('summaryShipping').textContent = '₱' + shippingCost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      
      const total = cartSubtotal + shippingCost;
      document.getElementById('summaryTotal').textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      
      // Update selected class
      document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
      if (selected) selected.closest('.shipping-option').classList.add('selected');
    }

    function selectPayment(btn) {
      document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Update hidden input
      const method = btn.dataset.method;
      document.getElementById('paymentMethod').value = method;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      // Set initial shipping selected class
      const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
      if (selectedShipping) {
        selectedShipping.closest('.shipping-option').classList.add('selected');
      }
      
      // Form validation
      document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const required = ['fullName', 'address', 'postal', 'city'];
        let hasError = false;
        
        required.forEach(id => {
          const input = document.getElementById(id);
          if (!input.value.trim()) {
            input.classList.add('shake');
            setTimeout(() => input.classList.remove('shake'), 500);
            hasError = true;
          }
        });
        
        if (hasError) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });
    });
  </script>

</body>
</html>