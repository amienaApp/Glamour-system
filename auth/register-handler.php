<?php
/**
 * Centralized User Registration Handler
 * Handles user registration for the entire Glamour Palace system
 * 
 * @author Glamour Palace Team
 * @version 1.0
 * @since 2024
 */

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

try {
    // Include required files
    require_once __DIR__ . '/../config1/mongodb.php';
    require_once __DIR__ . '/../models/User.php';

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }

    // Validate required fields
    $requiredFields = ['username', 'email', 'contact_number', 'gender', 'region', 'city', 'password', 'confirm_password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Validate password match
    if ($input['password'] !== $input['confirm_password']) {
        throw new Exception("Passwords do not match");
    }

    // Validate password strength
    if (strlen($input['password']) < 6) {
        throw new Exception("Password must be at least 6 characters long");
    }
    
    // Additional password validation
    if (strlen($input['password']) > 128) {
        throw new Exception("Password must be less than 128 characters");
    }
    
    // Check for common weak passwords
    $weakPasswords = ['password', '123456', '123456789', 'qwerty', 'abc123', 'password123', 'admin', 'letmein'];
    if (in_array(strtolower($input['password']), $weakPasswords)) {
        throw new Exception("Password is too common. Please choose a stronger password");
    }

    // Validate gender
    if (!in_array($input['gender'], ['male', 'female'])) {
        throw new Exception("Invalid gender selection");
    }

    // Validate region (Somalia regions)
    $validRegions = [
        'banadir', 'bari', 'bay', 'galguduud', 'gedo', 'hiran', 
        'jubbada-dhexe', 'jubbada-hoose', 'mudug', 'nugaal', 
        'sanaag', 'shabeellaha-dhexe', 'shabeellaha-hoose', 
        'sool', 'togdheer', 'woqooyi-galbeed'
    ];
    
    if (!in_array($input['region'], $validRegions)) {
        throw new Exception("Invalid region selection");
    }

    // Create User model instance
    $userModel = new User();

    // Prepare user data
    $userData = [
        'username' => trim($input['username']),
        'email' => trim($input['email']),
        'contact_number' => trim($input['contact_number']),
        'gender' => $input['gender'],
        'region' => $input['region'],
        'city' => trim($input['city']),
        'password' => $input['password']
    ];

    // Register user
    $registeredUser = $userModel->register($userData);

    // Automatically log in the user after successful registration
    session_start();
    $_SESSION['user_id'] = $registeredUser['_id'];
    $_SESSION['username'] = $registeredUser['username'];
    $_SESSION['email'] = $registeredUser['email'];
    $_SESSION['user_role'] = $registeredUser['role'];

    // Update last login time
    $userModel->updateLastLogin($registeredUser['_id']);

    // Return success response with auto-login
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You are now signed in.',
        'user' => [
            'id' => $registeredUser['_id'],
            'username' => $registeredUser['username'],
            'email' => $registeredUser['email'],
            'role' => $registeredUser['role']
        ],
        'auto_login' => true
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}
?>


 * Centralized User Registration Handler
 * Handles user registration for the entire Glamour Palace system
 * 
 * @author Glamour Palace Team
 * @version 1.0
 * @since 2024
 */

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

try {
    // Include required files
    require_once __DIR__ . '/../config1/mongodb.php';
    require_once __DIR__ . '/../models/User.php';

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }

    // Validate required fields
    $requiredFields = ['username', 'email', 'contact_number', 'gender', 'region', 'city', 'password', 'confirm_password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Validate password match
    if ($input['password'] !== $input['confirm_password']) {
        throw new Exception("Passwords do not match");
    }

    // Validate password strength
    if (strlen($input['password']) < 6) {
        throw new Exception("Password must be at least 6 characters long");
    }
    
    // Additional password validation
    if (strlen($input['password']) > 128) {
        throw new Exception("Password must be less than 128 characters");
    }
    
    // Check for common weak passwords
    $weakPasswords = ['password', '123456', '123456789', 'qwerty', 'abc123', 'password123', 'admin', 'letmein'];
    if (in_array(strtolower($input['password']), $weakPasswords)) {
        throw new Exception("Password is too common. Please choose a stronger password");
    }

    // Validate gender
    if (!in_array($input['gender'], ['male', 'female'])) {
        throw new Exception("Invalid gender selection");
    }

    // Validate region (Somalia regions)
    $validRegions = [
        'banadir', 'bari', 'bay', 'galguduud', 'gedo', 'hiran', 
        'jubbada-dhexe', 'jubbada-hoose', 'mudug', 'nugaal', 
        'sanaag', 'shabeellaha-dhexe', 'shabeellaha-hoose', 
        'sool', 'togdheer', 'woqooyi-galbeed'
    ];
    
    if (!in_array($input['region'], $validRegions)) {
        throw new Exception("Invalid region selection");
    }

    // Create User model instance
    $userModel = new User();

    // Prepare user data
    $userData = [
        'username' => trim($input['username']),
        'email' => trim($input['email']),
        'contact_number' => trim($input['contact_number']),
        'gender' => $input['gender'],
        'region' => $input['region'],
        'city' => trim($input['city']),
        'password' => $input['password']
    ];

    // Register user
    $registeredUser = $userModel->register($userData);

    // Automatically log in the user after successful registration
    session_start();
    $_SESSION['user_id'] = $registeredUser['_id'];
    $_SESSION['username'] = $registeredUser['username'];
    $_SESSION['email'] = $registeredUser['email'];
    $_SESSION['user_role'] = $registeredUser['role'];

    // Update last login time
    $userModel->updateLastLogin($registeredUser['_id']);

    // Return success response with auto-login
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You are now signed in.',
        'user' => [
            'id' => $registeredUser['_id'],
            'username' => $registeredUser['username'],
            'email' => $registeredUser['email'],
            'role' => $registeredUser['role']
        ],
        'auto_login' => true
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}
?>




