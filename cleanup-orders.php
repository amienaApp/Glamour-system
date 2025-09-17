<?php
/**
 * Cleanup Orders - Actually Delete Expired and Old Pending Orders
 */

echo "<h1>Cleaning Up Orders</h1>";

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
    $deletedExpired = [];
    
    foreach ($expiredOrders as $order) {
        $expiredCount++;
        $orderId = (string)$order['_id'];
        $createdAt = $order['created_at'] ?? 'Unknown';
        $expiresAt = $order['expires_at'] ?? 'Unknown';
        
        // Delete the expired order
        $deleteResult = $ordersCollection->deleteOne(['_id' => $order['_id']]);
        
        if ($deleteResult->getDeletedCount() > 0) {
            $deletedExpired[] = [
                'order_id' => $orderId,
                'created_at' => $createdAt,
                'expires_at' => $expiresAt
            ];
        }
    }
    
    echo "<h2>Expired Orders Cleanup</h2>";
    if (count($deletedExpired) === 0) {
        echo "<p style='color: green;'>No expired orders found to clean up.</p>";
    } else {
        echo "<p style='color: green;'>Successfully deleted " . count($deletedExpired) . " expired orders:</p>";
        
        foreach ($deletedExpired as $order) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background: #e8f5e8;'>";
            echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
            echo "<p><strong>Created:</strong> {$order['created_at']}</p>";
            echo "<p><strong>Expired:</strong> {$order['expires_at']}</p>";
            echo "<p style='color: green;'>✓ DELETED</p>";
            echo "</div>";
        }
    }
    
    // Find old pending orders (older than 1 day)
    $oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));
    $oldPendingOrders = $ordersCollection->find([
        'status' => 'pending',
        'created_at' => ['$lt' => $oneDayAgo]
    ]);
    
    $oldCount = 0;
    $deletedOld = [];
    
    foreach ($oldPendingOrders as $order) {
        $oldCount++;
        $orderId = (string)$order['_id'];
        $createdAt = $order['created_at'] ?? 'Unknown';
        
        // Delete the old pending order
        $deleteResult = $ordersCollection->deleteOne(['_id' => $order['_id']]);
        
        if ($deleteResult->getDeletedCount() > 0) {
            $deletedOld[] = [
                'order_id' => $orderId,
                'created_at' => $createdAt
            ];
        }
    }
    
    echo "<h2>Old Pending Orders Cleanup</h2>";
    if (count($deletedOld) === 0) {
        echo "<p style='color: green;'>No old pending orders found to clean up.</p>";
    } else {
        echo "<p style='color: green;'>Successfully deleted " . count($deletedOld) . " old pending orders:</p>";
        
        foreach ($deletedOld as $order) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background: #e8f5e8;'>";
            echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
            echo "<p><strong>Created:</strong> {$order['created_at']}</p>";
            echo "<p style='color: green;'>✓ DELETED</p>";
            echo "</div>";
        }
    }
    
    // Check remaining pending orders
    echo "<h2>Remaining Pending Orders</h2>";
    
    $remainingPending = $ordersCollection->find([
        'status' => 'pending'
    ]);
    
    $remainingCount = 0;
    foreach ($remainingPending as $order) {
        $remainingCount++;
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
    
    if ($remainingCount === 0) {
        echo "<p style='color: green;'>No pending orders remaining.</p>";
    } else {
        echo "<p style='color: orange;'>Found {$remainingCount} remaining pending orders.</p>";
    }
    
    // Summary
    echo "<h2>Cleanup Summary</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>Cleanup Results:</h3>";
    echo "<ul>";
    echo "<li><strong>Expired Orders Deleted:</strong> " . count($deletedExpired) . "</li>";
    echo "<li><strong>Old Pending Orders Deleted:</strong> " . count($deletedOld) . "</li>";
    echo "<li><strong>Remaining Pending Orders:</strong> {$remainingCount}</li>";
    echo "</ul>";
    
    $totalDeleted = count($deletedExpired) + count($deletedOld);
    
    if ($totalDeleted > 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Successfully cleaned up {$totalDeleted} orders!</p>";
        echo "<p>This should free up stock that was being held by these orders.</p>";
    } else {
        echo "<p style='color: blue; font-weight: bold;'>ℹ️ No orders needed to be cleaned up.</p>";
    }
    
    if ($remainingCount > 0) {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ {$remainingCount} pending orders still remain.</p>";
        echo "<p>These are recent orders that haven't expired yet.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
?>
