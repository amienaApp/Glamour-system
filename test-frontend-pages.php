<?php
/**
 * Test Frontend Pages Sold Out Display
 * This script tests if sold out products display correctly on frontend pages
 */

require_once 'config1/mongodb.php';
require_once 'models/Product.php';

echo "<h1>Testing Frontend Pages Sold Out Display</h1>";

try {
    $productModel = new Product();
    
    // Get a few products to test
    $products = $productModel->getAll(['limit' => 5]);
    
    if (empty($products)) {
        echo "<p style='color: red;'>No products found. Please add some products first.</p>";
        exit;
    }
    
    echo "<h2>Testing Product Display Logic</h2>";
    
    foreach ($products as $product) {
        $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
        $isAvailable = isset($product['available']) ? $product['available'] : true;
        $isSoldOut = $stock <= 0 || !$isAvailable;
        
        echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px; background: " . ($isSoldOut ? '#ffebee' : '#e8f5e8') . ";'>";
        echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
        echo "<p><strong>Stock:</strong> {$stock}</p>";
        echo "<p><strong>Available:</strong> " . ($isAvailable ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>Sold Out:</strong> " . ($isSoldOut ? 'YES' : 'No') . "</p>";
        
        // Simulate the product card logic
        if ($isSoldOut) {
            echo "<p style='color: #dc3545; font-weight: bold;'>🔴 SOLD OUT</p>";
            echo "<button disabled style='background: #ccc; color: #666; padding: 10px; border: none; border-radius: 5px;'>Sold Out</button>";
        } elseif ($stock > 0 && $stock <= 2) {
            echo "<p style='color: #ffc107; font-weight: bold;'>⚠️ Only {$stock} left in stock!</p>";
            echo "<button style='background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;'>Add to Cart</button>";
        } else {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ In Stock</p>";
            echo "<button style='background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;'>Add to Cart</button>";
        }
        
        echo "</div>";
    }
    
    echo "<h2>Test Instructions</h2>";
    echo "<ol>";
    echo "<li>Go to your admin panel and edit a product</li>";
    echo "<li>Set the stock to 0 and save</li>";
    echo "<li>Refresh this page to see if the product shows as sold out</li>";
    echo "<li>Go to your frontend pages (beauty, women, men, etc.) and check if the product shows as sold out there too</li>";
    echo "<li>Try to add the sold out product to cart - it should be blocked</li>";
    echo "</ol>";
    
    echo "<h2>Quick Test</h2>";
    echo "<p>To quickly test, you can:</p>";
    echo "<ol>";
    echo "<li>Click <a href='debug-product-stock.php' target='_blank'>here</a> to see all products and their stock status</li>";
    echo "<li>Edit a product in admin and set stock to 0</li>";
    echo "<li>Check the debug page again to confirm the change</li>";
    echo "<li>Visit your frontend pages to see the sold out display</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

