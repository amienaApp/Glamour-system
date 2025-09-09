<?php
// Test the get-subcategories.php endpoint properly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test get-subcategories.php Endpoint Properly</h1>";

// Simulate proper environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['category'] = 'Beauty & Cosmetics';

echo "<p>Testing with category: " . $_GET['category'] . "</p>";

// Test the Category model directly first
echo "<h2>Testing Category Model Directly:</h2>";
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

$categoryModel = new Category();
$beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');

if ($beautyCategory) {
    echo "<p>✅ Found Beauty & Cosmetics category</p>";
    if (isset($beautyCategory['subcategories']) && is_array($beautyCategory['subcategories'])) {
        echo "<p>✅ Category has " . count($beautyCategory['subcategories']) . " subcategories:</p>";
        echo "<ul>";
        foreach ($beautyCategory['subcategories'] as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Category has no subcategories</p>";
    }
} else {
    echo "<p>❌ Beauty & Cosmetics category not found</p>";
}

// Now test the endpoint
echo "<h2>Testing get-subcategories.php endpoint:</h2>";

// Capture output without headers
ob_start();
include 'admin/get-subcategories.php';
$output = ob_get_clean();

echo "<h3>Raw Output:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to decode JSON
$json = json_decode($output, true);
if ($json) {
    echo "<h3>Decoded JSON:</h3>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    
    if ($json['success'] && isset($json['subcategories'])) {
        echo "<h3>✅ SUCCESS! Subcategories found:</h3>";
        echo "<ul>";
        foreach ($json['subcategories'] as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>❌ FAILED: " . ($json['message'] ?? 'Unknown error') . "</h3>";
    }
} else {
    echo "<p>❌ JSON decode failed: " . json_last_error_msg() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

