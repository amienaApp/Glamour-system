<?php
// Initialize categories with proper structure
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "<h2>Initializing Categories</h2>\n";

try {
    $categoryModel = new Category();
    
    // Initialize default categories (this includes Beauty & Cosmetics with proper structure)
    $result = $categoryModel->initializeDefaultCategories();
    
    echo "<h3>Initialization Results:</h3>\n";
    echo "<p>Added: " . $result['added'] . " categories</p>\n";
    echo "<p>Existing: " . $result['existing'] . " categories</p>\n";
    echo "<p>Total: " . $result['total'] . " categories</p>\n";
    
    // Test Beauty & Cosmetics sub-subcategories
    echo "<h3>Testing Beauty & Cosmetics Sub-Subcategories:</h3>\n";
    
    $beautySubcategories = $categoryModel->getSubcategories('Beauty & Cosmetics');
    echo "<p>Beauty & Cosmetics subcategories: " . implode(', ', $beautySubcategories) . "</p>\n";
    
    foreach ($beautySubcategories as $subcategory) {
        echo "<h4>Subcategory: $subcategory</h4>\n";
        $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategory);
        
        if (is_array($subSubcategories) && !empty($subSubcategories)) {
            echo "<p>Sub-subcategories found: " . count($subSubcategories) . "</p>\n";
            echo "<pre>" . print_r($subSubcategories, true) . "</pre>\n";
        } else {
            echo "<p>No sub-subcategories found</p>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Stack trace:</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>

