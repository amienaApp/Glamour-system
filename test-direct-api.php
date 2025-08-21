<?php
// Test direct API calls
echo "<h1>Direct API Test</h1>";

// Test cart API directly
echo "<h2>Testing Cart API Directly</h2>";

// Simulate the exact request that place-order.php makes
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'action' => 'place_order',
    'full_name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '612345678',
    'shipping_address' => '123 Test Street, Mogadishu',
    'billing_address' => '123 Test Street, Mogadishu',
    'notes' => 'Test order'
];

// Also set JSON input
$jsonInput = json_encode($_POST);
file_put_contents('php://input', $jsonInput);

echo "<p><strong>Request Data:</strong></p>";
echo "<pre>" . htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT)) . "</pre>";

// Capture output
ob_start();
include 'cart-api.php';
$output = ob_get_clean();

echo "<p><strong>Raw Output:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

if (empty($output)) {
    echo "<p style='color: red;'>❌ Empty output!</p>";
} else {
    $json = json_decode($output, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
        echo "<p><strong>First 100 characters:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($output, 0, 100)) . "</pre>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    }
}

echo "<hr>";

// Test payment API directly
echo "<h2>Testing Payment API Directly</h2>";

$_POST = [
    'action' => 'create_payment',
    'order_id' => 'test_order_123',
    'user_id' => 'demo_user_123',
    'amount' => 150.00,
    'payment_method' => 'waafi',
    'payment_details' => [
        'full_name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '612345678'
    ]
];

$jsonInput = json_encode($_POST);
file_put_contents('php://input', $jsonInput);

echo "<p><strong>Request Data:</strong></p>";
echo "<pre>" . htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT)) . "</pre>";

// Capture output
ob_start();
include 'payment-api.php';
$output = ob_get_clean();

echo "<p><strong>Raw Output:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

if (empty($output)) {
    echo "<p style='color: red;'>❌ Empty output!</p>";
} else {
    $json = json_decode($output, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
        echo "<p><strong>First 100 characters:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($output, 0, 100)) . "</pre>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    }
}
?>

