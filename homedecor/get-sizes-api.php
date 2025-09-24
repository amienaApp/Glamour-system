<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once '../config1/mongodb.php';
require_once '../models/Product.php';

try {
    $productModel = new Product();
    
    // Get all home decor products
    $allHomeDecorProducts = $productModel->getByCategory('Home & Living');
    
    // Define home decor subcategory sizes based on admin panel structure
    $homeDecorSubcategorySizes = [
        'bedding' => ['Single', 'Double', 'Queen', 'King', 'Super King'],
        'living room' => ['Small', 'Medium', 'Large', 'Extra Large', 'Sectional'],
        'dinning room' => ['2 Seater', '4 Seater', '6 Seater', '8 Seater', '10 Seater'],
        'kitchen' => ['Compact', 'Standard', 'Large', 'Commercial'],
        'artwork' => ['8x10', '11x14', '16x20', '18x24', '24x36', '30x40'],
        'lightinning' => ['Small', 'Medium', 'Large', 'Extra Large']
    ];
    
    // Extract actual sizes from products that have them in the database
    $databaseSizes = [];
    foreach ($allHomeDecorProducts as $product) {
        // Get sizes from main sizes field
        if (!empty($product['sizes'])) {
            $sizes = is_string($product['sizes']) ? 
                json_decode($product['sizes'], true) : $product['sizes'];
            if (is_array($sizes)) {
                foreach ($sizes as $size) {
                    if (!empty($size)) {
                        $databaseSizes[] = $size;
                    }
                }
            }
        }
        
        // Get sizes from selected_sizes field
        if (!empty($product['selected_sizes'])) {
            $selectedSizes = is_string($product['selected_sizes']) ? 
                json_decode($product['selected_sizes'], true) : $product['selected_sizes'];
            if (is_array($selectedSizes)) {
                foreach ($selectedSizes as $size) {
                    if (!empty($size)) {
                        $databaseSizes[] = $size;
                    }
                }
            }
        }
    }
    
    // Get all unique sizes from database
    $databaseSizes = array_unique($databaseSizes);
    
    // If we have database sizes, use them. Otherwise, use subcategory-specific sizes
    if (!empty($databaseSizes)) {
        $allSizes = $databaseSizes;
    } else {
        // Combine all subcategory sizes as fallback
        $allSizes = [];
        foreach ($homeDecorSubcategorySizes as $subcategory => $sizes) {
            $allSizes = array_merge($allSizes, $sizes);
        }
        $allSizes = array_unique($allSizes);
    }
    
    sort($allSizes);
    
    echo json_encode([
        'success' => true,
        'sizes' => $allSizes,
        'count' => count($allSizes),
        'database_sizes' => $databaseSizes,
        'subcategory_sizes' => $homeDecorSubcategorySizes
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
