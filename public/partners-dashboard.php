<?php
require_once '../config/session.php';

// Check if user is logged in and is a business customer
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'business') {
    header('Location: partners-login.php');
    exit;
}

require_once '../vendor/autoload.php';
use App\Models\User;
use App\Models\SupportMessage;

$userModel = new User();
$user = $userModel->getById($_SESSION['user_id']);

// Profile info
$company = $user['company'] ?? '-';
$contact = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$email = $user['email'] ?? '-';
$status = ucfirst($user['status'] ?? 'Pending');
$lastLogin = isset($user['last_login']) && $user['last_login'] ?
    (is_object($user['last_login']) && method_exists($user['last_login'], 'toDateTime')
        ? $user['last_login']->toDateTime()->format('Y-m-d H:i')
        : date('Y-m-d H:i', strtotime($user['last_login']))) : 'N/A';

// Support summary
$supportModel = new SupportMessage();
$supportMessages = $supportModel->getByUserId($_SESSION['user_id']);
$pendingCount = 0;
$repliedCount = 0;
$recentMessages = [];
foreach ($supportMessages as $msg) {
    if ($msg['status'] === 'pending') $pendingCount++;
    if ($msg['status'] === 'replied') $repliedCount++;
}
$recentMessages = array_slice($supportMessages, 0, 2);

