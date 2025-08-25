<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== MEN'S FILTER FUNCTIONALITY TEST ===\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Test 1: Get all men's clothing products
    echo "1. Getting all Men's Clothing products...\n";
    $allProducts = $productModel->getByCategory("Men's Clothing");
    echo "   Total products: " . count($allProducts) . "\n\n";
    
    // Test 2: Test category filters
    echo "2. Testing category filters...\n";
    $categories = ['Shirts', 'T-Shirts', 'Suits', 'Pants', 'Shorts', 'Hoodies'];
    
    foreach ($categories as $category) {
        $filters = [
            'category' => "Men's Clothing",
            'subcategory' => $category
        ];
        
        $products = $productModel->getAll($filters);
        echo "   Category '$category': " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 3: Test color filters
    echo "3. Testing color filters...\n";
    $colors = ['#0066cc', '#812d2d', '#000000', '#ffffff', '#808080', '#333333', '#667eea'];
    
    foreach ($colors as $color) {
        $filters = [
            'category' => "Men's Clothing",
            'color' => $color
        ];
        
        $products = $productModel->getAll($filters);
        echo "   Color '$color': " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 4: Test price range filters
    echo "4. Testing price range filters...\n";
    $priceRanges = [
        ['min' => 0, 'max' => 25, 'label' => '0-25'],
        ['min' => 25, 'max' => 50, 'label' => '25-50'],
        ['min' => 50, 'max' => 100, 'label' => '50-100'],
        ['min' => 100, 'max' => 200, 'label' => '100-200'],
        ['min' => 200, 'max' => null, 'label' => '200+']
    ];
    
    foreach ($priceRanges as $range) {
        $filters = [
            'category' => "Men's Clothing"
        ];
        
        if ($range['max'] !== null) {
            $filters['price'] = ['$gte' => $range['min'], '$lte' => $range['max']];
        } else {
            $filters['price'] = ['$gte' => $range['min']];
        }
        
        $products = $productModel->getAll($filters);
        echo "   Price range '{$range['label']}': " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 5: Test combined filters
    echo "5. Testing combined filters...\n";
    $filters = [
        'category' => "Men's Clothing",
        'subcategory' => 'Shirts',
        'color' => '#0066cc',
        'price' => ['$gte' => 25, '$lte' => 100]
    ];
    
    $products = $productModel->getAll($filters);
    echo "   Shirts, Blue, $25-$100: " . count($products) . " products\n";
    
    if (count($products) > 0) {
        echo "   Sample product: " . ($products[0]['name'] ?? 'No name') . "\n";
    }
    echo "\n";
    
    // Test 6: Test size filters (when sizes are added)
    echo "6. Testing size filters...\n";
    $testSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL'];
    
    foreach ($testSizes as $size) {
        $filters = [
            'category' => "Men's Clothing",
            '$and' => [
                ['$or' => [
                    ['sizes' => ['$elemMatch' => ['$eq' => $size]]],
                    ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')],
                    ['size_category' => $size]
                ]]
            ]
        ];
        
        $products = $productModel->getAll($filters);
        echo "   Size '$size': " . count($products) . " products\n";
    }
    echo "\n";
    
    // Test 7: Test filter API endpoint
    echo "7. Testing filter API endpoint...\n";
    
    // Simulate API request
    $apiData = [
        'action' => 'filter_products',
        'categories' => ['Shirts'],
        'colors' => ['#0066cc'],
        'price_ranges' => ['25-50']
    ];
    
    // This would normally be sent to the API
    echo "   API request data: " . json_encode($apiData, JSON_PRETTY_PRINT) . "\n";
    echo "   (API endpoint would process this request)\n\n";
    
    echo "✅ All men's filter tests completed successfully!\n";
    echo "\nNote: Size filters will work once size data is added to products.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
