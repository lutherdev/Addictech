<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Messages</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/admin_contacts.css') ?>" />
</head>
<body>

  <div class="contacts-page">

    <!-- Main Content Area -->
    <div class="contacts-main">

      <!-- Title Row -->
      <div class="contacts-title-row">
        <h1 class="page-title">MESSAGES</h1>
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

      <!-- Messages Table -->
      <div class="contacts-table-wrapper">
        <table class="contacts-table">
          <thead>
            <tr>
              <th class="col-id">#</th>
              <th class="col-name">NAME</th>
              <th class="col-email">EMAIL</th>
              <th class="col-message">MESSAGE</th>
              <th class="col-date">DATE</th>
              <th class="col-status">STATUS</th>
              <th class="col-action">ACTION</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($contacts) && is_array($contacts)): ?>
              <?php foreach ($contacts as $contact): ?>
                <tr class="<?= $contact['is_read'] ? '' : 'unread-row' ?>">
                  <td class="col-id contact-id">#<?= esc($contact['id']) ?></td>
                  <td class="col-name contact-name"><?= esc($contact['full_name']) ?></td>
                  <td class="col-email contact-email">
                    <a href="mailto:<?= esc($contact['email']) ?>">
                      <?= esc($contact['email']) ?>
                    </a>
                  </td>
                  <td class="col-message message-cell">
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
                  <td class="col-date contact-date">
                    <?= date('M d, Y', strtotime($contact['created_at'])) ?>
                    <br>
                    <small><?= date('h:i A', strtotime($contact['created_at'])) ?></small>
                  </td>
                  <td class="col-status">
                    <?php if ($contact['is_read']) : ?>
                      <span class="contact-status read">READ</span>
                    <?php else : ?>
                      <span class="contact-status unread">UNREAD</span>
                    <?php endif; ?>
                  </td>
                  <td class="col-action">
                    <div class="contact-actions">
                      <?php if (!$contact['is_read']) : ?>
                        <a href="<?= base_url('admin/contacts/read/' . $contact['id']) ?>" 
                           class="btn-action">MARK READ</a>
                      <?php endif; ?>
                      <a href="<?= base_url('admin/contacts/delete/' . $contact['id']) ?>" 
                         class="btn-action btn-delete"
                         onclick="return confirm('Delete this message?')">DELETE</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty-row">No messages found</td>
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
          <li><a href="<?= base_url('admin/products') ?>">PRODUCTS</a></li>
          <li><a href="<?= base_url('admin/orders') ?>">ORDERS</a></li>
          <li><a href="<?= base_url('admin/users') ?>">USERS</a></li>
          <li><a href="<?= base_url('admin/contacts') ?>" class="active">MESSAGES</a></li>
          <li><a href="<?= base_url('logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>

  </div>

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

</body>
<?= $this->endSection() ?>