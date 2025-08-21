<?php
/**
 * User Login Handler
 * Processes user login form submissions
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
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/User.php';

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }

    // Validate required fields
    if (empty($input['username']) || empty($input['password'])) {
        throw new Exception("Username/Email and password are required");
    }

    // Create User model instance
    $userModel = new User();

    // Attempt login
    $user = $userModel->login($input['username'], $input['password']);

    // Update last login time
    $userModel->updateLastLogin($user['_id']);

    // Start session and store user data
    session_start();
    $_SESSION['user_id'] = $user['_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Welcome back, ' . $user['username'] . '!',
        'user' => $user
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
