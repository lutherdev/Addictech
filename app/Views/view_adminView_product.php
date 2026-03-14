<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – View Product</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/products_view.css') ?>" />
</head>
<body>

  <div class="view-product-page">

    <!-- Main Content Area -->
    <div class="view-product-main">

      <!-- Title Row -->
      <div class="view-product-title-row">
        <h1 class="page-title">PRODUCT DETAILS</h1>
        <div class="title-actions">
          <a href="<?= base_url('products') ?>" class="btn-outline">← BACK</a>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="flash flash-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
        <div class="flash flash-error"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <!-- Product Card -->
      <div class="product-card">

        <!-- Left: Image -->
        <div class="product-image-col">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= base_url('uploads/' . esc($product['image'])) ?>"
                 alt="<?= esc($product['name']) ?>" class="product-image" />
          <?php else: ?>
            <div class="product-image-placeholder">
              <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="6" y="10" width="52" height="44" rx="2" stroke="currentColor" stroke-width="2"/>
                <circle cx="22" cy="26" r="6" stroke="currentColor" stroke-width="2"/>
                <path d="M6 46l14-14 10 10 8-8 20 16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
              <span>NO IMAGE</span>
            </div>
          <?php endif; ?>

          <!-- Status Badge -->
          <div class="status-badge status-<?= esc(strtolower($product['status'] ?? 'active')) ?>">
            <?= strtoupper(esc($product['status'] ?? 'ACTIVE')) ?>
          </div>
        </div>

        <!-- Right: Details -->
        <div class="product-details-col">

          <div class="detail-group">
            <span class="detail-label">PRODUCT ID</span>
            <span class="detail-value product-id">#<?= esc($product['id']) ?></span>
          </div>

          <div class="detail-group">
            <span class="detail-label">PRODUCT NAME</span>
            <span class="detail-value detail-name"><?= esc($product['name']) ?></span>
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">CATEGORY</span>
              <span class="detail-value"><?= esc($product['category']) ?></span>
            </div>

            <?php if (!empty($product['variant'])): ?>
            <div class="detail-group">
              <span class="detail-label">VARIANT</span>
              <span class="detail-value"><?= esc($product['variant']) ?></span>
            </div>
            <?php endif; ?>
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">PRICE</span>
              <span class="detail-value detail-price">₱<?= number_format($product['price'], 0) ?></span>
            </div>

            <div class="detail-group">
              <span class="detail-label">STOCK</span>
              <span class="detail-value <?= ($product['stock'] ?? 0) == 0 ? 'stock-out' : (($product['stock'] ?? 0) <= 5 ? 'stock-low' : '') ?>">
                <?= esc($product['stock'] ?? 0) ?> unit/s
              </span>
            </div>
          </div>

          <?php if (!empty($product['description'])): ?>
          <div class="detail-group">
            <span class="detail-label">DESCRIPTION</span>
            <p class="detail-value detail-description"><?= esc($product['description']) ?></p>
          </div>
          <?php endif; ?>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">CREATED</span>
              <span class="detail-value detail-date">
                <?= date('M d, Y', strtotime($product['created_at'])) ?>
              </span>
            </div>

            <div class="detail-group">
              <span class="detail-label">LAST UPDATED</span>
              <span class="detail-value detail-date">
                <?= date('M d, Y', strtotime($product['updated_at'])) ?>
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="product-actions">
            <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn-action">EDIT PRODUCT</a>
            <a href="<?= base_url('products/delete/' . $product['id']) ?>"
               class="btn-action btn-delete"
               onclick="return confirm('Are you sure you want to delete this product?')">DELETE</a>
          </div>

        </div>
      </div>
    </div>

    <!-- Right Sidebar Navigation -->
    <aside class="sidebar-nav">
      <nav>
        <ul>
          <li><a href="<?= base_url('products') ?>" class="active">PRODUCTS</a></li>
          <li><a href="<?= base_url('orders') ?>">ORDERS</a></li>
          <li><a href="<?= base_url('users') ?>">USERS</a></li>
          <li><a href="<?= base_url('logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

</body>
<?= $this->endSection() ?>