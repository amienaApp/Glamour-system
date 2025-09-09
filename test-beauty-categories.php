<?php
// Test Beauty & Cosmetics categories in the database
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Beauty & Cosmetics Categories Test</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Category.php';
    
    $categoryModel = new Category();
    $categories = $categoryModel->getAll();
    
    echo "<h2>All Categories in Database:</h2>";
    foreach ($categories as $category) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<h3>" . htmlspecialchars($category['name']) . "</h3>";
        
        if (isset($category['subcategories']) && is_array($category['subcategories'])) {
            echo "<p><strong>Subcategories:</strong></p>";
            echo "<ul>";
            foreach ($category['subcategories'] as $subcategory) {
                if (is_array($subcategory) && isset($subcategory['name'])) {
                    echo "<li>" . htmlspecialchars($subcategory['name']) . "</li>";
                } else {
                    echo "<li>" . htmlspecialchars($subcategory) . "</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p><em>No subcategories</em></p>";
        }
        echo "</div>";
    }
    
    // Test the get-subcategories.php endpoint
    echo "<h2>Testing get-subcategories.php endpoint:</h2>";
    
    // Test Beauty & Cosmetics
    $url = "http://localhost/Glamour-system/admin/get-subcategories.php?category=" . urlencode("Beauty & Cosmetics");
    echo "<p><strong>Testing URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
    
    $response = file_get_contents($url);
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

