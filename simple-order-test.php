<?php
echo "Simple order test...\n";

// Test basic file inclusion
if (file_exists('models/Order.php')) {
    echo "✓ Order.php exists\n";
} else {
    echo "✗ Order.php missing\n";
}

if (file_exists('models/Product.php')) {
    echo "✓ Product.php exists\n";
} else {
    echo "✗ Product.php missing\n";
}

if (file_exists('config/database.php')) {
    echo "✓ database.php exists\n";
} else {
    echo "✗ database.php missing\n";
}

echo "Test completed.\n";
?>
