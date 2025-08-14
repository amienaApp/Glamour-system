<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../models/Product.php';
require_once '../models/Category.php';

$productModel = new Product();
$categoryModel = new Category();
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

// Get categories for dropdown
$categories = $categoryModel->getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image uploads
    $uploadDir = '../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $productData = [
        'name' => $_POST['name'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'category' => $_POST['category'] ?? '',
        'subcategory' => $_POST['subcategory'] ?? $product['subcategory'] ?? '',
        'color' => $_POST['color'] ?? '',
        'description' => $_POST['description'] ?? '',
        'featured' => isset($_POST['featured']),
        'sale' => isset($_POST['sale']),
        'available' => isset($_POST['available']),
        'stock' => (int)($_POST['stock'] ?? 0),
        'salePrice' => !empty($_POST['salePrice']) ? floatval($_POST['salePrice']) : null,
        'size_category' => $_POST['size_category'] ?? '',
        'selected_sizes' => $_POST['selected_sizes'] ?? ''
    ];

    // Handle main images
    if (isset($_FILES['front_image']) && $_FILES['front_image']['error'] === UPLOAD_ERR_OK) {
        $frontImageName = uniqid() . '_' . basename($_FILES['front_image']['name']);
        $frontImagePath = $uploadDir . $frontImageName;
        if (move_uploaded_file($_FILES['front_image']['tmp_name'], $frontImagePath)) {
            $productData['front_image'] = 'uploads/products/' . $frontImageName;
        }
    } else {
        $productData['front_image'] = $product['front_image'] ?? '';
    }

    if (isset($_FILES['back_image']) && $_FILES['back_image']['error'] === UPLOAD_ERR_OK) {
        $backImageName = uniqid() . '_' . basename($_FILES['back_image']['name']);
        $backImagePath = $uploadDir . $backImageName;
        if (move_uploaded_file($_FILES['back_image']['tmp_name'], $backImagePath)) {
            $productData['back_image'] = 'uploads/products/' . $backImageName;
        }
    } else {
        $productData['back_image'] = $product['back_image'] ?? '';
    }

    // Handle color variants
    $colorVariants = [];
    if (isset($_POST['color_variants']) && is_array($_POST['color_variants'])) {
        foreach ($_POST['color_variants'] as $index => $variant) {
            if (!empty($variant['name']) && !empty($variant['color'])) {
                $variantData = [
                    'name' => $variant['name'],
                    'color' => $variant['color'],
                    'size_category' => $variant['size_category'] ?? '',
                    'selected_sizes' => $variant['selected_sizes'] ?? ''
                ];

                // Handle variant images
                if (isset($_FILES['color_variants']['name'][$index]['front_image']) && 
                    $_FILES['color_variants']['error'][$index]['front_image'] === UPLOAD_ERR_OK) {
                    $variantFrontImageName = uniqid() . '_variant_' . $index . '_front_' . basename($_FILES['color_variants']['name'][$index]['front_image']);
                    $variantFrontImagePath = $uploadDir . $variantFrontImageName;
                    if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['front_image'], $variantFrontImagePath)) {
                        $variantData['front_image'] = 'uploads/products/' . $variantFrontImageName;
                    }
    } else {
                    $variantData['front_image'] = $variant['front_image'] ?? '';
                }

                if (isset($_FILES['color_variants']['name'][$index]['back_image']) && 
                    $_FILES['color_variants']['error'][$index]['back_image'] === UPLOAD_ERR_OK) {
                    $variantBackImageName = uniqid() . '_variant_' . $index . '_back_' . basename($_FILES['color_variants']['name'][$index]['back_image']);
                    $variantBackImagePath = $uploadDir . $variantBackImageName;
                    if (move_uploaded_file($_FILES['color_variants']['tmp_name'][$index]['back_image'], $variantBackImagePath)) {
                        $variantData['back_image'] = 'uploads/products/' . $variantBackImageName;
                    }
    } else {
                    $variantData['back_image'] = $variant['back_image'] ?? '';
                }

                $colorVariants[] = $variantData;
            }
        }
    }

    $productData['color_variants'] = $colorVariants;

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Circular Std', 'Segoe UI', sans-serif; background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%); min-height: 100vh; color: #3E2723; display: flex; }
        
        /* Sidebar Styles */
        .sidebar { width: 280px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px); border-right: 1px solid rgba(255, 255, 255, 0.3); padding: 30px 0; box-shadow: 5px 0 25px rgba(62, 39, 35, 0.1); position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; }
        .sidebar-header { padding: 0 30px 30px; border-bottom: 1px solid rgba(62, 39, 35, 0.1); margin-bottom: 30px; }
        .sidebar-logo { font-size: 1.8rem; font-weight: 700; color: #3E2723; text-decoration: none; display: flex; align-items: center; gap: 12px; }
        .sidebar-logo i { background: linear-gradient(135deg, #29B6F6, #0288D1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2rem; }
        .sidebar-nav { padding: 0 20px; }
        .nav-section { margin-bottom: 30px; }
        .nav-section-title { font-size: 0.8rem; font-weight: 600; color: #3E2723; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding: 0 10px; }
        .nav-item { display: block; padding: 12px 20px; color: #3E2723; text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s ease; font-weight: 500; position: relative; }
        .nav-item:hover { background: rgba(41, 182, 246, 0.1); color: #0288D1; transform: translateX(5px); }
        .nav-item.active { background: linear-gradient(135deg, #29B6F6, #0288D1); color: white; box-shadow: 0 5px 15px rgba(41, 182, 246, 0.3); }
        .nav-item i { width: 20px; margin-right: 12px; text-align: center; }
        .logout-btn { position: absolute; bottom: 30px; left: 20px; right: 20px; background: linear-gradient(135deg, #e53e3e, #c53030); color: white; border: none; padding: 15px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3); }
        
        /* Main Content */
        .main-content { flex: 1; margin-left: 280px; padding: 30px; }
        .container { max-width: 1200px; margin: 0 auto; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px); border-radius: 25px; box-shadow: 0 20px 40px rgba(62, 39, 35, 0.08); overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.3); }
        .header { background: linear-gradient(135deg, #29B6F6 0%, #0288D1 100%); color: white; padding: 40px; text-align: center; }
        .header h1 { font-size: 2.8rem; margin-bottom: 15px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { font-size: 1.2rem; opacity: 0.9; }
        .form-container { padding: 40px; }
        .form-section { margin-bottom: 40px; }
        .section-title { font-size: 1.6rem; font-weight: 700; color: #3E2723; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid rgba(62, 39, 35, 0.1); position: relative; letter-spacing: -0.5px; }
        .section-title::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 80px; height: 3px; background: linear-gradient(90deg, #29B6F6, #0288D1); border-radius: 2px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; font-weight: 600; color: #3E2723; font-size: 0.95rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 15px 20px; border: 2px solid rgba(62, 39, 35, 0.1); border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); color: #3E2723; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #29B6F6; box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1); background: white; }
        
        .color-panel { background: rgba(255, 255, 255, 0.8); border-radius: 15px; padding: 25px; border: 2px solid rgba(62, 39, 35, 0.1); }
        .color-input { width: 100%; height: 34px; border: none; border-radius: 8px; cursor: pointer; }
        .image-upload-section { background: rgba(255, 255, 255, 0.8); border-radius: 15px; padding: 25px; border: 2px solid rgba(62, 39, 35, 0.1); }
        .image-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .image-input-group { text-align: center; }
        .image-preview { margin-top: 15px; min-height: 120px; border: 2px dashed rgba(62, 39, 35, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.5); }
        .image-preview img { max-width: 100%; max-height: 120px; border-radius: 8px; }
        .no-image { color: #718096; font-size: 0.9rem; }
        
        .color-variants-section { background: rgba(255, 255, 255, 0.8); border-radius: 15px; padding: 25px; border: 2px solid rgba(62, 39, 35, 0.1); }
        .variant-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .variant-item { background: rgba(255, 255, 255, 0.9); border-radius: 12px; padding: 20px; border: 2px solid rgba(62, 39, 35, 0.1); position: relative; }
        .variant-item h4 { color: #3E2723; margin-bottom: 15px; font-size: 1.1rem; font-weight: 600; }
        .variant-color-input { width: 100%; height: 50px; border: none; border-radius: 12px; cursor: pointer; margin-bottom: 15px; }
        .variant-image-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .variant-image-preview { margin-top: 10px; min-height: 80px; border: 2px dashed rgba(62, 39, 35, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.5); }
        .variant-image-preview img { max-width: 100%; max-height: 80px; border-radius: 6px; }
        .remove-variant { position: absolute; top: 10px; right: 10px; background: linear-gradient(135deg, #e53e3e, #c53030); color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 0.8rem; transition: all 0.3s ease; }
        .remove-variant:hover { transform: scale(1.1); }
                 .add-variant-btn { background: linear-gradient(135deg, #29B6F6, #0288D1); color: white; border: none; padding: 15px 30px; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 20px; display: inline-flex; align-items: center; gap: 10px; }
         .add-variant-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3); }
         
         .sidebar-actions { margin-top: 30px; padding: 0 20px; }
         .sidebar-action-btn { width: 100%; background: linear-gradient(135deg, #29B6F6, #0288D1); color: white; border: none; padding: 12px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px; }
         .sidebar-action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3); text-decoration: none; color: white; }
         .sidebar-action-btn.secondary { background: linear-gradient(135deg, #3E2723, #5D4037); }
         .sidebar-action-btn.secondary:hover { box-shadow: 0 8px 25px rgba(62, 39, 35, 0.3); }
         .sidebar-action-btn.success { background: linear-gradient(135deg, #4CAF50, #45a049); }
         .sidebar-action-btn.success:hover { box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3); }
        
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .checkbox-group input[type="checkbox"] { width: auto; margin: 0; }
        .submit-btn { background: linear-gradient(135deg, #29B6F6, #0288D1); color: white; border: none; padding: 18px 40px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3); }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 35px rgba(41, 182, 246, 0.4); }
        
        .reset-btn { background: linear-gradient(135deg, #3E2723, #5D4037); color: white; border: none; padding: 18px 40px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 8px 25px rgba(62, 39, 35, 0.3); }
        .reset-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 35px rgba(62, 39, 35, 0.4); }
        
        .message { padding: 20px 25px; border-radius: 15px; margin-bottom: 30px; font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: 12px; }
        .message.success { background: linear-gradient(135deg, #4CAF50, #45a049); color: white; box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3); }
        .message.error { background: linear-gradient(135deg, #f44336, #d32f2f); color: white; box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3); }
        
        .mobile-menu-btn { display: none; }

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

        .variant-size-selection {
            margin-top: 15px;
        }

        @media (max-width: 768px) { 
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; } 
            .sidebar.open { transform: translateX(0); } 
            .main-content { margin-left: 0; } 
            .mobile-menu-btn { display: block; position: fixed; top: 20px; left: 20px; z-index: 1001; background: rgba(255, 255, 255, 0.9); border: none; padding: 12px; border-radius: 10px; cursor: pointer; box-shadow: 0 5px 15px rgba(62, 39, 35, 0.1); } 
            .form-grid { grid-template-columns: 1fr; } 
            .image-inputs { grid-template-columns: 1fr; } 
            .variant-grid { grid-template-columns: 1fr; } 
            .variant-image-inputs { grid-template-columns: 1fr; } 
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            margin: 10% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            padding: 25px 30px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        .modal-body {
            padding: 30px;
            text-align: center;
        }
        
        .modal-body p {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            color: #3E2723;
            line-height: 1.6;
        }
        
        .warning-text {
            color: #ff9800 !important;
            font-weight: 600;
            font-size: 0.9rem !important;
        }
        
        .modal-footer {
            padding: 20px 30px 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .btn-cancel, .btn-reset {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-cancel {
            background: rgba(62, 39, 35, 0.1);
            color: #3E2723;
        }
        
        .btn-cancel:hover {
            background: rgba(62, 39, 35, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
        }
        
        .btn-reset:hover {
            background: linear-gradient(135deg, #f57c00, #ff9800);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 152, 0, 0.3);
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
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $message; ?>
                    </div>
        <?php endif; ?>
        
                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h2>
                        <div class="form-grid">
            <div class="form-group">
                                <label for="name">Product Name *</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="form-group">
                                <label for="price">Price *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" required value="<?php echo $product['price']; ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                                <select id="category" name="category" required onchange="loadSubcategories()">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['name']); ?>" <?php echo ($product['category'] === $category['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subcategory">Subcategory</label>
                <select id="subcategory" name="subcategory">
                    <option value="">Select Subcategory</option>
                </select>
            </div>
            
            <div class="form-group">
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

                    <!-- Image Uploads -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-images"></i> Product Images</h2>
                        <div class="image-upload-section">
                            <h3><i class="fas fa-upload"></i> Update Product Images</h3>
                            <p>Upload new images or keep existing ones</p>
                            <div class="image-inputs">
                                <div class="image-input-group">
                                    <label for="front_image">Front Image</label>
                                    <input type="file" id="front_image" name="front_image" accept="image/*" onchange="previewImage(this, 'front-preview')">
                                    <div id="front-preview" class="image-preview">
                                        <?php if (!empty($product['front_image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($product['front_image']); ?>" alt="Front Image">
                                        <?php else: ?>
                                            <div class="no-image">No image selected</div>
                <?php endif; ?>
            </div>
                                </div>

                                <div class="image-input-group">
                                    <label for="back_image">Back Image</label>
                                    <input type="file" id="back_image" name="back_image" accept="image/*" onchange="previewImage(this, 'back-preview')">
                                    <div id="back-preview" class="image-preview">
                                        <?php if (!empty($product['back_image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($product['back_image']); ?>" alt="Back Image">
                                        <?php else: ?>
                                            <div class="no-image">No image selected</div>
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
                                <?php if (!empty($product['color_variants'])): ?>
                                    <?php foreach ($product['color_variants'] as $index => $variant): ?>
                                        <div class="variant-item">
                                            <button type="button" class="remove-variant" onclick="removeColorVariant(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <h4>Color Variant #<?php echo $index + 1; ?></h4>
            
            <div class="form-group">
                                                <label>Variant Name *</label>
                                                <input type="text" name="color_variants[<?php echo $index; ?>][name]" required value="<?php echo htmlspecialchars($variant['name']); ?>">
                </div>
            
            <div class="form-group">
                                                <label>Color *</label>
                                                <input type="color" name="color_variants[<?php echo $index; ?>][color]" class="variant-color-input" required value="<?php echo htmlspecialchars($variant['color']); ?>">
            </div>
            
            <div class="form-group">
                <label>Variant Size Category</label>
                <select name="color_variants[<?php echo $index; ?>][size_category]" onchange="loadVariantSizeOptions(<?php echo $index; ?>)">
                    <option value="">Select Size Category</option>
                    <option value="clothing" <?php echo ($variant['size_category'] ?? '') === 'clothing' ? 'selected' : ''; ?>>Clothing</option>
                    <option value="shoes" <?php echo ($variant['size_category'] ?? '') === 'shoes' ? 'selected' : ''; ?>>Shoes</option>
                    <option value="none" <?php echo ($variant['size_category'] ?? '') === 'none' ? 'selected' : ''; ?>>No Sizes</option>
                </select>
                </div>
            
            <div class="form-group variant-size-selection" id="variant-size-selection-<?php echo $index; ?>" style="display: none;">
                <label>Variant Available Sizes</label>
                <div class="size-dropdown-container">
                    <div class="size-dropdown-header" onclick="toggleVariantSizeDropdown(<?php echo $index; ?>)">
                        <span id="variant-selected-sizes-text-<?php echo $index; ?>">Select sizes...</span>
                        <i class="fas fa-chevron-down" id="variant-size-dropdown-icon-<?php echo $index; ?>"></i>
                    </div>
                    <div class="size-dropdown-content" id="variant-size-dropdown-content-<?php echo $index; ?>">
                        <!-- Size options will be loaded here -->
                    </div>
                </div>
                <input type="hidden" name="color_variants[<?php echo $index; ?>][selected_sizes]" value="<?php echo htmlspecialchars($variant['selected_sizes'] ?? ''); ?>">
            </div>
            
                                            

                                            <div class="variant-image-inputs">
                                                <div class="image-input-group">
                                                    <label>Front Image</label>
                                                    <input type="file" name="color_variants[<?php echo $index; ?>][front_image]" accept="image/*" onchange="previewVariantImage(this, 'variant-front-<?php echo $index; ?>')">
                                                    <div id="variant-front-<?php echo $index; ?>" class="variant-image-preview">
                                                        <?php if (!empty($variant['front_image'])): ?>
                                                            <img src="../<?php echo htmlspecialchars($variant['front_image']); ?>" alt="Variant Front">
                                                        <?php else: ?>
                                                            <div class="no-image">No image</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="image-input-group">
                                                    <label>Back Image</label>
                                                    <input type="file" name="color_variants[<?php echo $index; ?>][back_image]" accept="image/*" onchange="previewVariantImage(this, 'variant-back-<?php echo $index; ?>')">
                                                    <div id="variant-back-<?php echo $index; ?>" class="variant-image-preview">
                                                        <?php if (!empty($variant['back_image'])): ?>
                                                            <img src="../<?php echo htmlspecialchars($variant['back_image']); ?>" alt="Variant Back">
                                                        <?php else: ?>
                                                            <div class="no-image">No image</div>
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
                </div>
            </div>
            
                    <!-- Additional Information -->
                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-info-circle"></i> Additional Information</h2>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
                        <div class="checkbox-group">
                            <input type="checkbox" id="featured" name="featured" <?php echo ($product['featured'] ?? false) ? 'checked' : ''; ?>>
                            <label for="featured">Featured Product</label>
            </div>
            
                        <div class="checkbox-group">
                            <input type="checkbox" id="sale" name="sale" <?php echo ($product['sale'] ?? false) ? 'checked' : ''; ?>>
                            <label for="sale">On Sale</label>
            </div>
            
                        <div class="form-group" id="salePriceGroup" style="display: <?php echo ($product['sale'] ?? false) ? 'block' : 'none'; ?>;">
                            <label for="salePrice">Sale Price</label>
                            <input type="number" id="salePrice" name="salePrice" step="0.01" min="0" value="<?php echo $product['salePrice'] ?? ''; ?>">
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
                    <button type="button" class="reset-btn" onclick="showResetModal()">
                        <i class="fas fa-undo"></i> Reset to Original
            </button>
                </div>
        </form>
            </div>
        </div>
    </div>
    
    <!-- Reset Confirmation Modal -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Reset</h3>
                <button class="modal-close" onclick="closeResetModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset all changes and return to the original product data?</p>
                <p class="warning-text">This action cannot be undone. All unsaved changes will be lost.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeResetModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn-reset" onclick="confirmReset()">
                    <i class="fas fa-undo"></i> Reset to Original
                </button>
            </div>
        </div>
    </div>

    <script>
        let variantIndex = <?php echo count($product['color_variants'] ?? []); ?>;

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

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<div class="no-image">No image selected</div>';
            }
        }

        function previewVariantImage(input, previewId) {
                    const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<div class="no-image">No image selected</div>';
            }
        }

        function addColorVariant() {
            const container = document.getElementById('color-variants-container');
            const variantHtml = `
                <div class="variant-item">
                    <button type="button" class="remove-variant" onclick="removeColorVariant(this)">
                        <i class="fas fa-times"></i>
                    </button>
                    <h4>Color Variant #${variantIndex + 1}</h4>
                    
                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="color_variants[${variantIndex}][name]" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Color *</label>
                        <input type="color" name="color_variants[${variantIndex}][color]" class="variant-color-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Variant Size Category</label>
                        <select name="color_variants[${variantIndex}][size_category]" onchange="loadVariantSizeOptions(${variantIndex})">
                            <option value="">Select Size Category</option>
                            <option value="clothing">Clothing</option>
                            <option value="shoes">Shoes</option>
                            <option value="none">No Sizes</option>
                        </select>
                    </div>
                    
                    <div class="form-group variant-size-selection" id="variant-size-selection-${variantIndex}" style="display: none;">
                        <label>Variant Available Sizes</label>
                        <div class="size-dropdown-container">
                            <div class="size-dropdown-header" onclick="toggleVariantSizeDropdown(${variantIndex})">
                                <span id="variant-selected-sizes-text-${variantIndex}">Select sizes...</span>
                                <i class="fas fa-chevron-down" id="variant-size-dropdown-icon-${variantIndex}"></i>
                            </div>
                            <div class="size-dropdown-content" id="variant-size-dropdown-content-${variantIndex}">
                                <!-- Size options will be loaded here -->
                            </div>
                        </div>
                        <input type="hidden" name="color_variants[${variantIndex}][selected_sizes]" value="">
                    </div>

                    <div class="variant-image-inputs">
                        <div class="image-input-group">
                            <label>Front Image</label>
                            <input type="file" name="color_variants[${variantIndex}][front_image]" accept="image/*" onchange="previewVariantImage(this, 'variant-front-${variantIndex}')">
                            <div id="variant-front-${variantIndex}" class="variant-image-preview">
                                <div class="no-image">No image selected</div>
                            </div>
                        </div>

                        <div class="image-input-group">
                            <label>Back Image</label>
                            <input type="file" name="color_variants[${variantIndex}][back_image]" accept="image/*" onchange="previewVariantImage(this, 'variant-back-${variantIndex}')">
                            <div id="variant-back-${variantIndex}" class="variant-image-preview">
                                <div class="no-image">No image selected</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', variantHtml);
            variantIndex++;
        }

        function removeColorVariant(button) {
            button.closest('.variant-item').remove();
        }

        // Handle sale checkbox
        document.getElementById('sale').addEventListener('change', function() {
            const salePriceGroup = document.getElementById('salePriceGroup');
            salePriceGroup.style.display = this.checked ? 'block' : 'none';
        });
        
        // Modal functions for reset confirmation
        function showResetModal() {
            document.getElementById('resetModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeResetModal() {
            document.getElementById('resetModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function confirmReset() {
            // Reload the page to reset to original data
            window.location.reload();
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('resetModal');
            if (event.target === modal) {
                closeResetModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeResetModal();
            }
        });

        // Load subcategories on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSubcategories();
            
            // Add event listener for category dropdown
        document.getElementById('category').addEventListener('change', function() {
                loadSubcategories();
            });
            
            // Initialize size dropdowns
            initializeSizeDropdowns();
        });

        // Size Dropdown Functions
        let selectedSizes = new Set();
        let variantSelectedSizes = {};

        function initializeSizeDropdowns() {
            // Initialize main product size dropdown
            const sizeCategory = document.getElementById('size_category').value;
            if (sizeCategory && sizeCategory !== 'none') {
                loadSizeOptions();
                // Load existing selected sizes
                const existingSizes = document.getElementById('selected_sizes').value;
                if (existingSizes) {
                    try {
                        const sizes = JSON.parse(existingSizes);
                        selectedSizes = new Set(sizes);
                        updateSelectedSizesDisplay();
                    } catch (e) {
                        console.error('Error parsing existing sizes:', e);
                    }
                }
            }
            
            // Initialize variant size dropdowns
            const variants = document.querySelectorAll('.variant-item');
            variants.forEach((variant, index) => {
                const sizeCategorySelect = variant.querySelector('select[name*="[size_category]"]');
                if (sizeCategorySelect && sizeCategorySelect.value && sizeCategorySelect.value !== 'none') {
                    loadVariantSizeOptions(index);
                    // Load existing variant sizes
                    const existingVariantSizes = variant.querySelector('input[name*="[selected_sizes]"]').value;
                    if (existingVariantSizes) {
                        try {
                            const sizes = JSON.parse(existingVariantSizes);
                            variantSelectedSizes[index] = new Set(sizes);
                            updateVariantSelectedSizesDisplay(index);
                        } catch (e) {
                            console.error('Error parsing existing variant sizes:', e);
                        }
                    }
                }
            });
        }

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
                sizeDropdownContent.innerHTML = generateClothingSizes();
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateShoeSizes();
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
                sizeDropdownContent.innerHTML = generateClothingSizes();
            } else if (sizeCategory === 'shoes') {
                sizeDropdownContent.innerHTML = generateShoeSizes();
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

        function generateClothingSizes() {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
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

        function generateShoeSizes() {
            return `
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Infant & Baby Shoes (0-24 months)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
                        <div class="size-option" onclick="toggleSize('16')">
                            <input type="checkbox" id="size_16" name="sizes[]" value="16">
                            <label for="size_16">16 (EU 16)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('17')">
                            <input type="checkbox" id="size_17" name="sizes[]" value="17">
                            <label for="size_17">17 (EU 17)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('18')">
                            <input type="checkbox" id="size_18" name="sizes[]" value="18">
                            <label for="size_18">18 (EU 18)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('19')">
                            <input type="checkbox" id="size_19" name="sizes[]" value="19">
                            <label for="size_19">19 (EU 19)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('20')">
                            <input type="checkbox" id="size_20" name="sizes[]" value="20">
                            <label for="size_20">20 (EU 20)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('21')">
                            <input type="checkbox" id="size_21" name="sizes[]" value="21">
                            <label for="size_21">21 (EU 21)</label>
                        </div>
                        <div class="size-option" onclick="toggleSize('22')">
                            <input type="checkbox" id="size_22" name="sizes[]" value="22">
                            <label for="size_22">22 (EU 22)</label>
                        </div>
                    </div>
                </div>
                
                <div class="size-category">
                    <div class="size-category-header">
                        <span>Children Shoes (1-7 years)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="size-options">
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
    </script>
</body>
</html> 
