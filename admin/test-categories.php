<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Beauty & Cosmetics categories...\n\n";

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

try {
    $categoryModel = new Category();
    
    // Get the current Beauty & Cosmetics category
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if (!$beautyCategory) {
        echo "Beauty & Cosmetics category not found in database.\n";
        exit;
    }
    
    echo "Current Beauty & Cosmetics category structure:\n";
    echo json_encode($beautyCategory, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test sub-subcategory retrieval for Makeup
    echo "Testing Makeup sub-subcategories:\n";
    $makeupSubSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', 'Makeup');
    echo json_encode($makeupSubSubcategories) . "\n\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>



