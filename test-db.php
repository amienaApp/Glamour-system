<?php
// Test database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Test Cart model
    echo "<h2>Testing Cart Model</h2>";
    require_once 'models/Cart.php';
    $cartModel = new Cart();
    echo "<p style='color: green;'>✅ Cart model loaded successfully</p>";
    
    // Test Order model
    echo "<h2>Testing Order Model</h2>";
    require_once 'models/Order.php';
    $orderModel = new Order();
    echo "<p style='color: green;'>✅ Order model loaded successfully</p>";
    
    // Test Payment model
    echo "<h2>Testing Payment Model</h2>";
    require_once 'models/Payment.php';
    $paymentModel = new Payment();
    echo "<p style='color: green;'>✅ Payment model loaded successfully</p>";
    
    echo "<h2>All Models Working!</h2>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

