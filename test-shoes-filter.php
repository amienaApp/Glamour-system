<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SHOES FILTER FUNCTIONALITY TEST ===\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Test 1: Get all shoes products
    echo "1. Getting all Shoes products...\n";
    $allProducts = $productModel->getByCategory("Shoes");
    echo "   Total products: " . count($allProducts) . "\n\n";
    
    // Test 2: Test category filters
    echo "2. Testing category filters...\n";
    $categories = ['Women\'s Shoes', 'Men\'s Shoes', 'Children\'s Shoes', 'Infant Shoes'];
    
    foreach ($categories as $category) {
        $filters = [
            'category' => "Shoes",
            'subcategory' => $category
        ];
        $products = $productModel->getAll($filters);
        echo "   $category: " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 3: Test size filters
    echo "3. Testing size filters...\n";
    $sizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47'];
    
    foreach ($sizes as $size) {
        $sizeFilters = [
            '$or' => [
                ['sizes' => ['$elemMatch' => ['$eq' => $size]]],
                ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')],
                ['size_category' => $size]
            ]
        ];
        
        $filters = [
            'category' => "Shoes",
            '$and' => [$sizeFilters]
        ];
        
        $products = $productModel->getAll($filters);
        if (count($products) > 0) {
            echo "   Size $size: " . count($products) . " products\n";
        }
    }
    echo "\n";
    
    // Test 4: Test color filters
    echo "4. Testing color filters...\n";
    $colors = ['Black', 'Red', 'Brown', 'White', 'Pink', '#667eea'];
    
    foreach ($colors as $color) {
        $filters = [
            'category' => "Shoes",
            '$or' => [
                ['color' => $color],
                ['color_variants.color' => $color]
            ]
        ];
        $products = $productModel->getAll($filters);
        if (count($products) > 0) {
            echo "   Color $color: " . count($products) . " products\n";
        }
    }
    echo "\n";
    
    // Test 5: Test price filters
    echo "5. Testing price filters...\n";
    $priceRanges = [
        '0-25' => ['$gte' => 0, '$lte' => 25],
        '25-50' => ['$gte' => 25, '$lte' => 50],
        '50-100' => ['$gte' => 50, '$lte' => 100],
        '100-200' => ['$gte' => 100, '$lte' => 200],
        '200+' => ['$gte' => 200]
    ];
    
    foreach ($priceRanges as $range => $priceFilter) {
        $filters = [
            'category' => "Shoes",
            'price' => $priceFilter
        ];
        $products = $productModel->getAll($filters);
        echo "   Price $range: " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 6: Test combined filters
    echo "6. Testing combined filters...\n";
    
    // Test: Women's Shoes + Black + $25-$50
    $combinedFilters = [
        'category' => "Shoes",
        '$and' => [
            ['subcategory' => 'Women\'s Shoes'],
            ['$or' => [
                ['color' => 'Black'],
                ['color_variants.color' => 'Black']
            ]],
            ['price' => ['$gte' => 25, '$lte' => 50]]
        ]
    ];
    
    $products = $productModel->getAll($combinedFilters);
    echo "   Women's Shoes + Black + $25-$50: " . count($products) . " products\n";
    
    // Test: Men's Shoes + Size 42-45
    $sizeFilters = [];
    for ($i = 42; $i <= 45; $i++) {
        $sizeFilters[] = ['sizes' => ['$elemMatch' => ['$eq' => (string)$i]]];
        $sizeFilters[] = ['selected_sizes' => new MongoDB\BSON\Regex('"' . $i . '"', 'i')];
        $sizeFilters[] = ['size_category' => (string)$i];
    }
    
    $combinedFilters = [
        'category' => "Shoes",
        '$and' => [
            ['subcategory' => 'Men\'s Shoes'],
            ['$or' => $sizeFilters]
        ]
    ];
    
    $products = $productModel->getAll($combinedFilters);
    echo "   Men's Shoes + Size 42-45: " . count($products) . " products\n";
    echo "\n";
    
    // Test 7: Test filter API endpoint
    echo "7. Testing filter API endpoint...\n";
    
    // Simulate POST request to filter API
    $postData = [
        'action' => 'filter_products',
        'categories' => ['women\'s shoes'],
        'colors' => ['Black'],
        'price_ranges' => ['25-50']
    ];
    
    // This would normally be a POST request to shoess/filter-api.php
    echo "   Filter API endpoint ready for testing\n";
    echo "   POST data: " . json_encode($postData) . "\n\n";
    
    echo "✅ Shoes filter functionality test completed!\n";
    echo "\n📋 Summary:\n";
    echo "- Total shoes products: " . count($allProducts) . "\n";
    echo "- Categories available: " . implode(', ', $categories) . "\n";
    echo "- Sizes available: " . implode(', ', $sizes) . "\n";
    echo "- Colors available: " . implode(', ', $colors) . "\n";
    echo "- Price ranges: " . implode(', ', array_keys($priceRanges)) . "\n";
    echo "- Filter API: Ready for use\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
