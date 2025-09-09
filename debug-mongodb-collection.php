<?php
// Debug MongoDB collection directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug MongoDB Collection Directly</h1>";

try {
    require_once 'config1/mongodb.php';
    
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('categories');
    
    // Get all documents in the collection
    echo "<h2>All documents in categories collection:</h2>";
    $allDocs = $collection->find([]);
    $docCount = 0;
    foreach ($allDocs as $doc) {
        $docCount++;
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<p><strong>Document #$docCount:</strong></p>";
        echo "<p><strong>ID:</strong> " . $doc['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($doc['name'] ?? 'NULL') . "</p>";
        echo "<p><strong>Raw document:</strong></p>";
        echo "<pre>" . htmlspecialchars(json_encode($doc, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    }
    
    // Try to find Beauty & Cosmetics specifically
    echo "<h2>Beauty & Cosmetics documents specifically:</h2>";
    $beautyDocs = $collection->find(['name' => 'Beauty & Cosmetics']);
    $beautyCount = 0;
    foreach ($beautyDocs as $doc) {
        $beautyCount++;
        echo "<div style='border: 2px solid red; padding: 10px; margin: 5px;'>";
        echo "<p><strong>Beauty Document #$beautyCount:</strong></p>";
        echo "<p><strong>ID:</strong> " . $doc['_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($doc['name'] ?? 'NULL') . "</p>";
        echo "<p><strong>Has subcategories field:</strong> " . (isset($doc['subcategories']) ? 'YES' : 'NO') . "</p>";
        if (isset($doc['subcategories'])) {
            echo "<p><strong>Subcategories type:</strong> " . gettype($doc['subcategories']) . "</p>";
            echo "<p><strong>Subcategories value:</strong> " . htmlspecialchars(json_encode($doc['subcategories'])) . "</p>";
        }
        echo "<p><strong>Raw document:</strong></p>";
        echo "<pre>" . htmlspecialchars(json_encode($doc, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    }
    
    if ($beautyCount === 0) {
        echo "<p><em>No Beauty & Cosmetics documents found</em></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

