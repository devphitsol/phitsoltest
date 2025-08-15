<!-- Content Header -->
<div class="content-header">
    <div class="header-info">
        <h1 class="page-title">
            <i class="fas fa-plus"></i>
            Create Blog Post
        </h1>
        <div class="stats-info">
            <span class="stat-item">Add new blog post</span>
        </div>
    </div>
    <div class="header-actions">
        <a href="index.php?action=blog" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Posts
        </a>
    </div>
</div>

<!-- Content Body -->
<div class="content-body">
    <div class="form-container">
        <div class="form-title">
            <i class="fas fa-edit"></i>
            New Blog Post
        </div>
        
        <form action="index.php?action=blog&method=create" method="POST" enctype="multipart/form-data" id="blogForm">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="title" class="form-label">Post Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="200" placeholder="Enter blog post title">
                        <div class="form-text">Enter a compelling title for your blog post (max 200 characters)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-control" id="content" name="content" rows="15" required placeholder="Write your blog post content here"></textarea>
                        <div class="form-text">Write your blog post content here. You can use HTML formatting.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" placeholder="Brief summary of the post (optional)"></textarea>
                        <div class="form-text">A short summary of your blog post (optional)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control" id="category" name="category">
                                    <option value="">Select Category</option>
                                    <?php if (isset($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Choose a category for your post</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                                <div class="form-text">Choose whether this post should be published immediately</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags separated by commas">
                        <div class="form-text">Enter relevant tags separated by commas (e.g., technology, business, marketing)</div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <div class="file-upload-area" id="fileUpload">
                            <div class="file-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag image here</p>
                                <small>Recommended: 1200x630px, Max 5MB</small>
                            </div>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*" style="display: none;">
                        </div>
                        <div class="form-text">Upload a featured image for your blog post (optional)</div>
                    </div>
                    
                    <div id="imagePreview" class="image-preview" style="display: none;">
                        <img id="previewImg" class="preview-image" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="60" placeholder="SEO meta title">
                        <div class="form-text">SEO meta title (max 60 characters)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160" placeholder="SEO meta description"></textarea>
                        <div class="form-text">SEO meta description (max 160 characters)</div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="index.php?action=blog" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Post
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUpload = document.getElementById('fileUpload');
    const fileInput = document.getElementById('featured_image');
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
                fileUpload.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            alert('Please select a valid image file.');
        }
    }

    window.removeImage = function() {
        fileInput.value = '';
        imagePreview.style.display = 'none';
        fileUpload.style.display = 'block';
    };

    // Form validation
    const blogForm = document.getElementById('blogForm');
    if (blogForm) {
        blogForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            const image = document.getElementById('featured_image').files[0];
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a blog post title');
                document.getElementById('title').focus();
                return;
            }
            
            if (!content) {
                e.preventDefault();
                alert('Please enter blog post content');
                document.getElementById('content').focus();
                return;
            }
            
            if (image && image.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Image file size must be less than 5MB');
                return;
            }
        });
    }
});
</script> 