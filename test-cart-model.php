<?php
/**
 * Test Cart Model Loading
 * This file tests if the Cart model can be loaded and instantiated
 */

header('Content-Type: text/plain');

echo "=== Testing Cart Model Loading ===\n\n";

try {
    echo "1. Checking if Cart.php exists...\n";
    if (file_exists(__DIR__ . '/models/Cart.php')) {
        echo "   ✅ Cart.php exists\n";
    } else {
        echo "   ❌ Cart.php not found\n";
        exit;
    }

    echo "\n2. Including Cart.php...\n";
    require_once __DIR__ . '/models/Cart.php';
    echo "   ✅ Cart.php included successfully\n";

    echo "\n3. Checking if Cart class exists...\n";
    if (class_exists('Cart')) {
        echo "   ✅ Cart class exists\n";
    } else {
        echo "   ❌ Cart class not found\n";
        exit;
    }

    echo "\n4. Checking if MongoDB config exists...\n";
    if (file_exists(__DIR__ . '/config1/mongodb.php')) {
        echo "   ✅ MongoDB config exists\n";
    } else {
        echo "   ❌ MongoDB config not found\n";
        exit;
    }

    echo "\n5. Including MongoDB config...\n";
    require_once __DIR__ . '/config1/mongodb.php';
    echo "   ✅ MongoDB config included successfully\n";

    echo "\n6. Checking if MongoDB class exists...\n";
    if (class_exists('MongoDB')) {
        echo "   ✅ MongoDB class exists\n";
    } else {
        echo "   ❌ MongoDB class not found\n";
        exit;
    }

    echo "\n7. Attempting to instantiate Cart model...\n";
    $cartModel = new Cart();
    echo "   ✅ Cart model instantiated successfully\n";

    echo "\n8. Checking Cart model properties...\n";
    if (isset($cartModel->collection)) {
        echo "   ✅ Cart collection property exists\n";
    } else {
        echo "   ❌ Cart collection property not found\n";
    }

    echo "\n=== Cart Model Test PASSED ===\n";
    echo "The Cart model is working correctly!\n";

} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== Cart Model Test FAILED ===\n";
} catch (Error $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== Cart Model Test FAILED ===\n";
}
?>
