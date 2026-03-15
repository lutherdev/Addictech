/* ═══════════════════════════════════════
   catalog.js — addictech
   globals: products, wishlistIds, CSRF_NAME, CSRF_HASH, WISHLIST_TOGGLE
═══════════════════════════════════════ */

function showToast(msg) {
  const t = document.getElementById('atcToast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function() { t.classList.remove('show'); }, 2200);
}

function updateWishBadge(count) {
  const b = document.getElementById('wishBadge');
  if (!b) return;
  b.textContent   = count;
  b.style.display = count > 0 ? 'flex' : 'none';
}

/* ── state ── */
let currentProduct = null;
let qty            = 1;
let activeFilter   = 'all';
let sortState      = 0;
let searchTerm     = '';

/* ══════════════════════════════════
   FILTER / SORT / SEARCH
══════════════════════════════════ */
function applyFilters() {
  const cards = document.querySelectorAll('.product-card');
  let visible = [];

  cards.forEach(function(card) {
    const category    = card.dataset.category.toUpperCase();
    const name        = card.dataset.name;
    const matchCat    = activeFilter === 'all' || category === activeFilter.toUpperCase();
    const matchSearch = name.includes(searchTerm.toLowerCase());

    if (matchCat && matchSearch) {
      card.style.display = '';
      visible.push(card);
    } else {
      card.style.display = 'none';
    }
  });

  if (sortState !== 0) {
    const grid = document.getElementById('productGrid');
    visible.sort(function(a, b) {
      const pa = parseFloat(a.dataset.price);
      const pb = parseFloat(b.dataset.price);
      return sortState === 1 ? pa - pb : pb - pa;
    });
    visible.forEach(function(card) { grid.appendChild(card); });
  }
}

/* ══════════════════════════════════
   WISHLIST
══════════════════════════════════ */
function toggleWishlistAjax(productId) {
  fetch(WISHLIST_TOGGLE, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      product_id:  productId,
      [CSRF_NAME]: CSRF_HASH
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      if (data.wished) {
        wishlistIds.add(productId);
      } else {
        wishlistIds.delete(productId);
      }
      updateWishBadge(data.count);
      showToast(data.wished ? 'Added to wishlist.' : 'Removed from wishlist.');
      updateModalWishBtn(data.wished);
    }
  })
  .catch(function() { showToast('Could not update wishlist.'); });
}

function updateModalWishBtn(wished) {
  const wishBtn   = document.getElementById('modalWishBtn');
  const wishIcon  = document.getElementById('wishIcon');
  const wishLabel = document.getElementById('wishLabel');
  wishBtn.classList.toggle('modal-wish-active', wished);
  wishIcon.setAttribute('fill',   wished ? '#e05252' : 'none');
  wishIcon.setAttribute('stroke', wished ? '#e05252' : 'currentColor');
  wishLabel.textContent = wished ? 'WISHLISTED' : 'WISHLIST';
}

/* ══════════════════════════════════
   CARD CLICKS
══════════════════════════════════ */
document.querySelectorAll('.product-card').forEach(function(card) {
  card.addEventListener('click', function() {
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

  updateModalWishBtn(isWishlisted(p.id));

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
  toggleWishlistAjax(currentProduct.id);
});

document.getElementById('modalShareBtn').addEventListener('click', function() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const orig = this.innerHTML;
    this.innerHTML = 'COPIED!';
    setTimeout(() => { this.innerHTML = orig; }, 1500);
  });
});

document.querySelectorAll('.tag[data-filter]').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tag[data-filter]').forEach(function(b) {
      b.classList.remove('active');
    });
    btn.classList.add('active');
    activeFilter = btn.dataset.filter;
    applyFilters();
  });
});

document.getElementById('sortBtn').addEventListener('click', function() {
  sortState = (sortState + 1) % 3;
  this.textContent = sortState === 1 ? 'Price: Low → High'
                   : sortState === 2 ? 'Price: High → Low'
                   : 'Sort By Price';
  applyFilters();
});

document.getElementById('searchInput').addEventListener('input', function() {
  searchTerm = this.value;
  applyFilters();
});