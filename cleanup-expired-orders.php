<?php
/**
 * Cleanup Expired Orders
 * This script cleans up expired pending orders to free up stock
 */

echo "<h1>Cleanup Expired Orders</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $ordersCollection = $db->getCollection('orders');
    
    // Find expired pending orders
    $expiredOrders = $ordersCollection->find([
        'status' => 'pending',
        'expires_at' => ['$lt' => date('Y-m-d H:i:s')]
    ]);
    
    $expiredCount = 0;
    $cleanedOrders = [];
    
    foreach ($expiredOrders as $order) {
        $expiredCount++;
        $orderId = (string)$order['_id'];
        $createdAt = $order['created_at'] ?? 'Unknown';
        $expiresAt = $order['expires_at'] ?? 'Unknown';
        
        $cleanedOrders[] = [
            'order_id' => $orderId,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt
        ];
    }
    
    if ($expiredCount === 0) {
        echo "<p style='color: green;'>No expired orders found.</p>";
    } else {
        echo "<p style='color: orange;'>Found {$expiredCount} expired orders:</p>";
        
        foreach ($cleanedOrders as $order) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background: #ffebee;'>";
            echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
            echo "<p><strong>Created:</strong> {$order['created_at']}</p>";
            echo "<p><strong>Expired:</strong> {$order['expires_at']}</p>";
            echo "</div>";
        }
        
        // Ask for confirmation
        echo "<h2>Cleanup Confirmation</h2>";
        echo "<p>These expired orders are holding up stock unnecessarily.</p>";
        echo "<p>Do you want to clean them up? (This will free up stock for other customers)</p>";
        
        // For now, just show what would be cleaned up
        echo "<p style='color: blue;'>To actually clean them up, you would need to:</p>";
        echo "<ol>";
        echo "<li>Delete these expired orders from the database</li>";
        echo "<li>This will free up the stock they were holding</li>";
        echo "<li>Other customers can then purchase the products</li>";
        echo "</ol>";
    }
    
    // Check current pending orders
    echo "<h2>Current Pending Orders</h2>";
    
    $pendingOrders = $ordersCollection->find([
        'status' => 'pending'
    ]);
    
    $pendingCount = 0;
    foreach ($pendingOrders as $order) {
        $pendingCount++;
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
    
    if ($pendingCount === 0) {
        echo "<p style='color: green;'>No pending orders found.</p>";
    } else {
        echo "<p style='color: orange;'>Found {$pendingCount} pending orders.</p>";
    }
    
    // Summary
    echo "<h2>Summary</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>Current Situation:</h3>";
    echo "<ul>";
    echo "<li><strong>Expired Orders:</strong> {$expiredCount}</li>";
    echo "<li><strong>Pending Orders:</strong> {$pendingCount}</li>";
    echo "</ul>";
    
    if ($expiredCount > 0) {
        echo "<p style='color: red; font-weight: bold;'>❌ You have {$expiredCount} expired orders that should be cleaned up.</p>";
        echo "<p>These orders are holding up stock unnecessarily.</p>";
    }
    
    if ($pendingCount > 0) {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ You have {$pendingCount} pending orders.</p>";
        echo "<p>These orders are holding up stock until they expire or are paid for.</p>";
    }
    
    if ($expiredCount === 0 && $pendingCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ No pending or expired orders found.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
