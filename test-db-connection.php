<?php
// Simple database connection test
echo "<h2>Database Connection Test</h2>\n";

try {
    require_once 'config1/mongodb.php';
    echo "<p>✅ MongoDB config loaded</p>\n";
    
    $db = MongoDB::getInstance();
    echo "<p>✅ MongoDB instance created</p>\n";
    
    $collection = $db->getCollection('categories');
    echo "<p>✅ Categories collection accessed</p>\n";
    
    $count = $collection->countDocuments();
    echo "<p>✅ Total categories: $count</p>\n";
    
    // Get Beauty & Cosmetics category
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    if ($beautyCategory) {
        echo "<p>✅ Beauty & Cosmetics category found</p>\n";
        echo "<p>Category ID: " . $beautyCategory['_id'] . "</p>\n";
        
        if (isset($beautyCategory['subcategories'])) {
            echo "<p>✅ Has subcategories</p>\n";
            echo "<p>Number of subcategories: " . count($beautyCategory['subcategories']) . "</p>\n";
            
            // Check first subcategory
            $firstSub = $beautyCategory['subcategories'][0];
            echo "<p>First subcategory type: " . gettype($firstSub) . "</p>\n";
            
            if (is_array($firstSub)) {
                echo "<p>First subcategory is array</p>\n";
                if (isset($firstSub['name'])) {
                    echo "<p>First subcategory name: " . $firstSub['name'] . "</p>\n";
                    if (isset($firstSub['sub_subcategories'])) {
                        echo "<p>✅ First subcategory has sub_subcategories</p>\n";
                    } else {
                        echo "<p>❌ First subcategory has no sub_subcategories</p>\n";
                    }
                }
            } else {
                echo "<p>First subcategory is not array: " . $firstSub . "</p>\n";
            }
        } else {
            echo "<p>❌ No subcategories found</p>\n";
        }
    } else {
        echo "<p>❌ Beauty & Cosmetics category not found</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>

