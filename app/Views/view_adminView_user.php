<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – View User</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_viewUsers.css') ?>" />
</head>
<body>

  <div class="view-user-page">

    <!-- Main Content Area -->
    <div class="view-user-main">

      <!-- Title Row -->
      <div class="view-user-title-row">
        <h1 class="user-page-title">USER DETAILS</h1>
        <div class="user-title-actions">
          <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="user-btn-outline">EDIT</a>
          <a href="<?= base_url('admin/users') ?>" class="user-btn-outline">← BACK</a>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="user-flash user-flash-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
        <div class="user-flash user-flash-error"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <!-- User Summary Card -->
      <div class="user-summary-card">

        <!-- Left: User Info -->
        <div class="user-info-col">

          <div class="user-detail-group">
            <span class="user-detail-label">USER ID</span>
            <span class="user-detail-value user-id-val">#<?= esc($user['id']) ?></span>
          </div>

          <div class="user-detail-row">
            <div class="user-detail-group">
              <span class="user-detail-label">FULL NAME</span>
              <span class="user-detail-value"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></span>
            </div>
            <div class="user-detail-group">
              <span class="user-detail-label">EMAIL</span>
              <span class="user-detail-value"><?= esc($user['email'] ?? '—') ?></span>
            </div>
          </div>

          <div class="user-detail-row">
            <div class="user-detail-group">
              <span class="user-detail-label">PHONE</span>
              <span class="user-detail-value"><?= esc($user['phone'] ?? '—') ?></span>
            </div>
            <div class="user-detail-group">
              <span class="user-detail-label">ROLE</span>
              <span class="user-detail-value"><?= esc($user['role'] ?? '—') ?></span>
            </div>
          </div>

          <div class="user-detail-row">
            <div class="user-detail-group">
              <span class="user-detail-label">CREATED</span>
              <span class="user-detail-value user-detail-date">
                <?= !empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '—' ?>
              </span>
            </div>
            <div class="user-detail-group">
              <span class="user-detail-label">LAST UPDATED</span>
              <span class="user-detail-value user-detail-date">
                <?= !empty($user['updated_at']) ? date('M d, Y', strtotime($user['updated_at'])) : '—' ?>
              </span>
            </div>
          </div>

          <div class="user-detail-group">
            <span class="user-detail-label">STATUS</span>
            <span class="user-status-badge user-status-<?= esc(strtolower($user['status'] ?? 'active')) ?>">
              <?= strtoupper(esc($user['status'] ?? 'ACTIVE')) ?>
            </span>
          </div>

        </div>

        <!-- Right: Delete Action -->
        <div class="user-action-col">
          <a href="#" class="user-btn-action user-btn-delete"
             onclick="openDeleteModal('<?= base_url('admin/users/delete/' . $user['id']) ?>', '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>'); return false;">
            DELETE USER
          </a>
        </div>

      </div>

      <!-- Address Section -->
      <div class="user-section-label">ADDRESS</div>
      <div class="user-items-wrapper">
        <table class="user-items-table">
          <thead>
            <tr>
              <th class="ucol-address">ADDRESS LINE</th>
              <th class="ucol-city">CITY</th>
              <th class="ucol-postal">POSTAL CODE</th>
              <th class="ucol-country">COUNTRY</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="uitem-address"><?= esc($user['address_line1'] ?? $user['address'] ?? '—') ?></td>
              <td class="uitem-city"><?= esc($user['city'] ?? '—') ?></td>
              <td class="uitem-postal"><?= esc($user['postal_code'] ?? '—') ?></td>
              <td class="uitem-country"><?= esc($user['country'] ?? '—') ?></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- Right Sidebar Navigation -->
    <aside class="user-sidebar-nav">
      <nav>
        <ul>
          <li><a href="<?= base_url('admin/products') ?>">PRODUCTS</a></li>
          <li><a href="<?= base_url('admin/orders') ?>">ORDERS</a></li>
          <li><a href="<?= base_url('admin/users') ?>" class="active">USERS</a></li>
          <li><a href="<?= base_url('auth/logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

  <!-- Delete Confirmation Modal -->
  <div class="user-delete-backdrop" id="deleteModalBackdrop">
    <div class="user-delete-modal">
      <h2 class="user-delete-title">DELETE USER</h2>
      <p class="user-delete-msg">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
      <div class="user-delete-actions">
        <button class="user-btn-cancel" id="deleteModalCancel">CANCEL</button>
        <a href="#" class="user-btn-confirm" id="deleteModalConfirm">DELETE</a>
      </div>
    </div>
  </div>

  <script src="<?= base_url('/public/js/delete_modal_user.js') ?>"></script>

</body>
<?= $this->endSection() ?>