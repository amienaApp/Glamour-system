<?php
// Quick test to see what's happening
echo "Starting test...\n";

try {
    require_once 'config1/mongodb.php';
    echo "MongoDB config loaded\n";
    
    $db = MongoDB::getInstance();
    echo "MongoDB instance created\n";
    
    $collection = $db->getCollection('categories');
    echo "Collection accessed\n";
    
    $beautyCategory = $collection->findOne(['name' => 'Beauty & Cosmetics']);
    if ($beautyCategory) {
        echo "Beauty category found\n";
        echo "Subcategories count: " . count($beautyCategory['subcategories']) . "\n";
        
        // Check if first subcategory has the right structure
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
            echo "First subcategory is not array or missing name\n";
        }
    } else {
        echo "Beauty category NOT found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>

