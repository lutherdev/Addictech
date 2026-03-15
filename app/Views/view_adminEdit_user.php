<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Admin: Edit User</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_editUsers.css') ?>" />
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
    <h1 class="admin-page-title">EDIT USER</h1>
  </div>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="admin-alert admin-alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')) : ?>
    <div class="admin-alert admin-alert-error"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <div class="admin-detail-card">

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

      </div>
    </div>

    <div class="admin-detail-divider"></div>

    <form method="POST" action="<?= base_url('admin/users/update/' . $user['id']) ?>">
      <?= csrf_field() ?>

      <div class="admin-form-grid">

        <!-- Personal Info -->
        <div class="admin-form-section">
          <p class="admin-detail-section-label">PERSONAL INFO</p>

          <div class="admin-field-group">
            <label class="admin-field-label">FIRST NAME</label>
            <input type="text" name="first_name" class="admin-field-input"
              value="<?= esc($user['first_name'] ?? '') ?>" required />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">LAST NAME</label>
            <input type="text" name="last_name" class="admin-field-input"
              value="<?= esc($user['last_name'] ?? '') ?>" required />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">EMAIL</label>
            <input type="email" name="email" class="admin-field-input"
              value="<?= esc($user['email'] ?? '') ?>" required />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">PHONE</label>
            <input type="text" name="phone" class="admin-field-input"
              value="<?= esc($user['phone'] ?? '') ?>" />
          </div>
        </div>

        <!-- Role & Status -->
        <div class="admin-form-section">
          <p class="admin-detail-section-label">ACCOUNT SETTINGS</p>

          <div class="admin-field-group">
            <label class="admin-field-label">ROLE</label>
            <select name="role" class="admin-field-input">
              <option value="user"  <?= ($user['role'] ?? '') === 'user'  ? 'selected' : '' ?>>User</option>
              <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">STATUS</label>
            <select name="status" class="admin-field-input">
              <option value="ACTIVE"   <?= strtoupper($user['status'] ?? '') === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
              <option value="INACTIVE" <?= strtoupper($user['status'] ?? '') === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
              <option value="BANNED"   <?= strtoupper($user['status'] ?? '') === 'BANNED'   ? 'selected' : '' ?>>Banned</option>
            </select>
          </div>

          <p class="admin-detail-section-label" style="margin-top:1.5rem;">ADDRESS</p>

          <div class="admin-field-group">
            <label class="admin-field-label">ADDRESS LINE</label>
            <input type="text" name="address" class="admin-field-input"
              value="<?= esc($user['address'] ?? '') ?>" />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">CITY</label>
            <input type="text" name="city" class="admin-field-input"
              value="<?= esc($user['city'] ?? '') ?>" />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">POSTAL CODE</label>
            <input type="text" name="postal_code" class="admin-field-input"
              value="<?= esc($user['postal_code'] ?? '') ?>" />
          </div>
          <div class="admin-field-group">
            <label class="admin-field-label">COUNTRY</label>
            <input type="text" name="country" class="admin-field-input"
              value="<?= esc($user['country'] ?? '') ?>" />
          </div>
          <input type="hidden" name="redirect_to" value="admin/users">
        </div>

      </div>

      <!-- Form Actions -->
      <div class="admin-detail-divider"></div>
      <div class="admin-form-actions">
        <a href="<?= base_url('admin/users') ?>" class="admin-btn admin-btn-view">CANCEL</a>
        <button type="submit" class="admin-btn admin-btn-save">SAVE CHANGES</button>
      </div>

    </form>
  </div>
</div>

</body>
<?= $this->endSection() ?>