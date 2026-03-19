<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Admin: Messages</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_users.css') ?>" />
</head>

<nav class="admin-nav">
  <a href="<?= base_url('admin/products') ?>">PRODUCTS</a>
  <a href="<?= base_url('admin/orders') ?>">ORDERS</a>
  <a href="<?= base_url('admin/users') ?>">USERS</a>
  <a href="<?= base_url('admin/contacts') ?>" class="active">MESSAGES</a>
  <a href="<?= base_url('auth/logout') ?>">LOGOUT</a>
</nav>

<div class="admin-page-wrapper">

  <h1 class="admin-page-title">MESSAGES</h1>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="admin-alert admin-alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <div class="admin-table-wrapper">
    <table class="admin-users-table">
      <thead>
        <tr>
          <th>#</th>
          <th>NAME</th>
          <th>EMAIL</th>
          <th>MESSAGE</th>
          <th>DATE</th>
          <th>STATUS</th>
          <th>ACTION</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($contacts)) : ?>
          <?php foreach ($contacts as $contact) : ?>
            <tr class="<?= $contact['is_read'] ? '' : 'unread-row' ?>">
              <td class="admin-id-cell">#<?= esc($contact['id']) ?></td>
              <td><?= esc($contact['full_name']) ?></td>
              <td>
                <a href="mailto:<?= esc($contact['email']) ?>" style="color:var(--text);text-decoration:underline">
                  <?= esc($contact['email']) ?>
                </a>
              </td>
              <td class="message-cell">
                <div class="message-preview" id="msg-<?= $contact['id'] ?>">
                  <span class="message-short">
                    <?= esc(mb_strimwidth($contact['concern'], 0, 80, '...')) ?>
                  </span>
                  <?php if (mb_strlen($contact['concern']) > 80) : ?>
                    <span class="message-full" style="display:none">
                      <?= nl2br(esc($contact['concern'])) ?>
                    </span>
                    <button type="button" class="btn-expand"
                            onclick="toggleMessage(<?= $contact['id'] ?>, this)">
                      READ MORE
                    </button>
                  <?php else : ?>
                    <span class="message-full" style="display:none">
                      <?= nl2br(esc($contact['concern'])) ?>
                    </span>
                  <?php endif; ?>
                </div>
              </td>
              <td style="white-space:nowrap">
                <?= date('M d, Y', strtotime($contact['created_at'])) ?>
                <br>
                <small style="color:var(--text-muted);font-size:0.7rem">
                  <?= date('h:i A', strtotime($contact['created_at'])) ?>
                </small>
              </td>
              <td>
                <?php if ($contact['is_read']) : ?>
                  <span class="admin-status admin-status-active">READ</span>
                <?php else : ?>
                  <span class="admin-status admin-status-inactive">UNREAD</span>
                <?php endif; ?>
              </td>
              <td class="admin-action-cell">
                <?php if (!$contact['is_read']) : ?>
                  <a href="<?= base_url('admin/contacts/read/' . $contact['id']) ?>"
                     class="admin-btn admin-btn-view">
                    MARK READ
                  </a>
                <?php endif; ?>
                <a href="<?= base_url('admin/contacts/delete/' . $contact['id']) ?>"
                   class="admin-btn admin-btn-delete"
                   onclick="return confirm('Delete this message?')">
                  DELETE
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="7" class="admin-empty-row">No messages yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<style>
  .unread-row td {
    font-weight: 500;
  }
  .message-cell {
    max-width: 300px;
    font-size: 0.82rem;
    line-height: 1.5;
  }
  .btn-expand {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 0.7rem;
    letter-spacing: 0.05em;
    cursor: pointer;
    padding: 0;
    margin-top: 4px;
    display: block;
    text-decoration: underline;
  }
  .btn-expand:hover {
    color: var(--text);
  }
</style>

<script>
  function toggleMessage(id, btn) {
    const container = document.getElementById('msg-' + id);
    const shortEl   = container.querySelector('.message-short');
    const fullEl    = container.querySelector('.message-full');
    const isExpanded = fullEl.style.display !== 'none';

    if (isExpanded) {
      fullEl.style.display  = 'none';
      shortEl.style.display = '';
      btn.textContent = 'READ MORE';
    } else {
      fullEl.style.display  = '';
      shortEl.style.display = 'none';
      btn.textContent = 'SHOW LESS';
    }
  }
</script>

<?= $this->endSection() ?>