<?php
// Debug script to check database products
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug: Database Products</h2>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Product.php';
    
    $db = MongoDB::getInstance();
    $productModel = new Product();
    
    echo "<h3>1. All Products in Database:</h3>";
    $allProducts = $productModel->getAll();
    echo "Total products found: " . count($allProducts) . "<br><br>";
    
    foreach ($allProducts as $product) {
        echo "<strong>Product ID:</strong> " . $product['_id'] . "<br>";
        echo "<strong>Name:</strong> " . ($product['name'] ?? 'N/A') . "<br>";
        echo "<strong>Category:</strong> " . ($product['category'] ?? 'N/A') . "<br>";
        echo "<strong>Subcategory:</strong> " . ($product['subcategory'] ?? 'N/A') . "<br>";
        echo "<strong>Price:</strong> " . ($product['price'] ?? 'N/A') . "<br>";
        echo "<strong>Images:</strong> " . json_encode($product['images'] ?? []) . "<br>";
        echo "<strong>Front Image:</strong> " . ($product['front_image'] ?? 'N/A') . "<br>";
        echo "<strong>Back Image:</strong> " . ($product['back_image'] ?? 'N/A') . "<br>";
        echo "<hr>";
    }
    
    echo "<h3>2. Women's Clothing Products:</h3>";
    $womensProducts = $productModel->getByCategory("Women's Clothing");
    echo "Women's products found: " . count($womensProducts) . "<br><br>";
    
    foreach ($womensProducts as $product) {
        echo "<strong>Name:</strong> " . ($product['name'] ?? 'N/A') . "<br>";
        echo "<strong>Subcategory:</strong> " . ($product['subcategory'] ?? 'N/A') . "<br>";
        echo "<hr>";
    }
    
    echo "<h3>3. Dresses Products:</h3>";
    $dresses = $productModel->getBySubcategory('Dresses');
    echo "Dresses found: " . count($dresses) . "<br><br>";
    
    foreach ($dresses as $product) {
        echo "<strong>Name:</strong> " . ($product['name'] ?? 'N/A') . "<br>";
        echo "<strong>Category:</strong> " . ($product['category'] ?? 'N/A') . "<br>";
        echo "<hr>";
    }
    
    echo "<h3>4. All Categories:</h3>";
    $categories = $productModel->getCategories();
    echo "Categories: " . implode(', ', $categories) . "<br>";
    
    echo "<h3>5. All Subcategories:</h3>";
    $subcategories = $productModel->getSubcategories();
    echo "Subcategories: " . implode(', ', $subcategories) . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>

