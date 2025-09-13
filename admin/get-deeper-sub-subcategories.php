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
    $subSubcategory = $_GET['sub_subcategory'] ?? '';
    
    if (empty($category) || empty($subcategory) || empty($subSubcategory)) {
        ob_clean();
        echo json_encode(['error' => 'Category, subcategory, and sub_subcategory are required']);
        exit;
    }
    
    $categoryModel = new Category();
    $deeperSubSubcategories = $categoryModel->getDeeperSubSubcategories($category, $subcategory, $subSubcategory);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'deeper_sub_subcategories' => $deeperSubSubcategories
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
