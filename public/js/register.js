/* ═══════════════════════════════════════
   register.js — addictech signup
═══════════════════════════════════════ */

window.addEventListener('load', function () {

  const form      = document.getElementById('signupForm');
  const emailEl   = document.getElementById('email');
  const passEl    = document.getElementById('password');
  const confirmEl = document.getElementById('confirm_password');
  const errorEl   = document.getElementById('signupError');

  if (!form || !emailEl || !passEl || !confirmEl || !errorEl) {
    console.warn('register.js: missing elements.');
    return;
  }

  /* ── show / hide error ── */
  function block(msg) {
    errorEl.textContent = msg;
    errorEl.style.display = 'block';
    errorEl.style.color = '#c0392b';
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function clearMsg() {
    errorEl.textContent = '';
    errorEl.style.display = 'none';
  }

  /* ── known domains ── */
  const VALID_DOMAINS = [
    'gmail.com',
    'yahoo.com',
    'yahoo.com.ph',
    'outlook.com',
    'hotmail.com',
    'icloud.com',
    'live.com'
  ];

  function isDomainValid(domain) {
    return VALID_DOMAINS.includes(domain.toLowerCase());
  }

  /* ── password hint live feedback ── */
  passEl.addEventListener('input', function () {
    const hint = document.querySelector('.field-hint');
    if (!hint) return;
    const n = this.value.length;
    if (n === 0)    { hint.style.color = '#888';    hint.textContent = 'Minimum 6 characters'; }
    else if (n < 6) { hint.style.color = '#c0392b'; hint.textContent = 'Too short — ' + n + '/6'; }
    else            { hint.style.color = '#4caf7d'; hint.textContent = '✓ ' + n + ' characters'; }
  });

  /* ─────────────────────────────────────
     SUBMIT HANDLER
  ───────────────────────────────────── */
  form.addEventListener('submit', function (e) {

    e.preventDefault(); /* always block first */
    clearMsg();

    const email   = emailEl.value.trim();
    const pass    = passEl.value;
    const confirm = confirmEl.value;

    /* 1 — email required */
    if (!email) {
      block('Email address is required.');
      emailEl.focus();
      return;
    }

    /* 2 — must contain @ and a dot after it */
    const emailParts = email.split('@');
    if (emailParts.length !== 2 || !emailParts[1].includes('.')) {
      block('Please enter a valid email address.');
      emailEl.focus();
      return;
    }

    /* 3 — domain must be a known provider */
    const domain = emailParts[1].toLowerCase();
    if (!isDomainValid(domain)) {
      block('Invalid email domain "' + domain + '". Please use a valid provider (e.g. gmail.com, yahoo.com, outlook.com).');
      emailEl.focus();
      return;
    }

    /* 4 — password required */
    if (!pass) {
      block('Password is required.');
      passEl.focus();
      return;
    }

    /* 5 — minimum 6 characters */
    if (pass.length < 6) {
      block('Password must be at least 6 characters. You entered ' + pass.length + '.');
      passEl.focus();
      return;
    }

    /* 6 — passwords match */
    if (pass !== confirm) {
      block('Passwords do not match.');
      confirmEl.focus();
      return;
    }

    /* ✅ all checks passed */
    form.submit();
  });

});