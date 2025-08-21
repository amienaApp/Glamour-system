<?php
// Simple test to debug cart API issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing cart API...\n";

// Test the required files
$requiredFiles = [
    'config/database.php',
    'models/Cart.php',
    'models/Order.php',
    'models/Product.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

// Test database connection
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test Cart model
try {
    require_once 'models/Cart.php';
    $cart = new Cart();
    echo "✓ Cart model loaded successfully\n";
    
    // Test getCartItemCount method
    $count = $cart->getCartItemCount('demo_user_123');
    echo "✓ Cart count for demo user: $count\n";
} catch (Exception $e) {
    echo "✗ Cart model error: " . $e->getMessage() . "\n";
}

// Test Product model
try {
    require_once 'models/Product.php';
    $product = new Product();
    echo "✓ Product model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Product model error: " . $e->getMessage() . "\n";
}

// Test Order model
try {
    require_once 'models/Order.php';
    $order = new Order();
    echo "✓ Order model loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Order model error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>
