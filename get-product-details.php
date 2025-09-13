<?php
// Suppress warnings and errors to ensure clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config1/mongodb.php';
require_once 'models/Product.php';
require_once 'includes/product-functions.php';

try {
    $productModel = new Product();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $productId = $_GET['product_id'] ?? null;
        
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $product = $productModel->getById($productId);
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Format product data for quick view
        $quickViewData = formatProductForQuickView($product);
        
        echo json_encode([
            'success' => true,
            'product' => $quickViewData
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
