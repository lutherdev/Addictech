<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Update Order</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/orders_edit.css') ?>" />
</head>
<body>

  <div class="update-order-page">

    <!-- Main Content Area -->
    <div class="update-order-main">

      <!-- Title Row -->
      <div class="update-order-title-row">
        <h1 class="page-title">UPDATE ORDER</h1>
        <div class="title-actions">
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

      <!-- Order Info + Update Form -->
      <div class="update-order-card">

        <!-- Order Summary -->
        <div class="order-summary">

          <div class="detail-group">
            <span class="detail-label">ORDER ID</span>
            <span class="detail-value order-id">#<?= esc($order['id']) ?></span>
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">CUSTOMER</span>
              <span class="detail-value"><?= esc($order['user_id']) ?></span>
            </div>
            
          </div>

          <div class="detail-row">
            <div class="detail-group">
              <span class="detail-label">TOTAL PRICE</span>
              <span class="detail-value detail-price">₱<?= number_format($order['total'], 0) ?></span>
            </div>
            <div class="detail-group">
              <span class="detail-label">DATE PLACED</span>
              <span class="detail-value detail-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
            </div>
          </div>

          <div class="detail-group">
            <span class="detail-label">CURRENT STATUS</span>
            <span class="status-badge status-<?= esc(strtolower($order['status'])) ?>">
              <?= strtoupper(esc($order['status'])) ?>
            </span>
          </div>

        </div>

        <!-- Divider -->
        <div class="card-divider"></div>

        <!-- Update Form -->
        <div class="update-form-col">
          <form action="<?= base_url('admin/orders/update/' . $order['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
              <label for="status">NEW STATUS</label>
              <select id="status" name="status" required>
                <?php
                  $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                  foreach ($statuses as $s):
                    $selected = $order['status'] === $s ? 'selected' : '';
                ?>
                  <option value="<?= $s ?>" <?= $selected ?>><?= strtoupper($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Status flow hint -->
            <div class="status-flow">
              <div class="flow-step <?= $order['status'] === 'pending'    ? 'active' : ($order['status'] !== 'cancelled' ? 'done' : '') ?>">PENDING</div>
              <div class="flow-arrow">→</div>
              <div class="flow-step <?= $order['status'] === 'processing' ? 'active' : (in_array($order['status'], ['shipped','delivered']) ? 'done' : '') ?>">PROCESSING</div>
              <div class="flow-arrow">→</div>
              <div class="flow-step <?= $order['status'] === 'shipped'    ? 'active' : ($order['status'] === 'delivered' ? 'done' : '') ?>">SHIPPED</div>
              <div class="flow-arrow">→</div>
              <div class="flow-step <?= $order['status'] === 'delivered'  ? 'active' : '' ?>">DELIVERED</div>
            </div>

            <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
              <p class="cancel-warning">
                ⚠ Setting status to <strong>CANCELLED</strong> will automatically restore product stock.
              </p>
            <?php endif; ?>

            <div class="form-actions">
              <a href="<?= base_url('admin/orders') ?>" class="btn-cancel">CANCEL</a>
              <button type="submit" class="btn-submit">SAVE CHANGES</button>
            </div>

          </form>
        </div>

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