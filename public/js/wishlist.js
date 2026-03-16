/* ═══════════════════════════════════════
   wishlist.js — addictech
   globals: products, wishlistIds, CSRF_NAME, CSRF_HASH
═══════════════════════════════════════ */

function updateCartBadge(count) {
  const b = document.getElementById('cartBadge');
  if (!b) return;
  b.textContent   = count;
  b.style.display = count > 0 ? 'flex' : 'none';
}

function updateWishBadge(count) {
  const b = document.getElementById('wishBadge');
  if (!b) return;
  b.textContent   = count;
  b.style.display = count > 0 ? 'flex' : 'none';
}

function showToast(msg) {
  const t = document.getElementById('atcToast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function() { t.classList.remove('show'); }, 2200);
}

/* ── state ── */
let currentProduct = null;
let qty            = 1;
let activeFilter   = 'all';

/* ══════════════════════════════════
   FILTER + SEARCH
══════════════════════════════════ */
function applyFilters() {
  const term = (document.getElementById('searchInput')?.value ?? '').toLowerCase();

  document.querySelectorAll('.product-card').forEach(function(card) {
    const name     = (card.dataset.name     ?? '').toLowerCase();
    const category = (card.dataset.category ?? '').toLowerCase();

    const matchCat    = activeFilter === 'all'
                     || category === activeFilter.toLowerCase();
    const matchSearch = name.includes(term);

    card.style.display = (matchCat && matchSearch) ? '' : 'none';
  });
}

/* filter buttons */
document.querySelectorAll('.filter-tags .tag[data-filter]').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-tags .tag[data-filter]').forEach(function(b) {
      b.classList.remove('active');
    });
    this.classList.add('active');
    activeFilter = this.dataset.filter;
    applyFilters();
  });
});

/* search input */
document.getElementById('searchInput')?.addEventListener('input', applyFilters);

/* ══════════════════════════════════
   CARD CLICKS — open modal
══════════════════════════════════ */
document.querySelectorAll('.product-card').forEach(function(card) {
  card.addEventListener('click', function(e) {
    if (e.target.closest('.card-wish-btn') || e.target.closest('.card-wish-form')) return;
    const id = parseInt(this.dataset.id);
    const p  = products.find(function(x) { return x.id === id; });
    if (p) openModal(p);
  });
});

/* ══════════════════════════════════
   MODAL
══════════════════════════════════ */
function openModal(p) {
  currentProduct = p;
  qty = 1;

  document.getElementById('formProductId').value    = p.id;
  document.getElementById('formQty').value          = qty;
  document.getElementById('qtyVal').textContent     = qty;
  document.getElementById('modalName').textContent  = p.name.toUpperCase();
  document.getElementById('modalDesc').textContent  = p.desc || '—';
  document.getElementById('modalPrice').textContent = '₱' + Number(p.price).toLocaleString();

  const inStock = p.stock > 0;
  const stockEl = document.getElementById('modalStock');
  stockEl.textContent = inStock ? 'IN STOCK' : 'OUT OF STOCK';
  stockEl.className   = 'modal-stock ' + (inStock ? 'in-stock' : 'out-stock');

  document.getElementById('modalAtcBtn').disabled         = !inStock;
  document.getElementById('modalAtcBtn').style.opacity    = inStock ? '1' : '0.45';
  document.getElementById('modalBuyNowBtn').disabled      = !inStock;
  document.getElementById('modalBuyNowBtn').style.opacity = inStock ? '1' : '0.45';

  document.getElementById('modalWishBtn').classList.add('modal-wish-active');
  document.getElementById('wishIcon').setAttribute('fill',   '#e05252');
  document.getElementById('wishIcon').setAttribute('stroke', '#e05252');
  document.getElementById('wishLabel').textContent = 'WISHLISTED';

  document.getElementById('modalBackdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalBackdrop').classList.remove('open');
  document.body.style.overflow = '';
}

/* ══════════════════════════════════
   EVENT LISTENERS
══════════════════════════════════ */
document.getElementById('modalClose').addEventListener('click', closeModal);

document.getElementById('modalBackdrop').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});

document.getElementById('qtyPlus').addEventListener('click', function() {
  qty = Math.min(qty + 1, 99);
  document.getElementById('qtyVal').textContent = qty;
  document.getElementById('formQty').value      = qty;
});

document.getElementById('qtyMinus').addEventListener('click', function() {
  qty = Math.max(qty - 1, 1);
  document.getElementById('qtyVal').textContent = qty;
  document.getElementById('formQty').value      = qty;
});

document.getElementById('modalWishBtn').addEventListener('click', function() {
  if (!currentProduct) return;
  toggleWishlist(currentProduct).then(function() {
    closeModal();
  });
});

document.getElementById('modalShareBtn').addEventListener('click', function() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const orig = this.innerHTML;
    this.innerHTML = 'COPIED!';
    setTimeout(() => { this.innerHTML = orig; }, 1500);
  });
});