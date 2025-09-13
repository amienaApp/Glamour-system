<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = ['success' => false, 'message' => '', 'data' => [], 'debug' => []];

try {
    // Step 1: Check if required files exist
    $configPath = '../config/mongodb.php';
    $modelPath = '../models/Product.php';
    
    $response['debug']['files'] = [
        'config_exists' => file_exists($configPath),
        'model_exists' => file_exists($modelPath)
    ];
    
    if (!file_exists($configPath)) {
        throw new Exception('MongoDB config file not found');
    }
    
    if (!file_exists($modelPath)) {
        throw new Exception('Product model file not found');
    }
    
    // Step 2: Include files
    require_once $configPath;
    require_once $modelPath;
    
    $response['debug']['files_loaded'] = true;
    
    // Step 3: Check if classes exist
    $response['debug']['classes'] = [
        'MongoDB_exists' => class_exists('MongoDB'),
        'Product_exists' => class_exists('Product')
    ];
    
    if (!class_exists('MongoDB')) {
        throw new Exception('MongoDB class not found');
    }
    
    if (!class_exists('Product')) {
        throw new Exception('Product class not found');
    }
    
    // Step 4: Try to create Product model instance
    $productModel = new Product();
    $response['debug']['model_created'] = true;
    
    // Step 5: Try to get products
    $allProducts = $productModel->getByCategory("Men's Clothing");
    $response['debug']['products_count'] = count($allProducts);
    
    if (empty($allProducts)) {
        $response = [
            'success' => true,
            'message' => 'No products found in Men\'s Clothing category',
            'data' => [
                'categories' => [],
                'total_categories' => 0
            ],
            'debug' => $response['debug']
        ];
    } else {
        // Process categories
        $categories = [];
        $categoryCounts = [];
        
        foreach ($allProducts as $product) {
            if (!empty($product['subcategory'])) {
                $category = $product['subcategory'];
                $categoryKey = strtolower($category);
                
                if (!in_array($category, $categories)) {
                    $categories[] = $category;
                }
                
                if (!isset($categoryCounts[$categoryKey])) {
                    $categoryCounts[$categoryKey] = 0;
                }
                $categoryCounts[$categoryKey]++;
            }
        }
        
        sort($categories);
        
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryKey = strtolower($category);
            $value = strtolower(str_replace([' ', '-'], '', $category));
            
            if ($category === 'T-Shirts') {
                $value = 'tshirts';
            }
            
            $categoryData[] = [
                'name' => $category,
                'value' => $value,
                'count' => $categoryCounts[$categoryKey] ?? 0
            ];
        }
        
        $response = [
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => [
                'categories' => $categoryData,
                'total_categories' => count($categoryData)
            ],
            'debug' => $response['debug']
        ];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => $response['debug'] ?? []
    ];
}

// Send JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit();
?>
