<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

echo "<h1>Subcategory Test</h1>";

$productModel = new Product();

// Test 1: Get all products
$allProducts = $productModel->getAll();
echo "<h2>Total Products: " . count($allProducts) . "</h2>";

// Test 2: Check first product's subcategory
if (!empty($allProducts)) {
    $firstProduct = $allProducts[0];
    echo "<h3>First Product:</h3>";
    echo "Name: " . $firstProduct['name'] . "<br>";
    echo "Category: " . ($firstProduct['category'] ?? 'NULL') . "<br>";
    echo "Subcategory: " . ($firstProduct['subcategory'] ?? 'NULL') . "<br>";
    echo "Subcategory type: " . gettype($firstProduct['subcategory'] ?? 'NULL') . "<br>";
}

// Test 3: Try to get dresses
echo "<h3>Getting Dresses:</h3>";
$dresses = $productModel->getBySubcategory('Dresses');
echo "Dresses found: " . count($dresses) . "<br>";

// Test 4: Show all subcategories
echo "<h3>All Subcategories:</h3>";
$subcategories = $productModel->getSubcategories();
echo "Subcategories: ";
foreach ($subcategories as $sub) {
    echo "'" . $sub . "' ";
}
echo "<br>";

// Test 5: Manual filter test
echo "<h3>Manual Filter Test:</h3>";
$manualFilter = $productModel->getAll(['subcategory' => 'Dresses']);
echo "Manual filter dresses: " . count($manualFilter) . "<br>";

// Test 6: Show some products with their subcategories
echo "<h3>First 3 Products with Subcategories:</h3>";
for ($i = 0; $i < min(3, count($allProducts)); $i++) {
    $product = $allProducts[$i];
    echo ($i + 1) . ". " . $product['name'] . " - Subcategory: '" . ($product['subcategory'] ?? 'NULL') . "'<br>";
}
?>



