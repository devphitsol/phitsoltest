<?php
require_once '../vendor/autoload.php';

use App\Config\Database;
use App\Models\User;

// Simulate controller data
$userModel = new User();
$users = $userModel->getAll();
$userCount = $userModel->getCount();
$activeUserCount = $userModel->getActiveCount();
$adminUserCount = $userModel->getAdminCount();
$maxUsers = $userModel->getMaxUsers();
$employeeCount = $userModel->getEmployeeCount();
$businessUserCount = $userModel->getBusinessUserCount();
$pendingCount = $userModel->getPendingCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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

        .stat-card:nth-child(1) { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card:nth-child(2) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card:nth-child(3) { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card:nth-child(4) { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
        .stat-card:nth-child(5) { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
        .stat-card:nth-child(6) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card:nth-child(7) { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
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
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Statistics Test Page</h1>
        
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
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo count(array_filter($users, function($user) { 
                                $userDate = strtotime($user['created_at']);
                                $currentDate = time();
                                $daysDiff = floor(($currentDate - $userDate) / (60 * 60 * 24));
                                return $daysDiff <= 7;
                            })); ?></div>
                            <div class="stat-label">Recent Users (7 days)</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $pendingCount ?? 0; ?></div>
                            <div class="stat-label">Pending Approvals</div>
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
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $employeeCount ?? 0; ?></div>
                            <div class="stat-label">Employee Users</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Debug Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Admin Users:</strong> <?php echo $adminUserCount; ?></p>
                <p><strong>Employee Users:</strong> <?php echo $employeeCount; ?></p>
                <p><strong>Business Users:</strong> <?php echo $businessUserCount; ?></p>
                <p><strong>Total Users:</strong> <?php echo $userCount; ?></p>
                <p><strong>Active Users:</strong> <?php echo $activeUserCount; ?></p>
                <p><strong>Pending Users:</strong> <?php echo $pendingCount; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
