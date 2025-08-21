<?php
// Simple test to check cart API
echo "Testing cart API directly...\n";

// Include the cart API file directly
$_POST['action'] = 'get_cart_count';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Capture output
ob_start();
include 'cart-api.php';
$output = ob_get_clean();

echo "Output:\n";
echo $output . "\n";

echo "JSON test:\n";
$decoded = json_decode($output, true);
if ($decoded) {
    echo "✓ Valid JSON\n";
    print_r($decoded);
} else {
    echo "✗ Invalid JSON\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
?>
