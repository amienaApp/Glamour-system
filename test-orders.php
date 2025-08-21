<?php
// Test orders functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing orders functionality...\n";

try {
    require_once 'models/Order.php';
    require_once 'models/Cart.php';
    require_once 'models/Product.php';
    
    $orderModel = new Order();
    $cartModel = new Cart();
    $productModel = new Product();
    
    echo "✓ Models loaded successfully\n";
    
    // Test getting user orders
    $userId = 'demo_user_123';
    $orders = $orderModel->getUserOrders($userId);
    
    echo "✓ Found " . count($orders) . " orders for user\n";
    
    if (!empty($orders)) {
        $order = $orders[0];
        echo "✓ First order structure:\n";
        echo "  - Order ID: " . ($order['_id'] ?? 'N/A') . "\n";
        echo "  - Status: " . ($order['status'] ?? 'N/A') . "\n";
        echo "  - Total: $" . ($order['total_amount'] ?? 'N/A') . "\n";
        echo "  - Items count: " . count($order['items'] ?? []) . "\n";
        
        if (!empty($order['items'])) {
            $item = $order['items'][0];
            echo "✓ First item structure:\n";
            echo "  - Product ID: " . ($item['product_id'] ?? 'N/A') . "\n";
            echo "  - Quantity: " . ($item['quantity'] ?? 'N/A') . "\n";
            
            if (isset($item['product'])) {
                echo "  - Product name: " . ($item['product']['name'] ?? 'N/A') . "\n";
                echo "  - Product price: $" . ($item['product']['price'] ?? 'N/A') . "\n";
            } else {
                echo "  - No product details found\n";
            }
            
            if (isset($item['price'])) {
                echo "  - Direct price: $" . $item['price'] . "\n";
            }
            
            if (isset($item['subtotal'])) {
                echo "  - Subtotal: $" . $item['subtotal'] . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
?>
