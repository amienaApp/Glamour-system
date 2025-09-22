<?php
/**
 * Check Pending Orders
 */

echo "<h1>Check Pending Orders</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $ordersCollection = $db->getCollection('orders');
    
    // Find pending orders for "elegent beautiful fail"
    $productId = '68c93b82e93ef16f8d080453'; // elegent beautiful fail product ID
    
    echo "<h2>Pending Orders for 'elegent beautiful fail':</h2>";
    
    $pendingOrders = $ordersCollection->find([
        'status' => 'pending',
        'items.product_id' => $productId
    ]);
    
    $count = 0;
    foreach ($pendingOrders as $order) {
        $count++;
        $orderId = (string)$order['_id'];
        $createdAt = $order['created_at'] ?? 'Unknown';
        $expiresAt = $order['expires_at'] ?? 'Unknown';
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<p><strong>Order ID:</strong> {$orderId}</p>";
        echo "<p><strong>Created:</strong> {$createdAt}</p>";
        echo "<p><strong>Expires:</strong> {$expiresAt}</p>";
        echo "<p><strong>Status:</strong> pending</p>";
        echo "</div>";
    }
    
    if ($count === 0) {
        echo "<p style='color: green;'>No pending orders found for this product.</p>";
    } else {
        echo "<p style='color: orange;'>Found {$count} pending orders for this product.</p>";
        echo "<p><strong>Note:</strong> These orders are holding up stock but haven't been paid for yet.</p>";
    }
    
    // Check if any orders are expired
    echo "<h2>Expired Orders:</h2>";
    
    $expiredOrders = $ordersCollection->find([
        'status' => 'pending',
        'expires_at' => ['$lt' => date('Y-m-d H:i:s')]
    ]);
    
    $expiredCount = 0;
    foreach ($expiredOrders as $order) {
        $expiredCount++;
        $orderId = (string)$order['_id'];
        $createdAt = $order['created_at'] ?? 'Unknown';
        $expiresAt = $order['expires_at'] ?? 'Unknown';
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background: #ffebee;'>";
        echo "<p><strong>Order ID:</strong> {$orderId}</p>";
        echo "<p><strong>Created:</strong> {$createdAt}</p>";
        echo "<p><strong>Expired:</strong> {$expiresAt}</p>";
        echo "<p><strong>Status:</strong> pending (EXPIRED)</p>";
        echo "</div>";
    }
    
    if ($expiredCount === 0) {
        echo "<p style='color: green;'>No expired orders found.</p>";
    } else {
        echo "<p style='color: red;'>Found {$expiredCount} expired orders.</p>";
        echo "<p><strong>Note:</strong> These orders should be cleaned up to free up stock.</p>";
    }
    
    // Summary
    echo "<h2>Summary</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>Current Situation:</h3>";
    echo "<ul>";
    echo "<li><strong>Pending Orders:</strong> {$count}</li>";
    echo "<li><strong>Expired Orders:</strong> {$expiredCount}</li>";
    echo "<li><strong>Current Stock:</strong> 1</li>";
    echo "</ul>";
    
    if ($count > 0) {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ You have pending orders that haven't been paid for yet.</p>";
        echo "<p>These orders are preventing the stock from being available to other customers.</p>";
    }
    
    if ($expiredCount > 0) {
        echo "<p style='color: red; font-weight: bold;'>❌ You have expired orders that should be cleaned up.</p>";
        echo "<p>These orders are holding up stock unnecessarily.</p>";
    }
    
    if ($count === 0 && $expiredCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ No pending or expired orders found.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
