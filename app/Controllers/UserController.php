<?php

namespace App\Controllers;

// Use the User model with proper namespace
use App\Models\User;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Display user management page
     */
    public function index()
    {
        $users = $this->userModel->getAll();
        $userCount = $this->userModel->getCount();
        $activeUserCount = $this->userModel->getActiveCount();
        $adminUserCount = $this->userModel->getAdminCount();
        $maxUsers = $this->userModel->getMaxUsers();
        
        // Get customer type statistics
        $employeeCount = $this->userModel->getEmployeeCount();
        $businessUserCount = $this->userModel->getBusinessUserCount();
        
        include __DIR__ . '/../../admin/views/users/index.php';
    }

    /**
     * Show create user form
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validateUserData($_POST);
            
            if (empty($data['errors'])) {
                try {
                    $userId = $this->userModel->create($data['data']);
                    if ($userId) {
                        $_SESSION['success'] = 'User created successfully!';
                        header('Location: index.php?action=users');
                        exit;
                    } else {
                        $_SESSION['error'] = 'Failed to create user.';
                    }
                } catch (\Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
            
            if (!empty($data['errors'])) {
                $_SESSION['error'] = implode(', ', $data['errors']);
            }
        }
        
        // Get user count for the form
        $userCount = $this->userModel->getCount();
        $maxUsers = $this->userModel->getMaxUsers();
        
        ob_start();
        include __DIR__ . '/../../admin/views/users/create-content.php';
        $pageContent = ob_get_clean();
        include __DIR__ . '/../../admin/views/layout.php';
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?action=users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validateUserData($_POST, true); // true for update mode
            
            if (empty($data['errors'])) {
                try {
                    if ($this->userModel->update($id, $data['data'])) {
                        $_SESSION['success'] = 'User updated successfully!';
                        header('Location: index.php?action=users');
                        exit;
                    } else {
                        $_SESSION['error'] = 'Failed to update user.';
                    }
                } catch (\Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
            
            if (!empty($data['errors'])) {
                $_SESSION['error'] = implode(', ', $data['errors']);
            }
        }
        
        ob_start();
        include __DIR__ . '/../../admin/views/users/edit-content.php';
        $pageContent = ob_get_clean();
        include __DIR__ . '/../../admin/views/layout.php';
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?action=users');
            exit;
        }

        // Prevent deleting the current admin user
        if (isset($_SESSION['admin_user_id']) && $_SESSION['admin_user_id'] == $id) {
            $_SESSION['error'] = 'You cannot delete your own account.';
            header('Location: index.php?action=users');
            exit;
        }

        if ($this->userModel->delete($id)) {
            $_SESSION['success'] = 'User deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete user.';
        }
        
        header('Location: index.php?action=users');
        exit;
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?action=users');
            exit;
        }

        // Prevent deactivating the current admin user
        if (isset($_SESSION['admin_user_id']) && $_SESSION['admin_user_id'] == $id) {
            $_SESSION['error'] = 'You cannot deactivate your own account.';
            header('Location: index.php?action=users');
            exit;
        }

        if ($this->userModel->toggleStatus($id)) {
            $_SESSION['success'] = 'User status updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update user status.';
        }
        
        header('Location: index.php?action=users');
        exit;
    }

    /**
     * Approve business customer account
     */
    public function approve($id)
    {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?action=users');
            exit;
        }

        // Only allow approval of pending business customers
        if ($user['role'] !== 'business' || $user['status'] !== 'pending') {
            $_SESSION['error'] = 'Only pending business customer accounts can be approved.';
            header('Location: index.php?action=users');
            exit;
        }

        if ($this->userModel->approveBusinessAccount($id)) {
            $_SESSION['success'] = 'Business customer account approved successfully!';
        } else {
            $_SESSION['error'] = 'Failed to approve business customer account.';
        }
        
        header('Location: index.php?action=users');
        exit;
    }

    /**
     * Get all users for layout
     */
    public function getUsers()
    {
        return $this->userModel->getAll();
    }

    /**
     * Get user count for layout
     */
    public function getUserCount()
    {
        return $this->userModel->getCount();
    }

    /**
     * Get business user count for layout
     */
    public function getBusinessCount()
    {
        return $this->userModel->getBusinessUserCount();
    }

    /**
     * Get all business users for layout
     */
    public function getBusinessUsers()
    {
        return $this->userModel->getBusinessUsers();
    }

    /**
     * Get pending user count for layout
     */
    public function getPendingCount()
    {
        return $this->userModel->getPendingCount();
    }

    /**
     * Get max users allowed for layout
     */
    public function getMaxUsers()
    {
        return $this->userModel->getMaxUsers();
    }

    /**
     * Get user by ID for layout
     */
    public function getUserById($id)
    {
        return $this->userModel->getById($id);
    }

    /**
     * Authenticate user login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username and password are required.';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = $user['_id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_full_name'] = $user['name'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid username or password.';
                }
            }
        }
        
        include __DIR__ . '/../../admin/views/login.php';
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    /**
     * Validate user data
     */
    public function validateUserData($data, $isUpdate = false)
    {
        $errors = [];
        $validatedData = [];

        // Validate username
        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } else {
            $validatedData['username'] = trim($data['username']);
        }

        // Validate email
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        } else {
            $validatedData['email'] = trim($data['email']);
        }

        // Validate password (only required for new users)
        if (!$isUpdate || !empty($data['password'])) {
            if (empty($data['password'])) {
                $errors[] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            } else {
                $validatedData['password'] = $data['password'];
            }
        }

        // Validate full name
        if (empty($data['name'])) {
            $errors[] = 'Full name is required';
        } else {
            $validatedData['name'] = trim($data['name']);
        }

        // Optional fields
        $validatedData['role'] = $data['role'] ?? 'employee';
        $validatedData['status'] = $data['status'] ?? 'active';
        
        // Additional optional fields
        if (!empty($data['phone'])) {
            $validatedData['phone'] = trim($data['phone']);
        }
        if (!empty($data['company'])) {
            $validatedData['company'] = trim($data['company']);
        }
        if (!empty($data['address'])) {
            $validatedData['address'] = trim($data['address']);
        }

        return [
            'data' => $validatedData,
            'errors' => $errors
        ];
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'total_users' => $this->userModel->getCount(),
            'active_users' => $this->userModel->getActiveCount(),
            'admin_users' => $this->userModel->getAdminCount(),
            'max_users' => $this->userModel->getMaxUsers()
        ];
    }

    public function updateUserDocuments($data) {
        try {
            $userId = $data['user_id'] ?? null;
            
            if (!$userId) {
                return ['success' => false, 'message' => 'User ID is required'];
            }
            
            // Prepare documents data
            $documents = [
                'company_profile' => isset($data['company_profile']) && $data['company_profile'] === '1',
                'business_permit' => isset($data['business_permit']) && $data['business_permit'] === '1',
                'bir_2303' => isset($data['bir_2303']) && $data['bir_2303'] === '1',
                'gis' => isset($data['gis']) && $data['gis'] === '1',
                'audited_financial' => isset($data['audited_financial']) && $data['audited_financial'] === '1',
                'proof_of_payment' => isset($data['proof_of_payment']) && $data['proof_of_payment'] === '1',
                'valid_id' => isset($data['valid_id']) && $data['valid_id'] === '1',
                'corporate_secretary' => isset($data['corporate_secretary']) && $data['corporate_secretary'] === '1',
                'credit_investigation' => isset($data['credit_investigation']) && $data['credit_investigation'] === '1',
                'peza_certification' => isset($data['peza_certification']) && $data['peza_certification'] === '1'
            ];
            
            // Update documents in database
            $result = $this->userModel->updateUserDocuments($userId, $documents);
            
            if ($result) {
                return ['success' => true, 'message' => 'Documents updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update documents'];
            }
        } catch (\Exception $e) {
            error_log("Error updating user documents: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating documents: ' . $e->getMessage()];
        }
    }

    /**
     * Update company profile information
     */
    public function updateCompanyProfile($data) {
        try {
            $userId = $data['user_id'] ?? null;
            
            if (!$userId) {
                return ['success' => false, 'message' => 'User ID is required'];
            }
            
            // Prepare company profile data
            $profileData = [
                'company_name' => $data['company_name'] ?? '',
                'company_address' => $data['company_address'] ?? '',
                'date_of_incorporation' => $data['date_of_incorporation'] ?? '',
                'tin_number' => $data['tin_number'] ?? '',
                'business_permit' => $data['business_permit'] ?? '',
                'email' => $data['email_address'] ?? '',
                'contact_number' => $data['contact_number'] ?? '',
                'website_url' => $data['website_url'] ?? ''
            ];
            
            // Update company profile in database
            $result = $this->userModel->updateProfile($userId, $profileData);
            
            if ($result) {
                return ['success' => true, 'message' => 'Company profile updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update company profile'];
            }
        } catch (\Exception $e) {
            error_log("Error updating company profile: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating company profile: ' . $e->getMessage()];
        }
    }

    /**
     * Update contact persons information
     */
    public function updateContactPersons($data) {
        try {
            $userId = $data['user_id'] ?? null;
            
            if (!$userId) {
                return ['success' => false, 'message' => 'User ID is required'];
            }
            
            // Prepare contact persons data
            $contactData = [
                'name' => $data['authorized_representative'] ?? '',
                'position' => $data['position_title'] ?? '',
                'contact_number' => $data['representative_contact'] ?? '',
                'email' => $data['representative_email'] ?? '',
                'secondary_contact_name' => $data['secondary_contact_name'] ?? '',
                'secondary_contact_position' => $data['secondary_contact_position'] ?? '',
                'secondary_contact_number' => $data['secondary_contact_number'] ?? '',
                'secondary_contact_email' => $data['secondary_contact_email'] ?? ''
            ];
            
            // Update contact persons in database
            $result = $this->userModel->updateProfile($userId, $contactData);
            
            if ($result) {
                return ['success' => true, 'message' => 'Contact information updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update contact information'];
            }
        } catch (\Exception $e) {
            error_log("Error updating contact persons: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating contact information: ' . $e->getMessage()];
        }
    }
} 