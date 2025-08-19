<!-- Content Header -->
<div class="content-header">
    <div class="header-info">
        <h1 class="page-title">
            <i class="fas fa-plus"></i>
            Create New Slide
        </h1>
        <div class="stats-info">
            <span class="stat-item">Add new slider image</span>
        </div>
    </div>
    <div class="header-actions">
        <a href="index.php?action=slider" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Slides
        </a>
    </div>
</div>

<!-- Content Body -->
<div class="content-body">
    <div class="form-container">
        <div class="form-title">
            <i class="fas fa-plus"></i>
            Add New Slide
        </div>
        
        <form action="index.php?action=slider&method=create" method="POST" enctype="multipart/form-data" id="slideForm">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="title" class="form-label">Slide Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="100" placeholder="Enter slide title">
                        <div class="form-text">Enter a compelling title for your slide (max 100 characters)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="500" placeholder="Enter slide description"></textarea>
                        <div class="form-text">Brief description of the slide content (max 500 characters)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control" id="button_text" name="button_text" maxlength="50" placeholder="Enter button text">
                                <div class="form-text">Text for the call-to-action button</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_url" class="form-label">Button URL</label>
                                <input type="url" class="form-control" id="button_url" name="button_url" maxlength="255" placeholder="https://example.com">
                                <div class="form-text">URL where the button should link to</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="form-text">Choose whether this slide should be visible on the website</div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="image" class="form-label">Slide Image *</label>
                        <div class="file-upload-area" id="fileUpload">
                            <div class="file-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag image here</p>
                                <small>Recommended: 1920x800px, Max 5MB</small>
                            </div>
                            <input type="file" id="image" name="image" accept="image/*" required style="display: none;">
                        </div>
                        <div class="form-text">Upload a high-quality image for your slide</div>
                    </div>
                    
                    <div id="imagePreview" style="display: none;">
                        <div class="image-preview">
                            <img id="previewImg" class="preview-image" alt="Preview">
                            <button type="button" class="remove-image" id="changeImageBtn" title="Change Image">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="index.php?action=slider" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Slide
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUpload = document.getElementById('fileUpload');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const changeImageBtn = document.getElementById('changeImageBtn');

    if (fileUpload && fileInput) {
        // Click to upload
        fileUpload.addEventListener('click', () => fileInput.click());

        // Drag and drop functionality
        fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUpload.classList.add('dragover');
        });

        fileUpload.addEventListener('dragleave', () => {
            fileUpload.classList.remove('dragover');
        });

        fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        // Change image button
        if (changeImageBtn) {
            changeImageBtn.addEventListener('click', () => {
                fileInput.click();
            });
        }
    }

    function handleFileSelect(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imagePreview.style.display = 'block';
            fileUpload.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    // Form validation
    const slideForm = document.getElementById('slideForm');
    if (slideForm) {
        slideForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const image = document.getElementById('image').files[0];
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a slide title.');
                document.getElementById('title').focus();
                return;
            }
            
            if (!image) {
                e.preventDefault();
                alert('Please select an image for the slide.');
                return;
            }
            
            // Validate image size
            if (image.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Image file size must be less than 5MB.');
                return;
            }
        });
    }
});
</script> 