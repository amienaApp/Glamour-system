<?php
// Test script to verify sub-subcategory functionality
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "<h2>Testing Sub-Subcategory Functionality</h2>\n";

try {
    $categoryModel = new Category();
    
    // Test Beauty & Cosmetics sub-subcategories
    echo "<h3>Testing Beauty & Cosmetics Sub-Subcategories</h3>\n";
    
    $beautySubcategories = $categoryModel->getSubcategories('Beauty & Cosmetics');
    echo "<p>Beauty & Cosmetics subcategories: " . implode(', ', $beautySubcategories) . "</p>\n";
    
    foreach ($beautySubcategories as $subcategory) {
        echo "<h4>Subcategory: $subcategory</h4>\n";
        $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategory);
        
        if (is_array($subSubcategories)) {
            echo "<p>Sub-subcategories structure:</p>\n";
            echo "<pre>" . print_r($subSubcategories, true) . "</pre>\n";
            
            // Test the API endpoint
            echo "<p>Testing API endpoint...</p>\n";
            $url = "admin/get-sub-subcategories.php?category=" . urlencode('Beauty & Cosmetics') . "&subcategory=" . urlencode($subcategory);
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if ($data && $data['success']) {
                echo "<p>API Response - Sub-subcategories: " . implode(', ', $data['sub_subcategories']) . "</p>\n";
            } else {
                echo "<p>API Error: " . ($data['error'] ?? 'Unknown error') . "</p>\n";
            }
        } else {
            echo "<p>No sub-subcategories found or not an array</p>\n";
        }
        echo "<hr>\n";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
}
?>

