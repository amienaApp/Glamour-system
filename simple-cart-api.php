<?php
// Simple cart API for testing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
        switch ($input['action']) {
            case 'get_cart_count':
                $response = [
                    'success' => true,
                    'cart_count' => 0
                ];
                break;
                
            case 'add_to_cart':
                $response = [
                    'success' => true,
                    'message' => 'Product added to cart successfully!',
                    'cart_count' => 1
                ];
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
            'message' => 'Invalid request method or missing action'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
