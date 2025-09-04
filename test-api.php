<?php
/**
 * Test file to check if the get-product-details.php API is working
 */

echo "<h1>API Test</h1>";

// Test 1: Check if MongoDB connection works
echo "<h2>Test 1: MongoDB Connection</h2>";
try {
    require_once 'config1/mongodb.php';
    echo "✅ MongoDB connection successful<br>";
} catch (Exception $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "<br>";
}

// Test 2: Check if Product model works
echo "<h2>Test 2: Product Model</h2>";
try {
    require_once 'models/Product.php';
    $productModel = new Product();
    echo "✅ Product model loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Product model failed: " . $e->getMessage() . "<br>";
}

// Test 3: Try to get a product
echo "<h2>Test 3: Get Product</h2>";
try {
    if (isset($productModel)) {
        // Try to get the first product from the database
        $products = $productModel->getAll([], [], 1);
        if (!empty($products)) {
            $firstProduct = $products[0];
            echo "✅ Found product: " . ($firstProduct['name'] ?? 'Unknown') . "<br>";
            echo "Product ID: " . $firstProduct['_id'] . "<br>";
            
            // Test the API endpoint
            echo "<h2>Test 4: API Endpoint Test</h2>";
            $testUrl = "get-product-details.php?product_id=" . $firstProduct['_id'];
            echo "Testing URL: <a href='$testUrl' target='_blank'>$testUrl</a><br>";
            
        } else {
            echo "❌ No products found in database<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error getting product: " . $e->getMessage() . "<br>";
}

// Test 5: Check file paths
echo "<h2>Test 5: File Paths</h2>";
$files = [
    'config1/mongodb.php',
    'models/Product.php',
    'get-product-details.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>Test Complete</h2>";
echo "<p>If you see any ❌ errors above, those need to be fixed before the quickview will work.</p>";
?>
