<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Catalog</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/catalog.css') ?>" />
</head>
<body>

  <?php
  // Start session
  session_start();
  
  // Include database configuration
  require_once 'config/database.php';
  
  // Initialize cart and wishlist in session if they don't exist
  if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
  }
  if (!isset($_SESSION['wishlist'])) {
      $_SESSION['wishlist'] = [];
  }
  
  // Get products from database
  $products = [];
  $result = $conn->query("SELECT * FROM products ORDER BY id");
  while ($row = $result->fetch_assoc()) {
      $products[] = $row;
  }
  
  // If no products in database, use sample data
  if (empty($products)) {
      $products = [
        ['id'=>1, 'name'=>'MK Pro X', 'category'=>'keyboard', 'price'=>5890, 'stock'=>1, 'variant'=>'Cherry MX Red', 'desc'=>'Mechanical full-size keyboard with per-key RGB lighting, tactile switches, and a durable aluminum top frame built for long gaming sessions.'],
        ['id'=>2, 'name'=>'MK Slim 60%', 'category'=>'keyboard', 'price'=>3490, 'stock'=>1, 'variant'=>'Low Profile', 'desc'=>'Ultra-compact 60% layout with low-profile switches. Perfect for minimalist desk setups and on-the-go use.'],
        ['id'=>3, 'name'=>'Viper V2', 'category'=>'mouse', 'price'=>2850, 'stock'=>1, 'variant'=>'Standard', 'desc'=>'Lightweight ambidextrous gaming mouse with a precision optical sensor and up to 20,000 DPI resolution.'],
        ['id'=>4, 'name'=>'Basilisk X', 'category'=>'mouse', 'price'=>4200, 'stock'=>0, 'variant'=>'Ergonomic', 'desc'=>'Ergonomic right-handed mouse with customizable scroll resistance and 6 programmable buttons for power users.'],
        ['id'=>5, 'name'=>'Void RGB', 'category'=>'headset', 'price'=>5990, 'stock'=>1, 'variant'=>'Wireless', 'desc'=>'Surround sound USB headset with custom-tuned 50mm drivers and long-range wireless for unrestricted play.'],
        ['id'=>6, 'name'=>'Cloud II', 'category'=>'headset', 'price'=>4490, 'stock'=>1, 'variant'=>'Wired', 'desc'=>'Award-winning gaming headset with memory foam ear cushions and detachable noise-cancelling microphone.'],
        ['id'=>7, 'name'=>'UltraSharp 27', 'category'=>'monitor', 'price'=>21900, 'stock'=>1, 'variant'=>'4K UHD', 'desc'=>'27-inch 4K IPS display with factory-calibrated colors, USB-C connectivity, and ultra-slim bezels for immersive work.'],
        ['id'=>8, 'name'=>'Odyssey G5', 'category'=>'monitor', 'price'=>14500, 'stock'=>0, 'variant'=>'1440p 165Hz', 'desc'=>'27-inch 1440p curved gaming monitor with a 165Hz refresh rate and 1ms response time for competitive play.'],
        ['id'=>9, 'name'=>'Audioengine A2', 'category'=>'speaker', 'price'=>16800, 'stock'=>1, 'variant'=>'Powered', 'desc'=>'Compact powered desktop speakers with a built-in amplifier delivering audiophile-grade stereo sound from a small footprint.'],
        ['id'=>10, 'name'=>'Logitech Z200', 'category'=>'speaker', 'price'=>2390, 'stock'=>1, 'variant'=>'Stereo', 'desc'=>'Affordable stereo speakers with clear, room-filling sound and easy-access volume control on the front panel.'],
        ['id'=>11, 'name'=>'C920 HD Pro', 'category'=>'webcam', 'price'=>4350, 'stock'=>1, 'variant'=>'1080p', 'desc'=>'Full HD 1080p webcam with dual built-in stereo mics and automatic low-light correction for crisp video calls.'],
        ['id'=>12, 'name'=>'StreamCam', 'category'=>'webcam', 'price'=>9290, 'stock'=>0, 'variant'=>'USB-C 60fps', 'desc'=>'Premium USB-C streaming camera with smooth 60fps 1080p video and intelligent auto-focus that tracks your face.'],
      ];
  }
  
  // Handle AJAX requests
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
      header('Content-Type: application/json');
      
      $response = ['success' => false, 'message' => ''];
      
      switch ($_POST['action']) {
          case 'toggle_wishlist':
              $product_id = (int)$_POST['product_id'];
              $product = null;
              
              // Find product
              foreach ($products as $p) {
                  if ($p['id'] == $product_id) {
                      $product = $p;
                      break;
                  }
              }
              
              if ($product) {
                  if (isset($_SESSION['wishlist'][$product_id])) {
                      unset($_SESSION['wishlist'][$product_id]);
                      $response['wished'] = false;
                      $response['message'] = $product['name'] . ' removed from wishlist';
                  } else {
                      $_SESSION['wishlist'][$product_id] = [
                          'id' => $product['id'],
                          'name' => $product['name'],
                          'category' => $product['category'],
                          'price' => $product['price'],
                          'variant' => $product['variant']
                      ];
                      $response['wished'] = true;
                      $response['message'] = $product['name'] . ' added to wishlist';
                  }
                  $response['success'] = true;
                  $response['wishlist_count'] = count($_SESSION['wishlist']);
              }
              break;
              
          case 'add_to_cart':
              $product_id = (int)$_POST['product_id'];
              $quantity = (int)($_POST['quantity'] ?? 1);
              $product = null;
              
              // Find product
              foreach ($products as $p) {
                  if ($p['id'] == $product_id) {
                      $product = $p;
                      break;
                  }
              }
              
              if ($product && $product['stock'] > 0) {
                  if (isset($_SESSION['cart'][$product_id])) {
                      $_SESSION['cart'][$product_id]['qty'] = min($_SESSION['cart'][$product_id]['qty'] + $quantity, 99);
                  } else {
                      $_SESSION['cart'][$product_id] = [
                          'id' => $product['id'],
                          'name' => $product['name'],
                          'category' => $product['category'],
                          'price' => $product['price'],
                          'variant' => $product['variant'],
                          'qty' => $quantity
                      ];
                  }
                  $response['success'] = true;
                  $response['message'] = $product['name'] . ' added to cart!';
                  $response['cart_count'] = array_sum(array_column($_SESSION['cart'], 'qty'));
              } else {
                  $response['message'] = 'Product is out of stock';
              }
              break;
              
          case 'get_counts':
              $response['success'] = true;
              $response['cart_count'] = array_sum(array_column($_SESSION['cart'], 'qty'));
              $response['wishlist_count'] = count($_SESSION['wishlist']);
              break;
      }
      
      echo json_encode($response);
      exit();
  }
  
  // Get counts for badges
  $cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
  $wishlist_count = count($_SESSION['wishlist']);
  
  // Convert products to JSON for JavaScript
  $products_json = json_encode($products);
  ?>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="nav-left">
      <a href="index.php" class="brand">addictech</a>
    </div>
    <div class="nav-links">
      <a href="index.php" class="nav-link">HOME</a>
      <a href="about.php" class="nav-link">ABOUT</a>
      <a href="catalog.php" class="nav-link active">CATALOG</a>
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

  <main class="catalog-main">
    <div class="catalog-toolbar">
      <div class="search-wrap">
        <input type="text" class="search-input" placeholder="SEARCH" id="searchInput" />
        <button class="search-icon-btn" aria-label="Search">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </div>
      <div class="filter-tags">
        <button class="tag active" data-filter="all">ALL</button>
        <button class="tag" data-filter="keyboard">KEYBOARD</button>
        <button class="tag" data-filter="mouse">MOUSE</button>
        <button class="tag" data-filter="headset">HEADSET</button>
        <button class="tag" data-filter="monitor">MONITOR</button>
        <button class="tag" data-filter="speaker">SPEAKER</button>
        <button class="tag" data-filter="webcam">WEB CAM</button>
        <button class="tag sort-btn" id="sortBtn">Sort By Price</button>
      </div>
    </div>
    <div class="product-grid" id="productGrid"></div>
  </main>

  <div class="modal-backdrop" id="modalBackdrop">
    <div class="modal" id="productModal">
      <button class="modal-close" id="modalClose" aria-label="Close">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </button>

      <div class="modal-left">
        <div class="modal-img">
          <svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">
            <rect x="1" y="1" width="298" height="338" stroke="#555" stroke-width="1.5"/>
            <line x1="1" y1="1" x2="299" y2="339" stroke="#555" stroke-width="1.5"/>
            <line x1="299" y1="1" x2="1" y2="339" stroke="#555" stroke-width="1.5"/>
          </svg>
        </div>
        <p class="modal-product-name" id="modalName">PRODUCT NAME</p>
      </div>

      <div class="modal-right">
        <div class="modal-desc-box">
          <p class="modal-desc-title">Product Description</p>
          <p class="modal-desc-text" id="modalDesc"></p>
        </div>

        <p class="modal-availability">
          AVAILABLE: <span class="modal-stock" id="modalStock">IN STOCK</span>
        </p>

        <div class="modal-action-row">
          <button class="modal-share-btn" id="modalShareBtn">
            SHARE
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </button>
          <button class="modal-wish-btn" id="modalWishBtn" aria-label="Add to wishlist">
            <svg id="wishIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span id="wishLabel">WISHLIST</span>
          </button>
        </div>

        <p class="modal-price" id="modalPrice">₱0</p>
        <hr class="modal-divider"/>

        <div class="modal-qty-row">
          <span class="modal-qty-label">Quantity:</span>
          <div class="modal-qty-ctrl">
            <span class="modal-qty-val" id="qtyVal">1</span>
            <button class="qty-btn" id="qtyPlus">+</button>
            <button class="qty-btn" id="qtyMinus">−</button>
          </div>
        </div>

        <button class="modal-atc-btn" id="modalAtcBtn">ADD TO CART</button>
      </div>
    </div>
  </div>

  <div class="atc-toast" id="atcToast"></div>

  <script>
    // Pass PHP data to JavaScript
    const products = <?php echo $products_json; ?>;
    const wishlistData = <?php echo json_encode($_SESSION['wishlist']); ?>;
    
    // Create wishlist set for quick lookup
    const wishlistIds = new Set(Object.keys(wishlistData).map(id => parseInt(id)));

    /* ── WISHLIST FUNCTIONS ── */
    function isWishlisted(id) {
      return wishlistIds.has(id);
    }

    function toggleWishlist(product) {
      return fetch('catalog.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_wishlist&product_id=${product.id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.wished) {
            wishlistIds.add(product.id);
          } else {
            wishlistIds.delete(product.id);
          }
          updateWishBadge(data.wishlist_count);
          return data.wished;
        }
        return false;
      });
    }

    function addToCart(product, qty) {
      return fetch('catalog.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_to_cart&product_id=${product.id}&quantity=${qty}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateBadge(data.cart_count);
          showToast(data.message);
          return true;
        } else {
          showToast(data.message);
          return false;
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

    function updateWishBadge(count) {
      const badge = document.getElementById('wishBadge');
      if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
    }

    /* ── TOAST ── */
    function showToast(message) {
      const t = document.getElementById('atcToast');
      t.textContent = message;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2200);
    }

    /* ── MODAL STATE ── */
    let currentProduct = null;
    let qty = 1;

    /* ── GRID ── */
    let activeFilter = 'all';
    let sortAsc = null;
    let sortState = 0;
    let searchTerm = '';

    function renderGrid() {
      const grid = document.getElementById('productGrid');
      let filtered = products.filter(p => {
        const matchCat    = activeFilter === 'all' || p.category === activeFilter;
        const matchSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase());
        return matchCat && matchSearch;
      });
      
      if (sortAsc !== null) {
        filtered = [...filtered].sort((a,b) => sortAsc ? a.price - b.price : b.price - a.price);
      }
      
      grid.innerHTML = filtered.map((p, i) => `
        <div class="product-card" style="animation-delay:${i * 0.05}s" data-id="${p.id}">
          <div class="product-img">
            <svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">
              <rect x="1" y="1" width="298" height="338" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="1" y1="1" x2="299" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="299" y1="1" x2="1" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
            </svg>
            <button class="card-wish-btn ${isWishlisted(p.id) ? 'wished' : ''}" data-id="${p.id}" aria-label="Wishlist">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="${isWishlisted(p.id) ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
          </div>
          <div class="product-meta">
            <span class="product-name">${p.name.toUpperCase()}</span>
            <span class="product-price">₱${p.price.toLocaleString()}</span>
          </div>
        </div>
      `).join('');

      // Add event listeners
      grid.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => openModal(parseInt(card.dataset.id)));
      });

      // Heart buttons
      grid.querySelectorAll('.card-wish-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
          e.stopPropagation();
          const p = products.find(x => x.id === parseInt(btn.dataset.id));
          if (!p) return;
          
          const wished = await toggleWishlist(p);
          btn.classList.toggle('wished', wished);
          btn.querySelector('svg').setAttribute('fill', wished ? 'currentColor' : 'none');
        });
      });
    }

    /* ── MODAL ── */
    function openModal(id) {
      const p = products.find(x => x.id === id);
      if (!p) return;
      
      currentProduct = p;
      qty = 1;
      
      document.getElementById('qtyVal').textContent = qty;
      document.getElementById('modalName').textContent = p.name.toUpperCase();
      document.getElementById('modalDesc').textContent = p.desc;
      document.getElementById('modalPrice').textContent = '₱' + p.price.toLocaleString();
      
      const stockEl = document.getElementById('modalStock');
      stockEl.textContent = p.stock ? 'IN STOCK' : 'OUT OF STOCK';
      stockEl.className = 'modal-stock ' + (p.stock ? 'in-stock' : 'out-stock');
      
      const atcBtn = document.getElementById('modalAtcBtn');
      atcBtn.textContent = p.stock ? 'ADD TO CART' : 'OUT OF STOCK';
      atcBtn.disabled = !p.stock;
      atcBtn.style.opacity = p.stock ? '1' : '0.45';
      
      // Update wish button state
      const wishBtn = document.getElementById('modalWishBtn');
      const wishIcon = document.getElementById('wishIcon');
      const wishLabel = document.getElementById('wishLabel');
      const wished = isWishlisted(p.id);
      
      wishBtn.classList.toggle('modal-wish-active', wished);
      wishIcon.setAttribute('fill', wished ? '#e05252' : 'none');
      wishIcon.setAttribute('stroke', wished ? '#e05252' : 'currentColor');
      wishLabel.textContent = wished ? 'WISHLISTED' : 'WISHLIST';
      
      document.getElementById('modalBackdrop').classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      document.getElementById('modalBackdrop').classList.remove('open');
      document.body.style.overflow = '';
    }

    /* ── EVENT LISTENERS ── */
    document.addEventListener('DOMContentLoaded', () => {
      // Modal close events
      document.getElementById('modalClose').addEventListener('click', closeModal);
      document.getElementById('modalBackdrop').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeModal();
      });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

      // Quantity controls
      document.getElementById('qtyPlus').addEventListener('click', () => { 
        qty = Math.min(qty + 1, 99); 
        document.getElementById('qtyVal').textContent = qty; 
      });
      
      document.getElementById('qtyMinus').addEventListener('click', () => { 
        qty = Math.max(qty - 1, 1);  
        document.getElementById('qtyVal').textContent = qty; 
      });

      // Add to cart
      document.getElementById('modalAtcBtn').addEventListener('click', async () => {
        if (!currentProduct || !currentProduct.stock) return;
        const success = await addToCart(currentProduct, qty);
        if (success) closeModal();
      });

      // Modal wishlist button
      document.getElementById('modalWishBtn').addEventListener('click', async () => {
        if (!currentProduct) return;
        
        const wished = await toggleWishlist(currentProduct);
        
        const wishBtn = document.getElementById('modalWishBtn');
        const wishIcon = document.getElementById('wishIcon');
        const wishLabel = document.getElementById('wishLabel');
        
        wishBtn.classList.toggle('modal-wish-active', wished);
        wishIcon.setAttribute('fill', wished ? '#e05252' : 'none');
        wishIcon.setAttribute('stroke', wished ? '#e05252' : 'currentColor');
        wishLabel.textContent = wished ? 'WISHLISTED' : 'WISHLIST';
        
        // Update card heart if visible
        const card = document.querySelector(`.card-wish-btn[data-id="${currentProduct.id}"]`);
        if (card) {
          card.classList.toggle('wished', wished);
          card.querySelector('svg').setAttribute('fill', wished ? 'currentColor' : 'none');
        }
      });

      // Share button
      document.getElementById('modalShareBtn').addEventListener('click', () => {
        navigator.clipboard.writeText(window.location.href).then(() => {
          const btn = document.getElementById('modalShareBtn');
          const orig = btn.innerHTML;
          btn.innerHTML = 'COPIED!';
          setTimeout(() => { btn.innerHTML = orig; }, 1500);
        });
      });

      // Filter buttons
      document.querySelectorAll('.tag[data-filter]').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.tag[data-filter]').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          activeFilter = btn.dataset.filter;
          renderGrid();
        });
      });

      // Sort button
      document.getElementById('sortBtn').addEventListener('click', () => {
        sortState = (sortState + 1) % 3;
        sortAsc = sortState === 1 ? true : sortState === 2 ? false : null;
        const btn = document.getElementById('sortBtn');
        btn.textContent = sortState === 1 ? 'Price: Low → High'
                        : sortState === 2 ? 'Price: High → Low'
                        : 'Sort By Price';
        renderGrid();
      });

      // Search input
      document.getElementById('searchInput').addEventListener('input', (e) => {
        searchTerm = e.target.value;
        renderGrid();
      });

      // Initial render
      renderGrid();
    });
  </script>

</body>
</html>
<?= $this->endSection() ?>