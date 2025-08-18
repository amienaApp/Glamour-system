<?php
/**
 * User Model
 * Handles user registration, authentication, and database operations
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->collection = $this->db->getCollection('users');
    }

    /**
     * Register a new user
     */
    public function register($userData) {
        // Validate required fields
        $requiredFields = ['username', 'email', 'contact_number', 'gender', 'region', 'city', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        // Check if email already exists (only email validation)
        $existingEmail = $this->collection->findOne(['email' => $userData['email']]);
        if ($existingEmail) {
            throw new Exception("Email already registered");
        }

        // Username can be the same as others (no validation needed)

        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Add additional fields
        $userData['status'] = 'active';
        $userData['role'] = 'user';
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');

        // Insert user into database
        $result = $this->collection->insertOne($userData);
        
        if ($result->getInsertedId()) {
            // Return user data without password
            unset($userData['password']);
            $userData['_id'] = $result->getInsertedId();
            return $userData;
        }

        throw new Exception("Failed to register user");
    }

    /**
     * Authenticate user login
     */
    public function login($username, $password) {
        // Find user by username or email
        $user = $this->collection->findOne(['username' => $username]);
        if (!$user) {
            $user = $this->collection->findOne(['email' => $username]);
        }

        if (!$user) {
            throw new Exception("Invalid username or email");
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid password");
        }

        // Check if user is active
        if ($user['status'] !== 'active') {
            throw new Exception("Account is not active");
        }

        // Return user data without password
        unset($user['password']);
        return $user;
    }

    /**
     * Get user by ID
     */
    public function getById($userId) {
        $user = $this->collection->findOne(['_id' => $userId]);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $user = $this->collection->findOne(['username' => $username]);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $user = $this->collection->findOne(['email' => $email]);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $updateData) {
        // Remove sensitive fields that shouldn't be updated directly
        unset($updateData['password']);
        unset($updateData['_id']);
        unset($updateData['created_at']);
        unset($updateData['role']);
        unset($updateData['status']);

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $result = $this->collection->updateOne(
            ['_id' => $userId],
            ['$set' => $updateData]
        );

        return $result->getModifiedCount() > 0;
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->collection->findOne(['_id' => $userId]);
        if (!$user) {
            throw new Exception("User not found");
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Current password is incorrect");
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $result = $this->collection->updateOne(
            ['_id' => $userId],
            ['$set' => [
                'password' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ]]
        );

        return $result->getModifiedCount() > 0;
    }

    /**
     * Delete user account
     */
    public function deleteAccount($userId) {
        $result = $this->collection->deleteOne(['_id' => $userId]);
        return $result->getDeletedCount() > 0;
    }

    /**
     * Get all users (for admin purposes)
     */
    public function getAllUsers($limit = 50, $skip = 0) {
        $users = $this->collection->find([], [
            'limit' => $limit,
            'skip' => $skip,
            'sort' => ['created_at' => -1]
        ]);

        $userList = [];
        foreach ($users as $user) {
            unset($user['password']);
            $userList[] = $user;
        }

        return $userList;
    }

    /**
     * Count total users
     */
    public function getTotalUsers() {
        return $this->collection->countDocuments();
    }

    /**
     * Search users
     */
    public function searchUsers($searchTerm, $limit = 20) {
        $users = $this->collection->find([
            '$text' => ['$search' => $searchTerm]
        ], ['limit' => $limit]);

        $userList = [];
        foreach ($users as $user) {
            unset($user['password']);
            $userList[] = $user;
        }

        return $userList;
    }

    /**
     * Validate email format
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength (optional - allows any password)
     */
    public function validatePassword($password) {
        // Allow any password (minimum 1 character)
        return strlen($password) >= 1;
    }

    /**
     * Generate password reset token
     */
    public function generateResetToken($email) {
        $user = $this->collection->findOne(['email' => $email]);
        if (!$user) {
            throw new Exception("Email not found");
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $result = $this->collection->updateOne(
            ['email' => $email],
            ['$set' => [
                'reset_token' => $token,
                'reset_expires' => $expires,
                'updated_at' => date('Y-m-d H:i:s')
            ]]
        );

        return $result->getModifiedCount() > 0 ? $token : false;
    }

    /**
     * Reset password with token
     */
    public function resetPassword($email, $token, $newPassword) {
        $user = $this->collection->findOne([
            'email' => $email,
            'reset_token' => $token,
            'reset_expires' => ['$gte' => date('Y-m-d H:i:s')]
        ]);

        if (!$user) {
            throw new Exception("Invalid or expired reset token");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $result = $this->collection->updateOne(
            ['email' => $email],
            ['$set' => [
                'password' => $hashedPassword,
                'reset_token' => null,
                'reset_expires' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]]
        );

        return $result->getModifiedCount() > 0;
    }
}
?>
