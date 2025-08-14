<?php
// Test the exact same code that's in womenF/includes/main-content.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

// Get all dresses from the database
$dresses = $productModel->getBySubcategory('Dresses');

echo "<h1>WomenF Page Test</h1>";
echo "<h2>Dresses Count: " . count($dresses) . "</h2>";

if (!empty($dresses)) {
    echo "<h3>Dresses Found:</h3>";
    foreach ($dresses as $dress) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<strong>Name:</strong> " . $dress['name'] . "<br>";
        echo "<strong>Price:</strong> $" . $dress['price'] . "<br>";
        echo "<strong>Category:</strong> " . $dress['category'] . "<br>";
        echo "<strong>Subcategory:</strong> " . $dress['subcategory'] . "<br>";
        echo "<strong>Front Image:</strong> " . ($dress['front_image'] ?? $dress['image_front'] ?? 'None') . "<br>";
        echo "<strong>Back Image:</strong> " . ($dress['back_image'] ?? $dress['image_back'] ?? 'None') . "<br>";
        echo "<strong>Color Variants:</strong> " . count($dress['color_variants'] ?? []) . "<br>";
        echo "</div>";
    }
} else {
    echo "<p>No dresses found!</p>";
}
?>



