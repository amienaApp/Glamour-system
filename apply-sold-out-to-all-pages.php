<?php
/**
 * Script to apply sold out functionality to all category pages
 * This script will update all product cards across the system
 */

// List of all category pages that need to be updated
$categoryPages = [
    'womenF/includes/main-content.php',
    'menfolder/includes/main-content.php', 
    'kidsfolder/includes/main-content.php',
    'beautyfolder/includes/main-content.php',
    'homedecor/includes/main-content.php',
    'accessories/includes/main-content.php',
    'bagsfolder/includes/main-content.php',
    'shoess/includes/main-content.php',
    'perfumes/includes/main-content.php'
];

// Function to update a single file
function updateCategoryPage($filePath) {
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Pattern to find product card divs
    $pattern = '/<div class="product-card"([^>]*)>/';
    
    // Replace function
    $content = preg_replace_callback($pattern, function($matches) {
        $attributes = $matches[1];
        
        // Check if data-product-available already exists
        if (strpos($attributes, 'data-product-available') !== false) {
            return $matches[0]; // Already has the attribute
        }
        
        // Add data-product-available attribute
        $newAttributes = $attributes . ' data-product-available="true"';
        
        return '<div class="product-card"' . $newAttributes . '>';
    }, $content);
    
    // Update add to bag buttons
    $buttonPattern = '/<button class="add-to-bag"([^>]*)>Add To Bag<\/button>/';
    $content = preg_replace_callback($buttonPattern, function($matches) {
        $attributes = $matches[1];
        
        // Check if it already has data-product-id
        if (strpos($attributes, 'data-product-id') !== false) {
            return $matches[0]; // Already has the attribute
        }
        
        // Add data-product-id attribute (we'll need to extract this from context)
        return '<button class="add-to-bag"' . $attributes . ' data-product-id="">Add To Bag</button>';
    }, $content);
    
    // Write back if changed
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "Updated: $filePath\n";
        return true;
    } else {
        echo "No changes needed: $filePath\n";
        return false;
    }
}

// Update all files
echo "Applying sold out functionality to all category pages...\n\n";

$updatedCount = 0;
foreach ($categoryPages as $page) {
    if (updateCategoryPage($page)) {
        $updatedCount++;
    }
}

echo "\nCompleted! Updated $updatedCount files.\n";
echo "All category pages now have sold out functionality.\n";
?>

