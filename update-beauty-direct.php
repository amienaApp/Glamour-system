<?php
// Direct update of Beauty & Cosmetics category
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Beauty & Cosmetics Category Update</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find the Beauty & Cosmetics category
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    
    if ($beautyCategory) {
        echo "<p>Found Beauty & Cosmetics category with ID: " . $beautyCategory['_id'] . "</p>";
        
        // Update with subcategories
        $beautySubcategories = [
            'Makeup',
            'Skincare', 
            'Hair Care',
            'Bath & Body',
            'Fragrance',
            'Tools & Accessories'
        ];
        
        $result = $collection->updateOne(
            ['_id' => $beautyCategory['_id']],
            [
                '$set' => [
                    'subcategories' => $beautySubcategories,
                    'updated_at' => new MongoDB\BSON\UTCDateTime()
                ]
            ]
        );
        
        if ($result->getModifiedCount() > 0) {
            echo "<p>✅ Successfully updated Beauty & Cosmetics category!</p>";
            echo "<p>Subcategories added:</p>";
            echo "<ul>";
            foreach ($beautySubcategories as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ No changes made to the category</p>";
        }
        
        // Verify the update
        $updatedCategory = $collection->findOne(['_id' => $beautyCategory['_id']]);
        echo "<h2>Verification:</h2>";
        echo "<p><strong>Category Name:</strong> " . htmlspecialchars($updatedCategory['name']) . "</p>";
        if (isset($updatedCategory['subcategories']) && is_array($updatedCategory['subcategories'])) {
            echo "<p><strong>Subcategories:</strong></p>";
            echo "<ul>";
            foreach ($updatedCategory['subcategories'] as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p><em>No subcategories found</em></p>";
        }
        
    } else {
        echo "<p>❌ Beauty & Cosmetics category not found</p>";
        
        // List all categories to see what we have
        echo "<h2>All categories in database:</h2>";
        $allCategories = $collection->find([]);
        foreach ($allCategories as $category) {
            echo "<p>" . htmlspecialchars($category['name']) . "</p>";
        }
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

