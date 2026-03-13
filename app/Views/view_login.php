<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Login</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/login.css') ?>" />
</head>
<body>
 
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
  <div class="page-title-bar">
    <h1 class="page-title">LOGIN</h1>
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
  </div>

  <section class="form-section">
    <form method="POST" action="<?= base_url('auth/login') ?>">
      <div class="form-card">
        <div class="field-group">
          <label for="email" class="field-label">EMAIL ADDRESS</label>
          <input id="email" name="email" type="text" class="field-input" placeholder="NAME@EXAMPLE.COM" value="" required/>
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
        <p class="register-prompt">Don't have an account? <a href="<?= base_url('register')?>" class="link-register">Register</a></p>
      </div>
    </form>
  </section>
  <script src="<?= base_url('/public/js/login.js') ?>"></script>
</body>
</html>
<?= $this->endSection() ?>