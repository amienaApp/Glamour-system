<?php
// Update Beauty & Cosmetics category to include proper subcategories
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Updating Beauty & Cosmetics Categories</h1>";

try {
    require_once 'config1/mongodb.php';
    require_once 'models/Category.php';
    
    $categoryModel = new Category();
    
    // First, let's see what we have
    echo "<h2>Current Beauty & Cosmetics Categories:</h2>";
    $categories = $categoryModel->getAll();
    $beautyCategories = array_filter($categories, function($cat) {
        return strpos($cat['name'], 'Beauty') !== false;
    });
    
    foreach ($beautyCategories as $category) {
        echo "<p><strong>" . htmlspecialchars($category['name']) . "</strong> - ID: " . $category['_id'] . "</p>";
    }
    
    // Update the main Beauty & Cosmetics category to include subcategories
    $beautySubcategories = [
        'Makeup',
        'Skincare', 
        'Hair Care',
        'Bath & Body',
        'Fragrance',
        'Tools & Accessories'
    ];
    
    // Find the main Beauty & Cosmetics category
    $mainBeautyCategory = null;
    foreach ($categories as $category) {
        if ($category['name'] === 'Beauty & Cosmetics') {
            $mainBeautyCategory = $category;
            break;
        }
    }
    
    if ($mainBeautyCategory) {
        echo "<h2>Updating main Beauty & Cosmetics category...</h2>";
        
        // Update the category with subcategories
        $updateData = [
            'subcategories' => $beautySubcategories,
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        $result = $categoryModel->update($mainBeautyCategory['_id'], $updateData);
        
        if ($result) {
            echo "<p>✅ Successfully updated Beauty & Cosmetics category with subcategories:</p>";
            echo "<ul>";
            foreach ($beautySubcategories as $subcategory) {
                echo "<li>" . htmlspecialchars($subcategory) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ Failed to update category</p>";
        }
    } else {
        echo "<p>❌ Main Beauty & Cosmetics category not found</p>";
    }
    
    // Test the updated category
    echo "<h2>Testing updated category:</h2>";
    $updatedCategories = $categoryModel->getAll();
    foreach ($updatedCategories as $category) {
        if ($category['name'] === 'Beauty & Cosmetics') {
            echo "<p><strong>Updated Beauty & Cosmetics category:</strong></p>";
            if (isset($category['subcategories']) && is_array($category['subcategories'])) {
                echo "<ul>";
                foreach ($category['subcategories'] as $subcategory) {
                    echo "<li>" . htmlspecialchars($subcategory) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p><em>No subcategories found</em></p>";
            }
            break;
        }
    }
    
    // Test the get-subcategories.php endpoint
    echo "<h2>Testing get-subcategories.php endpoint:</h2>";
    $url = "http://localhost/Glamour-system/admin/get-subcategories.php?category=" . urlencode("Beauty & Cosmetics");
    echo "<p><strong>Testing URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
    
    $response = file_get_contents($url);
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/add-product.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Add Product</a></p>";
?>

