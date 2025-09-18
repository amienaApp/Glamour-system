<?php
/**
 * Perfumes Login Handler
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
    require_once __DIR__ . '/../config1/mongodb.php';
    require_once __DIR__ . '/../models/User.php';

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }

    // Validate required fields
    if (empty(trim($input['username'])) || empty(trim($input['password']))) {
        throw new Exception("Username/Email and password are required");
    }

    // Sanitize input
    $username = trim($input['username']);
    $password = trim($input['password']);

    // Create User model instance
    $userModel = new User();

    // Attempt login
    $user = $userModel->login($username, $password);

    // Update last login time
    $userModel->updateLastLogin($user['_id']);

    // Start session and store user data
    session_start();
    $_SESSION['user_id'] = $user['_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();

    // Transfer session cart to user account if it exists
    $sessionCartId = 'session_' . session_id();
    try {
        // Only attempt cart transfer if Cart model exists and can be loaded
        if (file_exists(__DIR__ . '/../models/Cart.php')) {
            require_once __DIR__ . '/../models/Cart.php';
            
            // Check if Cart class exists and can be instantiated
            if (class_exists('Cart')) {
                $cartModel = new Cart();
                
                // Use the new public transfer method
                if ($cartModel->transferCart($sessionCartId, $user['_id'])) {
                }
            }
        }
    } catch (Exception $e) {
        // Cart transfer failed, but login should still succeed
    } catch (Error $e) {
        // Cart transfer failed, but login should still succeed
    }

    // Check if there's a redirect parameter in the request
    $redirectUrl = 'index.php'; // Default redirect
    
    // Check POST data for redirect parameter
    if (isset($input['redirect']) && $input['redirect'] === 'cart') {
        $redirectUrl = '/Glamour-system/cart-unified.php?mode=view';
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Welcome back, ' . $user['username'] . '!',
        'user' => $user,
        'redirect' => $redirectUrl
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
