<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Product.php';
require_once '../models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

// Get test parameters
$testCategory = $_GET['test_category'] ?? '';
$testSubcategory = $_GET['test_subcategory'] ?? '';

echo "<h1>🔍 Filtering Debug Test</h1>";

// Test 1: Show all products
echo "<h2>1. All Products (No Filters)</h2>";
$allProducts = $productModel->getAll();
echo "<p>Total products in database: " . count($allProducts) . "</p>";

if (!empty($allProducts)) {
    echo "<h3>Sample Products:</h3>";
    echo "<ul>";
    foreach (array_slice($allProducts, 0, 5) as $product) {
        echo "<li><strong>" . htmlspecialchars($product['name']) . "</strong>";
        echo " - Category: " . htmlspecialchars($product['category'] ?? 'NULL');
        echo " - Subcategory: " . htmlspecialchars($product['subcategory'] ?? 'NULL');
        echo "</li>";
    }
    echo "</ul>";
}

// Test 2: Test category filtering
if (!empty($testCategory)) {
    echo "<h2>2. Products with Category: '$testCategory'</h2>";
    
    // Test using existing model methods
    $filter = ['category' => $testCategory];
    echo "<p>Testing filter: " . htmlspecialchars(json_encode($filter)) . "</p>";
    
    // Test through Product model
    $modelResult = $productModel->getByCategory($testCategory);
    echo "<p>Product model getByCategory returned: " . count($modelResult) . " products</p>";
    
    if (!empty($modelResult)) {
        echo "<h3>Model Query Results:</h3>";
        echo "<ul>";
        foreach (array_slice($modelResult, 0, 5) as $product) {
            echo "<li><strong>" . htmlspecialchars($product['name']) . "</strong>";
            echo " - Category: " . htmlspecialchars($product['category'] ?? 'NULL');
            echo " - Subcategory: " . htmlspecialchars($product['subcategory'] ?? 'NULL');
            echo "</li>";
        }
        echo "</ul>";
    }
    
    // Test paginated method
    $paginatedResult = $productModel->getPaginated(1, 10, $filter);
    echo "<p>Paginated method returned: " . count($paginatedResult['products']) . " products (Total: " . $paginatedResult['total'] . ")</p>";
}

// Test 3: Test subcategory filtering
if (!empty($testSubcategory)) {
    echo "<h2>3. Products with Subcategory: '$testSubcategory'</h2>";
    
    // Test using existing model methods
    $filter = ['subcategory' => $testSubcategory];
    echo "<p>Testing filter: " . htmlspecialchars(json_encode($filter)) . "</p>";
}

// Test 4: Show all categories and subcategories
echo "<h2>4. Available Categories and Subcategories</h2>";
$categories = $categoryModel->getAll();
echo "<h3>Categories:</h3>";
echo "<ul>";
foreach ($categories as $cat) {
    echo "<li>" . htmlspecialchars($cat['name']) . "</li>";
}
echo "</ul>";

if (!empty($testCategory)) {
    $subcategories = $categoryModel->getSubcategories($testCategory);
    echo "<h3>Subcategories for '$testCategory':</h3>";
    echo "<ul>";
    foreach ($subcategories as $subcat) {
        echo "<li>" . htmlspecialchars($subcat) . "</li>";
    }
    echo "</ul>";
}

// Test 5: Test accessories gender functionality
echo "<h2>5. Test Accessories Gender Functionality</h2>";
$accessoriesProducts = $productModel->getByCategory('Accessories');
echo "<p>Total accessories products: " . count($accessoriesProducts) . "</p>";

if (!empty($accessoriesProducts)) {
    echo "<h3>Accessories Products with Gender Info:</h3>";
    echo "<ul>";
    foreach (array_slice($accessoriesProducts, 0, 10) as $product) {
        echo "<li><strong>" . htmlspecialchars($product['name']) . "</strong>";
        echo " - Gender: " . htmlspecialchars($product['gender'] ?? 'NULL');
        echo " - Subcategory: " . htmlspecialchars($product['subcategory'] ?? 'NULL');
        echo "</li>";
    }
    echo "</ul>";
}

// Test form
echo "<h2>6. Test Filtering</h2>";
echo "<form method='GET'>";
echo "<label>Test Category: <input type='text' name='test_category' value='" . htmlspecialchars($testCategory) . "'></label><br><br>";
echo "<label>Test Subcategory: <input type='text' name='test_subcategory' value='" . htmlspecialchars($testSubcategory) . "'></label><br><br>";
echo "<button type='submit'>Test Filters</button>";
echo "</form>";

echo "<p><a href='add-product.php'>→ Test Add Product (Accessories Gender)</a></p>";
echo "<p><a href='view-products.php'>← Back to View Products</a></p>";
?>
