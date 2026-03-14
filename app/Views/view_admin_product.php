<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Products</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/products.css') ?>" />
</head>
<body>

  <div class="products-page">

    <!-- Main Content Area -->
    <div class="products-main">

      <!-- Title Row -->
      <div class="products-title-row">
        <h1 class="page-title">PRODUCTS</h1>
        <a href="<?= base_url('products/add') ?>" class="btn-add-product">ADD PRODUCT</a>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="flash flash-success">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
        <div class="flash flash-error">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <!-- Products Table -->
      <div class="products-table-wrapper">
        <table class="products-table">
          <thead>
            <tr>
              <th class="col-img"></th>
              <th class="col-id">ID</th>
              <th class="col-product">PRODUCT</th>
              <th class="col-type">TYPE</th>
              <th class="col-price">PRICE</th>
              <th class="col-qty">QUANTITY</th>
              <th class="col-action">ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($products) && is_array($products)): ?>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td class="col-img">
                    <?php if (!empty($product['image'])): ?>
                      <img src="<?= base_url('uploads/' . esc($product['image'])) ?>" alt="<?= esc($product['name']) ?>" class="product-thumb" />
                    <?php else: ?>
                      <div class="product-thumb-placeholder">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="4" y="8" width="40" height="32" rx="2" stroke="currentColor" stroke-width="2"/>
                          <circle cx="16" cy="20" r="4" stroke="currentColor" stroke-width="2"/>
                          <path d="M4 36l10-10 8 8 6-6 16 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="col-id product-id">#<?= esc($product['id']) ?></td>
                  <td class="col-product product-name"><?= esc($product['name']) ?></td>
                  <td class="col-type product-type"><?= esc($product['category']) ?></td>
                  <td class="col-price product-price">₱<?= number_format($product['price'], 0) ?></td>
                  <td class="col-qty product-quantity"><?= esc($product['stock']) ?></td>
                  <td class="col-action product-actions">
                    <a href="<?= base_url('products/view/' . $product['id']) ?>" class="btn-action">VIEW</a>
                    <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn-action">EDIT</a>
                    <a href="<?= base_url('products/delete/' . $product['id']) ?>" class="btn-action btn-delete"
                       onclick="return confirm('Delete this product?')">DELETE</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty-row">No products found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
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