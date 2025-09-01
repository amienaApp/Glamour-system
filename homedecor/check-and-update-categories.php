<?php
/**
 * Check and Update Categories Script
 * This script checks what categories exist in the database and updates them to match Category.php
 */

require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();

echo "🔍 Checking current categories in database...\n\n";

// Get all existing categories
$existingCategories = $categoryModel->getAll();

echo "📋 Current Categories in Database:\n";
echo "=====================================\n";

foreach ($existingCategories as $category) {
    echo "Category: " . $category['name'] . "\n";
    echo "Subcategories: " . implode(', ', $category['subcategories'] ?? []) . "\n";
    echo "---\n";
}

echo "\n🎯 Target Categories from Category.php:\n";
echo "========================================\n";

// Define the target categories from Category.php
$targetCategories = [
    [
        'name' => "Home & Living",
        'subcategories' => ['Bedding', 'artwork', 'Kitchen', 'living room', 'lighting', 'dinning room'],
        'description' => 'Beautiful items for your home',
        'icon' => 'fa-home'
    ],
    [
        'name' => "Women's Clothing",
        'subcategories' => ['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Activewear', 'Lingerie', 'Swimwear'],
        'description' => 'Fashionable clothing for women of all ages',
        'icon' => 'fa-female'
    ],
    [
        'name' => "Men's Clothing",
        'subcategories' => ['Shirts', 'Pants', 'Jackets', 'Activewear', 'Underwear', 'Swimwear'],
        'description' => 'Stylish clothing for men',
        'icon' => 'fa-male'
    ],
    [
        'name' => "Kids' Clothing",
        'subcategories' => ['Boys', 'Girls', 'Baby', 'Toddler'],
        'description' => 'Adorable clothing for children',
        'icon' => 'fa-child'
    ],
    [
        'name' => "Accessories",
        'subcategories' => ['Bags', 'Jewelry', 'Shoes', 'Hats', 'Scarves', 'Belts'],
        'description' => 'Complete your look with our accessories',
        'icon' => 'fa-diamond'
    ],
    [
        'name' => "Beauty & Cosmetics",
        'subcategories' => ['Skincare', 'Makeup', 'Hair Care', 'Fragrances', 'Tools'],
        'description' => 'Beauty products for everyone',
        'icon' => 'fa-magic'
    ],
    [
        'name' => "Sports & Fitness",
        'subcategories' => ['Athletic Wear', 'Sports Equipment', 'Fitness Accessories', 'Outdoor Gear'],
        'description' => 'Everything for an active lifestyle',
        'icon' => 'fa-futbol-o'
    ]
];

foreach ($targetCategories as $target) {
    echo "Category: " . $target['name'] . "\n";
    echo "Subcategories: " . implode(', ', $target['subcategories']) . "\n";
    echo "---\n";
}

echo "\n🔄 Updating categories to match Category.php...\n";
echo "==============================================\n";

$updatedCount = 0;
$createdCount = 0;

foreach ($targetCategories as $target) {
    $existing = $categoryModel->getByName($target['name']);
    
    if ($existing) {
        // Update existing category
        $updateData = [
            'subcategories' => $target['subcategories'],
            'description' => $target['description'],
            'icon' => $target['icon'],
            'updatedAt' => new MongoDB\BSON\UTCDateTime()
        ];
        
        if ($categoryModel->update($existing['_id'], $updateData)) {
            echo "✅ Updated category: " . $target['name'] . "\n";
            echo "   New subcategories: " . implode(', ', $target['subcategories']) . "\n";
            $updatedCount++;
        } else {
            echo "❌ Failed to update category: " . $target['name'] . "\n";
        }
    } else {
        // Create new category
        $categoryData = array_merge($target, [
            'createdAt' => new MongoDB\BSON\UTCDateTime(),
            'updatedAt' => new MongoDB\BSON\UTCDateTime()
        ]);
        
        $categoryId = $categoryModel->create($categoryData);
        if ($categoryId) {
            echo "✅ Created category: " . $target['name'] . "\n";
            echo "   Subcategories: " . implode(', ', $target['subcategories']) . "\n";
            $createdCount++;
        } else {
            echo "❌ Failed to create category: " . $target['name'] . "\n";
        }
    }
}

echo "\n🎉 Update Complete!\n";
echo "==================\n";
echo "Updated: $updatedCount categories\n";
echo "Created: $createdCount categories\n";
echo "\nNow when you select 'Home & Living' in the admin panel, you should see:\n";
echo "- Bedding\n";
echo "- artwork\n";
echo "- Kitchen\n";
echo "- living room\n";
echo "- lighting\n";
echo "- dinning room\n";
?>

