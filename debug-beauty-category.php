<?php
// Debug Beauty & Cosmetics category structure
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "<h2>Debug Beauty & Cosmetics Category</h2>\n";

try {
    $categoryModel = new Category();
    
    // Get Beauty & Cosmetics category
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if ($beautyCategory) {
        echo "<h3>Beauty & Cosmetics Category Structure:</h3>\n";
        echo "<pre>" . print_r($beautyCategory, true) . "</pre>\n";
        
        // Check if subcategories have the expected structure
        if (isset($beautyCategory['subcategories'])) {
            echo "<h4>Subcategories Analysis:</h4>\n";
            foreach ($beautyCategory['subcategories'] as $index => $sub) {
                echo "<p>Subcategory $index:</p>\n";
                echo "<pre>" . print_r($sub, true) . "</pre>\n";
                
                // Check if it has sub_subcategories
                if (is_array($sub) && isset($sub['sub_subcategories'])) {
                    echo "<p>✅ Has sub_subcategories</p>\n";
                } elseif (is_object($sub) && isset($sub['sub_subcategories'])) {
                    echo "<p>✅ Has sub_subcategories (object)</p>\n";
                } else {
                    echo "<p>❌ No sub_subcategories found</p>\n";
                }
            }
        }
    } else {
        echo "<p>Beauty & Cosmetics category not found!</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
}
?>