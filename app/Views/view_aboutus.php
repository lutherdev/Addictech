<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – About Us</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/aboutus.css') ?>" />
</head>
<body>

<div class="about-container">
  
  <!-- Header -->
  <div class="about-header">
    <h1 class="about-title">ABOUT US</h1>
    <p class="about-subtitle">The team behind addictech</p>
  </div>

  <!-- Team Members -->
  <div class="team-section">
    
    <!-- Member 1 -->
<div class="member-card">
  <div class="member-avatar">
    <?php 
    $imagePath = FCPATH . 'public/images/';
    $imageFile = 'lutherpic.jpg';
    
    if (file_exists($imagePath . $imageFile)): 
    ?>
      <img src="<?= base_url('public/images/lutherpic.jpg') ?>" alt="Luther Dean M. Sambeli" class="member-photo">
    <?php else: ?>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="12" cy="10" r="3"/>
        <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
      </svg>
    <?php endif; ?>
  </div>
  <h2 class="member-name">Luther Dean M. Sambeli</h2>
  <p class="member-role">Lead Developer</p>
  <p class="member-bio">Full-stack developer</p>
</div>

    <!-- Member 2 -->
<div class="member-card">
  <div class="member-avatar">
    <?php 
    $imagePath = FCPATH . 'public/images/';
    $imageFile = 'cjpic.jpg';
    
    if (file_exists($imagePath . $imageFile)): 
    ?>
      <img src="<?= base_url('public/images/cjpic.jpg') ?>" alt="Christian Joshua S. Molina" class="member-photo">
    <?php else: ?>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="12" cy="10" r="3"/>
        <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
      </svg>
    <?php endif; ?>
  </div>
  <h2 class="member-name">Christian Joshua S. Molina</h2>
  <p class="member-role">Frontend Developer</p>
  <p class="member-bio">Frontend designs for the system</p>
</div>

    <!-- Member 3 -->
<div class="member-card">
  <div class="member-avatar">
    <?php 
    $imagePath = FCPATH . 'public/images/';
    $imageFile = 'pingpic.png';
    
    if (file_exists($imagePath . $imageFile)): 
    ?>
      <img src="<?= base_url('public/images/pingpic.png') ?>" alt="Ping-Wei H. Chen" class="member-photo">
    <?php else: ?>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="12" cy="10" r="3"/>
        <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
      </svg>
    <?php endif; ?>
  </div>
  <h2 class="member-name">Ping-Wei H. Chen</h2>
  <p class="member-role">Database/Frontend</p>
  <p class="member-bio">Arranges the database and designs for the system</p>
</div>

    <!-- Member 1 -->
<div class="member-card">
  <div class="member-avatar">
    <?php 
    $imagePath = FCPATH . 'public/images/';
    $imageFile = 'jaypic.jpg';
    
    if (file_exists($imagePath . $imageFile)): 
    ?>
      <img src="<?= base_url('public/images/jaypic.jpg') ?>" alt="Jaymard T. Licas" class="member-photo">
    <?php else: ?>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="12" cy="10" r="3"/>
        <path d="M6 21v-1a6 6 0 0 1 12 0v1"/>
      </svg>
    <?php endif; ?>
  </div>
  <h2 class="member-name">Jaymard T. Licas</h2>
  <p class="member-role">Frontend Developer</p>
  <p class="member-bio">Frontend designs for the system</p>
</div>

  </div>

  <!-- Optional: Admin upload note (only visible to admins) -->
  <?php if (session()->get('isAdmin')): ?>
  <div class="upload-note">
    <p>📷 To add team photos, upload images to: <code>/public/uploads/team/</code> with filenames: luther.jpg, christian.jpg, pingwei.jpg, jaymard.jpg</p>
  </div>
  <?php endif; ?>

</div>

</body>
<?= $this->endSection() ?>