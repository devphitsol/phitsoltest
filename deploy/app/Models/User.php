<?php

namespace App\Models;

use App\Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class User
{
    private $collection;
    private const MAX_USERS = 100;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->collection = $db->getCollection('users');
    }

    /**
     * Get all users
     */
    public function getAll()
    {
        try {
            $cursor = $this->collection->find(
                [],
                ['sort' => ['created_at' => -1]]
            );
            return $cursor->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get active users only
     */
    public function getActive()
    {
        try {
            $cursor = $this->collection->find(
                ['status' => 'active'],
                ['sort' => ['created_at' => -1]]
            );
            return $cursor->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get user by ID
     */
    public function getById($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $user = $this->collection->findOne(['_id' => $id]);
            return $user ? (array) $user : null;
        } catch (\Exception $e) {
            error_log("Error fetching user by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by email
     */
    public function getByEmail($email)
    {
        try {
            $user = $this->collection->findOne(['email' => $email]);
            return $user ? (array) $user : null;
        } catch (\Exception $e) {
            error_log("Error fetching user by email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by username
     */
    public function getByUsername($username)
    {
        try {
            $user = $this->collection->findOne(['username' => $username]);
            return $user ? (array) $user : null;
        } catch (\Exception $e) {
            error_log("Error fetching user by username: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new user
     */
    public function create($data)
    {
        try {
            // Check if we've reached the maximum number of users
            $totalUsers = $this->collection->countDocuments([]);
            if ($totalUsers >= self::MAX_USERS) {
                throw new \Exception("Maximum number of users (" . self::MAX_USERS . ") reached");
            }

            // Check if email already exists
            if ($this->getByEmail($data['email'])) {
                throw new \Exception("Email already exists");
            }

            // Check if username already exists
            if ($this->getByUsername($data['username'])) {
                throw new \Exception("Username already exists");
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Set default values
            $data['created_at'] = $this->getCurrentDateTime();
            $data['updated_at'] = $this->getCurrentDateTime();
            $data['status'] = $data['status'] ?? 'active';
            $data['role'] = $data['role'] ?? 'employee';
            $data['last_login'] = null;

            $result = $this->collection->insertOne($data);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $data['updated_at'] = $this->getCurrentDateTime();
            
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']); // Don't update password if not provided
            }

            // Check if email already exists (excluding current user)
            if (isset($data['email'])) {
                $existingUser = $this->getByEmail($data['email']);
                if ($existingUser && $existingUser['_id'] != $id) {
                    throw new \Exception("Email already exists");
                }
            }

            // Check if username already exists (excluding current user)
            if (isset($data['username'])) {
                $existingUser = $this->getByUsername($data['username']);
                if ($existingUser && $existingUser['_id'] != $id) {
                    throw new \Exception("Username already exists");
                }
            }
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                ['$set' => $data]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update user profile (for partners portal)
     */
    public function updateProfile($id, $data)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $updateData = [
                'updated_at' => $this->getCurrentDateTime()
            ];

            // Add provided data to update
            foreach ($data as $key => $value) {
                if ($value !== null && $value !== '') {
                    $updateData[$key] = $value;
                }
            }
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                ['$set' => $updateData]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating user profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $result = $this->collection->deleteOne(['_id' => $id]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $user = $this->getById($id);
            if (!$user) {
                return false;
            }

            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$set' => [
                        'status' => $newStatus,
                        'updated_at' => $this->getCurrentDateTime()
                    ]
                ]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error toggling user status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve business customer account
     */
    public function approveBusinessAccount($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $user = $this->getById($id);
            if (!$user || $user['role'] !== 'business' || $user['status'] !== 'pending') {
                return false;
            }
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$set' => [
                        'status' => 'active',
                        'updated_at' => $this->getCurrentDateTime()
                    ]
                ]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error approving business account: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Authenticate user
     */
    public function authenticate($username, $password)
    {
        try {
            // Try to find user by username or email
            $user = $this->getByUsername($username);
            if (!$user) {
                $user = $this->getByEmail($username);
            }

            if (!$user) {
                return null;
            }

            // Check if user is active
            if ($user['status'] !== 'active') {
                return null;
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['_id']);
                return $user;
            }

            return null;
        } catch (\Exception $e) {
            error_log("Error authenticating user: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update last login time
     */
    public function updateLastLogin($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$set' => [
                        'last_login' => $this->getCurrentDateTime(),
                        'updated_at' => $this->getCurrentDateTime()
                    ]
                ]
            );
        } catch (\Exception $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }

    /**
     * Get user count
     */
    public function getCount()
    {
        try {
            return $this->collection->countDocuments([]);
        } catch (\Exception $e) {
            error_log("Error counting users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get active user count
     */
    public function getActiveCount()
    {
        try {
            return $this->collection->countDocuments(['status' => 'active']);
        } catch (\Exception $e) {
            error_log("Error counting active users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get admin user count
     */
    public function getAdminCount()
    {
        try {
            return $this->collection->countDocuments(['role' => 'admin']);
        } catch (\Exception $e) {
            error_log("Error counting admin users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get business user count
     */
    public function getBusinessUserCount()
    {
        try {
            return $this->collection->countDocuments(['role' => 'business']);
        } catch (\Exception $e) {
            error_log("Error counting business users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all business users
     */
    public function getBusinessUsers()
    {
        try {
            $cursor = $this->collection->find(
                ['role' => 'business'],
                ['sort' => ['created_at' => -1]]
            );
            return $cursor->toArray();
        } catch (\Exception $e) {
            error_log("Error fetching business users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get employee count
     */
    public function getEmployeeCount()
    {
        try {
            return $this->collection->countDocuments(['role' => 'employee']);
        } catch (\Exception $e) {
            error_log("Error counting employees: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get pending user count
     */
    public function getPendingCount()
    {
        try {
            return $this->collection->countDocuments(['status' => 'pending']);
        } catch (\Exception $e) {
            error_log("Error counting pending users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get maximum users allowed
     */
    public function getMaxUsers()
    {
        return self::MAX_USERS;
    }

    /**
     * Validate user data
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        if (empty($data['name'])) {
            $errors[] = 'Full name is required';
        }

        return $errors;
    }

    /**
     * Get document status for a user
     */
    public function getDocumentStatus($userId)
    {
        $user = $this->getById($userId);
        return $user['document_status'] ?? [];
    }

    /**
     * Update document status for a user
     */
    public function updateDocumentStatus($userId, $docKey, $status, $note = '')
    {
        try {
            if (is_string($userId)) {
                $userId = new \MongoDB\BSON\ObjectId($userId);
            }
            $update = [
                "document_status.$docKey.status" => $status,
                "document_status.$docKey.admin_note" => $note,
                'updated_at' => $this->getCurrentDateTime()
            ];
            $result = $this->collection->updateOne(
                ['_id' => $userId],
                ['$set' => $update]
            );
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            error_log("Error updating document status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get business users count
     */
    public function getBusinessCount()
    {
        try {
            return $this->collection->countDocuments(['role' => 'business']);
        } catch (\Exception $e) {
            error_log("Error counting business users: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update user documents
     */
    public function updateUserDocuments($userId, $documents)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($userId)) {
                $userId = new ObjectId($userId);
            }
            
            $result = $this->collection->updateOne(
                ['_id' => $userId],
                ['$set' => ['documents' => $documents]]
            );
            
            return $result->getModifiedCount() > 0;
            
        } catch (\Exception $e) {
            error_log('Error updating user documents: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get users with pagination, search, and filters
     */
    public function getUsersWithFilters($options = [])
    {
        try {
            $page = $options['page'] ?? 1;
            $limit = $options['limit'] ?? 10;
            $search = $options['search'] ?? '';
            $role = $options['role'] ?? '';
            $status = $options['status'] ?? '';
            $sortBy = $options['sortBy'] ?? 'created_at';
            $sortOrder = $options['sortOrder'] ?? -1;
            
            // Build filter criteria
            $filter = [];
            
            // Search filter
            if (!empty($search)) {
                $filter['$or'] = [
                    ['name' => ['$regex' => $search, '$options' => 'i']],
                    ['email' => ['$regex' => $search, '$options' => 'i']],
                    ['username' => ['$regex' => $search, '$options' => 'i']]
                ];
            }
            
            // Role filter
            if (!empty($role)) {
                $filter['role'] = $role;
            }
            
            // Status filter
            if (!empty($status)) {
                $filter['status'] = $status;
            }
            
            // Calculate skip value for pagination
            $skip = ($page - 1) * $limit;
            
            // Get total count for pagination
            $totalCount = $this->collection->countDocuments($filter);
            
            // Get users with filters and pagination
            $cursor = $this->collection->find(
                $filter,
                [
                    'sort' => [$sortBy => $sortOrder],
                    'skip' => $skip,
                    'limit' => $limit
                ]
            );
            
            $users = $cursor->toArray();
            
            return [
                'users' => $users,
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $limit),
                'limit' => $limit,
                'hasNextPage' => $page < ceil($totalCount / $limit),
                'hasPrevPage' => $page > 1
            ];
        } catch (\Exception $e) {
            error_log("Error fetching users with filters: " . $e->getMessage());
            return [
                'users' => [],
                'totalCount' => 0,
                'currentPage' => 1,
                'totalPages' => 0,
                'limit' => $limit ?? 10,
                'hasNextPage' => false,
                'hasPrevPage' => false
            ];
        }
    }

    /**
     * Get users for API response
     */
    public function getUsersForAPI($options = [])
    {
        $result = $this->getUsersWithFilters($options);
        
        // Transform users for API response
        $transformedUsers = [];
        foreach ($result['users'] as $user) {
            $transformedUsers[] = [
                'id' => (string) $user['_id'],
                'name' => $user['name'] ?? 'N/A',
                'email' => $user['email'],
                'username' => $user['username'],
                'role' => $user['role'],
                'status' => $user['status'],
                'last_login' => $user['last_login'] ? 
                    (is_object($user['last_login']) ? $user['last_login']->toDateTime()->format('Y-m-d H:i:s') : $user['last_login']) : 
                    null,
                'created_at' => $user['created_at'] ? 
                    (is_object($user['created_at']) ? $user['created_at']->toDateTime()->format('Y-m-d H:i:s') : $user['created_at']) : 
                    null,
                'updated_at' => $user['updated_at'] ? 
                    (is_object($user['updated_at']) ? $user['updated_at']->toDateTime()->format('Y-m-d H:i:s') : $user['updated_at']) : 
                    null
            ];
        }
        
        return [
            'users' => $transformedUsers,
            'pagination' => [
                'totalCount' => $result['totalCount'],
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'limit' => $result['limit'],
                'hasNextPage' => $result['hasNextPage'],
                'hasPrevPage' => $result['hasPrevPage']
            ]
        ];
    }

    /**
     * Get available roles for filtering
     */
    public function getAvailableRoles()
    {
        try {
            $roles = $this->collection->distinct('role');
            return array_values(array_filter($roles));
        } catch (\Exception $e) {
            error_log("Error fetching available roles: " . $e->getMessage());
            return ['admin', 'employee', 'business'];
        }
    }

    /**
     * Get available statuses for filtering
     */
    public function getAvailableStatuses()
    {
        try {
            $statuses = $this->collection->distinct('status');
            return array_values(array_filter($statuses));
        } catch (\Exception $e) {
            error_log("Error fetching available statuses: " . $e->getMessage());
            return ['active', 'inactive', 'pending'];
        }
    }

    /**
     * Get current date time in appropriate format
     */
    private function getCurrentDateTime()
    {
        $db = Database::getInstance();
        if ($db->isUsingFileStorage()) {
            return date('Y-m-d H:i:s');
        } else {
            // For MongoDB, use UTCDateTime
            if (extension_loaded('mongodb')) {
                return new \MongoDB\BSON\UTCDateTime();
            } else {
                return date('Y-m-d H:i:s');
            }
        }
    }
} 