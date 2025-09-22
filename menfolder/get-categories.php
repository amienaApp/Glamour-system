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
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all men's clothing products to extract categories
        $allProducts = $productModel->getByCategory("Men's Clothing");
        
        $categories = [];
        $categoryCounts = [];
        
        foreach ($allProducts as $product) {
            // Extract subcategory (which represents the category in this context)
            if (!empty($product['subcategory'])) {
                $category = $product['subcategory'];
                
                // Convert to lowercase for consistent comparison
                $categoryKey = strtolower($category);
                
                if (!in_array($category, $categories)) {
                    $categories[] = $category;
                }
                
                // Count products in each category
                if (!isset($categoryCounts[$categoryKey])) {
                    $categoryCounts[$categoryKey] = 0;
                }
                $categoryCounts[$categoryKey]++;
            }
        }
        
        // Sort categories alphabetically
        sort($categories);
        
        // Prepare category data with counts
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryKey = strtolower($category);
            
            // Create URL-friendly value that matches the filter API mapping
            $value = strtolower(str_replace([' ', '-'], '', $category));
            
            // Ensure the value matches the expected format in filter API
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
            ]
        ];
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Send JSON response
echo json_encode($response);
exit();
?>
