<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Edit User</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_editUsers.css') ?>" />
</head>
<body>

  <div class="edit-user-page">

    <!-- Main Content Area -->
    <div class="edit-user-main">

      <!-- Title Row -->
      <div class="edit-user-title-row">
        <h1 class="edit-user-page-title">EDIT USER</h1>
        <div class="edit-user-title-actions">
          <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="edit-user-btn-outline">← BACK</a>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="edit-user-flash edit-user-flash-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
        <div class="edit-user-flash edit-user-flash-error"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <!-- Edit User Card -->
      <div class="edit-user-card">

        <!-- Left: User Summary -->
        <div class="edit-user-summary">

          <div class="eu-detail-group">
            <span class="eu-detail-label">USER ID</span>
            <span class="eu-detail-value eu-id-val">#<?= esc($user['id']) ?></span>
          </div>

          <div class="eu-detail-group">
            <span class="eu-detail-label">FULL NAME</span>
            <span class="eu-detail-value"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></span>
          </div>

          <div class="eu-detail-group">
            <span class="eu-detail-label">EMAIL</span>
            <span class="eu-detail-value"><?= esc($user['email'] ?? '—') ?></span>
          </div>

          <div class="eu-detail-group">
            <span class="eu-detail-label">CURRENT STATUS</span>
            <span class="eu-status-badge eu-status-<?= esc(strtolower($user['status'] ?? 'active')) ?>">
              <?= strtoupper(esc($user['status'] ?? 'ACTIVE')) ?>
            </span>
          </div>

          <div class="eu-detail-group">
            <span class="eu-detail-label">MEMBER SINCE</span>
            <span class="eu-detail-value eu-detail-date">
              <?= !empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '—' ?>
            </span>
          </div>

        </div>

        <!-- Divider -->
        <div class="edit-user-divider"></div>

        <!-- Right: Edit Form -->
        <div class="edit-user-form-col">
          <form action="<?= base_url('users/update/' . $user['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect_to" value="admin/users/view/<?= $user['id'] ?>">

            <div class="eu-form-group">
              <label for="first_name">FIRST NAME</label>
              <input type="text" id="first_name" name="first_name"
                     value="<?= old('first_name', esc($user['first_name'] ?? '')) ?>" />
            </div>

            <div class="eu-form-group">
              <label for="last_name">LAST NAME</label>
              <input type="text" id="last_name" name="last_name"
                     value="<?= old('last_name', esc($user['last_name'] ?? '')) ?>" />
            </div>

            <div class="eu-form-group">
              <label for="phone">PHONE</label>
              <input type="text" id="phone" name="phone"
                     value="<?= old('phone', esc($user['phone'] ?? '')) ?>" />
            </div>

            <div class="eu-form-group">
              <label for="role">ROLE</label>
              <select id="role" name="role">
                <option value="user"  <?= old('role', $user['role']) === 'user'  ? 'selected' : '' ?>>USER</option>
                <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>ADMIN</option>
              </select>
            </div>

            <div class="eu-form-group">
              <label for="status">STATUS</label>
              <select id="status" name="status">
                <option value="active"   <?= old('status', $user['status']) === 'active'   ? 'selected' : '' ?>>ACTIVE</option>
                <option value="inactive" <?= old('status', $user['status']) === 'inactive' ? 'selected' : '' ?>>INACTIVE</option>
                <option value="banned"   <?= old('status', $user['status']) === 'banned'   ? 'selected' : '' ?>>BANNED</option>
              </select>
            </div>

            <div class="eu-form-group">
              <label for="address">ADDRESS</label>
              <input type="text" id="address" name="address"
                     value="<?= old('address', esc($user['address'] ?? '')) ?>" />
            </div>

            <div class="eu-form-group">
              <label for="city">CITY</label>
              <input type="text" id="city" name="city"
                     value="<?= old('city', esc($user['city'] ?? '')) ?>" />
            </div>

            <div class="eu-form-row">
              <div class="eu-form-group">
                <label for="postal_code">POSTAL CODE</label>
                <input type="text" id="postal_code" name="postal_code"
                       value="<?= old('postal_code', esc($user['postal_code'] ?? '')) ?>" />
              </div>
              <div class="eu-form-group">
                <label for="country">COUNTRY</label>
                <input type="text" id="country" name="country"
                       value="<?= old('country', esc($user['country'] ?? 'Philippines')) ?>" />
              </div>
            </div>

            <div class="eu-form-actions">
              <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="eu-btn-cancel">CANCEL</a>
              <button type="submit" class="eu-btn-submit">SAVE CHANGES</button>
            </div>

          </form>
        </div>

      </div>
    </div>

    <!-- Right Sidebar Navigation -->
    <aside class="edit-user-sidebar">
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

</body>
<?= $this->endSection() ?>