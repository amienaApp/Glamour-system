<?php
/**
 * Test Cart Clearing After Payment
 * This file tests the cart clearing functionality after successful payments
 */

session_start();
require_once 'models/Payment.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';

// Test configuration
$testUserId = 'test_user_' . time();
$testOrderId = 'test_order_' . time();

echo "<h1>Cart Clearing After Payment - Test Suite</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .test-result { margin: 5px 0; padding: 5px; border-left: 3px solid #ccc; }
</style>";

// Test 1: Add items to cart
echo "<div class='test-section'>";
echo "<h2>Test 1: Adding Items to Cart</h2>";

try {
    $cartModel = new Cart();
    
    // Add test items
    $items = [
        ['product_id' => 'test_product_1', 'quantity' => 2, 'price' => 25.99],
        ['product_id' => 'test_product_2', 'quantity' => 1, 'price' => 15.50],
        ['product_id' => 'test_product_3', 'quantity' => 3, 'price' => 8.99]
    ];
    
    foreach ($items as $item) {
        $success = $cartModel->addToCart($testUserId, $item['product_id'], $item['quantity'], '', '', ['price' => $item['price']]);
        if ($success) {
            echo "<div class='test-result success'>✓ Added {$item['product_id']} (Qty: {$item['quantity']}, Price: \${$item['price']})</div>";
        } else {
            echo "<div class='test-result error'>✗ Failed to add {$item['product_id']}</div>";
        }
    }
    
    // Verify cart contents
    $cart = $cartModel->getCart($testUserId);
    $cartCount = $cartModel->getCartItemCount($testUserId);
    
    echo "<div class='test-result info'>Cart contains {$cartCount} items with total: \${$cart['total']}</div>";
    
} catch (Exception $e) {
    echo "<div class='test-result error'>✗ Error adding items to cart: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 2: Create test order
echo "<div class='test-section'>";
echo "<h2>Test 2: Creating Test Order</h2>";

try {
    $orderModel = new Order();
    $orderDetails = [
        'shipping_address' => 'Test Address, Test City',
        'billing_address' => 'Test Address, Test City',
        'payment_method' => 'waafi',
        'notes' => 'Test order for cart clearing'
    ];
    
    $orderId = $orderModel->createOrder($testUserId, $cart, $orderDetails);
    
    if ($orderId) {
        echo "<div class='test-result success'>✓ Order created successfully: {$orderId}</div>";
        $testOrderId = $orderId;
    } else {
        echo "<div class='test-result error'>✗ Failed to create order</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='test-result error'>✗ Error creating order: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 3: Test payment processing with cart clearing
echo "<div class='test-section'>";
echo "<h2>Test 3: Payment Processing with Cart Clearing</h2>";

try {
    $paymentModel = new Payment();
    
    // Create payment
    $paymentData = [
        'order_id' => $testOrderId,
        'user_id' => $testUserId,
        'amount' => $cart['total'],
        'payment_method' => 'waafi',
        'payment_details' => []
    ];
    
    $paymentId = $paymentModel->createPayment($paymentData);
    
    if ($paymentId) {
        echo "<div class='test-result success'>✓ Payment created: {$paymentId}</div>";
        
        // Process payment (this should clear the cart)
        $paymentResult = $paymentModel->processPayment($paymentId, [
            'phone_number' => '+2520901234567',
            'full_name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        if ($paymentResult['success']) {
            echo "<div class='test-result success'>✓ Payment processed successfully</div>";
            echo "<div class='test-result info'>Message: {$paymentResult['message']}</div>";
            echo "<div class='test-result info'>Cart cleared: " . ($paymentResult['cart_cleared'] ? 'Yes' : 'No') . "</div>";
            echo "<div class='test-result info'>Order confirmed: " . ($paymentResult['order_confirmed'] ? 'Yes' : 'No') . "</div>";
        } else {
            echo "<div class='test-result error'>✗ Payment processing failed: {$paymentResult['message']}</div>";
        }
    } else {
        echo "<div class='test-result error'>✗ Failed to create payment</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='test-result error'>✗ Error processing payment: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 4: Verify cart is cleared
echo "<div class='test-section'>";
echo "<h2>Test 4: Verifying Cart is Cleared</h2>";

try {
    $cartAfterPayment = $cartModel->getCart($testUserId);
    $cartCountAfter = $cartModel->getCartItemCount($testUserId);
    
    if ($cartCountAfter == 0) {
        echo "<div class='test-result success'>✓ Cart successfully cleared! (0 items remaining)</div>";
    } else {
        echo "<div class='test-result error'>✗ Cart not cleared! Still has {$cartCountAfter} items</div>";
        echo "<div class='test-result info'>Remaining items: " . json_encode($cartAfterPayment['items']) . "</div>";
    }
    
    echo "<div class='test-result info'>Cart total after payment: \${$cartAfterPayment['total']}</div>";
    
} catch (Exception $e) {
    echo "<div class='test-result error'>✗ Error checking cart: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 5: Test different payment methods
echo "<div class='test-section'>";
echo "<h2>Test 5: Testing Different Payment Methods</h2>";

$paymentMethods = ['waafi', 'card', 'bank'];

foreach ($paymentMethods as $method) {
    echo "<h3>Testing {$method} payment method</h3>";
    
    try {
        // Add items to cart again
        $cartModel->addToCart($testUserId, 'test_product_' . $method, 1, '', '', ['price' => 10.00]);
        $cartBefore = $cartModel->getCart($testUserId);
        
        // Create payment
        $paymentData = [
            'order_id' => 'test_order_' . $method . '_' . time(),
            'user_id' => $testUserId,
            'amount' => 10.00,
            'payment_method' => $method,
            'payment_details' => []
        ];
        
        $paymentId = $paymentModel->createPayment($paymentData);
        
        if ($paymentId) {
            // Process payment with method-specific data
            $methodData = [];
            switch ($method) {
                case 'waafi':
                    $methodData = ['phone_number' => '+2520901234567'];
                    break;
                case 'card':
                    $methodData = ['card_number' => '4111111111111111', 'expiry' => '12/25', 'cvv' => '123'];
                    break;
                case 'bank':
                    $methodData = ['bank_name' => 'Test Bank', 'account_number' => '123456789'];
                    break;
            }
            
            $result = $paymentModel->processPayment($paymentId, $methodData);
            
            if ($result['success']) {
                $cartAfter = $cartModel->getCart($testUserId);
                $cartCountAfter = $cartModel->getCartItemCount($testUserId);
                
                echo "<div class='test-result success'>✓ {$method} payment successful, cart cleared: " . ($cartCountAfter == 0 ? 'Yes' : 'No') . "</div>";
            } else {
                echo "<div class='test-result error'>✗ {$method} payment failed: {$result['message']}</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='test-result error'>✗ Error testing {$method}: " . $e->getMessage() . "</div>";
    }
}
echo "</div>";

// Test 6: Cleanup
echo "<div class='test-section'>";
echo "<h2>Test 6: Cleanup</h2>";

try {
    // Clear test cart
    $cartModel->clearCart($testUserId);
    echo "<div class='test-result success'>✓ Test cart cleaned up</div>";
    
    // Clean up test orders and payments
    // Note: In a real scenario, you might want to delete test data
    echo "<div class='test-result info'>Test data cleanup completed</div>";
    
} catch (Exception $e) {
    echo "<div class='test-result error'>✗ Error during cleanup: " . $e->getMessage() . "</div>";
}
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Test Summary</h2>";
echo "<p>Cart clearing after payment functionality has been tested across all payment methods.</p>";
echo "<p>Check the results above to verify that:</p>";
echo "<ul>";
echo "<li>Items can be added to cart</li>";
echo "<li>Orders can be created</li>";
echo "<li>Payments can be processed</li>";
echo "<li>Carts are cleared after successful payments</li>";
echo "<li>All payment methods work correctly</li>";
echo "</ul>";
echo "</div>";

?>

