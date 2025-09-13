<?php
// Beauty & Cosmetics Login Handler
// Handles user login for beauty section

session_start();

// Include required files
require_once '../config1/mongodb.php';
require_once '../models/User.php';

// Set content type
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'login':
                $response = handleLogin($input);
                break;
            case 'register':
                $response = handleRegister($input);
                break;
            case 'logout':
                $response = handleLogout();
                break;
            case 'check_auth':
                $response = checkAuth();
                break;
            default:
                $response = [
                    'success' => false,
                    'message' => 'Invalid action'
                ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid request method'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ];
}

echo json_encode($response);

// Function to handle login
function handleLogin($input) {
    try {
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }
        
        $userModel = new User();
        $user = $userModel->getByEmail($email);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Set session variables
        $_SESSION['user_id'] = (string)$user['_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['logged_in'] = true;
        
        // Update last login
        $userModel->updateLastLogin((string)$user['_id']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => (string)$user['_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'user'
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Login error: ' . $e->getMessage()
        ];
    }
}

// Function to handle registration
function handleRegister($input) {
    try {
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
        }
        
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters long'
            ];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }
        
        $userModel = new User();
        
        // Check if user already exists
        $existingUser = $userModel->getByEmail($email);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'User with this email already exists'
            ];
        }
        
        // Create new user
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'createdAt' => new MongoDB\BSON\UTCDateTime(),
            'lastLogin' => null,
            'isActive' => true
        ];
        
        $userId = $userModel->create($userData);
        
        if ($userId) {
            // Auto-login after registration
            $_SESSION['user_id'] = (string)$userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = 'user';
            $_SESSION['logged_in'] = true;
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => (string)$userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => 'user'
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create user account'
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Registration error: ' . $e->getMessage()
        ];
    }
}

// Function to handle logout
function handleLogout() {
    try {
        // Clear session
        session_unset();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Logout error: ' . $e->getMessage()
        ];
    }
}

// Function to check authentication status
function checkAuth() {
    try {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return [
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user_id'] ?? '',
                    'name' => $_SESSION['user_name'] ?? '',
                    'email' => $_SESSION['user_email'] ?? '',
                    'role' => $_SESSION['user_role'] ?? 'user'
                ]
            ];
        } else {
            return [
                'success' => true,
                'authenticated' => false,
                'user' => null
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Auth check error: ' . $e->getMessage()
        ];
    }
}
?>




