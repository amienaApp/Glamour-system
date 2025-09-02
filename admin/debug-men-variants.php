<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

echo "<h1>Debug: Men's Products Database Check</h1>";

// Get all men's products
$menProducts = $productModel->getByCategory("Men's Clothing");

echo "<h2>Men's Products Found: " . count($menProducts) . "</h2>";

if (empty($menProducts)) {
    echo "<p>No men's products found in database.</p>";
    echo "<p>Available categories: " . implode(', ', $productModel->getCategories()) . "</p>";
} else {
    foreach ($menProducts as $index => $product) {
        echo "<hr>";
        echo "<h3>Product " . ($index + 1) . ": " . htmlspecialchars($product['name']) . "</h3>";
        
        // Basic info
        echo "<p><strong>ID:</strong> " . $product['_id'] . "</p>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category']) . "</p>";
        echo "<p><strong>Subcategory:</strong> " . htmlspecialchars($product['subcategory']) . "</p>";
        
        // Check color variants
        if (isset($product['color_variants'])) {
            echo "<p><strong>Color Variants:</strong> " . count($product['color_variants']) . " found</p>";
            
            if (is_array($product['color_variants'])) {
                foreach ($product['color_variants'] as $vIndex => $variant) {
                    echo "<div style='margin-left: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px;'>";
                    echo "<h4>Variant " . ($vIndex + 1) . "</h4>";
                    
                    // Show all variant data
                    foreach ($variant as $key => $value) {
                        if ($key === 'front_image' || $key === 'back_image') {
                            echo "<p><strong>$key:</strong> " . htmlspecialchars($value) . "</p>";
                            
                            // Check if file exists
                            if ($value) {
                                $filePath = "../" . $value;
                                if (file_exists($filePath)) {
                                    echo "<p>✅ File exists: $filePath</p>";
                                    echo "<img src='../$value' style='max-width: 80px; max-height: 80px; border: 1px solid #ccc;'>";
                                } else {
                                    echo "<p>❌ File missing: $filePath</p>";
                                }
                            }
                        } else {
                            echo "<p><strong>$key:</strong> " . htmlspecialchars($value) . "</p>";
                        }
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>Color variants is not an array: " . gettype($product['color_variants']) . "</p>";
                echo "<p>Value: " . json_encode($product['color_variants']) . "</p>";
            }
        } else {
            echo "<p><strong>Color Variants:</strong> ❌ NOT SET</p>";
        }
        
        // Show raw data
        echo "<details>";
        echo "<summary>Raw Product Data</summary>";
        echo "<pre>" . json_encode($product, JSON_PRETTY_PRINT) . "</pre>";
        echo "</details>";
    }
}

// Check uploads directory
echo "<hr><h2>Uploads Directory Check</h2>";
$uploadsDir = "../uploads/products/";
if (is_dir($uploadsDir)) {
    echo "<p>✅ Uploads directory exists: $uploadsDir</p>";
    
    $files = scandir($uploadsDir);
    $imageFiles = array_filter($files, function($file) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif']);
    });
    
    echo "<p>Found " . count($imageFiles) . " image files in uploads directory</p>";
    
    if (count($imageFiles) > 0) {
        echo "<p>Recent image files:</p>";
        echo "<ul>";
        foreach (array_slice($imageFiles, 0, 10) as $file) {
            echo "<li>$file</li>";
        }
        if (count($imageFiles) > 10) {
            echo "<li>... and " . (count($imageFiles) - 10) . " more</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>❌ Uploads directory missing: $uploadsDir</p>";
}

// Check database connection
echo "<hr><h2>Database Connection Test</h2>";
try {
    $db = MongoDB::getInstance();
    $collection = $db->getCollection('products');
    $totalProducts = $collection->countDocuments();
    echo "<p>✅ Database connected successfully</p>";
    echo "<p>Total products in database: $totalProducts</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1, h2, h3 { color: #333; }
hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }
details { margin: 10px 0; }
summary { cursor: pointer; padding: 10px; background: #f0f0f0; border-radius: 5px; }
pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
img { border-radius: 5px; }
</style>
