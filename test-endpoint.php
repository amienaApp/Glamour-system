<?php
// Test the get-subcategories.php endpoint directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test get-subcategories.php Endpoint</h1>";

// Simulate the GET request
$_GET['category'] = 'Beauty & Cosmetics';

echo "<h2>Testing with category: Beauty & Cosmetics</h2>";

// Include the get-subcategories.php file
ob_start();
include 'admin/get-subcategories.php';
$output = ob_get_clean();

echo "<p><strong>Raw output:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to decode as JSON
$jsonData = json_decode($output, true);
if ($jsonData) {
    echo "<p><strong>Decoded JSON:</strong></p>";
    echo "<pre>" . htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p><strong>JSON decode error:</strong> " . json_last_error_msg() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

