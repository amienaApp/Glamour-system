<?php
// Basic API test - just check if files exist and can be accessed
echo "<h1>Basic API Test</h1>";

// Check if files exist
echo "<h2>File Existence Check</h2>";
$files = ['cart-api.php', 'payment-api.php', 'config/database.php', 'models/Cart.php', 'models/Order.php', 'models/Payment.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

echo "<hr>";

// Test basic PHP syntax
echo "<h2>PHP Syntax Check</h2>";
$syntaxFiles = ['cart-api.php', 'payment-api.php', 'models/Cart.php', 'models/Order.php', 'models/Payment.php'];

foreach ($syntaxFiles as $file) {
    $output = shell_exec("C:\\xampp\\php\\php.exe -l $file 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<p style='color: green;'>✅ $file - No syntax errors</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - Syntax errors found</p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
}

echo "<hr>";

// Test basic include
echo "<h2>Basic Include Test</h2>";
try {
    require_once 'config/database.php';
    echo "<p style='color: green;'>✅ Database config loaded</p>";
    
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database instance created</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test direct API access
echo "<h2>Direct API Access Test</h2>";

// Test cart-api.php directly
echo "<h3>Cart API Direct Test</h3>";
try {
    // Simulate a simple request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['action'] = 'get_cart_count';
    
    ob_start();
    include 'cart-api.php';
    $output = ob_get_clean();
    
    echo "<p><strong>Output:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    if (empty($output)) {
        echo "<p style='color: red;'>❌ Empty output from cart-api.php</p>";
    } else {
        $json = json_decode($output, true);
        if ($json === null) {
            echo "<p style='color: red;'>❌ Not valid JSON: " . json_last_error_msg() . "</p>";
        } else {
            echo "<p style='color: green;'>✅ Valid JSON from cart-api.php</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Cart API error: " . $e->getMessage() . "</p>";
}
?>

