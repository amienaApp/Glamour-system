<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../config1/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'colors' => []];

try {
    // Get POST data (handle both JSON and form data)
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
        switch ($input['action']) {
            case 'get_colors':
                $category = $input['category'] ?? 'Beauty & Cosmetics';
                
                // Get all products from the category
                $products = $productModel->getByCategory($category);
                
                $colors = [];
                
                foreach ($products as $product) {
                    // Add main product color
                    if (!empty($product['color'])) {
                        $colors[] = $product['color'];
                    }
                    
                    // Add color variant colors
                    if (!empty($product['color_variants'])) {
                        foreach ($product['color_variants'] as $variant) {
                            if (!empty($variant['color'])) {
                                $colors[] = $variant['color'];
                            }
                        }
                    }
                }
                
                // Remove duplicates and sort
                $colors = array_unique($colors);
                sort($colors);
                
                $response = [
                    'success' => true,
                    'colors' => $colors,
                    'message' => count($colors) . ' unique colors found'
                ];
                break;
                
            default:
                $response['message'] = 'Invalid action';
                break;
        }
    } else {
        $response['message'] = 'Invalid request method or missing action';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
?>