<?php
/**
 * Get Materials API
 * Extracts unique materials from home decor products
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();

try {
    // Get all home decor products to extract materials
    $allHomeDecorProducts = [];
    
    // Try different category names
    $categories = ["Home & Living", "Home Decor", "Home and Living", "Home"];
    foreach ($categories as $category) {
        $products = $productModel->getByCategory($category);
        if (!empty($products)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        }
    }
    
    // Also get from subcategories
    $subcategories = ['Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture', 'living room', 'dinning room', 'artwork', 'lightinning'];
    foreach ($subcategories as $subcat) {
        $products = $productModel->getBySubcategory($subcat);
        if (!empty($products)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $products);
        }
    }
    
    // Remove duplicates based on product ID
    $uniqueProducts = [];
    $seenIds = [];
    foreach ($allHomeDecorProducts as $product) {
        $productId = (string)$product['_id'];
        if (!in_array($productId, $seenIds)) {
            $uniqueProducts[] = $product;
            $seenIds[] = $productId;
        }
    }
    
    // Predefined materials that should always be available (with case variations)
    $predefinedMaterials = [
        'Wood' => 0,
        'wood' => 0,
        'WOOD' => 0,
        'Metal' => 0,
        'metal' => 0,
        'METAL' => 0,
        'Fabric' => 0,
        'fabric' => 0,
        'FABRIC' => 0,
        'Glass' => 0,
        'glass' => 0,
        'GLASS' => 0,
        'Ceramic' => 0,
        'ceramic' => 0,
        'CERAMIC' => 0,
        'Plastic' => 0,
        'plastic' => 0,
        'PLASTIC' => 0
    ];
    
    // Extract materials with better deduplication
    $materialCounts = [];
    $materialMapping = []; // Maps lowercase to proper case
    
    // Initialize predefined materials
    foreach ($predefinedMaterials as $material => $count) {
        $lowercase = strtolower($material);
        if (!isset($materialMapping[$lowercase])) {
            $materialMapping[$lowercase] = $material; // Store the first (proper case) version
            $materialCounts[$material] = 0;
        }
    }
    
    foreach ($uniqueProducts as $product) {
        $material = $product['material'] ?? '';
        
        // Skip empty materials
        if (empty($material) || trim($material) === '') {
            continue;
        }
        
        // Clean and normalize material name
        $material = trim($material);
        $lowercase = strtolower($material);
        
        // Check if this material matches any predefined material (case-insensitive)
        $matchedMaterial = null;
        foreach ($materialMapping as $predefinedLower => $predefinedProper) {
            if ($lowercase === $predefinedLower) {
                $matchedMaterial = $predefinedProper;
                break;
            }
        }
        
        if ($matchedMaterial) {
            // Use the predefined proper case version
            $materialCounts[$matchedMaterial]++;
        } else {
            // New material not in predefined list
            $materialCounts[$material] = ($materialCounts[$material] ?? 0) + 1;
        }
    }
    
    // Sort materials: first by count (descending), then alphabetically for same count
    uasort($materialCounts, function($a, $b) {
        if ($a == $b) return 0;
        if ($a == 0) return 1; // Put zero-count materials at the end
        if ($b == 0) return -1;
        return $b - $a; // Higher count first
    });
    
    // Convert to array format for frontend
    $materials = [];
    foreach ($materialCounts as $material => $count) {
        $materials[] = [
            'name' => $material,
            'count' => $count
        ];
    }
    
    $response = [
        'success' => true,
        'materials' => $materials,
        'total_materials' => count($materials),
        'total_products' => count($uniqueProducts)
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Failed to load materials: ' . $e->getMessage(),
        'materials' => []
    ];
    
    http_response_code(500);
    echo json_encode($response);
}
?>
