<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Admin: Users</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_users.css') ?>" />
</head>
<body>

  <div class="products-page">

    <!-- Main Content Area -->
    <div class="products-main">

      <!-- Title Row -->
      <div class="products-title-row">
        <h1 class="page-title">USERS</h1>
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

      <!-- Users Table -->
      <div class="products-table-wrapper">
        <table class="products-table">
          <thead>
            <tr>
              <th class="col-img"></th>
              <th class="col-id">ID</th>
              <th class="col-product">NAME</th>
              <th class="col-type">ROLE</th>
              <th class="col-type">ADDRESS</th>
              <th class="col-qty">STATUS</th>
              <th class="col-action">ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($users)) : ?>
              <?php foreach ($users as $user) : ?>
                <tr>
                  <td class="col-img">
                    <div class="product-thumb-placeholder">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="12" cy="10" r="3"/>
                        <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
                      </svg>
                    </div>
                  </td>
                  <td class="col-id product-id">#<?= esc($user['id']) ?></td>
                  <td class="col-product product-name"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                  <td class="col-type product-type"><?= esc($user['role'] ?? '—') ?></td>
                  <td class="col-type product-type"><?= esc($user['address'] ?? '—') ?><?= !empty($user['city']) ? ', ' . esc($user['city']) : '' ?></td>
                  <td class="col-qty product-quantity">
                    <span class="admin-status admin-status-<?= strtolower(esc($user['status'] ?? 'active')) ?>">
                      <?= esc($user['status'] ?? 'ACTIVE') ?>
                    </span>
                  </td>
                  <td class="col-action product-actions">
                    <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="btn-action">VIEW</a>
                    <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn-action">EDIT</a>
                    <button
                      class="btn-action btn-delete"
                      onclick="openDeleteModal('<?= $user['id'] ?>', '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')"
                    >DELETE</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="7" class="empty-row">No users found.</td>
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
          <li><a href="<?= base_url('admin/orders') ?>">ORDERS</a></li>
          <li><a href="<?= base_url('admin/users') ?>" class="active">USERS</a></li>
          <li><a href="<?= base_url('logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

  <!-- Delete Confirmation Modal -->
  <div class="admin-modal-overlay" id="deleteModal">
    <div class="admin-modal-box">
      <h2 class="admin-modal-title">Confirm Delete</h2>
      <p class="admin-modal-text">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
      <div class="admin-modal-actions">
        <button class="admin-btn admin-btn-view" onclick="closeDeleteModal()">CANCEL</button>
        <a href="" id="confirmDeleteBtn" class="admin-btn admin-btn-delete">DELETE</a>
      </div>
    </div>
  </div>

  <script>
    function openDeleteModal(id, name) {
      document.getElementById('deleteUserName').textContent = name;
      document.getElementById('confirmDeleteBtn').href = `<?= base_url('admin/users/delete/') ?>${id}`;
      document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.remove('active');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
      if (e.target === this) closeDeleteModal();
    });
  </script>

</body>
<?= $this->endSection() ?>