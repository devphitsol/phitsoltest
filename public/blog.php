<?php
// Load Composer autoloader
require_once '../vendor/autoload.php';
require_once '../config/session.php';

use App\Models\Blog;

$blogModel = new Blog();

// Get search query
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';

// Check if user is logged in
$isLoggedIn = isLoggedIn();

// Get posts based on filters and user access
if (!$isLoggedIn) {
    // Non-logged in users can only see latest posts (max 3)
    $posts = $blogModel->getPublished(3);
} else {
    // Logged in users can access all features
    if (!empty($search)) {
        $posts = $blogModel->search($search);
    } elseif (!empty($category)) {
        $posts = $blogModel->getByCategory($category);
    } elseif (!empty($type)) {
        $posts = $blogModel->getByType($type);
    } else {
        $posts = $blogModel->getPublished();
    }
}

// Get categories for filter
$categories = $blogModel->getCategories();

// Increment view count for individual post view
if (isset($_GET['post'])) {
    $post = $blogModel->getBySlug($_GET['post']);
    if ($post) {
        $blogModel->incrementViews($post['_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - PHITSOL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="assets/css/blog.css?v=<?php echo time(); ?>" rel="stylesheet">

</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img 
                    src="assets/img/logo_black.png"
                    data-logo-black="assets/img/logo_black.png"
                    data-logo-white="assets/img/logo_white.png"
                    alt="PHITSOL Logo"
                    class="phitsol-logo"
                    id="phitsol-logo"
                >
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php if ($isLoggedIn): ?>blog.php<?php else: ?>index.php#blog<?php endif; ?>">
                            <?php if ($isLoggedIn): ?>Blog<?php else: ?>Latest Posts<?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="partners-dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user-circle me-2"></i>Profile
                                </a></li>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/">
                                        <i class="fas fa-cog me-2"></i>Admin Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($isLoggedIn): ?>
        <!-- Original Blog Content -->
            <?php if (isset($_GET['post']) && $post): ?>
                <!-- Individual Post View -->
                <div style="padding-top: 80px;">
                    <div class="container">
                        <div class="post-detail">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                     class="post-detail-image">
                            <?php endif; ?>
                            
                            <h1 class="post-detail-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                            
                            <div class="post-detail-meta">
                                <span><i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                <span><i class="fas fa-eye me-1"></i><?php echo $post['views'] ?? 0; ?> views</span>
                                <?php if (!empty($post['category'])): ?>
                                    <span><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($post['category']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-detail-content">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="blog.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Blog
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Blog List View -->
                <!-- Hero Section -->
                <section class="hero-section">
                    <div class="container">
                        <!-- Back to Dashboard Link -->
                        <div class="text-start mb-3">
                            <a href="partners-dashboard.php" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                        
                        <h1 class="hero-title">
                            Our Blog
                        </h1>
                        <p class="hero-subtitle">
                            Discover insights, updates, and stories from our team
                        </p>
                        
                        <!-- Search Box -->
                        <div class="search-box">
                            <form method="GET" action="blog.php">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search blog posts..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <!-- Filters Section -->
                <section class="filters-section">
                    <div class="container">
                        <div class="text-center">
                            <h5 class="mb-3">Filter by Category</h5>
                            <a href="blog.php" class="filter-btn <?php echo empty($category) && empty($type) && empty($search) ? 'active' : ''; ?>">
                                All Posts
                            </a>
                            <a href="blog.php?type=post" class="filter-btn <?php echo $type === 'post' ? 'active' : ''; ?>">
                                <i class="fas fa-file-alt me-1"></i>
                                Articles
                            </a>
                            <a href="blog.php?type=video" class="filter-btn <?php echo $type === 'video' ? 'active' : ''; ?>">
                                <i class="fas fa-video me-1"></i>
                                Videos
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a href="blog.php?category=<?php echo urlencode($cat); ?>" class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($cat); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- Blog Posts Section -->
                <section class="blog-section">
                    <div class="container">
                        <?php if (!empty($search)): ?>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4>Search Results for "<?php echo htmlspecialchars($search); ?>"</h4>
                                    <p class="text-muted">Found <?php echo count($posts); ?> post(s)</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($posts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>No posts found</h4>
                                <p class="text-muted">Try adjusting your search criteria or browse all posts.</p>
                                <a href="blog.php" class="btn btn-primary">View All Posts</a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="post-card">
                                            <?php if ($post['type'] === 'video' && !empty($post['video_url'])): ?>
                                                <div class="video-container">
                                                    <i class="fas fa-play-circle fa-3x"></i>
                                                    <div class="video-overlay">
                                                        <i class="fas fa-play fa-2x"></i>
                                                    </div>
                                                </div>
                                            <?php elseif (!empty($post['featured_image'])): ?>
                                                <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>?v=<?php echo time(); ?>" 
                                                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                                     class="post-image">
                                            <?php else: ?>
                                                <div class="post-image" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="post-content">
                                                <h5 class="post-title">
                                                    <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>">
                                                        <?php echo htmlspecialchars($post['title']); ?>
                                                    </a>
                                                </h5>
                                                <p class="post-excerpt">
                                                    <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 120)) . '...'; ?>
                                                </p>
                                                
                                                <div class="post-meta">
                                                    <span class="post-date">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                                    </span>
                                                    <span class="post-views">
                                                        <i class="fas fa-eye me-1"></i>
                                                        <?php echo $post['views'] ?? 0; ?>
                                                    </span>
                                                </div>
                                                
                                                <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>" class="read-more">
                                                    Read More
                                                    <i class="fas fa-arrow-right ms-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Public Blog View (Non-logged in users) -->
        <div style="padding-top: 80px;">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container">
                    <h1 class="hero-title">Latest Posts</h1>
                    <p class="hero-subtitle">Get a glimpse of our latest content. Sign in for full access.</p>
                </div>
            </section>

            <!-- Blog Posts Section -->
            <section class="blog-section">
                <div class="container">
                    <?php if (empty($posts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No posts found</h4>
                            <p class="text-muted">No latest posts available at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($posts as $post): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="post-card">
                                        <?php if ($post['type'] === 'video' && !empty($post['video_url'])): ?>
                                            <div class="video-container">
                                                <i class="fas fa-play-circle fa-3x"></i>
                                                <div class="video-overlay">
                                                    <i class="fas fa-play fa-2x"></i>
                                                </div>
                                            </div>
                                        <?php elseif (!empty($post['featured_image'])): ?>
                                            <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>?v=<?php echo time(); ?>" 
                                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                                 class="post-image">
                                        <?php else: ?>
                                            <div class="post-image" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="post-content">
                                            <h5 class="post-title">
                                                <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>">
                                                    <?php echo htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h5>
                                            <p class="post-excerpt">
                                                <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 120)) . '...'; ?>
                                            </p>
                                            
                                            <div class="post-meta">
                                                <span class="post-date">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                                </span>
                                                <span class="post-views">
                                                    <i class="fas fa-eye me-1"></i>
                                                    <?php echo $post['views'] ?? 0; ?>
                                                </span>
                                            </div>
                                            
                                            <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>" class="read-more">
                                                Read More
                                                <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>


        // Navbar background on scroll (for public view)
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                } else {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                }
            }
        });

        // Logo switching function
        function switchLogoBasedOnBackground() {
            const logos = document.querySelectorAll('.phitsol-logo');
            
            logos.forEach(logo => {
                const logoBlack = logo.getAttribute('data-logo-black');
                const logoWhite = logo.getAttribute('data-logo-white');
                
                if (!logoBlack || !logoWhite) return;
                
                // Get the computed background color of the logo's parent element
                const parent = logo.closest('.navbar, .sidebar-header, .login-header, .auth-header') || logo.parentElement;
                const backgroundColor = window.getComputedStyle(parent).backgroundColor;
                
                // Parse RGB values
                const rgb = backgroundColor.match(/\d+/g);
                if (rgb && rgb.length >= 3) {
                    const r = parseInt(rgb[0]);
                    const g = parseInt(rgb[1]);
                    const b = parseInt(rgb[2]);
                    
                    // Calculate brightness (0-255)
                    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                    
                    // Switch logo based on background brightness
                    if (brightness < 128) {
                        // Dark background - use white logo
                        logo.src = logoWhite;
                    } else {
                        // Light background - use black logo
                        logo.src = logoBlack;
                    }
                }
            });
        }

        // Run logo switching on page load and when scrolling
        document.addEventListener('DOMContentLoaded', switchLogoBasedOnBackground);
        window.addEventListener('scroll', switchLogoBasedOnBackground);
        window.addEventListener('resize', switchLogoBasedOnBackground);
    </script>
</body>
</html> 