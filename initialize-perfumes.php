<?php
/**
 * Initialize Perfumes Script
 * Run this script to add sample perfume products to the database
 */

require_once 'config/mongodb.php';
require_once 'models/Perfume.php';

try {
    $perfumeModel = new Perfume();
    
    echo "<h2>Initializing Perfumes Database...</h2>";
    
    // Initialize default perfumes
    $result = $perfumeModel->initializeDefaultPerfumes();
    
    echo "<h3>Results:</h3>";
    echo "<p>✅ Successfully added {$result['added']} perfumes out of {$result['total']} total</p>";
    
    if ($result['added'] > 0) {
        echo "<p>🎉 Perfumes have been successfully added to the database!</p>";
        echo "<p><a href='perfumes/'>View Perfumes Page</a></p>";
    } else {
        echo "<p>ℹ️ All perfumes already exist in the database.</p>";
        echo "<p><a href='perfumes/'>View Perfumes Page</a></p>";
    }
    
    // Show some statistics
    $stats = $perfumeModel->getPerfumeStatistics();
    echo "<h3>Current Statistics:</h3>";
    echo "<ul>";
    echo "<li>Total Perfumes: {$stats['total_perfumes']}</li>";
    echo "<li>Men's Perfumes: {$stats['men_perfumes']}</li>";
    echo "<li>Women's Perfumes: {$stats['women_perfumes']}</li>";
    echo "<li>Featured Perfumes: {$stats['featured_perfumes']}</li>";
    echo "<li>Sale Perfumes: {$stats['sale_perfumes']}</li>";
    echo "</ul>";
    
    // Show available brands
    $brands = $perfumeModel->getPerfumeBrands();
    echo "<h3>Available Brands:</h3>";
    echo "<ul>";
    foreach ($brands as $brand) {
        echo "<li>{$brand}</li>";
    }
    echo "</ul>";
    
    // Show available sizes
    $sizes = $perfumeModel->getPerfumeSizes();
    echo "<h3>Available Sizes:</h3>";
    echo "<ul>";
    foreach ($sizes as $size) {
        echo "<li>{$size}</li>";
    }
    echo "</ul>";
    
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
}

h2 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

h3 {
    color: #555;
    margin-top: 30px;
}

ul {
    background-color: #f8f9fa;
    padding: 15px 25px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

li {
    margin-bottom: 5px;
}

a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

p {
    background-color: #e7f3ff;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
</style>
