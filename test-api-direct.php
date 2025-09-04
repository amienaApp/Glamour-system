<?php
/**
 * Direct API Test
 * Test the get-product-details.php endpoint directly
 */

// Test with a known product ID
$productId = '68b5eadb91a66fb16a0d9254';

echo "<h1>Testing API Directly</h1>";
echo "<p>Product ID: $productId</p>";

// Test the API endpoint
$apiUrl = "get-product-details.php?product_id=" . $productId;
echo "<p>API URL: <a href='$apiUrl' target='_blank'>$apiUrl</a></p>";

// Make the API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Glamour-system/$apiUrl");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h2>API Response</h2>";
echo "<p>HTTP Code: $httpCode</p>";

if ($response) {
    echo "<h3>Raw Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<h3>Parsed JSON:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
        if (isset($data['success']) && $data['success']) {
            echo "<h3>✅ API Working!</h3>";
            echo "<p>Product: " . ($data['product']['name'] ?? 'Unknown') . "</p>";
            echo "<p>Colors found: " . count($data['product']['colors'] ?? []) . "</p>";
            echo "<p>Images found: " . count($data['product']['images'] ?? []) . "</p>";
        } else {
            echo "<h3>❌ API Error</h3>";
            echo "<p>Message: " . ($data['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<h3>❌ JSON Parse Error</h3>";
        echo "<p>Response is not valid JSON</p>";
    }
} else {
    echo "<h3>❌ No Response</h3>";
    echo "<p>Failed to get response from API</p>";
}

// Test file existence
echo "<h2>File Check</h2>";
$apiFile = "get-product-details.php";
if (file_exists($apiFile)) {
    echo "<p>✅ $apiFile exists</p>";
} else {
    echo "<p>❌ $apiFile not found</p>";
}

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    require_once 'config1/mongodb.php';
    echo "<p>✅ MongoDB config loaded</p>";
    
    require_once 'models/Product.php';
    echo "<p>✅ Product model loaded</p>";
    
    $productModel = new Product();
    echo "<p>✅ Product model instantiated</p>";
    
    $product = $productModel->getById($productId);
    if ($product) {
        echo "<p>✅ Product found in database</p>";
        echo "<p>Product name: " . ($product['name'] ?? 'Unknown') . "</p>";
    } else {
        echo "<p>❌ Product not found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>

