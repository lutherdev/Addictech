<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – User Profile</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/user_profile.css') ?>" />
</head>

<?php
$orders          = $orders ?? [];
$total_count     = count($orders);
$pending_count   = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
$completed_count = count(array_filter($orders, fn($o) => $o['status'] === 'delivered'));
$cancelled_count = count(array_filter($orders, fn($o) => $o['status'] === 'cancelled'));
?>

<main class="account-main">

  <?php if (session()->getFlashdata('error')) : ?>
    <div class="flash flash-error">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')) : ?>
    <div class="flash flash-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <div class="account-top">
    <aside class="account-sidebar">
      <div class="sidebar-member">
        <p class="sidebar-label">MEMBER SINCE</p>
        <p class="sidebar-date"><?= date('M Y', strtotime($user['created_at'])) ?></p>
      </div>
      <hr class="sidebar-rule"/>
      <nav class="sidebar-nav">
        <a href="#" class="sidebar-link" id="linkPassword">PASSWORD</a>
        <a href="<?= base_url('auth/logout') ?>" class="sidebar-link">LOGOUT</a>
      </nav>
    </aside>

    <div class="account-vdivider"></div>

    <div class="account-info">
      <h1 class="account-greeting">
        WASSUP <?= strtoupper($user['first_name'] ?: explode('@', $user['email'])[0]) ?>!
      </h1>

      <div class="info-grid">
        <div class="info-row">
          <span class="info-key">USER'S ID:</span>
          <span class="info-val"><?= esc($user['user_id'] ?? '—') ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">EMAIL:</span>
          <span class="info-val"><?= strtoupper(esc($user['email'] ?? '—')) ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">NUMBER:</span>
          <span class="info-val"><?= strtoupper(esc($user['phone'] ?? '—')) ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">ADDRESS:</span>
          <span class="info-val">
            <?php
              $parts = array_filter([
                $user['address']     ?? '',
                $user['city']        ?? '',
                $user['postal_code'] ?? '',
                $user['country']     ?? '',
              ]);
              echo !empty($parts) ? esc(implode(', ', $parts)) : '—';
            ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="account-tabs">
    <button class="tab-btn active" id="tabHistory" onclick="switchTab('history')">ORDER HISTORY</button>
    <button class="tab-btn" id="tabSettings" onclick="switchTab('settings')">ACCOUNT SETTINGS</button>
  </div>

