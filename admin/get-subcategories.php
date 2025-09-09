
<?php
// Suppress all output and errors to ensure clean JSON response
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = $_POST['category'] ?? '';
    } else {
        $categoryName = $_GET['category'] ?? '';
    }
    
    // Debug: Log what we received
    error_log("get-subcategories.php - REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("get-subcategories.php - GET: " . json_encode($_GET));
    error_log("get-subcategories.php - POST: " . json_encode($_POST));
    error_log("get-subcategories.php - categoryName: " . $categoryName);
    
    if (empty($categoryName)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required. Received: ' . json_encode($_GET) . ' | ' . json_encode($_POST)
        ]);
        exit;
    }
    
    $categoryModel = new Category();
    
    // Use the getSubcategories method directly
    $subcategories = $categoryModel->getSubcategories($categoryName);
    
    if (empty($subcategories)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'No subcategories found for: ' . $categoryName
        ]);
        exit;
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'subcategories' => $subcategories
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error loading subcategories: ' . $e->getMessage()
    ]);
}
?>