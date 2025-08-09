<?php
require_once '../config/session.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'business') {
        header('Location: partners-dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Load Composer autoloader
require_once '../vendor/autoload.php';

use App\Models\User;

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($username)) {
            $errors[] = 'Username or email is required';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        }

        // If no validation errors, attempt login
        if (empty($errors)) {
            try {
                $userModel = new User();
                $user = $userModel->authenticate($username, $password);
                
                if ($user) {
                    // Check if user is a business customer
                    if ($user['role'] !== 'business') {
                        $errors[] = 'Access denied. This portal is for business customers only.';
                    } elseif ($user['status'] !== 'active') {
                        if ($user['status'] === 'pending') {
                            $errors[] = 'Your account is pending approval. Please wait for admin approval before logging in.';
                        } else {
                            $errors[] = 'Your account has been deactivated. Please contact support.';
                        }
                    } else {
                        // Set session variables
                        $_SESSION['user_id'] = (string) $user['_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name'] = $user['last_name'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login_time'] = time();
                        
                        // Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Redirect to Partners Dashboard
                        header('Location: partners-dashboard.php');
                        exit;
                    }
                } else {
                    $errors[] = 'Invalid username/email or password';
                }
            } catch (\Exception $e) {
                error_log('Partners Portal Login error: ' . $e->getMessage());
                $errors[] = 'An error occurred during login. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners Portal Login - PHITSOL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/partners-layout.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Login-specific styles */
        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--primary-50) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-4);
            font-family: var(--font-family);
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-radius: var(--border-radius-xl);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            padding: var(--spacing-8);
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-8);
            position: relative;
        }
        
        .login-logo {
            margin-bottom: var(--spacing-6);
        }
        
        .login-logo img {
            height: 60px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform var(--transition-fast);
        }
        
        .login-logo img:hover {
            transform: scale(1.05);
        }
        
        .login-title {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: var(--spacing-2);
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-subtitle {
            color: var(--gray-600);
            font-size: var(--font-size-lg);
            font-weight: 500;
        }
        
        .login-body {
            position: relative;
        }
        
        .form-group {
            margin-bottom: var(--spacing-6);
            position: relative;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--spacing-2);
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .form-control {
            width: 100%;
            padding: var(--spacing-4);
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-md);
            font-size: var(--font-size-base);
            font-family: var(--font-family);
            transition: all var(--transition-fast);
            background: white;
            position: relative;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-100);
            transform: translateY(-1px);
        }
        
        .form-control::placeholder {
            color: var(--gray-400);
        }
        
        .input-icon {
            position: absolute;
            left: var(--spacing-4);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: var(--font-size-lg);
            transition: color var(--transition-fast);
            pointer-events: none;
        }
        
        .form-control:focus + .input-icon {
            color: var(--primary-500);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon .form-control {
            padding-left: calc(var(--spacing-4) * 2 + 20px);
        }
        
        .btn-login {
            width: 100%;
            padding: var(--spacing-4) var(--spacing-6);
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: white;
            border: none;
            border-radius: var(--border-radius-md);
            font-size: var(--font-size-base);
            font-weight: 600;
            font-family: var(--font-family);
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
            overflow: hidden;
            margin-top: var(--spacing-4);
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left var(--transition-normal);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: var(--spacing-6);
            padding-top: var(--spacing-6);
            border-top: 1px solid var(--gray-200);
        }
        
        .back-link {
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
        }
        
        .back-link:hover {
            color: var(--primary-600);
            transform: translateX(-2px);
        }
        
        .alert {
            padding: var(--spacing-4) var(--spacing-5);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-6);
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
            font-weight: 500;
        }
        
        .alert-danger {
            background: var(--error-50);
            border-color: var(--error-500);
            color: var(--error-600);
        }
        
        .alert ul {
            margin: var(--spacing-2) 0 0 0;
            padding-left: var(--spacing-4);
        }
        
        .alert li {
            margin-bottom: var(--spacing-1);
        }
        
        .alert li:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }
            
            .login-card {
                padding: var(--spacing-6);
            }
            
            .login-title {
                font-size: var(--font-size-2xl);
            }
            
            .login-logo img {
                height: 50px;
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="assets/img/logo_black.png" alt="PHITSOL Logo">
                </div>
                <h1 class="login-title">Partners Portal</h1>
                <p class="login-subtitle">Business Customer Login</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Login Error!</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="partners-login.php" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Enter your username or email" required autocomplete="username"
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <div class="invalid-feedback">
                            Please enter your username or email.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-with-icon">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required autocomplete="current-password">
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In to Partners Portal
                    </button>
                </form>
                
                <div class="login-footer">
                    <a href="index.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html> 