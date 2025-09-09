<?php
// Suppress all output and errors to ensure clean JSON response
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

try {
    $category = $_GET['category'] ?? '';
    $subcategory = $_GET['subcategory'] ?? '';
    
    if (empty($category) || empty($subcategory)) {
        ob_clean();
        echo json_encode(['error' => 'Category and subcategory are required']);
        exit;
    }
    
    $categoryModel = new Category();
    $subSubcategories = $categoryModel->getSubSubcategories($category, $subcategory);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'sub_subcategories' => $subSubcategories
    ]);
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>