<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Homepage</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/homepage.css') ?>" />
</head>
<body>
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
 
  <!-- RIGHT: stacked promo panels -->
  <div class="promo-stack">
 
    <!-- TOP promo -->
    <div class="promo-panel">
      <div class="promo-img">
        <div class="promo-img-placeholder">
          <!-- placeholder icon -->
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1">
            <rect x="3" y="3" width="18" height="18" rx="1"/>
            <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
      </div>
      <div>
        <span class="promo-panel-label">SHOP›</span>
        <p class="promo-panel-text">WE<br>THE<br>BEST</p>
      </div>
    </div>
 
    <!-- BOTTOM promo -->
    <div class="promo-panel">
      <div class="promo-img">
        <div class="promo-img-placeholder">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1">
            <rect x="3" y="3" width="18" height="18" rx="1"/>
            <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
      </div>
      <div>
        <p class="promo-panel-text">YOU<br>THE<br>BEST</p>
      </div>
    </div>
 
  </div>
 
</div>
</body>
<?= $this->endSection() ?>