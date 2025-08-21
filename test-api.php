<?php
// Test API endpoints
echo "<h1>API Test</h1>";

// Test cart API
echo "<h2>Testing Cart API</h2>";
$cartUrl = "http://localhost/Glamour-system/cart-api.php";
$cartData = ['action' => 'get_cart_count'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cartUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($cartData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Cart API Response (HTTP $httpCode):</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Test payment API
echo "<h2>Testing Payment API</h2>";
$paymentUrl = "http://localhost/Glamour-system/payment-api.php";
$paymentData = ['action' => 'get_payment_methods'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paymentUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Payment API Response (HTTP $httpCode):</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Test database connection
echo "<h2>Testing Database Connection</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>

