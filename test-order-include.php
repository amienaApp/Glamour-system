<?php
echo "Testing Order model inclusion...\n";

try {
    require_once 'models/Order.php';
    echo "✓ Order model included successfully\n";
    
    $orderModel = new Order();
    echo "✓ Order model instantiated successfully\n";
    
} catch (Exception $e) {
    echo "✗ Error including Order model: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>
