
<?php
/**
 * Authentication Check API
 * Returns the current authentication status of the user
 */

session_start();

// Set JSON header
header('Content-Type: application/json');

try {
    // Check if user is logged in
    $isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    
    $response = [
        'success' => true,
        'authenticated' => $isAuthenticated,
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'authenticated' => false,
        'error' => 'Authentication check failed: ' . $e->getMessage()
    ]);
}
?>
