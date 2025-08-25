<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking Actual Sizes in Women's Clothing Products...\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Get all women's clothing products
    $products = $productModel->getByCategory("Women's Clothing");
    
    echo "Total Women's Clothing Products: " . count($products) . "\n\n";
    
    $allSizes = [];
    $productsWithSizes = 0;
    $productsWithoutSizes = 0;
    
    foreach ($products as $product) {
        $sizes = $product['sizes'] ?? [];
        $selectedSizes = $product['selected_sizes'] ?? '';
        
        if (!empty($sizes) && is_array($sizes)) {
            $productsWithSizes++;
            foreach ($sizes as $size) {
                if (!in_array($size, $allSizes)) {
                    $allSizes[] = $size;
                }
            }
        } elseif (!empty($selectedSizes)) {
            $productsWithSizes++;
            // Parse selected_sizes string
            $sizeArray = explode(',', $selectedSizes);
            foreach ($sizeArray as $size) {
                $size = trim($size);
                if (!empty($size) && !in_array($size, $allSizes)) {
                    $allSizes[] = $size;
                }
            }
        } else {
            $productsWithoutSizes++;
            echo "Product without sizes: " . ($product['name'] ?? 'No name') . "\n";
        }
    }
    
    // Sort sizes
    sort($allSizes);
    
    echo "=== SIZE ANALYSIS ===\n";
    echo "Products with sizes: " . $productsWithSizes . "\n";
    echo "Products without sizes: " . $productsWithoutSizes . "\n";
    echo "Total unique sizes found: " . count($allSizes) . "\n\n";
    
    echo "=== ALL AVAILABLE SIZES ===\n";
    foreach ($allSizes as $size) {
        echo "- " . $size . "\n";
    }
    
    echo "\n=== SAMPLE PRODUCTS WITH SIZES ===\n";
    $sampleCount = 0;
    foreach ($products as $product) {
        if ($sampleCount >= 5) break;
        
        $sizes = $product['sizes'] ?? [];
        $selectedSizes = $product['selected_sizes'] ?? '';
        
        if (!empty($sizes) || !empty($selectedSizes)) {
            echo "Product: " . ($product['name'] ?? 'No name') . "\n";
            echo "  Subcategory: " . ($product['subcategory'] ?? 'None') . "\n";
            if (!empty($sizes)) {
                echo "  Sizes array: " . implode(', ', $sizes) . "\n";
            }
            if (!empty($selectedSizes)) {
                echo "  Selected sizes: " . $selectedSizes . "\n";
            }
            echo "\n";
            $sampleCount++;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
