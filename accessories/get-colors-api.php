<?php
// Get all available colors from accessories products
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

// Get all accessories products
$products = $productModel->getAll(['category' => 'Accessories']);

$allColors = [];
$colorGroups = [];

// Extract all colors from products
foreach ($products as $product) {
    // Main product color
    if (!empty($product['color'])) {
        $allColors[] = $product['color'];
    }
    
    // Color variants
    if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            // Handle different variant structures
            if (is_array($variant)) {
                // Standard array structure
                if (!empty($variant['color'])) {
                    $allColors[] = $variant['color'];
                }
            } elseif (is_string($variant)) {
                // Direct string color
                $allColors[] = $variant;
            }
        }
    }
}

// Remove duplicates and sort
$allColors = array_unique($allColors);
sort($allColors);

// Define base color groups for common colors
$baseColorGroups = [
    'black' => ['#000000', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c', '#2f2a2c', '#0d0f0d', '#1b1b1e', '#222222', '#202020', '#1e1e1e', '#0b0706', '#0f0f10', '#2b2a2d', 'black', 'Black', 'BLACK', 'Black', 'Noir', 'Schwarz'],
    'beige' => ['#dac0b4', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65', '#e6e8c0', '#d3d4d9', '#c2c2c6', '#927962', 'beige', 'Beige', 'BEIGE', 'Beige', 'Beige', 'Camel', 'Tan'],
    'blue' => ['#0a1e3b', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0', '#597783', '#29566b', '#2f4558', '#5c7a7a', '#667eea', 'blue', 'Blue', 'BLUE', 'Blue', 'Bleu', 'Azul'],
    'brown' => ['#966345', '#8c5738', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460', '#382d29', '#62352b', '#bf8768', 'brown', 'Brown', 'BROWN', 'Brown', 'Brun', 'Marrón'],
    'gold' => ['#f9d07f', '#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#c89b4c', 'gold', 'Gold', 'GOLD', 'Gold', 'Or', 'Dorado'],
    'green' => ['#04613f', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32', 'green', 'Green', 'GREEN', 'Green', 'Vert', 'Verde'],
    'grey' => ['#676b6e', '#6f725f', '#394647', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899', '#e6e7eb', '#dddddb', 'grey', 'gray', 'Grey', 'Gray', 'GREY', 'GRAY', 'Gris', 'Gris'],
    'orange' => ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347', 'orange', 'Orange', 'ORANGE', 'Orange', 'Orange', 'Naranja'],
    'pink' => ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4', 'pink', 'Pink', 'PINK', 'Pink', 'Rose', 'Rosa'],
    'purple' => ['#63678f', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6', '#667eea', 'purple', 'Purple', 'PURPLE', 'Purple', 'Violet', 'Morado'],
    'red' => ['#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585', 'red', 'Red', 'RED', 'Red', 'Rouge', 'Rojo'],
    'silver' => ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff', 'silver', 'Silver', 'SILVER', 'Silver', 'Argent', 'Plata'],
    'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc', 'white', 'White', 'WHITE', 'White', 'Blanc', 'Blanco']
];

// Function to determine color group based on hex value
function getColorGroup($color) {
    // Remove # if present
    $hex = ltrim($color, '#');
    
    // Convert to RGB
    if (strlen($hex) == 6) {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Determine color group based on RGB values
        if ($r < 50 && $g < 50 && $b < 50) return 'black';
        if ($r > 200 && $g > 200 && $b > 200) return 'white';
        if ($r > $g && $r > $b) return 'red';
        if ($g > $r && $g > $b) return 'green';
        if ($b > $r && $b > $g) return 'blue';
        if ($r > 200 && $g > 150 && $b < 100) return 'orange';
        if ($r > 200 && $g < 150 && $b > 200) return 'pink';
        if ($r > 150 && $g < 100 && $b > 150) return 'purple';
        if ($r > 150 && $g > 150 && $b < 100) return 'yellow';
        if ($r > 100 && $g > 100 && $b > 100 && $r < 200 && $g < 200 && $b < 200) return 'grey';
        if ($r > 200 && $g > 200 && $b < 100) return 'gold';
        if ($r > 200 && $g > 200 && $b > 200) return 'silver';
    }
    
    // Check for text-based color names
    $colorLower = strtolower($color);
    if (strpos($colorLower, 'black') !== false) return 'black';
    if (strpos($colorLower, 'white') !== false) return 'white';
    if (strpos($colorLower, 'red') !== false) return 'red';
    if (strpos($colorLower, 'blue') !== false) return 'blue';
    if (strpos($colorLower, 'green') !== false) return 'green';
    if (strpos($colorLower, 'yellow') !== false) return 'yellow';
    if (strpos($colorLower, 'orange') !== false) return 'orange';
    if (strpos($colorLower, 'pink') !== false) return 'pink';
    if (strpos($colorLower, 'purple') !== false) return 'purple';
    if (strpos($colorLower, 'brown') !== false) return 'brown';
    if (strpos($colorLower, 'grey') !== false || strpos($colorLower, 'gray') !== false) return 'grey';
    if (strpos($colorLower, 'gold') !== false) return 'gold';
    if (strpos($colorLower, 'silver') !== false) return 'silver';
    if (strpos($colorLower, 'beige') !== false) return 'beige';
    
    return 'other';
}

// Group colors dynamically and count products for each color
$colorCounts = [];
foreach ($allColors as $color) {
    if (!isset($colorCounts[$color])) {
        $colorCounts[$color] = 0;
    }
    $colorCounts[$color]++;
}

// Only include colors that have at least one product
$colorsWithProducts = array_filter($colorCounts, function($count) {
    return $count > 0;
});

foreach ($colorsWithProducts as $color => $count) {
    $group = getColorGroup($color);
    
    if (!isset($colorGroups[$group])) {
        $colorGroups[$group] = [];
    }
    
    if (!in_array($color, $colorGroups[$group])) {
        $colorGroups[$group][] = $color;
    }
}

// Merge with base color groups
$finalColorGroups = [];
foreach ($baseColorGroups as $groupName => $baseColors) {
    $finalColorGroups[$groupName] = array_unique(array_merge($baseColors, $colorGroups[$groupName] ?? []));
}

// Add any new color groups that weren't in the base
foreach ($colorGroups as $groupName => $colors) {
    if (!isset($finalColorGroups[$groupName])) {
        $finalColorGroups[$groupName] = $colors;
    }
}

// Return the color groups
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => [
        'colorGroups' => $finalColorGroups,
        'allColors' => $allColors,
        'totalColors' => count($allColors)
    ]
]);
?>
