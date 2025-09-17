<?php
/**
 * Debug Cart API
 * This file helps debug cart API issues
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Cart API Debug</h1>";

try {
    echo "<h2>1. Testing MongoDB Connection</h2>";
    require_once 'config1/mongodb.php';
    echo "✅ MongoDB config loaded successfully<br>";
    
    $db = MongoDB::getInstance();
    echo "✅ MongoDB instance created successfully<br>";
    
    $collection = $db->getCollection('carts');
    echo "✅ Cart collection accessed successfully<br>";
    
    // Test a simple query
    $testQuery = $collection->findOne([]);
    echo "✅ Test query executed successfully<br>";
    
    echo "<h2>2. Testing Cart Model</h2>";
    require_once 'models/Cart.php';
    echo "✅ Cart model loaded successfully<br>";
    
    $cartModel = new Cart();
    echo "✅ Cart model instantiated successfully<br>";
    
    echo "<h2>3. Testing Cart API Endpoints</h2>";
    
    // Test get_cart_count action
    echo "<h3>Testing get_cart_count action:</h3>";
    $testUserId = 'test_user_' . time();
    
    $cartCount = $cartModel->getCartItemCount($testUserId);
    echo "✅ getCartItemCount returned: " . $cartCount . "<br>";
    
    // Test get_cart_summary action
    echo "<h3>Testing get_cart_summary action:</h3>";
    if (method_exists($cartModel, 'getCartSummary')) {
        $summary = $cartModel->getCartSummary($testUserId);
        echo "✅ getCartSummary returned: " . json_encode($summary) . "<br>";
    } else {
        echo "⚠️ getCartSummary method not found, using fallback<br>";
        $cart = $cartModel->getCart($testUserId);
        $summary = [
            'item_count' => $cart['item_count'],
            'total' => $cart['total']
        ];
        echo "✅ Fallback summary: " . json_encode($summary) . "<br>";
    }
    
    echo "<h2>4. Testing Cart API with cURL</h2>";
    
    // Test the actual API endpoint
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/cart-api.php';
    echo "API URL: " . $apiUrl . "<br>";
    
    $postData = 'action=get_cart_count';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: " . $httpCode . "<br>";
    if ($error) {
        echo "cURL Error: " . $error . "<br>";
    } else {
        echo "Response: " . $response . "<br>";
    }
    
    echo "<h2>5. Testing Session</h2>";
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ Session started successfully<br>";
    echo "Session ID: " . session_id() . "<br>";
    
    echo "<h2>✅ All tests completed successfully!</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error occurred:</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
?>

