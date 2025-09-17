<?php
/**
 * Simple Database Check
 */

echo "<h1>Simple Database Check</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $productsCollection = $db->getCollection('products');
    
    // Get all products
    $products = $productsCollection->find([]);
    
    echo "<p>Checking products in database...</p>";
    
    $count = 0;
    foreach ($products as $product) {
        $count++;
        $name = $product['name'] ?? 'Unknown';
        $price = $product['price'] ?? 0;
        $stock = (int)($product['stock'] ?? 0);
        
        // Show products with price around $45
        if (abs($price - 45) < 5) {
            echo "<p><strong>" . htmlspecialchars($name) . "</strong> - $" . number_format($price, 2) . " - Stock: {$stock}</p>";
        }
    }
    
    echo "<p>Total products checked: {$count}</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
