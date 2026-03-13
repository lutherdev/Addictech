/* ═══════════════════════════════════════
   wishlist.js — addictech
   globals: products, wishlistIds, CART_ADD_URL, CSRF_NAME, CSRF_HASH
═══════════════════════════════════════ */

/* ── cart ── */
function addToCart(product, qty) {
  return fetch(CART_ADD_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      product_id:  product.id,
      quantity:    qty,
      [CSRF_NAME]: CSRF_HASH
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    showToast(data.success ? data.message : (data.message || 'Could not add to cart.'));
    if (data.success) updateCartBadge(data.cart_count);
    return !!data.success;
  })
  .catch(function() { showToast('Error adding to cart.'); return false; });
}

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

/* ── toast ── */
function showToast(msg) {
  const t = document.getElementById('atcToast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function() { t.classList.remove('show'); }, 2200);
}

/* ── state ── */
let currentProduct = null;
let qty            = 1;
let searchTerm     = '';

/* ══════════════════════════════════
   RENDER GRID
══════════════════════════════════ */
function renderGrid() {
  const grid = document.getElementById('productGrid');

  /* only show wishlisted items that match search */
  let list = products.filter(function(p) {
    return wishlistIds.has(p.id) &&
           p.name.toLowerCase().includes(searchTerm.toLowerCase());
  });

  if (list.length === 0) {
    grid.innerHTML =
      '<div class="wishlist-empty">' +
        'Your wishlist is empty. ' +
        '<a href="/catalog">Browse the catalog →</a>' +
      '</div>';
    return;
  }

  grid.innerHTML = list.map(function(p, i) {
    return (
      '<div class="product-card" style="animation-delay:' + (i * 0.05) + 's" data-id="' + p.id + '">' +
        '<div class="product-img">' +
          '<svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">' +
            '<rect x="1" y="1" width="298" height="338" stroke="#b8b4aa" stroke-width="1.5"/>' +
            '<line x1="1" y1="1" x2="299" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>' +
            '<line x1="299" y1="1" x2="1" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>' +
          '</svg>' +
          /* heart — no background, always red/filled since it's in wishlist */
          '<button class="card-wish-btn" data-id="' + p.id + '" aria-label="Remove from wishlist">' +
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="#e05252" stroke="#e05252" stroke-width="1.8">' +
              '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>' +
            '</svg>' +
          '</button>' +
        '</div>' +
        '<div class="product-meta">' +
          '<span class="product-name">' + p.name.toUpperCase() + '</span>' +
          '<span class="product-price">₱' + Number(p.price).toLocaleString() + '</span>' +
        '</div>' +
      '</div>'
    );
  }).join('');

  /* card click → open modal */
  grid.querySelectorAll('.product-card').forEach(function(card) {
    card.addEventListener('click', function() {
      openModal(parseInt(this.dataset.id));
    });
  });

  /* heart click → remove from wishlist */
  grid.querySelectorAll('.card-wish-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const id = parseInt(this.dataset.id);
      const p  = products.find(function(x) { return x.id === id; });
      if (!p) return;
      toggleWishlist(p).then(function() {
        updateWishBadge(wishlistIds.size);
        renderGrid(); /* re-render — removes the card */
      });
    });
  });
}

/* ══════════════════════════════════
   OPEN MODAL
══════════════════════════════════ */
function openModal(id) {
  const p = products.find(function(x) { return x.id === id; });
  if (!p) return;

  currentProduct = p;
  qty = 1;

  document.getElementById('qtyVal').textContent    = qty;
  document.getElementById('modalName').textContent  = p.name.toUpperCase();
  document.getElementById('modalDesc').textContent  = p.desc || '—';
  document.getElementById('modalPrice').textContent = '₱' + Number(p.price).toLocaleString();

  const inStock = p.stock === 1 || p.stock === true;
  const stockEl = document.getElementById('modalStock');
  stockEl.textContent = inStock ? 'IN STOCK' : 'OUT OF STOCK';
  stockEl.className   = 'modal-stock ' + (inStock ? 'in-stock' : 'out-stock');

  const atcBtn         = document.getElementById('modalAtcBtn');
  atcBtn.textContent   = inStock ? 'ADD TO CART' : 'OUT OF STOCK';
  atcBtn.disabled      = !inStock;
  atcBtn.style.opacity = inStock ? '1' : '0.45';

  /* always wishlisted on this page */
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
document.addEventListener('DOMContentLoaded', function() {

  /* close modal */
  document.getElementById('modalClose').addEventListener('click', closeModal);
  document.getElementById('modalBackdrop').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
  });

  /* qty */
  document.getElementById('qtyPlus').addEventListener('click', function() {
    qty = Math.min(qty + 1, 99);
    document.getElementById('qtyVal').textContent = qty;
  });
  document.getElementById('qtyMinus').addEventListener('click', function() {
    qty = Math.max(qty - 1, 1);
    document.getElementById('qtyVal').textContent = qty;
  });

  /* add to cart */
  document.getElementById('modalAtcBtn').addEventListener('click', async function() {
    if (!currentProduct || currentProduct.stock !== 1) return;
    const ok = await addToCart(currentProduct, qty);
    if (ok) closeModal();
  });

  /* wishlist button inside modal — removes item */
  document.getElementById('modalWishBtn').addEventListener('click', function() {
    if (!currentProduct) return;
    toggleWishlist(currentProduct).then(function() {
      updateWishBadge(wishlistIds.size);
      closeModal();
      renderGrid();
    });
  });

  /* share */
  document.getElementById('modalShareBtn').addEventListener('click', function() {
    navigator.clipboard.writeText(window.location.href).then(() => {
      const orig = this.innerHTML;
      this.innerHTML = 'COPIED!';
      setTimeout(() => { this.innerHTML = orig; }, 1500);
    });
  });

  /* search */
  document.getElementById('searchInput').addEventListener('input', function() {
    searchTerm = this.value;
    renderGrid();
  });

  /* initial render */
  renderGrid();
});