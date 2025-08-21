<?php
// Simple API test
echo "<h1>Simple API Test</h1>";

// Test cart API with minimal data
echo "<h2>Testing Cart API - get_cart_count</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Glamour-system/cart-api.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'get_cart_count']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Headers:</strong></p>";
echo "<pre>" . htmlspecialchars($headers) . "</pre>";
echo "<p><strong>Body:</strong></p>";
echo "<pre>" . htmlspecialchars($body) . "</pre>";

if (empty($body)) {
    echo "<p style='color: red;'>❌ Empty response body!</p>";
} else {
    $json = json_decode($body, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
    }
}

echo "<hr>";

// Test payment API with minimal data
echo "<h2>Testing Payment API - get_payment_methods</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Glamour-system/payment-api.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'get_payment_methods']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Headers:</strong></p>";
echo "<pre>" . htmlspecialchars($headers) . "</pre>";
echo "<p><strong>Body:</strong></p>";
echo "<pre>" . htmlspecialchars($body) . "</pre>";

if (empty($body)) {
    echo "<p style='color: red;'>❌ Empty response body!</p>";
} else {
    $json = json_decode($body, true);
    if ($json === null) {
        echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
    }
}
?>

