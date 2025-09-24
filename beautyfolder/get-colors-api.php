<?php
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

header('Content-Type: application/json');

try {
    $productModel = new Product();
    
    // Get all beauty products
    $allBeautyProducts = $productModel->getByCategory('Beauty & Cosmetics');
    
    $allColors = [];
    
    foreach ($allBeautyProducts as $product) {
        // Get color from main color field
        if (!empty($product['color'])) {
            $allColors[] = $product['color'];
        }

        // Get colors from color_variants
        if (!empty($product['color_variants'])) {
            $colorVariants = is_string($product['color_variants']) ?
                json_decode($product['color_variants'], true) : $product['color_variants'];

            if (is_array($colorVariants)) {
                foreach ($colorVariants as $variant) {
                    if (!empty($variant['color'])) {
                        $allColors[] = $variant['color'];
                    }
                }
            }
        }
    }
    
    // Remove duplicates and sort colors
    $allColors = array_unique($allColors);
    sort($allColors);
    
    echo json_encode([
        'success' => true,
        'colors' => $allColors,
        'count' => count($allColors)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
