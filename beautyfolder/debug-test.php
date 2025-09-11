<?php
// Simple debug test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";

try {
    echo "Testing MongoDB connection...<br>";
    require_once '../config1/mongodb.php';
    echo "MongoDB config loaded<br>";
    
    $db = Database::getInstance();
    echo "Database instance created<br>";
    
    if ($db->isConnected()) {
        echo "✅ MongoDB connection successful!<br>";
    } else {
        echo "❌ MongoDB connection failed!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

try {
    echo "Testing Product model...<br>";
    require_once '../models/Product.php';
    echo "Product model loaded<br>";
    
    $productModel = new Product();
    echo "Product model instance created<br>";
    
    $products = $productModel->getByCategory("Beauty & Cosmetics");
    echo "✅ Found " . count($products) . " beauty products<br>";
    
} catch (Exception $e) {
    echo "❌ Product model error: " . $e->getMessage() . "<br>";
}
?>



