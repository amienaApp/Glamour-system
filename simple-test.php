<?php
echo "<h1>Simple Product Model Test</h1>";

try {
    require_once 'config1/mongodb.php';
    echo "✅ MongoDB connection successful<br>";
    
    require_once 'models/Product.php';
    echo "✅ Product model loaded<br>";
    
    $productModel = new Product();
    echo "✅ Product model instantiated<br>";
    
    // Test the method signature
    $reflection = new ReflectionMethod('Product', 'getAll');
    $params = $reflection->getParameters();
    echo "✅ getAll method has " . count($params) . " parameters:<br>";
    foreach ($params as $param) {
        echo "  - " . $param->getName() . " (default: " . ($param->isDefaultValueAvailable() ? var_export($param->getDefaultValue(), true) : 'none') . ")<br>";
    }
    
    // Test with correct parameters
    echo "<br>Testing getAll([], [], 1)...<br>";
    $products = $productModel->getAll([], [], 1);
    echo "✅ getAll([], [], 1) successful - found " . count($products) . " products<br>";
    
    if (!empty($products)) {
        $firstProduct = $products[0];
        echo "✅ First product: " . ($firstProduct['name'] ?? 'Unknown') . " (ID: " . $firstProduct['_id'] . ")<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>

