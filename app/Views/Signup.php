<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Signup</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/login.css') ?>" />
</head>
<body>

  <?php
  // Start session
  session_start();
  
  // Include database configuration
  require_once 'config/database.php';
  
  // If already logged in, redirect to account
  if (isset($_SESSION['user_id'])) {
      header('Location: account.php');
      exit();
  }
  
  // Initialize variables
  $error = '';
  $success = '';
  $email = '';
  $first_name = '';
  $last_name = '';
  
  // Handle signup form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
      // Get and sanitize input
      $email = strtolower(trim($_POST['email'] ?? ''));
      $password = $_POST['password'] ?? '';
      $confirm_password = $_POST['confirm_password'] ?? '';
      $first_name = trim($_POST['first_name'] ?? '');
      $last_name = trim($_POST['last_name'] ?? '');
      
      // Validation
      $errors = [];
      
      if (empty($email)) {
          $errors[] = 'Email address is required.';
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = 'Please enter a valid email address.';
      }
      
      if (empty($password)) {
          $errors[] = 'Password is required.';
      } elseif (strlen($password) < 6) {
          $errors[] = 'Password must be at least 6 characters long.';
      }
      
      if ($password !== $confirm_password) {
          $errors[] = 'Passwords do not match.';
      }
      
      // Check if email already exists
      if (empty($errors)) {
          $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
          $check_stmt->bind_param("s", $email);
          $check_stmt->execute();
          $check_result = $check_stmt->get_result();
          
          if ($check_result->num_rows > 0) {
              $errors[] = 'An account with this email already exists.';
          }
      }
      
      // If no errors, create user
      if (empty($errors)) {
          // Hash password
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          
          // Get current date for member since
          $member_since = strtoupper(date('F Y'));
          
          // Insert user into database
          $insert_stmt = $conn->prepare("
              INSERT INTO users (
                  email, password, first_name, last_name, 
                  country, language, member_since, created_at
              ) VALUES (?, ?, ?, ?, 'Philippines', 'English', ?, NOW())
          ");
          
          $insert_stmt->bind_param("sssss", 
              $email, $hashed_password, $first_name, $last_name, $member_since
          );
          
          if ($insert_stmt->execute()) {
              $user_id = $conn->insert_id;
              
              // Set session variables
              $_SESSION['user_id'] = $user_id;
              $_SESSION['user_email'] = $email;
              $_SESSION['user_name'] = $first_name ?: explode('@', $email)[0];
              
              // Redirect to account page
              header('Location: account.php');
              exit();
          } else {
              $error = 'An error occurred while creating your account. Please try again.';
          }
      } else {
          $error = implode(' ', $errors);
      }
  }
  
  // Get cart count for badge
  $cart_count = 0;
  if (isset($_SESSION['cart'])) {
      $cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
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
      <a href="wishlist.php" class="icon-btn" aria-label="Wishlist">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </a>
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
    <h1 class="page-title">SIGN UP</h1>
  </div>

  <section class="form-section">
    <form method="POST" action="" id="signupForm">
      <div class="form-card">
        <!-- Optional: Add name fields for better user experience -->
        <div class="field-group">
          <label for="first_name" class="field-label">FIRST NAME (OPTIONAL)</label>
          <input id="first_name" name="first_name" type="text" class="field-input" 
                 value="<?php echo htmlspecialchars($first_name); ?>" autocomplete="given-name"/>
        </div>
        
        <div class="field-group">
          <label for="last_name" class="field-label">LAST NAME (OPTIONAL)</label>
          <input id="last_name" name="last_name" type="text" class="field-input" 
                 value="<?php echo htmlspecialchars($last_name); ?>" autocomplete="family-name"/>
        </div>
        
        <div class="field-group">
          <label for="email" class="field-label">EMAIL ADDRESS *</label>
          <input id="email" name="email" type="email" class="field-input" 
                 placeholder="NAME@EXAMPLE.COM" autocomplete="email" 
                 value="<?php echo htmlspecialchars($email); ?>" required/>
        </div>
        
        <div class="field-group">
          <label for="password" class="field-label">CREATE PASSWORD *</label>
          <input id="password" name="password" type="password" class="field-input" 
                 autocomplete="new-password" required/>
          <small class="field-hint">Minimum 6 characters</small>
        </div>
        
        <div class="field-group">
          <label for="confirm_password" class="field-label">CONFIRM PASSWORD *</label>
          <input id="confirm_password" name="confirm_password" type="password" class="field-input" 
                 autocomplete="new-password" required/>
        </div>
        
        <?php if (!empty($error)): ?>
        <p class="auth-error" id="signupError"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
        <p class="auth-error" id="signupError"></p>
        <?php endif; ?>
      </div>

      <div class="cta-section">
        <button type="submit" class="btn-signin" id="signupBtn">CREATE ACCOUNT</button>
        <p class="register-prompt">
          Already have an account? <a href="login.php" class="link-register">Login</a>
        </p>
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
    
    .field-group {
      margin-bottom: 1.5rem;
    }
    
    .field-label {
      display: block;
      font-family: var(--font-body);
      font-size: 0.7rem;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 0.3rem;
    }
    
    .field-input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border);
      padding: 0.5rem 0;
      font-family: var(--font-body);
      font-size: 0.9rem;
      color: var(--text);
      outline: none;
    }
    
    .field-input:focus {
      border-bottom-color: var(--text);
    }
    
    .field-hint {
      display: block;
      font-family: var(--font-body);
      font-size: 0.65rem;
      color: var(--text-muted);
      margin-top: 0.3rem;
    }
    
    .form-card {
      max-width: 400px;
      margin: 0 auto;
      padding: 2rem;
      background: #fff;
    }
    
    .page-title-bar {
      text-align: center;
      margin: 3rem 0 2rem;
    }
    
    .page-title {
      font-family: var(--font-heading);
      font-weight: 300;
      letter-spacing: 0.2em;
      font-size: 2rem;
    }
    
    .cta-section {
      text-align: center;
      margin: 2rem 0 4rem;
    }
  </style>

  <script>
    // Client-side validation (optional, complements server-side)
    document.getElementById('signupForm')?.addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const confirm = document.getElementById('confirm_password').value;
      const errorEl = document.getElementById('signupError');
      
      // Clear previous error
      errorEl.textContent = '';
      
      // Email validation
      if (!email) {
        e.preventDefault();
        errorEl.textContent = 'Email address is required.';
        return;
      }
      
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        errorEl.textContent = 'Please enter a valid email address.';
        return;
      }
      
      // Password validation
      if (!password) {
        e.preventDefault();
        errorEl.textContent = 'Password is required.';
        return;
      }
      
      if (password.length < 6) {
        e.preventDefault();
        errorEl.textContent = 'Password must be at least 6 characters.';
        return;
      }
      
      if (password !== confirm) {
        e.preventDefault();
        errorEl.textContent = 'Passwords do not match.';
        return;
      }
    });
    
    // Optional: Add password strength indicator
    document.getElementById('password')?.addEventListener('input', function() {
      const password = this.value;
      const hint = document.querySelector('.field-hint');
      
      if (hint) {
        if (password.length === 0) {
          hint.style.color = 'var(--text-muted)';
          hint.textContent = 'Minimum 6 characters';
        } else if (password.length < 6) {
          hint.style.color = '#c0392b';
          hint.textContent = 'Too short (' + password.length + '/6)';
        } else {
          hint.style.color = '#4caf7d';
          hint.textContent = '✓ Good length (' + password.length + ' characters)';
        }
      }
    });
  </script>

</body>
</html>
<?= $this->endSection() ?>