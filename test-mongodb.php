<?php
/**
 * MongoDB Connection Test
 * This script tests if MongoDB is properly configured and connected
 */

echo "<h2>MongoDB Connection Test</h2>";

// Test 1: Check if MongoDB extension is loaded
echo "<h3>1. Checking MongoDB Extension</h3>";
if (extension_loaded('mongodb')) {
    echo "✅ MongoDB extension is loaded successfully<br>";
} else {
    echo "❌ MongoDB extension is NOT loaded<br>";
    echo "You need to install the MongoDB PHP extension<br>";
    exit;
}

// Test 2: Check if vendor autoload exists
echo "<h3>2. Checking Vendor Autoload</h3>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ Vendor autoload file exists<br>";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "❌ Vendor autoload file not found<br>";
    exit;
}

// Test 3: Check if MongoDB Client class exists
echo "<h3>3. Checking MongoDB Client Class</h3>";
if (class_exists('MongoDB\Client')) {
    echo "✅ MongoDB Client class is available<br>";
} else {
    echo "❌ MongoDB Client class not found<br>";
    exit;
}

// Test 4: Try to connect to MongoDB
echo "<h3>4. Testing MongoDB Connection</h3>";
try {
    $client = new MongoDB\Client('mongodb://localhost:27017');
    echo "✅ MongoDB client created successfully<br>";
    
    // Test database connection
    $database = $client->selectDatabase('glamour_system');
    echo "✅ Database 'glamour_system' selected<br>";
    
    // Test ping
    $result = $database->command(['ping' => 1]);
    echo "✅ MongoDB server responded to ping<br>";
    
    // List databases
    $databases = $client->listDatabases();
    echo "✅ Available databases:<br>";
    foreach ($databases as $db) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;• " . $db->getName() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "<br>";
    echo "<br><strong>Troubleshooting:</strong><br>";
    echo "1. Make sure MongoDB server is running<br>";
    echo "2. Check if MongoDB is running on port 27017<br>";
    echo "3. Verify MongoDB service is started<br>";
}

// Test 5: Test your custom MongoDB class
echo "<h3>5. Testing Custom MongoDB Class</h3>";
try {
    require_once __DIR__ . '/config/mongodb.php';
    $mongo = MongoDB::getInstance();
    
    if ($mongo->isConnected()) {
        echo "✅ Custom MongoDB class connection successful<br>";
        
        // Get database stats
        $stats = $mongo->getStats();
        if ($stats) {
            echo "✅ Database stats retrieved successfully<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Database: " . $mongo->getDatabaseName() . "<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Collections: " . $stats['collections'] . "<br>";
        }
    } else {
        echo "❌ Custom MongoDB class connection failed<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Custom MongoDB class error: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Test Complete!</h3>";
?>
