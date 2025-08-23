<?php
/**
 * API endpoint to get product details for quick view
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/mongodb.php';
require_once 'models/Product.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Method not allowed');
    }

    $productId = $_GET['id'] ?? null;
    
    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    $productModel = new Product();
    $product = $productModel->getById($productId);

    if (!$product) {
        throw new Exception('Product not found');
    }

    // Format the response
    $response = [
        'success' => true,
        'product' => [
            'id' => $product['_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'salePrice' => $product['salePrice'] ?? null,
            'sale' => $product['sale'] ?? false,
            'description' => $product['description'] ?? '',
            'images' => $product['images'] ?? [],
            'colors' => $product['colors'] ?? ['#000000'],
            'sizes' => $product['sizes'] ?? ['S', 'M', 'L'],
            'category' => $product['category'] ?? '',
            'subcategory' => $product['subcategory'] ?? '',
            'stock' => $product['stock'] ?? 0,
            'featured' => $product['featured'] ?? false
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

