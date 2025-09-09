<?php
// Fix Beauty & Cosmetics category structure
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "<h2>Fixing Beauty & Cosmetics Category Structure</h2>\n";

try {
    $categoryModel = new Category();
    
    // First, let's delete the existing Beauty & Cosmetics category
    $existingCategory = $categoryModel->getByName('Beauty & Cosmetics');
    if ($existingCategory) {
        echo "<p>Found existing Beauty & Cosmetics category, deleting it...</p>\n";
        $categoryModel->delete($existingCategory['_id']);
        echo "<p>✅ Deleted existing category</p>\n";
    }
    
    // Now create the proper Beauty & Cosmetics category with nested structure
    $beautyCategoryData = [
        'name' => "Beauty & Cosmetics",
        'subcategories' => [
            [
                'name' => 'Makeup',
                'sub_subcategories' => [
                    'Face' => ['Foundation', 'Concealer', 'Powder', 'Blush', 'Highlighter', 'Bronzer & Contour', 'Face Primer', 'Setting Spray'],
                    'Eye' => ['Mascara', 'Eyeliner', 'Eyeshadow', 'Eyebrow Pencils/Gels', 'False Lashes', 'Eye Primer'],
                    'Lip' => ['Lipstick', 'Lip Gloss', 'Lip Liner', 'Lip Stain', 'Lip Balm'],
                    'Nails' => ['Nail Polish', 'Nail Care & Treatments', 'Nail Tools'],
                    'Tools' => ['Brushes (Face, Eye, Lip)', 'Makeup Removers']
                ]
            ],
            [
                'name' => 'Skincare',
                'sub_subcategories' => [
                    'Moisturizers' => ['Face Moisturizer', 'Body Lotion', 'Eye Cream', 'Night Cream'],
                    'Cleansers' => ['Face Wash', 'Cleansing Oil', 'Micellar Water', 'Exfoliating Scrub'],
                    'Masks' => ['Face Masks', 'Sheet Masks', 'Clay Masks', 'Peel-off Masks'],
                    'Call Who' => ['Serums', 'Toners', 'Essences', 'Spot Treatments'],
                    'cream' => ['Day Cream', 'Night Cream', 'Eye Cream', 'Hand Cream']
                ]
            ],
            [
                'name' => 'Hair',
                'sub_subcategories' => [
                    'Shampoo' => ['Daily Shampoo', 'Clarifying Shampoo', 'Color-Safe Shampoo', 'Anti-Dandruff'],
                    'Conditioner' => ['Daily Conditioner', 'Deep Conditioner', 'Leave-in Conditioner', 'Hair Mask'],
                    'Tools' => ['Hair Dryer', 'Straightener', 'Curling Iron', 'Hair Brush']
                ]
            ],
            [
                'name' => 'Bath & Body',
                'sub_subcategories' => [
                    'Shower gel' => ['Body Wash', 'Shower Gel', 'Shower Oil', 'Body Scrub'],
                    'Scrubs' => ['Body Scrub', 'Face Scrub', 'Foot Scrub', 'Hand Scrub'],
                    'soap' => ['Bar Soap', 'Liquid Soap', 'Antibacterial Soap', 'Natural Soap']
                ]
            ]
        ],
        'description' => 'Beauty products for everyone',
        'icon' => 'fa-magic'
    ];
    
    $categoryId = $categoryModel->create($beautyCategoryData);
    echo "<p>✅ Created new Beauty & Cosmetics category with ID: $categoryId</p>\n";
    
    // Test the sub-subcategories
    echo "<h3>Testing Sub-Subcategories:</h3>\n";
    
    $beautySubcategories = $categoryModel->getSubcategories('Beauty & Cosmetics');
    $subcategoryNames = [];
    foreach ($beautySubcategories as $sub) {
        if (is_string($sub)) {
            $subcategoryNames[] = $sub;
        } elseif (is_array($sub) && isset($sub['name'])) {
            $subcategoryNames[] = $sub['name'];
        } elseif (is_object($sub) && isset($sub['name'])) {
            $subcategoryNames[] = $sub['name'];
        }
    }
    echo "<p>Beauty & Cosmetics subcategories: " . implode(', ', $subcategoryNames) . "</p>\n";
    
    foreach ($beautySubcategories as $subcategory) {
        $subcategoryName = is_string($subcategory) ? $subcategory : (is_array($subcategory) ? $subcategory['name'] : $subcategory['name']);
        echo "<h4>Subcategory: $subcategoryName</h4>\n";
        $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', $subcategoryName);
        
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