<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../config1/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    // Get all home decor products to extract colors
    $allHomeDecorProducts = [];
    
    // Try different category names
    $categories = ["Home & Living", "Home Decor", "Home and Living", "Home"];
    foreach ($categories as $category) {
        $products = $productModel->getByCategory($category);
        if (!empty($products)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        }
    }
    
    // Also get from subcategories
    $subcategories = ['Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture', 'living room', 'dinning room', 'artwork', 'lightinning'];
    foreach ($subcategories as $subcat) {
        $products = $productModel->getBySubcategory($subcat);
        if (!empty($products)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        }
    }
    
    // Remove duplicates based on product ID
    $uniqueProducts = [];
    $seenIds = [];
    foreach ($allHomeDecorProducts as $product) {
        $productId = (string)$product['_id'];
        if (!in_array($productId, $seenIds)) {
            $uniqueProducts[] = $product;
            $seenIds[] = $productId;
        }
    }
    
    // Extract colors with better deduplication
    $colorCounts = [];
    
    foreach ($uniqueProducts as $product) {
        // Main product color
        if (!empty($product['color'])) {
            $color = trim($product['color']);
            // Normalize color (convert to lowercase for comparison but keep original case for display)
            $normalizedColor = strtolower($color);
            $colorCounts[$normalizedColor] = [
                'original' => $color,
                'count' => ($colorCounts[$normalizedColor]['count'] ?? 0) + 1
            ];
        }
        
        // Color variants
        if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
            foreach ($product['color_variants'] as $variant) {
                if (!empty($variant['color'])) {
                    $color = trim($variant['color']);
                    $normalizedColor = strtolower($color);
                    $colorCounts[$normalizedColor] = [
                        'original' => $color,
                        'count' => ($colorCounts[$normalizedColor]['count'] ?? 0) + 1
                    ];
                }
            }
        }
    }
    
    // Convert to array format and sort
    $colors = [];
    foreach ($colorCounts as $normalizedColor => $data) {
        $colors[] = [
            'name' => $data['original'], // Keep original case
            'normalized' => $normalizedColor, // For comparison
            'count' => $data['count'],
            'display_name' => formatColorDisplayName($data['original'])
        ];
    }
    
    // Sort colors by display name
    usort($colors, function($a, $b) {
        return strcmp($a['display_name'], $b['display_name']);
    });
    
    // Group similar colors together (e.g., all "Blue" colors become one "Blue" filter)
    $groupedColors = [];
    
    foreach ($colors as $color) {
        $displayName = $color['display_name'];
        
        if (!isset($groupedColors[$displayName])) {
            $groupedColors[$displayName] = [
                'name' => $color['name'], // Use first color as representative
                'display_name' => $displayName,
                'count' => 0,
                'all_colors' => []
            ];
        }
        
        $groupedColors[$displayName]['count'] += $color['count'];
        $groupedColors[$displayName]['all_colors'][] = $color['name'];
    }
    
    // Create color data with counts
    $colorData = [];
    foreach ($groupedColors as $color) {
        $colorData[] = [
            'name' => $color['name'],
            'count' => $color['count'],
            'display_name' => $color['display_name'],
            'all_colors' => $color['all_colors']
        ];
    }
    
    $response = [
        'success' => true,
        'message' => 'Colors retrieved successfully',
        'data' => [
            'colors' => $colorData,
            'total_colors' => count($colors),
            'total_products' => count($uniqueProducts)
        ]
    ];
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Helper function to format color display names
function formatColorDisplayName($color) {
    // If it's a hex color, convert to readable name
    if (strpos($color, '#') === 0) {
        return hexToReadableName($color);
    }
    
    // Otherwise, just capitalize the first letter
    return ucfirst(strtolower($color));
}

// Helper function to convert hex to readable color name
function hexToReadableName($hex) {
    // Convert hex to RGB
    $r = hexdec(substr($hex, 1, 2));
    $g = hexdec(substr($hex, 3, 2));
    $b = hexdec(substr($hex, 5, 2));
    
    // Convert RGB to HSL
    $hsl = rgbToHsl($r, $g, $b);
    $hue = $hsl[0];
    $saturation = $hsl[1];
    $lightness = $hsl[2];
    
    // Determine color name based on hue and saturation
    if ($saturation < 0.1) {
        if ($lightness > 0.9) return 'White';
        if ($lightness < 0.1) return 'Black';
        return 'Gray';
    }
    
    if ($hue < 15) return 'Red';
    if ($hue < 45) return 'Orange';
    if ($hue < 75) return 'Yellow';
    if ($hue < 150) return 'Green';
    if ($hue < 195) return 'Cyan';
    if ($hue < 255) return 'Blue';
    if ($hue < 285) return 'Purple';
    if ($hue < 315) return 'Magenta';
    return 'Red';
}

// Helper function to group similar colors together
function groupSimilarColors($colors) {
    $groupedColors = [];
    
    foreach ($colors as $color) {
        $colorName = $color['name'];
        $displayName = $color['display_name'];
        $count = $color['count'];
        
        // Group by display name (e.g., all "Blue" colors together)
        if (!isset($groupedColors[$displayName])) {
            $groupedColors[$displayName] = [
                'display_name' => $displayName,
                'colors' => [],
                'total_count' => 0,
                'representative_color' => $colorName
            ];
        }
        
        $groupedColors[$displayName]['colors'][] = $colorName;
        $groupedColors[$displayName]['total_count'] += $count;
    }
    
    // Convert back to array format
    $result = [];
    foreach ($groupedColors as $group) {
        $result[] = [
            'name' => $group['representative_color'], // Use the first color as representative
            'display_name' => $group['display_name'],
            'count' => $group['total_count'],
            'all_colors' => $group['colors'] // Keep track of all colors in this group
        ];
    }
    
    return $result;
}

// Helper function to convert RGB to HSL
function rgbToHsl($r, $g, $b) {
    $r /= 255;
    $g /= 255;
    $b /= 255;
    
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;
    
    if ($max === $min) {
        $h = $s = 0; // achromatic
    } else {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        
        switch ($max) {
            case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
            case $g: $h = ($b - $r) / $d + 2; break;
            case $b: $h = ($r - $g) / $d + 4; break;
        }
        $h /= 6;
    }
    
    return [$h * 360, $s, $l];
}

// Send JSON response
echo json_encode($response);
exit();
?>
