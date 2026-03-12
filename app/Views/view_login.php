<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>addictech – Login</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/login.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>
 <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
  <?php
  
  // If already logged in, redirect to account
  // if (isset($_SESSION['user_id'])) {
  //     header('Location: account.php');
  //     exit();
  // }
  
  // Initialize error message
  $error = '';
  
  // Get cart count for badge (optional)
  $cart_count = 0;
  if (isset($_SESSION['cart'])) {
      $cart_count = array_sum($_SESSION['cart']);
  }
  ?>

  <nav class="navbar">
    <div class="nav-left"><a href="index.php" class="brand">addictech</a></div>
    <div class="nav-links">
      <a href="index.php" class="nav-link">HOME</a>
      <a href="about.php" class="nav-link">ABOUT</a>
      <a href="catalog.php" class="nav-link">CATALOG</a>
      <a href="contact.php" class="nav-link">CONTACT</a>
    </div>
    <div class="nav-icons">
      <button class="icon-btn" aria-label="Wishlist">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      <a href="cart.php" class="icon-btn cart-icon-wrap" aria-label="Cart">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <?php if ($cart_count > 0): ?>
        <span class="cart-badge"><?php echo $cart_count; ?></span>
        <?php endif; ?>
      </a>
      <a href="account.php" class="icon-btn" aria-label="Account">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </a>
    </div>
  </nav>

  <div class="page-title-bar">
    <h1 class="page-title">LOGIN</h1>
  </div>

  <section class="form-section">
    <form method="POST" action="<?= base_url('auth/login') ?>">
      <div class="form-card">
        <div class="field-group">
          <label for="email" class="field-label">EMAIL ADDRESS</label>
          <input id="username" name="username" type="text" class="field-input" placeholder="NAME@EXAMPLE.COM" value="" required/>
        </div>
        <div class="field-group">
          <label for="password" class="field-label">PASSWORD</label>
          <input id="password" name="password" type="password" class="field-input" required/>
        </div>
        <?php if (!empty($error)): ?>
        <p class="auth-error" id="loginError"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
        <p class="auth-error" id="loginError"></p>
        <?php endif; ?>
      </div>

      <div class="cta-section">
        <button type="submit" class="btn-signin" id="loginBtn">SIGN IN</button>
        <a href="forgot-password.php" class="link-forgot">Forgot password?</a>
        <p class="register-prompt">Don't have an account? <a href="signup.php" class="link-register">Register</a></p>
      </div>
    </form>
  </section>

  <style>
    .auth-error {
      font-family: var(--font-body);
      font-size: 0.75rem;
      color: #c0392b;
      letter-spacing: 0.04em;
      margin-top: 0.5rem;
      min-height: 1.2em;
    }
    .btn-signin {
      background: none;
      border: 1.5px solid var(--text);
      color: var(--text);
      padding: 0.8rem 3rem;
      font-family: var(--font-body);
      font-weight: 500;
      letter-spacing: 0.15em;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s;
      margin-bottom: 1.5rem;
    }
    .btn-signin:hover {
      background: var(--text);
      color: var(--bg);
    }
    .link-forgot {
      display: block;
      font-family: var(--font-body);
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      text-decoration: none;
      margin-bottom: 2rem;
    }
    .link-forgot:hover {
      color: var(--text);
    }
    .register-prompt {
      font-family: var(--font-body);
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }
    .link-register {
      color: var(--text);
      text-decoration: none;
      border-bottom: 1px solid var(--text);
      padding-bottom: 1px;
    }
    .link-register:hover {
      opacity: 0.7;
    }
  </style>

  <!-- Optional JavaScript for client-side validation -->
  <script>
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
  </script>

</body>
</html>