function formatSupportDate($date) {
    if (!$date) return 'N/A';
    if (is_object($date) && method_exists($date, 'toDateTime')) {
        return $date->toDateTime()->format('Y-m-d H:i');
    } else {
        return date('Y-m-d H:i', strtotime($date));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners Dashboard - PHITSOL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="assets/css/partners-layout.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Dashboard-specific styles */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: var(--spacing-6);
            margin-bottom: var(--spacing-8);
        }
        
        .summary-card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            padding: var(--spacing-8);
            margin-bottom: var(--spacing-6);
            display: flex;
            flex-direction: column;
            min-height: 320px;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
        }
        
        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
            margin-bottom: var(--spacing-6);
        }
        
        .summary-header i {
            font-size: var(--font-size-2xl);
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .summary-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.025em;
        }
        
        .summary-section {
            margin-bottom: var(--spacing-5);
        }
        
        .summary-label {
            color: var(--gray-600);
            font-size: var(--font-size-sm);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--spacing-1);
        }
        
        .summary-value {
            color: var(--gray-900);
            font-weight: 700;
            font-size: var(--font-size-lg);
        }
        
        .summary-link {
            margin-top: auto;
            align-self: flex-start;
            padding: var(--spacing-3) var(--spacing-5);
            border-radius: var(--border-radius-md);
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
            box-shadow: var(--shadow-md);
        }
        
        .summary-link:hover {
            background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }
        
        .support-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .support-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 0;
        }
        .support-item:last-child {
            border-bottom: none;
        }
        .support-subject {
            font-weight: 600;
            color: #1e293b;
            font-size: 1rem;
        }
        .support-meta {
            color: #64748b;
            font-size: 0.92rem;
        }
        .support-status {
            font-size: 0.92rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; gap: 1rem; }
            .summary-card { padding: 1.25rem; min-height: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="partners-sidebar">
        <div class="sidebar-header">
            <a href="partners-dashboard.php" class="sidebar-brand">
                <img 
                    src="assets/img/logo_white.png"
                    alt="PHITSOL Logo"
                    class="phitsol-logo"
                    id="phitsol-logo"
                >
            </a>
        </div>
        
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="partners-dashboard.php" class="sidebar-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="profile.php" class="sidebar-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="company-profile.php" class="sidebar-link">
                    <i class="fas fa-building"></i>
                    <span>Company Profile</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="purchased-products.php" class="sidebar-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Purchased Products</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="product-catalogue.php" class="sidebar-link">
                    <i class="fas fa-book"></i>
                    <span>Product Catalogue</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="contact-support.php" class="sidebar-link">
                    <i class="fas fa-headset"></i>
                    <span>Contact Support</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="partners-main">
        <!-- Partners Header -->
        <div class="partners-header">
            <div class="header-left">
                <div>
                    <h1 class="header-title">Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($contact); ?></p>
                </div>
                <a href="index.php" class="header-home-link" title="Go to Home page">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="header-user">
                <div class="new-user-info">
                    <div class="user-trigger">
                        <div class="user-icon">
                            <span><?php echo strtoupper(substr($contact, 0, 1)); ?></span>
                        </div>
                        <div class="user-name"><?php echo htmlspecialchars($contact); ?></div>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="new-user-dropdown">
                        <a href="index.php" class="dropdown-item">
                            <i class="fas fa-home"></i>
                            Home
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
                <button id="mobileMenuToggle" class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
                <div class="dashboard-grid">
                    <!-- Profile Summary Card -->
                    <div class="summary-card">
                        <div class="summary-header">
                            <i class="fas fa-user-circle"></i>
                            <span class="summary-title">My Profile</span>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Name</div>
                            <div class="summary-value"><?php echo htmlspecialchars($contact); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Company</div>
                            <div class="summary-value"><?php echo htmlspecialchars($company); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Email</div>
                            <div class="summary-value"><?php echo htmlspecialchars($email); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Account Status</div>
                            <span class="status-badge status-<?php echo strtolower($user['status'] ?? 'pending'); ?>"><?php echo $status; ?></span>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Last Login</div>
                            <div class="summary-value"><?php echo htmlspecialchars($lastLogin); ?></div>
                        </div>
                        <a href="profile.php" class="summary-link">
                            <i class="fas fa-arrow-right"></i> View Profile
                        </a>
                    </div>
                    
                    <!-- Company Profile Summary Card -->
                    <div class="summary-card">
                        <div class="summary-header">
                            <i class="fas fa-building"></i>
                            <span class="summary-title">Company Profile</span>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Company Name</div>
                            <div class="summary-value"><?php echo htmlspecialchars($user['company_name'] ?? $user['company'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Business Permit</div>
                            <div class="summary-value"><?php echo htmlspecialchars($user['business_permit'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">TIN Number</div>
                            <div class="summary-value"><?php echo htmlspecialchars($user['tin_number'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Documents Submitted</div>
                            <div class="summary-value">
                                <?php 
                                $documents = $user['documents'] ?? [];
                                // Convert BSONDocument to array if needed
                                if ($documents instanceof \MongoDB\Model\BSONDocument) {
                                    $documents = $documents->getArrayCopy();
                                } elseif (!is_array($documents)) {
                                    $documents = [];
                                }
                                $submittedCount = array_sum(array_map('intval', $documents));
                                echo $submittedCount . ' / ' . count($documents);
                                ?>
                            </div>
                        </div>
                        <a href="company-profile.php" class="summary-link">
                            <i class="fas fa-arrow-right"></i> View Company Profile
                        </a>
                    </div>
                    
                    <!-- Support Summary Card -->
                    <div class="summary-card">
                        <div class="summary-header">
                            <i class="fas fa-envelope"></i>
                            <span class="summary-title">Support / Inquiry</span>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Pending Inquiries</div>
                            <div class="summary-value"><?php echo $pendingCount; ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Replied Inquiries</div>
                            <div class="summary-value"><?php echo $repliedCount; ?></div>
                        </div>
                        <div class="summary-section">
                            <div class="summary-label">Recent Inquiries</div>
                            <ul class="support-list">
                                <?php if (empty($recentMessages)): ?>
                                    <li class="support-item support-meta">No recent inquiries.</li>
                                <?php else: ?>
                                    <?php foreach ($recentMessages as $msg): ?>
                                        <li class="support-item">
                                            <div class="support-subject"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                            <div class="support-meta">
                                                <?php echo htmlspecialchars($msg['purpose']); ?> · <?php echo formatSupportDate($msg['created_at'] ?? null); ?>
                                                <span class="support-status status-badge status-<?php echo $msg['status']; ?>"><?php echo ucfirst($msg['status']); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <a href="contact-support.php" class="summary-link">
                            <i class="fas fa-arrow-right"></i> Go to Support
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="mobile-overlay"></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/unified-layout.js?v=<?php echo time(); ?>"></script>
    <script>
        // Initialize modern UI features
        document.addEventListener('DOMContentLoaded', function() {
            if (window.modernUnifiedLayout) {
        
            }
        });
    </script>
</body>
</html> 