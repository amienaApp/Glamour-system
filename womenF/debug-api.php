<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Product.php';

    $productModel = new Product();

    // Get product ID from request
    $productId = $_GET['id'] ?? null;

    if (!$productId) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID is required', 'debug' => 'No ID provided']);
        exit;
    }

    // Debug: Get all products to see what IDs are available
    $allProducts = $productModel->getAll();
    $availableIds = array_map(function($product) {
        return $product['_id'];
    }, $allProducts);

    // Get product data
    $product = $productModel->getById($productId);

    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Product not found', 
            'debug' => [
                'requested_id' => $productId,
                'available_ids' => array_slice($availableIds, 0, 5), // Show first 5 IDs
                'total_products' => count($allProducts)
            ]
        ]);
        exit;
    }

    // Format product data for quick view
    $formattedProduct = [
        'id' => $product['_id'],
        'name' => $product['name'],
        'price' => '$' . number_format($product['price'], 0),
        'description' => $product['description'] ?? 'A beautiful dress perfect for any occasion.',
        'available' => $product['available'] ?? true,
        'stock' => $product['stock'] ?? 0,
        'images' => [],
        'colors' => [],
        'sizes' => []
    ];

    // Add main product images
    $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
    $backImage = $product['back_image'] ?? $product['image_back'] ?? '';

    if ($frontImage) {
        $formattedProduct['images'][] = [
            'src' => '../' . $frontImage,
            'color' => $product['color'] ?? '#000000',
            'type' => 'front'
        ];
    }

    if ($backImage) {
        $formattedProduct['images'][] = [
            'src' => '../' . $backImage,
            'color' => $product['color'] ?? '#000000',
            'type' => 'back'
        ];
    }

    // Add main product color
    if (!empty($product['color'])) {
        $formattedProduct['colors'][] = [
            'name' => 'Main Color',
            'value' => $product['color'],
            'hex' => $product['color']
        ];
    }

    // Add color variants
    if (!empty($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            $formattedProduct['colors'][] = [
                'name' => $variant['name'] ?? 'Variant',
                'value' => $variant['color'],
                'hex' => $variant['color']
            ];
            
            // Add variant images
            if (!empty($variant['front_image'])) {
                $formattedProduct['images'][] = [
                    'src' => '../' . $variant['front_image'],
                    'color' => $variant['color'],
                    'type' => 'front',
                    'variant' => $variant['name']
                ];
            }
            
            if (!empty($variant['back_image'])) {
                $formattedProduct['images'][] = [
                    'src' => '../' . $variant['back_image'],
                    'color' => $variant['color'],
                    'type' => 'back',
                    'variant' => $variant['name']
                ];
            }
        }
    }

    // Add sizes if available
    if (!empty($product['selected_sizes'])) {
        $sizes = json_decode($product['selected_sizes'], true);
        if (is_array($sizes)) {
            $formattedProduct['sizes'] = $sizes;
        }
    } else {
        // Default sizes if none specified
        $formattedProduct['sizes'] = ['XS', 'S', 'M', 'L', 'XL'];
    }

    // Add variant sizes
    if (!empty($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (!empty($variant['selected_sizes'])) {
                $variantSizes = json_decode($variant['selected_sizes'], true);
                if (is_array($variantSizes)) {
                    $formattedProduct['variant_sizes'][$variant['color']] = $variantSizes;
                }
            }
        }
    }

    echo json_encode($formattedProduct);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error', 
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>
