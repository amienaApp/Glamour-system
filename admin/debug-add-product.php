<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

echo "<h1>Debug Add Product Test</h1>";

$productModel = new Product();

// Test 1: Check current products
echo "<h2>1. Current Products Count:</h2>";
$currentProducts = $productModel->getAll();
echo "Total products: " . count($currentProducts) . "<br>";

// Test 2: Try to create a test product
echo "<h2>2. Testing Product Creation:</h2>";

$testProductData = [
    'name' => 'Test Product ' . date('Y-m-d H:i:s'),
    'price' => 99.99,
    'color' => '#FF0000',
    'category' => "Women's Clothing",
    'subcategory' => 'Dresses',
    'description' => 'Test product for debugging',
    'featured' => false,
    'sale' => false,
    'available' => true,
    'stock' => 10,
    'size_category' => '',
    'selected_sizes' => ''
];

echo "Test product data:<br>";
echo "<pre>" . print_r($testProductData, true) . "</pre>";

// Test 3: Validate the product data
echo "<h2>3. Validation Test:</h2>";
$validationErrors = $productModel->validateProductData($testProductData);
if (empty($validationErrors)) {
    echo "✅ Validation passed - No errors<br>";
} else {
    echo "❌ Validation failed:<br>";
    foreach ($validationErrors as $error) {
        echo "- " . $error . "<br>";
    }
}

// Test 4: Try to create the product
echo "<h2>4. Product Creation Test:</h2>";
if (empty($validationErrors)) {
    try {
        $newProductId = $productModel->create($testProductData);
        if ($newProductId) {
            echo "✅ Product created successfully!<br>";
            echo "New product ID: " . $newProductId . "<br>";
            
            // Test 5: Verify the product was added
            echo "<h2>5. Verification Test:</h2>";
            $updatedProducts = $productModel->getAll();
            echo "Updated total products: " . count($updatedProducts) . "<br>";
            
            if (count($updatedProducts) > count($currentProducts)) {
                echo "✅ Product count increased - Product was added successfully!<br>";
                
                // Show the latest product
                $latestProduct = end($updatedProducts);
                echo "<h3>Latest Product Details:</h3>";
                echo "<pre>" . print_r($latestProduct, true) . "</pre>";
            } else {
                echo "❌ Product count did not increase - Something went wrong<br>";
            }
        } else {
            echo "❌ Product creation failed - No ID returned<br>";
        }
    } catch (Exception $e) {
        echo "❌ Exception occurred: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Skipping product creation due to validation errors<br>";
}

// Test 6: Check database file
echo "<h2>6. Database File Check:</h2>";
$dbFile = __DIR__ . '/../data/collections/products.json';
if (file_exists($dbFile)) {
    echo "✅ Database file exists<br>";
    echo "File size: " . filesize($dbFile) . " bytes<br>";
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($dbFile)) . "<br>";
} else {
    echo "❌ Database file does not exist<br>";
}
?>



