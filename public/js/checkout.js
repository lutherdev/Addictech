 document.addEventListener('DOMContentLoaded', function() {
      // Set initial shipping selected class
      const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
      if (selectedShipping) {
        selectedShipping.closest('.shipping-option').classList.add('selected');
      }
      
      // Form validation
      document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const required = ['fullName', 'address', 'postal', 'city'];
        let hasError = false;
        
        required.forEach(id => {
          const input = document.getElementById(id);
          if (!input.value.trim()) {
            input.classList.add('shake');
            setTimeout(() => input.classList.remove('shake'), 500);
            hasError = true;
          }
        });
        
        if (hasError) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });
    });