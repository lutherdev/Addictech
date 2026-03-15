<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Admin: Users</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_users.css') ?>" />
</head>
<body>

<!-- Admin Sub-Nav -->
<nav class="admin-nav">
  <a href="<?= base_url('admin/products') ?>">PRODUCTS</a>
  <a href="<?= base_url('admin/orders') ?>">ORDERS</a>
  <a href="<?= base_url('admin/users') ?>" class="active">USERS</a>
  <a href="<?= base_url('auth/logout') ?>">LOGOUT</a>
</nav>

<div class="admin-page-wrapper">

  <h1 class="admin-page-title">USERS</h1>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="admin-alert admin-alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')) : ?>
    <div class="admin-alert admin-alert-error">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <div class="admin-table-wrapper">
    <table class="admin-users-table">
      <thead>
        <tr>
          <th></th>
          <th>ID</th>
          <th>NAME</th>
          <th>ROLE</th>
          <th>ADDRESS</th>
          <th>STATUS</th>
          <th>ACTION</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)) : ?>
          <?php foreach ($users as $user) : ?>
            <tr>
              <td class="admin-avatar-cell">
                <div class="admin-avatar">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
                  </svg>
                </div>
              </td>
              <td class="admin-id-cell">#<?= esc($user['id']) ?></td>
              <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
              <td><?= esc($user['role'] ?? '—') ?></td>
              <td><?= esc($user['address'] ?? '—') ?><?= !empty($user['city']) ? ', ' . esc($user['city']) : '' ?></td>
              <td>
                <span class="admin-status admin-status-<?= strtolower(esc($user['status'] ?? 'active')) ?>">
                  <?= esc($user['status'] ?? 'ACTIVE') ?>
                </span>
              </td>
              <td class="admin-action-cell">
                <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="admin-btn admin-btn-view">VIEW</a>
                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="admin-btn admin-btn-edit">EDIT</a>
                <button
                  class="admin-btn admin-btn-delete"
                  onclick="openDeleteModal('<?= $user['id'] ?>', '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')"
                >DELETE</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="7" class="admin-empty-row">No users found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

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