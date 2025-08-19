<?php
// Check if user is logged in and is admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php?action=login');
    exit;
}

// Check if slide data is available
if (!isset($slide) || !$slide) {
    header('Location: index.php?action=slider');
    exit;
}
?>

<!-- Content Header -->
<div class="content-header">
    <div class="header-info">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Edit Slide
        </h1>
        <div class="stats-info">
            <span class="stat-item">Editing: <?php echo htmlspecialchars($slide['title']); ?></span>
            <span class="stat-divider">•</span>
            <span class="stat-item">Status: <?php echo ucfirst($slide['status'] ?? 'active'); ?></span>
        </div>
    </div>
    <div class="header-actions">
        <a href="index.php?action=slider" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Slides
        </a>
    </div>
</div>

<!-- Content Body -->
<div class="content-body">
    <div class="row">
        <div class="col-12">

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Slide: <?php echo htmlspecialchars($slide['title']); ?>
                </h5>
            </div>
            
            <div class="card-body">
                <form action="index.php?action=slider&method=edit&id=<?php echo $slide['_id']; ?>" method="POST" enctype="multipart/form-data" id="slideForm">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Slide Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($slide['title']); ?>" required maxlength="100">
                                <div class="form-text">Enter a compelling title for your slide (max 100 characters)</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?php echo htmlspecialchars($slide['description'] ?? ''); ?></textarea>
                                <div class="form-text">Brief description of the slide content (max 500 characters)</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="button_text" class="form-label">Button Text</label>
                                        <input type="text" class="form-control" id="button_text" name="button_text" value="<?php echo htmlspecialchars($slide['button_text'] ?? ''); ?>" maxlength="50">
                                        <div class="form-text">Text for the call-to-action button</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="button_url" class="form-label">Button URL</label>
                                        <input type="url" class="form-control" id="button_url" name="button_url" value="<?php echo htmlspecialchars($slide['button_url'] ?? ''); ?>" maxlength="255">
                                        <div class="form-text">URL where the button should link to</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="active" <?php echo ($slide['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($slide['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                        <div class="form-text">Choose whether this slide should be visible on the website</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="order" name="order" value="<?php echo $slide['order'] ?? 1; ?>" min="1" max="10">
                                        <div class="form-text">Order in which this slide appears (1-10)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="form-group mb-3">
                                <label for="image" class="form-label">Slide Image</label>
                                
                                <?php if (!empty($slide['image'])): ?>
                                    <div class="current-image mb-3">
                                        <label class="form-label">Current Image:</label>
                                        <div class="current-image-container">
                                            <img src="../uploads/slider/<?php echo htmlspecialchars($slide['image']); ?>" 
                                                 alt="Current slide image" 
                                                 class="img-fluid rounded"
                                                 style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="file-upload" id="fileUpload">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <div class="file-upload-text">Click to upload new image or drag here</div>
                                    <div class="file-upload-hint">Recommended: 1920x800px, Max 5MB</div>
                                    <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="removeImage" name="remove_image" value="1">
                                    <label class="form-check-label" for="removeImage">
                                        Remove current image
                                    </label>
                                </div>
                                <div class="form-text">Upload a new image to replace the current one (optional)</div>
                            </div>
                            
                            <div id="imagePreview" style="display: none;">
                                <img id="previewImg" class="img-fluid rounded" alt="Preview">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions mt-4 pt-3 border-top">
                        <a href="index.php?action=slider" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Slides
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Slide
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<style>
/* File Upload Styles */
.file-upload {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #f8f9fa;
}

.file-upload:hover {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
}

.file-upload.dragover {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.1);
}

.file-upload i {
    font-size: 2rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.file-upload-text {
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.file-upload-hint {
    font-size: 0.875rem;
    color: #adb5bd;
}

/* Current Image Styles */
.current-image-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 0.5rem;
    background: #f8f9fa;
    text-align: center;
}

/* Image Preview */
#imagePreview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    margin-top: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Form Styles */
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
    border-color: #5a6268;
    transform: translateY(-1px);
}

/* Card Body Width */
.card-body {
    max-width: 100%;
    width: 100%;
    padding: 2rem;
}

/* Form Layout Improvements */
.form-group {
    margin-bottom: 1.5rem;
}

.form-control {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* Responsive Design */
@media (max-width: 768px) {
    .file-upload {
        padding: 1.5rem;
    }
    
    .file-upload i {
        font-size: 1.5rem;
    }
    
    .file-upload-text {
        font-size: 0.9rem;
    }
    
    .file-upload-hint {
        font-size: 0.8rem;
    }
    
    .current-image-container img {
        max-height: 150px;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}
</style>

<script>
// File Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileUpload = document.getElementById('fileUpload');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (fileUpload && fileInput) {
        fileUpload.addEventListener('click', () => fileInput.click());

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

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });
    }

    function handleFileSelect(file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Form Validation
    const slideForm = document.getElementById('slideForm');
    const removeImageCheckbox = document.getElementById('removeImage');
    
    if (slideForm) {
        slideForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const image = document.getElementById('image').files[0];
            const order = document.getElementById('order').value;
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a slide title');
                return;
            }
            
            if (image && image.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Image file size must be less than 5MB');
                return;
            }
            
            if (order < 1 || order > 10) {
                e.preventDefault();
                alert('Display order must be between 1 and 10');
                return;
            }
        });
    }
    
    // Handle remove image checkbox
    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            const fileUpload = document.getElementById('fileUpload');
            const imageInput = document.getElementById('image');
            
            if (this.checked) {
                fileUpload.style.opacity = '0.5';
                fileUpload.style.pointerEvents = 'none';
                imageInput.disabled = true;
            } else {
                fileUpload.style.opacity = '1';
                fileUpload.style.pointerEvents = 'auto';
                imageInput.disabled = false;
            }
        });
    }
});
</script> 