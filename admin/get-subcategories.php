<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();

if (isset($_GET['category'])) {
    $categoryName = $_GET['category'];
    
    // Get all categories
    $categories = $categoryModel->getAll();
    
    // Find the selected category and return its subcategories
    foreach ($categories as $category) {
        if ($category['name'] === $categoryName) {
            echo json_encode([
                'success' => true,
                'subcategories' => $category['subcategories'] ?? []
            ]);
            exit;
        }
    }
    
    // If category not found, return empty array
    echo json_encode([
        'success' => true,
        'subcategories' => []
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Category parameter is required'
    ]);
}
?>
