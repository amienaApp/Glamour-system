<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Category.php';
require_once '../models/Product.php';

// Helper function to convert BSONArray to regular array
function toArray($value) {
    if (is_object($value) && method_exists($value, 'toArray')) {
        $result = $value->toArray();
        // Ensure we get a proper array
        if (is_array($result)) {
            return $result;
        } else {
            // If toArray() returns an object, try to convert it
            return (array)$result;
        }
    }
    return $value;
}

// Helper function to get count of array or BSONArray
function getCount($value) {
    if (is_object($value) && method_exists($value, 'count')) {
        return $value->count();
    }
    return count($value);
}

$categoryModel = new Category();
$productModel = new Product();
$categories = $categoryModel->getAll();

$message = '';
$error = '';

// Get product ID from URL
$productId = $_GET['id'] ?? '';

if (!$productId) {
    header('Location: manage-products.php');
    exit;
}

// Get product data
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: manage-products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image uploads
    $uploadDir = '../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $productModel = new Product();
    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    // Check if this is a multi-product submission
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        // Multiple products submission
        foreach ($_POST['products'] as $productIndex => $productPost) {
            if (empty($productPost['name'])) continue; // Skip empty products
            
            $productId = $productPost['product_id'] ?? '';
            if (!$productId) continue; // Skip if no product ID
            
            $productData = [
                'name' => $productPost['name'] ?? '',
                'price' => (float)($productPost['price'] ?? 0),
                'color' => $productPost['color'] ?? '',
                'category' => $productPost['category'] ?? '',
                'subcategory' => $productPost['subcategory'] ?? '',
                'description' => $productPost['description'] ?? '',
                'featured' => isset($productPost['featured']),
                'sale' => isset($productPost['sale']),
                'available' => isset($productPost['available']),
                'stock' => (int)($productPost['stock'] ?? 0),
                'size_category' => $productPost['size_category'] ?? '',
                'selected_sizes' => $productPost['selected_sizes'] ?? ''
            ];

            // Handle perfume-specific fields
            if (strtolower($productData['category']) === 'perfumes') {
                $productData['category'] = 'Perfumes'; // Ensure correct case
                $productData['brand'] = $productPost['brand'] ?? '';
                $productData['gender'] = $productPost['gender'] ?? '';
                $productData['size'] = $productPost['size'] ?? '';
            }

            // Handle main product images for this product
            if (isset($_FILES['products']['name'][$productIndex]['front_image']) && 
                $_FILES['products']['error'][$productIndex]['front_image'] === UPLOAD_ERR_OK) {
                $frontImageName = time() . '_product_' . $productIndex . '_front_' . $_FILES['products']['name'][$productIndex]['front_image'];
                $frontImagePath = $uploadDir . $frontImageName;
                if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['front_image'], $frontImagePath)) {
                    $productData['front_image'] = 'uploads/products/' . $frontImageName;
                }
            } else {
                $productData['front_image'] = $productPost['existing_front_image'] ?? '';
            }

            if (isset($_FILES['products']['name'][$productIndex]['back_image']) && 
                $_FILES['products']['error'][$productIndex]['back_image'] === UPLOAD_ERR_OK) {
                $backImageName = time() . '_product_' . $productIndex . '_back_' . $_FILES['products']['name'][$productIndex]['back_image'];
                $backImagePath = $uploadDir . $backImageName;
                if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['back_image'], $backImagePath)) {
                    $productData['back_image'] = 'uploads/products/' . $backImageName;
                }
            } else {
                $productData['back_image'] = $productPost['existing_back_image'] ?? '';
            }

            // Handle color variants for this product
            if (isset($productPost['color_variants']) && is_array($productPost['color_variants'])) {
                $colorVariants = [];
                foreach ($productPost['color_variants'] as $variantIndex => $variant) {
                    if (!empty($variant['name']) && !empty($variant['color'])) {
                        $variantData = [
                            'name' => $variant['name'],
                            'color' => $variant['color'],
                            'size_category' => $variant['size_category'] ?? '',
                            'selected_sizes' => $variant['selected_sizes'] ?? ''
                        ];

                        // Handle perfume-specific fields for variants
                        if (strtolower($productData['category']) === 'perfumes') {
                            $variantData['brand'] = $variant['brand'] ?? '';
                            $variantData['gender'] = $variant['gender'] ?? '';
                            $variantData['size'] = $variant['size'] ?? '';
                        }

                        // Handle variant images
                        if (isset($_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['front_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants']['error'][$variantIndex]['front_image'] === UPLOAD_ERR_OK) {
                            $variantFrontImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_front_' . $_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['front_image'];
                            $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants']['tmp_name'][$variantIndex]['front_image'], $variantFrontImagePath)) {
                                $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                            }
                        } else {
                            $variantData['front_image'] = $variant['existing_front_image'] ?? '';
                        }

                        if (isset($_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['back_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants']['error'][$variantIndex]['back_image'] === UPLOAD_ERR_OK) {
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['back_image'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants']['tmp_name'][$variantIndex]['back_image'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                            }
                        } else {
                            $variantData['back_image'] = $variant['existing_back_image'] ?? '';
                        }

                        $colorVariants[] = $variantData;
                    }
                }
                $productData['color_variants'] = $colorVariants;
            }

            if (isset($productPost['sale']) && !empty($productPost['salePrice'])) {
                $productData['salePrice'] = (float)$productPost['salePrice'];
            }

            // Validate and update product
            $validationErrors = $productModel->validateProductData($productData);
            if (empty($validationErrors)) {
                if ($productModel->update($productId, $productData)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Failed to update product: " . $productData['name'];
                }
            } else {
                $errorCount++;
                $errors[] = "Validation errors for " . $productData['name'] . ": " . implode(', ', $validationErrors);
            }
        }

        // Set success/error messages
        if ($successCount > 0) {
            $message = "Successfully updated $successCount product(s)";
            if ($errorCount > 0) {
                $message .= ". Failed to update $errorCount product(s)";
            }
        } else {
            $error = "Failed to update any products. " . implode('; ', $errors);
        }

        if ($successCount > 0) {
            // Redirect to manage products
            header('Location: manage-products.php?action=bulk_updated&count=' . $successCount);
            exit;
        }
    } else {
        // Single product submission (original logic)
        $productData = [
            'name' => $_POST['name'] ?? '',
            'price' => (float)($_POST['price'] ?? 0),
            'color' => $_POST['color'] ?? '',
            'category' => $_POST['category'] ?? '',
            'subcategory' => $_POST['subcategory'] ?? $product['subcategory'] ?? '',
            'description' => $_POST['description'] ?? '',
            'featured' => isset($_POST['featured']),
            'sale' => isset($_POST['sale']),
            'available' => isset($_POST['available']),
            'stock' => (int)($_POST['stock'] ?? 0),
            'size_category' => $_POST['size_category'] ?? '',
            'selected_sizes' => $_POST['selected_sizes'] ?? ''
        ];

        // Handle perfume-specific fields
        if (strtolower($productData['category']) === 'perfumes') {
            $productData['category'] = 'Perfumes'; // Ensure correct case
            $productData['brand'] = $_POST['brand'] ?? '';
            $productData['gender'] = $_POST['gender'] ?? '';
            $productData['size'] = $_POST['size'] ?? '';
        }

        // Handle main product images
        if (isset($_FILES['front_image']) && $_FILES['front_image']['error'] === UPLOAD_ERR_OK) {
            $frontImageName = time() . '_front_' . $_FILES['front_image']['name'];
            $frontImagePath = $uploadDir . $frontImageName;
            if (move_uploaded_file($_FILES['front_image']['tmp_name'], $frontImagePath)) {
                $productData['front_image'] = 'uploads/products/' . $frontImageName;
            }
        } else {
            $productData['front_image'] = $_POST['existing_front_image'] ?? '';
        }

        if (isset($_FILES['back_image']) && $_FILES['back_image']['error'] === UPLOAD_ERR_OK) {
            $backImageName = time() . '_back_' . $_FILES['back_image']['name'];
            $backImagePath = $uploadDir . $backImageName;
            if (move_uploaded_file($_FILES['back_image']['tmp_name'], $backImagePath)) {
                $productData['back_image'] = 'uploads/products/' . $backImageName;
            }
        } else {
            $productData['back_image'] = $_POST['existing_back_image'] ?? '';
        }

        // Handle color variants - COMPLETE DELETION SYSTEM
        $originalVariants = $product['color_variants'] ?? [];
        $deletedVariants = [];
        
        // Get deleted variants from form
        if (isset($_POST['deleted_variants']) && !empty($_POST['deleted_variants'])) {
            $deletedVariants = json_decode($_POST['deleted_variants'], true) ?? [];

        }
        
        if (isset($_POST['color_variants']) && is_array($_POST['color_variants'])) {
            $colorVariants = [];
            
            foreach ($_POST['color_variants'] as $index => $variant) {
                // Always preserve the variant, even if some fields are empty
                $variantData = [
                    'name' => $variant['name'] ?? '',
                    'color' => $variant['color'] ?? '',
                    'size_category' => $variant['size_category'] ?? '',
                    'selected_sizes' => $variant['selected_sizes'] ?? ''
                ];

                // Handle perfume-specific fields for variants
                if (strtolower($productData['category']) === 'perfumes') {
                    $variantData['brand'] = $variant['brand'] ?? '';
                    $variantData['gender'] = $variant['gender'] ?? '';
                    $variantData['size'] = $variant['size'] ?? '';
                }

                // Handle variant front image
                if (isset($_FILES['color_variants']['name'][$index]['front_image']) && 
                    $_FILES['color_variants']['error'][$index]['front_image'] === UPLOAD_ERR_OK) {
                    $variantFrontImageName = time() . '_variant_' . $index . '_front_' . $_FILES['color_variants']['name'][$index]['front_image'];
                    $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                    if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['front_image'], $variantFrontImagePath)) {
                        $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                    }
                } else {
                    // Preserve existing image if no new one uploaded
                    $variantData['front_image'] = $variant['existing_front_image'] ?? '';
                }

                // Handle variant back image
                if (isset($_FILES['color_variants']['name'][$index]['back_image']) && 
                    $_FILES['color_variants']['error'][$index]['back_image'] === UPLOAD_ERR_OK) {
                    $variantBackImageName = time() . '_variant_' . $index . '_back_' . $_FILES['color_variants']['name'][$index]['back_image'];
                    $variantBackImagePath = $uploadDir . $variantBackImageName;
                    if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['back_image'], $variantBackImagePath)) {
                        $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                    }
                } else {
                    // Preserve existing image if no new one uploaded
                    $variantData['back_image'] = $variant['existing_back_image'] ?? '';
                }

                $colorVariants[] = $variantData;
            }
            
            // Remove deleted variants from the final array
            foreach ($deletedVariants as $deletedIndex) {
                if (isset($colorVariants[$deletedIndex])) {
                    unset($colorVariants[$deletedIndex]);
    
                }
            }
            
            // Reindex the array to remove gaps
            $colorVariants = array_values($colorVariants);

            $productData['color_variants'] = $colorVariants;

        } else {
            // If no color_variants were submitted, remove deleted variants from original
            $colorVariants = $originalVariants;
            foreach ($deletedVariants as $deletedIndex) {
                if (isset($colorVariants[$deletedIndex])) {
                    unset($colorVariants[$deletedIndex]);

                }
            }
            $colorVariants = array_values($colorVariants);
            $productData['color_variants'] = $colorVariants;
        }

        if (isset($_POST['sale']) && !empty($_POST['salePrice'])) {
            $productData['salePrice'] = (float)$_POST['salePrice'];
        }

        // Validate and update product
        $errors = $productModel->validateProductData($productData);
        if (empty($errors)) {
            if ($productModel->update($productId, $productData)) {
                // Redirect to manage products with the updated product highlighted
                header('Location: manage-products.php?highlight=' . $productId . '&action=updated');
                exit;
            } else {
                $error = 'Failed to update product.';
            }
        } else {
            $error = 'Validation errors: ' . implode(', ', $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Circular Std', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%);
            min-height: 100vh;
            color: #3E2723;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px 0;
            box-shadow: 5px 0 25px rgba(62, 39, 35, 0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 30px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            margin-bottom: 30px;
        }

        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3E2723;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo i {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2rem;
        }

        .sidebar-nav {
            padding: 0 20px;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #3E2723;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding: 0 10px;
        }

        .nav-item {
            display: block;
            padding: 12px 20px;
            color: #3E2723;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(41, 182, 246, 0.1);
            color: #0288D1;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            box-shadow: 0 5px 15px rgba(41, 182, 246, 0.3);
        }

        .nav-item i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .logout-btn {
            position: absolute;
            bottom: 30px;
            left: 20px;
            right: 20px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3);
        }
        
        .sidebar-actions {
            margin-top: 30px;
            padding: 0 20px;
        }
        
        .sidebar-action-btn {
            width: 100%;
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .sidebar-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3);
            text-decoration: none;
            color: white;
        }
        
        .sidebar-action-btn.secondary {
            background: linear-gradient(135deg, #3E2723, #5D4037);
        }
        
        .sidebar-action-btn.secondary:hover {
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.3);
        }
        
        .sidebar-action-btn.success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }
        
        .sidebar-action-btn.success:hover {
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(62, 39, 35, 0.08);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header {
            background: linear-gradient(135deg, #29B6F6 0%, #0288D1 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .form-container {
            padding: 25px;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            position: relative;
            letter-spacing: -0.3px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #29B6F6, #0288D1);
            border-radius: 2px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3E2723;
            font-size: 0.85rem;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Color Panel Styling */
        .color-panel {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .color-panel label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }

        .color-input {
            width: 100%;
            height: 28px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .color-input:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Image Upload Styling */
        .image-upload-section {
            background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
            border: 1px dashed #48bb78;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .image-upload-section:hover {
            border-color: #38a169;
            background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%);
        }

        .image-upload-section h3 {
            color: #2f855a;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .image-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }

        .image-input-group {
            text-align: center;
        }
        
        .image-input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2f855a;
            font-size: 0.85rem;
        }

        .image-input-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #48bb78;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .image-input-group input[type="file"]:hover {
            border-color: #38a169;
            background: #f0fff4;
        }

        /* Image Preview Styling */
        .image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
        }

        .image-preview img:hover {
            transform: scale(1.05);
        }

        .image-preview .no-image {
            width: 150px;
            height: 100px;
            background: #f7fafc;
            border: 1px dashed #cbd5e0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            font-size: 0.8rem;
            margin: 0 auto;
        }

        .variant-image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .variant-image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        /* Color Variants Section */
        .color-variants-section {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border: 1px solid #fc8181;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .color-variants-section h3 {
            color: #c53030;
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .color-variant-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #fed7d7;
        }
        
        .color-variant-item:last-child {
            margin-bottom: 0;
        }
        
        .variant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #fed7d7;
        }
        
        .variant-title {
            font-weight: 600;
            color: #c53030;
            font-size: 1rem;
        }
        
        .remove-variant-btn {
            background: #e53e3e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .remove-variant-btn:hover {
            background: #c53030;
            transform: scale(1.05);
        }

        .variant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .variant-color-panel {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }

        .variant-color-panel label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 6px;
            display: block;
        }

        .variant-color-input {
            width: 100%;
            height: 35px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(72, 187, 120, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 62, 62, 0.3);
        }

        .submit-btn {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(41, 182, 246, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(41, 182, 246, 0.4);
        }
        
        .clear-btn {
            background: linear-gradient(135deg, #3E2723, #5D4037);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(62, 39, 35, 0.3);
        }
        
        .clear-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(62, 39, 35, 0.4);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .add-variant-btn {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-variant-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(41, 182, 246, 0.3);
        }
        
        .color-variants-section {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 25px;
            border: 2px solid rgba(62, 39, 35, 0.1);
        }
        
        .variant-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            position: relative;
            margin-bottom: 20px;
        }
        
        .variant-item h4 {
            color: #3E2723;
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Multi-Product Styles */
        .product-count-selector {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }

        .product-count-selector h3 {
            color: #3E2723;
            margin-bottom: 20px;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .count-selector-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .count-selector-group label {
            font-weight: 600;
            color: #3E2723;
            font-size: 1.1rem;
        }

        .count-selector-group input[type="number"] {
            padding: 12px 20px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
        }

        .count-selector-group input[type="number"]:focus {
            outline: none;
            border-color: #29B6F6;
            box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1);
        }

        .count-selector-group input[type="number"]::-webkit-inner-spin-button,
        .count-selector-group input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
            height: 30px;
        }

        .generate-forms-btn {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .generate-forms-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3);
        }

        .product-form-container {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .product-form-header {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: -20px -20px 20px -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-form-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .product-form-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .remove-product-btn {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .remove-product-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }

        .single-product-form {
            display: none;
        }

        .multi-product-form {
            display: none;
        }

        .form-active {
            display: block;
        }
        
        .variant-image-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .variant-image-preview {
            margin-top: 10px;
            min-height: 80px;
            border: 2px dashed rgba(62, 39, 35, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .variant-image-preview img {
            max-width: 100%;
            max-height: 80px;
            border-radius: 6px;
        }
        
        .variant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .variant-header h4 {
            margin: 0;
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
        }

        .delete-variant-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .delete-variant-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .delete-variant-btn i {
            font-size: 12px;
        }

        .delete-variant-btn span {
            font-size: 13px;
        }

        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .message.error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .mobile-menu-btn {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-btn {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                padding: 12px;
                border-radius: 10px;
                cursor: pointer;
                box-shadow: 0 5px 15px rgba(62, 39, 35, 0.1);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .image-inputs {
                grid-template-columns: 1fr;
            }
            
            .variant-grid {
                grid-template-columns: 1fr;
            }
            
            .variant-image-inputs {
                grid-template-columns: 1fr;
            }
        }

        /* Size Dropdown Styles */
        .size-dropdown-container {
            position: relative;
            width: 100%;
        }

        .size-dropdown-header {
            background: white;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            padding: 12px 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .size-dropdown-header:hover {
            border-color: #29B6F6;
            box-shadow: 0 2px 8px rgba(41, 182, 246, 0.1);
        }

        .size-dropdown-header.active {
            border-color: #29B6F6;
            box-shadow: 0 4px 12px rgba(41, 182, 246, 0.2);
        }

        .size-dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #E0E0E0;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .size-dropdown-content.show {
            display: block;
        }

        .size-category {
            border-bottom: 1px solid #f0f0f0;
        }

        .size-category:last-child {
            border-bottom: none;
        }

        .size-category-header {
            background: #f8f9fa;
            padding: 10px 15px;
            font-weight: 600;
            color: #3E2723;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s ease;
        }

        .size-category-header:hover {
            background: #e9ecef;
        }

        .size-category-header i {
            transition: transform 0.3s ease;
        }

        .size-category-header.expanded i {
            transform: rotate(180deg);
        }

        .size-options {
            padding: 10px 15px;
            display: none;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-options.show {
            display: flex;
        }

        .size-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .size-option:hover {
            background: #e9ecef;
            border-color: #29B6F6;
        }

        .size-option.selected {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border-color: #29B6F6;
        }

        .size-option input[type="checkbox"] {
            margin: 0;
            cursor: pointer;
        }

        .size-option label {
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }

        .select-all-option {
            background: linear-gradient(135deg, #4CAF50, #45a049) !important;
            color: white !important;
            border-color: #4CAF50 !important;
            font-weight: 600 !important;
        }

        .select-all-option:hover {
            background: linear-gradient(135deg, #45a049, #4CAF50) !important;
        }

        .select-all-option.selected {
            background: linear-gradient(135deg, #45a049, #4CAF50) !important;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        }

        /* Admin Color Circle Styles */
        .color-input-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-color-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-color-circle:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .admin-color-circle.active {
            border-color: #29B6F6;
            box-shadow: 0 0 0 3px #fff, 0 0 0 6px #29B6F6;
        }

        .variant-image-preview {
            margin-top: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .variant-image-preview img {
            max-width: 100%;
            max-height: 100px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .variant-image-preview .no-image {
            color: #999;
            font-style: italic;
        }

        /* Elegant Modal System */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }
        
        .modal-header {
            padding: 24px 32px 16px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }
        
        .modal-body {
            padding: 24px 32px;
            color: #4b5563;
            line-height: 1.6;
        }
        
        .modal-body strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        .modal-footer {
            padding: 16px 32px 24px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            border-top: 1px solid #e5e7eb;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 80px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0288D1, #0277BD);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(41, 182, 246, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Modal Type Icons */
        .modal-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .modal-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .modal-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .modal-icon.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .modal-icon.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .size-dropdown-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .size-dropdown-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .size-dropdown-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .size-dropdown-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Selected sizes display */
        .selected-sizes-display {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .selected-size-tag {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <?php include 'includes/admin-sidebar.php'; ?>
        
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="header">
                <h1><i class="fas fa-edit"></i> Edit Product</h1>
                <p>Update product details and manage color variants</p>
        </div>
        
            <div class="form-container">
                <?php if ($message): ?>
                <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
                <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
                <!-- Product Count Selector -->
        <div class="product-count-selector">
            <h3><i class="fas fa-layer-group"></i> Choose How Many Products to Edit</h3>
            <div class="count-selector-group">
                <label for="product-count">Number of Products:</label>
                <input type="number" id="product-count" min="1" max="20" value="1" onchange="handleProductCountChange()" style="width: 80px; text-align: center;">
            </div>
            <button type="button" class="generate-forms-btn" onclick="generateProductForms()">
                <i class="fas fa-magic"></i> Generate Forms
            </button>
        </div>

        <!-- Single Product Form (Default) -->
        <form method="POST" enctype="multipart/form-data" id="single-product-form" class="single-product-form form-active">
            <!-- Basic Information -->
            <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h2>
                        <div class="form-grid">
            <div class="form-group">
                                <label for="name">Product Name *</label>
                                                            <input type="text" id="name" name="name" required placeholder="Enter product name" value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="form-group">
                                <label for="price">Price *</label>
                                                            <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00" value="<?php echo $product['price']; ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                                <select id="category" name="category" required onchange="loadSubcategories(); togglePerfumeFields(this.value);">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['name']); ?>" <?php echo ($product['category'] === $category['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" id="subcategory-group">
                <label for="subcategory">Subcategory</label>
                <select id="subcategory" name="subcategory">
                    <option value="">Select Subcategory</option>
                    <?php if (!empty($product['subcategory'])): ?>
                        <option value="<?php echo htmlspecialchars($product['subcategory']); ?>" selected>
                            <?php echo htmlspecialchars($product['subcategory']); ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group" id="brand-group" style="display: none;">
                <label for="brand">Brand *</label>
                <select id="brand" name="brand">
                    <option value="">Select Brand</option>
                    <option value="Valentino" <?php echo ($product['brand'] ?? '') === 'Valentino' ? 'selected' : ''; ?>>Valentino</option>
                    <option value="Chanel" <?php echo ($product['brand'] ?? '') === 'Chanel' ? 'selected' : ''; ?>>Chanel</option>
                    <option value="Dior" <?php echo ($product['brand'] ?? '') === 'Dior' ? 'selected' : ''; ?>>Dior</option>
                    <option value="Gucci" <?php echo ($product['brand'] ?? '') === 'Gucci' ? 'selected' : ''; ?>>Gucci</option>
                    <option value="Yves Saint Laurent" <?php echo ($product['brand'] ?? '') === 'Yves Saint Laurent' ? 'selected' : ''; ?>>Yves Saint Laurent</option>
                    <option value="Tom Ford" <?php echo ($product['brand'] ?? '') === 'Tom Ford' ? 'selected' : ''; ?>>Tom Ford</option>
                    <option value="Versace" <?php echo ($product['brand'] ?? '') === 'Versace' ? 'selected' : ''; ?>>Versace</option>
                    <option value="Prada" <?php echo ($product['brand'] ?? '') === 'Prada' ? 'selected' : ''; ?>>Prada</option>
                    <option value="Bvlgari" <?php echo ($product['brand'] ?? '') === 'Bvlgari' ? 'selected' : ''; ?>>Bvlgari</option>
                    <option value="Armani" <?php echo ($product['brand'] ?? '') === 'Armani' ? 'selected' : ''; ?>>Armani</option>
                    <option value="Calvin Klein" <?php echo ($product['brand'] ?? '') === 'Calvin Klein' ? 'selected' : ''; ?>>Calvin Klein</option>
                    <option value="Ralph Lauren" <?php echo ($product['brand'] ?? '') === 'Ralph Lauren' ? 'selected' : ''; ?>>Ralph Lauren</option>
                    <option value="Balenciaga" <?php echo ($product['brand'] ?? '') === 'Balenciaga' ? 'selected' : ''; ?>>Balenciaga</option>
                    <option value="Givenchy" <?php echo ($product['brand'] ?? '') === 'Givenchy' ? 'selected' : ''; ?>>Givenchy</option>
                    <option value="Hermès" <?php echo ($product['brand'] ?? '') === 'Hermès' ? 'selected' : ''; ?>>Hermès</option>
                    <option value="Jo Malone" <?php echo ($product['brand'] ?? '') === 'Jo Malone' ? 'selected' : ''; ?>>Jo Malone</option>
                    <option value="Marc Jacobs" <?php echo ($product['brand'] ?? '') === 'Marc Jacobs' ? 'selected' : ''; ?>>Marc Jacobs</option>
                    <option value="Viktor&Rolf" <?php echo ($product['brand'] ?? '') === 'Viktor&Rolf' ? 'selected' : ''; ?>>Viktor&Rolf</option>
                    <option value="Maison Margiela" <?php echo ($product['brand'] ?? '') === 'Maison Margiela' ? 'selected' : ''; ?>>Maison Margiela</option>
                    <option value="Byredo" <?php echo ($product['brand'] ?? '') === 'Byredo' ? 'selected' : ''; ?>>Byredo</option>
                </select>
            </div>
            
            <div class="form-group" id="gender-group" style="display: none;">
                <label for="gender">Gender *</label>
                <select id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="men" <?php echo ($product['gender'] ?? '') === 'men' ? 'selected' : ''; ?>>Men</option>
                    <option value="women" <?php echo ($product['gender'] ?? '') === 'women' ? 'selected' : ''; ?>>Women</option>
                    <option value="unisex" <?php echo ($product['gender'] ?? '') === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
                </select>
            </div>
            
            <div class="form-group" id="perfume-size-group" style="display: none;">
                <label for="perfume_size">Size *</label>
                <select id="perfume_size" name="size">
                    <option value="">Select Size</option>
                    <option value="30ml" <?php echo ($product['size'] ?? '') === '30ml' ? 'selected' : ''; ?>>30ml</option>
                    <option value="50ml" <?php echo ($product['size'] ?? '') === '50ml' ? 'selected' : ''; ?>>50ml</option>
                    <option value="100ml" <?php echo ($product['size'] ?? '') === '100ml' ? 'selected' : ''; ?>>100ml</option>
                    <option value="200ml" <?php echo ($product['size'] ?? '') === '200ml' ? 'selected' : ''; ?>>200ml</option>
                </select>
            </div>
            
            <div class="form-group" id="size-category-group">
                <label for="size_category">Size Category</label>
                <select id="size_category" name="size_category" onchange="loadSizeOptions()">
                    <option value="">Select Size Category</option>
                    <option value="clothing" <?php echo ($product['size_category'] ?? '') === 'clothing' ? 'selected' : ''; ?>>Clothing</option>
                    <option value="shoes" <?php echo ($product['size_category'] ?? '') === 'shoes' ? 'selected' : ''; ?>>Shoes</option>
                    <option value="none" <?php echo ($product['size_category'] ?? '') === 'none' ? 'selected' : ''; ?>>No Sizes</option>
                </select>
                </div>
            
            <div class="form-group" id="size_selection_group" style="display: none;">
                <label>Available Sizes</label>
                <div class="size-dropdown-container">
                    <div class="size-dropdown-header" onclick="toggleSizeDropdown()">
                        <span id="selected-sizes-text">Select sizes...</span>
                        <i class="fas fa-chevron-down" id="size-dropdown-icon"></i>
                </div>
                    <div class="size-dropdown-content" id="size-dropdown-content">
                        <!-- Size options will be loaded here -->
                    </div>
                </div>
                <input type="hidden" id="selected_sizes" name="selected_sizes" value="<?php echo htmlspecialchars($product['selected_sizes'] ?? ''); ?>">
            </div>
                        </div>
            </div>
            
                    <!-- Color Panel -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-palette"></i> Color Selection</h2>
                        <div class="color-panel">
                            <label for="color">Main Product Color</label>
                            <input type="color" id="color" name="color" class="color-input" value="<?php echo $product['color'] ?? '#667eea'; ?>">
                        </div>
                </div>

                            <!-- Media Uploads -->
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-images"></i> Product Media</h2>
            <div class="image-upload-section">
                                    <h3><i class="fas fa-upload"></i> Upload Product Media</h3>
                        <p>Add front and back images or videos for your product</p>
                        <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 15px 0; font-size: 0.9rem;">
                            <strong>Supported Formats:</strong><br>
                            <span style="color: #28a745;">✓ Images:</span> JPG, PNG, GIF, WebP<br>
                            <span style="color: #007bff;">✓ Videos:</span> MP4, WebM, MOV, AVI, MKV<br>
                            <span style="color: #ffc107;">⚠ Max Video Size:</span> 50MB per file
                        </div>
                <div class="image-inputs">
                    <div class="image-input-group">
                    <label for="front_image">Front Media *</label>
                                                        <input type="file" id="front_image" name="front_image" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewMedia(this, 'front-preview')">
                    <input type="hidden" name="existing_front_image" value="<?php echo htmlspecialchars($product['front_image'] ?? ''); ?>">
                        <div id="front-preview" class="image-preview">
                            <?php 
                            // Try to get image from color variants first
                            $displayImage = '';
                            if (isset($product['color_variants']) && !empty($product['color_variants'])) {
                                $colorVariants = (array)$product['color_variants'];
                                if (is_array($colorVariants) && !empty($colorVariants)) {
                                    $firstVariant = $colorVariants[0];
                                    if (isset($firstVariant['images']) && !empty($firstVariant['images'])) {
                                        $images = (array)$firstVariant['images'];
                                        if (!empty($images)) {
                                            $displayImage = $images[0];
                                        }
                                    }
                                }
                            }
                            
                            // Fallback to front_image if no color variant images
                            if (empty($displayImage)) {
                                $displayImage = $product['front_image'] ?? '';
                            }
                            
                            if (!empty($displayImage)): 
                            ?>
                                <?php if (pathinfo($displayImage, PATHINFO_EXTENSION) === 'mp4' || pathinfo($displayImage, PATHINFO_EXTENSION) === 'webm' || pathinfo($displayImage, PATHINFO_EXTENSION) === 'mov'): ?>
                                    <video controls style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                        <source src="../<?php echo htmlspecialchars($displayImage); ?>" type="video/<?php echo pathinfo($displayImage, PATHINFO_EXTENSION); ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($displayImage); ?>" alt="Product Image">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="no-image">No media selected</div>
        <?php endif; ?>
    </div>
                    </div>
                    <div class="image-input-group">
                    <label for="back_image">Back Media *</label>
                        <input type="file" id="back_image" name="back_image" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewMedia(this, 'back-preview')">
                    <input type="hidden" name="existing_back_image" value="<?php echo htmlspecialchars($product['back_image'] ?? ''); ?>">
                        <div id="back-preview" class="image-preview">
                            <?php if (!empty($product['back_image'])): ?>
                                <?php if (pathinfo($product['back_image'], PATHINFO_EXTENSION) === 'mp4' || pathinfo($product['back_image'], PATHINFO_EXTENSION) === 'webm' || pathinfo($product['back_image'], PATHINFO_EXTENSION) === 'mov'): ?>
                                    <video controls style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                        <source src="../<?php echo htmlspecialchars($product['back_image']); ?>" type="video/<?php echo pathinfo($product['back_image'], PATHINFO_EXTENSION); ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($product['back_image']); ?>" alt="Back Media">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="no-image">No media selected</div>
<?php endif; ?>
    </div>
                    </div>
                </div>
            </div>
        </div>

                    <!-- Color Variants -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-palette"></i> Color Variants</h2>
                        <div class="color-variants-section">
                            <p>Add or edit color variants for your product</p>
                            <div id="color-variants-container">
                                <?php 
                                $colorVariants = toArray($product['color_variants'] ?? []);
                                if (!empty($colorVariants)): 
                                ?>
                                    <?php foreach ($colorVariants as $index => $variant): ?>
                                        <div class="variant-item">
                                        <div class="variant-header">
                                            <h4>Color Variant #<?php echo $index + 1; ?></h4>
                                            <button type="button" class="delete-variant-btn" onclick="removeColorVariant(this)">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete Variant</span>
                                            </button>
                                        </div>
            
            <div class="form-group">
                                                <label>Variant Name *</label>
                                                <input type="text" name="color_variants[<?php echo $index; ?>][name]" required value="<?php echo htmlspecialchars($variant['name']); ?>">
                </div>
            
            <div class="form-group">
                                                <label>Color *</label>
                                            <div class="color-input-group">
                                                <input type="color" name="color_variants[<?php echo $index; ?>][color]" class="variant-color-input" required value="<?php echo htmlspecialchars($variant['color']); ?>" onchange="updateColorCircle(this, <?php echo $index; ?>)">
                                                <div class="admin-color-circle" id="admin-color-circle-<?php echo $index; ?>" style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" data-variant-index="<?php echo $index; ?>" onclick="showVariantImages(<?php echo $index; ?>)"></div>
            </div>
                </div>

                                            <div class="variant-image-inputs">
                                                <div class="image-input-group">
                                                    <label>Front Media</label>
                                                    <input type="file" name="color_variants[<?php echo $index; ?>][front_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewVariantMedia(this, 'variant-front-<?php echo $index; ?>')">
                                                <input type="hidden" name="color_variants[<?php echo $index; ?>][existing_front_image]" value="<?php echo htmlspecialchars($variant['front_image'] ?? ''); ?>">
                                                    <div id="variant-front-<?php echo $index; ?>" class="variant-image-preview">
                                                        <?php 
                                                        // Check for images array first (new structure)
                                                        $variantImages = [];
                                                        if (isset($variant['images']) && !empty($variant['images'])) {
                                                            $variantImages = (array)$variant['images'];
                                                        }
                                                        
                                                        // Fallback to front_image (old structure)
                                                        if (empty($variantImages) && !empty($variant['front_image'])) {
                                                            $variantImages = [$variant['front_image']];
                                                        }
                                                        
                                                        if (!empty($variantImages)): 
                                                            $firstImage = $variantImages[0];
                                                        ?>
                                                            <?php if (pathinfo($firstImage, PATHINFO_EXTENSION) === 'mp4' || pathinfo($firstImage, PATHINFO_EXTENSION) === 'webm' || pathinfo($firstImage, PATHINFO_EXTENSION) === 'mov'): ?>
                                                                <video controls style="max-width: 150px; max-height: 150px; border-radius: 6px;">
                                                                    <source src="../<?php echo htmlspecialchars($firstImage); ?>" type="video/<?php echo pathinfo($firstImage, PATHINFO_EXTENSION); ?>">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            <?php else: ?>
                                                                <img src="../<?php echo htmlspecialchars($firstImage); ?>" alt="Variant Image">
                                                            <?php endif; ?>
                                                            <?php if (count($variantImages) > 1): ?>
                                                                <div class="image-count">+<?php echo count($variantImages) - 1; ?> more</div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <div class="no-image">No media</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="image-input-group">
                                                    <label>Back Media</label>
                                                    <input type="file" name="color_variants[<?php echo $index; ?>][back_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewVariantMedia(this, 'variant-back-<?php echo $index; ?>')">
                                                <input type="hidden" name="color_variants[<?php echo $index; ?>][existing_back_image]" value="<?php echo htmlspecialchars($variant['back_image'] ?? ''); ?>">
                                                    <div id="variant-back-<?php echo $index; ?>" class="variant-image-preview">
                                                        <?php if (!empty($variant['back_image'])): ?>
                                                            <?php if (pathinfo($variant['back_image'], PATHINFO_EXTENSION) === 'mp4' || pathinfo($variant['back_image'], PATHINFO_EXTENSION) === 'webm' || pathinfo($variant['back_image'], PATHINFO_EXTENSION) === 'mov'): ?>
                                                                <video controls style="max-width: 150px; max-height: 150px; border-radius: 6px;">
                                                                    <source src="../<?php echo htmlspecialchars($variant['back_image']); ?>" type="video/<?php echo pathinfo($variant['back_image'], PATHINFO_EXTENSION); ?>">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            <?php else: ?>
                                                                <img src="../<?php echo htmlspecialchars($variant['back_image']); ?>" alt="Variant Back">
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <div class="no-image">No media</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <button type="button" class="add-variant-btn" onclick="addColorVariant()">
                                <i class="fas fa-plus"></i> Add Color Variant
                            </button>
                        
                        <!-- Hidden field to track deleted variants -->
                        <input type="hidden" id="deleted-variants" name="deleted_variants" value="">
                </div>
            </div>
            
                    <!-- Additional Information -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Additional Information</h2>
            <div class="form-group">
                <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter detailed product description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
                        <div class="checkbox-group">
                            <input type="checkbox" id="featured" name="featured" <?php echo ($product['featured'] ?? false) ? 'checked' : ''; ?>>
                            <label for="featured">Featured Product</label>
            </div>
            
                        <div class="checkbox-group">
                        <input type="checkbox" id="sale" name="sale" onchange="toggleSalePrice()" <?php echo ($product['sale'] ?? false) ? 'checked' : ''; ?>>
                            <label for="sale">On Sale</label>
            </div>
            
                        <div class="form-group" id="salePriceGroup" style="display: <?php echo ($product['sale'] ?? false) ? 'block' : 'none'; ?>;">
                            <label for="salePrice">Sale Price</label>
                        <input type="number" id="salePrice" name="salePrice" step="0.01" min="0" placeholder="Enter sale price" value="<?php echo $product['salePrice'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock Quantity</label>
                            <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock'] ?? 0; ?>" placeholder="Enter stock quantity">
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="available" name="available" <?php echo ($product['available'] ?? true) ? 'checked' : ''; ?>>
                            <label for="available">Available for Purchase</label>
                        </div>
            </div>
            
                <!-- Action Buttons -->
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
            <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Update Product
            </button>
                                        <button type="button" class="clear-btn" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset to Original
            </button>
                </div>
        </form>

        <!-- Multi-Product Form -->
        <form method="POST" enctype="multipart/form-data" id="multi-product-form" class="multi-product-form">
            <div id="multi-product-forms-container">
                <!-- Dynamic product forms will be generated here -->
            </div>
        </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !mobileBtn.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Existing JavaScript functions
        function loadSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const category = categorySelect.value;
            const currentSubcategory = '<?php echo $product['subcategory'] ?? ''; ?>';
            
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (!category) return;

            fetch(`get-subcategories.php?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    const subcategories = data.subcategories || data;
                    if (subcategories && subcategories.length > 0) {
                        subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            // Handle both string and object formats
                            const subName = typeof sub === 'string' ? sub : sub.name;
                            option.value = subName;
                            option.textContent = subName;
                            if (subName === currentSubcategory) {
                                option.selected = true;
                            }
                            subcategorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading subcategories:', error));
        }

        function togglePerfumeFields(category) {
            const brandGroup = document.getElementById('brand-group');
            const genderGroup = document.getElementById('gender-group');
            const perfumeSizeGroup = document.getElementById('perfume-size-group');
            const subcategoryGroup = document.getElementById('subcategory-group');
            const sizeCategoryGroup = document.getElementById('size-category-group');
            
            const shouldShow = category.toLowerCase() === 'perfumes';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = shouldShow ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = shouldShow ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = shouldShow ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = shouldShow ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = shouldShow ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById('brand');
            const genderField = document.getElementById('gender');
            const sizeField = document.getElementById('perfume_size');
            
            if (brandField) brandField.required = category.toLowerCase() === 'perfumes';
            if (genderField) genderField.required = category.toLowerCase() === 'perfumes';
            if (sizeField) sizeField.required = category.toLowerCase() === 'perfumes';
        }

        function toggleSalePrice() {
            const saleCheckbox = document.getElementById('sale');
            const salePriceGroup = document.getElementById('salePriceGroup');
            salePriceGroup.style.display = saleCheckbox.checked ? 'block' : 'none';
        }

                        let colorVariantIndex = <?php echo getCount($product['color_variants'] ?? []); ?>;
        let deletedVariants = []; // Track deleted variants

        function addColorVariant() {
            const container = document.getElementById('color-variants-container');
            const variantHtml = `
                <div class="variant-item">
                    <div class="variant-header">
                        <h4>Color Variant #${colorVariantIndex + 1}</h4>
                        <button type="button" class="delete-variant-btn" onclick="removeColorVariant(this)">
                            <i class="fas fa-trash"></i>
                            <span>Delete Variant</span>
                    </button>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="color_variants[${colorVariantIndex}][name]" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Color *</label>
                        <div class="color-input-group">
                            <input type="color" name="color_variants[${colorVariantIndex}][color]" class="variant-color-input" required onchange="updateColorCircle(this, ${colorVariantIndex})">
                            <div class="admin-color-circle" id="admin-color-circle-${colorVariantIndex}" style="background-color: #000000;" data-variant-index="${colorVariantIndex}" onclick="showVariantImages(${colorVariantIndex})"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Size Category</label>
                        <select name="color_variants[${colorVariantIndex}][size_category]" onchange="loadVariantSizeOptions(${colorVariantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="none">No Sizes</option>
                        </select>
                    </div>
                    
                    <div class="form-group variant-size-selection" id="variant-size-selection-${colorVariantIndex}" style="display: none;">
                        <label>Variant Available Sizes</label>
                        <div class="size-dropdown-container">
                            <div class="size-dropdown-header" onclick="toggleVariantSizeDropdown(${colorVariantIndex})">
                                <span id="variant-selected-sizes-text-${colorVariantIndex}">Select sizes...</span>
                                <i class="fas fa-chevron-down" id="variant-size-dropdown-icon-${colorVariantIndex}"></i>
                            </div>
                            <div class="size-dropdown-content" id="variant-size-dropdown-content-${colorVariantIndex}">
                                <!-- Size options will be loaded here -->
                            </div>
                        </div>
                        <input type="hidden" name="color_variants[${colorVariantIndex}][selected_sizes]" value="">
                    </div>

                                        <div class="variant-image-inputs">
                        <div class="image-input-group">
                            <label>Front Media</label>
                            <input type="file" name="color_variants[${colorVariantIndex}][front_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewVariantMedia(this, 'variant-front-${colorVariantIndex}')">
                            <div id="variant-front-${colorVariantIndex}" class="variant-image-preview">
                                <div class="no-image">No media selected</div>
                        </div>
                    </div>

                        <div class="image-input-group">
                            <label>Back Media</label>
                            <input type="file" name="color_variants[${colorVariantIndex}][back_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewVariantMedia(this, 'variant-back-${colorVariantIndex}')">
                            <div id="variant-back-${colorVariantIndex}" class="variant-image-preview">
                                <div class="no-image">No media selected</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', variantHtml);
            colorVariantIndex++;
        }

        function removeColorVariant(button) {
            const variantItem = button.closest('.variant-item');
            const variantName = variantItem.querySelector('input[name*="[name]"]')?.value || 'this variant';
            
            // Show elegant modal for confirmation
            showModal({
                title: 'Delete Color Variant',
                message: `Are you sure you want to delete <strong>${variantName}</strong>? This action cannot be undone.`,
                type: 'warning',
                buttons: [
                    {
                        text: 'Cancel',
                        class: 'btn-secondary',
                        action: 'close'
                    },
                    {
                        text: 'Delete',
                        class: 'btn-danger',
                        action: () => {
                            // Close the confirmation modal first
                            closeModal();
                            
                            // Get the original variant index before removal
                            const originalIndex = getOriginalVariantIndex(variantItem);
                            if (originalIndex !== -1) {
                                deletedVariants.push(originalIndex);
                    
                            }
                            
                            // Remove the variant item from DOM
                            variantItem.remove();
                            
                            // Reindex remaining variants
                            reindexVariants();
                            
                
                            
                            // Show success message after a short delay
                            setTimeout(() => {
                                showModal({
                                    title: 'Success',
                                    message: 'Color variant has been deleted successfully.',
                                    type: 'success',
                                    buttons: [
                                        {
                                            text: 'OK',
                                            class: 'btn-primary',
                                            action: 'close'
                                        }
                                    ]
                                });
                            }, 300);
                        }
                    }
                ]
            });
        }

        function getOriginalVariantIndex(variantItem) {
            // Try to find the original index from the input names
            const nameInput = variantItem.querySelector('input[name*="[name]"]');
            if (nameInput) {
                const match = nameInput.name.match(/color_variants\[(\d+)\]/);
                if (match) {
                    return parseInt(match[1]);
                }
            }
            return -1;
        }

        function reindexVariants() {
            const variantItems = document.querySelectorAll('.variant-item');
            
            variantItems.forEach((item, newIndex) => {
                // Update the variant number in the header
                const header = item.querySelector('h4');
                if (header) {
                    header.textContent = `Color Variant #${newIndex + 1}`;
                }
                
                // Update all input names to use the new index
                const inputs = item.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const oldName = input.name;
                    if (oldName && oldName.includes('color_variants[')) {
                        // Extract the field name from the old name
                        const fieldMatch = oldName.match(/color_variants\[\d+\]\[([^\]]+)\]/);
                        if (fieldMatch) {
                            const fieldName = fieldMatch[1];
                            input.name = `color_variants[${newIndex}][${fieldName}]`;
                        }
                    }
                });
                
                // Update color circle ID and data attribute
                const colorCircle = item.querySelector('.admin-color-circle');
                if (colorCircle) {
                    colorCircle.id = `admin-color-circle-${newIndex}`;
                    colorCircle.setAttribute('data-variant-index', newIndex);
                    // Update the onclick attribute
                    colorCircle.onclick = function() { showVariantImages(newIndex); };
                }
                
                // Update color input onchange
                const colorInput = item.querySelector('.variant-color-input');
                if (colorInput) {
                    colorInput.onchange = function() { updateColorCircle(this, newIndex); };
                }
                
                // Update size category onchange
                const sizeCategorySelect = item.querySelector('select[name*="[size_category]"]');
                if (sizeCategorySelect) {
                    sizeCategorySelect.onchange = function() { loadVariantSizeOptions(newIndex); };
                }
                
                // Update size dropdown onclick
                const sizeDropdownHeader = item.querySelector('.size-dropdown-header');
                if (sizeDropdownHeader) {
                    sizeDropdownHeader.onclick = function() { toggleVariantSizeDropdown(newIndex); };
                }
                
                // Update preview IDs
                const frontPreview = item.querySelector('#variant-front-\\d+');
                if (frontPreview) {
                    frontPreview.id = `variant-front-${newIndex}`;
                }
                
                const backPreview = item.querySelector('#variant-back-\\d+');
                if (backPreview) {
                    backPreview.id = `variant-back-${newIndex}`;
                }
                
                // Update file input onchange attributes
                const frontImageInput = item.querySelector('input[name*="[front_image]"]');
                if (frontImageInput) {
                    frontImageInput.onchange = function() { previewVariantImage(this, `variant-front-${newIndex}`); };
                }
                
                const backImageInput = item.querySelector('input[name*="[back_image]"]');
                if (backImageInput) {
                    backImageInput.onchange = function() { previewVariantImage(this, `variant-back-${newIndex}`); };
                }
            });
            
            // Update the global colorVariantIndex
            colorVariantIndex = variantItems.length;
        }

        // Admin Color Circle Functions
        function updateColorCircle(colorInput, variantIndex) {
            const colorCircle = document.getElementById(`admin-color-circle-${variantIndex}`);
            if (colorCircle) {
                colorCircle.style.backgroundColor = colorInput.value;
            }
        }

        function showVariantImages(variantIndex) {
    
            
            // Remove active class from all color circles
            document.querySelectorAll('.admin-color-circle').forEach(circle => {
                circle.classList.remove('active');
            });

            // Add active class to clicked circle
            const clickedCircle = document.getElementById(`admin-color-circle-${variantIndex}`);
            if (clickedCircle) {
                clickedCircle.classList.add('active');
                

            // Show the variant images
            const variantItem = clickedCircle ? clickedCircle.closest('.variant-item') : null;
            if (variantItem) {
                // Scroll to the variant images
                const imageInputs = variantItem.querySelector('.variant-image-inputs');
                if (imageInputs) {
                    imageInputs.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
    
                    imageInputs.style.backgroundColor = '#e3f2fd';
                    setTimeout(() => {
                        imageInputs.style.backgroundColor = '';
                    }, 2000);
                    
        
                }
            }


        }

        // Size Dropdown Functions
        let selectedSizes = new Set();
        let variantSelectedSizes = {};

        function loadSizeOptions() {
            const sizeCategory = document.getElementById('size_category').value;
            const sizeSelectionGroup = document.getElementById('size_selection_group');
            const sizeDropdownContent = document.getElementById('size-dropdown-content');
            
            if (sizeCategory === 'none' || sizeCategory === '') {
                sizeSelectionGroup.style.display = 'none';
                selectedSizes.clear();
                updateSelectedSizesDisplay();
                return;
            }
            
            sizeSelectionGroup.style.display = 'block';
            
            if (sizeCategory === 'clothing') {
                sizeDropdownContent.innerHTML = generateClothingSizes(false, null);
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateShoeSizes(false, null);
            }
            
            // Add event listeners to category headers
            setTimeout(() => {
                document.querySelectorAll('.size-category-header').forEach(header => {
                    header.addEventListener('click', function() {
                        this.classList.toggle('expanded');
                        const options = this.nextElementSibling;
                        options.classList.toggle('show');
                    });
                });
            }, 100);
        }

        function generateClothingSizes() {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('infant')">
                            <input type="checkbox" id="select_all_infant" name="sizes[]" value="select_all_infant">
                            <label for="select_all_infant">✓ Select All Infant</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('0M')">
                            <input type="checkbox" id="size_0M" name="sizes[]" value="0M">
                            <label for="size_0M">0M (EU 50)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('3M')">
                            <input type="checkbox" id="size_3M" name="sizes[]" value="3M">
                            <label for="size_3M">3M (EU 56)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('6M')">
                            <input type="checkbox" id="size_6M" name="sizes[]" value="6M">
                            <label for="size_6M">6M (EU 62)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('9M')">
                            <input type="checkbox" id="size_9M" name="sizes[]" value="9M">
                            <label for="size_9M">9M (EU 68)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('12M')">
                            <input type="checkbox" id="size_12M" name="sizes[]" value="12M">
                            <label for="size_12M">12M (EU 74)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('18M')">
                            <input type="checkbox" id="size_18M" name="sizes[]" value="18M">
                            <label for="size_18M">18M (EU 80)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('24M')">
                            <input type="checkbox" id="size_24M" name="sizes[]" value="24M">
                            <label for="size_24M">24M (EU 86)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('toddler')">
                            <input type="checkbox" id="select_all_toddler" name="sizes[]" value="select_all_toddler">
                            <label for="select_all_toddler">✓ Select All Toddler</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('2T')">
                            <input type="checkbox" id="size_2T" name="sizes[]" value="2T">
                            <label for="size_2T">2T (EU 92)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('3T')">
                            <input type="checkbox" id="size_3T" name="sizes[]" value="3T">
                            <label for="size_3T">3T (EU 98)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('4T')">
                            <input type="checkbox" id="size_4T" name="sizes[]" value="4T">
                            <label for="size_4T">4T (EU 104)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children (4-14 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('children')">
                            <input type="checkbox" id="select_all_children" name="sizes[]" value="select_all_children">
                            <label for="select_all_children">✓ Select All Children</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('4Y')">
                            <input type="checkbox" id="size_4Y" name="sizes[]" value="4Y">
                            <label for="size_4Y">4Y (EU 110)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('5Y')">
                            <input type="checkbox" id="size_5Y" name="sizes[]" value="5Y">
                            <label for="size_5Y">5Y (EU 116)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('6Y')">
                            <input type="checkbox" id="size_6Y" name="sizes[]" value="6Y">
                            <label for="size_6Y">6Y (EU 122)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('7Y')">
                            <input type="checkbox" id="size_7Y" name="sizes[]" value="7Y">
                            <label for="size_7Y">7Y (EU 128)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('8Y')">
                            <input type="checkbox" id="size_8Y" name="sizes[]" value="8Y">
                            <label for="size_8Y">8Y (EU 134)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('10Y')">
                            <input type="checkbox" id="size_10Y" name="sizes[]" value="10Y">
                            <label for="size_10Y">10Y (EU 140)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('12Y')">
                            <input type="checkbox" id="size_12Y" name="sizes[]" value="12Y">
                            <label for="size_12Y">12Y (EU 146)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('14Y')">
                            <input type="checkbox" id="size_14Y" name="sizes[]" value="14Y">
                            <label for="size_14Y">14Y (EU 152)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('women')">
                            <input type="checkbox" id="select_all_women" name="sizes[]" value="select_all_women">
                            <label for="select_all_women">✓ Select All Women</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('X')">
                            <input type="checkbox" id="size_X" name="sizes[]" value="X">
                            <label for="size_X">X (EU 34-36)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('S')">
                            <input type="checkbox" id="size_S" name="sizes[]" value="S">
                            <label for="size_S">S (EU 36-38)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('M')">
                            <input type="checkbox" id="size_M" name="sizes[]" value="M">
                            <label for="size_M">M (EU 38-40)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('L')">
                            <input type="checkbox" id="size_L" name="sizes[]" value="L">
                            <label for="size_L">L (EU 40-42)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('XL')">
                            <input type="checkbox" id="size_XL" name="sizes[]" value="XL">
                            <label for="size_XL">XL (EU 42-44)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('XXL')">
                            <input type="checkbox" id="size_XXL" name="sizes[]" value="XXL">
                            <label for="size_XXL">XXL (EU 44-46)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('men')">
                            <input type="checkbox" id="select_all_men" name="sizes[]" value="select_all_men">
                            <label for="select_all_men">✓ Select All Men</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('X')">
                            <input type="checkbox" id="size_MX" name="sizes[]" value="X">
                            <label for="size_MX">X (EU 46-48)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('S')">
                            <input type="checkbox" id="size_MS" name="sizes[]" value="S">
                            <label for="size_MS">S (EU 48-50)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('M')">
                            <input type="checkbox" id="size_MM" name="sizes[]" value="M">
                            <label for="size_MM">M (EU 50-52)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('L')">
                            <input type="checkbox" id="size_ML" name="sizes[]" value="L">
                            <label for="size_ML">L (EU 52-54)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('XL')">
                            <input type="checkbox" id="size_MXL" name="sizes[]" value="XL">
                            <label for="size_MXL">XL (EU 54-56)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('XXL')">
                            <input type="checkbox" id="size_MXXL" name="sizes[]" value="XXL">
                            <label for="size_MXXL">XXL (EU 56-58)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateShoeSizes(isVariant = false, variantIndex = null) {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby Shoes (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'infant_shoes')` : 'selectAllInCategory(\'infant_shoes\')'}">
                            <input type="checkbox" id="select_all_infant_shoes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_infant_shoes">
                            <label for="select_all_infant_shoes${isVariant ? '_' + variantIndex : ''}">✓ Select All Infant Shoes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '16')` : 'toggleSize(\'16\')'}">
                            <input type="checkbox" id="size_16${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="16">
                            <label for="size_16${isVariant ? '_' + variantIndex : ''}">16 (EU 16)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '17')` : 'toggleSize(\'17\')'}">
                            <input type="checkbox" id="size_17${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="17">
                            <label for="size_17${isVariant ? '_' + variantIndex : ''}">17 (EU 17)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '18')` : 'toggleSize(\'18\')'}">
                            <input type="checkbox" id="size_18${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="18">
                            <label for="size_18${isVariant ? '_' + variantIndex : ''}">18 (EU 18)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '19')` : 'toggleSize(\'19\')'}">
                            <input type="checkbox" id="size_19${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="19">
                            <label for="size_19${isVariant ? '_' + variantIndex : ''}">19 (EU 19)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '20')` : 'toggleSize(\'20\')'}">
                            <input type="checkbox" id="size_20${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="20">
                            <label for="size_20${isVariant ? '_' + variantIndex : ''}">20 (EU 20)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '21')` : 'toggleSize(\'21\')'}">
                            <input type="checkbox" id="size_21${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="21">
                            <label for="size_21${isVariant ? '_' + variantIndex : ''}">21 (EU 21)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '22')` : 'toggleSize(\'22\')'}">
                            <input type="checkbox" id="size_22${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="22">
                            <label for="size_22${isVariant ? '_' + variantIndex : ''}">22 (EU 22)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children Shoes (1-7 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('children_shoes')">
                            <input type="checkbox" id="select_all_children_shoes" name="sizes[]" value="select_all_children_shoes">
                            <label for="select_all_children_shoes">✓ Select All Children Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('23')">
                            <input type="checkbox" id="size_23" name="sizes[]" value="23">
                            <label for="size_23">23 (EU 23)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('24')">
                            <input type="checkbox" id="size_24" name="sizes[]" value="24">
                            <label for="size_24">24 (EU 24)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('25')">
                            <input type="checkbox" id="size_25" name="sizes[]" value="25">
                            <label for="size_25">25 (EU 25)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('26')">
                            <input type="checkbox" id="size_26" name="sizes[]" value="26">
                            <label for="size_26">26 (EU 26)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('27')">
                            <input type="checkbox" id="size_27" name="sizes[]" value="27">
                            <label for="size_27">27 (EU 27)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('28')">
                            <input type="checkbox" id="size_28" name="sizes[]" value="28">
                            <label for="size_28">28 (EU 28)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('29')">
                            <input type="checkbox" id="size_29" name="sizes[]" value="29">
                            <label for="size_29">29 (EU 29)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('30')">
                            <input type="checkbox" id="size_30" name="sizes[]" value="30">
                            <label for="size_30">30 (EU 30)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women Shoes (EU 35-42)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('women_shoes')">
                            <input type="checkbox" id="select_all_women_shoes" name="sizes[]" value="select_all_women_shoes">
                            <label for="select_all_women_shoes">✓ Select All Women Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('35')">
                            <input type="checkbox" id="size_35" name="sizes[]" value="35">
                            <label for="size_35">35 (EU 35)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('36')">
                            <input type="checkbox" id="size_36" name="sizes[]" value="36">
                            <label for="size_36">36 (EU 36)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('37')">
                            <input type="checkbox" id="size_37" name="sizes[]" value="37">
                            <label for="size_37">37 (EU 37)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('38')">
                            <input type="checkbox" id="size_38" name="sizes[]" value="38">
                            <label for="size_38">38 (EU 38)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('39')">
                            <input type="checkbox" id="size_39" name="sizes[]" value="39">
                            <label for="size_39">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('40')">
                            <input type="checkbox" id="size_40" name="sizes[]" value="40">
                            <label for="size_40">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('41')">
                            <input type="checkbox" id="size_41" name="sizes[]" value="41">
                            <label for="size_41">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('42')">
                            <input type="checkbox" id="size_42" name="sizes[]" value="42">
                            <label for="size_42">42 (EU 42)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men Shoes (EU 39-47)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInCategory('men_shoes')">
                            <input type="checkbox" id="select_all_men_shoes" name="sizes[]" value="select_all_men_shoes">
                            <label for="select_all_men_shoes">✓ Select All Men Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('39')">
                            <input type="checkbox" id="size_M39" name="sizes[]" value="39">
                            <label for="size_M39">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('40')">
                            <input type="checkbox" id="size_M40" name="sizes[]" value="40">
                            <label for="size_M40">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('41')">
                            <input type="checkbox" id="size_M41" name="sizes[]" value="41">
                            <label for="size_M41">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('42')">
                            <input type="checkbox" id="size_M42" name="sizes[]" value="42">
                            <label for="size_M42">42 (EU 42)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('43')">
                            <input type="checkbox" id="size_M43" name="sizes[]" value="43">
                            <label for="size_M43">43 (EU 43)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('44')">
                            <input type="checkbox" id="size_M44" name="sizes[]" value="44">
                            <label for="size_M44">44 (EU 44)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('45')">
                            <input type="checkbox" id="size_M45" name="sizes[]" value="45">
                            <label for="size_M45">45 (EU 45)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('46')">
                            <input type="checkbox" id="size_M46" name="sizes[]" value="46">
                            <label for="size_M46">46 (EU 46)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('47')">
                            <input type="checkbox" id="size_M47" name="sizes[]" value="47">
                            <label for="size_M47">47 (EU 47)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function toggleSizeDropdown() {
            const dropdownContent = document.getElementById('size-dropdown-content');
            const dropdownHeader = document.querySelector('.size-dropdown-header');
            const dropdownIcon = document.getElementById('size-dropdown-icon');
            
            dropdownContent.classList.toggle('show');
            dropdownHeader.classList.toggle('active');
            
            if (dropdownContent.classList.contains('show')) {
                dropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleSize(size) {
            const checkbox = document.querySelector(`input[value="${size}"]`);
            const sizeOption = checkbox.closest('.size-option');
            
            if (selectedSizes.has(size)) {
                selectedSizes.delete(size);
                checkbox.checked = false;
                sizeOption.classList.remove('selected');
            } else {
                selectedSizes.add(size);
                checkbox.checked = true;
                sizeOption.classList.add('selected');
            }
            
            updateSelectedSizesDisplay();
        }

        function updateSelectedSizesDisplay() {
            const selectedSizesText = document.getElementById('selected-sizes-text');
            const selectedSizesInput = document.getElementById('selected_sizes');
            
            if (selectedSizes.size === 0) {
                selectedSizesText.textContent = 'Select sizes...';
                selectedSizesInput.value = '';
                } else {
                const sizesArray = Array.from(selectedSizes).sort();
                selectedSizesText.textContent = `${sizesArray.length} size(s) selected`;
                selectedSizesInput.value = JSON.stringify(sizesArray);
            }
        }

        // Function to select all sizes in a specific category
        function selectAllInCategory(category) {
            const sizeCategory = document.getElementById('size_category').value;
            const sizeDropdownContent = document.getElementById('size-dropdown-content');
            
            // Define size mappings for each category
            const categorySizes = {
                'infant': ['0M', '3M', '6M', '9M', '12M', '18M', '24M'],
                'toddler': ['2T', '3T', '4T'],
                'children': ['4Y', '5Y', '6Y', '7Y', '8Y', '10Y', '12Y', '14Y'],
                'women': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'men': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'infant_shoes': ['16', '17', '18', '19', '20', '21', '22'],
                'children_shoes': ['23', '24', '25', '26', '27', '28', '29', '30'],
                'women_shoes': ['35', '36', '37', '38', '39', '40', '41', '42'],
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47']
            };
            
            const sizesToToggle = categorySizes[category] || [];
            
            // Check if all sizes in this category are already selected
            const allSelected = sizesToToggle.every(size => selectedSizes.has(size));
            
            if (allSelected) {
                // If all are selected, deselect all in this category
                sizesToToggle.forEach(size => {
                    selectedSizes.delete(size);
                    const checkbox = document.querySelector(`input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest('.size-option').classList.remove('selected');
                    }
                });
            } else {
                // If not all are selected, select all in this category
                sizesToToggle.forEach(size => {
                    selectedSizes.add(size);
                    const checkbox = document.querySelector(`input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.size-option').classList.add('selected');
                    }
                });
            }
            
            updateSelectedSizesDisplay();
        }

        // Variant Size Dropdown Functions
        function loadVariantSizeOptions(variantIndex) {
            const variant = document.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).closest('.variant-item');
            const sizeCategory = variant.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).value;
            const sizeSelectionGroup = variant.querySelector(`#variant-size-selection-${variantIndex}`);
            const sizeDropdownContent = variant.querySelector(`#variant-size-dropdown-content-${variantIndex}`);
            
            if (sizeCategory === 'none' || sizeCategory === '') {
                sizeSelectionGroup.style.display = 'none';
                variantSelectedSizes[variantIndex] = new Set();
                updateVariantSelectedSizesDisplay(variantIndex);
                return;
            }
            
            sizeSelectionGroup.style.display = 'block';
            
            if (sizeCategory === 'clothing') {
                sizeDropdownContent.innerHTML = generateClothingSizes(true, variantIndex);
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateShoeSizes(true, variantIndex);
            }
            
            // Add event listeners to category headers and select all buttons
            setTimeout(() => {
                sizeDropdownContent.querySelectorAll('.size-category-header').forEach(header => {
                    header.addEventListener('click', function() {
                        this.classList.toggle('expanded');
                        const options = this.nextElementSibling;
                        options.classList.toggle('show');
                    });
                });
                
                // Add event listeners for select all buttons in variants
                sizeDropdownContent.querySelectorAll('.select-all-option').forEach(option => {
                    const category = option.getAttribute('onclick').match(/selectAllInVariantCategory\((\d+), '([^']+)'\)/);
                    if (category) {
                        option.onclick = () => selectAllInVariantCategory(variantIndex, category[2]);
                    }
                });
            }, 100);
        }

        function toggleVariantSizeDropdown(variantIndex) {
            const dropdownContent = document.getElementById(`variant-size-dropdown-content-${variantIndex}`);
            const dropdownHeader = dropdownContent.previousElementSibling;
            const dropdownIcon = document.getElementById(`variant-size-dropdown-icon-${variantIndex}`);
            
            dropdownContent.classList.toggle('show');
            dropdownHeader.classList.toggle('active');
            
            if (dropdownContent.classList.contains('show')) {
                dropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleVariantSize(variantIndex, size) {
            if (!variantSelectedSizes[variantIndex]) {
                variantSelectedSizes[variantIndex] = new Set();
            }
            
            const variant = document.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).closest('.variant-item');
            const checkbox = variant.querySelector(`input[value="${size}"]`);
            const sizeOption = checkbox.closest('.size-option');
            
            if (variantSelectedSizes[variantIndex].has(size)) {
                variantSelectedSizes[variantIndex].delete(size);
                checkbox.checked = false;
                sizeOption.classList.remove('selected');
            } else {
                variantSelectedSizes[variantIndex].add(size);
                checkbox.checked = true;
                sizeOption.classList.add('selected');
            }
            
            updateVariantSelectedSizesDisplay(variantIndex);
        }

        function updateVariantSelectedSizesDisplay(variantIndex) {
            const selectedSizesText = document.getElementById(`variant-selected-sizes-text-${variantIndex}`);
            const selectedSizesInput = document.querySelector(`[name="color_variants[${variantIndex}][selected_sizes]"]`);
            
            if (!variantSelectedSizes[variantIndex] || variantSelectedSizes[variantIndex].size === 0) {
                selectedSizesText.textContent = 'Select sizes...';
                selectedSizesInput.value = '';
            } else {
                const sizesArray = Array.from(variantSelectedSizes[variantIndex]).sort();
                selectedSizesText.textContent = `${sizesArray.length} size(s) selected`;
                selectedSizesInput.value = JSON.stringify(sizesArray);
            }
        }

        // Function to select all sizes in a specific category for variants
        function selectAllInVariantCategory(variantIndex, category) {
            // Define size mappings for each category
            const categorySizes = {
                'infant': ['0M', '3M', '6M', '9M', '12M', '18M', '24M'],
                'toddler': ['2T', '3T', '4T'],
                'children': ['4Y', '5Y', '6Y', '7Y', '8Y', '10Y', '12Y', '14Y'],
                'women': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'men': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'infant_shoes': ['16', '17', '18', '19', '20', '21', '22'],
                'children_shoes': ['23', '24', '25', '26', '27', '28', '29', '30'],
                'women_shoes': ['35', '36', '37', '38', '39', '40', '41', '42'],
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47']
            };
            
            const sizesToToggle = categorySizes[category] || [];
            
            if (!variantSelectedSizes[variantIndex]) {
                variantSelectedSizes[variantIndex] = new Set();
            }
            
            // Check if all sizes in this category are already selected
            const allSelected = sizesToToggle.every(size => variantSelectedSizes[variantIndex].has(size));
            
            const variant = document.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).closest('.variant-item');
            
            if (allSelected) {
                // If all are selected, deselect all in this category
                sizesToToggle.forEach(size => {
                    variantSelectedSizes[variantIndex].delete(size);
                    const checkbox = variant.querySelector(`input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest('.size-option').classList.remove('selected');
                    }
                });
            } else {
                // If not all are selected, select all in this category
                sizesToToggle.forEach(size => {
                    variantSelectedSizes[variantIndex].add(size);
                    const checkbox = variant.querySelector(`input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.size-option').classList.add('selected');
                    }
                });
            }
            
            updateVariantSelectedSizesDisplay(variantIndex);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdownContainer = document.querySelector('.size-dropdown-container');
            if (dropdownContainer && !dropdownContainer.contains(event.target)) {
                const dropdownContent = document.getElementById('size-dropdown-content');
                const dropdownHeader = document.querySelector('.size-dropdown-header');
                const dropdownIcon = document.getElementById('size-dropdown-icon');
                
                dropdownContent.classList.remove('show');
                dropdownHeader.classList.remove('active');
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        });

        function previewVariantMedia(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                const fileName = file.name;
                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Size in MB
                
                // Check file size (max 50MB for videos)
                if (fileType.startsWith('video/') && file.size > 50 * 1024 * 1024) {
                    preview.innerHTML = `<div class="no-image" style="color: #e53e3e;">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        Video file too large (${fileSize}MB)<br>
                        Maximum size: 50MB
                    </div>`;
                    input.value = ''; // Clear the input
                    return;
                }
                
                if (fileType.startsWith('video/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <video controls style="max-width: 150px; max-height: 150px; border-radius: 6px;">
                                <source src="${e.target.result}" type="${fileType}">
                                Your browser does not support the video tag.
                            </video>
                            <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">
                                ${fileName}<br>
                                ${fileSize}MB
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div style="font-size: 0.7rem; color: #666; margin-top: 5px;">
                                ${fileName}<br>
                                ${fileSize}MB
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                preview.innerHTML = '<div class="no-image">No media selected</div>';
            }
        }

        function previewMedia(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                const fileName = file.name;
                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Size in MB
                
                // Check file size (max 50MB for videos)
                if (fileType.startsWith('video/') && file.size > 50 * 1024 * 1024) {
                    preview.innerHTML = `<div class="no-image" style="color: #e53e3e;">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        Video file too large (${fileSize}MB)<br>
                        Maximum size: 50MB
                    </div>`;
                    input.value = ''; // Clear the input
                    return;
                }
                
                if (fileType.startsWith('video/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <video controls style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                <source src="${e.target.result}" type="${fileType}">
                                Your browser does not support the video tag.
                            </video>
                            <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                                ${fileName}<br>
                                ${fileSize}MB
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                                ${fileName}<br>
                                ${fileSize}MB
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                preview.innerHTML = '<div class="no-image">No media selected</div>';
            }
        }

        // Function to reset form to original values
        function resetForm() {
            showModal({
                title: 'Reset Form',
                message: 'Are you sure you want to reset all changes and return to the original product data? All unsaved changes will be lost.',
                type: 'warning',
                buttons: [
                    {
                        text: 'Cancel',
                        class: 'btn-secondary',
                        action: 'close'
                    },
                    {
                        text: 'Reset',
                        class: 'btn-warning',
                        action: () => {
                            window.location.reload();
                        }
                    }
                ]
            });
        }

        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleSalePrice();
            loadSubcategories();
            
            // Initialize perfume fields based on current category
            const currentCategory = document.getElementById('category').value;
            if (currentCategory) {
                togglePerfumeFields(currentCategory);
            }
            
            // Add event listener for category dropdown
            document.getElementById('category').addEventListener('change', function() {
                loadSubcategories();
            });
            
            // Initialize admin color circles
            initializeAdminColorCircles();
            
            // Add form submission handler to track deleted variants
            document.querySelector('form').addEventListener('submit', function(e) {
                updateDeletedVariantsField();
            });
        });

        function updateDeletedVariantsField() {
            const deletedVariantsField = document.getElementById('deleted-variants');
            if (deletedVariantsField) {
                deletedVariantsField.value = JSON.stringify(deletedVariants);
    
            }
        }

        function initializeAdminColorCircles() {
    
            
            // Add change event listeners to existing color inputs
            document.querySelectorAll('.variant-color-input').forEach((input, index) => {
                input.addEventListener('change', function() {
                    updateColorCircle(this, index);
                });
            });
            
            // Log all admin color circles found
            const colorCircles = document.querySelectorAll('.admin-color-circle');
            colorCircles.forEach((circle, index) => {
                    id: circle.id,
                    'data-variant-index': circle.getAttribute('data-variant-index'),
                    backgroundColor: circle.style.backgroundColor
                });
            });
            
            // Add click event delegation for admin color circles
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('admin-color-circle')) {
                    e.preventDefault();
                    e.stopPropagation();
                    
    
                    
                    const variantIndex = e.target.getAttribute('data-variant-index');
                    if (variantIndex !== null) {
                        showVariantImages(parseInt(variantIndex));
                    }
                }
            });
        }

        // Elegant Modal System Functions
        function showModal(options) {
            const modalOverlay = document.getElementById('modal-overlay');
            const modalTitle = document.getElementById('modal-title-text');
            const modalMessage = document.getElementById('modal-message');
            const modalFooter = document.getElementById('modal-footer');
            const modalIcon = document.getElementById('modal-icon');

            // Set modal content
            modalTitle.textContent = options.title || 'Modal';
            modalMessage.innerHTML = options.message || '';

            // Set icon based on type
            const iconMap = {
                success: '<i class="fas fa-check"></i>',
                warning: '<i class="fas fa-exclamation-triangle"></i>',
                error: '<i class="fas fa-times-circle"></i>',
                info: '<i class="fas fa-info-circle"></i>'
            };

            modalIcon.className = `modal-icon ${options.type || 'info'}`;
            modalIcon.innerHTML = iconMap[options.type || 'info'] || iconMap.info;

            // Create buttons
            modalFooter.innerHTML = '';
            if (options.buttons && options.buttons.length > 0) {
                options.buttons.forEach(button => {
                    const btn = document.createElement('button');
                    btn.className = `modal-btn ${button.class}`;
                    btn.textContent = button.text;
                    btn.onclick = () => {
                        if (typeof button.action === 'function') {
                            button.action();
                        } else if (button.action === 'close') {
                            closeModal();
                        }
                    };
                    modalFooter.appendChild(btn);
                });
            } else {
                // Default OK button
                const btn = document.createElement('button');
                btn.className = 'modal-btn btn-primary';
                btn.textContent = 'OK';
                btn.onclick = closeModal;
                modalFooter.appendChild(btn);
            }

            // Show modal
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Focus first button
            setTimeout(() => {
                const firstBtn = modalFooter.querySelector('.modal-btn');
                if (firstBtn) firstBtn.focus();
            }, 100);
        }

        function closeModal() {
            const modalOverlay = document.getElementById('modal-overlay');
            modalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'modal-overlay') {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Multi-Product Functions
        let currentProductCount = 1;
        let globalColorVariantIndexes = {};

        function handleProductCountChange() {
            const count = parseInt(document.getElementById('product-count').value);
            currentProductCount = count;
        }

        function generateProductForms() {
            const count = parseInt(document.getElementById('product-count').value);
            if (count === 1) {
                showSingleProductForm();
            } else {
                showMultiProductForm(count);
            }
        }

        function showSingleProductForm() {
            document.getElementById('single-product-form').classList.add('form-active');
            document.getElementById('multi-product-form').classList.remove('form-active');
        }

        function showMultiProductForm(count) {
            document.getElementById('single-product-form').classList.remove('form-active');
            document.getElementById('multi-product-form').classList.add('form-active');
            
            const container = document.getElementById('multi-product-forms-container');
            container.innerHTML = '';
            
            for (let i = 0; i < count; i++) {
                const productForm = generateProductFormHTML(i);
                container.innerHTML += productForm;
            }
            
            // Initialize global color variant indexes
            globalColorVariantIndexes = {};
            for (let i = 0; i < count; i++) {
                globalColorVariantIndexes[i] = 0;
            }
        }

        // Multi-product variant size selection functions
        let multiVariantSelectedSizes = {};

        function loadMultiVariantSizeOptions(productIndex, variantIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][color_variants][${variantIndex}][size_category]"]`).value;
            const sizeSelectionGroup = document.getElementById(`variant-size_selection_group-${productIndex}-${variantIndex}`);
            const sizeDropdownContent = document.getElementById(`variant-size-dropdown-content-${productIndex}-${variantIndex}`);
            
            if (sizeCategory === 'none' || sizeCategory === '') {
                sizeSelectionGroup.style.display = 'none';
                return;
            }
            
            sizeSelectionGroup.style.display = 'block';
            
            if (sizeCategory === 'clothing') {
                sizeDropdownContent.innerHTML = generateMultiClothingSizes(productIndex, variantIndex);
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateMultiShoeSizes(productIndex, variantIndex);
            }
            
            // Add event listeners to category headers
            setTimeout(() => {
                sizeDropdownContent.querySelectorAll('.size-category-header').forEach(header => {
                    header.addEventListener('click', function() {
                        this.classList.toggle('expanded');
                        const options = this.nextElementSibling;
                        options.classList.toggle('show');
                    });
                });
            }, 100);
        }

        function toggleMultiVariantSizeDropdown(productIndex, variantIndex) {
            const dropdownContent = document.getElementById(`variant-size-dropdown-content-${productIndex}-${variantIndex}`);
            const dropdownHeader = dropdownContent.previousElementSibling;
            const dropdownIcon = document.getElementById(`variant-size-dropdown-icon-${productIndex}-${variantIndex}`);
            
            dropdownContent.classList.toggle('show');
            dropdownHeader.classList.toggle('active');
            
            if (dropdownContent.classList.contains('show')) {
                dropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleMultiVariantSize(productIndex, variantIndex, size) {
            const key = `${productIndex}-${variantIndex}`;
            if (!multiVariantSelectedSizes[key]) {
                multiVariantSelectedSizes[key] = new Set();
            }
            
            const checkbox = document.querySelector(`#variant-size-dropdown-content-${productIndex}-${variantIndex} input[value="${size}"]`);
            const sizeOption = checkbox.closest('.size-option');
            
            if (multiVariantSelectedSizes[key].has(size)) {
                multiVariantSelectedSizes[key].delete(size);
                checkbox.checked = false;
                sizeOption.classList.remove('selected');
            } else {
                multiVariantSelectedSizes[key].add(size);
                checkbox.checked = true;
                sizeOption.classList.add('selected');
            }
            
            updateMultiVariantSelectedSizesDisplay(productIndex, variantIndex);
        }

        function updateMultiVariantSelectedSizesDisplay(productIndex, variantIndex) {
            const key = `${productIndex}-${variantIndex}`;
            const selectedSizesText = document.getElementById(`variant-selected-sizes-text-${productIndex}-${variantIndex}`);
            const selectedSizesInput = document.getElementById(`variant-selected_sizes-${productIndex}-${variantIndex}`);
            
            if (!multiVariantSelectedSizes[key] || multiVariantSelectedSizes[key].size === 0) {
                selectedSizesText.textContent = 'Select sizes...';
                selectedSizesInput.value = '';
            } else {
                const sizesArray = Array.from(multiVariantSelectedSizes[key]).sort();
                selectedSizesText.textContent = `${sizesArray.length} size(s) selected`;
                selectedSizesInput.value = JSON.stringify(sizesArray);
            }
        }

        function selectAllInMultiVariantCategory(productIndex, variantIndex, category) {
            const key = `${productIndex}-${variantIndex}`;
            if (!multiVariantSelectedSizes[key]) {
                multiVariantSelectedSizes[key] = new Set();
            }
            
            // Define size mappings for each category
            const categorySizes = {
                'infant': ['0M', '3M', '6M', '9M', '12M', '18M', '24M'],
                'toddler': ['2T', '3T', '4T'],
                'children': ['4Y', '5Y', '6Y', '7Y', '8Y', '10Y', '12Y', '14Y'],
                'women': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'men': ['X', 'S', 'M', 'L', 'XL', 'XXL'],
                'infant_shoes': ['16', '17', '18', '19', '20', '21', '22'],
                'children_shoes': ['23', '24', '25', '26', '27', '28', '29', '30'],
                'women_shoes': ['35', '36', '37', '38', '39', '40', '41', '42'],
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47']
            };
            
            const sizesToToggle = categorySizes[category] || [];
            
            // Check if all sizes in this category are already selected
            const allSelected = sizesToToggle.every(size => multiVariantSelectedSizes[key].has(size));
            
            if (allSelected) {
                // If all are selected, deselect all in this category
                sizesToToggle.forEach(size => {
                    multiVariantSelectedSizes[key].delete(size);
                    const checkbox = document.querySelector(`#variant-size-dropdown-content-${productIndex}-${variantIndex} input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest('.size-option').classList.remove('selected');
                    }
                });
            } else {
                // If not all are selected, select all in this category
                sizesToToggle.forEach(size => {
                    multiVariantSelectedSizes[key].add(size);
                    const checkbox = document.querySelector(`#variant-size-dropdown-content-${productIndex}-${variantIndex} input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.size-option').classList.add('selected');
                    }
                });
            }
            
            updateMultiVariantSelectedSizesDisplay(productIndex, variantIndex);
        }
    </script>
    </div>

    <!-- Elegant Modal System -->
    <div id="modal-overlay" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <span class="modal-icon" id="modal-icon"></span>
                    <span id="modal-title-text"></span>
                </h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-message"></div>
            </div>
            <div class="modal-footer" id="modal-footer">
                <!-- Buttons will be dynamically added here -->
            </div>
        </div>
    </div>
</body>
</html> 
