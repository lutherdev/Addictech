// Client-side validation (optional, as we have server-side)
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const errEl = document.getElementById('loginError');
      
      if (!email || !password) {
        e.preventDefault();
        errEl.textContent = 'Please fill in all fields.';
      }
    });