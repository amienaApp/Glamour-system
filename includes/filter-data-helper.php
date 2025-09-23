<?php
/**
 * Helper function to get dynamic filter data (colors, prices) for sidebars
 * This should be included in each category's main-content.php before including the sidebar
 */

function getFilterData($categoryName) {
    require_once __DIR__ . '/../config1/mongodb.php';
    require_once __DIR__ . '/../models/Product.php';
    
    $productModel = new Product();
    
    // Get all products for this category
    $filters = ['category' => $categoryName];
    $products = $productModel->getAll($filters);
    
    $colors = [];
    $prices = [];
    $hasOnSale = false;
    
    foreach ($products as $product) {
        // Collect colors
        if (isset($product['color']) && !empty($product['color'])) {
            $colors[] = $product['color'];
        }
        
        // Collect prices
        if (isset($product['price']) && is_numeric($product['price'])) {
            $prices[] = (float)$product['price'];
        }
        
        // Check for sale items
        if (isset($product['on_sale']) && $product['on_sale'] === true) {
            $hasOnSale = true;
        }
    }
    
    // Remove duplicate colors and sort
    $colors = array_unique($colors);
    sort($colors);
    
    // Calculate price ranges
    $minPrice = !empty($prices) ? min($prices) : 0;
    $maxPrice = !empty($prices) ? max($prices) : 0;
    
    // Generate price ranges based on actual data
    $priceRanges = [];
    if ($minPrice > 0 && $maxPrice > 0) {
        $range = $maxPrice - $minPrice;
        $step = max(25, round($range / 4)); // At least $25 steps
        
        $priceRanges[] = ['min' => 0, 'max' => 25, 'label' => '$0 - $25'];
        $priceRanges[] = ['min' => 25, 'max' => 50, 'label' => '$25 - $50'];
        $priceRanges[] = ['min' => 50, 'max' => 75, 'label' => '$50 - $75'];
        $priceRanges[] = ['min' => 75, 'max' => 100, 'label' => '$75 - $100'];
        $priceRanges[] = ['min' => 100, 'max' => null, 'label' => '$100+'];
    }
    
    return [
        'colors' => $colors,
        'priceRanges' => $priceRanges,
        'hasOnSale' => $hasOnSale,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'productCount' => count($products)
    ];
}
?>
