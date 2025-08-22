<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../config/session.php';
require_once '../config/database.php';

use App\Models\Slider;
use App\Models\Blog;

ob_start();
header('Cache-Control: public, max-age=3600');
header('ETag: "' . md5_file(__FILE__) . '"');

$isLoggedIn = isLoggedIn();

try {
    $sliderModel = new Slider();
    $blogModel = new Blog();
    $activeSliders = $sliderModel->getActive();
    $latestPosts = $blogModel->getPublished(3);
} catch (Exception $e) {
    error_log("Error loading data: " . $e->getMessage());
    $activeSliders = [];
    $latestPosts = [];
}

$startTime = microtime(true);
?>
<!DOCTYPE html>
<html lang="ko" class="scroll-smooth">
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
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        border: "hsl(var(--border))",
                        input: "hsl(var(--input))",
                        ring: "hsl(var(--ring))",
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                        primary: {
                            DEFAULT: "hsl(var(--primary))",
                            foreground: "hsl(var(--primary-foreground))",
                        },
                        secondary: {
                            DEFAULT: "hsl(var(--secondary))",
                            foreground: "hsl(var(--secondary-foreground))",
                        },
                        destructive: {
                            DEFAULT: "hsl(var(--destructive))",
                            foreground: "hsl(var(--destructive-foreground))",
                        },
                        muted: {
                            DEFAULT: "hsl(var(--muted))",
                            foreground: "hsl(var(--muted-foreground))",
                        },
                        accent: {
                            DEFAULT: "hsl(var(--accent))",
                            foreground: "hsl(var(--accent-foreground))",
                        },
                        popover: {
                            DEFAULT: "hsl(var(--popover))",
                            foreground: "hsl(var(--popover-foreground))",
                        },
                        card: {
                            DEFAULT: "hsl(var(--card))",
                            foreground: "hsl(var(--card-foreground))",
                        },
                    },
                    borderRadius: {
                        lg: "var(--radius)",
                        md: "calc(var(--radius) - 2px)",
                        sm: "calc(var(--radius) - 4px)",
                    },
                },
            },
        }
    </script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96%;
            --accent-foreground: 222.2 84% 4.9%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }
        
        .dark {
            --background: 222.2 84% 4.9%;
            --foreground: 210 40% 98%;
            --card: 222.2 84% 4.9%;
            --card-foreground: 210 40% 98%;
            --popover: 222.2 84% 4.9%;
            --popover-foreground: 210 40% 98%;
            --primary: 217.2 91.2% 59.8%;
            --primary-foreground: 222.2 84% 4.9%;
            --secondary: 217.2 32.6% 17.5%;
            --secondary-foreground: 210 40% 98%;
            --muted: 217.2 32.6% 17.5%;
            --muted-foreground: 215 20.2% 65.1%;
            --accent: 217.2 32.6% 17.5%;
            --accent-foreground: 210 40% 98%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 210 40% 98%;
            --border: 217.2 32.6% 17.5%;
            --input: 217.2 32.6% 17.5%;
            --ring: 224.3 76.3% 94.1%;
        }
        
        * {
            border-color: hsl(var(--border));
        }
        
        body {
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
        }
        
        /* ShadCN Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }
        
        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
        }
        
        .btn-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }
        
        .btn-secondary:hover {
            background-color: hsl(var(--secondary) / 0.8);
        }
        
        .btn-outline {
            background-color: transparent;
            border-color: hsl(var(--border));
            color: hsl(var(--foreground));
        }
        
        .btn-outline:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }
        
        .btn-lg {
            height: 2.75rem;
            padding: 0 1.5rem;
            font-size: 0.875rem;
        }
        
        .btn-md {
            height: 2.25rem;
            padding: 0 1rem;
            font-size: 0.875rem;
        }
        
        .btn-sm {
            height: 1.75rem;
            padding: 0 0.75rem;
            font-size: 0.75rem;
        }
        
        /* ShadCN Card Styles */
        .card {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border-radius: var(--radius);
            border: 1px solid hsl(var(--border));
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid hsl(var(--border));
        }
        
        .card-content {
            padding: 1.5rem;
        }
        
        .card-footer {
            padding: 1.5rem;
            border-top: 1px solid hsl(var(--border));
        }
        
        /* ShadCN Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: var(--radius);
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.125rem 0.5rem;
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }
        
        .badge-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }
        
        .badge-secondary {
            background-color: hsl(var(--secondary));
            color: hsl(var(--secondary-foreground));
        }
        
        .badge-destructive {
            background-color: hsl(var(--destructive));
            color: hsl(var(--destructive-foreground));
        }
        
        /* ShadCN Separator Styles */
        .separator {
            background-color: hsl(var(--border));
            height: 1px;
            width: 100%;
        }
        
        .separator-vertical {
            background-color: hsl(var(--border));
            width: 1px;
            height: 100%;
        }
        
        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Hero Section */
        .hero-gradient {
            background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(var(--primary) / 0.8) 100%);
        }
        
        /* Service Cards */
        .service-card {
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
        }
        
        /* Stats Animation */
        .stat-number {
            transition: all 0.3s ease;
        }
        
        .stat-number:hover {
            transform: scale(1.1);
        }
        
        /* Hero Slider Styles */
        .slide {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .slider-dot {
            transition: all 0.3s ease;
        }
        
        .slider-dot:hover {
            transform: scale(1.2);
        }
        
        .slider-control {
            transition: all 0.3s ease;
        }
        
        .slider-control:hover {
            transform: scale(1.1);
        }
        
        /* Slider fade transition */
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        /* Responsive slider controls */
        @media (max-width: 768px) {
            .slider-control {
                padding: 0.5rem !important;
            }
            
            .slider-control i {
                font-size: 0.875rem;
            }
        }
    </style>
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
</head>
<body class="antialiased">
    <!-- Alert Messages -->
    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="font-medium">Success!</span> You have been logged out successfully.
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="font-medium">Success!</span> <?php echo htmlspecialchars($_SESSION['registration_success']); ?>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-sm border-b border-gray-200 z-40">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-2">
                    <img src="assets/img/logo_black.png" alt="PHITSOL Logo" class="h-8 w-auto">
                    <span class="text-xl font-bold text-gray-900">PHITSOL</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition-colors">About</a>
                    <a href="#services" class="text-gray-700 hover:text-blue-600 transition-colors">Services</a>
                    <a href="<?php if ($isLoggedIn): ?>blog.php<?php else: ?>#blog<?php endif; ?>" class="text-gray-700 hover:text-blue-600 transition-colors">Blog</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                    
                    <?php if ($isLoggedIn): ?>
                        <div class="relative">
                            <button class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 transition-colors">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <!-- Dropdown menu would go here -->
                        </div>
                    <?php else: ?>
                        <a href="partners-login.php" class="btn btn-primary btn-md">
                            <i class="fas fa-building mr-2"></i>
                            Partners Portal
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Slider Section -->
    <section id="home" class="relative min-h-screen overflow-hidden">
        <?php if (!empty($activeSliders)): ?>
            <!-- Slider Container -->
            <div id="heroSlider" class="relative w-full h-screen">
                <?php foreach ($activeSliders as $index => $slider): ?>
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?> absolute inset-0 transition-opacity duration-1000 ease-in-out"
                         style="background-image: url('uploads/slider/<?php echo htmlspecialchars($slider['image'] ?? ''); ?>'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-black/40"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="container mx-auto px-4 text-center text-white relative z-10">
                                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 animate-fade-in-up">
                                    <?php echo htmlspecialchars($slider['title'] ?? 'Professional IT Solutions'); ?>
                                </h1>
                                <p class="text-lg md:text-xl lg:text-2xl mb-8 max-w-4xl mx-auto animate-fade-in-up">
                                    <?php echo htmlspecialchars($slider['description'] ?? 'Accelerating toward prestigious excellence in the Philippines with innovative technology solutions'); ?>
                                </p>
                                <?php if (!empty($slider['button_text']) && !empty($slider['button_url'])): ?>
                                    <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up">
                                        <a href="<?php echo htmlspecialchars($slider['button_url']); ?>" class="btn bg-white text-blue-600 hover:bg-gray-100 btn-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            <?php echo htmlspecialchars($slider['button_text']); ?>
                                        </a>
                                        <a href="contact-support.php" class="btn btn-outline border-white text-white hover:bg-white hover:text-blue-600 btn-lg">
                                            <i class="fas fa-phone mr-2"></i>
                                            Contact Us
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up">
                                        <a href="#about" class="btn bg-white text-blue-600 hover:bg-gray-100 btn-lg">
                                            <i class="fas fa-arrow-right mr-2"></i>
                                            Learn More
                                        </a>
                                        <a href="contact-support.php" class="btn btn-outline border-white text-white hover:bg-white hover:text-blue-600 btn-lg">
                                            <i class="fas fa-phone mr-2"></i>
                                            Contact Us
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Slider Navigation -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
                    <div class="flex space-x-2">
                        <?php foreach ($activeSliders as $index => $slider): ?>
                            <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-colors <?php echo $index === 0 ? 'bg-white' : ''; ?>"
                                    data-slide="<?php echo $index; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Slider Controls -->
                <button class="slider-control slider-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 text-white p-3 rounded-full transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-control slider-next absolute right-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 text-white p-3 rounded-full transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <!-- Default Hero Section (when no sliders) -->
            <div class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="container mx-auto px-4 relative z-10">
                    <div class="text-center text-white">
                        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in-up">
                            Professional IT Solutions
                        </h1>
                        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto animate-fade-in-up">
                            Accelerating toward prestigious excellence in the Philippines with innovative technology solutions
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up">
                            <a href="#about" class="btn bg-white text-blue-600 hover:bg-gray-100 btn-lg">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Learn More
                            </a>
                            <a href="contact-support.php" class="btn btn-outline border-white text-white hover:bg-white hover:text-blue-600 btn-lg">
                                <i class="fas fa-phone mr-2"></i>
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">About PHITSOL INC.</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Accelerating toward prestigious excellence in the Philippines
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Who We Are</h3>
                            <p class="text-gray-600 leading-relaxed">
                                PHITSOL INC. is a premier technology partner in the Philippines, driven by a commitment to excellence. 
                                We deliver innovative and customized solutions to businesses and consumers alike.
                            </p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-content">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Our mission is to achieve prestigious excellence in the Philippines by being the best technology partner 
                                for businesses and consumers. We specialize in providing customized products and services.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="text-4xl font-bold text-blue-600 mb-2 stat-number" data-target="500">0</div>
                            <div class="text-gray-600">Happy Clients</div>
                        </div>
                    </div>
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="text-4xl font-bold text-blue-600 mb-2 stat-number" data-target="1000">0</div>
                            <div class="text-gray-600">Projects Completed</div>
                        </div>
                    </div>
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="text-4xl font-bold text-blue-600 mb-2 stat-number" data-target="5">0</div>
                            <div class="text-gray-600">Years Experience</div>
                        </div>
                    </div>
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="text-4xl font-bold text-blue-600 mb-2 stat-number" data-target="50">0</div>
                            <div class="text-gray-600">Awards Won</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive solutions tailored to your business needs
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Rental Services -->
                <div class="card service-card">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-handshake text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Rental & Rent-to-Own Services</h3>
                        <p class="text-gray-600 mb-6">
                            Flexible rental and rent-to-own plans on a wide array of products, providing cost-effective solutions 
                            for your short-term projects or long-term needs.
                        </p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="badge badge-primary">Flexible Terms</span>
                            <span class="badge badge-secondary">Cost-Effective</span>
                            <span class="badge badge-secondary">Wide Selection</span>
                        </div>
                    </div>
                </div>
                
                <!-- Disposal Services -->
                <div class="card service-card">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-recycle text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Disposal Services</h3>
                        <p class="text-gray-600 mb-6">
                            Professional disposal services for electronic equipment and IT assets. We ensure secure, 
                            environmentally responsible disposal while maintaining data security.
                        </p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="badge badge-primary">Secure</span>
                            <span class="badge badge-secondary">Eco-Friendly</span>
                            <span class="badge badge-secondary">Compliant</span>
                        </div>
                    </div>
                </div>
                
                <!-- Crushing Services -->
                <div class="card service-card">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-hammer text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Crushing Services</h3>
                        <p class="text-gray-600 mb-6">
                            Professional crushing services for various materials and industrial applications. 
                            We provide efficient, reliable crushing solutions with state-of-the-art equipment.
                        </p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="badge badge-primary">Efficient</span>
                            <span class="badge badge-secondary">Reliable</span>
                            <span class="badge badge-secondary">State-of-the-Art</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="text-center mt-16">
                <div class="card max-w-4xl mx-auto">
                    <div class="card-content">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Ready to Get Started?</h3>
                        <p class="text-gray-600 mb-8">
                            Contact us today to discuss your specific needs and get a customized solution
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="contact-support.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-phone mr-2"></i>
                                Contact Us
                            </a>
                            <a href="partners-login.php" class="btn btn-outline btn-lg">
                                <i class="fas fa-user mr-2"></i>
                                Partner Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Latest Insights</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Stay updated with our latest insights, tips, and industry trends
                </p>
            </div>
            
            <?php if (!empty($latestPosts)): ?>
                <div class="grid md:grid-cols-3 gap-8">
                    <?php foreach ($latestPosts as $post): ?>
                        <div class="card service-card">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                     class="w-full h-48 object-cover rounded-t-lg">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                    <i class="fas fa-newspaper text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">
                                    <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>" 
                                       class="hover:text-blue-600 transition-colors">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-4">
                                    <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 120)) . '...'; ?>
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        <?php echo $post['views'] ?? 0; ?>
                                    </span>
                                </div>
                                
                                <a href="blog.php?post=<?php echo htmlspecialchars($post['slug']); ?>" 
                                   class="btn btn-primary btn-sm w-full">
                                    Read More
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-12">
                    <?php if ($isLoggedIn): ?>
                        <a href="blog.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-newspaper mr-2"></i>
                            View All Posts
                        </a>
                    <?php else: ?>
                        <div class="card max-w-2xl mx-auto">
                            <div class="card-content">
                                <div class="flex items-center justify-center mb-4">
                                    <i class="fas fa-info-circle text-blue-600 text-2xl mr-3"></i>
                                    <span class="font-medium">Login Required</span>
                                </div>
                                <p class="text-gray-600 mb-6">
                                    Sign in to access our full blog archive and exclusive content.
                                </p>
                                <a href="auth.php?form=login" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-newspaper text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-2">No blog posts yet</h4>
                    <p class="text-gray-600">Check back soon for our latest insights and updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-900 text-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Contact Us</h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Get in touch with us for your IT solutions needs
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="card bg-gray-800 border-gray-700">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-map-marker-alt text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Address</h3>
                        <p class="text-gray-300">
                            2/F MANDAUE FOAM BUILDING 489 SHAW BOULEVARD ADDITION HILLS, 
                            CITY OF MANDALUYONG, SECOND DISTRICT, NATIONAL CAPITAL REGION (NCR), 1550<br>
                            Philippines
                        </p>
                    </div>
                </div>
                
                <div class="card bg-gray-800 border-gray-700">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-phone text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Phone</h3>
                        <p class="text-gray-300">
                            +63 2 8879 7058<br>
                            +63 915 086 1410
                        </p>
                    </div>
                </div>
                
                <div class="card bg-gray-800 border-gray-700">
                    <div class="card-content text-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Email</h3>
                        <p class="text-gray-300">
                            info@phitsol.com<br>
                            cs@phitsol.com
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="max-w-2xl mx-auto">
                <div class="card bg-gray-800 border-gray-700">
                    <div class="card-content">
                        <h3 class="text-2xl font-bold text-center mb-8">Send us a Message</h3>
                        <form class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <input type="text" 
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                           placeholder="Your Name" required>
                                </div>
                                <div>
                                    <input type="email" 
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                           placeholder="Your Email" required>
                                </div>
                            </div>
                            <div>
                                <input type="text" 
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       placeholder="Subject" required>
                            </div>
                            <div>
                                <textarea rows="5" 
                                          class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                          placeholder="Your Message" required></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane mr-2"></i>
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
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/img/logo_white.png" alt="PHITSOL" class="h-8 w-auto mr-3">
                        <span class="text-xl font-bold">PHITSOL INC</span>
                    </div>
                    <p class="text-gray-400 mb-6">
                        Professional IT solutions and services provider in the Philippines. 
                        We deliver innovative, customized solutions to businesses and consumers alike.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Services</h4>
                    <ul class="space-y-2">
                        <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Rental Services</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Disposal Services</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Crushing Services</a></li>
                        <li><a href="contact-support.php" class="text-gray-400 hover:text-white transition-colors">Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="blog.php" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="contact-support.php" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                        <li><a href="partners-login.php" class="text-gray-400 hover:text-white transition-colors">Partners</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact Info</h4>
                    <div class="space-y-2 text-gray-400">
                        <p><i class="fas fa-map-marker-alt mr-2"></i> Philippines</p>
                        <p><i class="fas fa-phone mr-2"></i> +63 XXX XXX XXXX</p>
                        <p><i class="fas fa-envelope mr-2"></i> info@phitsol.com</p>
                    </div>
                    <div class="mt-4">
                        <a href="contact-support.php" class="btn btn-outline border-gray-600 text-gray-400 hover:bg-gray-700 hover:text-white btn-sm">
                            <i class="fas fa-headset mr-2"></i>
                            Get Support
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="separator my-8"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400">
                    &copy; 2024 PHITSOL INC. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors opacity-0 invisible">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- JavaScript -->
    <script>
        // Back to Top Button
        const backToTop = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Stats Animation
        const stats = document.querySelectorAll('.stat-number');
        
        const animateStats = () => {
            stats.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current);
                }, 16);
            });
        };
        
        // Intersection Observer for stats
        const statsSection = document.querySelector('#about');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        if (statsSection) {
            observer.observe(statsSection);
        }
        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Performance Monitoring
        window.addEventListener('load', function() {
            const loadTime = performance.now();
            console.log('Page load time:', loadTime.toFixed(2), 'ms');
            
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
        
        // Hero Slider Functionality
        const heroSlider = document.getElementById('heroSlider');
        if (heroSlider) {
            const slides = heroSlider.querySelectorAll('.slide');
            const dots = heroSlider.querySelectorAll('.slider-dot');
            const prevBtn = heroSlider.querySelector('.slider-prev');
            const nextBtn = heroSlider.querySelector('.slider-next');
            
            let currentSlide = 0;
            let slideInterval;
            
            // Function to show slide
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.classList.remove('active');
                    slide.style.opacity = '0';
                });
                
                // Remove active class from all dots
                dots.forEach(dot => dot.classList.remove('bg-white'));
                
                // Show current slide
                if (slides[index]) {
                    slides[index].classList.add('active');
                    slides[index].style.opacity = '1';
                }
                
                // Update dot
                if (dots[index]) {
                    dots[index].classList.add('bg-white');
                }
                
                currentSlide = index;
            }
            
            // Function to next slide
            function nextSlide() {
                const next = (currentSlide + 1) % slides.length;
                showSlide(next);
            }
            
            // Function to previous slide
            function prevSlide() {
                const prev = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prev);
            }
            
            // Auto-play functionality
            function startAutoPlay() {
                slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
            }
            
            function stopAutoPlay() {
                clearInterval(slideInterval);
            }
            
            // Event listeners for dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    stopAutoPlay();
                    startAutoPlay(); // Restart auto-play
                });
            });
            
            // Event listeners for navigation buttons
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    prevSlide();
                    stopAutoPlay();
                    startAutoPlay(); // Restart auto-play
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    nextSlide();
                    stopAutoPlay();
                    startAutoPlay(); // Restart auto-play
                });
            }
            
            // Pause auto-play on hover
            heroSlider.addEventListener('mouseenter', stopAutoPlay);
            heroSlider.addEventListener('mouseleave', startAutoPlay);
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    prevSlide();
                    stopAutoPlay();
                    startAutoPlay();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                    stopAutoPlay();
                    startAutoPlay();
                }
            });
            
            // Start auto-play
            if (slides.length > 1) {
                startAutoPlay();
            }
        }
        
        // Error Monitoring
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
    </script>
    
    <?php
    if (defined('APP_DEBUG') && APP_DEBUG) {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        echo "<!-- Performance: {$executionTime}ms, Memory: {$memoryUsage}MB -->";
    }
    ob_end_flush();
    ?>
</body>
</html>
