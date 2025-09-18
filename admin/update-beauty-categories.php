<?php
/**
 * Script to update Beauty & Cosmetics categories with new simplified sub-subcategory structure
 * This script will update the existing database to match the new structure
 */

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
        echo "✅ Successfully updated Beauty & Cosmetics category structure!\n\n";
        
        // Verify the update
        $updatedCategory = $categoryModel->getByName('Beauty & Cosmetics');
        echo "Updated Beauty & Cosmetics category structure:\n";
        echo json_encode($updatedCategory, JSON_PRETTY_PRINT) . "\n";
        
        // Test sub-subcategory retrieval
        echo "\nTesting sub-subcategory retrieval:\n";
        foreach ($newSubcategories as $subcategory) {
            $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategory['name']);
            echo "Subcategory '{$subcategory['name']}': " . json_encode($subSubcategories) . "\n";
        }
        
    } else {
        echo "❌ Failed to update Beauty & Cosmetics category structure.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>



