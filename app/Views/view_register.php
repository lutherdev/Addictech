<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Register </title>
</head>

  <?php
  $error = '';
  $success = '';
  $email = '';
  $first_name = '';
  $last_name = '';

  ?>

  <div class="page-title-bar">
    <h1 class="page-title">SIGN UP</h1>
  </div>

  <section class="form-section">
    <form method="POST" action="<?= base_url('auth/register') ?>" id="signupForm" novalidate>
      <?= csrf_field() ?>
      <div class="form-card">

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
          <!-- type="text" + novalidate: disables browser validation so JS takes full control -->
          <input id="email" name="email" type="text" class="field-input" 
                 placeholder="NAME@EXAMPLE.COM" autocomplete="email" 
                 value="<?php echo htmlspecialchars($email); ?>"/>
        </div>
        
        <div class="field-group">
          <label for="password" class="field-label">CREATE PASSWORD *</label>
          <input id="password" name="password" type="password" class="field-input" 
                 autocomplete="new-password"/>
          <small class="field-hint">Minimum 6 characters</small>
        </div>
        
        <div class="field-group">
          <label for="confirm_password" class="field-label">CONFIRM PASSWORD *</label>
          <input id="confirm_password" name="confirm_password" type="password" class="field-input" 
                 autocomplete="new-password"/>
        </div>

        <!-- always in DOM so JS can always find it -->
        <p class="auth-error" id="signupError" style="display:none"></p>
        
        <?php if(session()->getFlashdata('error')): ?>
        <p class="auth-error"><?= session()->getFlashdata('error') ?></p>
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
        <p style="color:green"><?= session()->getFlashdata('success') ?></p>
        <?php endif; ?>
      </div>

      <div class="cta-section">
        <button type="submit" class="btn-signin" id="signupBtn">CREATE ACCOUNT</button>
        <p class="register-prompt">
          Already have an account? <a href="<?= base_url()?>" class="link-register">Login</a>
        </p>
      </div>
    </form>
  </section>
  <script src="<?= base_url('/public/js/register.js') ?>"></script>

<?= $this->endSection() ?>