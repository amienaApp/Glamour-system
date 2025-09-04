<?php
/**
 * Debug Images Script
 * Test image loading from database
 */

require_once 'config1/mongodb.php';
require_once 'models/Product.php';

$productModel = new Product();

// Test with a known product ID
$productId = '68b5eadb91a66fb16a0d9254'; // Use the one from your test

try {
    // Get product details
    $product = $productModel->getById($productId);
    
    if ($product) {
        echo "<h2>Raw Product Data:</h2>";
        echo "<pre>";
        print_r($product);
        echo "</pre>";
        
        // Test the API endpoint
        echo "<h2>Testing API Endpoint:</h2>";
        $apiUrl = "get-product-details.php?product_id=" . $productId;
        echo "<p>API URL: <a href='$apiUrl' target='_blank'>$apiUrl</a></p>";
        
        // Make the API call
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/Glamour-system/$apiUrl");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            echo "<h3>API Response:</h3>";
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            
            if (isset($data['product']['images'])) {
                echo "<h3>Images Found:</h3>";
                foreach ($data['product']['images'] as $index => $image) {
                    echo "<p>Image $index: " . $image['src'] . "</p>";
                    if (isset($image['src']) && !empty($image['src'])) {
                        echo "<img src='" . $image['src'] . "' style='max-width: 200px; margin: 10px; border: 1px solid #ccc;' alt='Test Image'>";
                    }
                }
            }
            
            if (isset($data['product']['colors'])) {
                echo "<h3>Colors Found:</h3>";
                foreach ($data['product']['colors'] as $color) {
                    echo "<p>Color: " . $color['name'] . " (Hex: " . $color['hex'] . ")</p>";
                    if (isset($color['images']) && is_array($color['images'])) {
                        foreach ($color['images'] as $index => $image) {
                            echo "<p>-- Color Image $index: " . $image['src'] . "</p>";
                            if (isset($image['src']) && !empty($image['src'])) {
                                echo "<img src='" . $image['src'] . "' style='max-width: 150px; margin: 5px; border: 1px solid #ccc;' alt='Color Image'>";
                            }
                        }
                    }
                }
            }
        } else {
            echo "<p>Failed to get API response</p>";
        }
        
    } else {
        echo "<p>Product not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
img { border: 1px solid #ddd; }
</style>

