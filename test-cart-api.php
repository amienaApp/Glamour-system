<?php
// Simple test for cart API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing cart API...\n";

try {
    require_once 'config1/mongodb.php';
    echo "MongoDB config loaded successfully\n";
    
    require_once 'models/Cart.php';
    echo "Cart model loaded successfully\n";
    
    $cart = new Cart();
    echo "Cart instance created successfully\n";
    
    // Test getCartSummary method
    $summary = $cart->getCartSummary('test_user');
    echo "Cart summary test: " . json_encode($summary) . "\n";
    
    echo "All tests passed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>

