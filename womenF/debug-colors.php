<?php
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

echo "<h1>🔍 Database Color Debug - Reading Colors from MongoDB</h1>";
echo "<style>
    .product-card { border: 1px solid #ccc; margin: 10px; padding: 15px; border-radius: 8px; }
    .color-info { background: #f5f5f5; padding: 10px; margin: 5px 0; border-radius: 4px; }
    .color-value { font-weight: bold; color: #e74c3c; }
    .null-value { color: #999; font-style: italic; }
    .success { color: #27ae60; }
    .error { color: #e74c3c; }
</style>";

try {
    $productModel = new Product();
    
    // Get products from different categories to see color data
    echo "<h2>📊 Reading Colors from Database...</h2>";
    
    // Get Women's Clothing products
    $womenProducts = $productModel->getByCategory("Women's Clothing");
    echo "<h3>👗 Women's Clothing Products (" . count($womenProducts) . " found)</h3>";
    
    if (!empty($womenProducts)) {
        foreach (array_slice($womenProducts, 0, 5) as $index => $product) {
            echo "<div class='product-card'>";
            echo "<h4>Product " . ($index + 1) . ": " . htmlspecialchars($product['name'] ?? 'No Name') . "</h4>";
            echo "<p><strong>ID:</strong> " . ($product['_id'] ?? 'No ID') . "</p>";
            
            // Check color field
            $color = $product['color'] ?? null;
            echo "<div class='color-info'>";
            echo "<strong>🎨 Color Field:</strong> ";
            if ($color !== null) {
                echo "<span class='color-value'>" . htmlspecialchars($color) . "</span>";
                echo " (Type: " . gettype($color) . ", Length: " . strlen($color) . ")";
            } else {
                echo "<span class='null-value'>NULL</span>";
            }
            echo "</div>";
            
            // Check color_variants field
            $colorVariants = $product['color_variants'] ?? null;
            echo "<div class='color-info'>";
            echo "<strong>🌈 Color Variants:</strong> ";
            if ($colorVariants !== null && is_array($colorVariants)) {
                echo "<span class='success'>Array with " . count($colorVariants) . " items</span>";
                foreach ($colorVariants as $i => $variant) {
                    echo "<br>  - Variant " . ($i + 1) . ": ";
                    if (isset($variant['color'])) {
                        echo "<span class='color-value'>" . htmlspecialchars($variant['color']) . "</span>";
                    } else {
                        echo "<span class='null-value'>No color</span>";
                    }
                    if (isset($variant['name'])) {
                        echo " (Name: " . htmlspecialchars($variant['name']) . ")";
                    }
                }
            } else {
                echo "<span class='null-value'>NULL or not array</span>";
            }
            echo "</div>";
            
            // Check other variant fields
            $variants = $product['variants'] ?? null;
            echo "<div class='color-info'>";
            echo "<strong>🔄 Variants:</strong> ";
            if ($variants !== null && is_array($variants)) {
                echo "<span class='success'>Array with " . count($variants) . " items</span>";
            } else {
                echo "<span class='null-value'>NULL or not array</span>";
            }
            echo "</div>";
            
            // Check product_variants field
            $productVariants = $product['product_variants'] ?? null;
            echo "<div class='color-info'>";
            echo "<strong>📦 Product Variants:</strong> ";
            if ($productVariants !== null && is_array($productVariants)) {
                echo "<span class='success'>Array with " . count($productVariants) . " items</span>";
            } else {
                echo "<span class='null-value'>NULL or not array</span>";
            }
            echo "</div>";
            
            // Show all available fields for this product
            echo "<div class='color-info'>";
            echo "<strong>📋 All Available Fields:</strong> ";
            $fields = array_keys($product);
            echo implode(', ', array_slice($fields, 0, 10));
            if (count($fields) > 10) {
                echo " and " . (count($fields) - 10) . " more...";
            }
            echo "</div>";
            
            echo "</div>";
        }
    } else {
        echo "<p class='error'>❌ No Women's Clothing products found!</p>";
    }
    
    // Also check Dresses specifically
    echo "<h3>👗 Dresses (" . count($productModel->getBySubcategory('Dresses')) . " found)</h3>";
    $dresses = $productModel->getBySubcategory('Dresses');
    if (!empty($dresses)) {
        $dress = $dresses[0]; // Show first dress
        echo "<div class='product-card'>";
        echo "<h4>Sample Dress: " . htmlspecialchars($dress['name'] ?? 'No Name') . "</h4>";
        echo "<p><strong>🎨 Color:</strong> ";
        if (isset($dress['color'])) {
            echo "<span class='color-value'>" . htmlspecialchars($dress['color']) . "</span>";
        } else {
            echo "<span class='null-value'>NULL</span>";
        }
        echo "</p>";
        echo "<p><strong>🌈 Color Variants:</strong> ";
        if (isset($dress['color_variants']) && is_array($dress['color_variants'])) {
            echo "<span class='success'>" . count($dress['color_variants']) . " variants</span>";
        } else {
            echo "<span class='null-value'>NULL or not array</span>";
        }
        echo "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace: " . htmlspecialchars($e->getTraceAsString()) . "</p>";
}
?>

