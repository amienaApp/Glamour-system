<?php
// Beauty & Cosmetics Category Setup Script
// This script helps set up the beauty categories in the database

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

try {
    $categoryModel = new Category();
    
    // Beauty categories to create
    $beautyCategories = [
        [
            'name' => 'Beauty & Cosmetics',
            'slug' => 'beauty-cosmetics',
            'description' => 'Complete beauty and cosmetics collection',
            'parent_id' => null,
            'image' => '../img/category/beauty.jpg',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Makeup',
            'slug' => 'makeup',
            'description' => 'Makeup products including face, eye, lip, and nail products',
            'parent_id' => 'beauty-cosmetics',
            'image' => '../img/category/makeup.jpg',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Skincare',
            'slug' => 'skincare',
            'description' => 'Skincare products for all skin types',
            'parent_id' => 'beauty-cosmetics',
            'image' => '../img/category/skincare.jpg',
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Hair',
            'slug' => 'hair',
            'description' => 'Hair care products and tools',
            'parent_id' => 'beauty-cosmetics',
            'image' => '../img/category/hair.jpg',
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Bath & Body',
            'slug' => 'bath-body',
            'description' => 'Bath and body care products',
            'parent_id' => 'beauty-cosmetics',
            'image' => '../img/category/bath-body.jpg',
            'is_active' => true,
            'sort_order' => 4,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]
    ];
    
    // Subcategories for Makeup
    $makeupSubcategories = [
        [
            'name' => 'Face',
            'slug' => 'face',
            'description' => 'Face makeup products',
            'parent_id' => 'makeup',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Eye',
            'slug' => 'eye',
            'description' => 'Eye makeup products',
            'parent_id' => 'makeup',
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Lip',
            'slug' => 'lip',
            'description' => 'Lip makeup products',
            'parent_id' => 'makeup',
            'is_active' => true,
            'sort_order' => 3,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Nails',
            'slug' => 'nails',
            'description' => 'Nail care and polish products',
            'parent_id' => 'makeup',
            'is_active' => true,
            'sort_order' => 4,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ],
        [
            'name' => 'Tools',
            'slug' => 'tools',
            'description' => 'Makeup tools and accessories',
            'parent_id' => 'makeup',
            'is_active' => true,
            'sort_order' => 5,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]
    ];
    
    echo "<h2>Setting up Beauty & Cosmetics Categories</h2>\n";
    
    // Create main categories
    foreach ($beautyCategories as $category) {
        $existing = $categoryModel->getBySlug($category['slug']);
        if (!$existing) {
            $categoryId = $categoryModel->create($category);
            echo "<p>✅ Created category: {$category['name']} (ID: {$categoryId})</p>\n";
        } else {
            echo "<p>⚠️ Category already exists: {$category['name']}</p>\n";
        }
    }
    
    // Create makeup subcategories
    foreach ($makeupSubcategories as $subcategory) {
        $existing = $categoryModel->getBySlug($subcategory['slug']);
        if (!$existing) {
            $subcategoryId = $categoryModel->create($subcategory);
            echo "<p>✅ Created subcategory: {$subcategory['name']} (ID: {$subcategoryId})</p>\n";
        } else {
            echo "<p>⚠️ Subcategory already exists: {$subcategory['name']}</p>\n";
        }
    }
    
    echo "<h3>Setup Complete!</h3>\n";
    echo "<p>You can now access the beauty section at: <a href='beauty.php'>beauty.php</a></p>\n";
    
} catch (Exception $e) {
    echo "<p>❌ Error setting up categories: " . $e->getMessage() . "</p>\n";
}
?>



in th