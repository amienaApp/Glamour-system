<?php
/**
 * Setup script for Home Decor categories
 * This script adds the "Home & Living" category and its subcategories to the database
 * Uses EXACT subcategory names from the Category.php model
 */

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();

       // Define the Home & Living category with subcategories - EXACT names from Category.php
       $homeDecorCategory = [
           'name' => 'Home & Living',
           'description' => 'Beautiful home decor and living essentials',
           'subcategories' => [
               'Bedding',
               'Bath',
               'Kitchen',
               'Decor',
               'Furniture'
           ],
           'createdAt' => new MongoDB\BSON\UTCDateTime(),
           'updatedAt' => new MongoDB\BSON\UTCDateTime()
       ];

try {
    // Check if category already exists
    $existingCategory = $categoryModel->getByName('Home & Living');
    
    if ($existingCategory) {
        echo "✅ Category 'Home & Living' already exists!\n";
        echo "Subcategories: " . implode(', ', $existingCategory['subcategories'] ?? []) . "\n";
    } else {
        // Create the category
        $categoryId = $categoryModel->create($homeDecorCategory);
        if ($categoryId) {
            echo "✅ Successfully created 'Home & Living' category!\n";
            echo "Category ID: " . $categoryId . "\n";
            echo "Subcategories: " . implode(', ', $homeDecorCategory['subcategories']) . "\n";
        } else {
            echo "❌ Failed to create category\n";
        }
    }
    
    // Also try alternative category names
    $alternativeCategories = ['Home Decor', 'Home and Living', 'Home'];
    
    foreach ($alternativeCategories as $altCategory) {
        $existing = $categoryModel->getByName($altCategory);
        if (!$existing) {
            $altCategoryData = [
                'name' => $altCategory,
                'description' => 'Home decor and living essentials',
                'subcategories' => [
                    'Bedding',
                    'Bath',
                    'Kitchen',
                    'Decor',
                    'Furniture'
                ],
                'createdAt' => new MongoDB\BSON\UTCDateTime(),
                'updatedAt' => new MongoDB\BSON\UTCDateTime()
            ];
            
            $altId = $categoryModel->create($altCategoryData);
            if ($altId) {
                echo "✅ Created alternative category: '$altCategory'\n";
            }
        } else {
            echo "ℹ️  Alternative category '$altCategory' already exists\n";
        }
    }
    
    echo "\n🎉 Home decor categories setup complete!\n";
    echo "You can now add products with category 'Home & Living' from the admin panel.\n";
    echo "Subcategories available: " . implode(', ', $homeDecorCategory['subcategories']) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
