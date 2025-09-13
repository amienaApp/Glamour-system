<?php
/**
 * Script to update Beauty & Cosmetics categories with deeper sub-subcategory structure
 * This script will add deeper sub-subcategories specifically for makeup items
 */

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

echo "<h2>Updating Beauty & Cosmetics with Deeper Sub-Subcategories</h2>";

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
    
    // Define the new structure with deeper sub-subcategories for makeup
    $newSubcategories = [
        [
            'name' => 'Makeup',
            'sub_subcategories' => [
                'Face',
                'Eye', 
                'Lip',
                'Nails'
            ],
            'deeper_sub_subcategories' => [
                'Face' => [
                    'Foundation',
                    'Concealer',
                    'Powder',
                    'Blush',
                    'Highlighter',
                    'Bronzer & Contour',
                    'Face Primer',
                    'Setting Spray'
                ],
                'Eye' => [
                    'Mascara',
                    'Eyeliner',
                    'Eyeshadow',
                    'Eyebrow Pencils/Gels',
                    'False Lashes',
                    'Eye Primer'
                ],
                'Lip' => [
                    'Lipstick',
                    'Lip Gloss',
                    'Lip Liner',
                    'Lip Stain',
                    'Lip Balm'
                ],
                'Nails' => [
                    'Nail Polish',
                    'Nail Care & Treatments',
                    'Nail Tools'
                ]
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
            'name' => 'Hair Care',
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
        ],
        [
            'name' => 'Beauty Tools',
            'sub_subcategories' => [
                'Makeup Brushes',
                'Beauty Sponges',
                'Tweezers',
                'Mirrors',
                'Hair Tools',
                'Skincare Tools'
            ]
        ]
    ];
    
    // Update the category with new structure
    $updateResult = $categoryModel->update($beautyCategory['_id'], [
        'subcategories' => $newSubcategories,
        'updatedAt' => new MongoDB\BSON\UTCDateTime()
    ]);
    
    if ($updateResult) {
        echo "<p style='color: green;'>✅ Successfully updated Beauty & Cosmetics category with deeper sub-subcategories!</p>";
        
        // Show the updated structure
        $updatedCategory = $categoryModel->getByName('Beauty & Cosmetics');
        echo "<h3>Updated Structure:</h3>";
        echo "<pre>" . json_encode($updatedCategory, JSON_PRETTY_PRINT) . "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Failed to update Beauty & Cosmetics category structure.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
