<?php
/**
 * NAVBAR PARTIAL
 *
 * Variables (all optional — pass from controller or view):
 *   $nav_active  string   Which link is active: 'home'|'about'|'catalog'|'contact'
 *   $cart_count  int      Number of items in cart (for badge)
 *   $wish_count  int      Number of wishlisted items (for badge)
 *   $current_user array  Logged-in user (checks for null to decide account link)
 */

$nav_active  = $nav_active  ?? '';
$cart_count  = $cart_count  ?? 0;
$wish_count  = $wish_count  ?? 0;

// Helper: apply 'active' class
function nav_class(string $page, string $active): string {
    return $page === $active ? 'nav-link active' : 'nav-link';
}
?>

<nav class="navbar">

  <!-- LEFT: brand -->
  <div class="nav-left">
    <a href="<?= base_url('home') ?>" class="brand">addictech</a>
  </div>

  <!-- CENTER: links -->
  <div class="nav-links">
    <a href="<?= base_url('home') ?>"        class="<?= nav_class('home',    $nav_active) ?>">HOME</a>
    <a href="<?= base_url('about') ?>"    class="<?= nav_class('about',   $nav_active) ?>">ABOUT</a>
    <a href="<?= base_url('catalog') ?>"  class="<?= nav_class('catalog', $nav_active) ?>">CATALOG</a>
    <a href="<?= base_url('contact') ?>"  class="<?= nav_class('contact', $nav_active) ?>">CONTACT</a>
    <a href="<?= base_url('admin/users') ?>"  class="<?= nav_class('contact', $nav_active) ?>">admin - user</a>
  </div>

  <!-- RIGHT: icons -->
  <div class="nav-icons">

    <!-- Wishlist -->
    <a href="<?= base_url('wishlist') ?>" class="icon-btn wish-icon-wrap" aria-label="Wishlist">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <?php if ($wish_count > 0): ?>
        <span class="wish-badge"><?= $wish_count ?></span>
      <?php endif; ?>
    </a>

    <!-- Cart -->
    <a href="<?= base_url('cart') ?>" class="icon-btn cart-icon-wrap" aria-label="Cart">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <?php if ($cart_count > 0): ?>
        <span class="cart-badge"><?= $cart_count ?></span>
      <?php endif; ?>
    </a>

    <!-- Account -->
    <a href="<?= base_url('user/profile') ?>" class="icon-btn" aria-label="Account">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
    </a>

    <!-- Logout (only shown when logged in) -->
    <?php if (!empty($current_user)): ?>
    <a href="<?= base_url('auth/logout') ?>" class="icon-btn" aria-label="Logout" title="Logout">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
    </a>
    <?php endif; ?>

  </div>
</nav>
