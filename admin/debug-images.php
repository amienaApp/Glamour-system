<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

// Get a few sample products
$products = $productModel->getAll([], [], 5);

echo "<h1>Image Path Debug</h1>";
echo "<p>Checking image paths for sample products...</p>";

foreach ($products as $product) {
    echo "<hr>";
    echo "<h3>Product: " . htmlspecialchars($product['name']) . "</h3>";
    echo "<p><strong>ID:</strong> " . $product['_id'] . "</p>";
    
    // Check front image
    if (isset($product['front_image'])) {
        $frontPath = $product['front_image'];
        $fullFrontPath = '../uploads/products/' . basename($frontPath);
        $fileExists = file_exists($fullFrontPath);
        
        echo "<p><strong>Front Image:</strong></p>";
        echo "<ul>";
        echo "<li>Database Path: " . htmlspecialchars($frontPath) . "</li>";
        echo "<li>Full Path: " . htmlspecialchars($fullFrontPath) . "</li>";
        echo "<li>File Exists: " . ($fileExists ? 'YES' : 'NO') . "</li>";
        if ($fileExists) {
            echo "<li>File Size: " . filesize($fullFrontPath) . " bytes</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><strong>Front Image:</strong> Not set</p>";
    }
    
    // Check back image
    if (isset($product['back_image'])) {
        $backPath = $product['back_image'];
        $fullBackPath = '../uploads/products/' . basename($backPath);
        $fileExists = file_exists($fullBackPath);
        
        echo "<p><strong>Back Image:</strong></p>";
        echo "<ul>";
        echo "<li>Database Path: " . htmlspecialchars($backPath) . "</li>";
        echo "<li>Full Path: " . htmlspecialchars($fullBackPath) . "</li>";
        echo "<li>File Exists: " . ($fileExists ? 'YES' : 'NO') . "</li>";
        if ($fileExists) {
            echo "<li>File Size: " . filesize($fullBackPath) . " bytes</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><strong>Back Image:</strong> Not set</p>";
    }
    
    // Check color variants
    if (isset($product['color_variants']) && !empty($product['color_variants'])) {
        echo "<p><strong>Color Variants:</strong></p>";
        foreach ($product['color_variants'] as $index => $variant) {
            echo "<p>Variant " . $index . ":</p>";
            echo "<ul>";
            
            if (isset($variant['front_image'])) {
                $variantFrontPath = $variant['front_image'];
                $fullVariantFrontPath = '../uploads/products/' . basename($variantFrontPath);
                $fileExists = file_exists($fullVariantFrontPath);
                
                echo "<li>Front Image: " . htmlspecialchars($variantFrontPath) . " (Exists: " . ($fileExists ? 'YES' : 'NO') . ")</li>";
            }
            
            if (isset($variant['back_image'])) {
                $variantBackPath = $variant['back_image'];
                $fullVariantBackPath = '../uploads/products/' . basename($variantBackPath);
                $fileExists = file_exists($fullVariantBackPath);
                
                echo "<li>Back Image: " . htmlspecialchars($variantBackPath) . " (Exists: " . ($fileExists ? 'YES' : 'NO') . ")</li>";
            }
            
            echo "</ul>";
        }
    } else {
        echo "<p><strong>Color Variants:</strong> None</p>";
    }
}

// Check uploads directory
echo "<hr>";
echo "<h2>Uploads Directory Check</h2>";
$uploadsDir = '../uploads/products/';
echo "<p><strong>Uploads Directory:</strong> " . realpath($uploadsDir) . "</p>";

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp|avif)$/i', $file);
    });
    
    echo "<p><strong>Total Image Files:</strong> " . count($imageFiles) . "</p>";
    echo "<p><strong>Sample Files:</strong></p>";
    echo "<ul>";
    $sampleFiles = array_slice($imageFiles, 0, 10);
    foreach ($sampleFiles as $file) {
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
    if (count($imageFiles) > 10) {
        echo "<li>... and " . (count($imageFiles) - 10) . " more</li>";
    }
    echo "</ul>";
} else {
    echo "<p><strong>Error:</strong> Uploads directory not found!</p>";
}

echo "<hr>";
echo "<p><a href='view-products.php'>Back to View Products</a></p>";
?>
