<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?> - PHITSOL</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Unified Admin Design System -->
    <link href="assets/css/unified-admin-design.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="assets/css/theme-system.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../public/assets/css/responsive-enhancements.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="theme-aware">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <img 
                    src="assets/img/logo_white.png?v=<?php echo uniqid(); ?>"
                    alt="PHITSOL Logo"
                    class="phitsol-logo"
                    id="phitsol-logo"
                >
            </a>
        </div>
        
        <div class="sidebar-nav">
            <a href="index.php" class="nav-link <?php echo $currentAction === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="index.php?action=slider" class="nav-link <?php echo $currentAction === 'slider' ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                Slider Management
            </a>
            <a href="index.php?action=blog" class="nav-link <?php echo $currentAction === 'blog' ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i>
                Blog Management
            </a>
            <a href="index.php?action=users" class="nav-link <?php echo $currentAction === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                User Management
            </a>
            
            <a href="index.php?action=company" class="nav-link <?php echo $currentAction === 'company' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i>
                Company Management
            </a>
            
            <a href="index.php?action=products" class="nav-link <?php echo $currentAction === 'products' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i>
                Product Management
            </a>
            
            <a href="index.php?action=support-messages" class="nav-link <?php echo $currentAction === 'support-messages' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i>
                Support Messages
            </a>
        </div>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="header-left">
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-content">
                    <h1 class="admin-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'Admin'); ?></p>
                </div>
            </div>
            
            <div class="header-right">
                <!-- Theme Toggle -->
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_email'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></div>
                        <div class="user-email"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'admin@phitsol.com'); ?></div>
                    </div>
                    <a href="index.php?logout=1" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="logout-text">Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
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

        <!-- Page Content -->
        <?php echo $pageContent ?? ''; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Layout JavaScript -->
    <script>
        // Theme System
        class ThemeManager {
            constructor() {
                this.themeToggle = document.getElementById('themeToggle');
                this.html = document.documentElement;
                this.currentTheme = localStorage.getItem('theme') || 'light';
                
                this.init();
            }
            
            init() {
                this.setTheme(this.currentTheme);
                this.themeToggle.addEventListener('click', () => this.toggleTheme());
                
                // Listen for system theme changes
                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (!localStorage.getItem('theme')) {
                            this.setTheme(e.matches ? 'dark' : 'light');
                        }
                    });
                }
            }
            
            setTheme(theme) {
                this.html.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                this.currentTheme = theme;
            }
            
            toggleTheme() {
                const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
            }
        }
        
        // Mobile Navigation
        class MobileNavigation {
            constructor() {
                this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
                this.sidebar = document.querySelector('.sidebar');
                this.sidebarOverlay = document.getElementById('sidebarOverlay');
                
                this.init();
            }
            
            init() {
                this.mobileMenuToggle.addEventListener('click', () => this.toggleSidebar());
                this.sidebarOverlay.addEventListener('click', () => this.closeSidebar());
                
                // Close sidebar on window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth > 1024) {
                        this.closeSidebar();
                        // Ensure sidebar is always visible on desktop
                        this.sidebar.style.transform = 'translateX(0)';
                        this.sidebar.style.visibility = 'visible';
                        this.sidebar.style.opacity = '1';
                        this.sidebar.style.position = 'fixed';
                        this.sidebar.style.left = '0';
                        this.sidebar.style.top = '0';
                        this.sidebar.style.height = '100vh';
                        this.sidebar.style.zIndex = '1000';
                    }
                });
                
                // Close sidebar on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closeSidebar();
                    }
                });
                
                // Initialize sidebar state based on screen size
                this.initializeSidebarState();
            }
            
            initializeSidebarState() {
                // On desktop (1024px and above), always show sidebar
                if (window.innerWidth > 1024) {
                    this.sidebar.classList.remove('show');
                    this.sidebarOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                    // Ensure sidebar is always visible on desktop
                    this.sidebar.style.transform = 'translateX(0)';
                    this.sidebar.style.visibility = 'visible';
                    this.sidebar.style.opacity = '1';
                    this.sidebar.style.position = 'fixed';
                    this.sidebar.style.left = '0';
                    this.sidebar.style.top = '0';
                    this.sidebar.style.height = '100vh';
                    this.sidebar.style.zIndex = '1000';
                }
            }
            
            toggleSidebar() {
                // Only toggle on mobile devices (1024px and below)
                if (window.innerWidth <= 1024) {
                    this.sidebar.classList.toggle('show');
                    this.sidebarOverlay.classList.toggle('show');
                    document.body.classList.toggle('sidebar-open');
                }
                // On desktop, ensure sidebar is always visible
                if (window.innerWidth > 1024) {
                    this.sidebar.style.transform = 'translateX(0)';
                    this.sidebar.style.visibility = 'visible';
                    this.sidebar.style.opacity = '1';
                    this.sidebar.style.position = 'fixed';
                    this.sidebar.style.left = '0';
                    this.sidebar.style.top = '0';
                    this.sidebar.style.height = '100vh';
                    this.sidebar.style.zIndex = '1000';
                }
            }
            
            closeSidebar() {
                this.sidebar.classList.remove('show');
                this.sidebarOverlay.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
        
        // Accessibility Enhancements
        class AccessibilityManager {
            constructor() {
                this.init();
            }
            
            init() {
                // Add focus indicators
                this.addFocusIndicators();
                
                // Add keyboard navigation
                this.addKeyboardNavigation();
                
                // Add skip links
                this.addSkipLinks();
            }
            
            addFocusIndicators() {
                const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
                
                focusableElements.forEach(element => {
                    element.addEventListener('focus', () => {
                        element.classList.add('focus-visible');
                    });
                    
                    element.addEventListener('blur', () => {
                        element.classList.remove('focus-visible');
                    });
                });
            }
            
            addKeyboardNavigation() {
                // Tab navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Tab') {
                        document.body.classList.add('keyboard-navigation');
                    }
                });
                
                // Mouse navigation
                document.addEventListener('mousedown', () => {
                    document.body.classList.remove('keyboard-navigation');
                });
            }
            
            addSkipLinks() {
                const skipLink = document.createElement('a');
                skipLink.href = '#main-content';
                skipLink.textContent = 'Skip to main content';
                skipLink.className = 'skip-link sr-only';
                skipLink.style.cssText = `
                    position: absolute;
                    top: -40px;
                    left: 6px;
                    background: var(--primary);
                    color: white;
                    padding: 8px;
                    text-decoration: none;
                    border-radius: 4px;
                    z-index: 10000;
                `;
                
                skipLink.addEventListener('focus', () => {
                    skipLink.style.top = '6px';
                });
                
                skipLink.addEventListener('blur', () => {
                    skipLink.style.top = '-40px';
                });
                
                document.body.insertBefore(skipLink, document.body.firstChild);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
            new MobileNavigation();
            new AccessibilityManager();
            
            // Additional sidebar persistence for desktop
            const sidebar = document.querySelector('.sidebar');
            if (sidebar && window.innerWidth > 1024) {
                // Ensure sidebar is always visible on page load
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.visibility = 'visible';
                sidebar.style.opacity = '1';
                sidebar.style.position = 'fixed';
                sidebar.style.left = '0';
                sidebar.style.top = '0';
                sidebar.style.height = '100vh';
                sidebar.style.zIndex = '1000';
                
                // Monitor for any changes that might hide the sidebar
                const observer = new MutationObserver(() => {
                    if (window.innerWidth > 1024) {
                        sidebar.style.transform = 'translateX(0)';
                        sidebar.style.visibility = 'visible';
                        sidebar.style.opacity = '1';
                        sidebar.style.position = 'fixed';
                        sidebar.style.left = '0';
                        sidebar.style.top = '0';
                        sidebar.style.height = '100vh';
                        sidebar.style.zIndex = '1000';
                    }
                });
                
                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
                
                // Ensure sidebar stays visible during scroll
                window.addEventListener('scroll', () => {
                    if (window.innerWidth > 1024) {
                        sidebar.style.transform = 'translateX(0)';
                        sidebar.style.visibility = 'visible';
                        sidebar.style.opacity = '1';
                        sidebar.style.position = 'fixed';
                        sidebar.style.left = '0';
                        sidebar.style.top = '0';
                        sidebar.style.height = '100vh';
                        sidebar.style.zIndex = '1000';
                    }
                });
            }
        });
    </script>
</body>
</html> 