<?php
/**
 * Debug script to check Beauty & Cosmetics categories
 * Run this in your browser: http://localhost/Glamour-system/admin/debug-beauty.php
 */

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

echo "<h2>Beauty & Cosmetics Category Debug</h2>";

try {
    $categoryModel = new Category();
    
    // Get the current Beauty & Cosmetics category
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if (!$beautyCategory) {
        echo "<p style='color: red;'>Beauty & Cosmetics category not found in database.</p>";
        exit;
    }
    
    echo "<h3>Current Database Structure:</h3>";
    echo "<pre>" . json_encode($beautyCategory, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test sub-subcategory retrieval for each subcategory
    echo "<h3>Current Sub-Subcategories:</h3>";
    $subcategories = $categoryModel->getSubcategories('Beauty & Cosmetics');
    
    foreach ($subcategories as $subcategory) {
        echo "<h4>$subcategory:</h4>";
        $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategory);
        echo "<p>" . json_encode($subSubcategories) . "</p>";
    }
    
    echo "<h3>Update Button:</h3>";
    echo "<form method='POST'>";
    echo "<input type='submit' name='update' value='Update to New Structure' style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "</form>";
    
    if (isset($_POST['update'])) {
        echo "<h3>Updating Database...</h3>";
        
        // Define the new simplified structure
        $newSubcategories = [
            [
                'name' => 'Makeup',
                'sub_subcategories' => [
                    'Face',
                    'Eye',
                    'Lip',
                    'Nail'
                ]
            ],
            [
                'name' => 'Skincare',
                'sub_subcategories' => [
                    'Moisturizers',
                    'Cleansers',
                    'Masks',
                    'Sun Care',
                    'cream'
                ]
            ],
            [
                'name' => 'Hair',
                'sub_subcategories' => [
                    'Shampoo',
                    'Conditioner',
                    'Tools'
                ]
            ],
            [
                'name' => 'Bath & Body',
                'sub_subcategories' => [
                    'Shower gel',
                    'Scrubs',
                    'soap'
                ]
            ]
        ];
        
        // Update the category with new structure
        $updateResult = $categoryModel->update($beautyCategory['_id'], [
            'subcategories' => $newSubcategories,
            'updatedAt' => new MongoDB\BSON\UTCDateTime()
        ]);
        
        if ($updateResult) {
            echo "<p style='color: green;'>✅ Successfully updated Beauty & Cosmetics category structure!</p>";
            
            // Refresh the page to show updated structure
            echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
            
        } else {
            echo "<p style='color: red;'>❌ Failed to update Beauty & Cosmetics category structure.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>



