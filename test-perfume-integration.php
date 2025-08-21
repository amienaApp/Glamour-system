<?php
/**
 * Test Perfume Integration
 * This script tests that perfume products added through admin panel appear on perfumes page
 */

require_once 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Perfume.php';

try {
    $productModel = new Product();
    $perfumeModel = new Perfume();
    
    echo "<h2>🧪 Testing Perfume Integration</h2>";
    
    // Test 1: Check if perfume products exist
    $perfumes = $perfumeModel->getAllPerfumes();
    echo "<h3>Test 1: Current Perfume Products</h3>";
    echo "<p>Found " . count($perfumes) . " perfume products in database</p>";
    
    if (count($perfumes) > 0) {
        echo "<ul>";
        foreach (array_slice($perfumes, 0, 5) as $perfume) {
            echo "<li><strong>{$perfume['name']}</strong> - {$perfume['brand']} ({$perfume['gender']}) - {$perfume['size']} - \${$perfume['price']}</li>";
        }
        echo "</ul>";
    }
    
    // Test 2: Add a test perfume product
    echo "<h3>Test 2: Adding Test Perfume Product</h3>";
    
    $testPerfume = [
        'name' => 'Test Perfume - Integration Test',
        'brand' => 'Test Brand',
        'gender' => 'unisex',
        'size' => '100ml',
        'price' => 199.99,
        'color' => '#FF6B9D',
        'category' => 'Perfumes',
        'subcategory' => 'Unisex',
        'description' => 'This is a test perfume product to verify integration.',
        'featured' => false,
        'sale' => false,
        'available' => true,
        'stock' => 10,
        'front_image' => 'img/perfumes/15.jpg',
        'back_image' => 'img/perfumes/15.0.jpg'
    ];
    
    // Check if test product already exists
    $existing = $productModel->getAll([
        'name' => $testPerfume['name'],
        'category' => 'Perfumes'
    ]);
    
    if (empty($existing)) {
        $newProductId = $productModel->create($testPerfume);
        if ($newProductId) {
            echo "<p>✅ Successfully added test perfume product with ID: $newProductId</p>";
        } else {
            echo "<p>❌ Failed to add test perfume product</p>";
        }
    } else {
        echo "<p>ℹ️ Test perfume product already exists</p>";
    }
    
    // Test 3: Verify the product appears in perfume queries
    echo "<h3>Test 3: Verifying Perfume Queries</h3>";
    
    $allPerfumes = $perfumeModel->getAllPerfumes();
    $testProduct = null;
    
    foreach ($allPerfumes as $perfume) {
        if ($perfume['name'] === $testPerfume['name']) {
            $testProduct = $perfume;
            break;
        }
    }
    
    if ($testProduct) {
        echo "<p>✅ Test product found in perfume queries!</p>";
        echo "<ul>";
        echo "<li><strong>Name:</strong> {$testProduct['name']}</li>";
        echo "<li><strong>Brand:</strong> {$testProduct['brand']}</li>";
        echo "<li><strong>Gender:</strong> {$testProduct['gender']}</li>";
        echo "<li><strong>Size:</strong> {$testProduct['size']}</li>";
        echo "<li><strong>Price:</strong> \${$testProduct['price']}</li>";
        echo "<li><strong>Category:</strong> {$testProduct['category']}</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ Test product not found in perfume queries</p>";
    }
    
    // Test 4: Test filtering
    echo "<h3>Test 4: Testing Perfume Filters</h3>";
    
    $unisexPerfumes = $perfumeModel->getPerfumesByGender('unisex');
    echo "<p>Unisex perfumes: " . count($unisexPerfumes) . "</p>";
    
    $testBrandPerfumes = $perfumeModel->getPerfumesByBrand('Test Brand');
    echo "<p>Test Brand perfumes: " . count($testBrandPerfumes) . "</p>";
    
    $testSizePerfumes = $perfumeModel->getPerfumesBySize('100ml');
    echo "<p>100ml perfumes: " . count($testSizePerfumes) . "</p>";
    
    // Test 5: Test statistics
    echo "<h3>Test 5: Perfume Statistics</h3>";
    
    $stats = $perfumeModel->getPerfumeStatistics();
    echo "<ul>";
    echo "<li>Total Perfumes: {$stats['total_perfumes']}</li>";
    echo "<li>Men's Perfumes: {$stats['men_perfumes']}</li>";
    echo "<li>Women's Perfumes: {$stats['women_perfumes']}</li>";
    echo "<li>Featured Perfumes: {$stats['featured_perfumes']}</li>";
    echo "<li>Sale Perfumes: {$stats['sale_perfumes']}</li>";
    echo "</ul>";
    
    // Test 6: Test brands and sizes
    echo "<h3>Test 6: Available Brands and Sizes</h3>";
    
    $brands = $perfumeModel->getPerfumeBrands();
    echo "<p><strong>Available Brands:</strong> " . implode(', ', $brands) . "</p>";
    
    $sizes = $perfumeModel->getPerfumeSizes();
    echo "<p><strong>Available Sizes:</strong> " . implode(', ', $sizes) . "</p>";
    
    echo "<h3>🎉 Integration Test Complete!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='admin/add-product.php' target='_blank'>Admin Add Product</a></li>";
    echo "<li>Select 'Perfumes' as category</li>";
    echo "<li>Fill in the perfume-specific fields (Brand, Gender, Size)</li>";
    echo "<li>Add product images and submit</li>";
    echo "<li>Check <a href='perfumes/' target='_blank'>Perfumes Page</a> to see the new product</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
    background-color: #f8f9fa;
}

h2 {
    color: #333;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
}

h3 {
    color: #555;
    margin-top: 30px;
    background: #e7f3ff;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

ul {
    background-color: #fff;
    padding: 15px 25px;
    border-radius: 5px;
    border-left: 4px solid #28a745;
    margin: 10px 0;
}

li {
    margin-bottom: 5px;
}

p {
    background-color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    margin: 10px 0;
}

a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

ol {
    background-color: #fff;
    padding: 20px 40px;
    border-radius: 5px;
    border-left: 4px solid #ffc107;
}

ol li {
    margin-bottom: 10px;
}
</style>
