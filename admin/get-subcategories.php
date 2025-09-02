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
    
    // Debug: Log the response
    error_log("Category: $categoryName, Subcategories: " . json_encode($subcategories));
    
    echo json_encode([
        'success' => true,
        'subcategories' => $subcategories,
        'debug' => [
            'category_name' => $categoryName,
            'full_category' => $fullCategory,
            'subcategories_count' => count($subcategories)
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
