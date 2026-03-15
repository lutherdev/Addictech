<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Orders</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/order.css') ?>" />
</head>
<body>

  <div class="orders-page">

    <!-- Main Content Area -->
    <div class="orders-main">

      <!-- Title Row -->
      <div class="orders-title-row">
        <h1 class="page-title">ORDERS</h1>
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

      <!-- Orders Table -->
      <div class="orders-table-wrapper">
        <table class="orders-table">
          <thead>
            <tr>
              <th class="col-img"></th>
              <th class="col-id">ID</th>
              <th class="col-product">PRODUCT</th>
              <th class="col-user">USER</th>
              <th class="col-price">PRICE</th>
              <th class="col-qty">QUANTITY</th>
              <th class="col-action">ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($orders) && is_array($orders)): ?>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td class="col-img">
                    <?php if (!empty($order['product_image'])): ?>
                      <img src="<?= base_url('uploads/' . esc($order['product_image'])) ?>"
                           alt="<?= esc($order['product_name'] ?? '') ?>" class="order-thumb" />
                    <?php else: ?>
                      <div class="order-thumb-placeholder">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="4" y="8" width="40" height="32" rx="2" stroke="currentColor" stroke-width="2"/>
                          <circle cx="16" cy="20" r="4" stroke="currentColor" stroke-width="2"/>
                          <path d="M4 36l10-10 8 8 6-6 16 12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="col-id order-id">#<?= esc($order['id']) ?></td>
                  <td class="col-product order-product"><?= esc($order['order_number'] ?? 'N/A') ?></td>
                  <td class="col-user order-user"><?= esc($order['username'] ?? 'N/A') ?></td>
                  <td class="col-price order-price">₱<?= number_format($order['total'], 0) ?></td>
                  <td class="col-qty order-qty"><?= esc($order['total_quantity'] ?? 1) ?></td>
                  <td class="col-action order-actions">
                    <a href="<?= base_url('admin/orders/view/' . $order['id']) ?>" class="btn-action">VIEW</a>
                    <a href="<?= base_url('admin/orders/update/' . $order['id']) ?>" class="btn-action">UPDATE</a>
                    <a href="<?= base_url('admin/orders/delete/' . $order['id']) ?>"
                       class="btn-action btn-delete"
                       onclick="return confirm('Delete this order?')">DELETE</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty-row">No orders found</td>
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
          <li><a href="<?= base_url('products') ?>">PRODUCTS</a></li>
          <li><a href="<?= base_url('orders') ?>" class="active">ORDERS</a></li>
          <li><a href="<?= base_url('users') ?>">USERS</a></li>
          <li><a href="<?= base_url('logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

</body>
<?= $this->endSection() ?>