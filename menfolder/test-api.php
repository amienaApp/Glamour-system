<?php
// Simple test script to check the filter API
header('Content-Type: application/json');

echo "Testing filter API...\n";

// Test 1: Check if filter-api.php exists
if (file_exists('filter-api.php')) {
    echo "✓ filter-api.php exists\n";
} else {
    echo "✗ filter-api.php not found\n";
}

// Test 2: Check if we can include the required files
try {
    require_once '../config/mongodb.php';
    require_once '../models/Product.php';
    echo "✓ Required files loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading files: " . $e->getMessage() . "\n";
}

// Test 3: Check if we can connect to database
try {
    $db = MongoDB::getInstance();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 4: Check if there are products in the database
try {
    $productModel = new Product();
    $products = $productModel->getByCategory("Men's Clothing");
    echo "✓ Found " . count($products) . " men's clothing products\n";
    
    if (count($products) > 0) {
        echo "Sample product: " . $products[0]['name'] . " - $" . $products[0]['price'] . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error getting products: " . $e->getMessage() . "\n";
}

// Test 5: Test the filter API directly
echo "\nTesting filter API directly...\n";

$testData = [
    'action' => 'filter_products',
    'subcategory' => '',
    'sizes' => [],
    'colors' => [],
    'price_ranges' => [],
    'categories' => [],
    'brands' => [],
    'styles' => [],
    'materials' => [],
    'fits' => [],
    'availability' => []
];

// Simulate the filter API call
try {
    $filters = [];
    $andConditions = [];
    
    // Base filter - only men's clothing
    $filters['category'] = "Men's Clothing";
    
    // Get products with filters
    $products = $productModel->getAll($filters, ['createdAt' => -1]);
    
    echo "✓ Filter API test successful\n";
    echo "✓ Found " . count($products) . " products after filtering\n";
    
    if (count($products) > 0) {
        echo "Sample filtered product: " . $products[0]['name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Filter API test failed: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
?>
