<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>User Management - PHITSOL Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Complete user management styles */
        * {
            box-sizing: border-box;
        }
        
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
        
        /* Statistics Grid Styles */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:nth-child(1) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .stat-content {
            text-align: left;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-icon {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
        
        /* Card Styles */
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
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
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #343a40;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Avatar */
        .avatar {
            display: flex;
            align-items: center;
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }
        
        .bg-success {
            background-color: #28a745 !important;
            color: white !important;
        }
        
        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .btn-edit {
            background: #ffc107;
            color: white;
        }
        
        .btn-edit:hover {
            background: #e0a800;
            color: white;
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
        
        .btn-toggle {
            background: #17a2b8;
            color: white;
        }
        
        .btn-toggle:hover {
            background: #138496;
            color: white;
            text-decoration: none;
        }
        
        /* Empty State */
        .text-center {
            text-align: center !important;
        }
        
        .py-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        
        .fa-3x {
            font-size: 3rem !important;
        }
        
        /* Bootstrap Overrides */
        .row {
            margin-left: -15px;
            margin-right: -15px;
        }
        
        .col-md-3 {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .d-flex {
            display: flex !important;
        }
        
        .align-items-center {
            align-items: center !important;
        }
        
        .justify-content-center {
            justify-content: center !important;
        }
        
        .gap-2 {
            gap: 0.5rem !important;
        }
        
        .me-2 {
            margin-right: 0.5rem !important;
        }
        
        .me-3 {
            margin-right: 1rem !important;
        }
        
        .mb-0 {
            margin-bottom: 0 !important;
        }
        
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        
        .p-0 {
            padding: 0 !important;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .fw-bold {
            font-weight: 700 !important;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .admin-header {
                padding: 1rem;
                margin: -2rem -2rem 2rem -2rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
        }
        
        /* PHITSOL Logo Styles */
        .phitsol-logo {
            height: 45px !important;
            width: auto !important;
            display: inline-block !important;
            vertical-align: middle !important;
            margin-right: 10px !important;
            max-width: 100% !important;
            object-fit: contain !important;
            filter: brightness(1.1) contrast(1.1) !important;
        }
        
        .sidebar-brand {
            display: flex !important;
            align-items: center !important;
            text-decoration: none !important;
        }
        
        .sidebar-brand:hover {
            text-decoration: none !important;
        }
        

    </style>
</head>
<body>
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
            <a href="index.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="index.php?action=slider" class="nav-link">
                <i class="fas fa-images"></i>
                Slider Management
            </a>
            <a href="index.php?action=blog" class="nav-link">
                <i class="fas fa-blog"></i>
                Blog Management
            </a>

            <a href="index.php?action=users" class="nav-link active">
                <i class="fas fa-users"></i>
                User Management
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Content Header -->
        <div class="content-header">
            <div class="header-info">
                <h1 class="page-title">
                    <i class="fas fa-users"></i>
                    User Management
                </h1>
                <div class="stats-info">
                    <span class="stat-item"><?php echo $userCount; ?> users</span>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?php echo $activeUserCount; ?> active</span>
                    <span class="stat-divider">•</span>
                    <span class="stat-item"><?php echo $adminUserCount; ?> admins</span>
                </div>
            </div>
            <div class="header-actions">
                <a href="index.php?action=users&method=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add User
                </a>
            </div>
        </div>

        <!-- Content Body -->
        <div class="content-body">

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

            <!-- Statistics Grid -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $userCount; ?></div>
                                <div class="stat-label">Total Users</div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $activeUserCount; ?></div>
                                <div class="stat-label">Active Users</div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $adminUserCount; ?></div>
                                <div class="stat-label">Admin Users</div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $businessUserCount ?? 0; ?></div>
                                <div class="stat-label">Business Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-info">
                        <h5 class="table-title">
                            <i class="fas fa-users me-2"></i>
                            All Users
                        </h5>
                        <p class="table-description">
                            Complete list of all registered users. Use the action buttons to manage user accounts, roles, and permissions.
                        </p>
                    </div>
                    <div class="table-actions">
                        <!-- Additional actions can be added here if needed -->
                    </div>
                </div>
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No users found</h5>
                        <p class="text-muted">Get started by adding your first user.</p>
                        <a href="index.php?action=users&method=create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add First User
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></div>
                                                    <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php elseif ($user['role'] === 'business'): ?>
                                                <span class="badge bg-primary">Business Customer</span>
                                            <?php elseif ($user['role'] === 'employee'): ?>
                                                <span class="badge bg-secondary">Employee</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = 'bg-success';
                                            $statusText = 'Active';
                                            
                                            if ($user['status'] === 'pending') {
                                                $statusClass = 'bg-warning';
                                                $statusText = 'Pending';
                                            } elseif ($user['status'] === 'inactive') {
                                                $statusClass = 'bg-danger';
                                                $statusText = 'Inactive';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($user['last_login'])): ?>
                                                <?php 
                                                $lastLogin = is_object($user['last_login']) ? $user['last_login']->toDateTime()->format('Y-m-d H:i') : $user['last_login'];
                                                echo htmlspecialchars($lastLogin);
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $createdAt = is_object($user['created_at']) ? $user['created_at']->toDateTime()->format('Y-m-d') : $user['created_at'];
                                            echo htmlspecialchars($createdAt);
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?action=users&method=edit&id=<?php echo $user['_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if ($user['role'] === 'business' && $user['status'] === 'pending'): ?>
                                                    <a href="index.php?action=users&method=approve&id=<?php echo $user['_id']; ?>" class="btn btn-sm btn-outline-success" title="Approve Business Account" onclick="return confirm('Are you sure you want to approve this business account?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="index.php?action=users&method=toggle-status&id=<?php echo $user['_id']; ?>" class="btn btn-sm btn-outline-warning" title="Toggle Status" onclick="return confirm('Are you sure you want to change this user\'s status?')">
                                                    <i class="fas fa-toggle-on"></i>
                                                </a>
                                                
                                                <?php if (!isset($_SESSION['admin_user_id']) || $_SESSION['admin_user_id'] != $user['_id']): ?>
                                                    <a href="index.php?action=users&method=delete&id=<?php echo $user['_id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logo is set to white for admin pages
        // No switching needed as admin pages use dark backgrounds
        
        // Force reload logo if not displaying correctly
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html> 