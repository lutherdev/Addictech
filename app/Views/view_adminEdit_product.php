<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<head>
  <title>addictech – Edit Product</title>
  <link rel="stylesheet" href="<?= base_url('/public/css/products_edit.css') ?>" />
</head>
<body>

  <div class="edit-product-page">

    <!-- Main Content Area -->
    <div class="edit-product-main">

      <!-- Title Row -->
      <div class="edit-product-title-row">
        <h1 class="page-title">EDIT PRODUCT</h1>
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
        <form action="<?= base_url('products/update/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="form-grid">

            <!-- Left Column -->
            <div class="form-col">

              <div class="form-group">
                <label for="name">PRODUCT NAME</label>
                <input type="text" id="name" name="name"
                  value="<?= old('name', esc($product['name'])) ?>" required />
              </div>

              <div class="form-group">
                <label for="category">CATEGORY</label>
                <select id="category" name="category" required>
                  <?php
                    $categories = ['KEYBOARD', 'MOUSE', 'HEADSET', 'MONITOR', 'SPEAKER', 'WEB CAM'];
                    foreach ($categories as $cat):
                      $selected = old('category', $product['category']) === $cat ? 'selected' : '';
                  ?>
                    <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="variant">VARIANT <span class="optional">(optional)</span></label>
                <input type="text" id="variant" name="variant" placeholder="e.g. Black / RGB"
                  value="<?= old('variant', esc($product['variant'] ?? '')) ?>" />
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="price">PRICE (₱)</label>
                  <input type="number" id="price" name="price" min="1" step="0.01"
                    value="<?= old('price', esc($product['price'])) ?>" required />
                </div>

                <div class="form-group">
                  <label for="stock">STOCK</label>
                  <input type="number" id="stock" name="stock" min="0"
                    value="<?= old('stock', esc($product['stock'] ?? 0)) ?>" />
                </div>
              </div>

              <div class="form-group">
                <label for="status">STATUS</label>
                <select id="status" name="status">
                  <option value="active"   <?= old('status', $product['status']) === 'active'   ? 'selected' : '' ?>>ACTIVE</option>
                  <option value="inactive" <?= old('status', $product['status']) === 'inactive' ? 'selected' : '' ?>>INACTIVE</option>
                </select>
              </div>

            </div>

            <!-- Right Column -->
            <div class="form-col">

              <div class="form-group">
                <label for="description">DESCRIPTION <span class="optional">(optional)</span></label>
                <textarea id="description" name="description" rows="5"
                  placeholder="Product description..."><?= old('description', esc($product['description'] ?? '')) ?></textarea>
              </div>

              <div class="form-group">
                <label>PRODUCT IMAGE</label>

                <!-- Current image preview -->
                <div class="current-image-wrap">
                  <?php if (!empty($product['image'])): ?>
                    <div class="current-image-label">CURRENT IMAGE</div>
                    <img src="<?= base_url('uploads/' . esc($product['image'])) ?>"
                         alt="Current product image" class="current-image" id="imagePreviewImg" />
                  <?php else: ?>
                    <div class="product-image-placeholder" id="imagePlaceholder">
                      <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="10" width="52" height="44" rx="2" stroke="currentColor" stroke-width="2"/>
                        <circle cx="22" cy="26" r="6" stroke="currentColor" stroke-width="2"/>
                        <path d="M6 46l14-14 10 10 8-8 20 16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                      </svg>
                      <span>NO IMAGE</span>
                    </div>
                    <img src="" alt="Preview" class="current-image" id="imagePreviewImg" style="display:none;" />
                  <?php endif; ?>
                </div>

                <!-- Upload new image -->
                <div class="image-upload-area" id="imageUploadArea">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span>UPLOAD NEW IMAGE</span>
                  <input type="file" id="image" name="image" accept="image/*" class="file-input" />
                </div>
                <p class="field-hint">Leave empty to keep current image. Accepted: JPG, PNG, WEBP — Max 2MB</p>
              </div>

            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <a href="<?= base_url('products') ?>" class="btn-cancel">CANCEL</a>
            <button type="submit" class="btn-submit">SAVE CHANGES</button>
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
    <script src="<?= base_url('/public/js/products_edit.js') ?>"></script>
</body>
<?= $this->endSection() ?>