<!-- ORDER HISTORY TAB -->
  <div class="tab-panel" id="panelHistory">
    <div class="orders-toolbar">
      <div class="order-filters" id="orderFilters">
        <button class="order-filter active" data-filter="all" onclick="filterOrders(this)">
          ALL ORDERS <span class="filter-count">(<?= $total_count ?>)</span>
        </button>
        <button class="order-filter" data-filter="pending" onclick="filterOrders(this)">
          PENDING <span class="filter-count">(<?= $pending_count ?>)</span>
        </button>
        <button class="order-filter" data-filter="delivered" onclick="filterOrders(this)">
          COMPLETED <span class="filter-count">(<?= $completed_count ?>)</span>
        </button>
        <button class="order-filter" data-filter="cancelled" onclick="filterOrders(this)">
          CANCELLED <span class="filter-count">(<?= $cancelled_count ?>)</span>
        </button>
      </div>
      <div class="order-search-wrap">
        <input type="text" class="order-search" placeholder="SEARCH" id="orderSearch" onkeyup="renderOrders()"/>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </div>
    </div>

    <div class="orders-table">
      <div class="orders-thead">
        <span>ORDER</span>
        <span>PAYMENT</span>
        <span>STATUS</span>
        <span>TOTAL</span>
      </div>
      <div class="orders-tbody" id="ordersBody">
        <?php if (empty($orders)) : ?>
          <div class="orders-empty">
            You have no orders yet.
            <a href="<?= base_url('catalog') ?>" style="color:var(--text);text-decoration:underline">
              Start shopping →
            </a>
          </div>
        <?php else : ?>
          <?php foreach ($orders as $i => $order) : ?>
            <?php
              $firstItem   = !empty($order['items']) ? $order['items'][0] : null;
              $displayName = esc($order['order_number']);
              $itemCount   = count($order['items'] ?? []);
              $extraCount  = $itemCount - 1;
            ?>

            <!-- Summary Row (clickable) -->
            <div class="orders-row order-row"
                 data-status="<?= esc($order['status']) ?>"
                 data-name="<?= strtolower($displayName) ?>"
                 data-payment="<?= strtolower(esc($order['payment_method'] ?? '')) ?>"
                 onclick="toggleDrop(<?= $i ?>)">

              <span class="order-name">
                <?= $displayName ?>
                <?php if ($firstItem && !empty($firstItem['variant'])) : ?>
                  <small style="color:var(--text-muted)"> — <?= esc($firstItem['product_name']) ?></small>
                <?php endif; ?>
                <?php if ($extraCount > 0) : ?>
                  <small class="order-extra-count">+<?= $extraCount ?> more item<?= $extraCount > 1 ? 's' : '' ?></small>
                <?php endif; ?>
                <br>
                <small style="font-weight:300;color:var(--text-muted);font-size:0.68rem">
                  <?= date('M d, Y', strtotime($order['created_at'])) ?>
                </small>
              </span>

              <span class="order-payment"><?= strtoupper(esc($order['payment_method'] ?? '—')) ?></span>

              <span class="order-status status-<?= esc($order['status']) ?>">
                <?= strtoupper(esc($order['status'])) ?>
              </span>

              <span class="order-total" style="display:flex;align-items:center;justify-content:space-between;">
                ₱<?= number_format($order['total_price'] ?? $order['total'] ?? 0, 2) ?>
                <svg class="drop-chevron" id="chevron-<?= $i ?>"
                     width="12" height="12" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     style="transition:transform 0.25s;flex-shrink:0;margin-left:0.5rem">
                  <polyline points="6 9 12 15 18 9"/>
                </svg>
              </span>
            </div>

            <!-- Expanded Detail Panel -->
            <div class="order-drop" id="accordion-<?= $i ?>">
              <div class="drop-inner">

                <div class="drop-head">
                  <span>PRODUCT</span>
                  <span>VARIANT</span>
                  <span>QTY</span>
                  <span>UNIT PRICE</span>
                  <span>SUBTOTAL</span>
                </div>

                <?php if (!empty($order['items'])) : ?>
                  <?php foreach ($order['items'] as $item) : ?>
                    <div class="drop-row">
                      <span class="drop-name"><?= esc($item['product_name'] ?? '—') ?></span>
                      <span class="drop-variant"><?= esc($item['variant'] ?? '—') ?></span>
                      <span class="drop-qty"><?= esc($item['quantity']) ?></span>
                      <span class="drop-price">₱<?= number_format($item['price'], 2) ?></span>
                      <span class="drop-subtotal">₱<?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                  <?php endforeach; ?>
                <?php else : ?>
                  <div class="drop-row">
                    <span style="color:var(--text-muted);font-size:0.75rem">No items found.</span>
                  </div>
                <?php endif; ?>

                <div class="drop-totals">
                  <?php if (!empty($order['shipping_fee'])) : ?>
                    <div class="drop-total">
                      <span>SHIPPING</span>
                      <span>₱<?= number_format($order['shipping_fee'], 2) ?></span>
                    </div>
                  <?php endif; ?>
                  <div class="drop-total drop-grand">
                    <span>TOTAL</span>
                    <span>₱<?= number_format($order['total_price'] ?? $order['total'] ?? 0, 2) ?></span>
                  </div>
                </div>

              </div>
            </div>

          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ACCOUNT SETTINGS TAB -->
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
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
        </button>
      </div>

      <!-- VIEW MODE -->
      <div id="profileView">
        <div class="profile-grid">
          <div class="profile-field">
            <p class="profile-field-label">FIRST NAME</p>
            <p class="profile-field-val"><?= esc($user['first_name'] ?: '—') ?></p>
          </div>
          <div class="profile-field">
            <p class="profile-field-label">LAST NAME</p>
            <p class="profile-field-val"><?= esc($user['last_name'] ?: '—') ?></p>
          </div>
          <div class="profile-field">
            <p class="profile-field-label">COUNTRY</p>
            <p class="profile-field-val"><?= esc($user['country'] ?: '—') ?></p>
          </div>
          <div class="profile-field">
            <p class="profile-field-label">POSTAL CODE</p>
            <p class="profile-field-val"><?= esc($user['postal_code'] ?: '—') ?></p>
          </div>
        </div>
        <div class="profile-address-row">
          <p class="profile-field-label">ADDRESS</p>
          <p class="profile-field-val">
            <?php
              $parts = array_filter([
                $user['address']     ?? '',
                $user['city']        ?? '',
                $user['postal_code'] ?? '',
                $user['country']     ?? '',
              ]);
              echo !empty($parts) ? esc(implode(', ', $parts)) : '—';
            ?>
          </p>
        </div>
      </div>

      <!-- EDIT MODE -->
      <div id="profileEdit" class="hidden">
        <form method="POST" action="<?= base_url('users/update/' . $user['user_id']) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="redirect_to" value="user/profile">

          <div class="profile-grid">
            <div class="profile-field">
              <p class="profile-field-label">FIRST NAME</p>
              <input class="settings-input" name="first_name" type="text"
                     value="<?= esc($user['first_name'] ?? '') ?>"/>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">LAST NAME</p>
              <input class="settings-input" name="last_name" type="text"
                     value="<?= esc($user['last_name'] ?? '') ?>"/>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">COUNTRY</p>
              <input class="settings-input" name="country" type="text"
                     value="<?= esc($user['country'] ?? 'Philippines') ?>"/>
            </div>
            <div class="profile-field">
              <p class="profile-field-label">POSTAL CODE</p>
              <input class="settings-input" name="postal_code" type="text"
                     value="<?= esc($user['postal_code'] ?? '') ?>"/>
            </div>
          </div>

          <div class="profile-address-row">
            <p class="profile-field-label">ADDRESS</p>
            <input class="settings-input" name="address" type="text" style="width:100%"
                   value="<?= esc($user['address'] ?? '') ?>"/>
          </div>

          <div class="profile-field">
            <p class="profile-field-label">CITY</p>
            <input class="settings-input" name="city" type="text"
                   value="<?= esc($user['city'] ?? '') ?>"/>
          </div>

          <div class="profile-field">
            <p class="profile-field-label">PHONE NUMBER</p>
            <input class="settings-input" name="phone" type="tel"
                   value="<?= esc($user['phone'] ?? '') ?>"/>
          </div>

          <div class="edit-actions">
            <button type="submit" class="save-btn">SAVE CHANGES</button>
            <button type="button" class="cancel-btn" onclick="cancelEdit()">CANCEL</button>
          </div>
        </form>
      </div>
    </div>

    <!-- CHANGE PASSWORD CARD -->
    <div class="settings-card" id="passwordCard" style="display:none">
      <div class="settings-card-head">
        <span class="settings-card-title">CHANGE PASSWORD</span>
      </div>
      <form method="POST" action="<?= base_url('password/change') ?>">
        <?= csrf_field() ?>
        <div class="profile-grid" style="grid-template-columns:1fr 1fr">
          <div class="profile-field">
            <p class="profile-field-label">CURRENT PASSWORD</p>
            <input class="settings-input" name="current_password" type="password" required/>
          </div>
          <div class="profile-field">
            <p class="profile-field-label">NEW PASSWORD</p>
            <input class="settings-input" name="new_password" type="password" required/>
          </div>
          <div class="profile-field">
            <p class="profile-field-label">CONFIRM PASSWORD</p>
            <input class="settings-input" name="confirm_password" type="password" required/>
          </div>
        </div>
        <div class="edit-actions">
          <button type="submit" class="save-btn">UPDATE PASSWORD</button>
        </div>
      </form>
    </div>

  </div>
</main>

<div class="acct-toast" id="acctToast"></div>

<script src="<?= base_url('/public/js/user_profile.js') ?>"></script>

<?= $this->endSection() ?>