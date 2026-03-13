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