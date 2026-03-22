/* ═══════════════════════════════════════
   wishlist.js — addictech
   globals: products, wishlistIds, CSRF_NAME, CSRF_HASH, WISHLIST_REMOVE
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

function removeCardFromGrid(productId) {
  const card = document.querySelector('.product-card[data-id="' + productId + '"]');
  if (card) card.remove();

  if (document.querySelectorAll('.product-card').length === 0) {
    document.querySelector('.product-grid').innerHTML =
      '<div class="wishlist-empty">Your wishlist is empty. ' +
      '<a href="/catalog">Browse the catalog →</a></div>';
  }
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
    const matchCat    = activeFilter === 'all' || category === activeFilter.toLowerCase();
    const matchSearch = name.includes(term);
    card.style.display = (matchCat && matchSearch) ? '' : 'none';
  });
}

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

document.getElementById('searchInput')?.addEventListener('input', applyFilters);

/* ══════════════════════════════════
   CARD HEART BUTTONS — remove from wishlist
══════════════════════════════════ */
document.querySelectorAll('.card-wish-btn').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    e.stopPropagation();

    const productId = parseInt(this.dataset.id);

    fetch(WISHLIST_REMOVE, {
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
        wishlistIds.delete(productId);
        updateWishBadge(data.count);
        removeCardFromGrid(productId);
      }
    })
    .catch(function() { showToast('Could not remove item.'); });
  });
});

/* ══════════════════════════════════
   CARD CLICKS — open modal
══════════════════════════════════ */
document.querySelectorAll('.product-card').forEach(function(card) {
  card.addEventListener('click', function(e) {
    if (e.target.closest('.card-wish-btn')) return;
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

  // add these lines
  const modalImg = document.getElementById('modalImage');
  if (p.image) {
    modalImg.src = BASE_URL + 'public/images/products/' + p.image;
    modalImg.alt = p.name;
  } else {
    modalImg.src = '';
    modalImg.alt = '';
  }

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

/* modal wish button — remove from wishlist and close */
document.getElementById('modalWishBtn').addEventListener('click', function() {
  if (!currentProduct) return;

  fetch(WISHLIST_REMOVE, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      product_id:  currentProduct.id,
      [CSRF_NAME]: CSRF_HASH
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      wishlistIds.delete(currentProduct.id);
      updateWishBadge(data.count);
      removeCardFromGrid(currentProduct.id);
      closeModal();
    }
  })
  .catch(function() { showToast('Could not remove item.'); });
});

document.getElementById('modalShareBtn').addEventListener('click', function() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const orig = this.innerHTML;
    this.innerHTML = 'COPIED!';
    setTimeout(() => { this.innerHTML = orig; }, 1500);
  });
});