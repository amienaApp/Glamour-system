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

// Get all men's products
$menProducts = $productModel->getByCategory("Men's Clothing");

echo "<h2>Men's Products in Database</h2>";
echo "<p>Total men's products found: " . count($menProducts) . "</p>";

foreach ($menProducts as $product) {
    echo "<hr>";
    echo "<h3>Product: " . htmlspecialchars($product['name']) . "</h3>";
    echo "<p><strong>ID:</strong> " . $product['_id'] . "</p>";
    echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category']) . "</p>";
    echo "<p><strong>Subcategory:</strong> " . htmlspecialchars($product['subcategory']) . "</p>";
    echo "<p><strong>Price:</strong> $" . $product['price'] . "</p>";
    
    // Check main product images
    if (isset($product['front_image'])) {
        echo "<p><strong>Main Front Image:</strong> " . htmlspecialchars($product['front_image']) . "</p>";
    }
    if (isset($product['back_image'])) {
        echo "<p><strong>Main Back Image:</strong> " . htmlspecialchars($product['back_image']) . "</p>";
    }
    
    // Check color variants
    if (isset($product['color_variants']) && is_array($product['color_variants'])) {
        echo "<p><strong>Color Variants:</strong> " . count($product['color_variants']) . " found</p>";
        
        foreach ($product['color_variants'] as $index => $variant) {
            echo "<div style='margin-left: 20px; border-left: 2px solid #ccc; padding-left: 10px;'>";
            echo "<h4>Variant " . ($index + 1) . "</h4>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($variant['name']) . "</p>";
            echo "<p><strong>Color:</strong> " . htmlspecialchars($variant['color']) . "</p>";
            
            // Check variant images
            if (isset($variant['front_image'])) {
                echo "<p><strong>Front Image:</strong> " . htmlspecialchars($variant['front_image']) . "</p>";
                
                // Check if file actually exists
                $filePath = "../" . $variant['front_image'];
                if (file_exists($filePath)) {
                    echo "<p><strong>Front Image File:</strong> ✅ EXISTS</p>";
                    echo "<img src='../" . $variant['front_image'] . "' style='max-width: 100px; max-height: 100px; border: 1px solid #ccc;'>";
                } else {
                    echo "<p><strong>Front Image File:</strong> ❌ MISSING</p>";
                }
            } else {
                echo "<p><strong>Front Image:</strong> ❌ NOT SET</p>";
            }
            
            if (isset($variant['back_image'])) {
                echo "<p><strong>Back Image:</strong> " . htmlspecialchars($variant['back_image']) . "</p>";
                
                // Check if file actually exists
                $filePath = "../" . $variant['back_image'];
                if (file_exists($filePath)) {
                    echo "<p><strong>Back Image File:</strong> ✅ EXISTS</p>";
                    echo "<img src='../" . $variant['back_image'] . "' style='max-width: 100px; max-height: 100px; border: 1px solid #ccc;'>";
                } else {
                    echo "<p><strong>Back Image File:</strong> ❌ MISSING</p>";
                }
            } else {
                echo "<p><strong>Back Image:</strong> ❌ NOT SET</p>";
            }
            
            // Show other variant data
            if (isset($variant['size_category'])) {
                echo "<p><strong>Size Category:</strong> " . htmlspecialchars($variant['size_category']) . "</p>";
            }
            if (isset($variant['selected_sizes'])) {
                echo "<p><strong>Selected Sizes:</strong> " . htmlspecialchars($variant['selected_sizes']) . "</p>";
            }
            
            echo "</div>";
        }
    } else {
        echo "<p><strong>Color Variants:</strong> ❌ NONE</p>";
    }
    
    // Show complete product data for debugging
    echo "<details>";
    echo "<summary>Complete Product Data (JSON)</summary>";
    echo "<pre>" . json_encode($product, JSON_PRETTY_PRINT) . "</pre>";
    echo "</details>";
}

// Also check for any products that might have men in the name or description
echo "<hr><h2>Products with 'men' in name or description</h2>";
$allProducts = $productModel->getAll();
$menRelatedProducts = [];

foreach ($allProducts as $product) {
    $name = strtolower($product['name'] ?? '');
    $description = strtolower($product['description'] ?? '');
    $category = strtolower($product['category'] ?? '');
    
    if (strpos($name, 'men') !== false || 
        strpos($description, 'men') !== false || 
        strpos($category, 'men') !== false) {
        $menRelatedProducts[] = $product;
    }
}

echo "<p>Found " . count($menRelatedProducts) . " products related to men</p>";

foreach ($menRelatedProducts as $product) {
    if (!in_array($product, $menProducts)) { // Don't show duplicates
        echo "<hr>";
        echo "<h3>Related Product: " . htmlspecialchars($product['name']) . "</h3>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category']) . "</p>";
        
        if (isset($product['color_variants']) && is_array($product['color_variants'])) {
            echo "<p><strong>Color Variants:</strong> " . count($product['color_variants']) . " found</p>";
            
            foreach ($product['color_variants'] as $index => $variant) {
                echo "<div style='margin-left: 20px;'>";
                echo "<p><strong>Variant " . ($index + 1) . ":</strong> " . htmlspecialchars($variant['name']) . "</p>";
                
                if (isset($variant['front_image'])) {
                    echo "<p><strong>Front Image:</strong> " . htmlspecialchars($variant['front_image']) . "</p>";
                }
                if (isset($variant['back_image'])) {
                    echo "<p><strong>Back Image:</strong> " . htmlspecialchars($variant['back_image']) . "</p>";
                }
                echo "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Men's Products - Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .variant { margin: 10px 0; padding: 10px; background: #f5f5f5; }
        img { border: 1px solid #ddd; }
        details { margin: 10px 0; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Men's Products Database Check</h1>
    <p>This page shows all men's products and their color variants to check if images are properly stored.</p>
</body>
</html>
