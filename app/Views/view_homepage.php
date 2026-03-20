<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Homepage</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/homepage.css') ?>" />
  
</head>
<body>
<!-- ═══════════════════════════════════════
     HERO
═══════════════════════════════════════ -->
<section class="hero">
 
  <!-- placeholder background (swap with <img> when ready) -->
  <div class="hero-img-placeholder"></div>
 
  <!-- scrolling brand marquee -->
  <div class="hero-marquee-wrap">
    <div class="hero-marquee">
      <!-- duplicated so it loops seamlessly -->
      <span>addictech /</span><span>addictech /</span><span>addictech /</span>
      <span>addictech /</span><span>addictech /</span><span>addictech /</span>
      <span>addictech /</span><span>addictech /</span><span>addictech /</span>
      <span>addictech /</span><span>addictech /</span><span>addictech /</span>
    </div>
  </div>
 
</section>
 
<!-- ═══════════════════════════════════════
     BOTTOM: SHOP BY + PROMO PANELS
═══════════════════════════════════════ -->
<div class="home-bottom">
 
  <!-- LEFT: Shop by Product Type -->
  <div class="shop-by">
    <h2 class="shop-by-heading">Shop by<br>Product Type</h2>
    <nav class="shop-by-links">
      <a href="<?= base_url('catalog?category=keyboard') ?>"  class="shop-by-link">KEYBOARDS</a>
      <a href="<?= base_url('catalog?category=mouse') ?>"     class="shop-by-link">MOUSE</a>
      <a href="<?= base_url('catalog?category=headset') ?>"   class="shop-by-link">HEADPHONES</a>
      <a href="<?= base_url('catalog?category=monitor') ?>"   class="shop-by-link">MONITORS</a>
      <a href="<?= base_url('catalog?category=speaker') ?>"   class="shop-by-link">SPEAKERS</a>
      <a href="<?= base_url('catalog?category=cam') ?>"       class="shop-by-link">WEB CAMS</a>
    </nav>
  </div>
 
  <!-- RIGHT: single image panel with text overlays -->
  <div class="promo-stack">
    <div class="promo-panel">
 
      <!-- background image placeholder — swap with <img> when 2.png is ready -->
      <div class="promo-img">
        <div class="promo-img-placeholder"></div>
      </div>
 
      <!-- TOP LEFT: SHOP link + WE THE BEST -->
      <div class="promo-topleft">
        <a href="<?= base_url('catalog') ?>" class="promo-shop-link">SHOP›</a>
        <p class="promo-panel-text">WE<br>THE<br>BEST</p>
      </div>
 
      <!-- BOTTOM RIGHT: YOU THE BEST -->
      <div class="promo-bottomright">
        <p class="promo-panel-text">YOU<br>THE<br>BEST</p>
      </div>
 
    </div>
  </div>
 
</div>
 
 
<!-- ═══════════════════════════════════════
     BANNER — 4.png
═══════════════════════════════════════ -->
<div class="banner-4"></div>
 
<!-- ═══════════════════════════════════════
     FEATURED PRODUCTS
