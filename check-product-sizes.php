<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking Product Sizes in Database...\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Get all women's clothing products
    $products = $productModel->getByCategory("Women's Clothing");
    
    echo "Found " . count($products) . " women's clothing products\n\n";
    
    $allSizes = [];
    $productsWithSizes = 0;
    $productsWithoutSizes = 0;
    
    foreach ($products as $product) {
        $sizes = $product['sizes'] ?? [];
        $selectedSizes = $product['selected_sizes'] ?? '';
        $sizeCategory = $product['size_category'] ?? '';
        
        if (!empty($sizes) && is_array($sizes)) {
            $productsWithSizes++;
            foreach ($sizes as $size) {
                if (!in_array($size, $allSizes)) {
                    $allSizes[] = $size;
                }
            }
        } elseif (!empty($selectedSizes)) {
            $productsWithSizes++;
            if (!in_array($selectedSizes, $allSizes)) {
                $allSizes[] = $selectedSizes;
            }
        } elseif (!empty($sizeCategory)) {
            $productsWithSizes++;
            if (!in_array($sizeCategory, $allSizes)) {
                $allSizes[] = $sizeCategory;
            }
        } else {
            $productsWithoutSizes++;
            echo "Product without sizes: " . ($product['name'] ?? 'No name') . "\n";
        }
    }
    
    echo "Products with sizes: " . $productsWithSizes . "\n";
    echo "Products without sizes: " . $productsWithoutSizes . "\n\n";
    
    echo "All available sizes found:\n";
    sort($allSizes);
    foreach ($allSizes as $size) {
        echo "  - " . $size . "\n";
    }
    
    echo "\n=== Size Analysis ===\n";
    echo "Total unique sizes: " . count($allSizes) . "\n";
    
    // Check which sizes are in the current filter options
    $currentFilterSizes = [
        'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '1X', '2X', '3X',
        '0', '2', '4', '6', '8', '10', '12', '24', '27',
        'UK XS/US 2', 'UK M Plus/US 8'
    ];
    
    echo "\nCurrent filter sizes vs Database sizes:\n";
    foreach ($currentFilterSizes as $filterSize) {
        if (in_array($filterSize, $allSizes)) {
            echo "  ✅ " . $filterSize . " - Found in database\n";
        } else {
            echo "  ❌ " . $filterSize . " - NOT found in database\n";
        }
    }
    
    echo "\nDatabase sizes NOT in current filters:\n";
    foreach ($allSizes as $dbSize) {
        if (!in_array($dbSize, $currentFilterSizes)) {
            echo "  🔍 " . $dbSize . " - Missing from filters\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
