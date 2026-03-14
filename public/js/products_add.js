        // Image preview on file select
        const fileInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const uploadArea = document.getElementById('imageUploadArea');
    
        uploadArea.addEventListener('click', () => fileInput.click());
    
        fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
    
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview" />`;
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
            imagePreview.innerHTML = `<img src="${ev.target.result}" alt="Preview" />`;
            };
            reader.readAsDataURL(file);
        }
        });