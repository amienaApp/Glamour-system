<?php
// Add subcategories to Beauty & Cosmetics category
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Add Subcategories to Beauty & Cosmetics</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find the Beauty & Cosmetics category
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    
    if ($beautyCategory) {
        echo "<p>✅ Found Beauty & Cosmetics category with ID: " . $beautyCategory['_id'] . "</p>";
        
        // Add the subcategories
        $beautySubcategories = [
            'Makeup',
            'Skincare', 
            'Hair Care',
            'Bath & Body',
            'Fragrance',
            'Tools & Accessories'
        ];
        
        echo "<h2>Adding subcategories to Beauty & Cosmetics:</h2>";
        echo "<ul>";
        foreach ($beautySubcategories as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
        
        // Update the category
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
            echo "<p>✅ Successfully updated Beauty & Cosmetics category with subcategories!</p>";
            
            // Verify the update
            $updatedCategory = $collection->findOne(['_id' => $beautyCategory['_id']]);
            if ($updatedCategory && isset($updatedCategory['subcategories'])) {
                echo "<h3>✅ Verification successful! Beauty & Cosmetics now has " . count($updatedCategory['subcategories']) . " subcategories:</h3>";
                echo "<ul>";
                foreach ($updatedCategory['subcategories'] as $subcategory) {
                    echo "<li>" . htmlspecialchars($subcategory) . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p>❌ Failed to update category</p>";
        }
        
    } else {
        echo "<p>❌ Beauty & Cosmetics category not found</p>";
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

