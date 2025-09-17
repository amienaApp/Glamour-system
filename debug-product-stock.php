<?php
/**
 * Debug Product Stock Status
 * This script helps debug product stock and sold out status
 */

require_once 'config1/mongodb.php';
require_once 'models/Product.php';

echo "<h1>Debug Product Stock Status</h1>";

try {
    $productModel = new Product();
    
    // Get all products with their stock status
    $products = $productModel->getAll();
    
    echo "<h2>All Products Stock Status</h2>";
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Product ID</th>";
    echo "<th>Name</th>";
    echo "<th>Stock</th>";
    echo "<th>Available</th>";
    echo "<th>Sold Out?</th>";
    echo "<th>Last Updated</th>";
    echo "</tr>";
    
    foreach ($products as $product) {
        $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
        $isAvailable = isset($product['available']) ? $product['available'] : true;
        $isSoldOut = $stock <= 0 || !$isAvailable;
        $lastUpdated = isset($product['updated_at']) ? $product['updated_at'] : 'Unknown';
        
        $rowColor = $isSoldOut ? '#ffebee' : ($stock <= 2 ? '#fff3e0' : '#e8f5e8');
        
        echo "<tr style='background: {$rowColor};'>";
        echo "<td>" . (string)$product['_id'] . "</td>";
        echo "<td>" . htmlspecialchars($product['name']) . "</td>";
        echo "<td><strong>{$stock}</strong></td>";
        echo "<td>" . ($isAvailable ? 'Yes' : 'No') . "</td>";
        echo "<td><strong>" . ($isSoldOut ? 'YES' : 'No') . "</strong></td>";
        echo "<td>{$lastUpdated}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show products that should be sold out
    echo "<h2>Products That Should Show as Sold Out</h2>";
    $soldOutProducts = array_filter($products, function($product) {
        $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
        $isAvailable = isset($product['available']) ? $product['available'] : true;
        return $stock <= 0 || !$isAvailable;
    });
    
    if (empty($soldOutProducts)) {
        echo "<p style='color: green;'>✓ No products are currently sold out</p>";
    } else {
        echo "<ul>";
        foreach ($soldOutProducts as $product) {
            $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
            $isAvailable = isset($product['available']) ? $product['available'] : true;
            $reason = $stock <= 0 ? "Stock = {$stock}" : "Available = " . ($isAvailable ? 'true' : 'false');
            echo "<li><strong>" . htmlspecialchars($product['name']) . "</strong> - {$reason}</li>";
        }
        echo "</ul>";
    }
    
    // Show products with low stock
    echo "<h2>Products with Low Stock (≤ 2 items)</h2>";
    $lowStockProducts = array_filter($products, function($product) {
        $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
        $isAvailable = isset($product['available']) ? $product['available'] : true;
        return $stock > 0 && $stock <= 2 && $isAvailable;
    });
    
    if (empty($lowStockProducts)) {
        echo "<p style='color: green;'>✓ No products have low stock</p>";
    } else {
        echo "<ul>";
        foreach ($lowStockProducts as $product) {
            $stock = (int)(isset($product['stock']) ? $product['stock'] : 0);
            echo "<li><strong>" . htmlspecialchars($product['name']) . "</strong> - Stock: {$stock}</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>Instructions</h2>";
    echo "<ol>";
    echo "<li>Check the table above to see current stock status of all products</li>";
    echo "<li>If a product shows as 'Sold Out: YES' but doesn't appear sold out on the frontend, try:</li>";
    echo "<li style='margin-left: 20px;'>- Hard refresh the page (Ctrl+F5)</li>";
    echo "<li style='margin-left: 20px;'>- Clear browser cache</li>";
    echo "<li style='margin-left: 20px;'>- Check if you're looking at the right product</li>";
    echo "<li>To test: Edit a product and set stock to 0, then check this page again</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

