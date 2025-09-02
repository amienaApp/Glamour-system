<?php
require_once 'vendor/autoload.php';

echo "<h1>�� Exporting Database to Cloud</h1>";

// Source database (your friend's database)
$sourceConnectionString = 'mongodb://192.168.100.168:27017';
$sourceDatabase = 'glamour_system';

// Destination database (your cloud database)
$destinationConnectionString = 'mongodb+srv://fmoha187_db_user:amina1144@cluster0.dnw6lj0.mongodb.net/glamour_system';

try {
    // Connect to source database (friend's)
    echo "<p>🔗 Connecting to source database...</p>";
    $sourceClient = new MongoDB\Client($sourceConnectionString);
    $sourceDb = $sourceClient->selectDatabase($sourceDatabase);
    
    // Connect to destination database (cloud)
    echo "<p>☁️ Connecting to cloud database...</p>";
    $destClient = new MongoDB\Client($destinationConnectionString);
    $destDb = $destClient->selectDatabase('glamour_system');
    
    // Collections to export
    $collections = ['products', 'users', 'categories', 'orders', 'carts', 'payments', 'admins'];
    
    foreach ($collections as $collectionName) {
        echo "<p>📤 Exporting $collectionName...</p>";
        
        try {
            $sourceCollection = $sourceDb->selectCollection($collectionName);
            $destCollection = $destDb->selectCollection($collectionName);
            
            // Get all documents
            $documents = $sourceCollection->find([]);
            $count = 0;
            
            foreach ($documents as $document) {
                // Remove _id to avoid conflicts
                unset($document['_id']);
                $destCollection->insertOne($document);
                $count++;
            }
            
            echo "<p>✅ $collectionName: $count documents exported</p>";
            
        } catch (Exception $e) {
            echo "<p>⚠️ $collectionName: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>�� Export Complete!</h2>";
    echo "<p>All data has been transferred to your cloud database.</p>";
    echo "<p><a href='test-connection.php'>Test Connection</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Export Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>