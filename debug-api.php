<?php
// Debug API issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>API Debug Test</h1>";

// Test cart API directly
echo "<h2>Testing Cart API Directly</h2>";
try {
    // Simulate the request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['action'] = 'get_cart_count';
    
    // Capture output
    ob_start();
    include 'cart-api.php';
    $output = ob_get_clean();
    
    echo "<p><strong>Raw Output:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Try to decode as JSON
    $json = json_decode($output, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . print_r($json, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test payment API directly
echo "<h2>Testing Payment API Directly</h2>";
try {
    // Simulate the request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['action'] = 'get_payment_methods';
    
    // Capture output
    ob_start();
    include 'payment-api.php';
    $output = ob_get_clean();
    
    echo "<p><strong>Raw Output:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Try to decode as JSON
    $json = json_decode($output, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . print_r($json, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

