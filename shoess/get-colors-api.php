<?php
// API to get actual colors from shoes products database
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

// Function to get color name from hex value
function getColorNameFromHex($hex) {
    // Return empty string since we're not displaying color names anymore
    return '';
}

// Function to get hex value from color name
function getHexFromColorName($colorName) {
    $colorMap = [
        'black' => '#000000',
        'white' => '#ffffff',
        'red' => '#ff0000',
        'green' => '#228b22',
        'blue' => '#0066cc',
        'yellow' => '#ffff00',
        'magenta' => '#ff00ff',
        'cyan' => '#00ffff',
        'grey' => '#808080',
        'gray' => '#808080',
        'silver' => '#c0c0c0',
        'gold' => '#ffd700',
        'orange' => '#ffa500',
        'purple' => '#800080',
        'pink' => '#ffc0cb',
        'brown' => '#8b4513',
        'beige' => '#f5f5dc',
        'taupe' => '#483c32',
        'navy' => '#000080',
        'maroon' => '#800000',
        'teal' => '#008080',
        'lime' => '#00ff00',
        'coral' => '#ff7f50',
        'salmon' => '#fa8072',
        'tan' => '#d2b48c',
        'ivory' => '#fffff0',
        'cream' => '#fff8dc',
        'mint' => '#f5fffa',
        'lavender' => '#e6e6fa',
        'peach' => '#ffdab9',
        'rose' => '#ff69b4',
        'turquoise' => '#40e0d0',
        'indigo' => '#4b0082',
        'violet' => '#8a2be2',
        'amber' => '#ffbf00',
        'bronze' => '#cd7f32',
        'copper' => '#b87333',
        'charcoal' => '#36454f',
        'slate' => '#708090',
        'steel' => '#4682b4',
        'denim' => '#1560bd',
        'khaki' => '#f0e68c',
        'burgundy' => '#800020',
        'wine' => '#722f37',
        'plum' => '#8e4585',
        'mauve' => '#e0b0ff',
        'sage' => '#9caf88'
    ];
    return $colorMap[strtolower($colorName)] ?? '#cccccc';
}

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    // Get all shoes products
    $products = $productModel->getByCategory("Shoes");
    
    $colors = [];
    $colorCounts = [];
    
    foreach ($products as $product) {
        // Check main color field
        if (!empty($product['color'])) {
            $color = trim($product['color']);
            if (!isset($colorCounts[$color])) {
                $colorCounts[$color] = 0;
            }
            $colorCounts[$color]++;
        }
        
        // Check color_variants
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
    
    // Convert all colors to array format for frontend (no grouping)
    // Only include colors that have at least one product
    foreach ($colorCounts as $color => $count) {
        // Skip colors with no products
        if ($count <= 0) {
            continue;
        }
        
        // Check if it's a hex color or text color name
        $isHexColor = preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
        $hexValue = $isHexColor ? $color : getHexFromColorName($color);
        
        $colors[] = [
            'value' => $color, // Use the original color value
            'name' => '', // No name since we're not displaying names
            'count' => $count,
            'hex' => $hexValue
        ];
    }
    
    // Sort by count (most common first)
    usort($colors, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    $response = [
        'success' => true,
        'message' => 'Colors retrieved successfully',
        'data' => [
            'colors' => $colors,
            'total_colors' => count($colors)
        ]
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Send JSON response
echo json_encode($response);
exit();
?>
