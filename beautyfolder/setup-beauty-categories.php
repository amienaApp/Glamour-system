<?php
// Beauty & Cosmetics Category Setup Script
// Fixed version based on the original setup-categories.php

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

try {
    $categoryModel = new Category();
    
    // First, remove any existing Beauty & Cosmetics categories
    echo "<h2>Removing existing Beauty & Cosmetics categories...</h2>";
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    $deleteResult = $collection->deleteMany(['name' => 'Beauty & Cosmetics']);
    echo "<p>Deleted " . $deleteResult->getDeletedCount() . " existing Beauty & Cosmetics categories</p>";
    
    // Create the main Beauty & Cosmetics category with subcategories
    echo "<h2>Creating Beauty & Cosmetics category with subcategories...</h2>";
    
    $beautyCategory = [
        'name' => 'Beauty & Cosmetics',
        'slug' => 'beauty-cosmetics',
        'description' => 'Complete beauty and cosmetics collection',
        'subcategories' => [
            'Makeup',
            'Skincare',
            'Hair',
            'Bath & Body'
        ],
        'is_active' => true,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $insertResult = $collection->insertOne($beautyCategory);
    $categoryId = $insertResult->getInsertedId();
    
    echo "<p>✅ Created Beauty & Cosmetics category with ID: " . $categoryId . "</p>";
    echo "<p>✅ Subcategories: Makeup, Skincare, Hair, Bath & Body</p>";
    
    // Verify the category was created correctly
    $createdCategory = $collection->findOne(['_id' => $categoryId]);
    if ($createdCategory) {
        echo "<h3>✅ Verification successful!</h3>";
        echo "<p><strong>Category Name:</strong> " . htmlspecialchars($createdCategory['name']) . "</p>";
        echo "<p><strong>Subcategories:</strong></p>";
        echo "<ul>";
        foreach ($createdCategory['subcategories'] as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    }
    
    // Test the get-subcategories.php endpoint
    echo "<h2>Testing get-subcategories.php endpoint:</h2>";
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['category'] = 'Beauty & Cosmetics';
    
    ob_start();
    include __DIR__ . '/../admin/get-subcategories.php';
    $output = ob_get_clean();
    
    echo "<p><strong>Endpoint response:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    $json = json_decode($output, true);
    if ($json && $json['success'] && isset($json['subcategories'])) {
        echo "<h3>✅ SUCCESS! Endpoint working:</h3>";
        echo "<ul>";
        foreach ($json['subcategories'] as $subcategory) {
            echo "<li>" . htmlspecialchars($subcategory) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>❌ Endpoint not working: " . ($json['message'] ?? 'Unknown error') . "</h3>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now test the admin panel at: <a href='../admin/add-product.php'>admin/add-product.php</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error setting up categories: " . $e->getMessage() . "</p>";
}
?>

