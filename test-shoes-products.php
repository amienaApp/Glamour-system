<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SHOES PRODUCTS ANALYSIS ===\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Get all shoes products
    echo "1. Getting all Shoes products...\n";
    $allProducts = $productModel->getByCategory("Shoes");
    echo "   Total products: " . count($allProducts) . "\n\n";
    
    if (count($allProducts) === 0) {
        echo "❌ No shoes products found in database!\n";
        echo "   Please add some shoes products first.\n";
        exit;
    }
    
    // Analyze products
    $categories = [];
    $sizes = [];
    $colors = [];
    $priceRanges = [];
    $productsWithSizes = 0;
    $productsWithoutSizes = 0;
    
    foreach ($allProducts as $product) {
        // Categories
        $category = $product['subcategory'] ?? 'Unknown';
        if (!isset($categories[$category])) {
            $categories[$category] = 0;
        }
        $categories[$category]++;
        
        // Sizes
        $productSizes = $product['sizes'] ?? [];
        $selectedSizes = $product['selected_sizes'] ?? '';
        
        if (!empty($productSizes)) {
            $productsWithSizes++;
            if (is_array($productSizes)) {
                foreach ($productSizes as $size) {
                    if (!in_array($size, $sizes)) {
                        $sizes[] = $size;
                    }
                }
            } elseif (is_string($productSizes)) {
                $parsedSizes = json_decode($productSizes, true);
                if (is_array($parsedSizes)) {
                    foreach ($parsedSizes as $size) {
                        if (!in_array($size, $sizes)) {
                            $sizes[] = $size;
                        }
                    }
                }
            }
        } elseif (!empty($selectedSizes)) {
            $productsWithSizes++;
            if (is_string($selectedSizes)) {
                $parsedSizes = json_decode($selectedSizes, true);
                if (is_array($parsedSizes)) {
                    foreach ($parsedSizes as $size) {
                        if (!in_array($size, $sizes)) {
                            $sizes[] = $size;
                        }
                    }
                }
            }
        } else {
            $productsWithoutSizes++;
        }
        
        // Colors
        $color = $product['color'] ?? '';
        if (!empty($color) && !in_array($color, $colors)) {
            $colors[] = $color;
        }
        
        // Price ranges
        $price = $product['price'] ?? 0;
        if ($price >= 0 && $price <= 25) {
            $priceRanges['0-25'] = ($priceRanges['0-25'] ?? 0) + 1;
        } elseif ($price > 25 && $price <= 50) {
            $priceRanges['25-50'] = ($priceRanges['25-50'] ?? 0) + 1;
        } elseif ($price > 50 && $price <= 100) {
            $priceRanges['50-100'] = ($priceRanges['50-100'] ?? 0) + 1;
        } elseif ($price > 100 && $price <= 200) {
            $priceRanges['100-200'] = ($priceRanges['100-200'] ?? 0) + 1;
        } elseif ($price > 200) {
            $priceRanges['200+'] = ($priceRanges['200+'] ?? 0) + 1;
        }
    }
    
    // Display results
    echo "2. Category Analysis:\n";
    foreach ($categories as $category => $count) {
        echo "   $category: $count products\n";
    }
    echo "\n";
    
    echo "3. Size Analysis:\n";
    echo "   Products with sizes: $productsWithSizes\n";
    echo "   Products without sizes: $productsWithoutSizes\n";
    echo "   Available sizes: " . implode(', ', $sizes) . "\n\n";
    
    echo "4. Color Analysis:\n";
    echo "   Available colors: " . implode(', ', $colors) . "\n\n";
    
    echo "5. Price Range Analysis:\n";
    foreach ($priceRanges as $range => $count) {
        echo "   $range: $count products\n";
    }
    echo "\n";
    
    echo "6. Sample Products:\n";
    foreach (array_slice($allProducts, 0, 5) as $product) {
        echo "   - " . ($product['name'] ?? 'No name') . "\n";
        echo "     Category: " . ($product['subcategory'] ?? 'Unknown') . "\n";
        echo "     Price: $" . ($product['price'] ?? 0) . "\n";
        echo "     Color: " . ($product['color'] ?? 'Unknown') . "\n";
        echo "     Sizes: " . ($product['selected_sizes'] ?? 'None') . "\n\n";
    }
    
    echo "✅ Shoes analysis completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
