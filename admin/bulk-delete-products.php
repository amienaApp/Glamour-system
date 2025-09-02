<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productIds = $_POST['product_ids'] ?? [];
    
    if (empty($productIds)) {
        $error = 'No products selected for deletion.';
    } else {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($productIds as $productId) {
            if ($productModel->delete($productId)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Failed to delete product ID: $productId";
            }
        }
        
        if ($successCount > 0) {
            $message = "Successfully deleted $successCount product(s).";
            if ($errorCount > 0) {
                $message .= " Failed to delete $errorCount product(s).";
            }
        } else {
            $error = "Failed to delete any products.";
        }
        
        if (!empty($errors)) {
            error_log('Bulk delete errors: ' . implode(', ', $errors));
        }
    }
}

// Redirect back to view products with message
$redirectUrl = 'view-products.php';
if (!empty($message)) {
    $redirectUrl .= '?message=' . urlencode($message);
}
if (!empty($error)) {
    $redirectUrl .= '?error=' . urlencode($error);
}

header("Location: $redirectUrl");
exit;
?>

