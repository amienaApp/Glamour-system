<?php
header('Content-Type: application/json');

require_once '../config1/mongodb.php';
require_once '../models/Category.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = $_POST['category'] ?? '';
    } else {
        $categoryName = $_GET['category'] ?? '';
    }
    
    if (empty($categoryName)) {
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required'
        ]);
        exit;
    }
    
    $categoryModel = new Category();
    
    // Debug: Get the full category data
    $fullCategory = $categoryModel->getByName($categoryName);
    
    if (!$fullCategory) {
        echo json_encode([
            'success' => false,
            'message' => 'Category not found: ' . $categoryName
        ]);
        exit;
    }
    
    $subcategories = $categoryModel->getSubcategories($categoryName);
    
    // Filter and normalize subcategories to remove duplicates and ensure proper capitalization
    $normalizedSubcategories = [];
    $seenSubcategories = [];
    
    foreach ($subcategories as $subcategory) {
        // Convert to lowercase for comparison
        $lowercaseSub = strtolower(trim($subcategory));
        
        // Skip if we've already seen this subcategory (case-insensitive)
        if (in_array($lowercaseSub, $seenSubcategories)) {
            continue;
        }
        
        // Add to seen list
        $seenSubcategories[] = $lowercaseSub;
        
        // For Women's Clothing, prefer properly capitalized versions and handle common variations
        if (strtolower($categoryName) === "women's clothing") {
            // Define preferred subcategory names (properly capitalized)
            $preferredSubcategories = [
                'dresses' => 'Dresses',
                'tops' => 'Tops', 
                'bottoms' => 'Bottoms',
                'outerwear' => 'Outerwear',
                'activewear' => 'Activewear',
                'lingerie' => 'Lingerie',
                'swimwear' => 'Swimwear',
                'skirts' => 'Skirts',
                'pants' => 'Pants',
                'jeans' => 'Jeans',
                'shorts' => 'Shorts',
                'blouses' => 'Blouses',
                'shirts' => 'Shirts',
                'sweaters' => 'Sweaters',
                'jackets' => 'Jackets',
                'coats' => 'Coats'
            ];
            
            $lowercaseSub = strtolower(trim($subcategory));
            
            // Use preferred name if available, otherwise capitalize properly
            if (isset($preferredSubcategories[$lowercaseSub])) {
                $normalizedSubcategories[] = $preferredSubcategories[$lowercaseSub];
            } else {
                // If not in preferred list, capitalize it properly
                $normalizedSubcategories[] = ucfirst(strtolower($subcategory));
            }
        } else {
            // For other categories, handle common variations and ensure proper capitalization
            $lowercaseSub = strtolower(trim($subcategory));
            
            // Define preferred names for other common categories
            $commonPreferredSubcategories = [
                // Men's Clothing
                'shirts' => 'Shirts',
                'pants' => 'Pants',
                'jackets' => 'Jackets',
                'activewear' => 'Activewear',
                'underwear' => 'Underwear',
                'swimwear' => 'Swimwear',
                'jeans' => 'Jeans',
                'shorts' => 'Shorts',
                'sweaters' => 'Sweaters',
                'coats' => 'Coats',
                
                // Shoes
                'boots' => 'Boots',
                'sandals' => 'Sandals',
                'heels' => 'Heels',
                'flats' => 'Flats',
                'sneakers' => 'Sneakers',
                'sport shoes' => 'Sport Shoes',
                'slippers' => 'Slippers',
                'formal shoes' => 'Formal Shoes',
                'casual shoes' => 'Casual Shoes',
                
                // Bags
                'handbags' => 'Handbags',
                'shoulder bags' => 'Shoulder Bags',
                'crossbody bags' => 'Crossbody Bags',
                'tote bags' => 'Tote Bags',
                'clutches' => 'Clutches',
                'backpacks' => 'Backpacks',
                'wallets' => 'Wallets',
                'luggage' => 'Luggage',
                
                // Accessories
                'jewelry' => 'Jewelry',
                'watches' => 'Watches',
                'sunglasses' => 'Sunglasses',
                'belts' => 'Belts',
                'scarves' => 'Scarves',
                'hats' => 'Hats',
                'gloves' => 'Gloves',
                'socks' => 'Socks',
                'underwear' => 'Underwear'
            ];
            
            // Use preferred name if available, otherwise capitalize properly
            if (isset($commonPreferredSubcategories[$lowercaseSub])) {
                $normalizedSubcategories[] = $commonPreferredSubcategories[$lowercaseSub];
            } else {
                // If not in preferred list, capitalize it properly
                $normalizedSubcategories[] = ucfirst(strtolower($subcategory));
            }
        }
    }
    
    // Sort subcategories alphabetically
    sort($normalizedSubcategories);
    
    // Debug: Log the response
    error_log("Category: $categoryName, Original Subcategories: " . json_encode($subcategories));
    error_log("Category: $categoryName, Normalized Subcategories: " . json_encode($normalizedSubcategories));
    
    echo json_encode([
        'success' => true,
        'subcategories' => $normalizedSubcategories,
        'debug' => [
            'category_name' => $categoryName,
            'original_subcategories' => $subcategories,
            'normalized_subcategories' => $normalizedSubcategories,
            'original_count' => count($subcategories),
            'normalized_count' => count($normalizedSubcategories)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-subcategories.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading subcategories: ' . $e->getMessage()
    ]);
}
?>
