<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// MongoDB connection
require_once '../config1/mongodb.php';
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
        
        // Helper function to restructure $_FILES for multi-product forms
        function restructureMultiProductFiles($files) {
            $restructured = [];
            
            if (!isset($files['name']['products']) || !is_array($files['name']['products'])) {
                return $restructured;
            }
            
            foreach ($files['name']['products'] as $productIndex => $productFiles) {
                if (isset($productFiles['color_variants']) && is_array($productFiles['color_variants'])) {
                    foreach ($productFiles['color_variants'] as $variantIndex => $variantFiles) {
                        // Handle front image
                        if (isset($variantFiles['front_image']) && !empty($variantFiles['front_image'])) {
                            $error = $files['error']['products'][$productIndex]['color_variants'][$variantIndex]['front_image'] ?? UPLOAD_ERR_NO_FILE;
                            if ($error === UPLOAD_ERR_OK) {
                                $restructured[$productIndex][$variantIndex]['front_image'] = [
                                    'name' => $variantFiles['front_image'],
                                    'tmp_name' => $files['tmp_name']['products'][$productIndex]['color_variants'][$variantIndex]['front_image'],
                                    'error' => $error,
                                    'size' => $files['size']['products'][$productIndex]['color_variants'][$variantIndex]['front_image']
                                ];
                            } else {
                            }
                        }
                        
                        // Handle back image
                        if (isset($variantFiles['back_image']) && !empty($variantFiles['back_image'])) {
                            $error = $files['error']['products'][$productIndex]['color_variants'][$variantIndex]['back_image'] ?? UPLOAD_ERR_NO_FILE;
                            if ($error === UPLOAD_ERR_OK) {
                                $restructured[$productIndex][$variantIndex]['back_image'] = [
                                    'name' => $variantFiles['back_image'],
                                    'tmp_name' => $files['tmp_name']['products'][$productIndex]['color_variants'][$variantIndex]['back_image'],
                                    'error' => $error,
                                    'size' => $files['size']['products'][$productIndex]['color_variants'][$variantIndex]['back_image']
                                ];
                            } else {
                            }
                        }
                    }
                }
            }
            
            return $restructured;
        }
        
        $restructuredFiles = restructureMultiProductFiles($_FILES);
        
        // Multiple products submission
        foreach ($_POST['products'] as $productIndex => $productPost) {
            if (empty($productPost['name'])) continue; // Skip empty products
            
            
            $productData = [
                'name' => $productPost['name'] ?? '',
                'price' => (float)($productPost['price'] ?? 0),
                'color' => $productPost['color'] ?? '',
                'category' => $productPost['category'] ?? '',
                'subcategory' => $productPost['subcategory'] ?? '',
                'sub_subcategory' => $productPost['sub_subcategory'] ?? '',
                'deeper_sub_subcategory' => $productPost['deeper_sub_subcategory'] ?? '',
                'description' => $productPost['description'] ?? '',
                'featured' => isset($productPost['featured']),
                'sale' => isset($productPost['sale']),
                'available' => isset($productPost['available']),
                'stock' => (int)($productPost['stock'] ?? 0),
                'size_category' => $productPost['size_category'] ?? '',
                'selected_sizes' => $productPost['selected_sizes'] ?? '',
                'shoe_type' => $productPost['shoe_type'] ?? '',
                'material' => $productPost['material'] ?? '',
                'length' => $productPost['length'] ?? '',
                'width' => $productPost['width'] ?? '',
                'bedding_size' => $productPost['bedding_size'] ?? '',
                'chair_count' => $productPost['chair_count'] ?? '',
                'table_length' => $productPost['table_length'] ?? '',
                'table_width' => $productPost['table_width'] ?? '',
                'sofa_count' => $productPost['sofa_count'] ?? ''
            ];
            
            // Force category to be "Perfumes" if it's any variation of "perfumes"
            if (strtolower($productData['category'] ?? '') === 'perfumes') {
                $productData['category'] = 'Perfumes';
                $productData['brand'] = $productPost['brand'] ?? '';
                $productData['gender'] = $productPost['gender'] ?? '';
                $productData['size'] = $productPost['size'] ?? '';
            }
            
            // Handle bags category with gender
            if (strtolower($productData['category'] ?? '') === 'bags') {
                $productData['gender'] = $productPost['gender'] ?? '';
            }
            
            // Handle accessories category with gender dropdown
            if (strtolower($productData['category'] ?? '') === 'accessories') {
                $productData['gender'] = $productPost['accessories_gender'] ?? '';
            }
            
            // Handle Home & Living category with specific fields
            if (strtolower($productData['category'] ?? '') === 'home & living') {
                $productData['material'] = $productPost['material'] ?? '';
                $productData['length'] = $productPost['length'] ?? '';
                $productData['width'] = $productPost['width'] ?? '';
                $productData['bedding_size'] = $productPost['bedding_size'] ?? '';
                $productData['chair_count'] = $productPost['chair_count'] ?? '';
                $productData['table_length'] = $productPost['table_length'] ?? '';
                $productData['table_width'] = $productPost['table_width'] ?? '';
                $productData['sofa_count'] = $productPost['sofa_count'] ?? '';
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
                        
                        // Handle bags-specific fields for variants
                        if (strtolower($productData['category']) === 'bags') {
                            $variantData['gender'] = $variant['gender'] ?? '';
                        }
                        
                        // Handle accessories-specific fields for variants
                        if (strtolower($productData['category']) === 'accessories') {
                            $variantData['gender'] = $variant['accessories_gender'] ?? '';
                        }

                                                // Handle variant images using restructured files
                        $frontImageProcessed = false;
                        if (isset($restructuredFiles[$productIndex][$variantIndex]['front_image'])) {
                            $frontFile = $restructuredFiles[$productIndex][$variantIndex]['front_image'];
                            $variantFrontImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_front_' . $frontFile['name'];
                            $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                            if (move_uploaded_file($frontFile['tmp_name'], $variantFrontImagePath)) {
                                $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                                $frontImageProcessed = true;
                            } else {
                            }
                        } else {
                        }
                        
                        // Fallback: Try direct $_FILES access if restructured files failed
                        if (!$frontImageProcessed && isset($_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['front_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants'][$variantIndex]['front_image'] === UPLOAD_ERR_OK) {
                            $variantFrontImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_front_' . $_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['front_image'];
                            $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants'][$variantIndex]['front_image'], $variantFrontImagePath)) {
                                $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                            } else {
                            }
                        }

                                                $backImageProcessed = false;
                        if (isset($restructuredFiles[$productIndex][$variantIndex]['back_image'])) {
                            $backFile = $restructuredFiles[$productIndex][$variantIndex]['back_image'];
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $backFile['name'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($backFile['tmp_name'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                                $backImageProcessed = true;
                            } else {
                            }
                        } else {
                        }
                        
                        // Fallback: Try direct $_FILES access if restructured files failed
                        if (!$backImageProcessed && isset($_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['back_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants'][$variantIndex]['back_image'] === UPLOAD_ERR_OK) {
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['back_image'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants'][$variantIndex]['back_image'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                            } else {
                            }
                        }
                        
                        $colorVariants[] = $variantData;
                    }
                }
                
                // Always set color variants, even if empty
                $productData['color_variants'] = $colorVariants;
            } else {
                // No color variants found, set empty array
                $productData['color_variants'] = [];
            }

            if (isset($productPost['sale']) && !empty($productPost['salePrice'])) {
                $productData['salePrice'] = (float)$productPost['salePrice'];
            }

            // Validate and create product
            
            // Final check: Ensure color_variants is properly set
            if (!isset($productData['color_variants'])) {
                $productData['color_variants'] = [];
            }
            
            // Validate color variants structure
            if (isset($productData['color_variants']) && is_array($productData['color_variants'])) {
                foreach ($productData['color_variants'] as $vIndex => $variant) {
                    if (!isset($variant['name']) || !isset($variant['color'])) {
                    }
                }
            }
            
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
            'sub_subcategory' => $_POST['sub_subcategory'] ?? '',
            'deeper_sub_subcategory' => $_POST['deeper_sub_subcategory'] ?? '',
            'description' => $_POST['description'] ?? '',
            'featured' => isset($_POST['featured']),
            'sale' => isset($_POST['sale']),
            'available' => isset($_POST['available']),
            'stock' => (int)($_POST['stock'] ?? 0),
            'size_category' => $_POST['size_category'] ?? '',
            'selected_sizes' => $_POST['selected_sizes'] ?? '',
            'shoe_type' => $_POST['shoe_type'] ?? '',
            'material' => $_POST['material'] ?? '',
            'length' => $_POST['length'] ?? '',
            'width' => $_POST['width'] ?? '',
            'bedding_size' => $_POST['bedding_size'] ?? '',
            'chair_count' => $_POST['chair_count'] ?? '',
            'table_length' => $_POST['table_length'] ?? '',
            'table_width' => $_POST['table_width'] ?? '',
            'sofa_count' => $_POST['sofa_count'] ?? ''
        ];
        
        // Force category to be "Perfumes" if it's any variation of "perfumes"
        if (strtolower($productData['category'] ?? '') === 'perfumes') {
            $productData['category'] = 'Perfumes';
            $productData['brand'] = $_POST['brand'] ?? '';
            $productData['gender'] = $_POST['gender'] ?? '';
            $productData['size'] = $_POST['size'] ?? '';
        }
        
        // Handle bags category with gender
        if (strtolower($productData['category'] ?? '') === 'bags') {
            $productData['gender'] = $_POST['gender'] ?? '';
        }
        
        // Handle accessories category with gender dropdown
        if (strtolower($productData['category'] ?? '') === 'accessories') {
            $productData['gender'] = $_POST['accessories_gender'] ?? '';
        }
        
        // Handle Home & Living category with specific fields
        if (strtolower($productData['category'] ?? '') === 'home & living') {
            $productData['material'] = $_POST['material'] ?? '';
            $productData['length'] = $_POST['length'] ?? '';
            $productData['width'] = $_POST['width'] ?? '';
            $productData['bedding_size'] = $_POST['bedding_size'] ?? '';
            $productData['chair_count'] = $_POST['chair_count'] ?? '';
            $productData['table_length'] = $_POST['table_length'] ?? '';
            $productData['table_width'] = $_POST['table_width'] ?? '';
            $productData['sofa_count'] = $_POST['sofa_count'] ?? '';
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
                    
                    // Handle bags-specific fields for variants
                    if (strtolower($productData['category']) === 'bags') {
                        $variantData['gender'] = $variant['gender'] ?? '';
                    }
                    
                    // Handle accessories-specific fields for variants
                    if (strtolower($productData['category']) === 'accessories') {
                        $variantData['gender'] = $variant['accessories_gender'] ?? '';
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
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin-right: 20px;
            font-size: 14px;
            color: #333;
        }
        
        .checkbox-label input[type="checkbox"] {
            display: none;
        }
        
        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .checkbox-label:hover .checkmark {
            border-color: #667eea;
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
            display: none !important;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-options.show {
            display: flex !important;
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
            <button type="button" class="generate-forms-btn" id="generate-forms-btn">
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
            
            <div class="form-group" id="accessories-gender-group" style="display: none;">
                <label for="accessories_gender">Gender *</label>
                <select id="accessories_gender" name="accessories_gender">
                    <option value="">Select Gender</option>
                    <option value="men">Men</option>
                    <option value="women">Women</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>
            
            <div class="form-group">
                            <label for="subcategory">Subcategory <span id="subcategory-required" style="color: #dc3545;">*</span></label>
                            <select id="subcategory" name="subcategory" onchange="handleHomeLivingSubcategory(); loadSubSubcategories(); refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Subcategory</option>
                </select>
                        </div>
            
            <!-- Sub-subcategory field (Beauty & Cosmetics, Kids' Clothing) -->
            <div class="form-group" id="sub-subcategory-group" style="display: none;">
                <label for="sub_subcategory">Sub-Subcategory</label>
                <select id="sub_subcategory" name="sub_subcategory" onchange="loadDeeperSubSubcategories(); refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Sub-Subcategory</option>
                </select>
                        </div>
            
            <!-- Deeper Sub-subcategory field (Makeup only) -->
            <div class="form-group" id="deeper-sub-subcategory-group" style="display: none;">
                <label for="deeper_sub_subcategory">Deeper Sub-Subcategory</label>
                <select id="deeper_sub_subcategory" name="deeper_sub_subcategory" onchange="refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Deeper Sub-Subcategory</option>
                </select>
            </div>
            
            <!-- Home & Living specific fields -->
            <div class="form-group" id="home-living-fields" style="display: none;">
                <div class="form-group">
                    <label for="material">Material</label>
                    <select id="material" name="material">
                        <option value="">Select Material</option>
                        <option value="Wood">Wood</option>
                        <option value="Metal">Metal</option>
                        <option value="Fabric">Fabric</option>
                        <option value="Glass">Glass</option>
                        <option value="Ceramic">Ceramic</option>
                        <option value="Plastic">Plastic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="length">Length (cm)</label>
                    <input type="number" id="length" name="length" placeholder="Length in centimeters">
                </div>
                
                <div class="form-group">
                    <label for="width">Width (cm)</label>
                    <input type="number" id="width" name="width" placeholder="Width in centimeters">
                </div>
                
                <!-- Bedding specific fields -->
                <div class="form-group" id="bedding-fields" style="display: none;">
                    <label for="bedding_size">Bedding Size</label>
                    <select id="bedding_size" name="bedding_size">
                        <option value="">Select Bedding Size</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Queen">Queen</option>
                        <option value="King">King</option>
                        <option value="Super King">Super King</option>
                    </select>
                </div>
                
                <!-- Dining specific fields -->
                <div class="form-group" id="dining-fields" style="display: none;">
                    <label for="chair_count">Number of Chairs</label>
                    <input type="number" id="chair_count" name="chair_count" placeholder="Number of chairs" min="1">
                    
                    <label for="table_length">Table Length (cm)</label>
                    <input type="number" id="table_length" name="table_length" placeholder="Table length in centimeters">
                    
                    <label for="table_width">Table Width (cm)</label>
                    <input type="number" id="table_width" name="table_width" placeholder="Table width in centimeters">
                </div>
                
                <!-- Living Room specific fields -->
                <div class="form-group" id="living-fields" style="display: none;">
                    <label for="sofa_count">Number of Sofas</label>
                    <input type="number" id="sofa_count" name="sofa_count" placeholder="Number of sofas" min="1">
                </div>
            </div>
            
            <div class="form-group" id="shoe-type-group" style="display: none;">
                <label for="shoe_type">Shoe Type</label>
                <select id="shoe_type" name="shoe_type">
                    <option value="">Select Shoe Type</option>
                    <option value="boots">Boots</option>
                    <option value="sandals">Sandals</option>
                    <option value="heels">Heels</option>
                    <option value="flats">Flats</option>
                    <option value="sneakers">Sneakers</option>
                    <option value="sport-shoes">Sport Shoes</option>
                    <option value="slippers">Slippers</option>
                    <option value="formal-shoes">Formal Shoes</option>
                    <option value="casual-shoes">Casual Shoes</option>
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
                    <option value="male">Male</option>
                    <option value="female">Female</option>
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
                    <option value="beauty">Beauty & Cosmetics</option>
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
            
            // Load subcategories from database
            fetch(`get-subcategories.php?category=${encodeURIComponent(category)}`)
                .then(response => {
                    return response.text().then(text => {
                        if (text.trim() === '') {
                            throw new Error('Empty response received');
                        }
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON: ' + e.message);
                        }
                    });
                })
                .then(data => {
                    if (data.success && data.subcategories) {
                        data.subcategories.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory;
                            option.textContent = subcategory;
                            subcategorySelect.appendChild(option);
                        });
                    } else {
                    }
                })
                .catch(error => {
                    console.error('Single form: Error loading subcategories:', error);
                });
            
            // Show/hide perfume-specific fields
            togglePerfumeFields(category);
        }
        
        function togglePerfumeFields(category) {
            const brandGroup = document.getElementById('brand-group');
            const genderGroup = document.getElementById('gender-group');
            const accessoriesGenderGroup = document.getElementById('accessories-gender-group');
            const perfumeSizeGroup = document.getElementById('perfume-size-group');
            const shoeTypeGroup = document.getElementById('shoe-type-group');
            const homeLivingGroup = document.getElementById('home-living-fields');
            const subcategoryGroup = document.querySelector('.form-group:has(#subcategory)');
            const sizeCategoryGroup = document.querySelector('.form-group:has(#size_category)');
            
            const isPerfume = category.toLowerCase() === 'perfumes';
            const isShoes = category.toLowerCase() === 'shoes';
            const isBags = category.toLowerCase() === 'bags';
            const isAccessories = category.toLowerCase() === 'accessories';
            const isHomeLiving = category.toLowerCase() === 'home & living';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = isPerfume ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = (isPerfume || isBags) ? 'block' : 'none';
            if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = isAccessories ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = isPerfume ? 'block' : 'none';
            
            // Show/hide shoe type field
            if (shoeTypeGroup) shoeTypeGroup.style.display = isShoes ? 'block' : 'none';
            
            // Show/hide Home & Living fields
            if (homeLivingGroup) homeLivingGroup.style.display = isHomeLiving ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = isPerfume ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = isPerfume ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById('brand');
            const genderField = document.getElementById('gender');
            const sizeField = document.getElementById('perfume_size');
            
            if (brandField) brandField.required = isPerfume;
            if (genderField) genderField.required = (isPerfume || isBags);
            if (sizeField) sizeField.required = isPerfume;
            
            // Make accessories gender dropdown required
            const accessoriesGenderField = document.getElementById('accessories_gender');
            if (accessoriesGenderField) accessoriesGenderField.required = isAccessories;
        }

        function toggleSalePrice() {
            const saleCheckbox = document.getElementById('sale');
            const salePriceGroup = document.getElementById('salePriceGroup');
            salePriceGroup.style.display = saleCheckbox.checked ? 'block' : 'none';
        }


        
        // Function to handle subcategory changes for Home & Living
        function handleHomeLivingSubcategory() {
            const subcategorySelect = document.getElementById('subcategory');
            const categorySelect = document.getElementById('category');
            const subcategory = subcategorySelect.value;
            const category = categorySelect.value;
            
            if (category.toLowerCase() !== 'home & living') return;
            
            // Get all subcategory-specific field groups
            const beddingFields = document.getElementById('bedding-fields');
            const diningFields = document.getElementById('dining-fields');
            const livingFields = document.getElementById('living-fields');
            
            // Hide all subcategory fields first
            if (beddingFields) beddingFields.style.display = 'none';
            if (diningFields) diningFields.style.display = 'none';
            if (livingFields) livingFields.style.display = 'none';
            
            // Show relevant fields based on subcategory
            if (subcategory.toLowerCase() === 'bedding' && beddingFields) {
                beddingFields.style.display = 'block';
            } else if ((subcategory.toLowerCase() === 'dining room' || subcategory.toLowerCase() === 'dinning room') && diningFields) {
                diningFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'living room' && livingFields) {
                livingFields.style.display = 'block';
            }
        }



        let colorVariantIndex = 0;

        function addColorVariant() {
            const container = document.getElementById('color-variants-container');
            const currentCategory = document.getElementById('category').value;
            const isPerfume = currentCategory.toLowerCase() === 'perfumes';
            const isBags = currentCategory.toLowerCase() === 'bags';
            const isAccessories = currentCategory.toLowerCase() === 'accessories';
            
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
                    ${isBags ? `
                    <!-- Bags-specific gender field for variants -->
                    <div class="form-group">
                        <label>Variant Gender</label>
                        <select name="color_variants[${colorVariantIndex}][gender]">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    ${isAccessories ? `
                    <!-- Accessories-specific gender dropdown for variants -->
                    <div class="form-group">
                        <label>Variant Gender *</label>
                        <select name="color_variants[${colorVariantIndex}][accessories_gender]">
                            <option value="">Select Gender</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    <!-- Regular size category for non-perfumes -->
                    <div class="form-group">
                        <label>Variant Size Category</label>
                        <select name="color_variants[${colorVariantIndex}][size_category]" onchange="loadVariantSizeOptions(${colorVariantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="beauty">Beauty & Cosmetics</option>
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections
                const subcategory = document.getElementById('subcategory').value;
                const subSubcategory = document.getElementById('sub_subcategory').value;
                sizeDropdownContent.innerHTML = generateFilteredBeautySizes(false, null, subcategory, subSubcategory);
            }
            
            // Event listeners are handled by onclick attributes in the HTML
        }

        function generateClothingSizes(isVariant = false, variantIndex = null) {
            return `
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'infant', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👶 Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'infant')` : 'selectAllInCategory(\'infant\')'}">
                            <input type="checkbox" id="select_all_infant${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_infant">
                            <label for="select_all_infant${isVariant ? '_' + variantIndex : ''}">✓ Select All Infant</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '0M')` : 'toggleSize(\'0M\')'}">
                            <input type="checkbox" id="size_0M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="0M">
                            <label for="size_0M${isVariant ? '_' + variantIndex : ''}">0M (EU 50)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '3M')` : 'toggleSize(\'3M\')'}">
                            <input type="checkbox" id="size_3M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="3M">
                            <label for="size_3M${isVariant ? '_' + variantIndex : ''}">3M (EU 56)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '6M')` : 'toggleSize(\'6M\')'}">
                            <input type="checkbox" id="size_6M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="6M">
                            <label for="size_6M${isVariant ? '_' + variantIndex : ''}">6M (EU 62)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '9M')` : 'toggleSize(\'9M\')'}">
                            <input type="checkbox" id="size_9M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="9M">
                            <label for="size_9M${isVariant ? '_' + variantIndex : ''}">9M (EU 68)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '12M')` : 'toggleSize(\'12M\')'}">
                            <input type="checkbox" id="size_12M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="12M">
                            <label for="size_12M${isVariant ? '_' + variantIndex : ''}">12M (EU 74)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '18M')` : 'toggleSize(\'18M\')'}">
                            <input type="checkbox" id="size_18M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="18M">
                            <label for="size_18M${isVariant ? '_' + variantIndex : ''}">18M (EU 80)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '24M')` : 'toggleSize(\'24M\')'}">
                            <input type="checkbox" id="size_24M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="24M">
                            <label for="size_24M${isVariant ? '_' + variantIndex : ''}">24M (EU 86)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'toddler', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🧒 Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'toddler')` : 'selectAllInCategory(\'toddler\')'}">
                            <input type="checkbox" id="select_all_toddler${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_toddler">
                            <label for="select_all_toddler${isVariant ? '_' + variantIndex : ''}">✓ Select All Toddler</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '2T')` : 'toggleSize(\'2T\')'}">
                            <input type="checkbox" id="size_2T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="2T">
                            <label for="size_2T${isVariant ? '_' + variantIndex : ''}">2T (EU 92)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '3T')` : 'toggleSize(\'3T\')'}">
                            <input type="checkbox" id="size_3T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="3T">
                            <label for="size_3T${isVariant ? '_' + variantIndex : ''}">3T (EU 98)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '4T')` : 'toggleSize(\'4T\')'}">
                            <input type="checkbox" id="size_4T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="4T">
                            <label for="size_4T${isVariant ? '_' + variantIndex : ''}">4T (EU 104)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'children', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👦 Children (4-14 years)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'children')` : 'selectAllInCategory(\'children\')'}">
                            <input type="checkbox" id="select_all_children${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_children">
                            <label for="select_all_children${isVariant ? '_' + variantIndex : ''}">✓ Select All Children</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '4Y')` : 'toggleSize(\'4Y\')'}">
                            <input type="checkbox" id="size_4Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="4Y">
                            <label for="size_4Y${isVariant ? '_' + variantIndex : ''}">4Y (EU 110)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '5Y')` : 'toggleSize(\'5Y\')'}">
                            <input type="checkbox" id="size_5Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="5Y">
                            <label for="size_5Y${isVariant ? '_' + variantIndex : ''}">5Y (EU 116)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '6Y')` : 'toggleSize(\'6Y\')'}">
                            <input type="checkbox" id="size_6Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="6Y">
                            <label for="size_6Y${isVariant ? '_' + variantIndex : ''}">6Y (EU 122)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '7Y')` : 'toggleSize(\'7Y\')'}">
                            <input type="checkbox" id="size_7Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="7Y">
                            <label for="size_7Y${isVariant ? '_' + variantIndex : ''}">7Y (EU 128)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '8Y')` : 'toggleSize(\'8Y\')'}">
                            <input type="checkbox" id="size_8Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="8Y">
                            <label for="size_8Y${isVariant ? '_' + variantIndex : ''}">8Y (EU 134)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '10Y')` : 'toggleSize(\'10Y\')'}">
                            <input type="checkbox" id="size_10Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="10Y">
                            <label for="size_10Y${isVariant ? '_' + variantIndex : ''}">10Y (EU 140)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '12Y')` : 'toggleSize(\'12Y\')'}">
                            <input type="checkbox" id="size_12Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="12Y">
                            <label for="size_12Y${isVariant ? '_' + variantIndex : ''}">12Y (EU 146)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '14Y')` : 'toggleSize(\'14Y\')'}">
                            <input type="checkbox" id="size_14Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="14Y">
                            <label for="size_14Y${isVariant ? '_' + variantIndex : ''}">14Y (EU 152)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'women', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👩 Women (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'women')` : 'selectAllInCategory(\'women\')'}">
                            <input type="checkbox" id="select_all_women${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_women">
                            <label for="select_all_women${isVariant ? '_' + variantIndex : ''}">✓ Select All Women</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'X')` : 'toggleSize(\'X\')'}">
                            <input type="checkbox" id="size_X${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="X">
                            <label for="size_X${isVariant ? '_' + variantIndex : ''}">X (EU 34-36)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'S')` : 'toggleSize(\'S\')'}">
                            <input type="checkbox" id="size_S${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="S">
                            <label for="size_S${isVariant ? '_' + variantIndex : ''}">S (EU 36-38)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'M')` : 'toggleSize(\'M\')'}">
                            <input type="checkbox" id="size_M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="M">
                            <label for="size_M${isVariant ? '_' + variantIndex : ''}">M (EU 38-40)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'L')` : 'toggleSize(\'L\')'}">
                            <input type="checkbox" id="size_L${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="L">
                            <label for="size_L${isVariant ? '_' + variantIndex : ''}">L (EU 40-42)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XL')` : 'toggleSize(\'XL\')'}">
                            <input type="checkbox" id="size_XL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XL">
                            <label for="size_XL${isVariant ? '_' + variantIndex : ''}">XL (EU 42-44)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XXL')` : 'toggleSize(\'XXL\')'}">
                            <input type="checkbox" id="size_XXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XXL">
                            <label for="size_XXL${isVariant ? '_' + variantIndex : ''}">XXL (EU 44-46)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'men', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👨 Men (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'men')` : 'selectAllInCategory(\'men\')'}">
                            <input type="checkbox" id="select_all_men${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_men">
                            <label for="select_all_men${isVariant ? '_' + variantIndex : ''}">✓ Select All Men</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'X')` : 'toggleSize(\'X\')'}">
                            <input type="checkbox" id="size_MX${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="X">
                            <label for="size_MX${isVariant ? '_' + variantIndex : ''}">X (EU 46-48)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'S')` : 'toggleSize(\'S\')'}">
                            <input type="checkbox" id="size_MS${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="S">
                            <label for="size_MS${isVariant ? '_' + variantIndex : ''}">S (EU 48-50)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'M')` : 'toggleSize(\'M\')'}">
                            <input type="checkbox" id="size_MM${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="M">
                            <label for="size_MM${isVariant ? '_' + variantIndex : ''}">M (EU 50-52)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'L')` : 'toggleSize(\'L\')'}">
                            <input type="checkbox" id="size_ML${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="L">
                            <label for="size_ML${isVariant ? '_' + variantIndex : ''}">L (EU 52-54)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XL')` : 'toggleSize(\'XL\')'}">
                            <input type="checkbox" id="size_MXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XL">
                            <label for="size_MXL${isVariant ? '_' + variantIndex : ''}">XL (EU 54-56)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XXL')` : 'toggleSize(\'XXL\')'}">
                            <input type="checkbox" id="size_MXXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XXL">
                            <label for="size_MXXL${isVariant ? '_' + variantIndex : ''}">XXL (EU 56-58)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'infant', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👶 Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'toddler', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>🧒 Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'children', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👦 Children (4-14 years)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'women', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👩 Women (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'men', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👨 Men (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections
                const subcategory = document.getElementById('subcategory').value;
                const subSubcategory = document.getElementById('sub_subcategory').value;
                sizeDropdownContent.innerHTML = generateFilteredBeautySizes(true, variantIndex, subcategory, subSubcategory);
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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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
            try {
                const count = parseInt(document.getElementById('product-count').value);
                
                if (count === 1) {
                    showSingleProductForm();
                } else {
                    showMultiProductForm(count);
                }
            } catch (error) {
                console.error('Error in generateProductForms:', error);
                alert('Error generating forms: ' + error.message);
            }
        }

        // Ensure function is available globally
        window.generateProductForms = generateProductForms;

        function showSingleProductForm() {
            document.getElementById('single-product-form').classList.add('form-active');
            document.getElementById('multi-product-form').classList.remove('form-active');
        }

        function showMultiProductForm(count) {
            try {
                document.getElementById('single-product-form').classList.remove('form-active');
                document.getElementById('multi-product-form').classList.add('form-active');
                
                const container = document.getElementById('multi-product-forms-container');
                if (!container) {
                    console.error('multi-product-forms-container not found');
                    return;
                }
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
                
                // Initialize multi-product form functionality
                setTimeout(() => {
                    initializeMultiProductForms();
                }, 100);
                
            } catch (error) {
                console.error('Error in showMultiProductForm:', error);
                alert('Error showing multi-product form: ' + error.message);
            }
        }

        function generateProductFormHTML(productIndex) {
            try {
                // Try to get categories from PHP, with fallback
                let categories;
                try {
                    categories = <?php echo json_encode(array_column($categories, 'name')); ?>;
                } catch (e) {
                    console.warn('PHP categories failed, using fallback');
                    categories = ['Perfumes', 'Bags', 'Shoes', 'Accessories', 'Home & Living'];
                }
                
                
                if (!categories || !Array.isArray(categories)) {
                    console.error('Categories not loaded properly:', categories);
                    categories = ['Perfumes', 'Bags', 'Shoes', 'Accessories', 'Home & Living'];
                }
                
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
                                <select id="category-${productIndex}" name="products[${productIndex}][category]" required>
                                    ${categoryOptions}
                                </select>
                            </div>
                            
                            <div class="form-group" id="accessories-gender-group-${productIndex}" style="display: none;">
                                <label for="accessories_gender-${productIndex}">Gender *</label>
                                <select id="accessories_gender-${productIndex}" name="products[${productIndex}][accessories_gender]">
                                    <option value="">Select Gender</option>
                                    <option value="men">Men</option>
                                    <option value="women">Women</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subcategory-${productIndex}">Subcategory <span id="subcategory-required-${productIndex}" style="color: #dc3545;">*</span></label>
                                <select id="subcategory-${productIndex}" name="products[${productIndex}][subcategory]" onchange="handleMultiHomeLivingSubcategory(${productIndex}); loadMultiSubSubcategories(${productIndex}); refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Sub-subcategory field (Beauty & Cosmetics, Kids' Clothing) -->
                            <div class="form-group" id="sub-subcategory-group-${productIndex}" style="display: none;">
                                <label for="sub_subcategory-${productIndex}">Sub-Subcategory</label>
                                <select id="sub_subcategory-${productIndex}" name="products[${productIndex}][sub_subcategory]" onchange="loadMultiDeeperSubSubcategories(${productIndex}); refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Sub-Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Deeper Sub-subcategory field for multi-product (Makeup only) -->
                            <div class="form-group" id="deeper-sub-subcategory-group-${productIndex}" style="display: none;">
                                <label for="deeper_sub_subcategory-${productIndex}">Deeper Sub-Subcategory</label>
                                <select id="deeper_sub_subcategory-${productIndex}" name="products[${productIndex}][deeper_sub_subcategory]" onchange="refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Deeper Sub-Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Home & Living specific fields for multi-product -->
                            <div class="form-group" id="home-living-fields-${productIndex}" style="display: none;">
                                <div class="form-group">
                                    <label for="material-${productIndex}">Material</label>
                                    <select id="material-${productIndex}" name="products[${productIndex}][material]">
                                        <option value="">Select Material</option>
                                        <option value="Wood">Wood</option>
                                        <option value="Metal">Metal</option>
                                        <option value="Fabric">Fabric</option>
                                        <option value="Glass">Glass</option>
                                        <option value="Ceramic">Ceramic</option>
                                        <option value="Plastic">Plastic</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="length-${productIndex}">Length (cm)</label>
                                    <input type="number" id="length-${productIndex}" name="products[${productIndex}][length]" placeholder="Length in centimeters">
                                </div>
                                
                                <div class="form-group">
                                    <label for="width-${productIndex}">Width (cm)</label>
                                    <input type="number" id="width-${productIndex}" name="products[${productIndex}][width]" placeholder="Width in centimeters">
                                </div>
                                
                                <!-- Bedding specific fields -->
                                <div class="form-group" id="bedding-fields-${productIndex}" style="display: none;">
                                    <label for="bedding_size-${productIndex}">Bedding Size</label>
                                    <select id="bedding_size-${productIndex}" name="products[${productIndex}][bedding_size]">
                                        <option value="">Select Bedding Size</option>
                                        <option value="Single">Single</option>
                                        <option value="Double">Double</option>
                                        <option value="Queen">Queen</option>
                                        <option value="King">King</option>
                                        <option value="Super King">Super King</option>
                                    </select>
                                </div>
                                
                                <!-- Dining specific fields -->
                                <div class="form-group" id="dining-fields-${productIndex}" style="display: none;">
                                    <label for="chair_count-${productIndex}">Number of Chairs</label>
                                    <input type="number" id="chair_count-${productIndex}" name="products[${productIndex}][chair_count]" placeholder="Number of chairs" min="1">
                                    
                                    <label for="table_length-${productIndex}">Table Length (cm)</label>
                                    <input type="number" id="table_length-${productIndex}" name="products[${productIndex}][table_length]" placeholder="Table length in centimeters">
                                    
                                    <label for="table_width-${productIndex}">Table Width (cm)</label>
                                    <input type="number" id="table_width-${productIndex}" name="products[${productIndex}][table_width]" placeholder="Table width in centimeters">
                                </div>
                                
                                <!-- Living Room specific fields -->
                                <div class="form-group" id="living-fields-${productIndex}" style="display: none;">
                                    <label for="sofa_count-${productIndex}">Number of Sofas</label>
                                    <input type="number" id="sofa_count-${productIndex}" name="products[${productIndex}][sofa_count]" placeholder="Number of sofas" min="1">
                                </div>
                            </div>
                            
                            <div class="form-group" id="shoe-type-group-${productIndex}" style="display: none;">
                                <label for="shoe_type-${productIndex}">Shoe Type</label>
                                <select id="shoe_type-${productIndex}" name="products[${productIndex}][shoe_type]">
                                    <option value="">Select Shoe Type</option>
                                    <option value="boots">Boots</option>
                                    <option value="sandals">Sandals</option>
                                    <option value="heels">Heels</option>
                                    <option value="flats">Flats</option>
                                    <option value="sneakers">Sneakers</option>
                                    <option value="sport-shoes">Sport Shoes</option>
                                    <option value="slippers">Slippers</option>
                                    <option value="formal-shoes">Formal Shoes</option>
                                    <option value="casual-shoes">Casual Shoes</option>
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
                                    <option value="beauty">Beauty & Cosmetics</option>
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
            } catch (error) {
                console.error('Error generating product form HTML:', error);
                return `<div class="error">Error generating form: ${error.message}</div>`;
            }
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

        // REMOVED DUPLICATE FUNCTION - Using the better one below
        
        function toggleMultiPerfumeFields(productIndex, category) {
            const brandGroup = document.getElementById(`brand-group-${productIndex}`);
            const genderGroup = document.getElementById(`gender-group-${productIndex}`);
            const accessoriesGenderGroup = document.getElementById(`accessories-gender-group-${productIndex}`);
            const perfumeSizeGroup = document.getElementById(`perfume-size-group-${productIndex}`);
            const shoeTypeGroup = document.getElementById(`shoe-type-group-${productIndex}`);
            const homeLivingGroup = document.getElementById(`home-living-fields-${productIndex}`);
            const subcategoryGroup = document.querySelector(`.form-group:has(#subcategory-${productIndex})`);
            const sizeCategoryGroup = document.querySelector(`.form-group:has(#size_category-${productIndex})`);
            
            const isPerfume = category.toLowerCase() === 'perfumes';
            const isShoes = category.toLowerCase() === 'shoes';
            const isBags = category.toLowerCase() === 'bags';
            const isAccessories = category.toLowerCase() === 'accessories';
            const isHomeLiving = category.toLowerCase() === 'home & living';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = isPerfume ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = (isPerfume || isBags) ? 'block' : 'none';
            if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = isAccessories ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = isPerfume ? 'block' : 'none';
            
            // Show/hide shoe type field
            if (shoeTypeGroup) shoeTypeGroup.style.display = isShoes ? 'block' : 'none';
            
            // Show/hide Home & Living fields
            if (homeLivingGroup) homeLivingGroup.style.display = isHomeLiving ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = isPerfume ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = isPerfume ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById(`brand-${productIndex}`);
            const genderField = document.getElementById(`gender-${productIndex}`);
            const accessoriesGenderField = document.getElementById(`accessories_gender-${productIndex}`);
            const sizeField = document.getElementById(`perfume_size-${productIndex}`);
            
            if (brandField) brandField.required = isPerfume;
            if (genderField) genderField.required = (isPerfume || isBags);
            if (accessoriesGenderField) accessoriesGenderField.required = isAccessories;
            if (sizeField) sizeField.required = isPerfume;
        }



        // Function to handle subcategory changes for Home & Living in multi-product form
        
        // Function to handle subcategory changes for Home & Living in multi-product form
        function handleMultiHomeLivingSubcategory(productIndex) {
            const subcategorySelect = document.getElementById(`subcategory-${productIndex}`);
            const categorySelect = document.getElementById(`category-${productIndex}`);
            const subcategory = subcategorySelect.value;
            const category = categorySelect.value;
            
            if (category.toLowerCase() !== 'home & living') return;
            
            // Get all subcategory-specific field groups
            const beddingFields = document.getElementById(`bedding-fields-${productIndex}`);
            const diningFields = document.getElementById(`dining-fields-${productIndex}`);
            const livingFields = document.getElementById(`living-fields-${productIndex}`);
            
            // Hide all subcategory fields first
            if (beddingFields) beddingFields.style.display = 'none';
            if (diningFields) diningFields.style.display = 'none';
            if (livingFields) livingFields.style.display = 'none';
            
            // Show relevant fields based on subcategory
            if (subcategory.toLowerCase() === 'bedding' && beddingFields) {
                beddingFields.style.display = 'block';
            } else if ((subcategory.toLowerCase() === 'dining room' || subcategory.toLowerCase() === 'dinning room') && diningFields) {
                diningFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'living room' && livingFields) {
                livingFields.style.display = 'block';
            }
        }
        
        function generateMultiClothingSizes(productIndex) {
            return `
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'infant', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👶 Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'toddler', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>🧒 Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'children', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👦 Children (4-14 years)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'women', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👩 Women (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'men', true, ${productIndex})">
                    <div class="size-category-header">
                        <span>👨 Men (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
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

<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// MongoDB connection
require_once '../config1/mongodb.php';
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
        
        // Helper function to restructure $_FILES for multi-product forms
        function restructureMultiProductFiles($files) {
            $restructured = [];
            
            if (!isset($files['name']['products']) || !is_array($files['name']['products'])) {
                return $restructured;
            }
            
            foreach ($files['name']['products'] as $productIndex => $productFiles) {
                if (isset($productFiles['color_variants']) && is_array($productFiles['color_variants'])) {
                    foreach ($productFiles['color_variants'] as $variantIndex => $variantFiles) {
                        // Handle front image
                        if (isset($variantFiles['front_image']) && !empty($variantFiles['front_image'])) {
                            $error = $files['error']['products'][$productIndex]['color_variants'][$variantIndex]['front_image'] ?? UPLOAD_ERR_NO_FILE;
                            if ($error === UPLOAD_ERR_OK) {
                                $restructured[$productIndex][$variantIndex]['front_image'] = [
                                    'name' => $variantFiles['front_image'],
                                    'tmp_name' => $files['tmp_name']['products'][$productIndex]['color_variants'][$variantIndex]['front_image'],
                                    'error' => $error,
                                    'size' => $files['size']['products'][$productIndex]['color_variants'][$variantIndex]['front_image']
                                ];
                            } else {
                            }
                        }
                        
                        // Handle back image
                        if (isset($variantFiles['back_image']) && !empty($variantFiles['back_image'])) {
                            $error = $files['error']['products'][$productIndex]['color_variants'][$variantIndex]['back_image'] ?? UPLOAD_ERR_NO_FILE;
                            if ($error === UPLOAD_ERR_OK) {
                                $restructured[$productIndex][$variantIndex]['back_image'] = [
                                    'name' => $variantFiles['back_image'],
                                    'tmp_name' => $files['tmp_name']['products'][$productIndex]['color_variants'][$variantIndex]['back_image'],
                                    'error' => $error,
                                    'size' => $files['size']['products'][$productIndex]['color_variants'][$variantIndex]['back_image']
                                ];
                            } else {
                            }
                        }
                    }
                }
            }
            
            return $restructured;
        }
        
        $restructuredFiles = restructureMultiProductFiles($_FILES);
        
        // Multiple products submission
        foreach ($_POST['products'] as $productIndex => $productPost) {
            if (empty($productPost['name'])) continue; // Skip empty products
            
            
            $productData = [
                'name' => $productPost['name'] ?? '',
                'price' => (float)($productPost['price'] ?? 0),
                'color' => $productPost['color'] ?? '',
                'category' => $productPost['category'] ?? '',
                'subcategory' => $productPost['subcategory'] ?? '',
                'sub_subcategory' => $productPost['sub_subcategory'] ?? '',
                'deeper_sub_subcategory' => $productPost['deeper_sub_subcategory'] ?? '',
                'description' => $productPost['description'] ?? '',
                'featured' => isset($productPost['featured']),
                'sale' => isset($productPost['sale']),
                'available' => isset($productPost['available']),
                'stock' => (int)($productPost['stock'] ?? 0),
                'size_category' => $productPost['size_category'] ?? '',
                'selected_sizes' => $productPost['selected_sizes'] ?? '',
                'shoe_type' => $productPost['shoe_type'] ?? '',
                'material' => $productPost['material'] ?? '',
                'length' => $productPost['length'] ?? '',
                'width' => $productPost['width'] ?? '',
                'bedding_size' => $productPost['bedding_size'] ?? '',
                'chair_count' => $productPost['chair_count'] ?? '',
                'table_length' => $productPost['table_length'] ?? '',
                'table_width' => $productPost['table_width'] ?? '',
                'sofa_count' => $productPost['sofa_count'] ?? ''
            ];
            
            // Force category to be "Perfumes" if it's any variation of "perfumes"
            if (strtolower($productData['category'] ?? '') === 'perfumes') {
                $productData['category'] = 'Perfumes';
                $productData['brand'] = $productPost['brand'] ?? '';
                $productData['gender'] = $productPost['gender'] ?? '';
                $productData['size'] = $productPost['size'] ?? '';
            }
            
            // Handle bags category with gender
            if (strtolower($productData['category'] ?? '') === 'bags') {
                $productData['gender'] = $productPost['gender'] ?? '';
            }
            
            // Handle accessories category with gender dropdown
            if (strtolower($productData['category'] ?? '') === 'accessories') {
                $productData['gender'] = $productPost['accessories_gender'] ?? '';
            }
            
            // Handle Home & Living category with specific fields
            if (strtolower($productData['category'] ?? '') === 'home & living') {
                $productData['material'] = $productPost['material'] ?? '';
                $productData['length'] = $productPost['length'] ?? '';
                $productData['width'] = $productPost['width'] ?? '';
                $productData['bedding_size'] = $productPost['bedding_size'] ?? '';
                $productData['chair_count'] = $productPost['chair_count'] ?? '';
                $productData['table_length'] = $productPost['table_length'] ?? '';
                $productData['table_width'] = $productPost['table_width'] ?? '';
                $productData['sofa_count'] = $productPost['sofa_count'] ?? '';
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
                        
                        // Handle bags-specific fields for variants
                        if (strtolower($productData['category']) === 'bags') {
                            $variantData['gender'] = $variant['gender'] ?? '';
                        }
                        
                        // Handle accessories-specific fields for variants
                        if (strtolower($productData['category']) === 'accessories') {
                            $variantData['gender'] = $variant['accessories_gender'] ?? '';
                        }

                                                // Handle variant images using restructured files
                        $frontImageProcessed = false;
                        if (isset($restructuredFiles[$productIndex][$variantIndex]['front_image'])) {
                            $frontFile = $restructuredFiles[$productIndex][$variantIndex]['front_image'];
                            $variantFrontImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_front_' . $frontFile['name'];
                            $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                            if (move_uploaded_file($frontFile['tmp_name'], $variantFrontImagePath)) {
                                $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                                $frontImageProcessed = true;
                            } else {
                            }
                        } else {
                        }
                        
                        // Fallback: Try direct $_FILES access if restructured files failed
                        if (!$frontImageProcessed && isset($_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['front_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants'][$variantIndex]['front_image'] === UPLOAD_ERR_OK) {
                            $variantFrontImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_front_' . $_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['front_image'];
                            $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants'][$variantIndex]['front_image'], $variantFrontImagePath)) {
                                $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                            } else {
                            }
                        }

                                                $backImageProcessed = false;
                        if (isset($restructuredFiles[$productIndex][$variantIndex]['back_image'])) {
                            $backFile = $restructuredFiles[$productIndex][$variantIndex]['back_image'];
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $backFile['name'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($backFile['tmp_name'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                                $backImageProcessed = true;
                            } else {
                            }
                        } else {
                        }
                        
                        // Fallback: Try direct $_FILES access if restructured files failed
                        if (!$backImageProcessed && isset($_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['back_image']) && 
                            $_FILES['products']['error'][$productIndex]['color_variants'][$variantIndex]['back_image'] === UPLOAD_ERR_OK) {
                            $variantBackImageName = time() . '_product_' . $productIndex . '_variant_' . $variantIndex . '_back_' . $_FILES['products']['name'][$productIndex]['color_variants'][$variantIndex]['back_image'];
                            $variantBackImagePath = $uploadDir . $variantBackImageName;
                            if (move_uploaded_file($_FILES['products']['tmp_name'][$productIndex]['color_variants'][$variantIndex]['back_image'], $variantBackImagePath)) {
                                $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                            } else {
                            }
                        }
                        
                        $colorVariants[] = $variantData;
                    }
                }
                
                // Always set color variants, even if empty
                $productData['color_variants'] = $colorVariants;
            } else {
                // No color variants found, set empty array
                $productData['color_variants'] = [];
            }

            if (isset($productPost['sale']) && !empty($productPost['salePrice'])) {
                $productData['salePrice'] = (float)$productPost['salePrice'];
            }

            // Validate and create product
            
            // Final check: Ensure color_variants is properly set
            if (!isset($productData['color_variants'])) {
                $productData['color_variants'] = [];
            }
            
            // Validate color variants structure
            if (isset($productData['color_variants']) && is_array($productData['color_variants'])) {
                foreach ($productData['color_variants'] as $vIndex => $variant) {
                    if (!isset($variant['name']) || !isset($variant['color'])) {
                    }
                }
            }
            
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
            'sub_subcategory' => $_POST['sub_subcategory'] ?? '',
            'deeper_sub_subcategory' => $_POST['deeper_sub_subcategory'] ?? '',
            'description' => $_POST['description'] ?? '',
            'featured' => isset($_POST['featured']),
            'sale' => isset($_POST['sale']),
            'available' => isset($_POST['available']),
            'stock' => (int)($_POST['stock'] ?? 0),
            'size_category' => $_POST['size_category'] ?? '',
            'selected_sizes' => $_POST['selected_sizes'] ?? '',
            'shoe_type' => $_POST['shoe_type'] ?? '',
            'material' => $_POST['material'] ?? '',
            'length' => $_POST['length'] ?? '',
            'width' => $_POST['width'] ?? '',
            'bedding_size' => $_POST['bedding_size'] ?? '',
            'chair_count' => $_POST['chair_count'] ?? '',
            'table_length' => $_POST['table_length'] ?? '',
            'table_width' => $_POST['table_width'] ?? '',
            'sofa_count' => $_POST['sofa_count'] ?? ''
        ];
        
        // Force category to be "Perfumes" if it's any variation of "perfumes"
        if (strtolower($productData['category'] ?? '') === 'perfumes') {
            $productData['category'] = 'Perfumes';
            $productData['brand'] = $_POST['brand'] ?? '';
            $productData['gender'] = $_POST['gender'] ?? '';
            $productData['size'] = $_POST['size'] ?? '';
        }
        
        // Handle bags category with gender
        if (strtolower($productData['category'] ?? '') === 'bags') {
            $productData['gender'] = $_POST['gender'] ?? '';
        }
        
        // Handle accessories category with gender dropdown
        if (strtolower($productData['category'] ?? '') === 'accessories') {
            $productData['gender'] = $_POST['accessories_gender'] ?? '';
        }
        
        // Handle Home & Living category with specific fields
        if (strtolower($productData['category'] ?? '') === 'home & living') {
            $productData['material'] = $_POST['material'] ?? '';
            $productData['length'] = $_POST['length'] ?? '';
            $productData['width'] = $_POST['width'] ?? '';
            $productData['bedding_size'] = $_POST['bedding_size'] ?? '';
            $productData['chair_count'] = $_POST['chair_count'] ?? '';
            $productData['table_length'] = $_POST['table_length'] ?? '';
            $productData['table_width'] = $_POST['table_width'] ?? '';
            $productData['sofa_count'] = $_POST['sofa_count'] ?? '';
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
                    
                    // Handle bags-specific fields for variants
                    if (strtolower($productData['category']) === 'bags') {
                        $variantData['gender'] = $variant['gender'] ?? '';
                    }
                    
                    // Handle accessories-specific fields for variants
                    if (strtolower($productData['category']) === 'accessories') {
                        $variantData['gender'] = $variant['accessories_gender'] ?? '';
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
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin-right: 20px;
            font-size: 14px;
            color: #333;
        }
        
        .checkbox-label input[type="checkbox"] {
            display: none;
        }
        
        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .checkbox-label input[type="checkbox"]:checked + .checkmark:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .checkbox-label:hover .checkmark {
            border-color: #667eea;
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
            display: none !important;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-options.show {
            display: flex !important;
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
            <button type="button" class="generate-forms-btn" id="generate-forms-btn">
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
            
            <div class="form-group" id="accessories-gender-group" style="display: none;">
                <label for="accessories_gender">Gender *</label>
                <select id="accessories_gender" name="accessories_gender">
                    <option value="">Select Gender</option>
                    <option value="men">Men</option>
                    <option value="women">Women</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>
            
            <div class="form-group">
                            <label for="subcategory">Subcategory <span id="subcategory-required" style="color: #dc3545;">*</span></label>
                            <select id="subcategory" name="subcategory" onchange="handleHomeLivingSubcategory(); loadSubSubcategories(); refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Subcategory</option>
                </select>
                        </div>
            
            <!-- Sub-subcategory field (Beauty & Cosmetics, Kids' Clothing) -->
            <div class="form-group" id="sub-subcategory-group" style="display: none;">
                <label for="sub_subcategory">Sub-Subcategory</label>
                <select id="sub_subcategory" name="sub_subcategory" onchange="loadDeeperSubSubcategories(); refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Sub-Subcategory</option>
                </select>
                        </div>
            
            <!-- Deeper Sub-subcategory field (Makeup only) -->
            <div class="form-group" id="deeper-sub-subcategory-group" style="display: none;">
                <label for="deeper_sub_subcategory">Deeper Sub-Subcategory</label>
                <select id="deeper_sub_subcategory" name="deeper_sub_subcategory" onchange="refreshSizeOptions(); refreshAllVariantSizeOptions();">
                    <option value="">Select Deeper Sub-Subcategory</option>
                </select>
            </div>
            
            <!-- Home & Living specific fields -->
            <div class="form-group" id="home-living-fields" style="display: none;">
                <div class="form-group">
                    <label for="material">Material</label>
                    <select id="material" name="material">
                        <option value="">Select Material</option>
                        <option value="Wood">Wood</option>
                        <option value="Metal">Metal</option>
                        <option value="Fabric">Fabric</option>
                        <option value="Glass">Glass</option>
                        <option value="Ceramic">Ceramic</option>
                        <option value="Plastic">Plastic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="length">Length (cm)</label>
                    <input type="number" id="length" name="length" placeholder="Length in centimeters">
                </div>
                
                <div class="form-group">
                    <label for="width">Width (cm)</label>
                    <input type="number" id="width" name="width" placeholder="Width in centimeters">
                </div>
                
                <!-- Bedding specific fields -->
                <div class="form-group" id="bedding-fields" style="display: none;">
                    <label for="bedding_size">Bedding Size</label>
                    <select id="bedding_size" name="bedding_size">
                        <option value="">Select Bedding Size</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Queen">Queen</option>
                        <option value="King">King</option>
                        <option value="Super King">Super King</option>
                    </select>
                </div>
                
                <!-- Dining specific fields -->
                <div class="form-group" id="dining-fields" style="display: none;">
                    <label for="chair_count">Number of Chairs</label>
                    <input type="number" id="chair_count" name="chair_count" placeholder="Number of chairs" min="1">
                    
                    <label for="table_length">Table Length (cm)</label>
                    <input type="number" id="table_length" name="table_length" placeholder="Table length in centimeters">
                    
                    <label for="table_width">Table Width (cm)</label>
                    <input type="number" id="table_width" name="table_width" placeholder="Table width in centimeters">
                </div>
                
                <!-- Living Room specific fields -->
                <div class="form-group" id="living-fields" style="display: none;">
                    <label for="sofa_count">Number of Sofas</label>
                    <input type="number" id="sofa_count" name="sofa_count" placeholder="Number of sofas" min="1">
                </div>
            </div>
            
            <div class="form-group" id="shoe-type-group" style="display: none;">
                <label for="shoe_type">Shoe Type</label>
                <select id="shoe_type" name="shoe_type">
                    <option value="">Select Shoe Type</option>
                    <option value="boots">Boots</option>
                    <option value="sandals">Sandals</option>
                    <option value="heels">Heels</option>
                    <option value="flats">Flats</option>
                    <option value="sneakers">Sneakers</option>
                    <option value="sport-shoes">Sport Shoes</option>
                    <option value="slippers">Slippers</option>
                    <option value="formal-shoes">Formal Shoes</option>
                    <option value="casual-shoes">Casual Shoes</option>
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
                    <option value="male">Male</option>
                    <option value="female">Female</option>
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
                    <option value="beauty">Beauty & Cosmetics</option>
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
            
            // Load subcategories from database
            fetch(`get-subcategories.php?category=${encodeURIComponent(category)}`)
                .then(response => {
                    return response.text().then(text => {
                        if (text.trim() === '') {
                            throw new Error('Empty response received');
                        }
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON: ' + e.message);
                        }
                    });
                })
                .then(data => {
                    if (data.success && data.subcategories) {
                        data.subcategories.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory;
                            option.textContent = subcategory;
                            subcategorySelect.appendChild(option);
                        });
                    } else {
                    }
                })
                .catch(error => {
                    console.error('Single form: Error loading subcategories:', error);
                });
            
            // Show/hide perfume-specific fields
            togglePerfumeFields(category);
        }
        
        function togglePerfumeFields(category) {
            const brandGroup = document.getElementById('brand-group');
            const genderGroup = document.getElementById('gender-group');
            const accessoriesGenderGroup = document.getElementById('accessories-gender-group');
            const perfumeSizeGroup = document.getElementById('perfume-size-group');
            const shoeTypeGroup = document.getElementById('shoe-type-group');
            const homeLivingGroup = document.getElementById('home-living-fields');
            const subcategoryGroup = document.querySelector('.form-group:has(#subcategory)');
            const sizeCategoryGroup = document.querySelector('.form-group:has(#size_category)');
            
            const isPerfume = category.toLowerCase() === 'perfumes';
            const isShoes = category.toLowerCase() === 'shoes';
            const isBags = category.toLowerCase() === 'bags';
            const isAccessories = category.toLowerCase() === 'accessories';
            const isHomeLiving = category.toLowerCase() === 'home & living';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = isPerfume ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = (isPerfume || isBags) ? 'block' : 'none';
            if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = isAccessories ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = isPerfume ? 'block' : 'none';
            
            // Show/hide shoe type field
            if (shoeTypeGroup) shoeTypeGroup.style.display = isShoes ? 'block' : 'none';
            
            // Show/hide Home & Living fields
            if (homeLivingGroup) homeLivingGroup.style.display = isHomeLiving ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = isPerfume ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = isPerfume ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById('brand');
            const genderField = document.getElementById('gender');
            const sizeField = document.getElementById('perfume_size');
            
            if (brandField) brandField.required = isPerfume;
            if (genderField) genderField.required = (isPerfume || isBags);
            if (sizeField) sizeField.required = isPerfume;
            
            // Make accessories gender dropdown required
            const accessoriesGenderField = document.getElementById('accessories_gender');
            if (accessoriesGenderField) accessoriesGenderField.required = isAccessories;
        }

        function toggleSalePrice() {
            const saleCheckbox = document.getElementById('sale');
            const salePriceGroup = document.getElementById('salePriceGroup');
            salePriceGroup.style.display = saleCheckbox.checked ? 'block' : 'none';
        }


        
        // Function to handle subcategory changes for Home & Living
        function handleHomeLivingSubcategory() {
            const subcategorySelect = document.getElementById('subcategory');
            const categorySelect = document.getElementById('category');
            const subcategory = subcategorySelect.value;
            const category = categorySelect.value;
            
            if (category.toLowerCase() !== 'home & living') return;
            
            // Get all subcategory-specific field groups
            const beddingFields = document.getElementById('bedding-fields');
            const diningFields = document.getElementById('dining-fields');
            const livingFields = document.getElementById('living-fields');
            
            // Hide all subcategory fields first
            if (beddingFields) beddingFields.style.display = 'none';
            if (diningFields) diningFields.style.display = 'none';
            if (livingFields) livingFields.style.display = 'none';
            
            // Show relevant fields based on subcategory
            if (subcategory.toLowerCase() === 'bedding' && beddingFields) {
                beddingFields.style.display = 'block';
            } else if ((subcategory.toLowerCase() === 'dining room' || subcategory.toLowerCase() === 'dinning room') && diningFields) {
                diningFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'living room' && livingFields) {
                livingFields.style.display = 'block';
            }
        }



        let colorVariantIndex = 0;

        function addColorVariant() {
            const container = document.getElementById('color-variants-container');
            const currentCategory = document.getElementById('category').value;
            const isPerfume = currentCategory.toLowerCase() === 'perfumes';
            const isBags = currentCategory.toLowerCase() === 'bags';
            const isAccessories = currentCategory.toLowerCase() === 'accessories';
            
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
                    ${isBags ? `
                    <!-- Bags-specific gender field for variants -->
                    <div class="form-group">
                        <label>Variant Gender</label>
                        <select name="color_variants[${colorVariantIndex}][gender]">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    ${isAccessories ? `
                    <!-- Accessories-specific gender dropdown for variants -->
                    <div class="form-group">
                        <label>Variant Gender *</label>
                        <select name="color_variants[${colorVariantIndex}][accessories_gender]">
                            <option value="">Select Gender</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    <!-- Regular size category for non-perfumes -->
                    <div class="form-group">
                        <label>Variant Size Category</label>
                        <select name="color_variants[${colorVariantIndex}][size_category]" onchange="loadVariantSizeOptions(${colorVariantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="beauty">Beauty & Cosmetics</option>
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections
                const subcategory = document.getElementById('subcategory').value;
                const subSubcategory = document.getElementById('sub_subcategory').value;
                sizeDropdownContent.innerHTML = generateFilteredBeautySizes(false, null, subcategory, subSubcategory);
            }
            
            // Event listeners are handled by onclick attributes in the HTML
        }

        function generateClothingSizes(isVariant = false, variantIndex = null) {
            return `
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'infant', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👶 Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'infant')` : 'selectAllInCategory(\'infant\')'}">
                            <input type="checkbox" id="select_all_infant${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_infant">
                            <label for="select_all_infant${isVariant ? '_' + variantIndex : ''}">✓ Select All Infant</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '0M')` : 'toggleSize(\'0M\')'}">
                            <input type="checkbox" id="size_0M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="0M">
                            <label for="size_0M${isVariant ? '_' + variantIndex : ''}">0M (EU 50)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '3M')` : 'toggleSize(\'3M\')'}">
                            <input type="checkbox" id="size_3M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="3M">
                            <label for="size_3M${isVariant ? '_' + variantIndex : ''}">3M (EU 56)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '6M')` : 'toggleSize(\'6M\')'}">
                            <input type="checkbox" id="size_6M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="6M">
                            <label for="size_6M${isVariant ? '_' + variantIndex : ''}">6M (EU 62)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '9M')` : 'toggleSize(\'9M\')'}">
                            <input type="checkbox" id="size_9M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="9M">
                            <label for="size_9M${isVariant ? '_' + variantIndex : ''}">9M (EU 68)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '12M')` : 'toggleSize(\'12M\')'}">
                            <input type="checkbox" id="size_12M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="12M">
                            <label for="size_12M${isVariant ? '_' + variantIndex : ''}">12M (EU 74)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '18M')` : 'toggleSize(\'18M\')'}">
                            <input type="checkbox" id="size_18M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="18M">
                            <label for="size_18M${isVariant ? '_' + variantIndex : ''}">18M (EU 80)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '24M')` : 'toggleSize(\'24M\')'}">
                            <input type="checkbox" id="size_24M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="24M">
                            <label for="size_24M${isVariant ? '_' + variantIndex : ''}">24M (EU 86)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'toddler', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🧒 Toddler (2-4 years)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'toddler')` : 'selectAllInCategory(\'toddler\')'}">
                            <input type="checkbox" id="select_all_toddler${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_toddler">
                            <label for="select_all_toddler${isVariant ? '_' + variantIndex : ''}">✓ Select All Toddler</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '2T')` : 'toggleSize(\'2T\')'}">
                            <input type="checkbox" id="size_2T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="2T">
                            <label for="size_2T${isVariant ? '_' + variantIndex : ''}">2T (EU 92)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '3T')` : 'toggleSize(\'3T\')'}">
                            <input type="checkbox" id="size_3T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="3T">
                            <label for="size_3T${isVariant ? '_' + variantIndex : ''}">3T (EU 98)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '4T')` : 'toggleSize(\'4T\')'}">
                            <input type="checkbox" id="size_4T${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="4T">
                            <label for="size_4T${isVariant ? '_' + variantIndex : ''}">4T (EU 104)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'children', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👦 Children (4-14 years)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'children')` : 'selectAllInCategory(\'children\')'}">
                            <input type="checkbox" id="select_all_children${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_children">
                            <label for="select_all_children${isVariant ? '_' + variantIndex : ''}">✓ Select All Children</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '4Y')` : 'toggleSize(\'4Y\')'}">
                            <input type="checkbox" id="size_4Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="4Y">
                            <label for="size_4Y${isVariant ? '_' + variantIndex : ''}">4Y (EU 110)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '5Y')` : 'toggleSize(\'5Y\')'}">
                            <input type="checkbox" id="size_5Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="5Y">
                            <label for="size_5Y${isVariant ? '_' + variantIndex : ''}">5Y (EU 116)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '6Y')` : 'toggleSize(\'6Y\')'}">
                            <input type="checkbox" id="size_6Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="6Y">
                            <label for="size_6Y${isVariant ? '_' + variantIndex : ''}">6Y (EU 122)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '7Y')` : 'toggleSize(\'7Y\')'}">
                            <input type="checkbox" id="size_7Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="7Y">
                            <label for="size_7Y${isVariant ? '_' + variantIndex : ''}">7Y (EU 128)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '8Y')` : 'toggleSize(\'8Y\')'}">
                            <input type="checkbox" id="size_8Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="8Y">
                            <label for="size_8Y${isVariant ? '_' + variantIndex : ''}">8Y (EU 134)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '10Y')` : 'toggleSize(\'10Y\')'}">
                            <input type="checkbox" id="size_10Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="10Y">
                            <label for="size_10Y${isVariant ? '_' + variantIndex : ''}">10Y (EU 140)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '12Y')` : 'toggleSize(\'12Y\')'}">
                            <input type="checkbox" id="size_12Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="12Y">
                            <label for="size_12Y${isVariant ? '_' + variantIndex : ''}">12Y (EU 146)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '14Y')` : 'toggleSize(\'14Y\')'}">
                            <input type="checkbox" id="size_14Y${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="14Y">
                            <label for="size_14Y${isVariant ? '_' + variantIndex : ''}">14Y (EU 152)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'women', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👩 Women (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'women')` : 'selectAllInCategory(\'women\')'}">
                            <input type="checkbox" id="select_all_women${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_women">
                            <label for="select_all_women${isVariant ? '_' + variantIndex : ''}">✓ Select All Women</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'X')` : 'toggleSize(\'X\')'}">
                            <input type="checkbox" id="size_X${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="X">
                            <label for="size_X${isVariant ? '_' + variantIndex : ''}">X (EU 34-36)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'S')` : 'toggleSize(\'S\')'}">
                            <input type="checkbox" id="size_S${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="S">
                            <label for="size_S${isVariant ? '_' + variantIndex : ''}">S (EU 36-38)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'M')` : 'toggleSize(\'M\')'}">
                            <input type="checkbox" id="size_M${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="M">
                            <label for="size_M${isVariant ? '_' + variantIndex : ''}">M (EU 38-40)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'L')` : 'toggleSize(\'L\')'}">
                            <input type="checkbox" id="size_L${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="L">
                            <label for="size_L${isVariant ? '_' + variantIndex : ''}">L (EU 40-42)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XL')` : 'toggleSize(\'XL\')'}">
                            <input type="checkbox" id="size_XL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XL">
                            <label for="size_XL${isVariant ? '_' + variantIndex : ''}">XL (EU 42-44)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XXL')` : 'toggleSize(\'XXL\')'}">
                            <input type="checkbox" id="size_XXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XXL">
                            <label for="size_XXL${isVariant ? '_' + variantIndex : ''}">XXL (EU 44-46)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'men', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👨 Men (X-XXL)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'men')` : 'selectAllInCategory(\'men\')'}">
                            <input type="checkbox" id="select_all_men${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_men">
                            <label for="select_all_men${isVariant ? '_' + variantIndex : ''}">✓ Select All Men</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'X')` : 'toggleSize(\'X\')'}">
                            <input type="checkbox" id="size_MX${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="X">
                            <label for="size_MX${isVariant ? '_' + variantIndex : ''}">X (EU 46-48)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'S')` : 'toggleSize(\'S\')'}">
                            <input type="checkbox" id="size_MS${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="S">
                            <label for="size_MS${isVariant ? '_' + variantIndex : ''}">S (EU 48-50)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'M')` : 'toggleSize(\'M\')'}">
                            <input type="checkbox" id="size_MM${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="M">
                            <label for="size_MM${isVariant ? '_' + variantIndex : ''}">M (EU 50-52)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'L')` : 'toggleSize(\'L\')'}">
                            <input type="checkbox" id="size_ML${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="L">
                            <label for="size_ML${isVariant ? '_' + variantIndex : ''}">L (EU 52-54)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XL')` : 'toggleSize(\'XL\')'}">
                            <input type="checkbox" id="size_MXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XL">
                            <label for="size_MXL${isVariant ? '_' + variantIndex : ''}">XL (EU 54-56)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'XXL')` : 'toggleSize(\'XXL\')'}">
                            <input type="checkbox" id="size_MXXL${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="XXL">
                            <label for="size_MXXL${isVariant ? '_' + variantIndex : ''}">XXL (EU 56-58)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateShoeSizes(isVariant = false, variantIndex = null) {
            return `
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'infant_shoes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👶 Infant & Baby Shoes (0-24 months)</span>
                        <i class="fas fa-chevron-right"></i>
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
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'children_shoes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👦 Children Shoes (1-7 years)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'children_shoes')` : 'selectAllInCategory(\'children_shoes\')'}">
                            <input type="checkbox" id="select_all_children_shoes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_children_shoes">
                            <label for="select_all_children_shoes${isVariant ? '_' + variantIndex : ''}">✓ Select All Children Shoes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '23')` : 'toggleSize(\'23\')'}">
                            <input type="checkbox" id="size_23${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="23">
                            <label for="size_23${isVariant ? '_' + variantIndex : ''}">23 (EU 23)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '24')` : 'toggleSize(\'24\')'}">
                            <input type="checkbox" id="size_24${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="24">
                            <label for="size_24${isVariant ? '_' + variantIndex : ''}">24 (EU 24)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '25')` : 'toggleSize(\'25\')'}">
                            <input type="checkbox" id="size_25${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="25">
                            <label for="size_25${isVariant ? '_' + variantIndex : ''}">25 (EU 25)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '26')` : 'toggleSize(\'26\')'}">
                            <input type="checkbox" id="size_26${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="26">
                            <label for="size_26${isVariant ? '_' + variantIndex : ''}">26 (EU 26)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '27')` : 'toggleSize(\'27\')'}">
                            <input type="checkbox" id="size_27${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="27">
                            <label for="size_27${isVariant ? '_' + variantIndex : ''}">27 (EU 27)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '28')` : 'toggleSize(\'28\')'}">
                            <input type="checkbox" id="size_28${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="28">
                            <label for="size_28${isVariant ? '_' + variantIndex : ''}">28 (EU 28)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '28')` : 'toggleSize(\'28\')'}">
                            <input type="checkbox" id="size_29${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="29">
                            <label for="size_29${isVariant ? '_' + variantIndex : ''}">29 (EU 29)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '30')` : 'toggleSize(\'30\')'}">
                            <input type="checkbox" id="size_30${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="30">
                            <label for="size_29${isVariant ? '_' + variantIndex : ''}">30 (EU 30)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'women_shoes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👩 Women Shoes (EU 35-42)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'women_shoes')` : 'selectAllInCategory(\'women_shoes\')'}">
                            <input type="checkbox" id="select_all_women_shoes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_women_shoes">
                            <label for="select_all_women_shoes${isVariant ? '_' + variantIndex : ''}">✓ Select All Women Shoes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '35')` : 'toggleSize(\'35\')'}">
                            <input type="checkbox" id="size_35${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="35">
                            <label for="size_35${isVariant ? '_' + variantIndex : ''}">35 (EU 35)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '36')` : 'toggleSize(\'36\')'}">
                            <input type="checkbox" id="size_36${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="36">
                            <label for="size_36${isVariant ? '_' + variantIndex : ''}">36 (EU 36)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '37')` : 'toggleSize(\'37\')'}">
                            <input type="checkbox" id="size_37${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="37">
                            <label for="size_37${isVariant ? '_' + variantIndex : ''}">37 (EU 37)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '38')` : 'toggleSize(\'38\')'}">
                            <input type="checkbox" id="size_38${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="38">
                            <label for="size_38${isVariant ? '_' + variantIndex : ''}">38 (EU 38)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '39')` : 'toggleSize(\'39\')'}">
                            <input type="checkbox" id="size_39${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="39">
                            <label for="size_39${isVariant ? '_' + variantIndex : ''}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '40')` : 'toggleSize(\'40\')'}">
                            <input type="checkbox" id="size_40${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="40">
                            <label for="size_40${isVariant ? '_' + variantIndex : ''}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '41')` : 'toggleSize(\'41\')'}">
                            <input type="checkbox" id="size_41${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="41">
                            <label for="size_41${isVariant ? '_' + variantIndex : ''}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '42')` : 'toggleSize(\'42\')'}">
                            <input type="checkbox" id="size_42${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="42">
                            <label for="size_42${isVariant ? '_' + variantIndex : ''}">42 (EU 42)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'men_shoes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>👨 Men Shoes (EU 39-47)</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'men_shoes')` : 'selectAllInCategory(\'men_shoes\')'}">
                            <input type="checkbox" id="select_all_men_shoes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_men_shoes">
                            <label for="select_all_men_shoes${isVariant ? '_' + variantIndex : ''}">✓ Select All Men Shoes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '39')` : 'toggleSize(\'39\')'}">
                            <input type="checkbox" id="size_M39${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="39">
                            <label for="size_M39${isVariant ? '_' + variantIndex : ''}">39 (EU 39)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '40')` : 'toggleSize(\'40\')'}">
                            <input type="checkbox" id="size_M40${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="40">
                            <label for="size_M40${isVariant ? '_' + variantIndex : ''}">40 (EU 40)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '41')` : 'toggleSize(\'41\')'}">
                            <input type="checkbox" id="size_M41${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="41">
                            <label for="size_M41${isVariant ? '_' + variantIndex : ''}">41 (EU 41)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '42')` : 'toggleSize(\'42\')'}">
                            <input type="checkbox" id="size_M42${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="42">
                            <label for="size_M42${isVariant ? '_' + variantIndex : ''}">42 (EU 42)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '43')` : 'toggleSize(\'43\')'}">
                            <input type="checkbox" id="size_M43${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="43">
                            <label for="size_M43${isVariant ? '_' + variantIndex : ''}">43 (EU 43)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '44')` : 'toggleSize(\'44\')'}">
                            <input type="checkbox" id="size_M44${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="44">
                            <label for="size_M44${isVariant ? '_' + variantIndex : ''}">44 (EU 44)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '45')` : 'toggleSize(\'45\')'}">
                            <input type="checkbox" id="size_M45${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="45">
                            <label for="size_M45${isVariant ? '_' + variantIndex : ''}">45 (EU 45)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '46')` : 'toggleSize(\'46\')'}">
                            <input type="checkbox" id="size_M46${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="46">
                            <label for="size_M46${isVariant ? '_' + variantIndex : ''}">46 (EU 46)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '47')` : 'toggleSize(\'47\')'}">
                            <input type="checkbox" id="size_M47${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="47">
                            <label for="size_M47${isVariant ? '_' + variantIndex : ''}">47 (EU 47)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateBeautySizes(isVariant = false, variantIndex = null) {
            return `
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'makeup_sizes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>💄 Makeup Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'makeup_sizes')` : 'selectAllInCategory(\'makeup_sizes\')'}">
                            <input type="checkbox" id="select_all_makeup_sizes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_makeup_sizes">
                            <label for="select_all_makeup_sizes${isVariant ? '_' + variantIndex : ''}">✓ Select All Makeup Sizes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Sample')` : 'toggleSize(\'Sample\')'}">
                            <input type="checkbox" id="size_Sample${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Sample">
                            <label for="size_Sample${isVariant ? '_' + variantIndex : ''}">Sample (1-2ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Travel')` : 'toggleSize(\'Travel\')'}">
                            <input type="checkbox" id="size_Travel${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Travel">
                            <label for="size_Travel${isVariant ? '_' + variantIndex : ''}">Travel Size (5-10ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Regular')` : 'toggleSize(\'Regular\')'}">
                            <input type="checkbox" id="size_Regular${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Regular">
                            <label for="size_Regular${isVariant ? '_' + variantIndex : ''}">Regular (15-30ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Large')` : 'toggleSize(\'Large\')'}">
                            <input type="checkbox" id="size_Large${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Large">
                            <label for="size_Large${isVariant ? '_' + variantIndex : ''}">Large (50-100ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Jumbo')` : 'toggleSize(\'Jumbo\')'}">
                            <input type="checkbox" id="size_Jumbo${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Jumbo">
                            <label for="size_Jumbo${isVariant ? '_' + variantIndex : ''}">Jumbo (150ml+)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'makeup_tools', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🖌️ Makeup Tools</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'makeup_tools')` : 'selectAllInCategory(\'makeup_tools\')'}">
                            <input type="checkbox" id="select_all_makeup_tools${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_makeup_tools">
                            <label for="select_all_makeup_tools${isVariant ? '_' + variantIndex : ''}">✓ Select All Makeup Tools</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Foundation_Brush')` : 'toggleSize(\'Foundation_Brush\')'}">
                            <input type="checkbox" id="size_Foundation_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Foundation_Brush">
                            <label for="size_Foundation_Brush${isVariant ? '_' + variantIndex : ''}">Foundation Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Concealer_Brush')` : 'toggleSize(\'Concealer_Brush\')'}">
                            <input type="checkbox" id="size_Concealer_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Concealer_Brush">
                            <label for="size_Concealer_Brush${isVariant ? '_' + variantIndex : ''}">Concealer Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Eyeshadow_Brush')` : 'toggleSize(\'Eyeshadow_Brush\')'}">
                            <input type="checkbox" id="size_Eyeshadow_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Eyeshadow_Brush">
                            <label for="size_Eyeshadow_Brush${isVariant ? '_' + variantIndex : ''}">Eyeshadow Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Blush_Brush')` : 'toggleSize(\'Blush_Brush\')'}">
                            <input type="checkbox" id="size_Blush_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Blush_Brush">
                            <label for="size_Blush_Brush${isVariant ? '_' + variantIndex : ''}">Blush Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Lip_Brush')` : 'toggleSize(\'Lip_Brush\')'}">
                            <input type="checkbox" id="size_Lip_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Lip_Brush">
                            <label for="size_Lip_Brush${isVariant ? '_' + variantIndex : ''}">Lip Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Makeup_Remover')` : 'toggleSize(\'Makeup_Remover\')'}">
                            <input type="checkbox" id="size_Makeup_Remover${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Makeup_Remover">
                            <label for="size_Makeup_Remover${isVariant ? '_' + variantIndex : ''}">Makeup Remover</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Brush_Set')` : 'toggleSize(\'Brush_Set\')'}">
                            <input type="checkbox" id="size_Brush_Set${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Brush_Set">
                            <label for="size_Brush_Set${isVariant ? '_' + variantIndex : ''}">Brush Set</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'skincare_sizes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🧴 Skincare Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'skincare_sizes')` : 'selectAllInCategory(\'skincare_sizes\')'}">
                            <input type="checkbox" id="select_all_skincare_sizes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_skincare_sizes">
                            <label for="select_all_skincare_sizes${isVariant ? '_' + variantIndex : ''}">✓ Select All Skincare Sizes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Mini')` : 'toggleSize(\'Mini\')'}">
                            <input type="checkbox" id="size_Mini${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Mini">
                            <label for="size_Mini${isVariant ? '_' + variantIndex : ''}">Mini (15ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Small')` : 'toggleSize(\'Small\')'}">
                            <input type="checkbox" id="size_Small${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Small">
                            <label for="size_Small${isVariant ? '_' + variantIndex : ''}">Small (30ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Medium')` : 'toggleSize(\'Medium\')'}">
                            <input type="checkbox" id="size_Medium${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Medium">
                            <label for="size_Medium${isVariant ? '_' + variantIndex : ''}">Medium (50ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Large')` : 'toggleSize(\'Large\')'}">
                            <input type="checkbox" id="size_Large_skincare${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Large_skincare">
                            <label for="size_Large_skincare${isVariant ? '_' + variantIndex : ''}">Large (100ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Family')` : 'toggleSize(\'Family\')'}">
                            <input type="checkbox" id="size_Family${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Family">
                            <label for="size_Family${isVariant ? '_' + variantIndex : ''}">Family Size (200ml+)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'call_who_sizes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🌟 Call Who Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'call_who_sizes')` : 'selectAllInCategory(\'call_who_sizes\')'}">
                            <input type="checkbox" id="select_all_call_who_sizes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_call_who_sizes">
                            <label for="select_all_call_who_sizes${isVariant ? '_' + variantIndex : ''}">✓ Select All Call Who Sizes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Serum_15ml')` : 'toggleSize(\'Serum_15ml\')'}">
                            <input type="checkbox" id="size_Serum_15ml${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Serum_15ml">
                            <label for="size_Serum_15ml${isVariant ? '_' + variantIndex : ''}">Serum (15ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Toner_100ml')` : 'toggleSize(\'Toner_100ml\')'}">
                            <input type="checkbox" id="size_Toner_100ml${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Toner_100ml">
                            <label for="size_Toner_100ml${isVariant ? '_' + variantIndex : ''}">Toner (100ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Essence_30ml')` : 'toggleSize(\'Essence_30ml\')'}">
                            <input type="checkbox" id="size_Essence_30ml${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Essence_30ml">
                            <label for="size_Essence_30ml${isVariant ? '_' + variantIndex : ''}">Essence (30ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Spot_Treatment_10ml')` : 'toggleSize(\'Spot_Treatment_10ml\')'}">
                            <input type="checkbox" id="size_Spot_Treatment_10ml${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Spot_Treatment_10ml">
                            <label for="size_Spot_Treatment_10ml${isVariant ? '_' + variantIndex : ''}">Spot Treatment (10ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Call_Who_Set')` : 'toggleSize(\'Call_Who_Set\')'}">
                            <input type="checkbox" id="size_Call_Who_Set${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Call_Who_Set">
                            <label for="size_Call_Who_Set${isVariant ? '_' + variantIndex : ''}">Call Who Set</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'hair_sizes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>💇 Hair Care Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'hair_sizes')` : 'selectAllInCategory(\'hair_sizes\')'}">
                            <input type="checkbox" id="select_all_hair_sizes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_hair_sizes">
                            <label for="select_all_hair_sizes${isVariant ? '_' + variantIndex : ''}">✓ Select All Hair Sizes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Trial')` : 'toggleSize(\'Trial\')'}">
                            <input type="checkbox" id="size_Trial${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Trial">
                            <label for="size_Trial${isVariant ? '_' + variantIndex : ''}">Trial Size (50ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Standard')` : 'toggleSize(\'Standard\')'}">
                            <input type="checkbox" id="size_Standard${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Standard">
                            <label for="size_Standard${isVariant ? '_' + variantIndex : ''}">Standard (250ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Professional')` : 'toggleSize(\'Professional\')'}">
                            <input type="checkbox" id="size_Professional${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Professional">
                            <label for="size_Professional${isVariant ? '_' + variantIndex : ''}">Professional (500ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Salon')` : 'toggleSize(\'Salon\')'}">
                            <input type="checkbox" id="size_Salon${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Salon">
                            <label for="size_Salon${isVariant ? '_' + variantIndex : ''}">Salon Size (1L+)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'hair_tools', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🔧 Hair Tools</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'hair_tools')` : 'selectAllInCategory(\'hair_tools\')'}">
                            <input type="checkbox" id="select_all_hair_tools${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_hair_tools">
                            <label for="select_all_hair_tools${isVariant ? '_' + variantIndex : ''}">✓ Select All Hair Tools</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Hair_Dryer')` : 'toggleSize(\'Hair_Dryer\')'}">
                            <input type="checkbox" id="size_Hair_Dryer${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Hair_Dryer">
                            <label for="size_Hair_Dryer${isVariant ? '_' + variantIndex : ''}">Hair Dryer</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Straightener')` : 'toggleSize(\'Straightener\')'}">
                            <input type="checkbox" id="size_Straightener${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Straightener">
                            <label for="size_Straightener${isVariant ? '_' + variantIndex : ''}">Straightener</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Curling_Iron')` : 'toggleSize(\'Curling_Iron\')'}">
                            <input type="checkbox" id="size_Curling_Iron${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Curling_Iron">
                            <label for="size_Curling_Iron${isVariant ? '_' + variantIndex : ''}">Curling Iron</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Hair_Brush')` : 'toggleSize(\'Hair_Brush\')'}">
                            <input type="checkbox" id="size_Hair_Brush${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Hair_Brush">
                            <label for="size_Hair_Brush${isVariant ? '_' + variantIndex : ''}">Hair Brush</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Comb')` : 'toggleSize(\'Comb\')'}">
                            <input type="checkbox" id="size_Comb${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Comb">
                            <label for="size_Comb${isVariant ? '_' + variantIndex : ''}">Comb</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Hair_Clips')` : 'toggleSize(\'Hair_Clips\')'}">
                            <input type="checkbox" id="size_Hair_Clips${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Hair_Clips">
                            <label for="size_Hair_Clips${isVariant ? '_' + variantIndex : ''}">Hair Clips</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category main-category" onclick="toggleMainSizeCategory(this, 'bath_body_sizes', ${isVariant}, ${variantIndex})">
                    <div class="size-category-header">
                        <span>🛁 Bath & Body Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, 'bath_body_sizes')` : 'selectAllInCategory(\'bath_body_sizes\')'}">
                            <input type="checkbox" id="select_all_bath_body_sizes${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_bath_body_sizes">
                            <label for="select_all_bath_body_sizes${isVariant ? '_' + variantIndex : ''}">✓ Select All Bath & Body Sizes</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Travel_Kit')` : 'toggleSize(\'Travel_Kit\')'}">
                            <input type="checkbox" id="size_Travel_Kit${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Travel_Kit">
                            <label for="size_Travel_Kit${isVariant ? '_' + variantIndex : ''}">Travel Kit (30ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Personal')` : 'toggleSize(\'Personal\')'}">
                            <input type="checkbox" id="size_Personal${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Personal">
                            <label for="size_Personal${isVariant ? '_' + variantIndex : ''}">Personal (100ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Family')` : 'toggleSize(\'Family\')'}">
                            <input type="checkbox" id="size_Family_bath${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Family_bath">
                            <label for="size_Family_bath${isVariant ? '_' + variantIndex : ''}">Family (250ml)</label>
                        </div>
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, 'Economy')` : 'toggleSize(\'Economy\')'}">
                            <input type="checkbox" id="size_Economy${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="Economy">
                            <label for="size_Economy${isVariant ? '_' + variantIndex : ''}">Economy (500ml+)</label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Generate filtered beauty sizes based on subcategory and sub-subcategory
        function generateFilteredBeautySizes(isVariant = false, variantIndex = null, subcategory = '', subSubcategory = '') {
            let html = '';
            
            // Define size categories and their relevance to subcategories
            const sizeCategories = {
                'makeup_sizes': {
                    name: '💄 Makeup Sizes',
                    sizes: ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                    relevantSubcategories: ['Makeup'],
                    relevantSubSubcategories: ['Face', 'Eye', 'Lip', 'Nail']
                },
                'makeup_tools': {
                    name: '🖌️ Makeup Tools',
                    sizes: ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                    relevantSubcategories: ['Makeup'],
                    relevantSubSubcategories: ['Face', 'Eye', 'Lip', 'Nail']
                },
                'skincare_sizes': {
                    name: '🧴 Skincare Sizes',
                    sizes: ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                    relevantSubcategories: ['Skincare'],
                    relevantSubSubcategories: ['Moisturizers', 'Cleansers', 'Masks', 'Sun Care', 'cream']
                },
                'call_who_sizes': {
                    name: '🌟 Call Who Sizes',
                    sizes: ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                    relevantSubcategories: ['Skincare'],
                    relevantSubSubcategories: ['Moisturizers', 'Cleansers', 'Masks', 'Sun Care', 'cream']
                },
                'hair_sizes': {
                    name: '💇 Hair Care Sizes',
                    sizes: ['Trial', 'Standard', 'Professional', 'Salon'],
                    relevantSubcategories: ['Hair'],
                    relevantSubSubcategories: ['Shampoo', 'Conditioner', 'Tools']
                },
                'hair_tools': {
                    name: '🔧 Hair Tools',
                    sizes: ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                    relevantSubcategories: ['Hair'],
                    relevantSubSubcategories: ['Shampoo', 'Conditioner', 'Tools']
                },
                'bath_body_sizes': {
                    name: '🛁 Bath & Body Sizes',
                    sizes: ['Travel_Kit', 'Personal', 'Family_bath', 'Economy'],
                    relevantSubcategories: ['Bath & Body'],
                    relevantSubSubcategories: ['Shower gel', 'Scrubs', 'soap']
                }
            };
            
            // Filter size categories based on subcategory and sub-subcategory
            const relevantCategories = Object.entries(sizeCategories).filter(([key, category]) => {
                // If no subcategory is selected, show all categories
                if (!subcategory) return true;
                
                // Check if subcategory matches
                const subcategoryMatch = category.relevantSubcategories.includes(subcategory);
                
                // If sub-subcategory is selected, also check that
                if (subSubcategory) {
                    const subSubcategoryMatch = category.relevantSubSubcategories.includes(subSubcategory);
                    return subcategoryMatch && subSubcategoryMatch;
                }
                
                return subcategoryMatch;
            });
            
            // Generate HTML for relevant categories only
            relevantCategories.forEach(([categoryKey, category]) => {
                html += `
                <div class="size-category main-category">
                    <div class="size-category-header" onclick="toggleMainSizeCategory(this, '${categoryKey}', ${isVariant}, ${variantIndex})">
                        <span>${category.name}</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option select-all-option" onclick="${isVariant ? `selectAllInVariantCategory(${variantIndex}, '${categoryKey}')` : `selectAllInCategory('${categoryKey}')`}">
                            <input type="checkbox" id="select_all_${categoryKey}${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="select_all_${categoryKey}">
                            <label for="select_all_${categoryKey}${isVariant ? '_' + variantIndex : ''}">✓ Select All ${category.name.replace(/[^\w\s]/g, '')}</label>
                        </div>`;
                
                category.sizes.forEach(size => {
                    const sizeId = size.replace(/[^a-zA-Z0-9]/g, '_');
                    html += `
                        <div class="size-option" onclick="${isVariant ? `toggleVariantSize(${variantIndex}, '${size}')` : `toggleSize('${size}')`}">
                            <input type="checkbox" id="size_${sizeId}${isVariant ? '_' + variantIndex : ''}" name="sizes[]" value="${size}">
                            <label for="size_${sizeId}${isVariant ? '_' + variantIndex : ''}">${size.replace(/_/g, ' ')}</label>
                        </div>`;
                });
                
                html += `
                    </div>
                </div>`;
            });
            
            // If no relevant categories found, show a message
            if (relevantCategories.length === 0) {
                html = `
                <div class="no-sizes-message">
                    <p>No specific sizes available for the selected subcategory/sub-subcategory combination.</p>
                    <p>Please select a subcategory and sub-subcategory to see relevant size options.</p>
                </div>`;
            }
            
            return html;
        }

        // Function to refresh size options when subcategory or sub-subcategory changes
        function refreshSizeOptions() {
            const sizeCategory = document.getElementById('size_category').value;
            if (sizeCategory === 'beauty') {
                loadSizeOptions();
            }
        }

        // Function to refresh multi-product size options when subcategory or sub-subcategory changes
        function refreshMultiSizeOptions(productIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][size_category]"]`).value;
            if (sizeCategory === 'beauty') {
                loadMultiSizeOptions(productIndex);
            }
        }

        // Function to refresh variant size options when subcategory or sub-subcategory changes
        function refreshVariantSizeOptions(variantIndex) {
            const variant = document.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).closest('.variant-item');
            const sizeCategory = variant.querySelector(`[name="color_variants[${variantIndex}][size_category]"]`).value;
            if (sizeCategory === 'beauty') {
                loadVariantSizeOptions(variantIndex);
            }
        }

        // Function to refresh multi-product variant size options when subcategory or sub-subcategory changes
        function refreshMultiVariantSizeOptions(productIndex, variantIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][color_variants][${variantIndex}][size_category]"]`).value;
            if (sizeCategory === 'beauty') {
                loadMultiVariantSizeOptions(productIndex, variantIndex);
            }
        }

        // Function to refresh all variant size options (for single product variants)
        function refreshAllVariantSizeOptions() {
            // Find all variant size category selects
            const variantSizeSelects = document.querySelectorAll('select[name^="color_variants["][name$="[size_category]"]');
            variantSizeSelects.forEach(select => {
                const variantIndex = select.name.match(/color_variants\[(\d+)\]/)[1];
                refreshVariantSizeOptions(variantIndex);
            });
        }

        // Function to refresh all multi-product variant size options for a specific product
        function refreshAllMultiVariantSizeOptions(productIndex) {
            // Find all multi-product variant size category selects for this product
            const variantSizeSelects = document.querySelectorAll(`select[name^="products[${productIndex}][color_variants]["][name$="[size_category]"]`);
            variantSizeSelects.forEach(select => {
                const match = select.name.match(/products\[\d+\]\[color_variants\]\[(\d+)\]/);
                if (match) {
                    const variantIndex = match[1];
                    refreshMultiVariantSizeOptions(productIndex, variantIndex);
                }
            });
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

        function toggleSizeCategory(headerElement, productIndex) {
            // For multi-product forms, find the size options within the same dropdown
            const sizeOptions = headerElement.nextElementSibling;
            const chevronIcon = headerElement.querySelector('i');
            
            // Toggle the expanded state
            headerElement.classList.toggle('expanded');
            sizeOptions.classList.toggle('show');
            
            // Rotate the chevron icon
            if (headerElement.classList.contains('expanded')) {
                chevronIcon.style.transform = 'rotate(90deg)';
            } else {
                chevronIcon.style.transform = 'rotate(0deg)';
            }
        }

        function toggleMainSizeCategory(categoryElement, categoryType, isVariant, variantIndex) {
            // Handle the case where variantIndex might be passed as string "null"
            if (variantIndex === 'null' || variantIndex === null) {
                variantIndex = null;
            }
            
            const sizeOptions = categoryElement.querySelector('.size-options');
            const chevronIcon = categoryElement.querySelector('i');
            
            if (!sizeOptions) {
                console.error('Size options not found!');
                return;
            }
            
            // Toggle the expanded state
            categoryElement.classList.toggle('expanded');
            sizeOptions.classList.toggle('show');
            
            // Rotate the chevron icon
            if (sizeOptions.classList.contains('show')) {
                chevronIcon.style.transform = 'rotate(90deg)';
                chevronIcon.className = 'fas fa-chevron-down';
            } else {
                chevronIcon.style.transform = 'rotate(0deg)';
                chevronIcon.className = 'fas fa-chevron-right';
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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections
                const subcategory = document.getElementById('subcategory').value;
                const subSubcategory = document.getElementById('sub_subcategory').value;
                sizeDropdownContent.innerHTML = generateFilteredBeautySizes(true, variantIndex, subcategory, subSubcategory);
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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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
            try {
                const count = parseInt(document.getElementById('product-count').value);
                
                if (count === 1) {
                    showSingleProductForm();
                } else {
                    showMultiProductForm(count);
                }
            } catch (error) {
                console.error('Error in generateProductForms:', error);
                alert('Error generating forms: ' + error.message);
            }
        }

        // Ensure function is available globally
        window.generateProductForms = generateProductForms;

        function showSingleProductForm() {
            document.getElementById('single-product-form').classList.add('form-active');
            document.getElementById('multi-product-form').classList.remove('form-active');
        }

        function showMultiProductForm(count) {
            try {
                document.getElementById('single-product-form').classList.remove('form-active');
                document.getElementById('multi-product-form').classList.add('form-active');
                
                const container = document.getElementById('multi-product-forms-container');
                if (!container) {
                    console.error('multi-product-forms-container not found');
                    return;
                }
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
                
                // Initialize multi-product form functionality
                setTimeout(() => {
                    initializeMultiProductForms();
                }, 100);
                
            } catch (error) {
                console.error('Error in showMultiProductForm:', error);
                alert('Error showing multi-product form: ' + error.message);
            }
        }

        function generateProductFormHTML(productIndex) {
            try {
                // Try to get categories from PHP, with fallback
                let categories;
                try {
                    categories = <?php echo json_encode(array_column($categories, 'name')); ?>;
                } catch (e) {
                    console.warn('PHP categories failed, using fallback');
                    categories = ['Perfumes', 'Bags', 'Shoes', 'Accessories', 'Home & Living'];
                }
                
                
                if (!categories || !Array.isArray(categories)) {
                    console.error('Categories not loaded properly:', categories);
                    categories = ['Perfumes', 'Bags', 'Shoes', 'Accessories', 'Home & Living'];
                }
                
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
                                <select id="category-${productIndex}" name="products[${productIndex}][category]" required>
                                    ${categoryOptions}
                                </select>
                            </div>
                            
                            <div class="form-group" id="accessories-gender-group-${productIndex}" style="display: none;">
                                <label for="accessories_gender-${productIndex}">Gender *</label>
                                <select id="accessories_gender-${productIndex}" name="products[${productIndex}][accessories_gender]">
                                    <option value="">Select Gender</option>
                                    <option value="men">Men</option>
                                    <option value="women">Women</option>
                                    <option value="unisex">Unisex</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subcategory-${productIndex}">Subcategory <span id="subcategory-required-${productIndex}" style="color: #dc3545;">*</span></label>
                                <select id="subcategory-${productIndex}" name="products[${productIndex}][subcategory]" onchange="handleMultiHomeLivingSubcategory(${productIndex}); loadMultiSubSubcategories(${productIndex}); refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Sub-subcategory field (Beauty & Cosmetics, Kids' Clothing) -->
                            <div class="form-group" id="sub-subcategory-group-${productIndex}" style="display: none;">
                                <label for="sub_subcategory-${productIndex}">Sub-Subcategory</label>
                                <select id="sub_subcategory-${productIndex}" name="products[${productIndex}][sub_subcategory]" onchange="loadMultiDeeperSubSubcategories(${productIndex}); refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Sub-Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Deeper Sub-subcategory field for multi-product (Makeup only) -->
                            <div class="form-group" id="deeper-sub-subcategory-group-${productIndex}" style="display: none;">
                                <label for="deeper_sub_subcategory-${productIndex}">Deeper Sub-Subcategory</label>
                                <select id="deeper_sub_subcategory-${productIndex}" name="products[${productIndex}][deeper_sub_subcategory]" onchange="refreshMultiSizeOptions(${productIndex}); refreshAllMultiVariantSizeOptions(${productIndex});">
                                    <option value="">Select Deeper Sub-Subcategory</option>
                                </select>
                            </div>
                            
                            <!-- Home & Living specific fields for multi-product -->
                            <div class="form-group" id="home-living-fields-${productIndex}" style="display: none;">
                                <div class="form-group">
                                    <label for="material-${productIndex}">Material</label>
                                    <select id="material-${productIndex}" name="products[${productIndex}][material]">
                                        <option value="">Select Material</option>
                                        <option value="Wood">Wood</option>
                                        <option value="Metal">Metal</option>
                                        <option value="Fabric">Fabric</option>
                                        <option value="Glass">Glass</option>
                                        <option value="Ceramic">Ceramic</option>
                                        <option value="Plastic">Plastic</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="length-${productIndex}">Length (cm)</label>
                                    <input type="number" id="length-${productIndex}" name="products[${productIndex}][length]" placeholder="Length in centimeters">
                                </div>
                                
                                <div class="form-group">
                                    <label for="width-${productIndex}">Width (cm)</label>
                                    <input type="number" id="width-${productIndex}" name="products[${productIndex}][width]" placeholder="Width in centimeters">
                                </div>
                                
                                <!-- Bedding specific fields -->
                                <div class="form-group" id="bedding-fields-${productIndex}" style="display: none;">
                                    <label for="bedding_size-${productIndex}">Bedding Size</label>
                                    <select id="bedding_size-${productIndex}" name="products[${productIndex}][bedding_size]">
                                        <option value="">Select Bedding Size</option>
                                        <option value="Single">Single</option>
                                        <option value="Double">Double</option>
                                        <option value="Queen">Queen</option>
                                        <option value="King">King</option>
                                        <option value="Super King">Super King</option>
                                    </select>
                                </div>
                                
                                <!-- Dining specific fields -->
                                <div class="form-group" id="dining-fields-${productIndex}" style="display: none;">
                                    <label for="chair_count-${productIndex}">Number of Chairs</label>
                                    <input type="number" id="chair_count-${productIndex}" name="products[${productIndex}][chair_count]" placeholder="Number of chairs" min="1">
                                    
                                    <label for="table_length-${productIndex}">Table Length (cm)</label>
                                    <input type="number" id="table_length-${productIndex}" name="products[${productIndex}][table_length]" placeholder="Table length in centimeters">
                                    
                                    <label for="table_width-${productIndex}">Table Width (cm)</label>
                                    <input type="number" id="table_width-${productIndex}" name="products[${productIndex}][table_width]" placeholder="Table width in centimeters">
                                </div>
                                
                                <!-- Living Room specific fields -->
                                <div class="form-group" id="living-fields-${productIndex}" style="display: none;">
                                    <label for="sofa_count-${productIndex}">Number of Sofas</label>
                                    <input type="number" id="sofa_count-${productIndex}" name="products[${productIndex}][sofa_count]" placeholder="Number of sofas" min="1">
                                </div>
                            </div>
                            
                            <div class="form-group" id="shoe-type-group-${productIndex}" style="display: none;">
                                <label for="shoe_type-${productIndex}">Shoe Type</label>
                                <select id="shoe_type-${productIndex}" name="products[${productIndex}][shoe_type]">
                                    <option value="">Select Shoe Type</option>
                                    <option value="boots">Boots</option>
                                    <option value="sandals">Sandals</option>
                                    <option value="heels">Heels</option>
                                    <option value="flats">Flats</option>
                                    <option value="sneakers">Sneakers</option>
                                    <option value="sport-shoes">Sport Shoes</option>
                                    <option value="slippers">Slippers</option>
                                    <option value="formal-shoes">Formal Shoes</option>
                                    <option value="casual-shoes">Casual Shoes</option>
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
                                    <option value="beauty">Beauty & Cosmetics</option>
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
            } catch (error) {
                console.error('Error generating product form HTML:', error);
                return `<div class="error">Error generating form: ${error.message}</div>`;
            }
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

        // REMOVED DUPLICATE FUNCTION - Using the better one below
        
        function toggleMultiPerfumeFields(productIndex, category) {
            const brandGroup = document.getElementById(`brand-group-${productIndex}`);
            const genderGroup = document.getElementById(`gender-group-${productIndex}`);
            const accessoriesGenderGroup = document.getElementById(`accessories-gender-group-${productIndex}`);
            const perfumeSizeGroup = document.getElementById(`perfume-size-group-${productIndex}`);
            const shoeTypeGroup = document.getElementById(`shoe-type-group-${productIndex}`);
            const homeLivingGroup = document.getElementById(`home-living-fields-${productIndex}`);
            const subcategoryGroup = document.querySelector(`.form-group:has(#subcategory-${productIndex})`);
            const sizeCategoryGroup = document.querySelector(`.form-group:has(#size_category-${productIndex})`);
            
            const isPerfume = category.toLowerCase() === 'perfumes';
            const isShoes = category.toLowerCase() === 'shoes';
            const isBags = category.toLowerCase() === 'bags';
            const isAccessories = category.toLowerCase() === 'accessories';
            const isHomeLiving = category.toLowerCase() === 'home & living';
            
            // Show/hide individual perfume fields
            if (brandGroup) brandGroup.style.display = isPerfume ? 'block' : 'none';
            if (genderGroup) genderGroup.style.display = (isPerfume || isBags) ? 'block' : 'none';
            if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = isAccessories ? 'block' : 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = isPerfume ? 'block' : 'none';
            
            // Show/hide shoe type field
            if (shoeTypeGroup) shoeTypeGroup.style.display = isShoes ? 'block' : 'none';
            
            // Show/hide Home & Living fields
            if (homeLivingGroup) homeLivingGroup.style.display = isHomeLiving ? 'block' : 'none';
            
            // Hide subcategory and size category for perfumes
            if (subcategoryGroup) subcategoryGroup.style.display = isPerfume ? 'none' : 'block';
            if (sizeCategoryGroup) sizeCategoryGroup.style.display = isPerfume ? 'none' : 'block';
            
            // Make perfume fields required when category is Perfumes
            const brandField = document.getElementById(`brand-${productIndex}`);
            const genderField = document.getElementById(`gender-${productIndex}`);
            const accessoriesGenderField = document.getElementById(`accessories_gender-${productIndex}`);
            const sizeField = document.getElementById(`perfume_size-${productIndex}`);
            
            if (brandField) brandField.required = isPerfume;
            if (genderField) genderField.required = (isPerfume || isBags);
            if (accessoriesGenderField) accessoriesGenderField.required = isAccessories;
            if (sizeField) sizeField.required = isPerfume;
        }



        // Function to handle subcategory changes for Home & Living in multi-product form
        
        // Function to handle subcategory changes for Home & Living in multi-product form
        function handleMultiHomeLivingSubcategory(productIndex) {
            const subcategorySelect = document.getElementById(`subcategory-${productIndex}`);
            const categorySelect = document.getElementById(`category-${productIndex}`);
            const subcategory = subcategorySelect.value;
            const category = categorySelect.value;
            
            if (category.toLowerCase() !== 'home & living') return;
            
            // Get all subcategory-specific field groups
            const beddingFields = document.getElementById(`bedding-fields-${productIndex}`);
            const diningFields = document.getElementById(`dining-fields-${productIndex}`);
            const livingFields = document.getElementById(`living-fields-${productIndex}`);
            
            // Hide all subcategory fields first
            if (beddingFields) beddingFields.style.display = 'none';
            if (diningFields) diningFields.style.display = 'none';
            if (livingFields) livingFields.style.display = 'none';
            
            // Show relevant fields based on subcategory
            if (subcategory.toLowerCase() === 'bedding' && beddingFields) {
                beddingFields.style.display = 'block';
            } else if ((subcategory.toLowerCase() === 'dining room' || subcategory.toLowerCase() === 'dinning room') && diningFields) {
                diningFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'living room' && livingFields) {
                livingFields.style.display = 'block';
            }
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
        let productForms = [];
        let multiVariantSelectedSizes = {};

        // Initialize multi-product form functionality when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any existing forms
            initializeMultiProductForms();
            
            // Add event listener for generate forms button
            const generateBtn = document.getElementById('generate-forms-btn');
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    if (typeof generateProductForms === 'function') {
                        generateProductForms();
                    } else {
                        console.error('generateProductForms function not found');
                        alert('Error: Form generation function not loaded. Please refresh the page.');
                    }
                });
            }
        });

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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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

        // Initialize multi-product forms when they are generated
        function initializeMultiProductForms() {
            // Set up event listeners for all multi-product forms
            const forms = document.querySelectorAll('.product-form-container');
            forms.forEach((form, index) => {
                // Initialize size dropdowns
                const sizeCategorySelect = form.querySelector('select[name*="[size_category]"]');
                if (sizeCategorySelect) {
                    sizeCategorySelect.addEventListener('change', function() {
                        loadMultiSizeOptions(index);
                    });
                }
                
                // Initialize sale price toggles
                const saleCheckbox = form.querySelector('input[name*="[sale]"]');
                if (saleCheckbox) {
                    saleCheckbox.addEventListener('change', function() {
                        toggleMultiSalePrice(index);
                    });
                }
                
                // Initialize category change handlers
                const categorySelect = form.querySelector('select[name*="[category]"]');
                if (categorySelect) {
                    categorySelect.addEventListener('change', function() {
                        loadMultiSubcategories(index);
                        toggleMultiPerfumeFields(index, this.value);
                    });
                }
            });
        }

        // Load subcategories for multi-product forms
        function loadMultiSubcategories(productIndex) {
            
            const categorySelect = document.querySelector(`select[name="products[${productIndex}][category]"]`);
            const subcategorySelect = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`);
            
            if (!categorySelect || !subcategorySelect) {
                console.error('Category or subcategory select not found for product:', productIndex);
                return;
            }
            
            const category = categorySelect.value;
            
            // Reset subcategory
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (!category) {
                return;
            }
            
            // Show/hide specific field groups based on category
            const homeLivingFields = document.getElementById(`home-living-fields-${productIndex}`);
            const shoeTypeGroup = document.getElementById(`shoe-type-group-${productIndex}`);
            const brandGroup = document.getElementById(`brand-group-${productIndex}`);
            const genderGroup = document.getElementById(`gender-group-${productIndex}`);
            const perfumeSizeGroup = document.getElementById(`perfume-size-group-${productIndex}`);
            const accessoriesGenderGroup = document.getElementById(`accessories-gender-group-${productIndex}`);
            
            // Hide all groups first
            if (homeLivingFields) homeLivingFields.style.display = 'none';
            if (shoeTypeGroup) shoeTypeGroup.style.display = 'none';
            if (brandGroup) brandGroup.style.display = 'none';
            if (genderGroup) genderGroup.style.display = 'none';
            if (perfumeSizeGroup) perfumeSizeGroup.style.display = 'none';
            if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = 'none';
            
            // Show relevant groups based on category
            if (category.toLowerCase() === 'home & living') {
                if (homeLivingFields) homeLivingFields.style.display = 'block';
            } else if (category.toLowerCase() === 'shoes') {
                if (shoeTypeGroup) shoeTypeGroup.style.display = 'block';
            } else if (category.toLowerCase() === 'perfumes') {
                if (brandGroup) brandGroup.style.display = 'block';
                if (genderGroup) genderGroup.style.display = 'block';
                if (perfumeSizeGroup) perfumeSizeGroup.style.display = 'block';
                // Perfumes don't have subcategories, so we're done
                return;
            } else if (category.toLowerCase() === 'bags') {
                if (genderGroup) genderGroup.style.display = 'block';
            } else if (category.toLowerCase() === 'accessories') {
                if (accessoriesGenderGroup) accessoriesGenderGroup.style.display = 'block';
            }
            
            // Load subcategories from database for all categories except perfumes
            fetch('get-subcategories.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `category=${encodeURIComponent(category)}`
            })
            .then(response => {
                return response.text().then(text => {
                    if (text.trim() === '') {
                        throw new Error('Empty response received');
                    }
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid JSON: ' + e.message);
                    }
                });
            })
            .then(data => {
                if (data.success && data.subcategories) {
                    // Clear existing options first to prevent duplicates
                    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                    
                    // Use Set to ensure unique subcategories
                    const uniqueSubcategories = [...new Set(data.subcategories)];
                    
                    uniqueSubcategories.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory;
                        option.textContent = subcategory;
                        subcategorySelect.appendChild(option);
                    });
                } else {
                }
            })
            .catch(error => {
                console.error('Error loading subcategories:', error);
            });
            
            // Show/hide perfume-specific fields for multi-product form
            toggleMultiPerfumeFields(productIndex, category);
        }

        // Load sub-subcategories for multi-product forms
        function loadMultiSubSubcategories(productIndex) {
            const categorySelect = document.querySelector(`select[name="products[${productIndex}][category]"]`);
            const subcategorySelect = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`);
            const subSubcategorySelect = document.getElementById(`sub_subcategory-${productIndex}`);
            const subSubcategoryGroup = document.getElementById(`sub-subcategory-group-${productIndex}`);
            
            if (!categorySelect || !subcategorySelect || !subSubcategorySelect || !subSubcategoryGroup) {
                return;
            }
            
            const category = categorySelect.value;
            const subcategory = subcategorySelect.value;
            
            // Hide sub-subcategory group by default
            subSubcategoryGroup.style.display = 'none';
            subSubcategorySelect.innerHTML = '<option value="">Select Sub-Subcategory</option>';
            
            // Show sub-subcategories for Beauty & Cosmetics and Kids' Clothing
            if ((category === 'Beauty & Cosmetics' || category === 'Kids\' Clothing') && subcategory) {
                fetch(`get-sub-subcategories.php?category=${encodeURIComponent(category)}&subcategory=${encodeURIComponent(subcategory)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.sub_subcategories && data.sub_subcategories.length > 0) {
                            subSubcategorySelect.innerHTML = '<option value="">Select Sub-Subcategory</option>';
                            data.sub_subcategories.forEach(subSubcategory => {
                                const option = document.createElement('option');
                                option.value = subSubcategory;
                                option.textContent = subSubcategory;
                                subSubcategorySelect.appendChild(option);
                            });
                            subSubcategoryGroup.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        // Silent error handling
                    });
            }
        }

        // Handle Home & Living subcategory changes for multi-product forms
        function handleMultiHomeLivingSubcategory(productIndex) {
            const subcategorySelect = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`);
            const subcategory = subcategorySelect.value;
            
            // Hide all specific field groups first
            const beddingFields = document.getElementById(`bedding-fields-${productIndex}`);
            const diningFields = document.getElementById(`dining-fields-${productIndex}`);
            const livingFields = document.getElementById(`living-fields-${productIndex}`);
            
            if (beddingFields) beddingFields.style.display = 'none';
            if (diningFields) diningFields.style.display = 'none';
            if (livingFields) livingFields.style.display = 'none';
            
            // Show relevant fields based on subcategory
            if (subcategory.toLowerCase() === 'bedding') {
                if (beddingFields) beddingFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'dining') {
                if (diningFields) diningFields.style.display = 'block';
            } else if (subcategory.toLowerCase() === 'living room') {
                if (livingFields) livingFields.style.display = 'block';
            }
        }



        // Load size options for multi-product forms
        function loadMultiSizeOptions(productIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][size_category]"]`).value;
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections for this product
                const subcategory = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`).value;
                const subSubcategory = document.querySelector(`select[name="products[${productIndex}][sub_subcategory]"]`).value;
                sizeDropdownContent.innerHTML = generateFilteredMultiBeautySizes(productIndex, subcategory, subSubcategory);
                
                // Automatically open the main size dropdown for beauty products
                setTimeout(() => {
                    const dropdownContent = document.getElementById(`size-dropdown-content-${productIndex}`);
                    const dropdownHeader = dropdownContent.previousElementSibling;
                    const dropdownIcon = document.getElementById(`size-dropdown-icon-${productIndex}`);
                    
                    if (!dropdownContent.classList.contains('show')) {
                        dropdownContent.classList.add('show');
                        dropdownHeader.classList.add('active');
                        dropdownIcon.style.transform = 'rotate(180deg)';
                    }
                }, 50);
            }
            
            // Event listeners are handled by inline onclick handlers in the filtered function
        }

        // Load size options for multi-product variant forms
        function loadMultiVariantSizeOptions(productIndex, variantIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][color_variants][${variantIndex}][size_category]"]`).value;
            const sizeSelectionGroup = document.getElementById(`variant-size-selection-group-${productIndex}-${variantIndex}`);
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections for this product
                const subcategory = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`).value;
                const subSubcategory = document.querySelector(`select[name="products[${productIndex}][sub_subcategory]"]`).value;
                sizeDropdownContent.innerHTML = generateFilteredMultiBeautySizes(productIndex, subcategory, subSubcategory);
            }
            
            // Add event listeners to category headers and make size options visible by default
            setTimeout(() => {
                const headers = sizeDropdownContent.querySelectorAll('.size-category-header');
                
                headers.forEach((header, index) => {
                    header.addEventListener('click', function() {
                        this.classList.toggle('expanded');
                        const options = this.nextElementSibling;
                        options.classList.toggle('show');
                    });
                });
            }, 100);
        }

        // Toggle size dropdown for multi-product forms
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

        // Toggle size category expansion for multi-product forms
        window.toggleSizeCategory = function(headerElement, productIndex) {
            const sizeOptions = headerElement.nextElementSibling;
            const icon = headerElement.querySelector('i');
            
            // Toggle the expanded class on the header
            headerElement.classList.toggle('expanded');
            
            // Toggle the show class on the size options
            sizeOptions.classList.toggle('show');
            
            // Rotate the chevron icon
            if (headerElement.classList.contains('expanded')) {
                icon.style.transform = 'rotate(90deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Toggle size dropdown for multi-product variant forms
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

        // Generate clothing sizes for multi-product forms
        function generateMultiClothingSizes(productIndex) {
            return `
                <div class="size-category-header" data-product="${productIndex}" onclick="toggleSizeCategory(this, ${productIndex})">
                    <span>Women's Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="XS" onchange="toggleMultiSize('${productIndex}', 'XS')"> XS</label>
                    <label><input type="checkbox" value="S" onchange="toggleMultiSize('${productIndex}', 'S')"> S</label>
                    <label><input type="checkbox" value="M" onchange="toggleMultiSize('${productIndex}', 'M')"> M</label>
                    <label><input type="checkbox" value="L" onchange="toggleMultiSize('${productIndex}', 'L')"> L</label>
                    <label><input type="checkbox" value="XL" onchange="toggleMultiSize('${productIndex}', 'XL')"> XL</label>
                    <label><input type="checkbox" value="XXL" onchange="toggleMultiSize('${productIndex}', 'XXL')"> XXL</label>
                </div>
                
                <div class="size-category-header" data-product="${productIndex}" onclick="toggleSizeCategory(this, ${productIndex})">
                    <span>Men's Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="S" onchange="toggleMultiSize('${productIndex}', 'S')"> S</label>
                    <label><input type="checkbox" value="M" onchange="toggleMultiSize('${productIndex}', 'M')"> M</label>
                    <label><input type="checkbox" value="L" onchange="toggleMultiSize('${productIndex}', 'L')"> L</label>
                    <label><input type="checkbox" value="XL" onchange="toggleMultiSize('${productIndex}', 'XL')"> XL</label>
                    <label><input type="checkbox" value="XXL" onchange="toggleMultiSize('${productIndex}', 'XXL')"> XXL</label>
                    <label><input type="checkbox" value="XXXL" onchange="toggleMultiSize('${productIndex}', 'XXXL')"> XXXL</label>
                </div>
            `;
        }

        // Generate beauty sizes for multi-product forms
        function generateMultiBeautySizes(productIndex) {
            return `
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>💄 Makeup Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Sample" onchange="toggleMultiSize('${productIndex}', 'Sample')"> Sample (1-2ml)</label>
                        <label><input type="checkbox" value="Travel" onchange="toggleMultiSize('${productIndex}', 'Travel')"> Travel Size (5-10ml)</label>
                        <label><input type="checkbox" value="Regular" onchange="toggleMultiSize('${productIndex}', 'Regular')"> Regular (15-30ml)</label>
                        <label><input type="checkbox" value="Large" onchange="toggleMultiSize('${productIndex}', 'Large')"> Large (50-100ml)</label>
                        <label><input type="checkbox" value="Jumbo" onchange="toggleMultiSize('${productIndex}', 'Jumbo')"> Jumbo (150ml+)</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>🖌️ Makeup Tools</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Foundation_Brush" onchange="toggleMultiSize('${productIndex}', 'Foundation_Brush')"> Foundation Brush</label>
                        <label><input type="checkbox" value="Concealer_Brush" onchange="toggleMultiSize('${productIndex}', 'Concealer_Brush')"> Concealer Brush</label>
                        <label><input type="checkbox" value="Eyeshadow_Brush" onchange="toggleMultiSize('${productIndex}', 'Eyeshadow_Brush')"> Eyeshadow Brush</label>
                        <label><input type="checkbox" value="Blush_Brush" onchange="toggleMultiSize('${productIndex}', 'Blush_Brush')"> Blush Brush</label>
                        <label><input type="checkbox" value="Lip_Brush" onchange="toggleMultiSize('${productIndex}', 'Lip_Brush')"> Lip Brush</label>
                        <label><input type="checkbox" value="Makeup_Remover" onchange="toggleMultiSize('${productIndex}', 'Makeup_Remover')"> Makeup Remover</label>
                        <label><input type="checkbox" value="Brush_Set" onchange="toggleMultiSize('${productIndex}', 'Brush_Set')"> Brush Set</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>🧴 Skincare Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Mini" onchange="toggleMultiSize('${productIndex}', 'Mini')"> Mini (15ml)</label>
                        <label><input type="checkbox" value="Small" onchange="toggleMultiSize('${productIndex}', 'Small')"> Small (30ml)</label>
                        <label><input type="checkbox" value="Medium" onchange="toggleMultiSize('${productIndex}', 'Medium')"> Medium (50ml)</label>
                        <label><input type="checkbox" value="Large_skincare" onchange="toggleMultiSize('${productIndex}', 'Large_skincare')"> Large (100ml)</label>
                        <label><input type="checkbox" value="Family" onchange="toggleMultiSize('${productIndex}', 'Family')"> Family Size (200ml+)</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>🌟 Call Who Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Serum_15ml" onchange="toggleMultiSize('${productIndex}', 'Serum_15ml')"> Serum (15ml)</label>
                        <label><input type="checkbox" value="Toner_100ml" onchange="toggleMultiSize('${productIndex}', 'Toner_100ml')"> Toner (100ml)</label>
                        <label><input type="checkbox" value="Essence_30ml" onchange="toggleMultiSize('${productIndex}', 'Essence_30ml')"> Essence (30ml)</label>
                        <label><input type="checkbox" value="Spot_Treatment_10ml" onchange="toggleMultiSize('${productIndex}', 'Spot_Treatment_10ml')"> Spot Treatment (10ml)</label>
                        <label><input type="checkbox" value="Call_Who_Set" onchange="toggleMultiSize('${productIndex}', 'Call_Who_Set')"> Call Who Set</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>💇 Hair Care Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Trial" onchange="toggleMultiSize('${productIndex}', 'Trial')"> Trial Size (50ml)</label>
                        <label><input type="checkbox" value="Standard" onchange="toggleMultiSize('${productIndex}', 'Standard')"> Standard (250ml)</label>
                        <label><input type="checkbox" value="Professional" onchange="toggleMultiSize('${productIndex}', 'Professional')"> Professional (500ml)</label>
                        <label><input type="checkbox" value="Salon" onchange="toggleMultiSize('${productIndex}', 'Salon')"> Salon Size (1L+)</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>🔧 Hair Tools</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Hair_Dryer" onchange="toggleMultiSize('${productIndex}', 'Hair_Dryer')"> Hair Dryer</label>
                        <label><input type="checkbox" value="Straightener" onchange="toggleMultiSize('${productIndex}', 'Straightener')"> Straightener</label>
                        <label><input type="checkbox" value="Curling_Iron" onchange="toggleMultiSize('${productIndex}', 'Curling_Iron')"> Curling Iron</label>
                        <label><input type="checkbox" value="Hair_Brush" onchange="toggleMultiSize('${productIndex}', 'Hair_Brush')"> Hair Brush</label>
                        <label><input type="checkbox" value="Comb" onchange="toggleMultiSize('${productIndex}', 'Comb')"> Comb</label>
                        <label><input type="checkbox" value="Hair_Clips" onchange="toggleMultiSize('${productIndex}', 'Hair_Clips')"> Hair Clips</label>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>🛁 Bath & Body Sizes</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">
                        <label><input type="checkbox" value="Travel_Kit" onchange="toggleMultiSize('${productIndex}', 'Travel_Kit')"> Travel Kit (30ml)</label>
                        <label><input type="checkbox" value="Personal" onchange="toggleMultiSize('${productIndex}', 'Personal')"> Personal (100ml)</label>
                        <label><input type="checkbox" value="Family_bath" onchange="toggleMultiSize('${productIndex}', 'Family_bath')"> Family (250ml)</label>
                        <label><input type="checkbox" value="Economy" onchange="toggleMultiSize('${productIndex}', 'Economy')"> Economy (500ml+)</label>
                    </div>
                </div>
            `;
        }

        // Generate filtered beauty sizes for multi-product forms based on subcategory and sub-subcategory
        function generateFilteredMultiBeautySizes(productIndex, subcategory = '', subSubcategory = '') {
            let html = '';
            
            // Define size categories and their relevance to subcategories
            const sizeCategories = {
                'makeup_sizes': {
                    name: '💄 Makeup Sizes',
                    sizes: ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                    relevantSubcategories: ['Makeup'],
                    relevantSubSubcategories: ['Face', 'Eye', 'Lip', 'Nail']
                },
                'makeup_tools': {
                    name: '🖌️ Makeup Tools',
                    sizes: ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                    relevantSubcategories: ['Makeup'],
                    relevantSubSubcategories: ['Face', 'Eye', 'Lip', 'Nail']
                },
                'skincare_sizes': {
                    name: '🧴 Skincare Sizes',
                    sizes: ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                    relevantSubcategories: ['Skincare'],
                    relevantSubSubcategories: ['Moisturizers', 'Cleansers', 'Masks', 'Sun Care', 'cream']
                },
                'call_who_sizes': {
                    name: '🌟 Call Who Sizes',
                    sizes: ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                    relevantSubcategories: ['Skincare'],
                    relevantSubSubcategories: ['Moisturizers', 'Cleansers', 'Masks', 'Sun Care', 'cream']
                },
                'hair_sizes': {
                    name: '💇 Hair Care Sizes',
                    sizes: ['Trial', 'Standard', 'Professional', 'Salon'],
                    relevantSubcategories: ['Hair'],
                    relevantSubSubcategories: ['Shampoo', 'Conditioner', 'Tools']
                },
                'hair_tools': {
                    name: '🔧 Hair Tools',
                    sizes: ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                    relevantSubcategories: ['Hair'],
                    relevantSubSubcategories: ['Shampoo', 'Conditioner', 'Tools']
                },
                'bath_body_sizes': {
                    name: '🛁 Bath & Body Sizes',
                    sizes: ['Travel_Kit', 'Personal', 'Family_bath', 'Economy'],
                    relevantSubcategories: ['Bath & Body'],
                    relevantSubSubcategories: ['Shower gel', 'Scrubs', 'soap']
                }
            };
            
            // Filter size categories based on subcategory and sub-subcategory
            const relevantCategories = Object.entries(sizeCategories).filter(([key, category]) => {
                // If no subcategory is selected, show all categories
                if (!subcategory) return true;
                
                // Check if subcategory matches
                const subcategoryMatch = category.relevantSubcategories.includes(subcategory);
                
                // If sub-subcategory is selected, also check that
                if (subSubcategory) {
                    const subSubcategoryMatch = category.relevantSubSubcategories.includes(subSubcategory);
                    return subcategoryMatch && subSubcategoryMatch;
                }
                
                return subcategoryMatch;
            });
            
            // Generate HTML for relevant categories only
            relevantCategories.forEach(([categoryKey, category]) => {
                html += `
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory(this, ${productIndex})">
                        <span>${category.name}</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="size-options">`;
                
                category.sizes.forEach(size => {
                    html += `
                        <label><input type="checkbox" value="${size}" onchange="toggleMultiSize('${productIndex}', '${size}')"> ${size.replace(/_/g, ' ')}</label>`;
                });
                
                html += `
                    </div>
                </div>`;
            });
            
            // If no relevant categories found, show a message
            if (relevantCategories.length === 0) {
                html = `
                <div class="no-sizes-message">
                    <p>No specific sizes available for the selected subcategory/sub-subcategory combination.</p>
                    <p>Please select a subcategory and sub-subcategory to see relevant size options.</p>
                </div>`;
            }
            
            return html;
        }

        // Generate shoe sizes for multi-product forms
        function generateMultiShoeSizes(productIndex) {
            return `
                <div class="size-category-header" data-product="${productIndex}" onclick="toggleSizeCategory(this, ${productIndex})">
                    <span>Women's Shoe Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="5" onchange="toggleMultiSize('${productIndex}', '5')"> 5</label>
                    <label><input type="checkbox" value="5.5" onchange="toggleMultiSize('${productIndex}', '5.5')"> 5.5</label>
                    <label><input type="checkbox" value="6" onchange="toggleMultiSize('${productIndex}', '6')"> 6</label>
                    <label><input type="checkbox" value="6.5" onchange="toggleMultiSize('${productIndex}', '6.5')"> 6.5</label>
                    <label><input type="checkbox" value="7" onchange="toggleMultiSize('${productIndex}', '7')"> 7</label>
                    <label><input type="checkbox" value="7.5" onchange="toggleMultiSize('${productIndex}', '7.5')"> 7.5</label>
                    <label><input type="checkbox" value="8" onchange="toggleMultiSize('${productIndex}', '8')"> 8</label>
                    <label><input type="checkbox" value="8.5" onchange="toggleMultiSize('${productIndex}', '8.5')"> 8.5</label>
                    <label><input type="checkbox" value="9" onchange="toggleMultiSize('${productIndex}', '9')"> 9</label>
                    <label><input type="checkbox" value="9.5" onchange="toggleMultiSize('${productIndex}', '9.5')"> 9.5</label>
                    <label><input type="checkbox" value="10" onchange="toggleMultiSize('${productIndex}', '10')"> 10</label>
                </div>
                
                <div class="size-category-header" data-product="${productIndex}" onclick="toggleSizeCategory(this, ${productIndex})">
                    <span>Men's Shoe Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="7" onchange="toggleMultiSize('${productIndex}', '7')"> 7</label>
                    <label><input type="checkbox" value="7.5" onchange="toggleMultiSize('${productIndex}', '7.5')"> 7.5</label>
                    <label><input type="checkbox" value="8" onchange="toggleMultiSize('${productIndex}', '8')"> 8</label>
                    <label><input type="checkbox" value="8.5" onchange="toggleMultiSize('${productIndex}', '8.5')"> 8.5</label>
                    <label><input type="checkbox" value="9" onchange="toggleMultiSize('${productIndex}', '9')"> 9</label>
                    <label><input type="checkbox" value="9.5" onchange="toggleMultiSize('${productIndex}', '9.5')"> 9.5</label>
                    <label><input type="checkbox" value="10" onchange="toggleMultiSize('${productIndex}', '10')"> 10</label>
                    <label><input type="checkbox" value="10.5" onchange="toggleMultiSize('${productIndex}', '10.5')"> 10.5</label>
                    <label><input type="checkbox" value="11" onchange="toggleMultiSize('${productIndex}', '11')"> 11</label>
                    <label><input type="checkbox" value="11.5" onchange="toggleMultiSize('${productIndex}', '11.5')"> 11.5</label>
                    <label><input type="checkbox" value="12" onchange="toggleMultiSize('${productIndex}', '12')"> 12</label>
                </div>
            `;
        }
                    
        // Toggle size selection for multi-product forms
        window.toggleMultiSize = function(productIndex, size) {
            const key = `${productIndex}`;
            if (!multiSelectedSizes[key]) {
                multiSelectedSizes[key] = new Set();
            }
            
            // Find the checkbox and update its state
            const checkbox = document.querySelector(`#size-dropdown-content-${productIndex} input[value="${size}"]`);
            if (checkbox) {
                if (multiSelectedSizes[key].has(size)) {
                    multiSelectedSizes[key].delete(size);
                    checkbox.checked = false;
                } else {
                    multiSelectedSizes[key].add(size);
                    checkbox.checked = true;
                }
            }
            
            updateMultiSelectedSizesDisplay(productIndex);
        }

        // Update selected sizes display for multi-product forms
        window.updateMultiSelectedSizesDisplay = function(productIndex) {
            const key = `${productIndex}`;
            const selectedSizesText = document.getElementById(`selected-sizes-text-${productIndex}`);
            const selectedSizesInput = document.getElementById(`selected_sizes-${productIndex}`);
            
            if (!multiSelectedSizes[key] || multiSelectedSizes[key].size === 0) {
                selectedSizesText.textContent = 'Select sizes...';
                selectedSizesInput.value = '';
            } else {
                const sizesArray = Array.from(multiSelectedSizes[key]).sort();
                selectedSizesText.textContent = `${sizesArray.length} size(s) selected`;
                selectedSizesInput.value = JSON.stringify(sizesArray);
            }
        }

        // Update selected sizes display for multi-product variant forms
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

        // Generate clothing sizes for multi-product variant forms
        function generateMultiClothingSizes(productIndex, variantIndex) {
            return `
                <div class="size-category-header" data-product="${productIndex}" data-variant="${variantIndex}">
                    <span>Women's Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="XS" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XS')"> XS</label>
                    <label><input type="checkbox" value="S" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'S')"> S</label>
                    <label><input type="checkbox" value="M" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'M')"> M</label>
                    <label><input type="checkbox" value="L" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'L')"> L</label>
                    <label><input type="checkbox" value="XL" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XL')"> XL</label>
                    <label><input type="checkbox" value="XXL" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XXL')"> XXL</label>
                </div>
                
                <div class="size-category-header" data-product="${productIndex}" data-variant="${variantIndex}">
                    <span>Men's Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="S" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'S')"> S</label>
                    <label><input type="checkbox" value="M" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'M')"> M</label>
                    <label><input type="checkbox" value="L" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'L')"> L</label>
                    <label><input type="checkbox" value="XL" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XL')"> XL</label>
                    <label><input type="checkbox" value="XXL" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XXL')"> XXL</label>
                    <label><input type="checkbox" value="XXXL" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', 'XXXL')"> XXXL</label>
                </div>
            `;
        }

        // Generate shoe sizes for multi-product variant forms
        function generateMultiShoeSizes(productIndex, variantIndex) {
            return `
                <div class="size-category-header" data-product="${productIndex}" data-variant="${variantIndex}">
                    <span>Women's Shoe Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '5')"> 5</label>
                    <label><input type="checkbox" value="5.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '5.5')"> 5.5</label>
                    <label><input type="checkbox" value="6" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '6')"> 6</label>
                    <label><input type="checkbox" value="6.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '6.5')"> 6.5</label>
                    <label><input type="checkbox" value="7" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '7')"> 7</label>
                    <label><input type="checkbox" value="7.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '7.5')"> 7.5</label>
                    <label><input type="checkbox" value="8" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '8')"> 8</label>
                    <label><input type="checkbox" value="8.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '8.5')"> 8.5</label>
                    <label><input type="checkbox" value="9" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '9')"> 9</label>
                    <label><input type="checkbox" value="9.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '9.5')"> 9.5</label>
                    <label><input type="checkbox" value="10" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '10')"> 10</label>
                </div>
                
                <div class="size-category-header" data-product="${productIndex}" data-variant="${variantIndex}">
                    <span>Men's Shoe Sizes</span>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="size-options">
                    <label><input type="checkbox" value="7" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '7')"> 7</label>
                    <label><input type="checkbox" value="7.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '7.5')"> 7.5</label>
                    <label><input type="checkbox" value="8" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '8')"> 8</label>
                    <label><input type="checkbox" value="8.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '8.5')"> 8.5</label>
                    <label><input type="checkbox" value="9" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '9')"> 9</label>
                    <label><input type="checkbox" value="9.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '9.5')"> 9.5</label>
                    <label><input type="checkbox" value="10" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '10')"> 10</label>
                    <label><input type="checkbox" value="10.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '10.5')"> 10.5</label>
                    <label><input type="checkbox" value="11" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '11')"> 11</label>
                    <label><input type="checkbox" value="11.5" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '11.5')"> 11.5</label>
                    <label><input type="checkbox" value="12" onchange="toggleMultiVariantSizeSelection('${productIndex}', '${variantIndex}', '12')"> 12</label>
                </div>
            `;
        }

        // Toggle size selection for multi-product variant forms
        function toggleMultiVariantSizeSelection(productIndex, variantIndex, size) {
            const key = `${productIndex}-${variantIndex}`;
            if (!multiVariantSelectedSizes[key]) {
                multiVariantSelectedSizes[key] = new Set();
            }
            
            // Find the checkbox and update its state
            const checkbox = document.querySelector(`#variant-size-dropdown-content-${productIndex}-${variantIndex} input[value="${size}"]`);
            if (checkbox) {
                if (multiVariantSelectedSizes[key].has(size)) {
                    multiVariantSelectedSizes[key].delete(size);
                    checkbox.checked = false;
                } else {
                    multiVariantSelectedSizes[key].add(size);
                    checkbox.checked = true;
                }
            }
            
            updateMultiVariantSelectedSizesDisplay(productIndex, variantIndex);
        }



        // Remove color variant from multi-product forms
        function removeMultiColorVariant(button, productIndex) {
            const variantItem = button.closest('.variant-item');
            variantItem.remove();
        }





        // Handle product count change
        function handleProductCountChange() {
            const count = parseInt(document.getElementById('product-count').value);
            if (count < 1 || count > 20) {
                alert('Please enter a valid number of products (1-20)');
                document.getElementById('product-count').value = 1;
                return;
            }
        }

        // Ensure function is available globally
        window.handleProductCountChange = handleProductCountChange;

        function addMultiColorVariant(productIndex) {
            const container = document.getElementById(`color-variants-container-${productIndex}`);
            const variantIndex = globalColorVariantIndexes[productIndex] || 0;
            const currentCategory = document.querySelector(`select[name="products[${productIndex}][category]"]`).value;
            const isPerfume = currentCategory.toLowerCase() === 'perfumes';
            const isBags = currentCategory.toLowerCase() === 'bags';
            const isAccessories = currentCategory.toLowerCase() === 'accessories';
            
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
                    ${isBags ? `
                    <!-- Bags-specific gender field for variants -->
                    <div class="form-group">
                        <label>Variant Gender</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][gender]">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    ${isAccessories ? `
                    <!-- Accessories-specific gender dropdown for variants -->
                    <div class="form-group">
                        <label>Variant Gender *</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][accessories_gender]">
                            <option value="">Select Gender</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                        </select>
                    </div>
                    ` : ''}
                    
                    <!-- Regular size category for non-perfumes -->
                    <div class="form-group">
                        <label>Size Category</label>
                        <select name="products[${productIndex}][color_variants][${variantIndex}][size_category]" onchange="loadMultiVariantSizeOptions(${productIndex}, ${variantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="beauty">Beauty & Cosmetics</option>
                            <option value="none">No Sizes</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="variant-size-selection-group-${productIndex}-${variantIndex}" style="display: none;">
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



        function loadMultiVariantSizeOptions(productIndex, variantIndex) {
            const sizeCategory = document.querySelector(`select[name="products[${productIndex}][color_variants][${variantIndex}][size_category]"]`).value;
            const sizeSelectionGroup = document.getElementById(`variant-size-selection-group-${productIndex}-${variantIndex}`);
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
            } else if (sizeCategory === 'beauty') {
                // Get current subcategory and sub-subcategory selections for this product
                const subcategory = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`).value;
                const subSubcategory = document.querySelector(`select[name="products[${productIndex}][sub_subcategory]"]`).value;
                sizeDropdownContent.innerHTML = generateFilteredMultiBeautySizes(productIndex, subcategory, subSubcategory);
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
                'men_shoes': ['39', '40', '41', '42', '43', '44', '45', '46', '47'],
                'makeup_sizes': ['Sample', 'Travel', 'Regular', 'Large', 'Jumbo'],
                'makeup_tools': ['Foundation_Brush', 'Concealer_Brush', 'Eyeshadow_Brush', 'Blush_Brush', 'Lip_Brush', 'Makeup_Remover', 'Brush_Set'],
                'skincare_sizes': ['Mini', 'Small', 'Medium', 'Large_skincare', 'Family'],
                'call_who_sizes': ['Serum_15ml', 'Toner_100ml', 'Essence_30ml', 'Spot_Treatment_10ml', 'Call_Who_Set'],
                'hair_sizes': ['Trial', 'Standard', 'Professional', 'Salon'],
                'hair_tools': ['Hair_Dryer', 'Straightener', 'Curling_Iron', 'Hair_Brush', 'Comb', 'Hair_Clips'],
                'bath_body_sizes': ['Travel_Kit', 'Personal', 'Family_bath', 'Economy']
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
        
        // Function to load sub-subcategories for Beauty & Cosmetics
        function loadSubSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const subSubcategorySelect = document.getElementById('sub_subcategory');
            const subSubcategoryGroup = document.getElementById('sub-subcategory-group');
            
            const category = categorySelect.value;
            const subcategory = subcategorySelect.value;
            
            // Hide sub-subcategory field by default
            if (subSubcategoryGroup) {
                subSubcategoryGroup.style.display = 'none';
            }
            
            // Show sub-subcategory for Beauty & Cosmetics and Kids' Clothing
            if ((category !== 'Beauty & Cosmetics' && category !== 'Kids\' Clothing') || !subcategory) {
                return;
            }
            
            // Show sub-subcategory field for supported categories
            if (subSubcategoryGroup) {
                subSubcategoryGroup.style.display = 'block';
            }
            
            // Reset sub-subcategory options
            if (subSubcategorySelect) {
                subSubcategorySelect.innerHTML = '<option value="">Select Sub-Subcategory</option>';
            }
            
            // Load sub-subcategories from database
            fetch(`get-sub-subcategories.php?category=${encodeURIComponent(category)}&subcategory=${encodeURIComponent(subcategory)}`)
                .then(response => response.text().then(text => {
                    if (text.trim() === '') {
                        throw new Error('Empty response received');
                    }
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid JSON: ' + e.message);
                    }
                }))
                .then(data => {
                    if (data.success && data.sub_subcategories) {
                        data.sub_subcategories.forEach(subSubcategory => {
                            const option = document.createElement('option');
                            option.value = subSubcategory;
                            option.textContent = subSubcategory;
                            subSubcategorySelect.appendChild(option);
                        });
                    } else {
                    }
                })
                .catch(error => {
                    console.error('Error loading sub-subcategories:', error);
                });
        }

        // Function to load deeper sub-subcategories for Makeup
        function loadDeeperSubSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const subSubcategorySelect = document.getElementById('sub_subcategory');
            const deeperSubSubcategorySelect = document.getElementById('deeper_sub_subcategory');
            const deeperSubSubcategoryGroup = document.getElementById('deeper-sub-subcategory-group');
            
            const category = categorySelect.value;
            const subcategory = subcategorySelect.value;
            const subSubcategory = subSubcategorySelect.value;
            
            // Hide deeper sub-subcategory field by default
            if (deeperSubSubcategoryGroup) {
                deeperSubSubcategoryGroup.style.display = 'none';
            }
            
            // Show deeper sub-subcategory only for Beauty & Cosmetics > Makeup
            if (category !== 'Beauty & Cosmetics' || subcategory !== 'Makeup' || !subSubcategory) {
                return;
            }
            
            // Show deeper sub-subcategory field for makeup
            if (deeperSubSubcategoryGroup) {
                deeperSubSubcategoryGroup.style.display = 'block';
            }
            
            // Reset deeper sub-subcategory options
            if (deeperSubSubcategorySelect) {
                deeperSubSubcategorySelect.innerHTML = '<option value="">Select Deeper Sub-Subcategory</option>';
            }
            
            // Load deeper sub-subcategories from database
            fetch(`get-deeper-sub-subcategories.php?category=${encodeURIComponent(category)}&subcategory=${encodeURIComponent(subcategory)}&sub_subcategory=${encodeURIComponent(subSubcategory)}`)
                .then(response => response.text().then(text => {
                    if (text.trim() === '') {
                        throw new Error('Empty response received');
                    }
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid JSON: ' + e.message);
                    }
                }))
                .then(data => {
                    if (data.success && data.deeper_sub_subcategories) {
                        data.deeper_sub_subcategories.forEach(deeperSubSubcategory => {
                            const option = document.createElement('option');
                            option.value = deeperSubSubcategory;
                            option.textContent = deeperSubSubcategory;
                            deeperSubSubcategorySelect.appendChild(option);
                        });
                    } else {
                        console.log('No deeper sub-subcategories found');
                    }
                })
                .catch(error => {
                    console.error('Error loading deeper sub-subcategories:', error);
                });
        }

        // Function to load deeper sub-subcategories for multi-product forms
        function loadMultiDeeperSubSubcategories(productIndex) {
            const categorySelect = document.querySelector(`select[name="products[${productIndex}][category]"]`);
            const subcategorySelect = document.querySelector(`select[name="products[${productIndex}][subcategory]"]`);
            const subSubcategorySelect = document.querySelector(`select[name="products[${productIndex}][sub_subcategory]"]`);
            const deeperSubSubcategorySelect = document.getElementById(`deeper_sub_subcategory-${productIndex}`);
            const deeperSubSubcategoryGroup = document.getElementById(`deeper-sub-subcategory-group-${productIndex}`);
            
            if (!categorySelect || !subcategorySelect || !subSubcategorySelect || !deeperSubSubcategorySelect || !deeperSubSubcategoryGroup) {
                return;
            }
            
            const category = categorySelect.value;
            const subcategory = subcategorySelect.value;
            const subSubcategory = subSubcategorySelect.value;
            
            // Hide deeper sub-subcategory field by default
            deeperSubSubcategoryGroup.style.display = 'none';
            
            // Show deeper sub-subcategory only for Beauty & Cosmetics > Makeup
            if (category !== 'Beauty & Cosmetics' || subcategory !== 'Makeup' || !subSubcategory) {
                return;
            }
            
            // Show deeper sub-subcategory field for makeup
            deeperSubSubcategoryGroup.style.display = 'block';
            
            // Reset deeper sub-subcategory options
            deeperSubSubcategorySelect.innerHTML = '<option value="">Select Deeper Sub-Subcategory</option>';
            
            // Load deeper sub-subcategories from database
            fetch(`get-deeper-sub-subcategories.php?category=${encodeURIComponent(category)}&subcategory=${encodeURIComponent(subcategory)}&sub_subcategory=${encodeURIComponent(subSubcategory)}`)
                .then(response => response.text().then(text => {
                    if (text.trim() === '') {
                        throw new Error('Empty response received');
                    }
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid JSON: ' + e.message);
                    }
                }))
                .then(data => {
                    if (data.success && data.deeper_sub_subcategories) {
                        data.deeper_sub_subcategories.forEach(deeperSubSubcategory => {
                            const option = document.createElement('option');
                            option.value = deeperSubSubcategory;
                            option.textContent = deeperSubSubcategory;
                            deeperSubSubcategorySelect.appendChild(option);
                        });
                    } else {
                        console.log('No deeper sub-subcategories found');
                    }
                })
                .catch(error => {
                    console.error('Error loading deeper sub-subcategories:', error);
                });
        }
    </script>
</body>
</html> 
