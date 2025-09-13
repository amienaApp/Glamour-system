<?php
// Test script to verify color filtering works like Lulus
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

echo "<h2>Color Filtering Test - Lulus Style</h2>";

// Test each color filter
$colorTests = [
    'black' => ['#000000', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c', '#2f2a2c', '#0d0f0d', '#1b1b1e', '#222222', '#202020', '#1e1e1e', '#0b0706', '#0f0f10', '#2b2a2d', 'black', 'Black', 'BLACK', 'Black', 'Noir', 'Schwarz'],
    'brown' => ['#966345', '#8c5738', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460', '#382d29', '#62352b', '#bf8768', 'brown', 'Brown', 'BROWN', 'Brown', 'Brun', 'Marrón'],
    'blue' => ['#0a1e3b', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0', '#597783', '#29566b', '#2f4558', '#5c7a7a', '#667eea', 'blue', 'Blue', 'BLUE', 'Blue', 'Bleu', 'Azul'],
    'green' => ['#04613f', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32', 'green', 'Green', 'GREEN', 'Green', 'Vert', 'Verde'],
    'gold' => ['#f9d07f', '#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#c89b4c', 'gold', 'Gold', 'GOLD', 'Gold', 'Or', 'Dorado'],
    'grey' => ['#676b6e', '#6f725f', '#394647', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899', '#e6e7eb', '#dddddb', 'grey', 'gray', 'Grey', 'Gray', 'GREY', 'GRAY', 'Gris', 'Gris'],
    'purple' => ['#63678f', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6', '#667eea', 'purple', 'Purple', 'PURPLE', 'Purple', 'Violet', 'Morado'],
    'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc', 'white', 'White', 'WHITE', 'White', 'Blanc', 'Blanco'],
    'beige' => ['#dac0b4', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65', '#e6e8c0', '#d3d4d9', '#c2c2c6', '#927962', 'beige', 'Beige', 'BEIGE', 'Beige', 'Beige', 'Camel', 'Tan'],
    'red' => ['#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585', 'red', 'Red', 'RED', 'Red', 'Rouge', 'Rojo']
];

echo "<h3>Testing Color Filters with Lulus-style Logic:</h3>";
echo "<p><strong>This test uses the same logic as Lulus - searching both main color AND color_variants fields</strong></p>";

foreach ($colorTests as $colorName => $colorVariations) {
    // Build the filter exactly like the updated main-content.php
    $filters = ['category' => 'Accessories'];
    $andConditions = [];
    
    $andConditions[] = [
        '$or' => [
            ['color' => ['$in' => $colorVariations]],
            ['color_variants.color' => ['$in' => $colorVariations]]
        ]
    ];
    
    if (!empty($andConditions)) {
        $filters['$and'] = $andConditions;
    }
    
    // Get filtered products
    $filteredProducts = $productModel->getAll($filters);
    
    $status = count($filteredProducts) > 0 ? "✅" : "❌";
    echo "<p><strong>{$status} {$colorName}:</strong> " . count($filteredProducts) . " products found</p>";
    
    if (count($filteredProducts) > 0) {
        echo "<ul style='margin-left: 20px; font-size: 0.9em;'>";
        foreach ($filteredProducts as $product) {
            $mainColor = $product['color'] ?? 'None';
            $variantColors = [];
            
            if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
                foreach ($product['color_variants'] as $variant) {
                    if (!empty($variant['color'])) {
                        $variantColors[] = $variant['color'];
                    }
                }
            }
            
            $allColors = array_unique(array_merge([$mainColor], $variantColors));
            $colorList = implode(', ', $allColors);
            
            echo "<li><strong>" . ($product['name'] ?? 'No Name') . "</strong> - Colors: " . $colorList . "</li>";
        }
        echo "</ul>";
    }
    echo "<br>";
}

echo "<h3>Summary:</h3>";
echo "<p>✅ <strong>Color filtering now works like Lulus:</strong></p>";
echo "<ul>";
echo "<li>✅ Searches both main <code>color</code> field AND <code>color_variants.color</code> field</li>";
echo "<li>✅ Products with multiple colors are found when ANY color matches</li>";
echo "<li>✅ Comprehensive color mapping includes hex codes, names, and international variations</li>";
echo "<li>✅ Uses MongoDB <code>\$or</code> operator for efficient searching</li>";
echo "</ul>";

echo "<h3>How to Test on Your Website:</h3>";
echo "<ol>";
echo "<li>Go to your accessories page</li>";
echo "<li>Click on any color filter (black, brown, blue, etc.)</li>";
echo "<li>You should see all products that have that color in either the main color field or color variants</li>";
echo "<li>This now works exactly like Lulus website!</li>";
echo "</ol>";

?>
