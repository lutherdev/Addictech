    const fileInput = document.getElementById('image');
    const uploadArea = document.getElementById('imageUploadArea');
    const previewImg = document.getElementById('imagePreviewImg');
    const placeholder = document.getElementById('imagePlaceholder');

    uploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
      };
      reader.readAsDataURL(file);
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.classList.add('drag-over');
    });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.classList.remove('drag-over');
      const file = e.dataTransfer.files[0];
      if (file) {
        fileInput.files = e.dataTransfer.files;
        const reader = new FileReader();
        reader.onload = (ev) => {
          previewImg.src = ev.target.result;
          previewImg.style.display = 'block';
          if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });