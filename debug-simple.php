<?php
/**
 * Simple Debug Test
 */

echo "<h1>Simple Debug Test</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    echo "<p>Product model loaded successfully</p>";
    
    // Get all products
    $products = $productModel->getAll();
    
    echo "<p>Total products: " . count($products) . "</p>";
    
    if (count($products) > 0) {
        $firstProduct = $products[0];
        echo "<p>First product: " . htmlspecialchars($firstProduct['name'] ?? 'Unknown') . "</p>";
        echo "<p>Stock: " . (int)($firstProduct['stock'] ?? 0) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
?>
