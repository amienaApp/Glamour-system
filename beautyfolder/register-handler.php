<?php
// Beauty & Cosmetics Registration Handler
// Handles user registration for beauty section

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
        
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        
        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
        } elseif ($password !== $confirmPassword) {
            $response = [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
        } elseif (strlen($password) < 6) {
            $response = [
                'success' => false,
                'message' => 'Password must be at least 6 characters long'
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        } else {
            $userModel = new User();
            
            // Check if user already exists
            $existingUser = $userModel->getByEmail($email);
            if ($existingUser) {
                $response = [
                    'success' => false,
                    'message' => 'User with this email already exists'
                ];
            } else {
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
                    
                    $response = [
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
                    $response = [
                        'success' => false,
                        'message' => 'Failed to create user account'
                    ];
                }
            }
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
        'message' => 'Registration error: ' . $e->getMessage()
    ];
}

echo json_encode($response);
?>




