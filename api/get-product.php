<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Add cache-busting headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

try {
    // Get product ID from query parameter
    $productId = $_GET['id'] ?? '';
    
    if (empty($productId)) {
        throw new Exception('Product ID is required');
    }
    
    // Load MongoDB connection and Product model
    require_once '../config1/mongodb.php';
    require_once '../models/Product.php';
    
    $productModel = new Product();
    $product = $productModel->getById($productId);
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Return product data
    echo json_encode([
        'success' => true,
        'product' => [
            'id' => (string)$product['_id'],
            'name' => $product['name'] ?? '',
            'stock' => (int)($product['stock'] ?? 0),
            'available' => (function() use ($product) {
                $available = $product['available'] ?? true;
                return ($available === true || $available === 'true' || $available === 1 || $available === '1');
            })(),
            'price' => (float)($product['price'] ?? 0),
            'category' => $product['category'] ?? '',
            'subcategory' => $product['subcategory'] ?? '',
            'featured' => (bool)($product['featured'] ?? false),
            'sale' => (bool)($product['sale'] ?? false),
            'salePrice' => isset($product['salePrice']) ? (float)$product['salePrice'] : null
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Add cache-busting headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

try {
    // Get product ID from query parameter
    $productId = $_GET['id'] ?? '';
    
    if (empty($productId)) {
        throw new Exception('Product ID is required');
    }
    
    // Load MongoDB connection and Product model
    require_once '../config1/mongodb.php';
    require_once '../models/Product.php';
    
    $productModel = new Product();
    $product = $productModel->getById($productId);
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Return product data
    echo json_encode([
        'success' => true,
        'product' => [
            'id' => (string)$product['_id'],
            'name' => $product['name'] ?? '',
            'stock' => (int)($product['stock'] ?? 0),
            'available' => (function() use ($product) {
                $available = $product['available'] ?? true;
                return ($available === true || $available === 'true' || $available === 1 || $available === '1');
            })(),
            'price' => (float)($product['price'] ?? 0),
            'category' => $product['category'] ?? '',
            'subcategory' => $product['subcategory'] ?? '',
            'featured' => (bool)($product['featured'] ?? false),
            'sale' => (bool)($product['sale'] ?? false),
            'salePrice' => isset($product['salePrice']) ? (float)$product['salePrice'] : null
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>



