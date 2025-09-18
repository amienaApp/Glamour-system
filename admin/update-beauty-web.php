<?php
/**
 * Web-accessible script to update Beauty & Cosmetics categories
 */

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

echo "<h2>Updating Beauty & Cosmetics Categories</h2>";

try {
    $categoryModel = new Category();
    
    // Get the current Beauty & Cosmetics category
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if (!$beautyCategory) {
        echo "<p style='color: red;'>Beauty & Cosmetics category not found in database.</p>";
        exit;
    }
    
    echo "<h3>Current Structure:</h3>";
    echo "<pre>" . json_encode($beautyCategory, JSON_PRETTY_PRINT) . "</pre>";
    
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
        
        // Verify the update
        $updatedCategory = $categoryModel->getByName('Beauty & Cosmetics');
        echo "<h3>Updated Structure:</h3>";
        echo "<pre>" . json_encode($updatedCategory, JSON_PRETTY_PRINT) . "</pre>";
        
        // Test sub-subcategory retrieval
        echo "<h3>Testing Sub-Subcategory Retrieval:</h3>";
        foreach ($newSubcategories as $subcategory) {
            $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategory['name']);
            echo "<p><strong>{$subcategory['name']}:</strong> " . json_encode($subSubcategories) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Failed to update Beauty & Cosmetics category structure.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>



