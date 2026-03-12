<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<body>

  <?php
  // Check if user is logged in
  if (!isset($_SESSION['user_id'])) {
      header('Location: login.php');
      exit();
  }
  
  // Get cart count for badge
  $cart_count = 0;
  if (isset($_SESSION['cart'])) {
      $cart_count = array_sum($_SESSION['cart']);
  }
  ?>
  <!-- ACCOUNT BODY -->
  <main class="account-main">
    <div class="account-top">
      <aside class="account-sidebar">
        <div class="sidebar-member">
          <p class="sidebar-label">MEMBER SINCE</p>
          <p class="sidebar-date" id="memberSince"><?php echo date('M Y', strtotime($user['created_at'])); ?></p>
        </div>
        <hr class="sidebar-rule"/>
        <nav class="sidebar-nav">
          <a href="#" class="sidebar-link" id="linkPassword">PASSWORD</a>
          <a href="logout.php" class="sidebar-link" id="linkLogout">LOGOUT</a>
        </nav>
      </aside>
      <div class="account-vdivider"></div>
      <div class="account-info">
        <h1 class="account-greeting" id="greeting">HI <?php echo strtoupper($user['username'] ?: explode('@', $user['email'])[0]); ?></h1>

        <div class="info-grid">
          <div class="info-row">
            <span class="info-key">USER'S ID:</span>
            <span class="info-val" id="infoId"><?php echo $user['user_id']; ?></span>
          </div>
          <div class="info-row">
            <span class="info-key">EMAIL:</span>
            <span class="info-val" id="infoEmail"><?php echo strtoupper($user['email']); ?></span>
          </div>
          <div class="info-row">
            <span class="info-key">NUMBER:</span>
            <span class="info-val" id="infoNumber"><?php echo '123213'; ?></span>
          </div>
          <div class="info-row">
            <span class="info-key">ADDRESS:</span>
            <span class="info-val" id="infoAddress"><?php echo  'BRTGY STAORSA'; ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="account-tabs">
      <button class="tab-btn active" id="tabHistory" onclick="switchTab('history')">ORDER HISTORY</button>
      <button class="tab-btn" id="tabSettings" onclick="switchTab('settings')">ACCOUNT SETTINGS</button>
    </div>

    <div class="tab-panel" id="panelHistory">
      <div class="orders-toolbar">
        <div class="order-filters" id="orderFilters">
          <button class="order-filter active" data-filter="all" onclick="filterOrders(this)">ALL ORDERS <span class="filter-count" id="fcAll">(<?php echo "NUMBER HERE" ?>)</span></button>
          <?php
          // $pending_count = count(array_filter($orders, function($o) { return $o['status'] == 'pending'; }));
          // $completed_count = count(array_filter($orders, function($o) { return $o['status'] == 'completed'; }));
          // $cancelled_count = count(array_filter($orders, function($o) { return $o['status'] == 'cancelled'; }));
          ?>
          <button class="order-filter" data-filter="pending" onclick="filterOrders(this)">PENDING <span class="filter-count" id="fcPending">(<?php echo "NUMBER HERE" ?>)</span></button>
          <button class="order-filter" data-filter="completed" onclick="filterOrders(this)">COMPLETED <span class="filter-count" id="fcCompleted">(<?php "NUMBER HERE" ?>)</span></button>
          <button class="order-filter" data-filter="cancelled" onclick="filterOrders(this)">CANCELLED <span class="filter-count" id="fcCancelled">(<?php echo "NUMBER HERE"?>)</span></button>
        </div>
        <div class="order-search-wrap">
          <input type="text" class="order-search" placeholder="SEARCH" id="orderSearch" onkeyup="renderOrders()"/>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
      </div>

      <div class="orders-table">
        <div class="orders-thead">
          <span>PRODUCT NAME</span>
          <span>PAYMENT</span>
          <span>STATUS</span>
          <span>TOTAL</span>
        </div>
        <div class="orders-tbody" id="ordersBody">
          <?php if (empty($orders)): ?>
          <div class="orders-empty">You have no orders yet. <a href="catalog.php" style="color:var(--text);text-decoration:underline">Start shopping →</a></div>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <div class="orders-row" data-status="<?php echo $order['status']; ?>" data-name="<?php echo strtolower($order['product_name']); ?>" data-payment="<?php echo strtolower($order['payment_method']); ?>">
              <span class="order-name"><?php echo $order['product_name']; ?><br><small style="font-weight:300;color:var(--text-muted);font-size:0.68rem"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></small></span>
              <span class="order-payment"><?php echo strtoupper($order['payment_method']); ?></span>
              <span class="order-status status-<?php echo $order['status']; ?>"><?php echo strtoupper($order['status']); ?></span>
              <span class="order-total">₱<?php echo number_format($order['total'], 2); ?></span>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="tab-panel hidden" id="panelSettings">
      <div class="settings-header">
        <p class="settings-title">SETTINGS</p>
        <p class="settings-sub">KEEP YOUR ACCOUNT DETAILS UP TO DATE.</p>
        <hr class="settings-rule"/>
      </div>

      <div class="settings-card">
        <div class="settings-card-head">
          <span class="settings-card-title">PROFILE</span>
          <button class="settings-edit-btn" id="editProfileBtn" onclick="toggleEditProfile()">
            EDIT
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
        </div>

        <div id="profileView">
          <div class="profile-grid">
            <div class="profile-field">
              <p class="profile-field-label">FIRST NAME</p>
              <p class="profile-field-val" id="viewFirstName"><?php echo $user['username'] ?: '—'; ?></p>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">LAST NAME</p>
              <p class="profile-field-val" id="viewLastName"><?php echo $user['username'] ?: '—'; ?></p>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">COUNTRY</p>
              <p class="profile-field-val" id="viewCountry"><?php echo $user['username'] ?: 'Philippines'; ?></p>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">LANGUAGE</p>
              <p class="profile-field-val" id="viewLanguage"><?php echo $user['username'] ?: 'English'; ?></p>
            </div>
          </div>
          <div class="profile-address-row">
            <p class="profile-field-label">ADDRESS</p>
            <p class="profile-field-val" id="viewAddressFull"><?php echo $user['username'] ?: '—'; ?></p>
          </div>
        </div>

        <!-- EDIT MODE -->
        <!-- <div id="profileEdit" class="hidden">
          <form id="profileForm" method="POST" action="update_profile.php">
            <div class="profile-grid">
              <div class="profile-field">
                <p class="profile-field-label">FIRST NAME</p>
                <input class="settings-input" id="editFirstName" name="first_name" type="text" value="<?php// echo htmlspecialchars($user['first_name']); ?>"/>
              </div>
              <div class="profile-field">
                <p class="profile-field-label">LAST NAME</p>
                <input class="settings-input" id="editLastName" name="last_name" type="text" value="<?php// echo htmlspecialchars($user['last_name']); ?>"/>
              </div>
              <div class="profile-field">
                <p class="profile-field-label">COUNTRY</p>
                <input class="settings-input" id="editCountry" name="country" type="text" value="<?php// echo htmlspecialchars($user['country'] ?: 'Philippines'); ?>"/>
              </div>
              <div class="profile-field">
                <p class="profile-field-label">LANGUAGE</p>
                <input class="settings-input" id="editLanguage" name="language" type="text" value="<?php//echo htmlspecialchars($user['language'] ?: 'English'); ?>"/>
              </div>
            </div>
            <div class="profile-address-row">
              <p class="profile-field-label">ADDRESS</p>
              <input class="settings-input" id="editAddressFull" name="address" type="text" style="width:100%" value="<?php// echo htmlspecialchars($user['address']); ?>"/>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">PHONE NUMBER</p>
              <input class="settings-input" id="editPhone" name="phone" type="tel" value="<?php// echo htmlspecialchars($user['phone']); ?>"/>
            </div>
            <div class="edit-actions">
              <button type="submit" class="save-btn">SAVE CHANGES</button>
              <button type="button" class="cancel-btn" onclick="cancelEdit()">CANCEL</button>
            </div> -->
          </form>
        </div>
      </div>

      <!-- CHANGE PASSWORD CARD -->
      <div class="settings-card" id="passwordCard" style="display:none">
        <div class="settings-card-head">
          <span class="settings-card-title">CHANGE PASSWORD</span>
        </div>
        <form id="passwordForm" method="POST" action="change_password.php">
          <div class="profile-grid" style="grid-template-columns:1fr 1fr">
            <div class="profile-field">
              <p class="profile-field-label">NEW PASSWORD</p>
              <input class="settings-input" id="newPass" name="new_password" type="password" required/>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">CONFIRM PASSWORD</p>
              <input class="settings-input" id="confirmPass" name="confirm_password" type="password" required/>
            </div>
          </div>
          <div class="edit-actions">
            <button type="submit" class="save-btn">UPDATE PASSWORD</button>
          </div>
        </form>
        <?php if (isset($_SESSION['password_message'])): ?>
        <p class="pass-msg" style="color: <?php echo $_SESSION['password_message_type'] == 'success' ? '#4caf7d' : '#c0392b'; ?>">
          <?php 
          echo $_SESSION['password_message'];
          unset($_SESSION['password_message']);
          unset($_SESSION['password_message_type']);
          ?>
        </p>
        <?php endif; ?>
      </div>

    </div>
  </main>
  <div class="acct-toast" id="acctToast"></div>

  <?php if (isset($_SESSION['profile_updated'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showToast('Profile updated successfully!');
    });
  </script>
  <?php 
  unset($_SESSION['profile_updated']);
  endif; 
  ?>

  <script>
    /* ──────────────────────────────────────
       JAVASCRIPT FUNCTIONS (mostly for UI)
    ────────────────────────────────────── */
    
    // Get PHP data for JavaScript
    // const ordersData = <?php //echo json_encode($orders); ?>;
    // const currentUser = <?php //echo json_encode($user); ?>;

    /* ──────────────────────────────────────
       TABS
    ────────────────────────────────────── */
    function switchTab(tab) {
      document.getElementById('panelHistory').classList.toggle('hidden', tab !== 'history');
      document.getElementById('panelSettings').classList.toggle('hidden', tab !== 'settings');
      document.getElementById('tabHistory').classList.toggle('active', tab === 'history');
      document.getElementById('tabSettings').classList.toggle('active', tab === 'settings');
    }

    /* ──────────────────────────────────────
       ORDER FILTERING
    ────────────────────────────────────── */
    let orderFilter = 'all';

    function filterOrders(btn) {
      document.querySelectorAll('.order-filter').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      orderFilter = btn.dataset.filter;
      renderOrders();
    }

    function renderOrders() {
      const searchTerm = (document.getElementById('orderSearch').value || '').toLowerCase();
      const rows = document.querySelectorAll('.orders-row');
      let visibleCount = 0;

      rows.forEach(row => {
        const status = row.dataset.status;
        const name = row.dataset.name;
        const payment = row.dataset.payment;
        
        const matchesFilter = orderFilter === 'all' || status === orderFilter;
        const matchesSearch = name.includes(searchTerm) || payment.includes(searchTerm);
        
        if (matchesFilter && matchesSearch) {
          row.style.display = 'grid';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Show empty message if no rows visible
      const tbody = document.getElementById('ordersBody');
      if (visibleCount === 0 && rows.length > 0) {
        // Check if empty message already exists
        if (!document.querySelector('.orders-empty-dynamic')) {
          const emptyMsg = document.createElement('div');
          emptyMsg.className = 'orders-empty orders-empty-dynamic';
          emptyMsg.textContent = 'No orders match your filter.';
          tbody.appendChild(emptyMsg);
        }
      } else {
        const emptyMsg = document.querySelector('.orders-empty-dynamic');
        if (emptyMsg) emptyMsg.remove();
      }
    }

    /* ──────────────────────────────────────
       PROFILE EDIT TOGGLE
    ────────────────────────────────────── */
    function toggleEditProfile() {
      document.getElementById('profileView').classList.toggle('hidden');
      document.getElementById('profileEdit').classList.toggle('hidden');
    }

    function cancelEdit() {
      document.getElementById('profileView').classList.remove('hidden');
      document.getElementById('profileEdit').classList.add('hidden');
    }

    /* ──────────────────────────────────────
       PASSWORD CARD
    ────────────────────────────────────── */
    document.getElementById('linkPassword')?.addEventListener('click', e => {
      e.preventDefault();
      switchTab('settings');
      document.getElementById('passwordCard').style.display = 'block';
      document.getElementById('passwordCard').scrollIntoView({ behavior: 'smooth' });
    });

    /* ──────────────────────────────────────
       TOAST
    ────────────────────────────────────── */
    function showToast(msg) {
      const t = document.getElementById('acctToast');
      t.textContent = msg;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2200);
    }

    /* ── INIT ── */
    renderOrders();
  </script>

</body>
</html>
<?= $this->endSection() ?>