<?php
// Debug category retrieval directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Category Retrieval Directly</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find Beauty & Cosmetics directly
    echo "<h2>Direct MongoDB query for Beauty & Cosmetics:</h2>";
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    
    if ($beautyCategory) {
        echo "<p>✅ Found Beauty & Cosmetics category</p>";
        echo "<p><strong>ID:</strong> " . $beautyCategory['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($beautyCategory['name']) . "</p>";
        echo "<p><strong>Has subcategories field:</strong> " . (isset($beautyCategory['subcategories']) ? 'YES' : 'NO') . "</p>";
        
        if (isset($beautyCategory['subcategories'])) {
            echo "<p><strong>Subcategories type:</strong> " . gettype($beautyCategory['subcategories']) . "</p>";
            echo "<p><strong>Subcategories count:</strong> " . (is_array($beautyCategory['subcategories']) ? count($beautyCategory['subcategories']) : 'Not an array') . "</p>";
            echo "<p><strong>Subcategories value:</strong></p>";
            echo "<pre>" . htmlspecialchars(json_encode($beautyCategory['subcategories'], JSON_PRETTY_PRINT)) . "</pre>";
        }
    } else {
        echo "<p>❌ Beauty & Cosmetics category not found</p>";
    }
    
    // Test Category model
    echo "<h2>Testing Category model:</h2>";
    require_once 'models/Category.php';
    $categoryModel = new Category();
    $modelCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if ($modelCategory) {
        echo "<p>✅ Category model found Beauty & Cosmetics</p>";
        echo "<p><strong>ID:</strong> " . $modelCategory['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($modelCategory['name']) . "</p>";
        echo "<p><strong>Has subcategories field:</strong> " . (isset($modelCategory['subcategories']) ? 'YES' : 'NO') . "</p>";
        
        if (isset($modelCategory['subcategories'])) {
            echo "<p><strong>Subcategories type:</strong> " . gettype($modelCategory['subcategories']) . "</p>";
            echo "<p><strong>Subcategories count:</strong> " . (is_array($modelCategory['subcategories']) ? count($modelCategory['subcategories']) : 'Not an array') . "</p>";
            echo "<p><strong>Subcategories value:</strong></p>";
            echo "<pre>" . htmlspecialchars(json_encode($modelCategory['subcategories'], JSON_PRETTY_PRINT)) . "</pre>";
        }
    } else {
        echo "<p>❌ Category model did not find Beauty & Cosmetics</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

