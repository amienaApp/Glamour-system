<?php
// Simple place order test without email complications
echo "<h1>Simple Place Order Test</h1>";

try {
    require_once 'config/database.php';
    require_once 'models/Cart.php';
    require_once 'models/Order.php';
    require_once 'models/Payment.php';
    
    $cartModel = new Cart();
    $orderModel = new Order();
    $paymentModel = new Payment();
    $userId = 'demo_user_123';
    
    // Get cart
    $cart = $cartModel->getCart($userId);
    
    if (empty($cart['items'])) {
        echo "<p style='color: orange;'>⚠️ Cart is empty! Adding a test item...</p>";
        
        // Add a test item if cart is empty
        require_once 'models/Product.php';
        $productModel = new Product();
        $products = $productModel->getAll();
        
        if (!empty($products)) {
            $firstProduct = $products[0];
            $cartModel->addToCart($userId, $firstProduct['_id'], 1);
            $cart = $cartModel->getCart($userId);
            echo "<p style='color: green;'>✅ Added test item to cart</p>";
        } else {
            echo "<p style='color: red;'>❌ No products available</p>";
            exit;
        }
    }
    
    echo "<h2>Cart Contents:</h2>";
    echo "<pre>" . print_r($cart, true) . "</pre>";
    
    // Test order creation
    echo "<h2>Testing Order Creation:</h2>";
    $orderDetails = [
        'shipping_address' => '123 Test Street, Mogadishu',
        'billing_address' => '123 Test Street, Mogadishu',
        'payment_method' => 'waafi',
        'notes' => 'Test order'
    ];
    
    $orderId = $orderModel->createOrder($userId, $cart, $orderDetails);
    
    if ($orderId) {
        echo "<p style='color: green;'>✅ Order created successfully! Order ID: " . $orderId . "</p>";
        
        // Test payment creation
        echo "<h2>Testing Payment Creation:</h2>";
        $paymentData = [
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $cart['total'],
            'payment_method' => 'waafi',
            'payment_details' => [
                'full_name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '612345678'
            ]
        ];
        
        $paymentId = $paymentModel->createPayment($paymentData);
        
        if ($paymentId) {
            echo "<p style='color: green;'>✅ Payment created successfully! Payment ID: " . $paymentId . "</p>";
            
            // Test payment processing (without email)
            echo "<h2>Testing Payment Processing:</h2>";
            
            // Temporarily disable email sending
            $paymentData['phone_number'] = '612345678';
            
            // Capture any output
            ob_start();
            $result = $paymentModel->processPayment($paymentId, $paymentData);
            $output = ob_get_clean();
            
            if (!empty($output)) {
                echo "<p style='color: orange;'>⚠️ Output captured: " . htmlspecialchars($output) . "</p>";
            }
            
            echo "<p><strong>Payment Result:</strong></p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
            
            if ($result && $result['success']) {
                echo "<p style='color: green;'>✅ Payment processed successfully!</p>";
                echo "<p><strong>Transaction ID:</strong> " . ($result['transaction_id'] ?? 'N/A') . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Payment processing failed</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Failed to create payment</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Failed to create order</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

