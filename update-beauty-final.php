<?php
// Final update of Beauty & Cosmetics category
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Final Beauty & Cosmetics Category Update</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find the Beauty & Cosmetics category
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    
    if ($beautyCategory) {
        echo "<p>Found Beauty & Cosmetics category with ID: " . $beautyCategory['_id'] . "</p>";
        
        // Show current state
        echo "<p><strong>Current subcategories:</strong> ";
        if (isset($beautyCategory['subcategories']) && is_array($beautyCategory['subcategories'])) {
            echo "<ul>";
            foreach ($beautyCategory['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<em>None</em>";
        }
        echo "</p>";
        
        // Update with subcategories using the working approach
        $beautySubcategories = [
            'Makeup',
            'Skincare', 
            'Hair Care',
            'Bath & Body',
            'Fragrance',
            'Tools & Accessories'
        ];
        
        echo "<h2>Updating category with subcategories...</h2>";
        $result = $collection->updateOne(
            ['_id' => $beautyCategory['_id']],
            [
                '$set' => [
                    'subcategories' => $beautySubcategories,
                    'updated_at' => new MongoDB\BSON\UTCDateTime()
                ]
            ]
        );
        
        echo "<p>Update result: " . $result->getModifiedCount() . " documents modified</p>";
        
        // Immediately verify the update
        $updatedCategory = $collection->findOne(['_id' => $beautyCategory['_id']]);
        echo "<h2>Immediate verification:</h2>";
        echo "<p><strong>Category Name:</strong> " . htmlspecialchars($updatedCategory['name']) . "</p>";
        echo "<p><strong>Subcategories:</strong> ";
        if (isset($updatedCategory['subcategories']) && is_array($updatedCategory['subcategories'])) {
            echo "<ul>";
            foreach ($updatedCategory['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<em>None found</em>";
        }
        echo "</p>";
        
        // Test using Category model
        echo "<h2>Testing with Category model:</h2>";
        require_once 'models/Category.php';
        $categoryModel = new Category();
        $modelCategory = $categoryModel->getByName('Beauty & Cosmetics');
        
        if ($modelCategory) {
            echo "<p><strong>Category model result:</strong></p>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($modelCategory['name']) . "</p>";
            echo "<p><strong>Subcategories:</strong> ";
            if (isset($modelCategory['subcategories']) && is_array($modelCategory['subcategories'])) {
                echo "<ul>";
                foreach ($modelCategory['subcategories'] as $subcategory) {
                    echo "<li>" . htmlspecialchars($subcategory) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<em>None found</em>";
            }
            echo "</p>";
        } else {
            echo "<p><em>Category not found via model</em></p>";
        }
        
    } else {
        echo "<p>❌ Beauty & Cosmetics category not found</p>";
    }
    
    // Test the get-subcategories.php endpoint
    echo "<h2>Testing get-subcategories.php endpoint:</h2>";
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

