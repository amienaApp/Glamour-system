<?php
/**
 * Admin Model
 * Handles admin user management and authentication
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';

class Admin {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = MongoDB::getInstance();
        $this->collection = $this->db->getCollection('admins');
    }

    /**
     * Create a new admin
     */
    public function createAdmin($adminData) {
        // Validate required fields
        $requiredFields = ['username', 'email', 'password', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($adminData[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        // Check if email already exists
        $existingEmail = $this->collection->findOne(['email' => $adminData['email']]);
        if ($existingEmail) {
            throw new Exception("Email already registered");
        }

        // Check if username already exists (check both username and name fields)
        $existingUsername = $this->collection->findOne(['username' => $adminData['username']]);
        if (!$existingUsername) {
            $existingUsername = $this->collection->findOne(['name' => $adminData['username']]);
        }
        if ($existingUsername) {
            throw new Exception("Username already exists");
        }

        // Hash password
        $adminData['password'] = password_hash($adminData['password'], PASSWORD_DEFAULT);
        
        // Add additional fields
        $adminData['status'] = 'active';
        $adminData['created_at'] = date('Y-m-d H:i:s');
        $adminData['updated_at'] = date('Y-m-d H:i:s');

        // Insert admin into database
        $result = $this->collection->insertOne($adminData);
        
        if ($result->getInsertedId()) {
            // Return admin data without password
            unset($adminData['password']);
            $adminData['_id'] = $result->getInsertedId();
            return $adminData;
        }

        throw new Exception("Failed to create admin");
    }

    /**
     * Authenticate admin login
     */
    public function login($username, $password) {
        // Find admin by username, name, or email
        $admin = $this->collection->findOne(['username' => $username]);
        if (!$admin) {
            $admin = $this->collection->findOne(['name' => $username]);
        }
        if (!$admin) {
            $admin = $this->collection->findOne(['email' => $username]);
        }

        if (!$admin) {
            throw new Exception("Invalid username or email");
        }

        // Verify password
        if (!password_verify($password, $admin['password'])) {
            throw new Exception("Invalid password");
        }

        // Check if admin is active (default to active if no status field)
        if (isset($admin['status']) && $admin['status'] !== 'active') {
            throw new Exception("Account is not active");
        }

        // Return admin data without password
        unset($admin['password']);
        return $admin;
    }

    /**
     * Get admin by ID
     */
    public function getById($adminId) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $admin = $this->collection->findOne(['_id' => $adminId]);
        if ($admin) {
            unset($admin['password']);
            // Ensure consistent field names for frontend
            if (isset($admin['name']) && !isset($admin['username'])) {
                $admin['username'] = $admin['name'];
            }
            if (!isset($admin['status'])) {
                $admin['status'] = 'active';
            }
            if (!isset($admin['role'])) {
                $admin['role'] = 'admin';
            }
        }
        return $admin;
    }

    /**
     * Get all admins
     */
    public function getAllAdmins() {
        $admins = $this->collection->find();
        $result = [];
        foreach ($admins as $admin) {
            unset($admin['password']);
            // Ensure consistent field names for frontend
            if (isset($admin['name']) && !isset($admin['username'])) {
                $admin['username'] = $admin['name'];
            }
            if (!isset($admin['status'])) {
                $admin['status'] = 'active';
            }
            if (!isset($admin['role'])) {
                $admin['role'] = 'admin';
            }
            $result[] = $admin;
        }
        return $result;
    }

    /**
     * Update admin
     */
    public function updateAdmin($adminId, $updateData) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        // Remove password from update data if not being changed
        if (!isset($updateData['password']) || empty($updateData['password'])) {
            unset($updateData['password']);
        } else {
            $updateData['password'] = password_hash($updateData['password'], PASSWORD_DEFAULT);
        }

        // Handle username/name field mapping for existing data
        if (isset($updateData['username'])) {
            // Check if we're updating an existing admin with 'name' field
            $existingAdmin = $this->collection->findOne(['_id' => $adminId]);
            if ($existingAdmin && isset($existingAdmin['name']) && !isset($existingAdmin['username'])) {
                // Convert name to username for consistency
                $updateData['name'] = $updateData['username'];
                unset($updateData['username']);
            }
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $result = $this->collection->updateOne(
            ['_id' => $adminId],
            ['$set' => $updateData]
        );

        return $result->getModifiedCount() > 0;
    }

    /**
     * Update admin status
     */
    public function updateStatus($adminId, $status) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        $result = $this->collection->updateOne(
            ['_id' => $adminId],
            ['$set' => ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]]
        );

        return $result->getModifiedCount() > 0;
    }

    /**
     * Delete admin
     */
    public function deleteAdmin($adminId) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        // Don't allow deletion of the last admin
        $totalAdmins = $this->collection->countDocuments();
        if ($totalAdmins <= 1) {
            throw new Exception("Cannot delete the last admin");
        }

        $result = $this->collection->deleteOne(['_id' => $adminId]);
        return $result->getDeletedCount() > 0;
    }

    /**
     * Get admin statistics
     */
    public function getAdminStatistics() {
        $totalAdmins = $this->collection->countDocuments();
        // For existing data without status field, consider all as active
        $activeAdmins = $this->collection->countDocuments(['$or' => [
            ['status' => 'active'],
            ['status' => ['$exists' => false]]
        ]]);
        $inactiveAdmins = $this->collection->countDocuments(['status' => 'inactive']);

        return [
            'total' => $totalAdmins,
            'active' => $activeAdmins,
            'inactive' => $inactiveAdmins
        ];
    }

    /**
     * Check if admin exists
     */
    public function adminExists($adminId) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        return $this->collection->countDocuments(['_id' => $adminId]) > 0;
    }

    /**
     * Get admin by email
     */
    public function getByEmail($email) {
        $admin = $this->collection->findOne(['email' => $email]);
        if ($admin) {
            unset($admin['password']);
        }
        return $admin;
    }

    /**
     * Get admin by username or name
     */
    public function getByUsername($username) {
        $admin = $this->collection->findOne(['username' => $username]);
        if (!$admin) {
            $admin = $this->collection->findOne(['name' => $username]);
        }
        if ($admin) {
            unset($admin['password']);
        }
        return $admin;
    }

    /**
     * Change admin password
     */
    public function changePassword($adminId, $newPassword) {
        // Convert string ID to ObjectId if needed
        if (is_string($adminId)) {
            try {
                $adminId = new MongoDB\BSON\ObjectId($adminId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $result = $this->collection->updateOne(
            ['_id' => $adminId],
            ['$set' => [
                'password' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ]]
        );

        return $result->getModifiedCount() > 0;
    }
}
?>
