document.addEventListener('DOMContentLoaded', function() {

  // Fix: was "shipping_method", should be "delivery_method"
  const selectedShipping = document.querySelector('input[name="delivery_method"]:checked');
  if (selectedShipping) {
    selectedShipping.closest('.shipping-option').classList.add('selected');
  }

  // Add selected class on change
  document.querySelectorAll('input[name="delivery_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.shipping-option').forEach(function(opt) {
        opt.classList.remove('selected');
      });
      this.closest('.shipping-option').classList.add('selected');
      updateShipping();
    });
  });

  // Form validation
  document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    const required = ['fullName', 'address', 'postal', 'city'];
    let hasError = false;

    required.forEach(function(id) {
      const input = document.getElementById(id);
      if (!input.value.trim()) {
        input.classList.add('shake');
        setTimeout(function() { input.classList.remove('shake'); }, 500);
        hasError = true;
      }
    });

    if (hasError) {
      e.preventDefault();
      alert('Please fill in all required fields.');
    }
  });

});