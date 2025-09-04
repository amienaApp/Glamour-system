<?php
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

echo "<h1>Database Debug - Color Data</h1>";

try {
    $productModel = new Product();
    
    // Get a few products to check their color data
    $products = $productModel->getByCategory("Women's Clothing");
    
    if (empty($products)) {
        echo "<p>No products found in Women's Clothing category</p>";
    } else {
        echo "<h2>Found " . count($products) . " products</h2>";
        
        foreach (array_slice($products, 0, 5) as $index => $product) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<h3>Product " . ($index + 1) . ": " . htmlspecialchars($product['name'] ?? 'No Name') . "</h3>";
            echo "<p><strong>ID:</strong> " . ($product['_id'] ?? 'No ID') . "</p>";
            echo "<p><strong>Color:</strong> " . htmlspecialchars($product['color'] ?? 'NULL') . "</p>";
            echo "<p><strong>Color Variants:</strong> " . htmlspecialchars(json_encode($product['color_variants'] ?? [])) . "</p>";
            echo "<p><strong>Variants:</strong> " . htmlspecialchars(json_encode($product['variants'] ?? [])) . "</p>";
            echo "<p><strong>Product Variants:</strong> " . htmlspecialchars(json_encode($product['product_variants'] ?? [])) . "</p>";
            echo "<p><strong>Options:</strong> " . htmlspecialchars(json_encode($product['options'] ?? [])) . "</p>";
            echo "<p><strong>Product Options:</strong> " . htmlspecialchars(json_encode($product['product_options'] ?? [])) . "</p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace: " . htmlspecialchars($e->getTraceAsString()) . "</p>";
}
?>

