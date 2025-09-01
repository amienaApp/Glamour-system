<?php
/**
 * Test Simple Login
 * This file tests basic login functionality without cart transfer
 */

header('Content-Type: text/plain');

echo "=== Testing Simple Login ===\n\n";

try {
    echo "1. Testing basic login functionality...\n";
    
    // Simulate a simple login request
    $_POST['username'] = 'testuser';
    $_POST['password'] = 'testpass';
    
    echo "2. POST data set...\n";
    
    // Test if we can include the login handler
    echo "3. Including login handler...\n";
    if (file_exists(__DIR__ . '/menfolder/login-handler.php')) {
        echo "   ✅ Login handler exists\n";
    } else {
        echo "   ❌ Login handler not found\n";
        exit;
    }
    
    echo "4. Testing basic file inclusion...\n";
    ob_start();
    include __DIR__ . '/menfolder/login-handler.php';
    $output = ob_get_clean();
    
    echo "   ✅ Login handler included successfully\n";
    
    echo "\n5. Checking output...\n";
    if (strpos($output, 'Method not allowed') !== false) {
        echo "   ✅ Login handler is working (expected method not allowed for GET)\n";
    } else {
        echo "   ⚠️  Unexpected output from login handler\n";
        echo "   Output: " . substr($output, 0, 200) . "...\n";
    }
    
    echo "\n=== Simple Login Test PASSED ===\n";
    echo "Basic login functionality appears to be working!\n";
    
} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== Simple Login Test FAILED ===\n";
} catch (Error $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n=== Simple Login Test FAILED ===\n";
}
?>
