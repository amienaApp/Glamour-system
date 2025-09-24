<?php
/**
 * User Registration Handler
 * Processes user registration form submissions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
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

    // Validate password (allow any password)
    if (strlen($input['password']) < 1) {
        throw new Exception("Password is required");
    }

    // Validate username - only alphabetic characters and spaces (for names)
    if (!preg_match('/^[a-zA-Z\s]+$/', $input['username'])) {
        throw new Exception("Username must contain only letters and spaces (for names)");
    }
    
    // Validate username length
    if (strlen(trim($input['username'])) < 2) {
        throw new Exception("Username must be at least 2 characters long");
    }
    
    if (strlen(trim($input['username'])) > 50) {
        throw new Exception("Username must be less than 50 characters long");
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

    // Return success response (don't auto-login)
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You can now sign in.',
        'user' => $registeredUser
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

