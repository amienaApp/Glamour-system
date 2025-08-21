<?php
// Test cart status
echo "<h1>Cart Status Test</h1>";

try {
    require_once 'config/database.php';
    require_once 'models/Cart.php';
    
    $cartModel = new Cart();
    $userId = 'demo_user_123';
    
    // Get cart
    $cart = $cartModel->getCart($userId);
    
    echo "<h2>Cart Contents:</h2>";
    echo "<pre>" . print_r($cart, true) . "</pre>";
    
    if (empty($cart['items'])) {
        echo "<p style='color: orange;'>⚠️ Cart is empty! You need to add items to cart first.</p>";
        echo "<p><a href='products.php'>Go to Products</a> to add items to cart.</p>";
    } else {
        echo "<p style='color: green;'>✅ Cart has " . count($cart['items']) . " items</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

