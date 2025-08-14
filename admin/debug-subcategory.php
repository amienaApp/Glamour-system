<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

echo "<h2>Debug Subcategory Test</h2>";

// Test 1: Get all products
echo "<h3>1. All Products:</h3>";
$allProducts = $productModel->getAll();
echo "Total products: " . count($allProducts) . "<br>";

// Test 2: Get products by subcategory "Dresses"
echo "<h3>2. Products with subcategory 'Dresses':</h3>";
$dresses = $productModel->getBySubcategory('Dresses');
echo "Dresses found: " . count($dresses) . "<br>";

if (!empty($dresses)) {
    echo "<ul>";
    foreach ($dresses as $dress) {
        echo "<li>" . $dress['name'] . " - Subcategory: " . ($dress['subcategory'] ?? 'NULL') . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No dresses found!</p>";
}

// Test 3: Check all subcategories in database
echo "<h3>3. All subcategories in database:</h3>";
$subcategories = $productModel->getSubcategories();
echo "Subcategories: " . implode(', ', $subcategories) . "<br>";

// Test 4: Check first few products and their subcategories
echo "<h3>4. First 5 products and their subcategories:</h3>";
$firstProducts = array_slice($allProducts, 0, 5);
foreach ($firstProducts as $product) {
    echo "Product: " . $product['name'] . " - Subcategory: " . ($product['subcategory'] ?? 'NULL') . "<br>";
}

// Test 5: Check if there are any products with empty subcategory
echo "<h3>5. Products with empty subcategory:</h3>";
$emptySubcategory = $productModel->getAll(['subcategory' => '']);
echo "Products with empty subcategory: " . count($emptySubcategory) . "<br>";

if (!empty($emptySubcategory)) {
    foreach ($emptySubcategory as $product) {
        echo "- " . $product['name'] . "<br>";
    }
}
?>



