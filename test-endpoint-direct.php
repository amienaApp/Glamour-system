<?php
// Test the get-subcategories.php endpoint directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test get-subcategories.php Endpoint</h1>";

// Set the category parameter
$_GET['category'] = 'Beauty & Cosmetics';

echo "<p>Testing with category: " . $_GET['category'] . "</p>";

// Capture output
ob_start();
include 'admin/get-subcategories.php';
$output = ob_get_clean();

echo "<h2>Raw Output:</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to decode JSON
$json = json_decode($output, true);
if ($json) {
    echo "<h2>Decoded JSON:</h2>";
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

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

