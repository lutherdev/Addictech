<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Catalog</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/catalog.css') ?>" />
</head>
<body>

<?php
// Normalize products to match DB schema (stock: 0/1, category: cam vs webcam)
$products_json = json_encode(array_map(function($p) {
    return [
        'id'       => (int) $p['id'],
        'name'     => $p['name'],
        'category' => $p['category'],   // raw from DB: cam, keyboard, mouse, etc.
        'price'    => (float) $p['price'],
        'variant'  => $p['variant']  ?? '',
        'desc'     => $p['description'] ?? '',  // DB column is 'description'
        'stock'    => (int) $p['stock'],         // DB is 0 or 1
    ];
}, $products));
?>
 
<main class="catalog-main">
  <div class="catalog-toolbar">
    <div class="search-wrap">
      <input type="text" class="search-input" placeholder="SEARCH" id="searchInput" />
      <button class="search-icon-btn" aria-label="Search">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </button>
    </div>
    <div class="filter-tags">
      <button class="tag active" data-filter="all">ALL</button>
      <button class="tag" data-filter="keyboard">KEYBOARD</button>
      <button class="tag" data-filter="mouse">MOUSE</button>
      <button class="tag" data-filter="headset">HEADSET</button>
      <button class="tag" data-filter="monitor">MONITOR</button>
      <button class="tag" data-filter="speaker">SPEAKER</button>
      <button class="tag" data-filter="cam">WEB CAM</button><!-- matches DB 'cam' -->
      <button class="tag sort-btn" id="sortBtn">Sort By Price</button>
    </div>
  </div>
  <div class="product-grid" id="productGrid"></div>
</main>
 
<!-- MODAL -->
<div class="modal-backdrop" id="modalBackdrop">
  <div class="modal" id="productModal">
    <button class="modal-close" id="modalClose" aria-label="Close">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
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
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
        </button>
        <button class="modal-wish-btn" id="modalWishBtn" aria-label="Add to wishlist">
          <svg id="wishIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
          <span id="wishLabel">WISHLIST</span>
        </button>
      </div>
 
      <p class="modal-price" id="modalPrice">₱0</p>
      <hr class="modal-divider"/>
 
      <div class="modal-qty-row">
        <span class="modal-qty-label">Quantity:</span>
        <div class="modal-qty-ctrl">
          <button class="qty-btn" id="qtyMinus">−</button>
          <span class="modal-qty-val" id="qtyVal">1</span>
          <button class="qty-btn" id="qtyPlus">+</button>
        </div>
      </div>
 
      <button class="modal-atc-btn" id="modalAtcBtn">ADD TO CART</button>
    </div>
  </div>
</div>
 
<div class="atc-toast" id="atcToast"></div>
<script>
  const products     = <?= $products_json ?>;
  const CART_ADD_URL = '<?= base_url('cart/add') ?>';
  const CSRF_NAME    = '<?= csrf_token() ?>';
  const CSRF_HASH    = '<?= csrf_hash() ?>';
</script>
<script src="<?= base_url('/public/js/catalog.js') ?>"></script>
</body>
</html>
<?= $this->endSection() ?>