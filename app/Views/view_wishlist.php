<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Wishlist</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/wishlist.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('/public/css/catalog.css') ?>" />
</head>

<?php
$products_json = json_encode(array_map(function($p) {
    return [
        'id'      => (int) $p['id'],
        'name'    => $p['name'],
        'category'=> $p['category'],
        'price'   => (float) $p['price'],
        'variant' => $p['variant']   ?? '',
        'desc'    => $p['description'] ?? '',
        'stock'   => (int) $p['stock'],
    ];
}, $products));
?>

<main class="wishlist-main">
  <?php if (session()->getFlashdata('error')) : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-center">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')) : ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-center">
      <i class="fas fa-check-circle mr-2"></i>
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>
 
  <div class="wishlist-header">
 
    <!-- TOP ROW: title + search -->
    <div class="wishlist-header-top">
      <h1 class="wishlist-title">MY WISHLIST</h1>
      <div class="search-wrap">
        <input type="text" class="search-input" placeholder="SEARCH" id="searchInput" />
        <button class="search-icon-btn" aria-label="Search">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </button>
      </div>
    </div>
 
    <!-- BOTTOM ROW: filter tags -->
    <div class="filter-tags">
      <button class="tag active" data-filter="all">ALL</button>
      <button class="tag" data-filter="KEYBOARD">KEYBOARD</button>
      <button class="tag" data-filter="MOUSE">MOUSE</button>
      <button class="tag" data-filter="HEADSET">HEADSET</button>
      <button class="tag" data-filter="MONITOR">MONITOR</button>
      <button class="tag" data-filter="SPEAKER">SPEAKER</button>
      <button class="tag" data-filter="cam">WEB CAM</button>
    </div>
 
  </div>
 
  <div class="product-grid">
    <?php if (!empty($products)) : ?>
      <?php foreach ($products as $p) : ?>
        <div class="product-card"
             data-id="<?= $p['id'] ?>"
             data-name="<?= esc(strtolower($p['name'])) ?>"
             data-category="<?= esc($p['category']) ?>">
          <div class="product-img">
            <svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">
              <rect x="1" y="1" width="298" height="338" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="1" y1="1" x2="299" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
              <line x1="299" y1="1" x2="1" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>
            </svg>
            <button type="button" class="card-wish-btn" data-id="<?= $p['id'] ?>">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="#e05252" stroke="#e05252" stroke-width="1.8">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </button>
          </div>
          <div class="product-meta">
            <span class="product-name"><?= esc(strtoupper($p['name'])) ?></span>
            <span class="product-price">₱<?= number_format($p['price']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="wishlist-empty">
        Your wishlist is empty.
        <a href="<?= base_url('catalog') ?>">Browse the catalog →</a>
      </div>
    <?php endif; ?>
  </div>
 
</main>
 
<!-- MODAL -->
<div class="modal-backdrop" id="modalBackdrop">
  <div class="modal" id="productModal">
    <button type="button" class="modal-close" id="modalClose" aria-label="Close">
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
        <button type="button" class="modal-share-btn" id="modalShareBtn">
          SHARE
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
        </button>
        <button type="button" class="modal-wish-btn modal-wish-active" id="modalWishBtn" aria-label="Remove from wishlist">
          <svg id="wishIcon" width="18" height="18" viewBox="0 0 24 24" fill="#e05252" stroke="#e05252" stroke-width="1.8">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
          <span id="wishLabel">WISHLISTED</span>
        </button>
      </div>
 
      <p class="modal-price" id="modalPrice">₱0</p>
      <hr class="modal-divider"/>
 
      <div class="modal-qty-row">
        <span class="modal-qty-label">Quantity:</span>
        <div class="modal-qty-ctrl">
          <button type="button" class="qty-btn" id="qtyMinus">−</button>
          <span class="modal-qty-val" id="qtyVal">1</span>
          <button type="button" class="qty-btn" id="qtyPlus">+</button>
        </div>
      </div>
 
      <form id="modalForm" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" id="formProductId" />
        <input type="hidden" name="quantity"   id="formQty" value="1" />
 
        <button id="modalAtcBtn" class="modal-atc-btn" type="submit"
                formaction="<?= base_url('cart/add') ?>">
          ADD TO CART
        </button>
 
        <button id="modalBuyNowBtn" class="modal-atc-btn" type="submit"
                formaction="<?= base_url('orders/buynow') ?>">
          BUY NOW
        </button>
      </form>
    </div>
  </div>
</div>
 
<div class="atc-toast" id="atcToast"></div>
 
<script>
  const products  = <?= $products_json ?>;
  const CSRF_NAME = '<?= csrf_token() ?>';
  const CSRF_HASH = '<?= csrf_hash() ?>';
 
  const wishlistIds = new Set(products.map(function(p) { return p.id; }));
 
  function isWishlisted(id) { return wishlistIds.has(id); }
  function toggleWishlist(product) {
    wishlistIds.delete(product.id);
    return Promise.resolve(false);
  }
</script>
<script src="<?= base_url('/public/js/wishlist.js') ?>"></script>
 
<?= $this->endSection() ?>
