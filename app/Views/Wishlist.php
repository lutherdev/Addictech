<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>addictech – Wishlist</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/wishlist.css" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>

  <?php
  // Start session
  session_start();
  
  // Include database configuration
  require_once 'config/database.php';
  
  // Initialize wishlist in session if it doesn't exist
  if (!isset($_SESSION['wishlist'])) {
      $_SESSION['wishlist'] = [];
  }
  
  // Handle AJAX requests
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
      header('Content-Type: application/json');
      
      $response = ['success' => false, 'message' => ''];
      
      switch ($_POST['action']) {
          case 'remove_from_wishlist':
              $product_id = (int)$_POST['product_id'];
              
              if (isset($_SESSION['wishlist'][$product_id])) {
                  unset($_SESSION['wishlist'][$product_id]);
                  $response['success'] = true;
                  $response['message'] = 'Item removed from wishlist';
                  $response['wishlist_count'] = count($_SESSION['wishlist']);
              }
              break;
              
          case 'add_to_cart':
              $product_id = (int)$_POST['product_id'];
              
              // Initialize cart if needed
              if (!isset($_SESSION['cart'])) {
                  $_SESSION['cart'] = [];
              }
              
              // Check if item exists in wishlist
              if (isset($_SESSION['wishlist'][$product_id])) {
                  $item = $_SESSION['wishlist'][$product_id];
                  
                  // Add to cart
                  if (isset($_SESSION['cart'][$product_id])) {
                      $_SESSION['cart'][$product_id]['qty'] = min($_SESSION['cart'][$product_id]['qty'] + 1, 99);
                  } else {
                      $_SESSION['cart'][$product_id] = [
                          'id' => $item['id'],
                          'name' => $item['name'],
                          'category' => $item['category'],
                          'price' => $item['price'],
                          'variant' => $item['variant'],
                          'qty' => 1
                      ];
                  }
                  
                  $response['success'] = true;
                  $response['message'] = $item['name'] . ' added to cart!';
                  $response['cart_count'] = array_sum(array_column($_SESSION['cart'], 'qty'));
              }
              break;
              
          case 'get_counts':
              $response['success'] = true;
              $response['wishlist_count'] = count($_SESSION['wishlist']);
              $response['cart_count'] = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;
              break;
      }
      
      echo json_encode($response);
      exit();
  }
  
  // Get counts for badges
  $wishlist_count = count($_SESSION['wishlist']);
  $cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;
  
  // Get wishlist items
  $wishlist_items = $_SESSION['wishlist'];
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
      <a href="wishlist.php" class="icon-btn wish-icon-wrap" aria-label="Wishlist">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <?php if ($wishlist_count > 0): ?>
        <span class="wish-badge" id="wishBadge"><?php echo $wishlist_count; ?></span>
        <?php else: ?>
        <span class="wish-badge" id="wishBadge" style="display:none">0</span>
        <?php endif; ?>
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

  <div class="wishlist-header">
    <h1 class="wishlist-title">MY WISHLIST</h1>
    <div class="wishlist-search-wrap">
      <input type="text" class="wishlist-search" id="wishSearch" placeholder="SEARCH" onkeyup="renderWishlist()"/>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>
  </div>

  <main class="wishlist-main">
    <div class="wishlist-grid" id="wishlistGrid">
      <?php if (empty($wishlist_items)): ?>
      <div class="wishlist-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b8b4aa" stroke-width="1.2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <p>Your wishlist is empty.</p>
        <a href="catalog.php" class="wishlist-empty-link">Browse Catalog →</a>
      </div>
      <?php else: ?>
        <?php foreach ($wishlist_items as $id => $item): ?>
        <div class="wish-card" data-id="<?php echo $id; ?>">
          <div class="wish-img">
            <!-- heart corner tag -->
            <div class="wish-corner-tag">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="1.5">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </div>
            <svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">
              <rect x="1" y="1" width="298" height="338" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="1" y1="1" x2="299" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="299" y1="1" x2="1" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
            </svg>
            <!-- hover overlay buttons -->
            <div class="wish-hover-actions">
              <button class="wish-atc-btn" data-id="<?php echo $id; ?>">ADD TO CART</button>
              <button class="wish-remove-btn" data-id="<?php echo $id; ?>" aria-label="Remove">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="wish-meta">
            <span class="wish-name"><?php echo htmlspecialchars(strtoupper($item['name'])); ?></span>
            <span class="wish-price">₱<?php echo number_format($item['price']); ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <div class="wish-toast" id="wishToast"></div>

  <style>
    .wishlist-empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 4rem 2rem;
      font-family: var(--font-body);
    }
    
    .wishlist-empty svg {
      margin-bottom: 1rem;
    }
    
    .wishlist-empty p {
      color: var(--text-muted);
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
    }
    
    .wishlist-empty-link {
      display: inline-block;
      padding: 0.8rem 2rem;
      border: 1.5px solid var(--text);
      text-decoration: none;
      color: var(--text);
      font-weight: 500;
      letter-spacing: 0.1em;
      transition: all 0.2s;
    }
    
    .wishlist-empty-link:hover {
      background: var(--text);
      color: var(--bg);
    }
    
    .wish-toast {
      position: fixed;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%) translateY(100%);
      background: var(--text);
      color: var(--bg);
      padding: 0.8rem 2rem;
      font-family: var(--font-body);
      font-size: 0.85rem;
      letter-spacing: 0.05em;
      transition: transform 0.3s;
      z-index: 1000;
      white-space: nowrap;
    }
    
    .wish-toast.show {
      transform: translateX(-50%) translateY(0);
    }
  </style>

  <script>
    // Pass PHP data to JavaScript
    const wishlistItems = <?php echo json_encode($wishlist_items); ?>;
    let currentWishlist = { ...wishlistItems };

    function updateBadges() {
      fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_counts'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const wb = document.getElementById('wishBadge');
          const cb = document.getElementById('cartBadge');
          
          if (data.wishlist_count > 0) {
            wb.textContent = data.wishlist_count;
            wb.style.display = 'flex';
          } else {
            wb.style.display = 'none';
          }
          
          if (data.cart_count > 0) {
            cb.textContent = data.cart_count;
            cb.style.display = 'flex';
          } else {
            cb.style.display = 'none';
          }
        }
      });
    }

    function removeFromWishlist(id) {
      fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove_from_wishlist&product_id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove card from DOM
          const card = document.querySelector(`.wish-card[data-id="${id}"]`);
          if (card) {
            card.remove();
          }
          
          // Update badges
          updateBadges();
          
          // Show empty state if no items left
          const grid = document.getElementById('wishlistGrid');
          if (grid.children.length === 0) {
            grid.innerHTML = `
              <div class="wishlist-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b8b4aa" stroke-width="1.2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <p>Your wishlist is empty.</p>
                <a href="catalog.php" class="wishlist-empty-link">Browse Catalog →</a>
              </div>
            `;
          }
          
          showToast(data.message);
        }
      });
    }

    function addToCart(id) {
      fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_to_cart&product_id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateBadges();
          showToast(data.message);
        }
      });
    }

    function showToast(message) {
      const t = document.getElementById('wishToast');
      t.textContent = message;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2200);
    }

    function renderWishlist() {
      const searchTerm = (document.getElementById('wishSearch').value || '').toLowerCase();
      const cards = document.querySelectorAll('.wish-card');
      
      if (cards.length === 0) return;
      
      cards.forEach(card => {
        const name = card.querySelector('.wish-name').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
      
      // Check if all cards are hidden
      const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
      const grid = document.getElementById('wishlistGrid');
      const emptyMsg = grid.querySelector('.wishlist-empty-search');
      
      if (visibleCards.length === 0) {
        if (!emptyMsg) {
          const msg = document.createElement('div');
          msg.className = 'wishlist-empty wishlist-empty-search';
          msg.innerHTML = '<p>No items match your search.</p>';
          grid.appendChild(msg);
        }
      } else if (emptyMsg) {
        emptyMsg.remove();
      }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Add to cart buttons
      document.querySelectorAll('.wish-atc-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          addToCart(id);
        });
      });

      // Remove buttons
      document.querySelectorAll('.wish-remove-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          removeFromWishlist(id);
        });
      });

      // Search input
      const searchInput = document.getElementById('wishSearch');
      if (searchInput) {
        searchInput.addEventListener('input', renderWishlist);
      }
    });
  </script>

</body>
</html>