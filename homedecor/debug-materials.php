<?php
// Debug script to check what materials are actually in the products
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

echo "<h2>Debug: Product Materials in Database</h2>";

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

// Remove duplicates based on product ID
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

// Check what materials are actually in the products
$materialCounts = [];

foreach ($uniqueProducts as $product) {
    $material = $product['material'] ?? '';
    
    if (empty($material) || trim($material) === '') {
        $material = 'No Material';
    }
    
    $material = trim($material);
    $materialCounts[$material] = ($materialCounts[$material] ?? 0) + 1;
}

echo "<h3>Materials Found in Database:</h3>";
foreach ($materialCounts as $material => $count) {
    echo "<p><strong>\"{$material}\":</strong> {$count} products</p>";
}

echo "<h3>Predefined Materials vs Database Materials:</h3>";
echo "<p><strong>Predefined materials:</strong></p>";
echo "<ul>";
$predefinedMaterials = ['Wood', 'Metal', 'Fabric', 'Glass', 'Ceramic', 'Plastic'];
foreach ($predefinedMaterials as $material) {
    $count = $materialCounts[$material] ?? 0;
    echo "<li><strong>{$material}</strong> - {$count} products in database</li>";
}
echo "</ul>";

echo "<p><strong>Database materials not in predefined list:</strong></p>";
echo "<ul>";
foreach ($materialCounts as $material => $count) {
    if (!in_array($material, $predefinedMaterials) && $material !== 'No Material') {
        echo "<li><strong>\"{$material}\"</strong> - {$count} products</li>";
    }
}
echo "</ul>";

echo "<h3>Sample Product Data with Materials:</h3>";
$sampleCount = 0;
foreach ($uniqueProducts as $product) {
    if ($sampleCount >= 5) break;
    
    $material = $product['material'] ?? 'No Material';
    if ($material !== 'No Material') {
        echo "<pre>";
        echo "Name: " . ($product['name'] ?? 'N/A') . "\n";
        echo "Material: \"{$material}\"\n";
        echo "Category: " . ($product['category'] ?? 'N/A') . "\n";
        echo "Subcategory: " . ($product['subcategory'] ?? 'N/A') . "\n";
        echo "Price: " . ($product['price'] ?? 'N/A') . "\n";
        echo "</pre>";
        $sampleCount++;
    }
}
?>

