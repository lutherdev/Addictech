    /* ── CART FUNCTIONS WITH AJAX ── */
    
    function updateCartDisplay() {
      // Fetch latest cart data from server
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_cart'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateBadge(data.cart_count);
          updateTotal(data.cart_total);
          
          // If cart is empty, reload to show empty state
          if (data.cart_count === 0) {
            location.reload();
          }
        }
      });
    }

    function updateBadge(count) {
      const badge = document.getElementById('cartBadge');
      if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
    }

    function updateTotal(total) {
      document.getElementById('cartTotal').textContent = '₱' + total.toLocaleString();
    }

    function updateQuantity(id, newQty) {
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_qty&id=${id}&qty=${newQty}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById(`qty-${id}`).textContent = newQty;
          updateBadge(data.cart_count);
          updateTotal(data.cart_total);
        }
      });
    }

    function removeItem(id) {
      fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove_item&id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove item from DOM
          const item = document.querySelector(`.cart-item[data-id="${id}"]`);
          if (item) {
            item.remove();
            
            // Check if cart is empty
            if (data.cart_count === 0) {
              location.reload();
            } else {
              updateBadge(data.cart_count);
              updateTotal(data.cart_total);
            }
          }
        }
      });
    }

    /* ── EVENT LISTENERS ── */
    document.addEventListener('DOMContentLoaded', function() {
      // Quantity plus buttons
      document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const currentQty = parseInt(document.getElementById(`qty-${id}`).textContent);
          const newQty = Math.min(currentQty + 1, 99);
          updateQuantity(id, newQty);
        });
      });

      // Quantity minus buttons
      document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          const currentQty = parseInt(document.getElementById(`qty-${id}`).textContent);
          const newQty = Math.max(currentQty - 1, 1);
          updateQuantity(id, newQty);
        });
      });

      // Remove buttons
      document.querySelectorAll('.item-remove').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.dataset.id;
          removeItem(id);
        });
      });

      // Save order note to localStorage (optional)
      const orderNote = document.getElementById('orderNote');
      if (orderNote) {
        // Load saved note
        const savedNote = localStorage.getItem('order_note');
        if (savedNote) {
          orderNote.value = savedNote;
        }
        
        // Save note on change
        orderNote.addEventListener('input', () => {
          localStorage.setItem('order_note', orderNote.value);
        });
      }
    });