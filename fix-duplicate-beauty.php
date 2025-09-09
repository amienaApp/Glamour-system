<?php
// Fix duplicate Beauty & Cosmetics categories
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fix Duplicate Beauty & Cosmetics Categories</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find all Beauty & Cosmetics categories
    echo "<h2>Finding all Beauty & Cosmetics categories:</h2>";
    $beautyCategories = $collection->find(['name' => 'Beauty & Cosmetics']);
    $count = 0;
    $categoriesWithSubcategories = [];
    $categoriesWithoutSubcategories = [];
    
    foreach ($beautyCategories as $category) {
        $count++;
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<p><strong>Category #$count:</strong></p>";
        echo "<p><strong>ID:</strong> " . $category['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($category['name']) . "</p>";
        
        if (isset($category['subcategories']) && is_array($category['subcategories']) && count($category['subcategories']) > 0) {
            echo "<p><strong>✅ HAS subcategories:</strong> " . count($category['subcategories']) . "</p>";
            echo "<ul>";
            foreach ($category['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
            $categoriesWithSubcategories[] = $category;
        } else {
            echo "<p><strong>❌ NO subcategories</strong></p>";
            $categoriesWithoutSubcategories[] = $category;
        }
        echo "</div>";
    }
    
    echo "<p><strong>Total Beauty & Cosmetics categories found:</strong> $count</p>";
    echo "<p><strong>Categories with subcategories:</strong> " . count($categoriesWithSubcategories) . "</p>";
    echo "<p><strong>Categories without subcategories:</strong> " . count($categoriesWithoutSubcategories) . "</p>";
    
    // Keep the one with subcategories, delete the others
    if (count($categoriesWithSubcategories) > 0) {
        echo "<h2>Keeping the category with subcategories and removing duplicates:</h2>";
        
        // Delete all Beauty & Cosmetics categories first
        $deleteResult = $collection->deleteMany(['name' => 'Beauty & Cosmetics']);
        echo "<p>Deleted " . $deleteResult->getDeletedCount() . " Beauty & Cosmetics categories</p>";
        
        // Re-insert the one with subcategories
        $goodCategory = $categoriesWithSubcategories[0];
        $insertResult = $collection->insertOne($goodCategory);
        echo "<p>✅ Re-inserted Beauty & Cosmetics category with subcategories (ID: " . $insertResult->getInsertedId() . ")</p>";
        
        // Verify
        $verifyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
        if ($verifyCategory && isset($verifyCategory['subcategories'])) {
            echo "<h3>✅ Verification successful! Beauty & Cosmetics now has " . count($verifyCategory['subcategories']) . " subcategories:</h3>";
            echo "<ul>";
            foreach ($verifyCategory['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<p>❌ No Beauty & Cosmetics category with subcategories found!</p>";
    }
    
    // Test the get-subcategories.php endpoint
    echo "<h2>Testing get-subcategories.php endpoint:</h2>";
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['category'] = 'Beauty & Cosmetics';
    
    ob_start();
    include 'admin/get-subcategories.php';
    $output = ob_get_clean();
    
    echo "<p><strong>Endpoint response:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    $json = json_decode($output, true);
    if ($json && $json['success'] && isset($json['subcategories'])) {
        echo "<h3>✅ SUCCESS! Endpoint now returns subcategories:</h3>";
        echo "<ul>";
        foreach ($json['subcategories'] as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>❌ Endpoint still not working: " . ($json['message'] ?? 'Unknown error') . "</h3>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

