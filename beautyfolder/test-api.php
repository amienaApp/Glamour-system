<?php
// Simple test for the filter API
header('Content-Type: application/json');

try {
    require_once '../config1/mongodb.php';
    require_once '../models/Product.php';
    
    $productModel = new Product();
    
    // Test getting all beauty products
    $allProducts = $productModel->getByCategory("Beauty & Cosmetics");
    
    echo json_encode([
        'success' => true,
        'total_products' => count($allProducts),
        'sample_products' => array_slice($allProducts, 0, 3),
        'message' => 'API is working'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>



