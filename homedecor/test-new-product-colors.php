<?php
// Simple test to check if new products and colors are being detected
echo "<h2>Testing New Product Color Detection</h2>";

// Test the colors API
$colorsResponse = @file_get_contents('http://localhost/Glamour-system/homedecor/get-colors-api.php');
if ($colorsResponse) {
    $colorsData = json_decode($colorsResponse, true);
    
    if ($colorsData && $colorsData['success']) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>✅ Colors API is Working!</h3>";
        echo "<p><strong>Total colors found:</strong> " . $colorsData['data']['total_colors'] . "</p>";
        echo "<p><strong>Total products scanned:</strong> " . $colorsData['data']['total_products'] . "</p>";
        echo "</div>";
        
        echo "<h3>Available Color Groups:</h3>";
        echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
        foreach ($colorsData['data']['colors'] as $color) {
            echo "<div style='background-color: {$color['name']}; color: white; padding: 10px; border-radius: 5px; text-align: center; min-width: 100px;'>";
            echo "<strong>{$color['display_name']}</strong><br>";
            echo "<small>{$color['count']} products</small>";
            echo "</div>";
        }
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>❌ Error Loading Colors</h3>";
        echo "<p>Error: " . ($colorsData['message'] ?? 'Unknown error') . "</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Cannot Connect to Colors API</h3>";
    echo "<p>Make sure your web server is running and the API file exists.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>How to Add New Products:</h3>";
echo "<ol>";
echo "<li><strong>Go to Admin Panel</strong> - Navigate to your admin area</li>";
echo "<li><strong>Add New Product</strong> - Create a new home decor product</li>";
echo "<li><strong>Set Category</strong> - Use 'Home & Living', 'Home Decor', 'Home and Living', or 'Home'</li>";
echo "<li><strong>Set Subcategory</strong> - Use 'Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture', 'living room', 'dinning room', 'artwork', or 'lightinning'</li>";
echo "<li><strong>Add Color</strong> - Set the color field to a hex code (#ff0000) or color name (red)</li>";
echo "<li><strong>Save Product</strong> - Save the product in the database</li>";
echo "<li><strong>Refresh Home Decor Page</strong> - The new color will appear automatically</li>";
echo "</ol>";

echo "<h3>Color Field Examples:</h3>";
echo "<ul>";
echo "<li><strong>Hex Colors:</strong> #ff0000 (red), #00ff00 (green), #0000ff (blue)</li>";
echo "<li><strong>Color Names:</strong> red, blue, green, yellow, black, white, gray</li>";
echo "<li><strong>Color Variants:</strong> Add multiple colors in the color_variants array</li>";
echo "</ul>";

echo "<h3>Testing Steps:</h3>";
echo "<ol>";
echo "<li>Add a new product with a unique color (e.g., #ff69b4 for pink)</li>";
echo "<li>Refresh this test page to see if the color count increases</li>";
echo "<li>Go to the home decor page and check if the new color appears in the sidebar</li>";
echo "<li>Click on the new color to test the filtering</li>";
echo "</ol>";

echo "<p><strong>Note:</strong> The system automatically groups similar colors together. For example, if you add multiple shades of blue, they will all be grouped under one 'Blue' filter.</p>";
?>
