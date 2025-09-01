<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $productIds = $_GET['ids'] ?? '';
    $value = $_GET['value'] ?? '';
    
    if (empty($action) || empty($productIds) || empty($value)) {
        $error = 'Missing required parameters for bulk edit.';
    } else {
        $ids = explode(',', $productIds);
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($ids as $productId) {
            $productId = trim($productId);
            if (empty($productId)) continue;
            
            $updateData = [];
            
            switch ($action) {
                case 'category':
                    $updateData['category'] = $value;
                    break;
                    
                case 'price':
                    $price = floatval($value);
                    if ($price > 0) {
                        $updateData['price'] = $price;
                    } else {
                        $errorCount++;
                        $errors[] = "Invalid price for product ID: $productId";
                        continue;
                    }
                    break;
                    
                case 'status':
                    $status = strtolower($value);
                    if (in_array($status, ['available', 'unavailable'])) {
                        $updateData['available'] = ($status === 'available');
                    } else {
                        $errorCount++;
                        $errors[] = "Invalid status for product ID: $productId";
                        continue;
                    }
                    break;
                    
                case 'toggle_featured':
                    // Get current product to toggle featured status
                    $product = $productModel->getById($productId);
                    if ($product) {
                        $currentFeatured = $product['featured'] ?? false;
                        $updateData['featured'] = !$currentFeatured;
                    } else {
                        $errorCount++;
                        $errors[] = "Product not found for ID: $productId";
                        continue;
                    }
                    break;
                    
                case 'toggle_sale':
                    // Get current product to toggle sale status
                    $product = $productModel->getById($productId);
                    if ($product) {
                        $currentSale = $product['sale'] ?? false;
                        $updateData['sale'] = !$currentSale;
                    } else {
                        $errorCount++;
                        $errors[] = "Product not found for ID: $productId";
                        continue;
                    }
                    break;
                    
                default:
                    $errorCount++;
                    $errors[] = "Unknown action: $action for product ID: $productId";
                    continue;
            }
            
            if (!empty($updateData)) {
                if ($productModel->update($productId, $updateData)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Failed to update product ID: $productId";
                }
            }
        }
        
        if ($successCount > 0) {
            $actionText = ucfirst($action);
            $message = "Successfully updated $actionText for $successCount product(s).";
            if ($errorCount > 0) {
                $message .= " Failed to update $errorCount product(s).";
            }
        } else {
            $error = "Failed to update any products.";
        }
        
        if (!empty($errors)) {
            error_log('Bulk edit errors: ' . implode(', ', $errors));
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
