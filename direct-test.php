<?php
echo "Direct test...\n";

// Set up the environment
$_POST['action'] = 'get_cart_count';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the file directly
include 'cart-api.php';

echo "Test completed.\n";
?>
