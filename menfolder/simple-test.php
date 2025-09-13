<?php
// Simple test to check if everything is working
echo "PHP is working!<br>";

// Check if we can include the MongoDB config
$configPath = '../config/mongodb.php';
echo "Config path: " . $configPath . "<br>";
echo "Config exists: " . (file_exists($configPath) ? 'YES' : 'NO') . "<br>";

if (file_exists($configPath)) {
    try {
        require_once $configPath;
        echo "MongoDB config loaded successfully<br>";
        
        if (class_exists('MongoDB')) {
            echo "MongoDB class exists<br>";
            
            try {
                $db = MongoDB::getInstance();
                echo "MongoDB instance created<br>";
                
                if ($db->isConnected()) {
                    echo "MongoDB is connected!<br>";
                } else {
                    echo "MongoDB connection failed<br>";
                }
            } catch (Exception $e) {
                echo "Error creating MongoDB instance: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "MongoDB class not found<br>";
        }
    } catch (Exception $e) {
        echo "Error loading MongoDB config: " . $e->getMessage() . "<br>";
    }
}

// Check if we can include the Product model
$modelPath = '../models/Product.php';
echo "Model path: " . $modelPath . "<br>";
echo "Model exists: " . (file_exists($modelPath) ? 'YES' : 'NO') . "<br>";

if (file_exists($modelPath)) {
    try {
        require_once $modelPath;
        echo "Product model loaded successfully<br>";
        
        if (class_exists('Product')) {
            echo "Product class exists<br>";
            
            try {
                $productModel = new Product();
                echo "Product model instance created<br>";
                
                $products = $productModel->getByCategory("Men's Clothing");
                echo "Found " . count($products) . " products in Men's Clothing<br>";
                
                if (!empty($products)) {
                    echo "Sample product: " . json_encode($products[0]) . "<br>";
                }
            } catch (Exception $e) {
                echo "Error creating Product model: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "Product class not found<br>";
        }
    } catch (Exception $e) {
        echo "Error loading Product model: " . $e->getMessage() . "<br>";
    }
}

echo "<br>Test completed!";
?>
