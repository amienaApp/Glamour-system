<?php
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

header('Content-Type: application/json');

$productModel = new Product();
$allMenProducts = $productModel->getByCategory("Men's Clothing");

$allColors = [];
foreach ($allMenProducts as $product) {
    if (!empty($product['color'])) {
        $allColors[] = $product['color'];
    }
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

$allColors = array_unique($allColors);
sort($allColors);

echo json_encode([
    'success' => true,
    'colors' => array_values($allColors), // Ensure keys are reset for JSON array
    'count' => count($allColors)
]);
?>