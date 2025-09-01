<?php
/**
 * Test MongoDB Connection
 * This file tests if MongoDB connection is working
 */

header('Content-Type: text/plain');

echo "=== Testing MongoDB Connection ===\n\n";

try {
    echo "1. Checking if MongoDB config exists...\n";
    if (file_exists(__DIR__ . '/config/mongodb.php')) {
        echo "   ✅ MongoDB config exists\n";
    } else {
        echo "   ❌ MongoDB config not found\n";
        exit;
    }

    echo "\n2. Including MongoDB config...\n";
    require_once __DIR__ . '/config/mongodb.php';
    echo "   ✅ MongoDB config included successfully\n";

    echo "\n3. Checking if MongoDB class exists...\n";
    if (class_exists('MongoDB')) {
        echo "   ✅ MongoDB class exists\n";
    } else {
        echo "   ❌ MongoDB class not found\n";
        exit;
    }

    echo "\n4. Attempting to get MongoDB instance...\n";
    $db = MongoDB::getInstance();
    echo "   ✅ MongoDB instance created successfully\n";

    echo "\n5. Testing database connection...\n";
    $collections = $db->listCollections();
    echo "   ✅ Database connection successful\n";
    echo "   Collections found: " . count($collections) . "\n";

    echo "\n6. Testing cart collection access...\n";
    $cartCollection = $db->getCollection('carts');
    echo "   ✅ Cart collection accessed successfully\n";

    echo "\n7. Testing user collection access...\n";
    $userCollection = $db->getCollection('users');
    echo "   ✅ User collection accessed successfully\n";

    echo "\n=== MongoDB Test PASSED ===\n";
    echo "MongoDB connection is working correctly!\n";

} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== MongoDB Test FAILED ===\n";
} catch (Error $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== MongoDB Test FAILED ===\n";
}
?>
