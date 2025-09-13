<?php
// Debug script to check what categories are actually in the products
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

echo "<h2>Debug: Product Categories in Database</h2>";

// Get all home decor products
$allHomeDecorProducts = [];

// Try different category names
$categories = ["Home & Living", "Home Decor", "Home and Living", "Home"];
foreach ($categories as $category) {
    $products = $productModel->getByCategory($category);
    if (!empty($products)) {
        $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        echo "<p><strong>Found {$category}:</strong> " . count($products) . " products</p>";
    }
}

// Also get from subcategories
$subcategories = ['Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture', 'living room', 'dinning room', 'artwork', 'lightinning'];
foreach ($subcategories as $subcat) {
    $products = $productModel->getBySubcategory($subcat);
    if (!empty($products)) {
        $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        echo "<p><strong>Found subcategory {$subcat}:</strong> " . count($products) . " products</p>";
    }
}

// Remove duplicates
$uniqueProducts = [];
$seenIds = [];
foreach ($allHomeDecorProducts as $product) {
    $productId = (string)$product['_id'];
    if (!in_array($productId, $seenIds)) {
        $uniqueProducts[] = $product;
        $seenIds[] = $productId;
    }
}

echo "<h3>Total Unique Products: " . count($uniqueProducts) . "</h3>";

// Check what categories are actually in the products
$categoryCounts = [];
$subcategoryCounts = [];
$materialCounts = [];

foreach ($uniqueProducts as $product) {
    $category = $product['category'] ?? 'Unknown';
    $subcategory = $product['subcategory'] ?? 'Unknown';
    $material = $product['material'] ?? 'No Material';
    
    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
    $subcategoryCounts[$subcategory] = ($subcategoryCounts[$subcategory] ?? 0) + 1;
    $materialCounts[$material] = ($materialCounts[$material] ?? 0) + 1;
}

echo "<h3>Categories Found:</h3>";
foreach ($categoryCounts as $cat => $count) {
    echo "<p><strong>{$cat}:</strong> {$count} products</p>";
}

echo "<h3>Subcategories Found:</h3>";
foreach ($subcategoryCounts as $subcat => $count) {
    echo "<p><strong>{$subcat}:</strong> {$count} products</p>";
}

echo "<h3>Materials Found:</h3>";
foreach ($materialCounts as $material => $count) {
    echo "<p><strong>\"{$material}\":</strong> {$count} products</p>";
}

echo "<h3>Sample Product Data:</h3>";
if (!empty($uniqueProducts)) {
    $sample = $uniqueProducts[0];
    echo "<pre>";
    echo "ID: " . ($sample['_id'] ?? 'N/A') . "\n";
    echo "Category: " . ($sample['category'] ?? 'N/A') . "\n";
    echo "Subcategory: " . ($sample['subcategory'] ?? 'N/A') . "\n";
    echo "Name: " . ($sample['name'] ?? 'N/A') . "\n";
    echo "Price: " . ($sample['price'] ?? 'N/A') . "\n";
    echo "Color: " . ($sample['color'] ?? 'N/A') . "\n";
    echo "Material: " . ($sample['material'] ?? 'N/A') . "\n";
    echo "</pre>";
}

echo "<h3>Sidebar Category Values vs Database Subcategories:</h3>";
echo "<p><strong>Sidebar values:</strong></p>";
echo "<ul>";
echo "<li>Bedding</li>";
echo "<li>living room</li>";
echo "<li>Kitchen</li>";
echo "<li>artwork</li>";
echo "<li>dinning room</li>";
echo "<li>lightinning</li>";
echo "</ul>";

echo "<p><strong>Database subcategories:</strong></p>";
echo "<ul>";
foreach (array_keys($subcategoryCounts) as $subcat) {
    echo "<li>{$subcat}</li>";
}
echo "</ul>";
?>
