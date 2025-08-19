<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management - PHITSOL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <style>
        /* Essential slider management styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: #343a40;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        
        .sidebar-brand:hover {
            color: #667eea;
            text-decoration: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }
        
        .nav-link.active {
            color: white;
            background: #667eea;
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }
        
        /* Table Header Styles */
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .table-info {
            flex: 1;
        }
        
        .table-title {
            margin: 0 0 0.5rem 0;
            color: #495057;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .table-description {
            margin: 0;
            color: #6c757d;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }
        
        /* Header */
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 0 0 15px 15px;
        }
        
        .admin-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
            margin: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
        }
        
        .card-title {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        /* Slider Preview */
        .slider-preview {
            position: relative;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            height: 400px;
        }
        
        .preview-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }
        
        .preview-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .preview-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .preview-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .slider-container {
            position: relative;
            height: 100%;
        }
        
        .slide-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .slide-item.active {
            opacity: 1;
        }
        
        .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 2rem;
        }
        
        .slide-overlay h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
        }
        
        .slide-overlay p {
            margin: 0 0 1rem 0;
            opacity: 0.9;
        }
        
        .slide-overlay .btn {
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .slider-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }
        
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .indicator.active {
            background: white;
        }
        
        /* Slide Cards */
        .slide-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .slide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .slide-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .slide-content {
            padding: 1rem;
        }
        
        .slide-title {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
            color: #343a40;
        }
        
        .slide-description {
            margin: 0 0 1rem 0;
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .slide-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .slide-order {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .slide-status {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .slide-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 0.5rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-edit:hover {
            background: #e0a800;
            color: #212529;
            text-decoration: none;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }

        .btn-status {
            background: #17a2b8;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-status:hover {
            background: #138496;
            color: white;
            text-decoration: none;
        }

        .btn-status:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Loading spinner */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-state h4 {
            margin-bottom: 0.5rem;
            color: #343a40;
        }
        
        .empty-state p {
            margin-bottom: 2rem;
        }
        

        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php?action=dashboard" class="sidebar-brand">
                <img 
                    src="assets/img/logo_white.png?v=<?php echo uniqid(); ?>"
                    alt="PHITSOL Logo"
                    class="phitsol-logo"
                    id="phitsol-logo"
                >
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php?action=dashboard" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="index.php?action=slider" class="nav-link active">
                <i class="fas fa-images"></i>
                Slider Management
            </a>
            <a href="index.php?action=blog" class="nav-link">
                <i class="fas fa-blog"></i>
                Blog Management
            </a>
            <a href="index.php?action=users" class="nav-link">
                <i class="fas fa-users"></i>
                User Management
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Content Header -->
        <div class="content-header">
            <div class="header-info">
                <h1 class="page-title">
                    <i class="fas fa-images"></i>
                    Slider Management
                </h1>
                <div class="stats-info">
                    <span class="stat-item"><?php echo count($slides); ?> slides</span>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'active'; })); ?> active</span>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'inactive'; })); ?> inactive</span>
                </div>
            </div>
            <div class="header-actions">
                <a href="index.php?action=slider&method=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Slide
                </a>
            </div>
        </div>

        <!-- Session Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Content Body -->
        <div class="content-body">
            <!-- Statistics Grid -->
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo count($slides); ?></div>
                            <div class="stat-label">Total Slides</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'active'; })); ?></div>
                            <div class="stat-label">Active Slides</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'inactive'; })); ?></div>
                            <div class="stat-label">Inactive Slides</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider Preview -->
            <?php if (!empty($slides)): ?>
            <div class="table-container">
                <div class="table-header">
                    <div class="table-info">
                        <h5 class="table-title">
                            <i class="fas fa-eye me-2"></i>
                            Live Preview
                        </h5>
                        <p class="table-description">
                            Interactive preview of your slider. Use the controls to navigate through slides and see how they will appear to visitors.
                        </p>
                    </div>
                    <div class="table-actions">
                        <!-- Additional actions can be added here if needed -->
                    </div>
                </div>
            
            <div class="card-body">
                <div class="slider-preview">
                    <div class="preview-header">
                        <span>Slider Preview</span>
                        <div class="preview-controls">
                            <button class="preview-btn" id="prevBtn" title="Previous">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="preview-btn" id="playBtn" title="Play/Pause">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="preview-btn" id="nextBtn" title="Next">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="slider-container">
                        <?php foreach ($slides as $index => $slide): ?>
                            <div class="slide-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php if (!empty($slide['image'])): ?>
                                    <img src="../uploads/slider/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="slide-overlay">
                                    <h3><?php echo htmlspecialchars($slide['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($slide['description']); ?></p>
                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($slide['button_url']); ?>" class="btn" target="_blank">
                                            <?php echo htmlspecialchars($slide['button_text']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="slider-indicators">
                        <?php foreach ($slides as $index => $slide): ?>
                            <div class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

            <!-- Slides List -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-info">
                        <h5 class="table-title">
                            <i class="fas fa-list me-2"></i>
                            Slides List
                        </h5>
                        <p class="table-description">
                            Manage your slider slides. Drag and drop to reorder, or use the action buttons to edit, toggle status, or delete slides.
                        </p>
                    </div>
                    <div class="table-actions">
                        <!-- Additional actions can be added here if needed -->
                    </div>
                </div>
            
            <div class="card-body">
                <?php if (empty($slides)): ?>
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h4>No slides yet</h4>
                        <p>Create your first slide to get started</p>
                        <a href="index.php?action=slider&method=create" class="btn-add">
                            <i class="fas fa-plus me-2"></i>
                            Add First Slide
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row" id="slidesContainer">
                        <?php foreach ($slides as $slide): ?>
                            <div class="col-md-6 col-lg-4 mb-3" data-id="<?php echo $slide['_id']; ?>">
                                <div class="slide-card">
                                    <?php if (!empty($slide['image'])): ?>
                                        <img src="../uploads/slider/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="slide-image">
                                    <?php else: ?>
                                        <div class="slide-image" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="slide-content">
                                        <h6 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h6>
                                        <p class="slide-description"><?php echo htmlspecialchars($slide['description']); ?></p>
                                        
                                        <div class="slide-meta">
                                            <span class="slide-order">Order: <?php echo $slide['order']; ?></span>
                                            <span class="slide-status status-<?php echo $slide['status']; ?>">
                                                <?php echo ucfirst($slide['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="slide-actions">
                                            <button type="button" class="btn-action btn-status" title="Toggle Status" onclick="toggleSlideStatus('<?php echo $slide['_id']; ?>', '<?php echo $slide['status']; ?>')">
                                                <i class="fas fa-<?php echo $slide['status'] === 'active' ? 'eye' : 'eye-slash'; ?>"></i>
                                            </button>
                                            <a href="index.php?action=slider&method=edit&id=<?php echo $slide['_id']; ?>" class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?action=slider&method=delete&id=<?php echo $slide['_id']; ?>" class="btn-action btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this slide?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Slider Preview Functionality
        let currentSlide = 0;
        let slideInterval;
        let isPlaying = false;
        const slides = document.querySelectorAll('.slide-item');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            if (slides[index]) {
                slides[index].classList.add('active');
                indicators[index].classList.add('active');
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        function togglePlay() {
            const playBtn = document.getElementById('playBtn');
            const playIcon = playBtn.querySelector('i');
            
            if (isPlaying) {
                clearInterval(slideInterval);
                playIcon.className = 'fas fa-play';
                isPlaying = false;
            } else {
                slideInterval = setInterval(nextSlide, 3000);
                playIcon.className = 'fas fa-pause';
                isPlaying = true;
            }
        }

        // Event listeners for slider controls
        document.getElementById('nextBtn').addEventListener('click', nextSlide);
        document.getElementById('prevBtn').addEventListener('click', prevSlide);
        document.getElementById('playBtn').addEventListener('click', togglePlay);

        // Event listeners for indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        // Auto-play on load
        if (totalSlides > 1) {
            togglePlay();
        }

        // Slide Status Toggle Functionality
        function toggleSlideStatus(slideId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const statusText = newStatus === 'active' ? 'activate' : 'deactivate';
            
            if (confirm(`Are you sure you want to ${statusText} this slide?`)) {
                // Show loading state
                const button = event.target.closest('.btn-status');
                const icon = button.querySelector('i');
                const originalIcon = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
                button.disabled = true;

                // Make AJAX request
                fetch(`index.php?action=slider&method=toggleStatus&id=${slideId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Update the status display
                        const statusElement = button.closest('.slide-card').querySelector('.slide-status');
                        statusElement.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusElement.className = `slide-status status-${newStatus}`;
                        
                        // Update the button icon
                        icon.className = `fas fa-${newStatus === 'active' ? 'eye' : 'eye-slash'}`;
                        
                        // Update the onclick attribute
                        button.onclick = () => toggleSlideStatus(slideId, newStatus);
                        
                        // Show success message
                        showAlert('Slide status updated successfully!', 'success');
                        
                        // Reload page after a short delay to update stats
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error('Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error updating slide status:', error);
                    // Restore original state
                    icon.className = originalIcon;
                    button.disabled = false;
                    showAlert('Failed to update slide status.', 'error');
                });
            }
        }

        // Alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert at the top of main content
            const mainContent = document.querySelector('.main-content');
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Drag and Drop Functionality
        const slidesContainer = document.getElementById('slidesContainer');
        if (slidesContainer) {
            new Sortable(slidesContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    const slideIds = Array.from(slidesContainer.children).map(item => item.dataset.id);
                    
                    fetch('index.php?action=slider&method=reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ order: slideIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                                <i class="fas fa-check-circle me-2"></i>
                                Slide order updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.querySelector('.main-content').insertBefore(alert, document.querySelector('.stats-cards'));
                            
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating slide order:', error);
                    });
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            } else if (e.key === ' ') {
                e.preventDefault();
                togglePlay();
            }
        });

        // Logo reload functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Logo is set to white for admin pages
    
            
            // Force reload logo if not displaying correctly
            setTimeout(function() {
                const logo = document.querySelector('.phitsol-logo');
                if (logo) {
                    // Force reload the logo image
                    const currentSrc = logo.src;
                    logo.src = '';
                    logo.src = currentSrc;
                    
                    // Ensure logo is visible
                    logo.style.visibility = 'visible';
                    logo.style.opacity = '1';
                    logo.style.display = 'block';
                }
            }, 100);
        });
    </script>
</body>
</html> 