<?php
// Suppress any PHP warnings/notices that might interfere with JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

try {
    $productId = $_GET['product_id'] ?? '';
    
    if (empty($productId)) {
        throw new Exception('Product ID is required');
    }
    
    $productModel = new Product();
    $product = $productModel->getById($productId);
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Format the product data for the quickview
    $formattedProduct = [
        'id' => $product['_id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'salePrice' => $product['sale_price'] ?? null,
        'description' => $product['description'] ?? '',
        'category' => $product['category'] ?? '',
        'subcategory' => $product['subcategory'] ?? '',
        'available' => $product['available'] ?? true,
        'stock' => $product['stock'] ?? 0,
        'rating' => $product['rating'] ?? 0,
        'reviewCount' => $product['review_count'] ?? 0,
        'images' => [],
        'colors' => [],
        'sizes' => []
    ];
    
    // Process images
    $images = [];
    
    // Main product images
    if (!empty($product['front_image'])) {
        $images[] = [
            'src' => '../' . $product['front_image'],
            'alt' => $product['name'] . ' - Front',
            'type' => 'front'
        ];
    }
    
    if (!empty($product['back_image']) && $product['back_image'] !== $product['front_image']) {
        $images[] = [
            'src' => '../' . $product['back_image'],
            'alt' => $product['name'] . ' - Back',
            'type' => 'back'
        ];
    }
    
    // Color variant images
    if (!empty($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (!empty($variant['front_image'])) {
                $images[] = [
                    'src' => '../' . $variant['front_image'],
                    'alt' => $product['name'] . ' - ' . ($variant['name'] ?? 'Variant') . ' - Front',
                    'type' => 'front',
                    'color' => $variant['color'] ?? ''
                ];
            }
            if (!empty($variant['back_image']) && $variant['back_image'] !== $variant['front_image']) {
                $images[] = [
                    'src' => '../' . $variant['back_image'],
                    'alt' => $product['name'] . ' - ' . ($variant['name'] ?? 'Variant') . ' - Back',
                    'type' => 'back',
                    'color' => $variant['color'] ?? ''
                ];
            }
        }
    }
    
    $formattedProduct['images'] = $images;
    
    // Process colors
    $colors = [];
    
    // Main product color
    if (!empty($product['color'])) {
        $colors[] = [
            'name' => $product['color'],
            'value' => $product['color'],
            'hex' => $product['color'],
            'images' => array_filter($images, function($img) use ($product) {
                return !isset($img['color']) || $img['color'] === $product['color'];
            })
        ];
    }
    
    // Color variants
    if (!empty($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (!empty($variant['color'])) {
                $colors[] = [
                    'name' => $variant['name'] ?? $variant['color'],
                    'value' => $variant['color'],
                    'hex' => $variant['color'],
                    'images' => array_filter($images, function($img) use ($variant) {
                        return isset($img['color']) && $img['color'] === $variant['color'];
                    })
                ];
            }
        }
    }
    
    $formattedProduct['colors'] = $colors;
    
    // Process sizes
    $sizes = [];
    
    if (!empty($product['sizes'])) {
        foreach ($product['sizes'] as $size) {
            $sizes[] = [
                'name' => $size,
                'available' => true,
                'stock' => $product['stock'] ?? 0
            ];
        }
    } elseif (!empty($product['selected_sizes'])) {
        foreach ($product['selected_sizes'] as $size) {
            $sizes[] = [
                'name' => $size,
                'available' => true,
                'stock' => $product['stock'] ?? 0
            ];
        }
    } else {
        // Default sizes for kids clothing
        $defaultSizes = ['2T', '3T', '4T', '5', '6', '7', '8', '10', '12', '14'];
        foreach ($defaultSizes as $size) {
            $sizes[] = [
                'name' => $size,
                'available' => true,
                'stock' => $product['stock'] ?? 0
            ];
        }
    }
    
    $formattedProduct['sizes'] = $sizes;
    
    // Set default selections
    $formattedProduct['defaultColor'] = $colors[0]['value'] ?? null;
    $formattedProduct['defaultSize'] = $sizes[0]['name'] ?? null;
    
    echo json_encode([
        'success' => true,
        'product' => $formattedProduct
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

