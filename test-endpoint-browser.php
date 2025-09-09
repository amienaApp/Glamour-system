<?php
// Test the endpoint as it would be called from the browser
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Endpoint as Browser Would Call It</h1>";

// Simulate browser request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['category'] = 'Beauty & Cosmetics';

echo "<p>Testing: GET request with category = " . $_GET['category'] . "</p>";

// Test the endpoint
$url = "http://localhost/Glamour-system/admin/get-subcategories.php?category=" . urlencode("Beauty & Cosmetics");
echo "<p><strong>URL:</strong> <a href='$url' target='_blank'>$url</a></p>";

// Try to get the response
$response = @file_get_contents($url);
if ($response === false) {
    echo "<p>❌ Failed to get response from URL</p>";
} else {
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $json = json_decode($response, true);
    if ($json) {
        echo "<p><strong>Decoded JSON:</strong></p>";
        echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
        
        if ($json['success'] && isset($json['subcategories'])) {
            echo "<h2>✅ SUCCESS! Subcategories found:</h2>";
            echo "<ul>";
            foreach ($json['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>❌ FAILED: " . ($json['message'] ?? 'Unknown error') . "</h2>";
        }
    } else {
        echo "<p>❌ JSON decode failed: " . json_last_error_msg() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

