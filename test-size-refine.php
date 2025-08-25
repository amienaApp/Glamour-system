<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SIZE REFINE FUNCTIONALITY TEST ===\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Test 1: Get all women's clothing products
    echo "1. Getting all Women's Clothing products...\n";
    $allProducts = $productModel->getByCategory("Women's Clothing");
    echo "   Total products: " . count($allProducts) . "\n\n";
    
    // Test 2: Test individual size filters
    echo "2. Testing individual size filters...\n";
    $testSizes = ['S', 'M', 'L', 'X', 'XL', 'XXL'];
    
    foreach ($testSizes as $size) {
        $filters = [
            'category' => "Women's Clothing",
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
    
    // Test 3: Test multiple size selection
    echo "3. Testing multiple size selection (S, M, L)...\n";
    $multipleSizes = ['S', 'M', 'L'];
    $filters = [
        'category' => "Women's Clothing",
        '$and' => [
            ['$or' => [
                ['sizes' => ['$elemMatch' => ['$in' => $multipleSizes]]],
                ['selected_sizes' => new MongoDB\BSON\Regex('"' . implode('|', array_map('preg_quote', $multipleSizes, array_fill(0, count($multipleSizes), '/'))) . '"', 'i')],
                ['size_category' => ['$in' => $multipleSizes]]
            ]]
        ]
    ];
    
    $products = $productModel->getAll($filters);
    echo "   Products with S, M, or L: " . count($products) . "\n";
    
    if (count($products) > 0) {
        echo "   Sample products:\n";
        foreach (array_slice($products, 0, 3) as $product) {
            echo "     - " . ($product['name'] ?? 'No name') . " (Sizes: " . ($product['selected_sizes'] ?? 'None') . ")\n";
        }
    }
    echo "\n";
    
    // Test 4: Test size filter with other filters
    echo "4. Testing size filter with subcategory filter...\n";
    $filters = [
        'category' => "Women's Clothing",
        'subcategory' => 'Dresses',
        '$and' => [
            ['$or' => [
                ['sizes' => ['$elemMatch' => ['$eq' => 'S']]],
                ['selected_sizes' => new MongoDB\BSON\Regex('"S"', 'i')],
                ['size_category' => 'S']
            ]]
        ]
    ];
    
    $products = $productModel->getAll($filters);
    echo "   Dresses with size S: " . count($products) . "\n\n";
    
    // Test 5: Test size filter with price range
    echo "5. Testing size filter with price range...\n";
    $filters = [
        'category' => "Women's Clothing",
        'price' => ['$gte' => 50, '$lte' => 100],
        '$and' => [
            ['$or' => [
                ['sizes' => ['$elemMatch' => ['$eq' => 'M']]],
                ['selected_sizes' => new MongoDB\BSON\Regex('"M"', 'i')],
                ['size_category' => 'M']
            ]]
        ]
    ];
    
    $products = $productModel->getAll($filters);
    echo "   Products $50-$100 with size M: " . count($products) . "\n\n";
    
    // Test 6: Verify size data structure
    echo "6. Analyzing size data structure...\n";
    $sizeAnalysis = [];
    foreach ($allProducts as $product) {
        $sizes = $product['sizes'] ?? [];
        $selectedSizes = $product['selected_sizes'] ?? '';
        $sizeCategory = $product['size_category'] ?? '';
        
        if (!empty($selectedSizes)) {
            // Try to parse as JSON
            $parsedSizes = json_decode($selectedSizes, true);
            if (is_array($parsedSizes)) {
                foreach ($parsedSizes as $size) {
                    if (!isset($sizeAnalysis[$size])) {
                        $sizeAnalysis[$size] = 0;
                    }
                    $sizeAnalysis[$size]++;
                }
            }
        }
    }
    
    echo "   Size distribution:\n";
    ksort($sizeAnalysis);
    foreach ($sizeAnalysis as $size => $count) {
        echo "     $size: $count products\n";
    }
    echo "\n";
    
    echo "✅ All size refine tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
