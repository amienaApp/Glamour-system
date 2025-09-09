<?php
// Test MongoDB array handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test MongoDB Array Handling</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('test_categories');
    
    // Clear test collection
    $collection->deleteMany([]);
    
    // Test 1: Simple array
    echo "<h2>Test 1: Simple array</h2>";
    $testData1 = [
        'name' => 'Test Category 1',
        'subcategories' => ['Sub1', 'Sub2', 'Sub3']
    ];
    
    $result1 = $collection->insertOne($testData1);
    echo "<p>Inserted document with ID: " . $result1->getInsertedId() . "</p>";
    
    $retrieved1 = $collection->findOne(['_id' => $result1->getInsertedId()]);
    echo "<p>Retrieved subcategories: ";
    if (isset($retrieved1['subcategories'])) {
        echo "<ul>";
        foreach ($retrieved1['subcategories'] as $sub) {
            echo "<li>" . htmlspecialchars($sub) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<em>None found</em>";
    }
    echo "</p>";
    
    // Test 2: Array with MongoDB\BSON\UTCDateTime
    echo "<h2>Test 2: Array with UTCDateTime</h2>";
    $testData2 = [
        'name' => 'Test Category 2',
        'subcategories' => ['SubA', 'SubB', 'SubC'],
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $result2 = $collection->insertOne($testData2);
    echo "<p>Inserted document with ID: " . $result2->getInsertedId() . "</p>";
    
    $retrieved2 = $collection->findOne(['_id' => $result2->getInsertedId()]);
    echo "<p>Retrieved subcategories: ";
    if (isset($retrieved2['subcategories'])) {
        echo "<ul>";
        foreach ($retrieved2['subcategories'] as $sub) {
            echo "<li>" . htmlspecialchars($sub) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<em>None found</em>";
    }
    echo "</p>";
    
    // Test 3: Update existing document
    echo "<h2>Test 3: Update existing document</h2>";
    $updateResult = $collection->updateOne(
        ['_id' => $result1->getInsertedId()],
        [
            '$set' => [
                'subcategories' => ['Updated1', 'Updated2', 'Updated3'],
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]
        ]
    );
    
    echo "<p>Update result: " . $updateResult->getModifiedCount() . " documents modified</p>";
    
    $retrieved3 = $collection->findOne(['_id' => $result1->getInsertedId()]);
    echo "<p>After update - Retrieved subcategories: ";
    if (isset($retrieved3['subcategories'])) {
        echo "<ul>";
        foreach ($retrieved3['subcategories'] as $sub) {
            echo "<li>" . htmlspecialchars($sub) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<em>None found</em>";
    }
    echo "</p>";
    
    // Clean up
    $collection->deleteMany([]);
    echo "<p>✅ Test collection cleaned up</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

