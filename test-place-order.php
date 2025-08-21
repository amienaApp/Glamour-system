<?php
// Test place order functionality
echo "<h1>Place Order Test</h1>";

// Test data
$testData = [
    'action' => 'place_order',
    'full_name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '612345678',
    'shipping_address' => '123 Test Street, Mogadishu',
    'billing_address' => '123 Test Street, Mogadishu',
    'notes' => 'Test order'
];

echo "<h2>Testing Cart API - Place Order</h2>";

// Test cart API
$cartUrl = "http://localhost/Glamour-system/cart-api.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cartUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Cart API Response (HTTP $httpCode):</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Try to decode as JSON
$json = json_decode($response, true);
if ($json === null) {
    echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
} else {
    echo "<p style='color: green;'>✅ Valid JSON</p>";
    echo "<pre>" . print_r($json, true) . "</pre>";
}

echo "<hr>";

// Test payment API
echo "<h2>Testing Payment API - Create Payment</h2>";

$paymentData = [
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

$paymentUrl = "http://localhost/Glamour-system/payment-api.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paymentUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Payment API Response (HTTP $httpCode):</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Try to decode as JSON
$json = json_decode($response, true);
if ($json === null) {
    echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
} else {
    echo "<p style='color: green;'>✅ Valid JSON</p>";
    echo "<pre>" . print_r($json, true) . "</pre>";
}
?>

