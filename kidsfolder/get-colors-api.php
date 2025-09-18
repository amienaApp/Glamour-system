<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

try {
    $productModel = new Product();
    
    // Get subcategory from URL parameter
    $subcategory = $_GET['subcategory'] ?? '';
    
    // Get products based on subcategory or all beauty products
    if ($subcategory) {
        $products = $productModel->getBySubcategory($subcategory);
    } else {
        $products = $productModel->getByCategory("Beauty & Cosmetics");
    }
    
    $colors = [];
    $colorCounts = [];
    
    // Extract colors from products
    foreach ($products as $product) {
        // Get main color
        if (!empty($product['color'])) {
            $color = $product['color'];
            if (!isset($colorCounts[$color])) {
                $colorCounts[$color] = 0;
            }
            $colorCounts[$color]++;
        }
        
        // Get colors from color variants
        if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
            foreach ($product['color_variants'] as $variant) {
                // Handle different variant structures
                if (is_array($variant)) {
                    // Standard array structure
                    if (!empty($variant['color'])) {
                        $color = trim($variant['color']);
                        if (!isset($colorCounts[$color])) {
                            $colorCounts[$color] = 0;
                        }
                        $colorCounts[$color]++;
                    }
                } elseif (is_string($variant)) {
                    // Direct string color
                    $color = trim($variant);
                    if (!isset($colorCounts[$color])) {
                        $colorCounts[$color] = 0;
                    }
                    $colorCounts[$color]++;
                }
            }
        }
    }
    
    // Sort colors by count (most common first)
    arsort($colorCounts);
    
    // Create color objects with name and count
    // Only include colors that have at least one product
    foreach ($colorCounts as $color => $count) {
        // Skip colors with no products
        if ($count <= 0) {
            continue;
        }
        
        $colors[] = [
            'color' => $color,
            'count' => $count,
            'name' => getColorName($color)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'colors' => $colors,
        'total_colors' => count($colors)
    ]);
    
} catch (Exception $e) {
    error_log('Error getting colors: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving colors',
        'error' => $e->getMessage()
    ]);
}

function getColorName($colorCode) {
    // Professional beauty industry color names
    $colorMap = [
        '#ff0000' => 'Classic Red',
        '#ff69b4' => 'Rose Pink',
        '#ff1493' => 'Fuchsia',
        '#ffc0cb' => 'Blush Pink',
        '#ffb6c1' => 'Light Pink',
        '#ff1493' => 'Hot Pink',
        '#800080' => 'Royal Purple',
        '#9932cc' => 'Amethyst',
        '#9370db' => 'Lavender',
        '#8a2be2' => 'Violet',
        '#0000ff' => 'Navy Blue',
        '#4169e1' => 'Royal Blue',
        '#87ceeb' => 'Sky Blue',
        '#008000' => 'Forest Green',
        '#32cd32' => 'Emerald',
        '#90ee90' => 'Mint Green',
        '#ffff00' => 'Sunshine Yellow',
        '#ffd700' => 'Champagne Gold',
        '#daa520' => 'Antique Gold',
        '#ffa500' => 'Coral Orange',
        '#ff8c00' => 'Burnt Orange',
        '#ff6347' => 'Terracotta',
        '#8b4513' => 'Espresso',
        '#a0522d' => 'Sienna',
        '#d2691e' => 'Cocoa',
        '#cd853f' => 'Peru',
        '#000000' => 'Jet Black',
        '#2f2f2f' => 'Charcoal',
        '#696969' => 'Dark Gray',
        '#808080' => 'Silver',
        '#c0c0c0' => 'Platinum',
        '#ffffff' => 'Pearl White',
        '#f5f5dc' => 'Ivory',
        '#f5deb3' => 'Nude',
        '#d2b48c' => 'Beige',
        '#f0e68c' => 'Khaki',
        '#cd7f32' => 'Bronze',
        '#b87333' => 'Copper',
        '#dc143c' => 'Crimson',
        '#b22222' => 'Fire Brick',
        '#ff4500' => 'Vermillion',
        '#ff7f50' => 'Coral',
        '#ff69b4' => 'Rose',
        '#ff1493' => 'Deep Pink',
        '#ffb6c1' => 'Light Rose',
        '#ffc0cb' => 'Pink',
        '#ff69b4' => 'Hot Pink',
        '#ff1493' => 'Magenta',
        '#800080' => 'Purple',
        '#9932cc' => 'Dark Orchid',
        '#9370db' => 'Medium Purple',
        '#8a2be2' => 'Blue Violet',
        '#4b0082' => 'Indigo',
        '#0000ff' => 'Blue',
        '#4169e1' => 'Royal Blue',
        '#87ceeb' => 'Light Blue',
        '#00bfff' => 'Deep Sky Blue',
        '#008000' => 'Green',
        '#32cd32' => 'Lime Green',
        '#90ee90' => 'Light Green',
        '#00ff7f' => 'Spring Green',
        '#ffff00' => 'Yellow',
        '#ffd700' => 'Gold',
        '#daa520' => 'Goldenrod',
        '#b8860b' => 'Dark Goldenrod',
        '#ffa500' => 'Orange',
        '#ff8c00' => 'Dark Orange',
        '#ff6347' => 'Tomato',
        '#8b4513' => 'Saddle Brown',
        '#a0522d' => 'Sienna',
        '#d2691e' => 'Chocolate',
        '#cd853f' => 'Peru',
        '#deb887' => 'Burlywood',
        '#f4a460' => 'Sandy Brown',
        '#000000' => 'Black',
        '#2f2f2f' => 'Dark Gray',
        '#696969' => 'Dim Gray',
        '#808080' => 'Gray',
        '#a9a9a9' => 'Dark Gray',
        '#c0c0c0' => 'Silver',
        '#d3d3d3' => 'Light Gray',
        '#ffffff' => 'White',
        '#f5f5dc' => 'Beige',
        '#f5deb3' => 'Wheat',
        '#d2b48c' => 'Tan',
        '#f0e68c' => 'Khaki',
        '#cd7f32' => 'Bronze',
        '#b87333' => 'Copper',
        '#d2691e' => 'Chocolate',
        '#a0522d' => 'Sienna',
        '#8b4513' => 'Saddle Brown',
        '#654321' => 'Dark Brown',
        '#3c2414' => 'Dark Chocolate',
        '#2f1b14' => 'Espresso',
        '#1a0f0a' => 'Black Coffee',
        '#8b0000' => 'Dark Red',
        '#a0522d' => 'Sienna',
        '#cd853f' => 'Peru',
        '#deb887' => 'Burlywood',
        '#f4a460' => 'Sandy Brown',
        '#d2691e' => 'Chocolate',
        '#a0522d' => 'Sienna',
        '#8b4513' => 'Saddle Brown',
        '#654321' => 'Dark Brown',
        '#3c2414' => 'Dark Chocolate',
        '#2f1b14' => 'Espresso',
        '#1a0f0a' => 'Black Coffee'
    ];
    
    $colorCode = strtolower($colorCode);
    return $colorMap[$colorCode] ?? 'Custom Shade';
}
?>
