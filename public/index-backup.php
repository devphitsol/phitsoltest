<?php
require_once __DIR__ . '/../vendor/autoload.php';
// Load shared configuration
require_once '../config/session.php';
require_once '../config/database.php';

use App\Models\Slider;
use App\Models\Blog;

// 성능 최적화: 출력 버퍼링 활성화
ob_start();

// 캐시 헤더 설정 (정적 콘텐츠용)
header('Cache-Control: public, max-age=3600'); // 1시간 캐시
header('ETag: "' . md5_file(__FILE__) . '"');

// 세션 유효성 검사
$isLoggedIn = isLoggedIn();

// 에러 핸들링
try {
    $sliderModel = new Slider();
    $blogModel = new Blog();

    // Get active sliders for main slider section
    $activeSliders = $sliderModel->getActive();

    // Get latest blog posts (max 3)
    $latestPosts = $blogModel->getPublished(3);
    
} catch (Exception $e) {
    error_log("Error loading data: " . $e->getMessage());
    $activeSliders = [];
    $latestPosts = [];
}

// 성능 모니터링
$startTime = microtime(true);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PHITSOL - Professional IT Solutions in the Philippines. We provide rental, disposal, and crushing services with innovative technology solutions.">
    <meta name="keywords" content="IT solutions, rental services, disposal services, crushing services, Philippines, technology">
    <meta name="author" content="PHITSOL INC">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://phitsol.com/">
    <meta property="og:title" content="PHITSOL - Professional IT Solutions">
    <meta property="og:description" content="Professional IT solutions and services provider in the Philippines">
    <meta property="og:image" content="assets/img/logo_black.png">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://phitsol.com/">
    <meta property="twitter:title" content="PHITSOL - Professional IT Solutions">
    <meta property="twitter:description" content="Professional IT solutions and services provider in the Philippines">
    <meta property="twitter:image" content="assets/img/logo_black.png">
    
    <title>PHITSOL - Professional IT Solutions</title>
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    
    <!-- Stylesheets -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo filemtime('assets/css/style.css'); ?>" rel="stylesheet">
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
</head>
<body>
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 80px; z-index: 1050; width: 100%;">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> You have been logged out successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 80px; z-index: 1050; width: 100%;">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['registration_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
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
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php if ($isLoggedIn): ?>blog.php<?php else: ?>#blog<?php endif; ?>">
                            Blog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
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
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="partners-login.php" style="margin-left: 10px;">
                                <i class="fas fa-building me-1"></i>Partners Portal
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Slider Section -->
    <section id="home" class="hero-slider">
        <?php if (!empty($activeSliders)): ?>
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($activeSliders as $index => $slider): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                                class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                    <?php endforeach; ?>
                </div>
                
                <div class="carousel-inner">
                    <?php foreach ($activeSliders as $index => $slider): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                             style="background-image: url('uploads/slider/<?php echo htmlspecialchars($slider['image']); ?>');">
                            <div class="carousel-caption">
                                <h2><?php echo htmlspecialchars($slider['title']); ?></h2>
                                <p><?php echo htmlspecialchars($slider['description']); ?></p>
                                <?php if (!empty($slider['button_text']) && !empty($slider['button_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($slider['button_url']); ?>" class="btn btn-light btn-lg">
                                        <?php echo htmlspecialchars($slider['button_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        <?php else: ?>
            <!-- Default hero if no sliders -->
            <div class="carousel-item active" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                <div class="carousel-caption">
                    <h2>Welcome to PHITSOL</h2>
                    <p>Professional IT Solutions for Your Business</p>
                    <a href="#about" class="btn btn-light btn-lg">Learn More</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- About Us Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <h2 class="section-title">About PHITSOL INC.</h2>
            <p class="section-subtitle">Accelerating toward prestigious excellence in the Philippines</p>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="about-content text-center">
                        <h3>Who We Are</h3>
                        <p>PHITSOL INC. is a premier technology partner in the Philippines, driven by a commitment to excellence. We deliver innovative and customized solutions to businesses and consumers alike.</p>
                        <p>We believe that being a partner for the future means prioritizing trust and reliability. That's why we handle every concern with swift, dependable action. Our promise is to provide not just technology, but a stable, quality partnership that you can always count on.</p>
                        
                        <h3 class="mt-5">Our Mission at PHITSOL INC.</h3>
                        <p>Our mission is to achieve prestigious excellence in the Philippines by being the best technology partner for businesses and consumers. We specialize in providing customized products and services that are perfectly tailored to your unique needs.</p>
                        <p>We are committed to building a partnership for the future, one built on stability, quality, and mutual trust. We believe that your confidence in us is our most valuable asset, and we work hard to earn it every day by addressing your concerns with immediate and reliable action.</p>
                        
                        <h3 class="mt-5">Our Commitment</h3>
                        <p>At PHITSOL INC., we are dedicated to accelerating toward prestigious excellence in the Philippines. As your trusted technology partner, we provide innovative, customized products and services that empower both businesses and consumers.</p>
                        <p>Our commitment to you is built on a foundation of stability and quality. We value your trust above all, and we earn it by ensuring every concern is met with swift and dependable action. This dedication to prompt and reliable service is how we forge strong, lasting relationships and reinforce the confidence our customers place in us.</p>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="stats-section">
                        <div class="row text-center">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-number" data-target="500">0</div>
                                    <div class="stat-label">Happy Clients</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div class="stat-number" data-target="1000">0</div>
                                    <div class="stat-label">Projects Completed</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-number" data-target="5">0</div>
                                    <div class="stat-label">Years Experience</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="stat-number" data-target="50">0</div>
                                    <div class="stat-label">Awards Won</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Services Section -->
    <section id="services" class="section">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Comprehensive solutions tailored to your business needs</p>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>RENTAL & RENT-TO-OWN SERVICES</h4>
                        <p>We offer flexible rental and rent-to-own plans on a wide array of products, providing cost-effective solutions for your short-term projects or long-term needs. Our plans are designed to give you the freedom and flexibility to manage your resources effectively.</p>
                        <div class="service-features">
                            <span class="feature-badge">Flexible Terms</span>
                            <span class="feature-badge">Cost-Effective</span>
                            <span class="feature-badge">Wide Selection</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <h4>DISPOSAL SERVICES</h4>
                        <p>Professional disposal services for electronic equipment and IT assets. We ensure secure, environmentally responsible disposal of your outdated or non-functional equipment while maintaining data security and compliance with environmental regulations.</p>
                        <div class="service-features">
                            <span class="feature-badge">Secure</span>
                            <span class="feature-badge">Eco-Friendly</span>
                            <span class="feature-badge">Compliant</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-hammer"></i>
                        </div>
                        <h4>CRUSHING SERVICES</h4>
                        <p>Professional crushing services for various materials and industrial applications. We provide efficient, reliable crushing solutions with state-of-the-art equipment to meet your specific processing requirements and production goals.</p>
                        <div class="service-features">
                            <span class="feature-badge">Efficient</span>
                            <span class="feature-badge">Reliable</span>
                            <span class="feature-badge">State-of-the-Art</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <div class="cta-section">
                        <h3>Ready to Get Started?</h3>
                        <p class="mb-4">Contact us today to discuss your specific needs and get a customized solution</p>
                        <div class="cta-buttons">
                            <a href="contact-support.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-phone me-2"></i>
                                Contact Us
                            </a>
                            <a href="partners-login.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user me-2"></i>
                                Partner Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="section blog-section">
        <div class="container">
            <h2 class="section-title">Blog</h2>
            <p class="section-subtitle">Stay updated with our latest insights, tips, and industry trends</p>
            
            <?php if (!empty($latestPosts)): ?>
                <div class="row">
                    <?php foreach ($latestPosts as $post): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="blog-card">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                         class="blog-image">
                                <?php else: ?>
                                    <div class="blog-placeholder">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-content">
                                    <h5 class="blog-title">
                                        <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="blog-excerpt">
                                        <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 100)) . '...'; ?>
                                    </p>
                                    
                                    <div class="blog-meta">
                                        <span class="blog-date">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                        </span>
                                        <span class="blog-views">
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo $post['views'] ?? 0; ?>
                                        </span>
                                    </div>
                                    
                                    <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>" class="read-more-btn">
                                        Read More
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <?php if ($isLoggedIn): ?>
                        <a href="blog.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-newspaper me-2"></i>
                            View All Posts
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Login Required:</strong> Sign in to access our full blog archive and exclusive content.
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="auth.php?form=login" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h4>No blog posts yet</h4>
                    <p class="text-muted">Check back soon for our latest insights and updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <h2 class="section-title text-white">Contact Us</h2>
            <p class="section-subtitle text-white">Get in touch with us for your IT solutions needs</p>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5>Address</h5>
                        <p>2/F MANDAUE FOAM BUILDING 489 SHAW BOULEVARD ADDITION HILLS, CITY OF MANDALUYONG, SECOND DISTRICT, NATIONAL CAPITAL REGION (NCR), 1550<br>Philippines</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h5>Phone</h5>
                        <p>+63 2 8879 7058<br>+63 915 086 1410</p>
                        
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5>Email</h5>
                        <p>info@phitsol.com<br>cs@phitsol.com</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-info">
                        <h4 class="text-center mb-4">Send us a Message</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" placeholder="Your Email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="contact-subject" placeholder="Subject" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-light btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <h5 class="mb-3">
                            <img src="assets/img/logo_white.png" alt="PHITSOL" height="40" class="me-2">
                            PHITSOL INC
                        </h5>
                        <p class="text-muted">
                            Professional IT solutions and services provider in the Philippines. 
                            We deliver innovative, customized solutions to businesses and consumers alike.
                        </p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="mb-3">Services</h6>
                        <ul class="footer-links">
                            <li><a href="#services">Rental Services</a></li>
                            <li><a href="#services">Disposal Services</a></li>
                            <li><a href="#services">Crushing Services</a></li>
                            <li><a href="contact-support.php">Support</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="mb-3">Company</h6>
                        <ul class="footer-links">
                            <li><a href="#about">About Us</a></li>
                            <li><a href="blog.php">Blog</a></li>
                            <li><a href="contact-support.php">Contact</a></li>
                            <li><a href="partners-login.php">Partners</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="mb-3">Contact Info</h6>
                        <div class="contact-info">
                            <p><i class="fas fa-map-marker-alt me-2"></i> Philippines</p>
                            <p><i class="fas fa-phone me-2"></i> +63 XXX XXX XXXX</p>
                            <p><i class="fas fa-envelope me-2"></i> info@phitsol.com</p>
                        </div>
                        <div class="mt-3">
                            <a href="contact-support.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-headset me-1"></i>
                                Get Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; 2024 PHITSOL INC. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-bottom-links">
                        <a href="#" class="text-muted me-3">Privacy Policy</a>
                        <a href="#" class="text-muted me-3">Terms of Service</a>
                        <a href="#" class="text-muted">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </a>
    
    <!-- Performance Monitoring -->
    <script>
        // 성능 모니터링
        window.addEventListener('load', function() {
            const loadTime = performance.now();
            console.log('Page load time:', loadTime.toFixed(2), 'ms');
            
            // Core Web Vitals 모니터링
            if ('PerformanceObserver' in window) {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.entryType === 'largest-contentful-paint') {
                            console.log('LCP:', entry.startTime.toFixed(2), 'ms');
                        }
                        if (entry.entryType === 'first-input') {
                            console.log('FID:', entry.processingStart - entry.startTime, 'ms');
                        }
                    }
                });
                observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input'] });
            }
        });
        
        // 에러 모니터링
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
    </script>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/unified-layout.js?v=<?php echo filemtime('assets/js/unified-layout.js'); ?>"></script>
    
    <?php
    // 성능 메트릭 출력 (개발 환경에서만)
    if (defined('APP_DEBUG') && APP_DEBUG) {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        echo "<!-- Performance: {$executionTime}ms, Memory: {$memoryUsage}MB -->";
    }
    
    // 출력 버퍼 플러시
    ob_end_flush();
    ?>
</body>
</html> 