═══════════════════════════════════════ -->
<section class="bestsellers">
  <h2 class="section-heading">FEATURED PRODUCTS</h2>
 
  <div class="bs-grid">
    <?php
    // Use featured_products passed from controller, fallback to empty array
    $featured = $featured_products ?? [];
    
    if (!empty($featured)):
        foreach ($featured as $product):
    ?>
    <a href="<?= base_url('catalog/product/' . $product['id']) ?>" class="bs-card">
      <div class="bs-img">
        <?php if (!empty($product['image']) && file_exists(FCPATH . 'public/uploads/' . $product['image'])): ?>
          <img src="<?= base_url('public/uploads/' . $product['image']) ?>" alt="<?= esc($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
          <!-- placeholder X box when no image exists -->
          <svg width="100%" height="100%" viewBox="0 0 300 280" preserveAspectRatio="none" fill="none">
            <rect x="1" y="1" width="298" height="278" stroke="#c8c4bc" stroke-width="1.5"/>
            <line x1="1" y1="1" x2="299" y2="279" stroke="#c8c4bc" stroke-width="1.5"/>
            <line x1="299" y1="1" x2="1" y2="279" stroke="#c8c4bc" stroke-width="1.5"/>
          </svg>
        <?php endif; ?>
      </div>
      <p class="bs-name"><?= strtoupper(esc($product['name'] ?? 'PRODUCT')) ?></p>
      <p class="bs-price">₱<?= number_format($product['price'] ?? 0, 0) ?></p>
    </a>
    <?php 
        endforeach;
    else:
        // Fallback placeholder products if no featured products exist
        for ($i = 1; $i <= 3; $i++):
    ?>
    <a href="<?= base_url('catalog') ?>" class="bs-card">
      <div class="bs-img">
        <svg width="100%" height="100%" viewBox="0 0 300 280" preserveAspectRatio="none" fill="none">
          <rect x="1" y="1" width="298" height="278" stroke="#c8c4bc" stroke-width="1.5"/>
          <line x1="1" y1="1" x2="299" y2="279" stroke="#c8c4bc" stroke-width="1.5"/>
          <line x1="299" y1="1" x2="1" y2="279" stroke="#c8c4bc" stroke-width="1.5"/>
        </svg>
      </div>
      <p class="bs-name">FEATURED PRODUCT <?= $i ?></p>
      <p class="bs-price">₱0</p>
    </a>
    <?php 
        endfor;
    endif; 
    ?>
  </div>
 
  <div class="bs-cta">
    <a href="<?= base_url('catalog') ?>" class="btn-viewall">VIEW ALL PRODUCTS</a>
  </div>
</section>
 
<!-- ═══════════════════════════════════════
     LOCATIONS + IMAGE SPLIT
═══════════════════════════════════════ -->
<div class="location-split">
 
  <!-- LEFT: location text box -->
  <div class="location-left">
    <div class="location-box">
      <p class="location-label">Our Locations</p>
      <p class="location-address">67 Street<br>Bulacan,<br>Manila 2027</p>
    </div>
  </div>
 
  <!-- RIGHT: image placeholder -->
  <div class="location-right">
    <div class="location-img-placeholder"></div>
  </div>
 
</div>
 
<!-- ═══════════════════════════════════════
     CONTACT + IMAGE SPLIT
═══════════════════════════════════════ -->
<div class="contact-split" id="contact-section">
 
  <!-- LEFT: image placeholder -->
  <div class="contact-img-placeholder"></div>
 
  <!-- RIGHT: contact form -->
  <div class="contact-right">
    <h2 class="contact-heading">CONTACT US</h2>
 
    <form class="contact-form" method="POST" action="<?= base_url('contact/send') ?>">
      <?= csrf_field() ?>
 
      <div class="contact-field">
        <label class="contact-label">Full Name</label>
        <input type="text" name="full_name" class="contact-input" required/>
      </div>
      <div class="contact-field">
        <label class="contact-label">Email Address</label>
        <input type="email" name="email" class="contact-input" required/>
      </div>
      <div class="contact-field">
        <label class="contact-label">Concern</label>
        <textarea name="concern" class="contact-input contact-textarea" rows="3" required></textarea>
      </div>
 
      <button type="submit" class="btn-submit">SUBMIT</button>
    </form>
 
    <p class="contact-need">NEED ANYTHING?</p>
    <a href="mailto:addictechthebest@gmail.com" class="contact-email">addictechthebest@gmail.com</a>
 
    <!-- social icons -->
    <div class="contact-socials">
      <a href="#" class="contact-social" aria-label="Facebook">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
        </svg>
      </a>
      <a href="#" class="contact-social" aria-label="Twitter">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
      </a>
      <a href="#" class="contact-social" aria-label="Instagram">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <circle cx="12" cy="12" r="4"/>
          <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
        </svg>
      </a>
    </div>
 
    <p class="contact-handle">@addictechthebest</p>
  </div>
 
</div>

<!-- Smooth Scroll JavaScript -->
<script src="<?= base_url('/public/js/homepage.js') ?>"></script>
</body>
<?= $this->endSection() ?>