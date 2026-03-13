/* ── debug: log so you can confirm data is correct ── */
console.log('Products loaded:', products.length, products[0]);
 
/* ── wishlist (placeholder until backend ready) ── */
function isWishlisted(id) { return false; }
function toggleWishlist(product) { return Promise.resolve(false); }
 
/* ── toast ── */
function showToast(msg) {
  const t = document.getElementById('atcToast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
}
 
/* ── state ── */
let currentProduct = null;
let qty            = 1;
let activeFilter   = 'all';
let sortState      = 0;
let sortAsc        = null;
let searchTerm     = '';
 
/* ══════════════════════════════════
   RENDER GRID
══════════════════════════════════ */
function renderGrid() {
  const grid = document.getElementById('productGrid');
 
  let list = products.filter(function(p) {
    const matchCat    = activeFilter === 'all' || p.category === activeFilter;
    const matchSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase());
    return matchCat && matchSearch;
  });
 
  if (sortAsc !== null) {
    list = list.slice().sort(function(a, b) {
      return sortAsc ? a.price - b.price : b.price - a.price;
    });
  }
 
  if (list.length === 0) {
    grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#888;padding:3rem">No products found.</p>';
    return;
  }
 
  grid.innerHTML = list.map(function(p, i) {
    return '<div class="product-card" style="animation-delay:' + (i * 0.05) + 's" data-id="' + p.id + '">' +
      '<div class="product-img">' +
        '<svg width="100%" height="100%" viewBox="0 0 300 340" preserveAspectRatio="none" fill="none">' +
          '<rect x="1" y="1" width="298" height="338" stroke="#b8b4aa" stroke-width="1.5"/>' +
          '<line x1="1" y1="1" x2="299" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>' +
          '<line x1="299" y1="1" x2="1" y2="339" stroke="#b8b4aa" stroke-width="1.5"/>' +
        '</svg>' +
      '</div>' +
      '<div class="product-meta">' +
        '<span class="product-name">' + p.name.toUpperCase() + '</span>' +
        '<span class="product-price">₱' + Number(p.price).toLocaleString() + '</span>' +
      '</div>' +
    '</div>';
  }).join('');
 
  /* attach click to each card */
  grid.querySelectorAll('.product-card').forEach(function(card) {
    card.addEventListener('click', function() {
      openModal(parseInt(this.dataset.id));
    });
  });
}
 
/* ══════════════════════════════════
   OPEN MODAL
══════════════════════════════════ */
function openModal(id) {
  const p = products.find(function(x) { return x.id === id; });
  if (!p) {
    console.warn('openModal: product not found, id =', id);
    return;
  }
 
  console.log('Opening modal for:', p.name, '| stock:', p.stock);
 
  currentProduct = p;
  qty = 1;
 
  document.getElementById('qtyVal').textContent    = qty;
  document.getElementById('modalName').textContent  = p.name.toUpperCase();
  document.getElementById('modalDesc').textContent  = p.desc || '—';
  document.getElementById('modalPrice').textContent = '₱' + Number(p.price).toLocaleString();
 
  const inStock  = p.stock === 1 || p.stock === true;
  const stockEl  = document.getElementById('modalStock');
  stockEl.textContent = inStock ? 'IN STOCK' : 'OUT OF STOCK';
  stockEl.className   = 'modal-stock ' + (inStock ? 'in-stock' : 'out-stock');
 
  const atcBtn         = document.getElementById('modalAtcBtn');
  atcBtn.textContent   = inStock ? 'ADD TO CART' : 'OUT OF STOCK';
  atcBtn.disabled      = !inStock;
  atcBtn.style.opacity = inStock ? '1' : '0.45';
 
  const wished = isWishlisted(p.id);
  document.getElementById('modalWishBtn').classList.toggle('modal-wish-active', wished);
  document.getElementById('wishIcon').setAttribute('fill',   wished ? '#e05252' : 'none');
  document.getElementById('wishIcon').setAttribute('stroke', wished ? '#e05252' : 'currentColor');
  document.getElementById('wishLabel').textContent = wished ? 'WISHLISTED' : 'WISHLIST';
 
  document.getElementById('modalBackdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}
 
/* ── close modal ── */
function closeModal() {
  document.getElementById('modalBackdrop').classList.remove('open');
  document.body.style.overflow = '';
}
 
/* ══════════════════════════════════
   EVENT LISTENERS
══════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
 
  /* close */
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
 
  /* wishlist */
  document.getElementById('modalWishBtn').addEventListener('click', async function() {
    if (!currentProduct) return;
    const wished = await toggleWishlist(currentProduct);
    this.classList.toggle('modal-wish-active', wished);
    document.getElementById('wishIcon').setAttribute('fill',   wished ? '#e05252' : 'none');
    document.getElementById('wishIcon').setAttribute('stroke', wished ? '#e05252' : 'currentColor');
    document.getElementById('wishLabel').textContent = wished ? 'WISHLISTED' : 'WISHLIST';
  });
 
  /* share */
  document.getElementById('modalShareBtn').addEventListener('click', function() {
    navigator.clipboard.writeText(window.location.href).then(() => {
      const orig = this.innerHTML;
      this.innerHTML = 'COPIED!';
      setTimeout(() => { this.innerHTML = orig; }, 1500);
    });
  });
 
  /* filter tags */
  document.querySelectorAll('.tag[data-filter]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tag[data-filter]').forEach(function(b) {
        b.classList.remove('active');
      });
      this.classList.add('active');
      activeFilter = this.dataset.filter;
      renderGrid();
    });
  });
 
  /* sort */
  document.getElementById('sortBtn').addEventListener('click', function() {
    sortState = (sortState + 1) % 3;
    sortAsc   = sortState === 1 ? true : sortState === 2 ? false : null;
    this.textContent = sortState === 1 ? 'Price: Low → High'
                     : sortState === 2 ? 'Price: High → Low'
                     : 'Sort By Price';
    renderGrid();
  });
 
  /* search */
  document.getElementById('searchInput').addEventListener('input', function() {
    searchTerm = this.value;
    renderGrid();
  });
 
  /* initial render */
  renderGrid();
});