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
                <tr class="<?= $contact['is_read'] ? '' : 'unread-row' ?>" 
                    data-contact-id="<?= $contact['id'] ?>"
                    data-contact-name="<?= esc($contact['full_name']) ?>"
                    data-contact-email="<?= esc($contact['email']) ?>"
                    data-contact-message="<?= esc(str_replace(["\r\n", "\n", "\r"], ' ', $contact['concern'])) ?>"
                    data-contact-date="<?= date('F d, Y h:i A', strtotime($contact['created_at'])) ?>">
                  <td class="col-id contact-id">#<?= esc($contact['id']) ?></td>
                  <td class="col-name contact-name"><?= esc($contact['full_name']) ?></td>
                  <td class="col-email contact-email">
                    <a href="mailto:<?= esc($contact['email']) ?>">
                      <?= esc($contact['email']) ?>
                    </a>
                  </td>
                  <td class="col-message message-cell">
                    <button type="button" class="btn-view-message" 
                            onclick="openMessageModal(this)">
                      VIEW MESSAGE
                    </button>
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

  <!-- Modal Popup -->
  <div id="messageModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Message Details</h2>
        <span class="modal-close">&times;</span>
      </div>
      <div class="modal-body">
        <div class="message-detail">
          <div class="detail-row">
            <strong>From:</strong>
            <span id="modal-name"></span>
          </div>
          <div class="detail-row">
            <strong>Email:</strong>
            <span id="modal-email"></span>
          </div>
          <div class="detail-row">
            <strong>Date:</strong>
            <span id="modal-date"></span>
          </div>
          <div class="detail-row">
            <strong>Message:</strong>
            <div id="modal-message" class="message-content"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn modal-btn-close">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Modal functionality
    const modal = document.getElementById('messageModal');
    const modalName = document.getElementById('modal-name');
    const modalEmail = document.getElementById('modal-email');
    const modalDate = document.getElementById('modal-date');
    const modalMessage = document.getElementById('modal-message');

    // Function to open modal with message details
    function openMessageModal(button) {
      // Get the parent row
      const row = button.closest('tr');
      
      // Get data from data attributes
      const name = row.getAttribute('data-contact-name');
      const email = row.getAttribute('data-contact-email');
      const date = row.getAttribute('data-contact-date');
      const message = row.getAttribute('data-contact-message');
      
      // Set modal content
      modalName.textContent = name;
      modalEmail.textContent = email;
      modalDate.textContent = date;
      // Preserve line breaks in message
      modalMessage.innerHTML = message.replace(/\n/g, '<br>').replace(/\r/g, '');
      
      // Show modal
      modal.style.display = 'block';
      document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
    }

    // Function to close modal
    function closeModal() {
      modal.style.display = 'none';
      document.body.style.overflow = ''; // Restore scrolling
    }

    // Close modal when clicking ×
    document.querySelector('.modal-close').addEventListener('click', closeModal);
    
    // Close modal when clicking Close button
    document.querySelector('.modal-btn-close').addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
      if (event.target === modal) {
        closeModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && modal.style.display === 'block') {
        closeModal();
      }
    });
  </script>

</body>
<?= $this->endSection() ?>