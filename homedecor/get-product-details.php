<?php
header('Content-Type: application/json');
require_once '../config/mongodb.php';
require_once '../models/Product.php';

if (!isset($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$productId = $_GET['product_id'];
$productModel = new Product();

try {
    $product = $productModel->getById($productId);
    
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
    
    // Prepare the response data
    $response = [
        'id' => $product['_id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'description' => $product['description'] ?? '',
        'category' => $product['category'],
        'subcategory' => $product['subcategory'] ?? '',
        'available' => $product['available'] ?? true,
        'stock' => $product['stock'] ?? 0,
        'featured' => $product['featured'] ?? false,
        'images' => [],
        'color_variants' => $product['color_variants'] ?? [],
        'color' => $product['color'] ?? '',
        
        // Home & Living specific fields
        'length' => $product['length'] ?? null,
        'width' => $product['width'] ?? null,
        'material' => $product['material'] ?? '',
        'bedding_size' => $product['bedding_size'] ?? '',
        'chair_count' => $product['chair_count'] ?? null,
        'table_length' => $product['table_length'] ?? null,
        'table_width' => $product['table_width'] ?? null,
        'sofa_count' => $product['sofa_count'] ?? null
    ];
    
    // Handle main product images
    if (!empty($product['front_image'])) {
        $response['images'][] = [
            'src' => '../' . $product['front_image'],
            'type' => pathinfo($product['front_image'], PATHINFO_EXTENSION) === 'mp4' ? 'video' : 'image',
            'alt' => $product['name'] . ' - Front'
        ];
    }
    
    if (!empty($product['back_image'])) {
        $response['images'][] = [
            'src' => '../' . $product['back_image'],
            'type' => pathinfo($product['back_image'], PATHINFO_EXTENSION) === 'mp4' ? 'video' : 'image',
            'alt' => $product['name'] . ' - Back'
        ];
    }
    
    // Handle color variant images
    if (!empty($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (!empty($variant['front_image'])) {
                $response['images'][] = [
                    'src' => '../' . $variant['front_image'],
                    'type' => pathinfo($variant['front_image'], PATHINFO_EXTENSION) === 'mp4' ? 'video' : 'image',
                    'alt' => $product['name'] . ' - ' . $variant['name'] . ' - Front',
                    'color' => $variant['color'] ?? ''
                ];
            }
            
            if (!empty($variant['back_image'])) {
                $response['images'][] = [
                    'src' => '../' . $variant['back_image'],
                    'type' => pathinfo($variant['back_image'], PATHINFO_EXTENSION) === 'mp4' ? 'video' : 'image',
                    'alt' => $product['name'] . ' - ' . $variant['name'] . ' - Back',
                    'color' => $variant['color'] ?? ''
                ];
            }
        }
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>

