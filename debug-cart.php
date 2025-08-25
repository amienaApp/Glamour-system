<?php
/**
 * Debug Script for Cart and Payment Issues
 * This script helps debug why the payment page shows $0.00
 */

// Start session
session_start();

// Include required files
require_once 'config/mongodb.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

// Include cart configuration
if (file_exists('cart-config.php')) {
    require_once 'cart-config.php';
}

$userId = getCartUserId();

echo "<h1>Cart and Payment Debug Information</h1>";
echo "<hr>";

// 1. Check User ID
echo "<h2>1. User ID Information</h2>";
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . "<br>";
echo "Session current_cart_user_id: " . ($_SESSION['current_cart_user_id'] ?? 'not set') . "<br>";
echo "Final user ID being used: " . $userId . "<br>";
echo "<hr>";

// 2. Check Cart
echo "<h2>2. Cart Information</h2>";
$cartModel = new Cart();
$cart = $cartModel->getCart($userId);

if ($cart) {
    echo "Cart found: YES<br>";
    echo "Cart total: $" . number_format($cart['total'], 2) . "<br>";
    echo "Cart item count: " . $cart['item_count'] . "<br>";
    echo "Cart items:<br>";
    
    if (!empty($cart['items'])) {
        foreach ($cart['items'] as $index => $item) {
            echo "- Item " . ($index + 1) . ": ";
            if (isset($item['product'])) {
                echo $item['product']['name'] . " (Qty: " . $item['quantity'] . ", Price: $" . $item['product']['price'] . ", Subtotal: $" . $item['subtotal'] . ")<br>";
            } else {
                echo "Product ID: " . $item['product_id'] . " (Product details not loaded)<br>";
            }
        }
    } else {
        echo "No items in cart<br>";
    }
} else {
    echo "Cart found: NO<br>";
}
echo "<hr>";

// 3. Check Products in Database
echo "<h2>3. Sample Products in Database</h2>";
$productModel = new Product();
$products = $productModel->getAll([], [], 5); // Get first 5 products

if (!empty($products)) {
    echo "Found " . count($products) . " products in database:<br>";
    foreach ($products as $product) {
        echo "- " . $product['name'] . " (Price: $" . $product['price'] . ")<br>";
    }
} else {
    echo "No products found in database<br>";
}
echo "<hr>";

// 4. Check Recent Orders
echo "<h2>4. Recent Orders</h2>";
$orderModel = new Order();
$orders = $orderModel->getUserOrders($userId);

if (!empty($orders)) {
    echo "Found " . count($orders) . " orders for user:<br>";
    foreach ($orders as $order) {
        echo "- Order ID: " . $order['_id'] . " (Total: $" . number_format($order['total_amount'], 2) . ", Status: " . $order['status'] . ")<br>";
    }
} else {
    echo "No orders found for user<br>";
}
echo "<hr>";

// 5. Test Order Creation
echo "<h2>5. Test Order Creation</h2>";
if (!empty($cart['items'])) {
    echo "Attempting to create test order...<br>";
    
    $orderDetails = [
        'shipping_address' => 'Test Address',
        'payment_method' => 'test'
    ];
    
    $orderId = $orderModel->createOrder($userId, $cart, $orderDetails);
    
    if ($orderId) {
        echo "Test order created successfully! Order ID: " . $orderId . "<br>";
        
        // Retrieve the created order
        $createdOrder = $orderModel->getById($orderId);
        if ($createdOrder) {
            echo "Retrieved order total: $" . number_format($createdOrder['total_amount'], 2) . "<br>";
        } else {
            echo "Failed to retrieve created order<br>";
        }
    } else {
        echo "Failed to create test order<br>";
    }
} else {
    echo "Cannot create test order - cart is empty<br>";
}
echo "<hr>";

// 6. Payment Page Simulation
echo "<h2>6. Payment Page Simulation</h2>";
if (!empty($orders)) {
    $lastOrder = $orders[0]; // Get most recent order
    echo "Simulating payment page with order ID: " . $lastOrder['_id'] . "<br>";
    
    $order = $orderModel->getById($lastOrder['_id']);
    if ($order) {
        echo "Order found: YES<br>";
        echo "Order total_amount: $" . number_format($order['total_amount'], 2) . "<br>";
    } else {
        echo "Order found: NO<br>";
    }
} else {
    echo "No orders to simulate payment page<br>";
}

echo "<hr>";
echo "<h2>Debug Complete</h2>";
echo "Check the error logs for additional debugging information.<br>";
echo "Common log locations:<br>";
echo "- Apache: /var/log/apache2/error.log<br>";
echo "- XAMPP: C:/xampp/apache/logs/error.log<br>";
echo "- PHP: Check your php.ini error_log setting<br>";
?>
