<?php
// Simple test to check database connection and categories
require_once 'config1/mongodb.php';

echo "<h2>Simple Database Test</h2>\n";

try {
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Count total categories
    $count = $collection->countDocuments();
    echo "<p>Total categories in database: $count</p>\n";
    
    // Get all categories
    $categories = $collection->find()->toArray();
    
    echo "<h3>All Categories:</h3>\n";
    foreach ($categories as $category) {
        echo "<p><strong>" . $category['name'] . "</strong></p>\n";
        if (isset($category['subcategories'])) {
            echo "<p>Subcategories: " . count($category['subcategories']) . "</p>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
}
?>