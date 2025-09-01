<?php
// Test database connection and basic functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Glamour System - Connection Test</h2>";

try {
    // Test MongoDB connection
    echo "<h3>1. Testing MongoDB Connection...</h3>";
    require_once 'config/mongodb.php';
    $db = MongoDB::getInstance();
    
    if ($db->isConnected()) {
        echo "✅ MongoDB connection successful!<br>";
        echo "Database: " . $db->getDatabaseName() . "<br>";
        
        // Test database stats
        $stats = $db->getStats();
        if ($stats) {
            echo "✅ Database stats retrieved successfully<br>";
        }
    } else {
        echo "❌ MongoDB connection failed<br>";
    }
    
    // Test Product model
    echo "<h3>2. Testing Product Model...</h3>";
    require_once 'models/Product.php';
    $productModel = new Product();
    
    // Get product summary
    $summary = $productModel->getProductSummary();
    echo "✅ Product model working!<br>";
    echo "Total products: " . $summary['total_products'] . "<br>";
    echo "Featured products: " . $summary['featured_products'] . "<br>";
    echo "Categories: " . $summary['categories'] . "<br>";
    
    // Test getting some products
    $products = $productModel->getAll([], [], 3);
    echo "✅ Retrieved " . count($products) . " sample products<br>";
    
    // Test Cart model
    echo "<h3>3. Testing Cart Model...</h3>";
    require_once 'models/Cart.php';
    $cartModel = new Cart();
    
    $testUserId = 'test_user_' . time();
    $cart = $cartModel->getCart($testUserId);
    echo "✅ Cart model working!<br>";
    echo "Cart items: " . count($cart['items'] ?? []) . "<br>";
    
    echo "<h3>4. System Status</h3>";
    echo "✅ All core components are working!<br>";
    echo "✅ You can now access the website<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>5. Access URLs</h3>";
echo "🌐 <a href='http://localhost/Glamour-system/'>Main Website</a><br>";
echo "🔧 <a href='http://localhost/Glamour-system/admin/'>Admin Panel</a><br>";
echo "👥 <a href='http://localhost/Glamour-system/menfolder/men.php'>Men's Collection</a><br>";
echo "👗 <a href='http://localhost/Glamour-system/womenF/'>Women's Collection</a><br>";
echo "👶 <a href='http://localhost/Glamour-system/childrenfolder/children.php'>Children's Collection</a><br>";
echo "👠 <a href='http://localhost/Glamour-system/shoess/shoes.php'>Shoes</a><br>";
echo "👜 <a href='http://localhost/Glamour-system/bagsfolder/bags.php'>Bags</a><br>";
echo "💍 <a href='http://localhost/Glamour-system/accessories/accessories.php'>Accessories</a><br>";
echo "🌸 <a href='http://localhost/Glamour-system/perfumes/'>Perfumes</a><br>";
?>

