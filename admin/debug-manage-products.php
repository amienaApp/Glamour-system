<?php
session_start();

echo "<h1>Debug Manage Products</h1>";

// Check admin login status
echo "<h2>Admin Login Status:</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<p style='color: green;'>✅ Admin is logged in</p>";
    echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Admin is NOT logged in</p>";
    echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

// Test product retrieval without login check
echo "<h2>Product Database Test (No Login Check):</h2>";
$allProducts = $productModel->getAll();
echo "<p>Total products in database: " . count($allProducts) . "</p>";

if (count($allProducts) > 0) {
    echo "<h3>First 5 products:</h3>";
    foreach (array_slice($allProducts, 0, 5) as $product) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<strong>Name:</strong> " . $product['name'] . "<br>";
        echo "<strong>ID:</strong> " . $product['_id'] . "<br>";
        echo "<strong>Category:</strong> " . ($product['category'] ?? 'N/A') . "<br>";
        echo "<strong>Created:</strong> " . ($product['createdAt'] ?? 'N/A') . "<br>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>No products found in database!</p>";
}

// Test pagination
echo "<h2>Pagination Test:</h2>";
$page = 1;
$perPage = 12;
$productsData = $productModel->getPaginated($page, $perPage, [], ['createdAt' => 1]);

echo "<p>Pagination results:</p>";
echo "<ul>";
echo "<li>Total products: " . $productsData['total'] . "</li>";
echo "<li>Products returned: " . count($productsData['products']) . "</li>";
echo "<li>Total pages: " . $productsData['pages'] . "</li>";
echo "<li>Current page: " . $productsData['currentPage'] . "</li>";
echo "</ul>";

// Check if there are products with different field names
echo "<h2>Field Name Analysis:</h2>";
$fieldCounts = [];
foreach ($allProducts as $product) {
    foreach ($product as $field => $value) {
        if (!isset($fieldCounts[$field])) {
            $fieldCounts[$field] = 0;
        }
        $fieldCounts[$field]++;
    }
}

echo "<h3>Field usage across all products:</h3>";
foreach ($fieldCounts as $field => $count) {
    echo "<p><strong>$field:</strong> $count products</p>";
}

// Check for products without required fields
echo "<h2>Products Missing Required Fields:</h2>";
$missingFields = [];
foreach ($allProducts as $product) {
    if (!isset($product['name'])) {
        $missingFields[] = $product['_id'] . " - missing 'name'";
    }
    if (!isset($product['category'])) {
        $missingFields[] = $product['_id'] . " - missing 'category'";
    }
}

if (empty($missingFields)) {
    echo "<p style='color: green;'>✅ All products have required fields</p>";
} else {
    echo "<p style='color: red;'>❌ Products missing required fields:</p>";
    foreach ($missingFields as $missing) {
        echo "<p>$missing</p>";
    }
}

echo "<hr>";
echo "<h2>Login Instructions:</h2>";
echo "<p>If you're not logged in, go to: <a href='login.php'>admin/login.php</a></p>";
echo "<p>Default credentials: admin / admin123</p>";
echo "<p>Or run: <a href='setup-admin.php'>admin/setup-admin.php</a> to create admin account</p>";
?>



