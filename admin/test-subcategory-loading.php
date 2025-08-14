<?php
// Test the get-subcategories.php functionality
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();

echo "<h1>Subcategory Loading Test</h1>";

// Test 1: Get all categories
$categories = $categoryModel->getAll();
echo "<h2>All Categories:</h2>";
foreach ($categories as $category) {
    echo "<h3>Category: " . $category['name'] . "</h3>";
    echo "Subcategories: ";
    if (!empty($category['subcategories'])) {
        foreach ($category['subcategories'] as $sub) {
            echo "'" . $sub . "' ";
        }
    } else {
        echo "None";
    }
    echo "<br><br>";
}

// Test 2: Simulate the get-subcategories.php request
echo "<h2>Testing get-subcategories.php:</h2>";

// Test with Women's Clothing
$_GET['category'] = "Women's Clothing";
include 'get-subcategories.php';

echo "<br><br>";

// Test with Men's Clothing
$_GET['category'] = "Men's Clothing";
include 'get-subcategories.php';
?>
