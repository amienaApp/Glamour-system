<?php
// Simple test of get-subcategories.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Test of get-subcategories.php</h1>";

// Set the category parameter
$_GET['category'] = 'Beauty & Cosmetics';

echo "<p>Testing with category: " . $_GET['category'] . "</p>";

// Capture output
ob_start();
include 'admin/get-subcategories.php';
$output = ob_get_clean();

echo "<h2>Output:</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to decode JSON
$json = json_decode($output, true);
if ($json) {
    echo "<h2>Decoded JSON:</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p>JSON decode failed: " . json_last_error_msg() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

