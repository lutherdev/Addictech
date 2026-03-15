<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – View Order</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/orders_view.css') ?>" />
</head>
<body>

  <div class="view-order-page">

    <!-- Main Content Area -->
    <div class="view-order-main">

      <!-- Title Row -->
      <div class="view-order-title-row">
        <h1 class="page-title">ORDER DETAILS</h1>
        <div class="title-actions">
          <a href="<?= base_url('admin/orders/update/' . $order['id']) ?>" class="btn-outline">UPDATE STATUS</a>
          <a href="<?= base_url('admin/orders') ?>" class="btn-outline">← BACK</a>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="flash flash-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
        <div class="flash flash-error"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <!-- Order Summary Card -->
      <div class="order-summary-card">

        <!-- Left: Order Info -->
        <div class="order-info-col">

          <div class="detail-group">
            <span class="detail-label">ORDER ID</span>
            <span class="detail-value order-id">#<?= esc($order['id']) ?></span>
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">CUSTOMER</span>
              <span class="detail-value"><?= esc($order['username']) ?></span>
            </div>
            <div class="detail-group">
              <span class="detail-label">EMAIL</span>
              <span class="detail-value"><?= esc($order['email']) ?></span>
            </div>
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">TOTAL PRICE</span>
              <span class="detail-value detail-price">₱<?= number_format($order['total_price'], 0) ?></span>
            </div>
            <div class="detail-group">
              <span class="detail-label">DATE PLACED</span>
              <span class="detail-value detail-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
            </div>
          </div>

          <div class="detail-group">
            <span class="detail-label">STATUS</span>
            <span class="status-badge status-<?= esc(strtolower($order['status'])) ?>">
              <?= strtoupper(esc($order['status'])) ?>
            </span>
          </div>

        </div>

        <!-- Right: Delete Action -->
        <div class="order-action-col">
          <a href="<?= base_url('admin/orders/delete/' . $order['id']) ?>"
             class="btn-action btn-delete"
             onclick="return confirm('Are you sure you want to delete this order?')">
            DELETE ORDER
          </a>
        </div>

      </div>

      <!-- Order Items Table -->
      <div class="section-label">ORDER ITEMS</div>
      <div class="order-items-wrapper">
        <table class="order-items-table">
          <thead>
            <tr>
              <th class="col-img"></th>
              <th class="col-product">PRODUCT</th>
              <th class="col-category">CATEGORY</th>
              <th class="col-qty">QUANTITY</th>
              <th class="col-unit">UNIT PRICE</th>
              <th class="col-subtotal">SUBTOTAL</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($order_items)): ?>
              <?php foreach ($order_items as $item): ?>
                <tr>
                  <td class="col-img">
                    <?php if (!empty($item['image'])): ?>
                      <img src="<?= base_url('uploads/' . esc($item['image'])) ?>"
                           alt="<?= esc($item['product_name'] ?? '') ?>" class="item-thumb" />
                    <?php else: ?>
                      <div class="item-thumb-placeholder">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="4" y="8" width="40" height="32" rx="2" stroke="currentColor" stroke-width="2"/>
                          <circle cx="16" cy="20" r="4" stroke="currentColor" stroke-width="2"/>
                          <path d="M4 36l10-10 8 8 6-6 16 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </div>
                    <?php endif; ?>
                  </td>
                 <td class="col-product item-name"><?= esc($item['product_name'] ?? '—') ?></td>
                  <td class="col-category item-category"><?= esc($item['category'] ?? '—') ?></td>
                  <td class="col-qty item-qty"><?= esc($item['quantity']) ?></td>
                  <td class="col-unit item-price">₱<?= number_format($item['price'], 0) ?></td>
                  <td class="col-subtotal item-subtotal">₱<?= number_format($item['price'] * $item['quantity'], 0) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="empty-row">No items found for this order</td>
              </tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="total-label">TOTAL</td>
              <td class="total-value">₱<?= number_format($order['total_price'], 0) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>

    </div>

    <!-- Right Sidebar Navigation -->
    <aside class="sidebar-nav">
      <nav>
        <ul>
          <li><a href="<?= base_url('admin/products') ?>">PRODUCTS</a></li>
          <li><a href="<?= base_url('admin/orders') ?>" class="active">ORDERS</a></li>
          <li><a href="<?= base_url('admin/users') ?>">USERS</a></li>
          <li><a href="<?= base_url('auth/logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

</body>
<?= $this->endSection() ?>