<?php
/**
 * Debug Sold Out Functionality Issue
 * This script helps debug why the sold out functionality is not working
 */

echo "<h1>Debug Sold Out Functionality Issue</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Product.php';
    require_once 'models/Order.php';
    require_once 'models/Payment.php';
    
    $productModel = new Product();
    $orderModel = new Order();
    $paymentModel = new Payment();
    
    // Step 1: Check the "elegent beautiful fail" product
    echo "<h2>Step 1: Check 'elegent beautiful fail' Product</h2>";
    
    $product = $productModel->getAll(['name' => 'elegent beautiful fail']);
    
    if (empty($product)) {
        echo "<p style='color: red;'>Product 'elegent beautiful fail' not found!</p>";
        exit;
    }
    
    $testProduct = $product[0];
    $productId = (string)$testProduct['_id'];
    $productName = $testProduct['name'];
    $currentStock = (int)($testProduct['stock'] ?? 0);
    $isAvailable = isset($testProduct['available']) ? $testProduct['available'] : true;
    
    echo "<p><strong>Product:</strong> {$productName}</p>";
    echo "<p><strong>Current Stock:</strong> {$currentStock}</p>";
    echo "<p><strong>Available:</strong> " . ($isAvailable ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Product ID:</strong> {$productId}</p>";
    
    // Step 2: Check recent orders for this product
    echo "<h2>Step 2: Check Recent Orders for This Product</h2>";
    
    $recentOrders = $orderModel->getAllOrders();
    $ordersWithThisProduct = [];
    
    foreach ($recentOrders as $order) {
        if (isset($order['items']) && is_array($order['items'])) {
            foreach ($order['items'] as $item) {
                if (isset($item['product_id']) && (string)$item['product_id'] === $productId) {
                    $ordersWithThisProduct[] = [
                        'order_id' => (string)$order['_id'],
                        'status' => $order['status'] ?? 'Unknown',
                        'created_at' => $order['created_at'] ?? 'Unknown',
                        'quantity' => $item['quantity'] ?? 0
                    ];
                }
            }
        }
    }
    
    if (empty($ordersWithThisProduct)) {
        echo "<p style='color: orange;'>No recent orders found for this product.</p>";
    } else {
        echo "<p>Found " . count($ordersWithThisProduct) . " recent orders for this product:</p>";
        
        foreach ($ordersWithThisProduct as $order) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
            echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
            echo "<p><strong>Status:</strong> {$order['status']}</p>";
            echo "<p><strong>Quantity:</strong> {$order['quantity']}</p>";
            echo "<p><strong>Created:</strong> {$order['created_at']}</p>";
            echo "</div>";
        }
    }
    
    // Step 3: Check recent payments
    echo "<h2>Step 3: Check Recent Payments</h2>";
    
    $recentPayments = $paymentModel->getAllPayments(['limit' => 5]);
    
    if (empty($recentPayments)) {
        echo "<p style='color: orange;'>No recent payments found.</p>";
    } else {
        echo "<p>Found " . count($recentPayments) . " recent payments:</p>";
        
        foreach ($recentPayments as $payment) {
            $paymentId = (string)$payment['_id'];
            $orderId = (string)$payment['order_id'];
            $status = $payment['status'] ?? 'Unknown';
            $amount = $payment['amount'] ?? 0;
            $createdAt = $payment['created_at'] ?? 'Unknown';
            
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
            echo "<p><strong>Payment ID:</strong> {$paymentId}</p>";
            echo "<p><strong>Order ID:</strong> {$orderId}</p>";
            echo "<p><strong>Status:</strong> {$status}</p>";
            echo "<p><strong>Amount:</strong> $" . number_format($amount, 2) . "</p>";
            echo "<p><strong>Created:</strong> {$createdAt}</p>";
            echo "</div>";
        }
    }
    
    // Step 4: Test the complete payment flow
    echo "<h2>Step 4: Test Complete Payment Flow</h2>";
    
    if ($currentStock > 0) {
        echo "<p>Testing payment flow with current stock: {$currentStock}</p>";
        
        // Simulate the payment flow
        $userId = 'test_user_debug';
        $quantity = 1;
        
        // Add to cart
        $cartModel = new Cart();
        $cartModel->clearCart($userId);
        $addResult = $cartModel->addToCart($userId, $productId, $quantity, '#ff0000', 'M');
        
        if ($addResult) {
            echo "<p style='color: green;'>✓ Product added to cart successfully</p>";
            
            // Create order
            $cart = $cartModel->getCart($userId);
            $orderId = $orderModel->createOrder($userId, $cart, [
                'shipping_address' => 'Test Address',
                'payment_method' => 'waafi'
            ]);
            
            if ($orderId) {
                echo "<p style='color: green;'>✓ Order created successfully</p>";
                
                // Create payment
                $paymentId = $paymentModel->createPayment([
                    'order_id' => (string)$orderId,
                    'user_id' => $userId,
                    'amount' => $cart['total'],
                    'payment_method' => 'waafi',
                    'payment_details' => ['phone_number' => '0901234567']
                ]);
                
                if ($paymentId) {
                    echo "<p style='color: green;'>✓ Payment created successfully</p>";
                    
                    // Process payment
                    $paymentResult = $paymentModel->processPayment($paymentId, [
                        'phone_number' => '0901234567'
                    ]);
                    
                    if ($paymentResult && $paymentResult['success']) {
                        echo "<p style='color: green;'>✓ Payment processed successfully</p>";
                        echo "<p><strong>Order Confirmed:</strong> " . (isset($paymentResult['order_confirmed']) && $paymentResult['order_confirmed'] ? 'Yes' : 'No') . "</p>";
                        
                        // Check stock after payment
                        $updatedProduct = $productModel->getById($productId);
                        $newStock = (int)($updatedProduct['stock'] ?? 0);
                        
                        echo "<p><strong>Stock Before Payment:</strong> {$currentStock}</p>";
                        echo "<p><strong>Stock After Payment:</strong> {$newStock}</p>";
                        
                        if ($newStock === ($currentStock - $quantity)) {
                            echo "<p style='color: green; font-weight: bold;'>✅ Stock reduction is working!</p>";
                        } else {
                            echo "<p style='color: red; font-weight: bold;'>❌ Stock reduction is NOT working!</p>";
                            echo "<p>Stock should be " . ($currentStock - $quantity) . " but is {$newStock}</p>";
                        }
                        
                        // Restore original stock
                        $productModel->update($productId, ['stock' => $currentStock]);
                        echo "<p>✓ Stock restored to original value</p>";
                        
                    } else {
                        echo "<p style='color: red;'>✗ Payment processing failed</p>";
                        if ($paymentResult) {
                            echo "<p><strong>Error:</strong> " . $paymentResult['message'] . "</p>";
                        }
                    }
                } else {
                    echo "<p style='color: red;'>✗ Failed to create payment</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Failed to create order</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to add product to cart</p>";
        }
        
        // Cleanup
        $cartModel->clearCart($userId);
    } else {
        echo "<p style='color: orange;'>Product has no stock, cannot test payment flow</p>";
    }
    
    // Step 5: Check frontend display logic
    echo "<h2>Step 5: Check Frontend Display Logic</h2>";
    
    $stock = (int)($testProduct['stock'] ?? 0);
    $isAvailable = isset($testProduct['available']) ? $testProduct['available'] : true;
    $isSoldOut = $stock <= 0 || !$isAvailable;
    
    echo "<p><strong>Stock:</strong> {$stock}</p>";
    echo "<p><strong>Available:</strong> " . ($isAvailable ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Is Sold Out:</strong> " . ($isSoldOut ? 'YES' : 'No') . "</p>";
    
    if ($isSoldOut) {
        echo "<p style='color: green;'>✅ Product should show as SOLD OUT</p>";
    } elseif ($stock > 0 && $stock <= 2) {
        echo "<p style='color: orange;'>⚠️ Product should show 'Only {$stock} left in stock!'</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Product should show normal stock</p>";
    }
    
    // Summary
    echo "<h2>Summary</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>Current Status:</h3>";
    echo "<ul>";
    echo "<li><strong>Product Stock:</strong> {$currentStock}</p>";
    echo "<li><strong>Recent Orders:</strong> " . count($ordersWithThisProduct) . "</p>";
    echo "<li><strong>Recent Payments:</strong> " . count($recentPayments) . "</p>";
    echo "<li><strong>Frontend Display:</strong> " . ($isSoldOut ? 'SOLD OUT' : ($stock <= 2 ? "Only {$stock} left" : 'Normal stock')) . "</p>";
    echo "</ul>";
    
    if ($currentStock <= 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Product should show as SOLD OUT</p>";
    } elseif ($currentStock <= 2) {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ Product should show low stock warning</p>";
    } else {
        echo "<p style='color: blue; font-weight: bold;'>ℹ️ Product has normal stock</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
?>
