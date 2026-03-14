<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Add Product</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/products_add.css') ?>" />
</head>
<body>
 
  <div class="add-product-page">
 
    <!-- Main Content Area -->
    <div class="add-product-main">
 
      <!-- Title Row -->
      <div class="add-product-title-row">
        <h1 class="page-title">ADD PRODUCT</h1>
        <a href="<?= base_url('products') ?>" class="btn-back">← BACK</a>
      </div>
 
      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('errors')) : ?>
        <div class="flash flash-error">
          <?= session()->getFlashdata('errors') ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')) : ?>
        <div class="flash flash-success">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>
 
      <!-- Form -->
      <div class="form-wrapper">
        <form action="<?= base_url('products/insert') ?>" method="POST" enctype="multipart/form-data">
          <?= csrf_field() ?>
 
          <div class="form-grid">
 
            <!-- Left Column -->
            <div class="form-col">
 
              <div class="form-group">
                <label for="name">PRODUCT NAME</label>
                <input type="text" id="name" name="name" placeholder="e.g. MK Pro X"
                  value="<?= old('name') ?>" required />
              </div>
 
              <div class="form-group">
                <label for="category">CATEGORY</label>
                <select id="category" name="category" required>
                  <option value="" disabled selected>Select category</option>
                  <option value="KEYBOARD"  <?= old('category') === 'KEYBOARD'  ? 'selected' : '' ?>>KEYBOARD</option>
                  <option value="MOUSE"     <?= old('category') === 'MOUSE'     ? 'selected' : '' ?>>MOUSE</option>
                  <option value="HEADSET"   <?= old('category') === 'HEADSET'   ? 'selected' : '' ?>>HEADSET</option>
                  <option value="MONITOR"   <?= old('category') === 'MONITOR'   ? 'selected' : '' ?>>MONITOR</option>
                  <option value="SPEAKER"   <?= old('category') === 'SPEAKER'   ? 'selected' : '' ?>>SPEAKER</option>
                  <option value="WEB CAM"   <?= old('category') === 'WEB CAM'   ? 'selected' : '' ?>>WEB CAM</option>
                </select>
              </div>
 
              <div class="form-group">
                <label for="variant">VARIANT <span class="optional">(optional)</span></label>
                <input type="text" id="variant" name="variant" placeholder="e.g. Black / RGB"
                  value="<?= old('variant') ?>" />
              </div>
 
              <div class="form-row">
                <div class="form-group">
                  <label for="price">PRICE (₱)</label>
                  <input type="number" id="price" name="price" placeholder="0" min="1" step="0.01"
                    value="<?= old('price') ?>" required />
                </div>
 
                <div class="form-group">
                  <label for="stock">STOCK</label>
                  <input type="number" id="stock" name="stock" placeholder="0" min="0"
                    value="<?= old('stock') ?? 0 ?>" />
                </div>
              </div>
 
              <div class="form-group">
                <label for="status">STATUS</label>
                <select id="status" name="status">
                  <option value="active"   <?= old('status', 'active') === 'active'   ? 'selected' : '' ?>>ACTIVE</option>
                  <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>INACTIVE</option>
                </select>
              </div>
 
            </div>
 
            <!-- Right Column -->
            <div class="form-col">
 
              <div class="form-group">
                <label for="description">DESCRIPTION <span class="optional">(optional)</span></label>
                <textarea id="description" name="description" rows="5"
                  placeholder="Product description..."><?= old('description') ?></textarea>
              </div>
 
              <div class="form-group">
                <label for="image">PRODUCT IMAGE <span class="optional">(optional)</span></label>
                <div class="image-upload-area" id="imageUploadArea">
                  <div class="image-preview" id="imagePreview">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <rect x="6" y="10" width="52" height="44" rx="2" stroke="currentColor" stroke-width="2"/>
                      <circle cx="22" cy="26" r="6" stroke="currentColor" stroke-width="2"/>
                      <path d="M6 46l14-14 10 10 8-8 20 16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>Click to upload image</span>
                  </div>
                  <input type="file" id="image" name="image" accept="image/*" class="file-input" />
                </div>
                <p class="field-hint">Accepted: JPG, PNG, WEBP — Max 2MB</p>
              </div>
 
            </div>
          </div>
 
          <!-- Form Actions -->
          <div class="form-actions">
            <a href="<?= base_url('products') ?>" class="btn-cancel">CANCEL</a>
            <button type="submit" class="btn-submit">ADD PRODUCT</button>
          </div>
 
        </form>
      </div>
    </div>
 
    <!-- Right Sidebar Navigation -->
    <aside class="sidebar-nav">
      <nav>
        <ul>
          <li><a href="<?= base_url('products') ?>" class="active">PRODUCTS</a></li>
          <li><a href="<?= base_url('orders') ?>">ORDERS</a></li>
          <li><a href="<?= base_url('users') ?>">USERS</a></li>
          <li><a href="<?= base_url('logout') ?>">LOGOUT</a></li>
        </ul>
      </nav>
    </aside>
 
  </div>
    <script src="<?= base_url('/public/js/products_add.js') ?>"></script>
  </body>
<?= $this->endSection() ?>