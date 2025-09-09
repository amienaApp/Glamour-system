<?php
// Debug script to check category structure
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "<h2>Debug Category Structure</h2>\n";

try {
    $categoryModel = new Category();
    
    // Get Beauty & Cosmetics category
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if ($beautyCategory) {
        echo "<h3>Beauty & Cosmetics Category Found</h3>\n";
        echo "<pre>" . print_r($beautyCategory, true) . "</pre>\n";
        
        // Check subcategories
        if (isset($beautyCategory['subcategories'])) {
            echo "<h4>Subcategories Structure:</h4>\n";
            foreach ($beautyCategory['subcategories'] as $index => $sub) {
                echo "<p>Subcategory $index:</p>\n";
                echo "<pre>" . print_r($sub, true) . "</pre>\n";
            }
        }
    } else {
        echo "<p>Beauty & Cosmetics category not found!</p>\n";
        
        // List all categories
        $allCategories = $categoryModel->getAll();
        echo "<h3>All Categories:</h3>\n";
        foreach ($allCategories as $cat) {
            echo "<p>" . $cat['name'] . "</p>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Stack trace:</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>

