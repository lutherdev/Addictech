<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Admin: View User</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_viewUsers.css') ?>" />
</head>
<body>

<nav class="admin-nav">
  <a href="<?= base_url('admin/products') ?>">PRODUCTS</a>
  <a href="<?= base_url('admin/orders') ?>">ORDERS</a>
  <a href="<?= base_url('admin/users') ?>" class="active">USERS</a>
  <a href="<?= base_url('auth/logout') ?>">LOGOUT</a>
</nav>

<div class="admin-page-wrapper">

  <div class="admin-page-header">
    <a href="<?= base_url('admin/users') ?>" class="admin-back-link">← BACK TO USERS</a>
    <h1 class="admin-page-title">VIEW USER</h1>
  </div>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="admin-alert admin-alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')) : ?>
    <div class="admin-alert admin-alert-error"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <div class="admin-detail-card">

    <!-- Card Header -->
    <div class="admin-detail-header">
      <div class="admin-detail-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
          <rect x="3" y="3" width="18" height="18" rx="2"/>
          <circle cx="12" cy="10" r="3"/>
          <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
        </svg>
      </div>
      <div class="admin-detail-header-info">
        <p class="admin-detail-uid">#<?= esc($user['id']) ?></p>
        <h2 class="admin-detail-name"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h2>
        <span class="admin-status admin-status-<?= strtolower(esc($user['status'] ?? 'active')) ?>">
          <?= esc($user['status'] ?? 'ACTIVE') ?>
        </span>
      </div>
      <div class="admin-detail-header-actions">
        <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="admin-btn admin-btn-edit">EDIT</a>
        <button class="admin-btn admin-btn-delete"
          onclick="openDeleteModal('<?= $user['id'] ?>', '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')">
          DELETE
        </button>
      </div>
    </div>

    <div class="admin-detail-divider"></div>

    <!-- Detail Fields -->
    <div class="admin-detail-grid">

      <div class="admin-detail-section">
        <p class="admin-detail-section-label">ACCOUNT</p>
        <div class="admin-detail-row">
          <span class="admin-detail-key">EMAIL</span>
          <span class="admin-detail-val"><?= esc($user['email'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">PHONE</span>
          <span class="admin-detail-val"><?= esc($user['phone'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">ROLE</span>
          <span class="admin-detail-val"><?= esc($user['role'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">STATUS</span>
          <span class="admin-detail-val"><?= esc($user['status'] ?? '—') ?></span>
        </div>
      </div>

      <div class="admin-detail-section">
        <p class="admin-detail-section-label">ADDRESS</p>
        <div class="admin-detail-row">
          <span class="admin-detail-key">ADDRESS</span>
          <span class="admin-detail-val"><?= esc($user['address'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">CITY</span>
          <span class="admin-detail-val"><?= esc($user['city'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">POSTAL CODE</span>
          <span class="admin-detail-val"><?= esc($user['postal_code'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">COUNTRY</span>
          <span class="admin-detail-val"><?= esc($user['country'] ?? '—') ?></span>
        </div>
      </div>

      <div class="admin-detail-section admin-detail-section-full">
        <p class="admin-detail-section-label">TIMESTAMPS</p>
        <div class="admin-detail-row">
          <span class="admin-detail-key">CREATED</span>
          <span class="admin-detail-val"><?= esc($user['created_at'] ?? '—') ?></span>
        </div>
        <div class="admin-detail-row">
          <span class="admin-detail-key">LAST UPDATED</span>
          <span class="admin-detail-val"><?= esc($user['updated_at'] ?? '—') ?></span>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal-overlay" id="deleteModal">
  <div class="admin-modal-box">
    <h2 class="admin-modal-title">Confirm Delete</h2>
    <p class="admin-modal-text">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
    <div class="admin-modal-actions">
      <button class="admin-btn admin-btn-view" onclick="closeDeleteModal()">CANCEL</button>
      <a href="#" id="confirmDeleteBtn" class="admin-btn admin-btn-delete">DELETE</a>
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