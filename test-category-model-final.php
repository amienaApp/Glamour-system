<?php
// Test Category model to see why it's not finding subcategories
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Category Model Final</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Category.php';
    
    $categoryModel = new Category();
    
    // Test getByName method
    echo "<h2>Testing getByName method:</h2>";
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
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
    
    // Test getAll method
    echo "<h2>Testing getAll method:</h2>";
    $allCategories = $categoryModel->getAll();
    $beautyCategories = array_filter($allCategories, function($cat) {
        return $cat['name'] === 'Beauty & Cosmetics';
    });
    
    echo "<p><strong>Beauty & Cosmetics categories found via getAll:</strong> " . count($beautyCategories) . "</p>";
    
    foreach ($beautyCategories as $category) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<p><strong>ID:</strong> " . $category['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($category['name']) . "</p>";
        echo "<p><strong>Has subcategories field:</strong> " . (isset($category['subcategories']) ? 'YES' : 'NO') . "</p>";
        
        if (isset($category['subcategories'])) {
            echo "<p><strong>Subcategories count:</strong> " . count($category['subcategories']) . "</p>";
            echo "<p><strong>Subcategories:</strong></p>";
            echo "<ul>";
            foreach ($category['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }
    
    // Test the getSubcategories method
    echo "<h2>Testing getSubcategories method:</h2>";
    $subcategories = $categoryModel->getSubcategories('Beauty & Cosmetics');
    echo "<p><strong>getSubcategories result:</strong> " . count($subcategories) . " subcategories</p>";
    if (!empty($subcategories)) {
        echo "<ul>";
        foreach ($subcategories as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

