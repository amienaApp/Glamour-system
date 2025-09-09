<?php
// Fix Beauty & Cosmetics category structure properly
require_once 'config1/mongodb.php';
require_once 'models/Category.php';

echo "Fixing Beauty & Cosmetics structure...\n";

try {
    $categoryModel = new Category();
    
    // Delete existing Beauty & Cosmetics category
    $existingCategory = $categoryModel->getByName('Beauty & Cosmetics');
    if ($existingCategory) {
        echo "Deleting existing category...\n";
        $categoryModel->delete($existingCategory['_id']);
        echo "Deleted.\n";
    }
    
    // Create the proper structure - using simple arrays that MongoDB will handle correctly
    $beautyCategoryData = [
        'name' => "Beauty & Cosmetics",
        'subcategories' => [
            [
                'name' => 'Makeup',
                'sub_subcategories' => [
                    'Foundation', 'Concealer', 'Powder', 'Blush', 'Highlighter', 
                    'Bronzer & Contour', 'Face Primer', 'Setting Spray',
                    'Mascara', 'Eyeliner', 'Eyeshadow', 'Eyebrow Pencils/Gels', 
                    'False Lashes', 'Eye Primer',
                    'Lipstick', 'Lip Gloss', 'Lip Liner', 'Lip Stain', 'Lip Balm',
                    'Nail Polish', 'Nail Care & Treatments', 'Nail Tools',
                    'Brushes (Face, Eye, Lip)', 'Makeup Removers'
                ]
            ],
            [
                'name' => 'Skincare',
                'sub_subcategories' => [
                    'Face Moisturizer', 'Body Lotion', 'Eye Cream', 'Night Cream',
                    'Face Wash', 'Cleansing Oil', 'Micellar Water', 'Exfoliating Scrub',
                    'Face Masks', 'Sheet Masks', 'Clay Masks', 'Peel-off Masks',
                    'Serums', 'Toners', 'Essences', 'Spot Treatments',
                    'Day Cream', 'Night Cream', 'Hand Cream'
                ]
            ],
            [
                'name' => 'Hair',
                'sub_subcategories' => [
                    'Daily Shampoo', 'Clarifying Shampoo', 'Color-Safe Shampoo', 'Anti-Dandruff',
                    'Daily Conditioner', 'Deep Conditioner', 'Leave-in Conditioner', 'Hair Mask',
                    'Hair Dryer', 'Straightener', 'Curling Iron', 'Hair Brush'
                ]
            ],
            [
                'name' => 'Bath & Body',
                'sub_subcategories' => [
                    'Body Wash', 'Shower Gel', 'Shower Oil', 'Body Scrub',
                    'Face Scrub', 'Foot Scrub', 'Hand Scrub',
                    'Bar Soap', 'Liquid Soap', 'Antibacterial Soap', 'Natural Soap'
                ]
            ]
        ],
        'description' => 'Beauty products for everyone',
        'icon' => 'fa-magic'
    ];
    
    $categoryId = $categoryModel->create($beautyCategoryData);
    echo "Created new category with ID: $categoryId\n";
    
    // Test the structure
    echo "Testing structure...\n";
    $beautyCategory = $categoryModel->getByName('Beauty & Cosmetics');
    
    if ($beautyCategory && isset($beautyCategory['subcategories'])) {
        $firstSub = $beautyCategory['subcategories'][0];
        echo "First subcategory type: " . gettype($firstSub) . "\n";
        
        if (is_array($firstSub) && isset($firstSub['name'])) {
            echo "First subcategory name: " . $firstSub['name'] . "\n";
            if (isset($firstSub['sub_subcategories'])) {
                echo "Has sub_subcategories: YES\n";
                echo "Sub_subcategories count: " . count($firstSub['sub_subcategories']) . "\n";
            } else {
                echo "Has sub_subcategories: NO\n";
            }
        } else {
            echo "First subcategory structure issue\n";
        }
    }
    
    // Test the API
    echo "Testing API...\n";
    $subSubcategories = $categoryModel->getSubSubcategories('Beauty & Cosmetics', 'Makeup');
    echo "API returned " . count($subSubcategories) . " sub-subcategories for Makeup\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done.\n";
?>

