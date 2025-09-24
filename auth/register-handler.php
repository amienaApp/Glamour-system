<?php
/**
 * Register Handler
 * Handles user registration
 */

session_start();
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// Validate required fields
$requiredFields = ['username', 'email', 'contact_number', 'gender', 'region', 'city', 'password', 'confirm_password'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit();
    }
}

// Validate password confirmation
if ($input['password'] !== $input['confirm_password']) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

// Validate password strength
if (strlen($input['password']) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit();
}

// Validate username (only letters and spaces)
if (!preg_match('/^[a-zA-Z\s]+$/', $input['username'])) {
    echo json_encode(['success' => false, 'message' => 'Username must contain only letters and spaces']);
    exit();
}

// Validate email
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

try {
    // Include required files
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config1/mongodb.php';
    require_once __DIR__ . '/../models/User.php';
    
    // Initialize user model
    $userModel = new User();
    
    // Prepare user data
    $userData = [
        'username' => trim($input['username']),
        'email' => trim($input['email']),
        'contact_number' => $input['contact_number'],
        'gender' => $input['gender'],
        'region' => $input['region'],
        'city' => $input['city'],
        'password' => $input['password']
    ];
    
    // Register user
    $user = $userModel->register($userData);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! You can now sign in.',
            'user' => [
                'id' => $user['_id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed: ' . $e->getMessage()
    ]);
}
?>