<?php
// API to get actual colors from men's products database
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

// Function to get color name from hex value
function getColorNameFromHex($hex) {
    $colorMap = [
        '#000000' => 'Black',
        '#ffffff' => 'White',
        '#ff0000' => 'Red',
        '#00ff00' => 'Green',
        '#0000ff' => 'Blue',
        '#ffff00' => 'Yellow',
        '#ff00ff' => 'Magenta',
        '#00ffff' => 'Cyan',
        '#808080' => 'Grey',
        '#c0c0c0' => 'Silver',
        '#ffd700' => 'Gold',
        '#ffa500' => 'Orange',
        '#800080' => 'Purple',
        '#ffc0cb' => 'Pink',
        '#8b4513' => 'Brown',
        '#f5f5dc' => 'Beige',
        '#483c32' => 'Taupe',
        '#228b22' => 'Green',
        '#667eea' => 'Light Blue',
        // Group blue colors together
        '#0066cc' => 'Blue',
        '#748dc1' => 'Blue', // Blue Grey
        '#47aeab' => 'Teal',
        // Group light colors as "White"
        '#e6e5e9' => 'White', // Light Grey
        '#eff0ee' => 'White', // Off White
        '#cfb89e' => 'Beige',
        '#2e425d' => 'Dark Blue',
        '#193129' => 'Dark Green',
        '#fe484a' => 'Red',
        '#303351' => 'Navy Blue',
        '#3c2d27' => 'Brown',
        '#4b5d8b' => 'Blue',
        '#3b5575' => 'Steel Blue',
        '#361d4d' => 'Purple',
        '#4d4a31' => 'Olive',
        '#3c5876' => 'Blue',
        '#7f6862' => 'Brown',
        // Group all dark/black colors as "Black"
        '#292526' => 'Black',
        '#242424' => 'Black', // Charcoal
        '#2d2e35' => 'Black',
        '#0b0b0b' => 'Black',
        '#1a1a19' => 'Black',
        '#2f292e' => 'Black'
    ];
    return $colorMap[strtolower($hex)] ?? $hex;
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
        'taupe' => '#483c32'
    ];
    return $colorMap[strtolower($colorName)] ?? '#cccccc';
}

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    // Get all men's clothing products
    $products = $productModel->getByCategory("Men's Clothing");
    
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
                if (!empty($variant['color'])) {
                    $color = trim($variant['color']);
                    if (!isset($colorCounts[$color])) {
                        $colorCounts[$color] = 0;
                    }
                    $colorCounts[$color]++;
                }
            }
        }
    }
    
    // Group colors by their mapped names and combine counts
    $groupedColors = [];
    foreach ($colorCounts as $color => $count) {
        // Check if it's a hex color or text color name
        $isHexColor = preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
        $mappedName = $isHexColor ? getColorNameFromHex($color) : $color;
        $hexValue = $isHexColor ? $color : getHexFromColorName($color);
        
        if (!isset($groupedColors[$mappedName])) {
            $groupedColors[$mappedName] = [
                'name' => $mappedName,
                'count' => 0,
                'hex' => $hexValue,
                'original_colors' => []
            ];
        }
        
        $groupedColors[$mappedName]['count'] += $count;
        $groupedColors[$mappedName]['original_colors'][] = $color;
    }
    
    // Convert to array format for frontend
    foreach ($groupedColors as $colorName => $colorData) {
        $colors[] = [
            'value' => $colorName, // Use the mapped name as the filter value
            'name' => $colorData['name'],
            'count' => $colorData['count'],
            'hex' => $colorData['hex']
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
