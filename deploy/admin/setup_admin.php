<?php
/** @phpstan-ignore-file */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;

$userModel = new User();

// If any admin already exists, redirect to login
try {
    if ($userModel->getAdminCount() > 0) {
        $_SESSION['error'] = 'Admin account already exists. Please log in.';
        header('Location: index.php?action=login');
        exit;
    }
} catch (\Throwable $e) {
    $_SESSION['error'] = 'Unable to verify admin status: ' . $e->getMessage();
    header('Location: index.php?action=login');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if ($username === '' || strlen($username) < 3) $errors[] = 'Username must be at least 3 characters';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if ($password === '' || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
        if ($name === '') $errors[] = 'Full name is required';

        if (empty($errors)) {
            try {
                $data = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'name' => $name,
                    'role' => 'admin',
                    'status' => 'active',
                ];
                $createdId = $userModel->create($data);
                if ($createdId) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = (string)$createdId;
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['admin_full_name'] = $name;
                    $_SESSION['admin_role'] = 'admin';
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = 'Failed to create admin account. Please try again.';
                }
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
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
    <title>Setup Admin Account - PHITSOL Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Setup Admin Account</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">No admin user found. Create the first admin account.</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><?php echo htmlspecialchars($e); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                <div class="form-text">At least 6 characters</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-check me-1"></i>Create Admin</button>
                                <a href="index.php?action=login" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>


