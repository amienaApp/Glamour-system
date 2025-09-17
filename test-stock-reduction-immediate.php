<?php
/**
 * Test Immediate Stock Reduction After Payment
 */

echo "<h1>Test Immediate Stock Reduction After Payment</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Product.php';
    require_once 'models/Order.php';
    require_once 'models/Payment.php';
    require_once 'models/Cart.php';
    
    $productModel = new Product();
    $orderModel = new Order();
    $paymentModel = new Payment();
    $cartModel = new Cart();
    
    // Find a product with stock
    $products = $productModel->getAll(['stock' => ['$gt' => 0]]);
    
    if (empty($products)) {
        echo "<p style='color: red;'>No products with stock found!</p>";
        exit;
    }
    
    $testProduct = $products[0];
    $productId = (string)$testProduct['_id'];
    $productName = $testProduct['name'];
    $initialStock = (int)($testProduct['stock'] ?? 0);
    
    echo "<h2>Testing with Product: {$productName}</h2>";
    echo "<p><strong>Initial Stock:</strong> {$initialStock}</p>";
    echo "<p><strong>Product ID:</strong> {$productId}</p>";
    
    if ($initialStock <= 0) {
        echo "<p style='color: red;'>Product has no stock to test with!</p>";
        exit;
    }
    
    // Test the complete flow
    $userId = 'test_user_immediate';
    $quantity = 1;
    
    // Clear any existing cart
    $cartModel->clearCart($userId);
    
    // Step 1: Add to cart
    echo "<h3>Step 1: Add to Cart</h3>";
    $addResult = $cartModel->addToCart($userId, $productId, $quantity, '#ff0000', 'M');
    
    if (!$addResult) {
        echo "<p style='color: red;'>Failed to add to cart!</p>";
        exit;
    }
    echo "<p style='color: green;'>✓ Added to cart successfully</p>";
    
    // Step 2: Create order
    echo "<h3>Step 2: Create Order</h3>";
    $cart = $cartModel->getCart($userId);
    $orderId = $orderModel->createOrder($userId, $cart, [
        'shipping_address' => 'Test Address',
        'payment_method' => 'waafi'
    ]);
    
    if (!$orderId) {
        echo "<p style='color: red;'>Failed to create order!</p>";
        exit;
    }
    echo "<p style='color: green;'>✓ Order created: {$orderId}</p>";
    
    // Check stock after order creation (should be same)
    $productAfterOrder = $productModel->getById($productId);
    $stockAfterOrder = (int)($productAfterOrder['stock'] ?? 0);
    echo "<p><strong>Stock after order creation:</strong> {$stockAfterOrder} (should be {$initialStock})</p>";
    
    // Step 3: Create payment
    echo "<h3>Step 3: Create Payment</h3>";
    $paymentId = $paymentModel->createPayment([
        'order_id' => (string)$orderId,
        'user_id' => $userId,
        'amount' => $cart['total'],
        'payment_method' => 'waafi',
        'payment_details' => ['phone_number' => '0901234567']
    ]);
    
    if (!$paymentId) {
        echo "<p style='color: red;'>Failed to create payment!</p>";
        exit;
    }
    echo "<p style='color: green;'>✓ Payment created: {$paymentId}</p>";
    
    // Step 4: Process payment (this should reduce stock)
    echo "<h3>Step 4: Process Payment</h3>";
    echo "<p><strong>Stock before payment:</strong> {$stockAfterOrder}</p>";
    
    $paymentResult = $paymentModel->processPayment($paymentId, [
        'phone_number' => '0901234567'
    ]);
    
    if (!$paymentResult || !$paymentResult['success']) {
        echo "<p style='color: red;'>Payment processing failed!</p>";
        if ($paymentResult) {
            echo "<p><strong>Error:</strong> " . $paymentResult['message'] . "</p>";
        }
        exit;
    }
    
    echo "<p style='color: green;'>✓ Payment processed successfully</p>";
    echo "<p><strong>Order Confirmed:</strong> " . (isset($paymentResult['order_confirmed']) && $paymentResult['order_confirmed'] ? 'Yes' : 'No') . "</p>";
    
    // Check stock after payment (should be reduced)
    $productAfterPayment = $productModel->getById($productId);
    $stockAfterPayment = (int)($productAfterPayment['stock'] ?? 0);
    
    echo "<p><strong>Stock after payment:</strong> {$stockAfterPayment}</p>";
    echo "<p><strong>Expected stock:</strong> " . ($initialStock - $quantity) . "</p>";
    
    // Verify stock reduction
    if ($stockAfterPayment === ($initialStock - $quantity)) {
        echo "<p style='color: green; font-weight: bold; font-size: 18px;'>✅ STOCK REDUCTION IS WORKING!</p>";
        echo "<p>Stock was reduced from {$initialStock} to {$stockAfterPayment} (reduced by {$quantity})</p>";
    } else {
        echo "<p style='color: red; font-weight: bold; font-size: 18px;'>❌ STOCK REDUCTION IS NOT WORKING!</p>";
        echo "<p>Stock should be " . ($initialStock - $quantity) . " but is {$stockAfterPayment}</p>";
        
        // Debug the payment processing
        echo "<h3>Debug Payment Processing</h3>";
        
        // Check if order was confirmed
        $order = $orderModel->getById($orderId);
        if ($order) {
            echo "<p><strong>Order Status:</strong> " . ($order['status'] ?? 'Unknown') . "</p>";
            echo "<p><strong>Order Items:</strong> " . count($order['items'] ?? []) . "</p>";
        } else {
            echo "<p style='color: red;'>Order not found!</p>";
        }
        
        // Check payment status
        $payment = $paymentModel->getById($paymentId);
        if ($payment) {
            echo "<p><strong>Payment Status:</strong> " . ($payment['status'] ?? 'Unknown') . "</p>";
        } else {
            echo "<p style='color: red;'>Payment not found!</p>";
        }
    }
    
    // Restore original stock
    $productModel->update($productId, ['stock' => $initialStock]);
    echo "<p>✓ Stock restored to original value: {$initialStock}</p>";
    
    // Cleanup
    $cartModel->clearCart($userId);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
?>
