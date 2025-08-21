<?php
echo "Testing simple cart API...\n";

$_POST['action'] = 'get_cart_count';
$_SERVER['REQUEST_METHOD'] = 'POST';

ob_start();
include 'simple-cart-api.php';
$output = ob_get_clean();

echo "Output:\n";
echo $output . "\n";

$decoded = json_decode($output, true);
if ($decoded) {
    echo "✓ Valid JSON\n";
    print_r($decoded);
} else {
    echo "✗ Invalid JSON\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
?>
