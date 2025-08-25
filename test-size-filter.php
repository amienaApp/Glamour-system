<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Size Filter...\n\n";

try {
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    
    $productModel = new Product();
    
    // Test the size filter logic
    $input = [
        'sizes' => ['S', 'M']
    ];
    
    $filters = [];
    $andConditions = [];
    
    // Base filter - only women's clothing
    $filters['category'] = "Women's Clothing";
    
    // Size filter
    if (!empty($input['sizes']) && is_array($input['sizes'])) {
        $sizeFilters = [];
        foreach ($input['sizes'] as $size) {
            // Check if the size exists in the sizes array field
            $sizeFilters[] = ['sizes' => ['$elemMatch' => ['$eq' => $size]]];
            // Check selected_sizes field (JSON array)
            $sizeFilters[] = ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')];
            // Also check size_category field
            $sizeFilters[] = ['size_category' => $size];
        }
        $andConditions[] = ['$or' => $sizeFilters];
    }
    
    // Combine all conditions
    if (!empty($andConditions)) {
        $filters['$and'] = $andConditions;
    }
    
    echo "Filters: " . json_encode($filters, JSON_PRETTY_PRINT) . "\n\n";
    
    $products = $productModel->getAll($filters);
    echo "✅ Found " . count($products) . " products with sizes S or M\n";
    
    if (count($products) > 0) {
        foreach ($products as $product) {
            $sizes = $product['sizes'] ?? [];
            $selectedSizes = $product['selected_sizes'] ?? '';
            $sizeCategory = $product['size_category'] ?? '';
            
            echo "  - " . ($product['name'] ?? 'No name') . "\n";
            echo "    Sizes: " . json_encode($sizes) . "\n";
            echo "    Selected Sizes: " . $selectedSizes . "\n";
            echo "    Size Category: " . $sizeCategory . "\n\n";
        }
    }
    
    // Test individual sizes
    echo "=== Testing Individual Sizes ===\n";
    
    $testSizes = ['S', 'M', 'L', 'X', 'XL', 'XXL'];
    
    foreach ($testSizes as $testSize) {
        $filters = [
            'category' => "Women's Clothing",
            '$and' => [
                ['$or' => [
                    ['sizes' => ['$elemMatch' => ['$eq' => $testSize]]],
                    ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($testSize, '/') . '"', 'i')],
                    ['size_category' => $testSize]
                ]]
            ]
        ];
        
        $products = $productModel->getAll($filters);
        echo "Size '$testSize': " . count($products) . " products\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
