<?php
echo "Testing database connection...\n";

try {
    require_once 'config/database.php';
    echo "✓ Database config loaded\n";
    
    $db = Database::getInstance();
    echo "✓ Database instance created\n";
    
    $collection = $db->getCollection('orders');
    echo "✓ Orders collection accessed\n";
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>
