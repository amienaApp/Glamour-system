<?php
/**
 * Direct database update for Beauty & Cosmetics categories
 */

require_once __DIR__ . '/../config1/mongodb.php';

try {
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Find the Beauty & Cosmetics category
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    
    if (!$beautyCategory) {
        echo "Beauty & Cosmetics category not found.\n";
        exit;
    }
    
    echo "Found Beauty & Cosmetics category with ID: " . $beautyCategory['_id'] . "\n";
    
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
    
    // Update the category
    $result = $collection->updateOne(
        ['_id' => $beautyCategory['_id']],
        [
            '$set' => [
                'subcategories' => $newSubcategories,
                'updatedAt' => new MongoDB\BSON\UTCDateTime()
            ]
        ]
    );
    
    if ($result->getModifiedCount() > 0) {
        echo "✅ Successfully updated Beauty & Cosmetics category!\n";
        
        // Verify the update
        $updatedCategory = $collection->findOne(['_id' => $beautyCategory['_id']]);
        echo "Updated structure:\n";
        echo json_encode($updatedCategory, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "❌ No changes made to the category.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>



