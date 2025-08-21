<?php
// Add test items to cart
echo "<h1>Add Test Items to Cart</h1>";

try {
    require_once 'config/database.php';
    require_once 'models/Cart.php';
    require_once 'models/Product.php';
    
    $cartModel = new Cart();
    $productModel = new Product();
    $userId = 'demo_user_123';
    
    // Get some products to add
    $products = $productModel->getAll();
    
    if (empty($products)) {
        echo "<p style='color: red;'>❌ No products found in database!</p>";
        echo "<p>Please add some products first.</p>";
    } else {
        echo "<h2>Available Products:</h2>";
        echo "<ul>";
        foreach ($products as $product) {
            echo "<li>" . $product['name'] . " - $" . $product['price'] . "</li>";
        }
        echo "</ul>";
        
        // Add first product to cart
        $firstProduct = $products[0];
        $productId = $firstProduct['_id'];
        $quantity = 2;
        
        echo "<h2>Adding to Cart:</h2>";
        echo "<p>Product: " . $firstProduct['name'] . "</p>";
        echo "<p>Quantity: " . $quantity . "</p>";
        echo "<p>Price: $" . $firstProduct['price'] . "</p>";
        
        $success = $cartModel->addToCart($userId, $productId, $quantity);
        
        if ($success) {
            echo "<p style='color: green;'>✅ Successfully added to cart!</p>";
            
            // Show updated cart
            $cart = $cartModel->getCart($userId);
            echo "<h2>Updated Cart:</h2>";
            echo "<pre>" . print_r($cart, true) . "</pre>";
            
            echo "<p><a href='place-order.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Place Order</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to add to cart</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

