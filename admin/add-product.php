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

$categoryModel = new Category();
$categories = $categoryModel->getAll();

$message = '';
$error = '';

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
            
            // Force category to be "Perfumes" if it's any variation of "perfumes"
            if (strtolower($productData['category'] ?? '') === 'perfumes') {
                $productData['category'] = 'Perfumes';
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
            }

            if (isset($_FILES['products']['name'][$productIndex]['back_image']) && 
                $_FILES['products']['error'][$productIndex]['back_image'] === UPLOAD_ERR_OK) {
                $backImageName = time() . '_product_' . $productIndex . '_back_' . $_FILES['products']['name'][$productIndex]['back_image'];
                $backImagePath = $uploadDir . $backImageName;
                if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['back_image'], $backImagePath)) {
                    $productData['back_image'] = 'uploads/products/' . $backImageName;
                }
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
                        }

                        if (isset($_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['back_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants']['error'][$variantIndex]['back_image'] === UPLOAD_ERR_OK) {
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $_FILES['products']['name'][$productIndex]['color_variants']['name'][$variantIndex]['back_image'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants']['tmp_name'][$variantIndex]['back_image'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                            }
                        }

                        $colorVariants[] = $variantData;
                    }
                }
                $productData['color_variants'] = $colorVariants;
            }

            if (isset($productPost['sale']) && !empty($productPost['salePrice'])) {
                $productData['salePrice'] = (float)$productPost['salePrice'];
            }

            // Validate and create product
            $validationErrors = $productModel->validateProductData($productData);
            if (empty($validationErrors)) {
                $newProductId = $productModel->create($productData);
                if ($newProductId) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Failed to add product: " . $productData['name'];
                }
            } else {
                $errorCount++;
                $errors[] = "Validation errors for " . $productData['name'] . ": " . implode(', ', $validationErrors);
            }
        }

        // Set success/error messages
        if ($successCount > 0) {
            $message = "Successfully added $successCount product(s)";
            if ($errorCount > 0) {
                $message .= ". Failed to add $errorCount product(s)";
            }
        } else {
            $error = "Failed to add any products. " . implode('; ', $errors);
        }

        if ($successCount > 0) {
            // Redirect to manage products
            header('Location: manage-products.php?action=bulk_added&count=' . $successCount);
            exit;
        }
    } else {
        // Single product submission (original logic)
        $productData = [
            'name' => $_POST['name'] ?? '',
            'price' => (float)($_POST['price'] ?? 0),
            'color' => $_POST['color'] ?? '',
            'category' => $_POST['category'] ?? '',
            'subcategory' => $_POST['subcategory'] ?? '',
            'description' => $_POST['description'] ?? '',
            'featured' => isset($_POST['featured']),
            'sale' => isset($_POST['sale']),
            'available' => isset($_POST['available']),
            'stock' => (int)($_POST['stock'] ?? 0),
            'size_category' => $_POST['size_category'] ?? '',
            'selected_sizes' => $_POST['selected_sizes'] ?? ''
        ];
        
        // Force category to be "Perfumes" if it's any variation of "perfumes"
        if (strtolower($productData['category'] ?? '') === 'perfumes') {
            $productData['category'] = 'Perfumes';
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
        }

        if (isset($_FILES['back_image']) && $_FILES['back_image']['error'] === UPLOAD_ERR_OK) {
            $backImageName = time() . '_back_' . $_FILES['back_image']['name'];
            $backImagePath = $uploadDir . $backImageName;
            if (move_uploaded_file($_FILES['back_image']['tmp_name'], $backImagePath)) {
                $productData['back_image'] = 'uploads/products/' . $backImageName;
            }
        }

        // Handle color variants
        if (isset($_POST['color_variants']) && is_array($_POST['color_variants'])) {
            $colorVariants = [];
            foreach ($_POST['color_variants'] as $index => $variant) {
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
                    if (isset($_FILES['color_variants']['name'][$index]['front_image']) && 
                        $_FILES['color_variants']['error'][$index]['front_image'] === UPLOAD_ERR_OK) {
                        $variantFrontImageName = time() . '_variant_' . $index . '_front_' . $_FILES['color_variants']['name'][$index]['front_image'];
                        $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                        if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['front_image'], $variantFrontImagePath)) {
                            $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                        }
                    }

                    if (isset($_FILES['color_variants']['name'][$index]['back_image']) && 
                        $_FILES['color_variants']['error'][$index]['back_image'] === UPLOAD_ERR_OK) {
                        $variantBackImageName = time() . '_variant_' . $index . '_back_' . $_FILES['color_variants']['name'][$index]['back_image'];
                        $variantBackImagePath = $uploadDir . $variantBackImageName;
                        if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['back_image'], $variantBackImagePath)) {
                            $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                        }
                    }

                    $colorVariants[] = $variantData;
                }
            }
            $productData['color_variants'] = $colorVariants;
        }

        if (isset($_POST['sale']) && !empty($_POST['salePrice'])) {
            $productData['salePrice'] = (float)$_POST['salePrice'];
        }

        $errors = $productModel->validateProductData($productData);

        if (empty($errors)) {
            $newProductId = $productModel->create($productData);
            if ($newProductId) {
                // Redirect to manage products with the new product highlighted
                header('Location: manage-products.php?highlight=' . $newProductId . '&action=added');
                exit;
            } else {
                $error = 'Failed to add product. Please try again.';
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
    <title>Add Product - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
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
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
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
        
        .remove-variant {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .remove-variant:hover {
            transform: scale(1.1);
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

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
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
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>
            <p>Create a new product with multiple color variants and images</p>
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
            <h3><i class="fas fa-layer-group"></i> Choose How Many Products to Add</h3>
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
                            <input type="text" id="name" name="name" required placeholder="Enter product name">
            </div>
            
            <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00">
            </div>
            
            <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required onchange="loadSubcategories()">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                            <label for="subcategory">Subcategory <span id="subcategory-required" style="color: #dc3545;">*</span></label>
                            <select id="subcategory" name="subcategory">
                    <option value="">Select Subcategory</option>
                </select>
                        </div>
            
            <div class="form-group" id="brand-group" style="display: none;">
                <label for="brand">Brand *</label>
                <select id="brand" name="brand">
                    <option value="">Select Brand</option>
                    <option value="Valentino">Valentino</option>
                    <option value="Chanel">Chanel</option>
                    <option value="Dior">Dior</option>
                    <option value="Gucci">Gucci</option>
                    <option value="Yves Saint Laurent">Yves Saint Laurent</option>
                    <option value="Tom Ford">Tom Ford</option>
                    <option value="Versace">Versace</option>
                    <option value="Prada">Prada</option>
                    <option value="Bvlgari">Bvlgari</option>
                    <option value="Armani">Armani</option>
                    <option value="Calvin Klein">Calvin Klein</option>
                    <option value="Ralph Lauren">Ralph Lauren</option>
                    <option value="Balenciaga">Balenciaga</option>
                    <option value="Givenchy">Givenchy</option>
                    <option value="Hermès">Hermès</option>
                    <option value="Jo Malone">Jo Malone</option>
                    <option value="Marc Jacobs">Marc Jacobs</option>
                    <option value="Viktor&Rolf">Viktor&Rolf</option>
                    <option value="Maison Margiela">Maison Margiela</option>
                    <option value="Byredo">Byredo</option>
                </select>
            </div>
            
            <div class="form-group" id="gender-group" style="display: none;">
                <label for="gender">Gender *</label>
                <select id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="men">Men</option>
                    <option value="women">Women</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>
            
            <div class="form-group" id="perfume-size-group" style="display: none;">
                <label for="perfume_size">Size *</label>
                <select id="perfume_size" name="size">
                    <option value="">Select Size</option>
                    <option value="30ml">30ml</option>
                    <option value="50ml">50ml</option>
                    <option value="100ml">100ml</option>
                    <option value="200ml">200ml</option>
                </select>
            </div>
            
            <div class="form-group">
                            <label for="size_category">Size Category</label>
                            <select id="size_category" name="size_category" onchange="loadSizeOptions()">
                    <option value="">Select Size Category</option>
                    <option value="clothing">Clothing</option>
                    <option value="shoes">Shoes</option>
                    <option value="none">No Sizes</option>
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
                            <input type="hidden" id="selected_sizes" name="selected_sizes" value="">
                        </div>
                        </div>
            </div>
            
                <!-- Color Panel -->
                <div class="form-section">
                    <h2 class="section-title"><i class="fas fa-palette"></i> Color Selection</h2>
                    <div class="color-panel">
                        <label for="color">Main Product Color</label>
                        <input type="color" id="color" name="color" class="color-input" value="#667eea">
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
                                <input type="file" id="front_image" name="front_image" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" required onchange="previewMedia(this, 'front-preview')">
                                <div id="front-preview" class="image-preview">
                                    <div class="no-image">No media selected</div>
                </div>
            </div>
                            <div class="image-input-group">
                                <label for="back_image">Back Media *</label>
                                <input type="file" id="back_image" name="back_image" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" required onchange="previewMedia(this, 'back-preview')">
                                <div id="back-preview" class="image-preview">
                                    <div class="no-image">No media selected</div>
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
                            <!-- Color variants will be added here -->
                    </div>
                    
                        <button type="button" class="add-variant-btn" onclick="addColorVariant()">
                            <i class="fas fa-plus"></i> Add Color Variant
                        </button>
                            </div>
            </div>
            
                <!-- Additional Information -->
                <div class="form-section">
                    <h2 class="section-title"><i class="fas fa-info-circle"></i> Additional Information</h2>
            <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter detailed product description..."></textarea>
                        </div>
                        
                            <div class="checkbox-group">
                        <input type="checkbox" id="featured" name="featured">
                        <label for="featured">Featured Product</label>
                            </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="sale" name="sale" onchange="toggleSalePrice()">
                        <label for="sale">On Sale</label>
                        </div>
                        
                    <div class="form-group" id="salePriceGroup" style="display: none;">
                        <label for="salePrice">Sale Price</label>
                        <input type="number" id="salePrice" name="salePrice" step="0.01" min="0" placeholder="Enter sale price">
                        </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0" value="0" placeholder="Enter stock quantity">
            </div>
            
                    <div class="checkbox-group">
                        <input type="checkbox" id="available" name="available" checked>
                        <label for="available">Available for Purchase</label>
                    </div>
            </div>
            
                <!-- Action Buttons -->
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
            <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                    <button type="button" class="clear-btn" onclick="clearForm()">
                        <i class="fas fa-eraser"></i> Clear Form
            </button>
            </div>
        </form>

        <!-- Multi-Product Form Container -->
        <form method="POST" enctype="multipart/form-data" id="multi-product-form" class="multi-product-form">
            <div id="multi-product-forms-container">
                <!-- Product forms will be generated here -->
            </div>
            
            <!-- Multi-Product Action Buttons -->
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save All Products
                </button>
                <button type="button" class="clear-btn" onclick="clearMultiProductForm()">
                    <i class="fas fa-eraser"></i> Clear All Forms
                </button>
            </div>
        </form>
        </div>
    </div>
    </div>

    <script src="includes/admin-sidebar.js"></script>
    <script>

        // Existing JavaScript functions
        function loadSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const category = categorySelect.value;

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
                            subcategorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading subcategories:', error));
            
            // Show/hide perfume-specific fields
            togglePerfumeFields(category);
        }
        
        function togglePerfumeFields(category) {
            const brandGroup = document.getElementById('brand-group');
            const genderGroup = document.getElementById('gender-group');
            const perfumeSizeGroup = document.getElementById('perfume-size-group');
            const subcategoryGroup = document.querySelector('.form-group:has(#subcategory)');
            const sizeCategoryGroup = document.querySelector('.form-group:has(#size_category)');
            
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



        let colorVariantIndex = 0;

        function addColorVariant() {
            const container = document.getElementById('color-variants-container');
            const currentCategory = document.getElementById('category').value;
            const isPerfume = currentCategory.toLowerCase() === 'perfumes';
            
            const variantHtml = `
                <div class="variant-item">
                    <button type="button" class="remove-variant" onclick="removeColorVariant(this)">
                        <i class="fas fa-times"></i>
                    </button>
                    <h4>Color Variant #${colorVariantIndex + 1}</h4>
                    
                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="color_variants[${colorVariantIndex}][name]" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Color *</label>
                        <input type="color" name="color_variants[${colorVariantIndex}][color]" class="variant-color-input" required>
                    </div>
                    
                    ${isPerfume ? `
                    <!-- Perfume-specific fields for variants -->
                    <div class="form-group">
                        <label>Variant Brand</label>
                        <select name="color_variants[${colorVariantIndex}][brand]">
                            <option value="">Select Brand</option>
                            <option value="Valentino">Valentino</option>
                            <option value="Chanel">Chanel</option>
                            <option value="Dior">Dior</option>
                            <option value="Gucci">Gucci</option>
                            <option value="Yves Saint Laurent">Yves Saint Laurent</option>
                            <option value="Tom Ford">Tom Ford</option>
                            <option value="Versace">Versace</option>
                            <option value="Prada">Prada</option>
                            <option value="Bvlgari">Bvlgari</option>
                            <option value="Armani">Armani</option>
                            <option value="Calvin Klein">Calvin Klein</option>
                            <option value="Ralph Lauren">Ralph Lauren</option>
                            <option value="Balenciaga">Balenciaga</option>
                            <option value="Givenchy">Givenchy</option>
                            <option value="Hermès">Hermès</option>
                            <option value="Jo Malone">Jo Malone</option>
                            <option value="Marc Jacobs">Marc Jacobs</option>
                            <option value="Viktor&Rolf">Viktor&Rolf</option>
                            <option value="Maison Margiela">Maison Margiela</option>
                            <option value="Byredo">Byredo</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Gender</label>
                        <select name="color_variants[${colorVariantIndex}][gender]">
                            <option value="">Select Gender</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Size</label>
                        <select name="color_variants[${colorVariantIndex}][size]">
                            <option value="">Select Size</option>
                            <option value="30ml">30ml</option>
                            <option value="50ml">50ml</option>
                            <option value="100ml">100ml</option>
                            <option value="200ml">200ml</option>
                        </select>
                    </div>
                    ` : `
                    <!-- Regular size category for non-perfumes -->
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
                    `}
                    
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
            button.closest('.variant-item').remove();
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

        // Function to clear all form fields
        function clearForm() {
            // Clear text inputs
            document.getElementById('name').value = '';
            document.getElementById('price').value = '';
            document.getElementById('description').value = '';
            document.getElementById('salePrice').value = '';
            
            // Reset color input
            document.getElementById('color').value = '#667eea';
            
            // Reset select dropdowns
            document.getElementById('category').selectedIndex = 0;
            document.getElementById('subcategory').innerHTML = '<option value="">Select Subcategory</option>';
            
            // Reset size dropdowns
            document.getElementById('size_category').selectedIndex = 0;
            document.getElementById('size_selection_group').style.display = 'none';
            selectedSizes.clear();
            variantSelectedSizes = {};
            
            // Clear file inputs
            document.getElementById('front_image').value = '';
            document.getElementById('back_image').value = '';
            
            // Reset checkboxes
            document.getElementById('featured').checked = false;
            document.getElementById('sale').checked = false;
            
            // Clear image previews
            document.getElementById('front-preview').innerHTML = '<div class="no-image">No image selected</div>';
            document.getElementById('back-preview').innerHTML = '<div class="no-image">No image selected</div>';
            
            // Clear color variants
            document.getElementById('color-variants-container').innerHTML = '';
            colorVariantIndex = 0;
            
            // Hide sale price group
            document.getElementById('salePriceGroup').style.display = 'none';
        }

        // Clear form on page load/refresh
        document.addEventListener('DOMContentLoaded', function() {
            toggleSalePrice();
            clearForm();
        });

        // Clear form when form is successfully submitted
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                // Clear form after a short delay to allow submission
                setTimeout(function() {
                    clearForm();
                }, 100);
            });
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

        function generateProductFormHTML(productIndex) {
            const categories = <?php echo json_encode(array_column($categories, 'name')); ?>;
            let categoryOptions = '<option value="">Select Category</option>';
            categories.forEach(category => {
                categoryOptions += `<option value="${category}">${category}</option>`;
            });

            return `
                <div class="product-form-container" id="product-form-${productIndex}">
                    <div class="product-form-header">
                        <h3><i class="fas fa-box"></i> Product ${productIndex + 1}</h3>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="product-form-number">#${productIndex + 1}</span>
                            ${productIndex > 0 ? `<button type="button" class="remove-product-btn" onclick="removeProductForm(${productIndex})">
                                <i class="fas fa-trash"></i> Remove
                            </button>` : ''}
                        </div>
                    </div>
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name-${productIndex}">Product Name *</label>
                                <input type="text" id="name-${productIndex}" name="products[${productIndex}][name]" required placeholder="Enter product name">
                            </div>
                            
                            <div class="form-group">
                                <label for="price-${productIndex}">Price *</label>
                                <input type="number" id="price-${productIndex}" name="products[${productIndex}][price]" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            
                            <div class="form-group">
                                <label for="category-${productIndex}">Category *</label>
                                <select id="category-${productIndex}" name="products[${productIndex}][category]" required onchange="loadMultiSubcategories(${productIndex})">
                                    ${categoryOptions}
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subcategory-${productIndex}">Subcategory <span id="subcategory-required-${productIndex}" style="color: #dc3545;">*</span></label>
                                <select id="subcategory-${productIndex}" name="products[${productIndex}][subcategory]">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="brand-group-${productIndex}" style="display: none;">
                                <label for="brand-${productIndex}">Brand *</label>
                                <select id="brand-${productIndex}" name="products[${productIndex}][brand]">
                                    <option value="">Select Brand</option>
                                    <option value="Valentino">Valentino</option>
                                    <option value="Chanel">Chanel</option>
                                    <option value="Dior">Dior</option>
                                    <option value="Gucci">Gucci</option>
                                    <option value="Yves Saint Laurent">Yves Saint Laurent</option>
                                    <option value="Tom Ford">Tom Ford</option>
                                    <option value="Versace">Versace</option>
                                    <option value="Prada">Prada</option>
                                    <option value="Bvlgari">Bvlgari</option>
                                    <option value="Armani">Armani</option>
                                    <option value="Calvin Klein">Calvin Klein</option>
                                    <option value="Ralph Lauren">Ralph Lauren</option>
                                    <option value="Balenciaga">Balenciaga</option>
                                    <option value="Givenchy">Givenchy</option>
                                    <option value="Hermès">Hermès</option>
                                    <option value="Jo Malone">Jo Malone</option>
                                    <option value="Marc Jacobs">Marc Jacobs</option>
                                    <option value="Viktor&Rolf">Viktor&Rolf</option>
                                    <option value="Maison Margiela">Maison Margiela</option>
                                    <option value="Byredo">Byredo</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="gender-group-${productIndex}" style="display: none;">
                                <label for="gender-${productIndex}">Gender *</label>
                                <select id="gender-${productIndex}" name="products[${productIndex}][gender]">
                                    <option value="">Select Gender</option>
                                    <option value="men">Men</option>
                                    <option value="women">Women</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="perfume-size-group-${productIndex}" style="display: none;">
                                <label for="perfume_size-${productIndex}">Size *</label>
                                <select id="perfume_size-${productIndex}" name="products[${productIndex}][size]">
                                    <option value="">Select Size</option>
                                    <option value="30ml">30ml</option>
                                    <option value="50ml">50ml</option>
                                    <option value="100ml">100ml</option>
                                    <option value="200ml">200ml</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="size_category-${productIndex}">Size Category</label>
                                <select id="size_category-${productIndex}" name="products[${productIndex}][size_category]" onchange="loadMultiSizeOptions(${productIndex})">
                                    <option value="">Select Size Category</option>
                                    <option value="clothing">Clothing</option>
                                    <option value="shoes">Shoes</option>
                                    <option value="none">No Sizes</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="size_selection_group-${productIndex}" style="display: none;">
                                <label>Available Sizes</label>
                                <div class="size-dropdown-container">
                                    <div class="size-dropdown-header" onclick="toggleMultiSizeDropdown(${productIndex})">
                                        <span id="selected-sizes-text-${productIndex}">Select sizes...</span>
                                        <i class="fas fa-chevron-down" id="size-dropdown-icon-${productIndex}"></i>
                                    </div>
                                    <div class="size-dropdown-content" id="size-dropdown-content-${productIndex}">
                                        <!-- Size options will be loaded here -->
                                    </div>
                                </div>
                                <input type="hidden" id="selected_sizes-${productIndex}" name="products[${productIndex}][selected_sizes]" value="">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Color Panel -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-palette"></i> Color Selection</h2>
                        <div class="color-panel">
                            <label for="color-${productIndex}">Main Product Color</label>
                            <input type="color" id="color-${productIndex}" name="products[${productIndex}][color]" class="color-input" value="#667eea">
                        </div>
                    </div>
                    
                    <!-- Media Uploads -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-images"></i> Product Media</h2>
                        <div class="image-upload-section">
                            <h3><i class="fas fa-upload"></i> Upload Product Media</h3>
                            <p>Add front and back images or videos for your product</p>
                            <div class="image-inputs">
                                <div class="image-input-group">
                                    <label for="front_image-${productIndex}">Front Media *</label>
                                    <input type="file" id="front_image-${productIndex}" name="products[${productIndex}][front_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" required onchange="previewMultiMedia(this, 'front-preview-${productIndex}')">
                                    <div id="front-preview-${productIndex}" class="image-preview">
                                        <div class="no-image">No media selected</div>
                                    </div>
                                </div>
                                <div class="image-input-group">
                                    <label for="back_image-${productIndex}">Back Media *</label>
                                    <input type="file" id="back_image-${productIndex}" name="products[${productIndex}][back_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" required onchange="previewMultiMedia(this, 'back-preview-${productIndex}')">
                                    <div id="back-preview-${productIndex}" class="image-preview">
                                        <div class="no-image">No media selected</div>
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
                            <div id="color-variants-container-${productIndex}">
                                <!-- Color variants will be added here -->
                            </div>
                            
                            <button type="button" class="add-variant-btn" onclick="addMultiColorVariant(${productIndex})">
                                <i class="fas fa-plus"></i> Add Color Variant
                            </button>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Additional Information</h2>
                        <div class="form-group">
                            <label for="description-${productIndex}">Description</label>
                            <textarea id="description-${productIndex}" name="products[${productIndex}][description]" rows="4" placeholder="Enter detailed product description..."></textarea>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="featured-${productIndex}" name="products[${productIndex}][featured]">
                            <label for="featured-${productIndex}">Featured Product</label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="sale-${productIndex}" name="products[${productIndex}][sale]" onchange="toggleMultiSalePrice(${productIndex})">
                            <label for="sale-${productIndex}">On Sale</label>
                        </div>
                        
                        <div class="form-group" id="salePriceGroup-${productIndex}" style="display: none;">
                            <label for="salePrice-${productIndex}">Sale Price</label>
                            <input type="number" id="salePrice-${productIndex}" name="products[${productIndex}][salePrice]" step="0.01" min="0" placeholder="Enter sale price">
                        </div>

                        <div class="form-group">
                            <label for="stock-${productIndex}">Stock Quantity</label>
                            <input type="number" id="stock-${productIndex}" name="products[${productIndex}][stock]" min="0" value="0" placeholder="Enter stock quantity">
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="available-${productIndex}" name="products[${productIndex}][available]" checked>
                            <label for="available-${productIndex}">Available for Purchase</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function removeProductForm(productIndex) {
            const formContainer = document.getElementById(`product-form-${productIndex}`);
            if (formContainer) {
                formContainer.remove();
                // Reindex remaining forms
                reindexProductForms();
            }
        }

        function reindexProductForms() {
            const forms = document.querySelectorAll('.product-form-container');
            forms.forEach((form, newIndex) => {
                // Update form ID
                form.id = `product-form-${newIndex}`;
                
                // Update header
                const header = form.querySelector('.product-form-header h3');
                if (header) {
                    header.innerHTML = `<i class="fas fa-box"></i> Product ${newIndex + 1}`;
                }
                
                const numberSpan = form.querySelector('.product-form-number');
                if (numberSpan) {
                    numberSpan.textContent = `#${newIndex + 1}`;
                }
                
                // Update remove button
                const removeBtn = form.querySelector('.remove-product-btn');
                if (removeBtn) {
                    removeBtn.onclick = () => removeProductForm(newIndex);
                }
                
                // Update all input names and IDs
                updateFormElementNames(form, newIndex);
            });
            
            // Update global color variant indexes
            const newIndexes = {};
            forms.forEach((form, index) => {
                newIndexes[index] = globalColorVariantIndexes[form.dataset.originalIndex] || 0;
            });
            globalColorVariantIndexes = newIndexes;
        }

        function updateFormElementNames(form, newIndex) {
            // Update all input names and IDs
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const oldName = input.name;
                const oldId = input.id;
                
                if (oldName && oldName.includes('products[')) {
                    input.name = oldName.replace(/products\[\d+\]/, `products[${newIndex}]`);
                }
                
                if (oldId && oldId.includes('-')) {
                    const parts = oldId.split('-');
                    const lastPart = parts[parts.length - 1];
                    if (!isNaN(lastPart)) {
                        parts[parts.length - 1] = newIndex;
                        input.id = parts.join('-');
                    }
                }
            });
            
            // Update onchange handlers
            const categorySelect = form.querySelector('select[name*="[category]"]');
            if (categorySelect) {
                categorySelect.onchange = () => loadMultiSubcategories(newIndex);
            }
            
            const sizeCategorySelect = form.querySelector('select[name*="[size_category]"]');
            if (sizeCategorySelect) {
                sizeCategorySelect.onchange = () => loadMultiSizeOptions(newIndex);
            }
            
            const saleCheckbox = form.querySelector('input[name*="[sale]"]');
            if (saleCheckbox) {
                saleCheckbox.onchange = () => toggleMultiSalePrice(newIndex);
            }
            
            // Update preview functions
            const frontImageInput = form.querySelector('input[name*="[front_image]"]');
            if (frontImageInput) {
                frontImageInput.onchange = function() {
                    previewMultiMedia(this, `front-preview-${newIndex}`);
                };
            }
            
            const backImageInput = form.querySelector('input[name*="[back_image]"]');
            if (backImageInput) {
                backImageInput.onchange = function() {
                    previewMultiMedia(this, `back-preview-${newIndex}`);
                };
            }
        }

        function loadMultiSubcategories(productIndex) {
            const categorySelect = document.getElementById(`category-${productIndex}`);
            const subcategorySelect = document.getElementById(`subcategory-${productIndex}`);
            const category = categorySelect.value;

            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (!category) return;
                        
            fetch(`get-subcategories.php?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    const subcategories = data.subcategories || data;
                    if (subcategories && subcategories.length > 0) {
                        subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            const subName = typeof sub === 'string' ? sub : sub.name;
                            option.value = subName;
                            option.textContent = subName;
                            subcategorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading subcategories:', error));
            
            // Show/hide perfume-specific fields for multi-product form
            toggleMultiPerfumeFields(productIndex, category);
        }
        
        function toggleMultiPerfumeFields(productIndex, category) {
            const brandGroup = document.getElementById(`brand-group-${productIndex}`);
            const genderGroup = document.getElementById(`gender-group-${productIndex}`);
            const perfumeSizeGroup = document.getElementById(`perfume-size-group-${productIndex}`);
            const subcategoryGroup = document.querySelector(`.form-group:has(#subcategory-${productIndex})`);
            const sizeCategoryGroup = document.querySelector(`.form-group:has(#size_category-${productIndex})`);
            
            const shouldShow = category.toLowerCase() === 'perfumes';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = shouldShow ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = shouldShow ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = shouldShow ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = shouldShow ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = shouldShow ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById(`brand-${productIndex}`);
            const genderField = document.getElementById(`gender-${productIndex}`);
            const sizeField = document.getElementById(`perfume_size-${productIndex}`);
            
            if (brandField) brandField.required = category.toLowerCase() === 'perfumes';
            if (genderField) genderField.required = category.toLowerCase() === 'perfumes';
            if (sizeField) sizeField.required = category.toLowerCase() === 'perfumes';
        }



        function loadMultiSizeOptions(productIndex) {
            const sizeCategory = document.getElementById(`size_category-${productIndex}`).value;
            const sizeSelectionGroup = document.getElementById(`size_selection_group-${productIndex}`);
            const sizeDropdownContent = document.getElementById(`size-dropdown-content-${productIndex}`);
            
            if (sizeCategory === 'none' || sizeCategory === '') {
                sizeSelectionGroup.style.display = 'none';
                return;
            }
            
            sizeSelectionGroup.style.display = 'block';
            
            if (sizeCategory === 'clothing') {
                sizeDropdownContent.innerHTML = generateMultiClothingSizes(productIndex);
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateMultiShoeSizes(productIndex);
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

        function generateMultiClothingSizes(productIndex) {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'infant')">
                            <input type="checkbox" id="select_all_infant_${productIndex}" name="sizes[]" value="select_all_infant">
                            <label for="select_all_infant_${productIndex}">✓ Select All Infant</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '0M')">
                            <input type="checkbox" id="size_0M_${productIndex}" name="sizes[]" value="0M">
                            <label for="size_0M_${productIndex}">0M (EU 50)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '3M')">
                            <input type="checkbox" id="size_3M_${productIndex}" name="sizes[]" value="3M">
                            <label for="size_3M_${productIndex}">3M (EU 56)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '6M')">
                            <input type="checkbox" id="size_6M_${productIndex}" name="sizes[]" value="6M">
                            <label for="size_6M_${productIndex}">6M (EU 62)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '9M')">
                            <input type="checkbox" id="size_9M_${productIndex}" name="sizes[]" value="9M">
                            <label for="size_9M_${productIndex}">9M (EU 68)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '12M')">
                            <input type="checkbox" id="size_12M_${productIndex}" name="sizes[]" value="12M">
                            <label for="size_12M_${productIndex}">12M (EU 74)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '18M')">
                            <input type="checkbox" id="size_18M_${productIndex}" name="sizes[]" value="18M">
                            <label for="size_18M_${productIndex}">18M (EU 80)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '24M')">
                            <input type="checkbox" id="size_24M_${productIndex}" name="sizes[]" value="24M">
                            <label for="size_24M_${productIndex}">24M (EU 86)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'toddler')">
                            <input type="checkbox" id="select_all_toddler_${productIndex}" name="sizes[]" value="select_all_toddler">
                            <label for="select_all_toddler_${productIndex}">✓ Select All Toddler</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '2T')">
                            <input type="checkbox" id="size_2T_${productIndex}" name="sizes[]" value="2T">
                            <label for="size_2T_${productIndex}">2T (EU 92)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '3T')">
                            <input type="checkbox" id="size_3T_${productIndex}" name="sizes[]" value="3T">
                            <label for="size_3T_${productIndex}">3T (EU 98)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '4T')">
                            <input type="checkbox" id="size_4T_${productIndex}" name="sizes[]" value="4T">
                            <label for="size_4T_${productIndex}">4T (EU 104)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children (4-14 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'children')">
                            <input type="checkbox" id="select_all_children_${productIndex}" name="sizes[]" value="select_all_children">
                            <label for="select_all_children_${productIndex}">✓ Select All Children</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '4Y')">
                            <input type="checkbox" id="size_4Y_${productIndex}" name="sizes[]" value="4Y">
                            <label for="size_4Y_${productIndex}">4Y (EU 110)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '5Y')">
                            <input type="checkbox" id="size_5Y_${productIndex}" name="sizes[]" value="5Y">
                            <label for="size_5Y_${productIndex}">5Y (EU 116)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '6Y')">
                            <input type="checkbox" id="size_6Y_${productIndex}" name="sizes[]" value="6Y">
                            <label for="size_6Y_${productIndex}">6Y (EU 122)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '7Y')">
                            <input type="checkbox" id="size_7Y_${productIndex}" name="sizes[]" value="7Y">
                            <label for="size_7Y_${productIndex}">7Y (EU 128)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '8Y')">
                            <input type="checkbox" id="size_8Y_${productIndex}" name="sizes[]" value="8Y">
                            <label for="size_8Y_${productIndex}">8Y (EU 134)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '10Y')">
                            <input type="checkbox" id="size_10Y_${productIndex}" name="sizes[]" value="10Y">
                            <label for="size_10Y_${productIndex}">10Y (EU 140)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '12Y')">
                            <input type="checkbox" id="size_12Y_${productIndex}" name="sizes[]" value="12Y">
                            <label for="size_12Y_${productIndex}">12Y (EU 146)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '14Y')">
                            <input type="checkbox" id="size_14Y_${productIndex}" name="sizes[]" value="14Y">
                            <label for="size_14Y_${productIndex}">14Y (EU 152)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'women')">
                            <input type="checkbox" id="select_all_women_${productIndex}" name="sizes[]" value="select_all_women">
                            <label for="select_all_women_${productIndex}">✓ Select All Women</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'X')">
                            <input type="checkbox" id="size_X_${productIndex}" name="sizes[]" value="X">
                            <label for="size_X_${productIndex}">X (EU 34-36)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'S')">
                            <input type="checkbox" id="size_S_${productIndex}" name="sizes[]" value="S">
                            <label for="size_S_${productIndex}">S (EU 36-38)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'M')">
                            <input type="checkbox" id="size_M_${productIndex}" name="sizes[]" value="M">
                            <label for="size_M_${productIndex}">M (EU 38-40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'L')">
                            <input type="checkbox" id="size_L_${productIndex}" name="sizes[]" value="L">
                            <label for="size_L_${productIndex}">L (EU 40-42)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'XL')">
                            <input type="checkbox" id="size_XL_${productIndex}" name="sizes[]" value="XL">
                            <label for="size_XL_${productIndex}">XL (EU 42-44)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'XXL')">
                            <input type="checkbox" id="size_XXL_${productIndex}" name="sizes[]" value="XXL">
                            <label for="size_XXL_${productIndex}">XXL (EU 44-46)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'men')">
                            <input type="checkbox" id="select_all_men_${productIndex}" name="sizes[]" value="select_all_men">
                            <label for="select_all_men_${productIndex}">✓ Select All Men</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'X')">
                            <input type="checkbox" id="size_MX_${productIndex}" name="sizes[]" value="X">
                            <label for="size_MX_${productIndex}">X (EU 46-48)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'S')">
                            <input type="checkbox" id="size_MS_${productIndex}" name="sizes[]" value="S">
                            <label for="size_MS_${productIndex}">S (EU 48-50)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'M')">
                            <input type="checkbox" id="size_MM_${productIndex}" name="sizes[]" value="M">
                            <label for="size_MM_${productIndex}">M (EU 50-52)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'L')">
                            <input type="checkbox" id="size_ML_${productIndex}" name="sizes[]" value="L">
                            <label for="size_ML_${productIndex}">L (EU 52-54)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'XL')">
                            <input type="checkbox" id="size_MXL_${productIndex}" name="sizes[]" value="XL">
                            <label for="size_MXL_${productIndex}">XL (EU 54-56)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, 'XXL')">
                            <input type="checkbox" id="size_MXXL_${productIndex}" name="sizes[]" value="XXL">
                            <label for="size_MXXL_${productIndex}">XXL (EU 56-58)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateMultiShoeSizes(productIndex) {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby Shoes (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'infant_shoes')">
                            <input type="checkbox" id="select_all_infant_shoes_${productIndex}" name="sizes[]" value="select_all_infant_shoes">
                            <label for="select_all_infant_shoes_${productIndex}">✓ Select All Infant Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '16')">
                            <input type="checkbox" id="size_16_${productIndex}" name="sizes[]" value="16">
                            <label for="size_16_${productIndex}">16 (EU 16)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '17')">
                            <input type="checkbox" id="size_17_${productIndex}" name="sizes[]" value="17">
                            <label for="size_17_${productIndex}">17 (EU 17)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '18')">
                            <input type="checkbox" id="size_18_${productIndex}" name="sizes[]" value="18">
                            <label for="size_18_${productIndex}">18 (EU 18)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '19')">
                            <input type="checkbox" id="size_19_${productIndex}" name="sizes[]" value="19">
                            <label for="size_19_${productIndex}">19 (EU 19)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '20')">
                            <input type="checkbox" id="size_20_${productIndex}" name="sizes[]" value="20">
                            <label for="size_20_${productIndex}">20 (EU 20)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '21')">
                            <input type="checkbox" id="size_21_${productIndex}" name="sizes[]" value="21">
                            <label for="size_21_${productIndex}">21 (EU 21)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '22')">
                            <input type="checkbox" id="size_22_${productIndex}" name="sizes[]" value="22">
                            <label for="size_22_${productIndex}">22 (EU 22)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children Shoes (1-7 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'children_shoes')">
                            <input type="checkbox" id="select_all_children_shoes_${productIndex}" name="sizes[]" value="select_all_children_shoes">
                            <label for="select_all_children_shoes_${productIndex}">✓ Select All Children Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '23')">
                            <input type="checkbox" id="size_23_${productIndex}" name="sizes[]" value="23">
                            <label for="size_23_${productIndex}">23 (EU 23)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '24')">
                            <input type="checkbox" id="size_24_${productIndex}" name="sizes[]" value="24">
                            <label for="size_24_${productIndex}">24 (EU 24)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '25')">
                            <input type="checkbox" id="size_25_${productIndex}" name="sizes[]" value="25">
                            <label for="size_25_${productIndex}">25 (EU 25)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '26')">
                            <input type="checkbox" id="size_26_${productIndex}" name="sizes[]" value="26">
                            <label for="size_26_${productIndex}">26 (EU 26)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '27')">
                            <input type="checkbox" id="size_27_${productIndex}" name="sizes[]" value="27">
                            <label for="size_27_${productIndex}">27 (EU 27)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '28')">
                            <input type="checkbox" id="size_28_${productIndex}" name="sizes[]" value="28">
                            <label for="size_28_${productIndex}">28 (EU 28)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '29')">
                            <input type="checkbox" id="size_29_${productIndex}" name="sizes[]" value="29">
                            <label for="size_29_${productIndex}">29 (EU 29)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '30')">
                            <input type="checkbox" id="size_30_${productIndex}" name="sizes[]" value="30">
                            <label for="size_30_${productIndex}">30 (EU 30)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women Shoes (EU 35-42)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'women_shoes')">
                            <input type="checkbox" id="select_all_women_shoes_${productIndex}" name="sizes[]" value="select_all_women_shoes">
                            <label for="select_all_women_shoes_${productIndex}">✓ Select All Women Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '35')">
                            <input type="checkbox" id="size_35_${productIndex}" name="sizes[]" value="35">
                            <label for="size_35_${productIndex}">35 (EU 35)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '36')">
                            <input type="checkbox" id="size_36_${productIndex}" name="sizes[]" value="36">
                            <label for="size_36_${productIndex}">36 (EU 36)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '37')">
                            <input type="checkbox" id="size_37_${productIndex}" name="sizes[]" value="37">
                            <label for="size_37_${productIndex}">37 (EU 37)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '38')">
                            <input type="checkbox" id="size_38_${productIndex}" name="sizes[]" value="38">
                            <label for="size_38_${productIndex}">38 (EU 38)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '39')">
                            <input type="checkbox" id="size_39_${productIndex}" name="sizes[]" value="39">
                            <label for="size_39_${productIndex}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '40')">
                            <input type="checkbox" id="size_40_${productIndex}" name="sizes[]" value="40">
                            <label for="size_40_${productIndex}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '41')">
                            <input type="checkbox" id="size_41_${productIndex}" name="sizes[]" value="41">
                            <label for="size_41_${productIndex}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '42')">
                            <input type="checkbox" id="size_42_${productIndex}" name="sizes[]" value="42">
                            <label for="size_42_${productIndex}">42 (EU 42)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men Shoes (EU 39-47)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiCategory(${productIndex}, 'men_shoes')">
                            <input type="checkbox" id="select_all_men_shoes_${productIndex}" name="sizes[]" value="select_all_men_shoes">
                            <label for="select_all_men_shoes_${productIndex}">✓ Select All Men Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '39')">
                            <input type="checkbox" id="size_M39_${productIndex}" name="sizes[]" value="39">
                            <label for="size_M39_${productIndex}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '40')">
                            <input type="checkbox" id="size_M40_${productIndex}" name="sizes[]" value="40">
                            <label for="size_M40_${productIndex}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '41')">
                            <input type="checkbox" id="size_M41_${productIndex}" name="sizes[]" value="41">
                            <label for="size_M41_${productIndex}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '42')">
                            <input type="checkbox" id="size_M42_${productIndex}" name="sizes[]" value="42">
                            <label for="size_M42_${productIndex}">42 (EU 42)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '43')">
                            <input type="checkbox" id="size_M43_${productIndex}" name="sizes[]" value="43">
                            <label for="size_M43_${productIndex}">43 (EU 43)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '44')">
                            <input type="checkbox" id="size_M44_${productIndex}" name="sizes[]" value="44">
                            <label for="size_M44_${productIndex}">44 (EU 44)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '45')">
                            <input type="checkbox" id="size_M45_${productIndex}" name="sizes[]" value="45">
                            <label for="size_M45_${productIndex}">45 (EU 45)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '46')">
                            <input type="checkbox" id="size_M46_${productIndex}" name="sizes[]" value="46">
                            <label for="size_M46_${productIndex}">46 (EU 46)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiSize(${productIndex}, '47')">
                            <input type="checkbox" id="size_M47_${productIndex}" name="sizes[]" value="47">
                            <label for="size_M47_${productIndex}">47 (EU 47)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function toggleMultiSizeDropdown(productIndex) {
            const dropdownContent = document.getElementById(`size-dropdown-content-${productIndex}`);
            const dropdownHeader = dropdownContent.previousElementSibling;
            const dropdownIcon = document.getElementById(`size-dropdown-icon-${productIndex}`);
            
            dropdownContent.classList.toggle('show');
            dropdownHeader.classList.toggle('active');
            
            if (dropdownContent.classList.contains('show')) {
                dropdownIcon.style.transform = 'rotate(180deg)';
            } else {
                dropdownIcon.style.transform = 'rotate(0deg)';
            }
        }

        // Multi-product size selection functions
        let multiSelectedSizes = {};

        function toggleMultiSize(productIndex, size) {
            if (!multiSelectedSizes[productIndex]) {
                multiSelectedSizes[productIndex] = new Set();
            }
            
            const checkbox = document.querySelector(`#size-dropdown-content-${productIndex} input[value="${size}"]`);
            const sizeOption = checkbox.closest('.size-option');
            
            if (multiSelectedSizes[productIndex].has(size)) {
                multiSelectedSizes[productIndex].delete(size);
                checkbox.checked = false;
                sizeOption.classList.remove('selected');
            } else {
                multiSelectedSizes[productIndex].add(size);
                checkbox.checked = true;
                sizeOption.classList.add('selected');
            }
            
            updateMultiSelectedSizesDisplay(productIndex);
        }

        function updateMultiSelectedSizesDisplay(productIndex) {
            const selectedSizesText = document.getElementById(`selected-sizes-text-${productIndex}`);
            const selectedSizesInput = document.getElementById(`selected_sizes-${productIndex}`);
            
            if (!multiSelectedSizes[productIndex] || multiSelectedSizes[productIndex].size === 0) {
                selectedSizesText.textContent = 'Select sizes...';
                selectedSizesInput.value = '';
            } else {
                const sizesArray = Array.from(multiSelectedSizes[productIndex]).sort();
                selectedSizesText.textContent = `${sizesArray.length} size(s) selected`;
                selectedSizesInput.value = JSON.stringify(sizesArray);
            }
        }

        function selectAllInMultiCategory(productIndex, category) {
            if (!multiSelectedSizes[productIndex]) {
                multiSelectedSizes[productIndex] = new Set();
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
            const allSelected = sizesToToggle.every(size => multiSelectedSizes[productIndex].has(size));
            
            if (allSelected) {
                // If all are selected, deselect all in this category
                sizesToToggle.forEach(size => {
                    multiSelectedSizes[productIndex].delete(size);
                    const checkbox = document.querySelector(`#size-dropdown-content-${productIndex} input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest('.size-option').classList.remove('selected');
                    }
                });
            } else {
                // If not all are selected, select all in this category
                sizesToToggle.forEach(size => {
                    multiSelectedSizes[productIndex].add(size);
                    const checkbox = document.querySelector(`#size-dropdown-content-${productIndex} input[value="${size}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.size-option').classList.add('selected');
                    }
                });
            }
            
            updateMultiSelectedSizesDisplay(productIndex);
        }

        function toggleMultiSalePrice(productIndex) {
            const saleCheckbox = document.getElementById(`sale-${productIndex}`);
            const salePriceGroup = document.getElementById(`salePriceGroup-${productIndex}`);
            salePriceGroup.style.display = saleCheckbox.checked ? 'block' : 'none';
        }

        function addMultiColorVariant(productIndex) {
            const container = document.getElementById(`color-variants-container-${productIndex}`);
            const variantIndex = globalColorVariantIndexes[productIndex] || 0;
            const currentCategory = document.querySelector(`select[name="products[${productIndex}][category]"]`).value;
            const isPerfume = currentCategory.toLowerCase() === 'perfumes';
            
            const variantHtml = `
                <div class="variant-item">
                    <button type="button" class="remove-variant" onclick="removeMultiColorVariant(this, ${productIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                    <h4>Color Variant #${variantIndex + 1}</h4>
                    
                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="products[${productIndex}][color_variants][${variantIndex}][name]" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Color *</label>
                        <input type="color" name="products[${productIndex}][color_variants][${variantIndex}][color]" class="variant-color-input" required>
                    </div>
                    
                    ${isPerfume ? `
                    <!-- Perfume-specific fields for variants -->
                    <div class="form-group">
                        <label>Variant Brand</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][brand]">
                            <option value="">Select Brand</option>
                            <option value="Valentino">Valentino</option>
                            <option value="Chanel">Chanel</option>
                            <option value="Dior">Dior</option>
                            <option value="Gucci">Gucci</option>
                            <option value="Yves Saint Laurent">Yves Saint Laurent</option>
                            <option value="Tom Ford">Tom Ford</option>
                            <option value="Versace">Versace</option>
                            <option value="Prada">Prada</option>
                            <option value="Bvlgari">Bvlgari</option>
                            <option value="Armani">Armani</option>
                            <option value="Calvin Klein">Calvin Klein</option>
                            <option value="Ralph Lauren">Ralph Lauren</option>
                            <option value="Balenciaga">Balenciaga</option>
                            <option value="Givenchy">Givenchy</option>
                            <option value="Hermès">Hermès</option>
                            <option value="Jo Malone">Jo Malone</option>
                            <option value="Marc Jacobs">Marc Jacobs</option>
                            <option value="Viktor&Rolf">Viktor&Rolf</option>
                            <option value="Maison Margiela">Maison Margiela</option>
                            <option value="Byredo">Byredo</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Gender</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][gender]">
                            <option value="">Select Gender</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Size</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][size]">
                            <option value="">Select Size</option>
                            <option value="30ml">30ml</option>
                            <option value="50ml">50ml</option>
                            <option value="100ml">100ml</option>
                            <option value="200ml">200ml</option>
                        </select>
                    </div>
                    ` : `
                    <!-- Regular size category for non-perfumes -->
                    <div class="form-group">
                        <label>Size Category</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][size_category]" onchange="loadMultiVariantSizeOptions(${productIndex}, ${variantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="none">No Sizes</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="variant-size_selection_group-${productIndex}-${variantIndex}" style="display: none;">
                        <label>Available Sizes for This Variant</label>
                        <div class="size-dropdown-container">
                            <div class="size-dropdown-header" onclick="toggleMultiVariantSizeDropdown(${productIndex}, ${variantIndex})">
                                <span id="variant-selected-sizes-text-${productIndex}-${variantIndex}">Select sizes...</span>
                                <i class="fas fa-chevron-down" id="variant-size-dropdown-icon-${productIndex}-${variantIndex}"></i>
                            </div>
                            <div class="size-dropdown-content" id="variant-size-dropdown-content-${productIndex}-${variantIndex}">
                                <!-- Size options will be loaded here -->
                            </div>
                        </div>
                        <input type="hidden" id="variant-selected_sizes-${productIndex}-${variantIndex}" name="products[${productIndex}][color_variants][${variantIndex}][selected_sizes]" value="">
                    </div>
                    `}
                    
                    <div class="variant-image-inputs">
                        <div class="image-input-group">
                            <label>Front Media</label>
                            <input type="file" name="products[${productIndex}][color_variants][${variantIndex}][front_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewMultiVariantMedia(this, 'variant-front-${productIndex}-${variantIndex}')">
                            <div id="variant-front-${productIndex}-${variantIndex}" class="variant-image-preview">
                                <div class="no-image">No media selected</div>
                            </div>
                        </div>

                        <div class="image-input-group">
                            <label>Back Media</label>
                            <input type="file" name="products[${productIndex}][color_variants][${variantIndex}][back_image]" accept="image/*,video/*,.mp4,.webm,.mov,.avi,.mkv" onchange="previewMultiVariantMedia(this, 'variant-back-${productIndex}-${variantIndex}')">
                            <div id="variant-back-${productIndex}-${variantIndex}" class="variant-image-preview">
                                <div class="no-image">No media selected</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', variantHtml);
            globalColorVariantIndexes[productIndex] = variantIndex + 1;
        }

        function removeMultiColorVariant(button, productIndex) {
            button.closest('.variant-item').remove();
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

        function generateMultiClothingSizes(productIndex, variantIndex) {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'infant')">
                            <input type="checkbox" id="variant-select_all_infant_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_infant">
                            <label for="variant-select_all_infant_${productIndex}_${variantIndex}">✓ Select All Infant</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '0M')">
                            <input type="checkbox" id="variant-size_0M_${productIndex}_${variantIndex}" name="sizes[]" value="0M">
                            <label for="variant-size_0M_${productIndex}_${variantIndex}">0M (EU 50)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '3M')">
                            <input type="checkbox" id="variant-size_3M_${productIndex}_${variantIndex}" name="sizes[]" value="3M">
                            <label for="variant-size_3M_${productIndex}_${variantIndex}">3M (EU 56)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '6M')">
                            <input type="checkbox" id="variant-size_6M_${productIndex}_${variantIndex}" name="sizes[]" value="6M">
                            <label for="variant-size_6M_${productIndex}_${variantIndex}">6M (EU 62)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '9M')">
                            <input type="checkbox" id="variant-size_9M_${productIndex}_${variantIndex}" name="sizes[]" value="9M">
                            <label for="variant-size_9M_${productIndex}_${variantIndex}">9M (EU 68)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '12M')">
                            <input type="checkbox" id="variant-size_12M_${productIndex}_${variantIndex}" name="sizes[]" value="12M">
                            <label for="variant-size_12M_${productIndex}_${variantIndex}">12M (EU 74)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '18M')">
                            <input type="checkbox" id="variant-size_18M_${productIndex}_${variantIndex}" name="sizes[]" value="18M">
                            <label for="variant-size_18M_${productIndex}_${variantIndex}">18M (EU 80)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '24M')">
                            <input type="checkbox" id="variant-size_24M_${productIndex}_${variantIndex}" name="sizes[]" value="24M">
                            <label for="variant-size_24M_${productIndex}_${variantIndex}">24M (EU 86)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'toddler')">
                            <input type="checkbox" id="variant-select_all_toddler_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_toddler">
                            <label for="variant-select_all_toddler_${productIndex}_${variantIndex}">✓ Select All Toddler</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '2T')">
                            <input type="checkbox" id="variant-size_2T_${productIndex}_${variantIndex}" name="sizes[]" value="2T">
                            <label for="variant-size_2T_${productIndex}_${variantIndex}">2T (EU 92)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '3T')">
                            <input type="checkbox" id="variant-size_3T_${productIndex}_${variantIndex}" name="sizes[]" value="3T">
                            <label for="variant-size_3T_${productIndex}_${variantIndex}">3T (EU 98)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '4T')">
                            <input type="checkbox" id="variant-size_4T_${productIndex}_${variantIndex}" name="sizes[]" value="4T">
                            <label for="variant-size_4T_${productIndex}_${variantIndex}">4T (EU 104)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children (4-14 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'children')">
                            <input type="checkbox" id="variant-select_all_children_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_children">
                            <label for="variant-select_all_children_${productIndex}_${variantIndex}">✓ Select All Children</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '4Y')">
                            <input type="checkbox" id="variant-size_4Y_${productIndex}_${variantIndex}" name="sizes[]" value="4Y">
                            <label for="variant-size_4Y_${productIndex}_${variantIndex}">4Y (EU 110)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '5Y')">
                            <input type="checkbox" id="variant-size_5Y_${productIndex}_${variantIndex}" name="sizes[]" value="5Y">
                            <label for="variant-size_5Y_${productIndex}_${variantIndex}">5Y (EU 116)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '6Y')">
                            <input type="checkbox" id="variant-size_6Y_${productIndex}_${variantIndex}" name="sizes[]" value="6Y">
                            <label for="variant-size_6Y_${productIndex}_${variantIndex}">6Y (EU 122)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '7Y')">
                            <input type="checkbox" id="variant-size_7Y_${productIndex}_${variantIndex}" name="sizes[]" value="7Y">
                            <label for="variant-size_7Y_${productIndex}_${variantIndex}">7Y (EU 128)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '8Y')">
                            <input type="checkbox" id="variant-size_8Y_${productIndex}_${variantIndex}" name="sizes[]" value="8Y">
                            <label for="variant-size_8Y_${productIndex}_${variantIndex}">8Y (EU 134)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '10Y')">
                            <input type="checkbox" id="variant-size_10Y_${productIndex}_${variantIndex}" name="sizes[]" value="10Y">
                            <label for="variant-size_10Y_${productIndex}_${variantIndex}">10Y (EU 140)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '12Y')">
                            <input type="checkbox" id="variant-size_12Y_${productIndex}_${variantIndex}" name="sizes[]" value="12Y">
                            <label for="variant-size_12Y_${productIndex}_${variantIndex}">12Y (EU 146)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '14Y')">
                            <input type="checkbox" id="variant-size_14Y_${productIndex}_${variantIndex}" name="sizes[]" value="14Y">
                            <label for="variant-size_14Y_${productIndex}_${variantIndex}">14Y (EU 152)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'women')">
                            <input type="checkbox" id="variant-select_all_women_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_women">
                            <label for="variant-select_all_women_${productIndex}_${variantIndex}">✓ Select All Women</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'X')">
                            <input type="checkbox" id="variant-size_X_${productIndex}_${variantIndex}" name="sizes[]" value="X">
                            <label for="variant-size_X_${productIndex}_${variantIndex}">X (EU 34-36)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'S')">
                            <input type="checkbox" id="variant-size_S_${productIndex}_${variantIndex}" name="sizes[]" value="S">
                            <label for="variant-size_S_${productIndex}_${variantIndex}">S (EU 36-38)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'M')">
                            <input type="checkbox" id="variant-size_M_${productIndex}_${variantIndex}" name="sizes[]" value="M">
                            <label for="variant-size_M_${productIndex}_${variantIndex}">M (EU 38-40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'L')">
                            <input type="checkbox" id="variant-size_L_${productIndex}_${variantIndex}" name="sizes[]" value="L">
                            <label for="variant-size_L_${productIndex}_${variantIndex}">L (EU 40-42)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'XL')">
                            <input type="checkbox" id="variant-size_XL_${productIndex}_${variantIndex}" name="sizes[]" value="XL">
                            <label for="variant-size_XL_${productIndex}_${variantIndex}">XL (EU 42-44)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'XXL')">
                            <input type="checkbox" id="variant-size_XXL_${productIndex}_${variantIndex}" name="sizes[]" value="XXL">
                            <label for="variant-size_XXL_${productIndex}_${variantIndex}">XXL (EU 44-46)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men (X-XXL)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'men')">
                            <input type="checkbox" id="variant-select_all_men_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_men">
                            <label for="variant-select_all_men_${productIndex}_${variantIndex}">✓ Select All Men</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'X')">
                            <input type="checkbox" id="variant-size_MX_${productIndex}_${variantIndex}" name="sizes[]" value="X">
                            <label for="variant-size_MX_${productIndex}_${variantIndex}">X (EU 46-48)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'S')">
                            <input type="checkbox" id="variant-size_MS_${productIndex}_${variantIndex}" name="sizes[]" value="S">
                            <label for="variant-size_MS_${productIndex}_${variantIndex}">S (EU 48-50)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'M')">
                            <input type="checkbox" id="variant-size_MM_${productIndex}_${variantIndex}" name="sizes[]" value="M">
                            <label for="variant-size_MM_${productIndex}_${variantIndex}">M (EU 50-52)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'L')">
                            <input type="checkbox" id="variant-size_ML_${productIndex}_${variantIndex}" name="sizes[]" value="L">
                            <label for="variant-size_ML_${productIndex}_${variantIndex}">L (EU 52-54)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'XL')">
                            <input type="checkbox" id="variant-size_MXL_${productIndex}_${variantIndex}" name="sizes[]" value="XL">
                            <label for="variant-size_MXL_${productIndex}_${variantIndex}">XL (EU 54-56)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, 'XXL')">
                            <input type="checkbox" id="variant-size_MXXL_${productIndex}_${variantIndex}" name="sizes[]" value="XXL">
                            <label for="variant-size_MXXL_${productIndex}_${variantIndex}">XXL (EU 56-58)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateMultiShoeSizes(productIndex, variantIndex) {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby Shoes (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'infant_shoes')">
                            <input type="checkbox" id="variant-select_all_infant_shoes_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_infant_shoes">
                            <label for="variant-select_all_infant_shoes_${productIndex}_${variantIndex}">✓ Select All Infant Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '16')">
                            <input type="checkbox" id="variant-size_16_${productIndex}_${variantIndex}" name="sizes[]" value="16">
                            <label for="variant-size_16_${productIndex}_${variantIndex}">16 (EU 16)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '17')">
                            <input type="checkbox" id="variant-size_17_${productIndex}_${variantIndex}" name="sizes[]" value="17">
                            <label for="variant-size_17_${productIndex}_${variantIndex}">17 (EU 17)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '18')">
                            <input type="checkbox" id="variant-size_18_${productIndex}_${variantIndex}" name="sizes[]" value="18">
                            <label for="variant-size_18_${productIndex}_${variantIndex}">18 (EU 18)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '19')">
                            <input type="checkbox" id="variant-size_19_${productIndex}_${variantIndex}" name="sizes[]" value="19">
                            <label for="variant-size_19_${productIndex}_${variantIndex}">19 (EU 19)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '20')">
                            <input type="checkbox" id="variant-size_20_${productIndex}_${variantIndex}" name="sizes[]" value="20">
                            <label for="variant-size_20_${productIndex}_${variantIndex}">20 (EU 20)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '21')">
                            <input type="checkbox" id="variant-size_21_${productIndex}_${variantIndex}" name="sizes[]" value="21">
                            <label for="variant-size_21_${productIndex}_${variantIndex}">21 (EU 21)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '22')">
                            <input type="checkbox" id="variant-size_22_${productIndex}_${variantIndex}" name="sizes[]" value="22">
                            <label for="variant-size_22_${productIndex}_${variantIndex}">22 (EU 22)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children Shoes (1-7 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'children_shoes')">
                            <input type="checkbox" id="variant-select_all_children_shoes_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_children_shoes">
                            <label for="variant-select_all_children_shoes_${productIndex}_${variantIndex}">✓ Select All Children Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '23')">
                            <input type="checkbox" id="variant-size_23_${productIndex}_${variantIndex}" name="sizes[]" value="23">
                            <label for="variant-size_23_${productIndex}_${variantIndex}">23 (EU 23)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '24')">
                            <input type="checkbox" id="variant-size_24_${productIndex}_${variantIndex}" name="sizes[]" value="24">
                            <label for="variant-size_24_${productIndex}_${variantIndex}">24 (EU 24)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '25')">
                            <input type="checkbox" id="variant-size_25_${productIndex}_${variantIndex}" name="sizes[]" value="25">
                            <label for="variant-size_25_${productIndex}_${variantIndex}">25 (EU 25)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '26')">
                            <input type="checkbox" id="variant-size_26_${productIndex}_${variantIndex}" name="sizes[]" value="26">
                            <label for="variant-size_26_${productIndex}_${variantIndex}">26 (EU 26)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '27')">
                            <input type="checkbox" id="variant-size_27_${productIndex}_${variantIndex}" name="sizes[]" value="27">
                            <label for="variant-size_27_${productIndex}_${variantIndex}">27 (EU 27)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '28')">
                            <input type="checkbox" id="variant-size_28_${productIndex}_${variantIndex}" name="sizes[]" value="28">
                            <label for="variant-size_28_${productIndex}_${variantIndex}">28 (EU 28)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '29')">
                            <input type="checkbox" id="variant-size_29_${productIndex}_${variantIndex}" name="sizes[]" value="29">
                            <label for="variant-size_29_${productIndex}_${variantIndex}">29 (EU 29)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '30')">
                            <input type="checkbox" id="variant-size_30_${productIndex}_${variantIndex}" name="sizes[]" value="30">
                            <label for="variant-size_30_${productIndex}_${variantIndex}">30 (EU 30)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Women Shoes (EU 35-42)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'women_shoes')">
                            <input type="checkbox" id="variant-select_all_women_shoes_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_women_shoes">
                            <label for="variant-select_all_women_shoes_${productIndex}_${variantIndex}">✓ Select All Women Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '35')">
                            <input type="checkbox" id="variant-size_35_${productIndex}_${variantIndex}" name="sizes[]" value="35">
                            <label for="variant-size_35_${productIndex}_${variantIndex}">35 (EU 35)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '36')">
                            <input type="checkbox" id="variant-size_36_${productIndex}_${variantIndex}" name="sizes[]" value="36">
                            <label for="variant-size_36_${productIndex}_${variantIndex}">36 (EU 36)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '37')">
                            <input type="checkbox" id="variant-size_37_${productIndex}_${variantIndex}" name="sizes[]" value="37">
                            <label for="variant-size_37_${productIndex}_${variantIndex}">37 (EU 37)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '38')">
                            <input type="checkbox" id="variant-size_38_${productIndex}_${variantIndex}" name="sizes[]" value="38">
                            <label for="variant-size_38_${productIndex}_${variantIndex}">38 (EU 38)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '39')">
                            <input type="checkbox" id="variant-size_39_${productIndex}_${variantIndex}" name="sizes[]" value="39">
                            <label for="variant-size_39_${productIndex}_${variantIndex}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '40')">
                            <input type="checkbox" id="variant-size_40_${productIndex}_${variantIndex}" name="sizes[]" value="40">
                            <label for="variant-size_40_${productIndex}_${variantIndex}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '41')">
                            <input type="checkbox" id="variant-size_41_${productIndex}_${variantIndex}" name="sizes[]" value="41">
                            <label for="variant-size_41_${productIndex}_${variantIndex}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '42')">
                            <input type="checkbox" id="variant-size_42_${productIndex}_${variantIndex}" name="sizes[]" value="42">
                            <label for="variant-size_42_${productIndex}_${variantIndex}">42 (EU 42)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Men Shoes (EU 39-47)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="selectAllInMultiVariantCategory(${productIndex}, ${variantIndex}, 'men_shoes')">
                            <input type="checkbox" id="variant-select_all_men_shoes_${productIndex}_${variantIndex}" name="sizes[]" value="select_all_men_shoes">
                            <label for="variant-select_all_men_shoes_${productIndex}_${variantIndex}">✓ Select All Men Shoes</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '39')">
                            <input type="checkbox" id="variant-size_M39_${productIndex}_${variantIndex}" name="sizes[]" value="39">
                            <label for="variant-size_M39_${productIndex}_${variantIndex}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '40')">
                            <input type="checkbox" id="variant-size_M40_${productIndex}_${variantIndex}" name="sizes[]" value="40">
                            <label for="variant-size_M40_${productIndex}_${variantIndex}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '41')">
                            <input type="checkbox" id="variant-size_M41_${productIndex}_${variantIndex}" name="sizes[]" value="41">
                            <label for="variant-size_M41_${productIndex}_${variantIndex}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '42')">
                            <input type="checkbox" id="variant-size_M42_${productIndex}_${variantIndex}" name="sizes[]" value="42">
                            <label for="variant-size_M42_${productIndex}_${variantIndex}">42 (EU 42)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '43')">
                            <input type="checkbox" id="variant-size_M43_${productIndex}_${variantIndex}" name="sizes[]" value="43">
                            <label for="variant-size_M43_${productIndex}_${variantIndex}">43 (EU 43)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '44')">
                            <input type="checkbox" id="variant-size_M44_${productIndex}_${variantIndex}" name="sizes[]" value="44">
                            <label for="variant-size_M44_${productIndex}_${variantIndex}">44 (EU 44)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '45')">
                            <input type="checkbox" id="variant-size_M45_${productIndex}_${variantIndex}" name="sizes[]" value="45">
                            <label for="variant-size_M45_${productIndex}_${variantIndex}">45 (EU 45)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '46')">
                            <input type="checkbox" id="variant-size_M46_${productIndex}_${variantIndex}" name="sizes[]" value="46">
                            <label for="variant-size_M46_${productIndex}_${variantIndex}">46 (EU 46)</label>
                        </div>
                        <div class="size-option" onclick="toggleMultiVariantSize(${productIndex}, ${variantIndex}, '47')">
                            <input type="checkbox" id="variant-size_M47_${productIndex}_${variantIndex}" name="sizes[]" value="47">
                            <label for="variant-size_M47_${productIndex}_${variantIndex}">47 (EU 47)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function previewMultiMedia(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                const fileName = file.name;
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                
                if (fileType.startsWith('video/') && file.size > 50 * 1024 * 1024) {
                    preview.innerHTML = `<div class="no-image" style="color: #e53e3e;">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        Video file too large (${fileSize}MB)<br>
                        Maximum size: 50MB
                    </div>`;
                    input.value = '';
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

        function previewMultiVariantMedia(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileType = file.type;
                const fileName = file.name;
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                
                if (fileType.startsWith('video/') && file.size > 50 * 1024 * 1024) {
                    preview.innerHTML = `<div class="no-image" style="color: #e53e3e;">
                        <i class="fas fa-exclamation-triangle"></i><br>
                        Video file too large (${fileSize}MB)<br>
                        Maximum size: 50MB
                    </div>`;
                    input.value = '';
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

        function clearMultiProductForm() {
            const forms = document.querySelectorAll('.product-form-container');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else if (input.type === 'color') {
                        input.value = '#667eea';
                    } else if (input.type === 'file') {
                        input.value = '';
                    } else {
                        input.value = '';
                    }
                });
                
                // Clear image previews
                const previews = form.querySelectorAll('.image-preview, .variant-image-preview');
                previews.forEach(preview => {
                    preview.innerHTML = '<div class="no-image">No media selected</div>';
                });
                
                // Clear color variants
                const variantContainers = form.querySelectorAll('[id^="color-variants-container-"]');
                variantContainers.forEach(container => {
                    container.innerHTML = '';
                });
            });
            
            // Reset global color variant indexes
            globalColorVariantIndexes = {};
        }

        function clearAllForms() {
            // Clear single product form
            clearForm();
            
            // Clear multi-product forms
            clearMultiProductForm();
            
            // Reset product count to 1
            document.getElementById('product-count').value = '1';
            currentProductCount = 1;
            
            // Reset multi-selected sizes
            multiSelectedSizes = {};
            
            // Show single product form by default
            showSingleProductForm();
            
            // Clear any success/error messages
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.display = 'none';
            });
        }

        // Clear forms on page load/refresh
        window.addEventListener('load', function() {
            // Clear all forms when page loads
            clearAllForms();
        });

        // Clear forms when page is refreshed (F5 or Ctrl+R)
        window.addEventListener('beforeunload', function() {
            // This will trigger when user refreshes the page
            sessionStorage.setItem('clearFormsOnLoad', 'true');
        });

        // Check if we need to clear forms on load
        window.addEventListener('load', function() {
            if (sessionStorage.getItem('clearFormsOnLoad') === 'true') {
                clearAllForms();
                sessionStorage.removeItem('clearFormsOnLoad');
            }
        });
    </script>
</body>
</html> 
