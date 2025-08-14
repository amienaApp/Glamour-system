<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../models/Product.php';

    $productModel = new Product();

$message = '';
$messageType = '';

// Check for highlight parameter (newly added/updated product)
$highlightProductId = $_GET['highlight'] ?? '';
$highlightAction = $_GET['action'] ?? '';

if ($highlightProductId && $highlightAction) {
    if ($highlightAction === 'added') {
        $message = 'Product added successfully!';
        $messageType = 'success';
    } elseif ($highlightAction === 'updated') {
        $message = 'Product updated successfully!';
        $messageType = 'success';
    }
}

// Check for import success message
if (isset($_GET['imported'])) {
    $importedCount = (int)$_GET['imported'];
    if ($importedCount > 0) {
        $message = "Successfully imported $importedCount products from index.html!";
        $messageType = 'success';
    }
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $productId = $_POST['product_id'] ?? '';
    if ($productModel->delete($productId)) {
        $message = 'Product deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to delete product.';
        $messageType = 'error';
    }
}

// Get all products without pagination
$products = $productModel->getAll([], ['createdAt' => 1]);
$totalProducts = count($products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Circular Std', 'Segoe UI', sans-serif; background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%); min-height: 100vh; color: #3E2723; display: flex; }
        
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

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.98); backdrop-filter: blur(15px); border-radius: 25px; padding: 40px; margin-bottom: 30px; text-align: center; box-shadow: 0 15px 35px rgba(62,39,35,0.1); border: 1px solid rgba(255,255,255,0.3); }
        .header h1 { color: #3E2723; font-size: 2.8rem; margin-bottom: 15px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: #3E2723; font-size: 1.2rem; opacity: 0.8; }
        .content { background: rgba(255,255,255,0.98); backdrop-filter: blur(15px); border-radius: 25px; padding: 35px; box-shadow: 0 15px 35px rgba(62,39,35,0.08); border: 1px solid rgba(255,255,255,0.3); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-item { background: rgba(255,255,255,0.9); border-radius: 15px; padding: 25px; text-align: center; border-left: 5px solid #29B6F6; box-shadow: 0 8px 25px rgba(62,39,35,0.05); }
        .stat-number { font-size: 2rem; font-weight: 800; color: #3E2723; margin-bottom: 8px; letter-spacing: -0.5px; }
        .stat-label { color: #3E2723; font-size: 1rem; font-weight: 600; opacity: 0.8; }
        
        .products-table {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .table-header {
            background: linear-gradient(135deg, #29B6F6 0%, #0288D1 100%);
            color: white;
            padding: 20px;
            display: grid;
            grid-template-columns: 60px 2fr 1fr 1fr 80px 80px 80px 100px;
            gap: 15px;
            align-items: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .table-header h3 {
            margin: 0;
            font-size: 1.1rem;
        }
        
        .product-row {
            display: grid;
            grid-template-columns: 60px 2fr 1fr 1fr 80px 80px 80px 100px;
            gap: 15px;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid rgba(1, 50, 55, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-row:hover {
            background-color: rgba(41, 182, 246, 0.05);
            transform: translateX(5px);
        }

        .product-row:last-child {
            border-bottom: none;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 6px;
            overflow: hidden;
            background: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image .no-image {
            color: #718096;
            font-size: 0.8rem;
            text-align: center;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .product-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .product-category {
            color: #667eea;
            font-size: 0.8rem;
            margin: 0;
        }
        
        .product-price {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
            margin: 0;
        }

        .product-status {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            width: fit-content;
        }

        .status-badge.featured {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
        }

        .status-badge.sale {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
            color: white;
        }

        .status-badge.active {
            background: linear-gradient(135deg, #3E2723, #5D4037);
            color: white;
        }

        .status-badge.inactive {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 6px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .action-btn.edit {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
        }

        .action-btn.edit:hover {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
            transform: scale(1.05);
        }

        .action-btn.delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .action-btn.delete:hover {
            background: linear-gradient(135deg, #c53030, #e53e3e);
            transform: scale(1.05);
        }

        .action-btn.view {
            background: linear-gradient(135deg, #3E2723, #5D4037);
            color: white;
        }

        .action-btn.view:hover {
            background: linear-gradient(135deg, #5D4037, #3E2723);
            transform: scale(1.05);
        }


        
        .btn { background: linear-gradient(135deg, #29B6F6, #0288D1); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; display: inline-block; box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3); }
        .btn:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 35px rgba(41, 182, 246, 0.4); }
        .btn-danger { background: linear-gradient(135deg, #e53e3e, #c53030); }
        .btn-success { background: linear-gradient(135deg, #3E2723, #5D4037); }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 30px; }
        .page-link { background: #f8f9fa; color: #4a5568; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; }
        .page-link:hover { background: #667eea; color: white; }
        .page-link.active { background: #667eea; color: white; }
        .message { padding: 15px 20px; border-radius: 12px; margin-bottom: 30px; font-weight: 500; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #667eea; text-decoration: none; font-weight: 500; }
        .back-link:hover { color: #764ba2; }
        .empty-state { text-align: center; padding: 60px 20px; color: #718096; }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; color: #cbd5e0; }

        .mobile-menu-btn {
            display: none;
        }

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

            .products-grid { 
                grid-template-columns: 1fr; 
            } 
            
            .stats-bar { 
                grid-template-columns: 1fr 1fr; 
            }

            .products-table {
                font-size: 0.8rem;
            }
            
            .table-header {
                grid-template-columns: 50px 1fr 70px 70px 70px 50px 70px;
                gap: 8px;
                padding: 12px;
            }
            
            .product-row {
                grid-template-columns: 50px 1fr 70px 70px 70px 50px 70px;
                gap: 8px;
                padding: 12px;
            }
        }

        /* Product Highlighting Styles */
        .product-row.highlighted {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05)) !important;
            border: 2px solid #4CAF50 !important;
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.3) !important;
            transform: scale(1.02);
            transition: all 0.5s ease;
        }

        .product-row.highlighted::before {
            content: '✨';
            position: absolute;
            top: -10px;
            left: -10px;
            font-size: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .product-row.highlighted .product-name {
            color: #2E7D32 !important;
            font-weight: 700;
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
            background: linear-gradient(135deg, #e53e3e, #c53030);
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
            color: #e53e3e !important;
            font-weight: 600;
            font-size: 0.9rem !important;
        }
        
        .modal-footer {
            padding: 20px 30px 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .btn-cancel, .btn-delete {
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
        
        .btn-delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #c53030, #e53e3e);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3);
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
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="fas fa-boxes"></i> Manage Products</h1>
            <p>View and manage all your products</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="content">
            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $totalProducts; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $productModel->getCount(['featured' => true]); ?></div>
                <div class="stat-label">Featured Products</div>
            </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $productModel->getCount(['sale' => true]); ?></div>
                <div class="stat-label">Products on Sale</div>
            </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($productModel->getCategories()); ?></div>
                    <div class="stat-label">Categories</div>
            </div>
        </div>
        
                        <!-- Action Buttons -->
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="add-product.php" class="btn" style="padding: 12px 24px; font-size: 1rem;">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
        </div>
        
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h2>No Products Found</h2>
                    <p>You haven't added any products yet. Start by adding your first product!</p>
                    <a href="add-product.php" class="btn" style="margin-top: 20px;">
                        <i class="fas fa-plus"></i> Add Your First Product
                    </a>
                </div>
            <?php else: ?>
                <div class="products-table">
                    <!-- Table Header -->
                    <div class="table-header">
                        <h3><i class="fas fa-image"></i></h3>
                        <h3>Product</h3>
                        <h3>Category</h3>
                        <h3>Price</h3>
                        <h3>Stock</h3>
                        <h3>Status</h3>
                        <h3>Colors</h3>
                        <h3>Actions</h3>
                </div>

                    <!-- Product Rows -->
                    <?php foreach ($products as $product): ?>
                        <div class="product-row" data-product-id="<?php echo $product['_id']; ?>">
                            <!-- Product Image -->
                            <div class="product-image">
                                <?php 
                                // Handle both field name formats
                                $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
                                $backImage = $product['back_image'] ?? $product['image_back'] ?? '';
                                
                                if (!empty($frontImage)): 
                                    $imagePath = "../" . $frontImage;
                                    $fullImagePath = __DIR__ . "/../" . $frontImage;
                                ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i><br><small>Path: <?php echo htmlspecialchars($imagePath); ?></small></div>'"
                                         style="max-width: 100%; height: auto;">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                        <br><small>No image path</small>
                </div>
                                <?php endif; ?>
                </div>
        
                            <!-- Product Info -->
                            <div class="product-info">
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <?php if (!empty($product['description'])): ?>
                                    <div style="color: #718096; font-size: 0.8rem;">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>
                                        <?php if (strlen($product['description']) > 50): ?>...<?php endif; ?>
            </div>
                                <?php endif; ?>
        </div>
        
                            <!-- Category -->
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category']); ?>
                                <?php if (!empty($product['subcategory'])): ?>
                                    <br><small style="color: #718096;"><?php echo htmlspecialchars($product['subcategory']); ?></small>
                                <?php endif; ?>
                </div>

                            <!-- Price -->
                            <div class="product-price">
                                $<?php echo number_format($product['price'], 2); ?>
                                <?php if (isset($product['salePrice'])): ?>
                                    <br><small style="color: #e53e3e; text-decoration: line-through;">$<?php echo number_format($product['salePrice'], 2); ?></small>
                                <?php endif; ?>
            </div>
            
                            <!-- Stock -->
                            <div class="product-stock">
                                <div style="font-weight: 600; color: #2d3748;">
                                    <?php echo $product['stock'] ?? 0; ?> pcs
                        </div>
                                <?php if (($product['available'] ?? true) === false): ?>
                                    <div style="color: #e53e3e; font-size: 0.8rem; font-weight: 600;">SOLD OUT</div>
                                <?php elseif (($product['stock'] ?? 0) <= 5): ?>
                                    <div style="color: #d69e2e; font-size: 0.8rem; font-weight: 600;">LOW STOCK</div>
                                <?php else: ?>
                                    <div style="color: #38a169; font-size: 0.8rem; font-weight: 600;">IN STOCK</div>
                                <?php endif; ?>
                        </div>

                            <!-- Status -->
                            <div class="product-status">
                            <?php if ($product['featured'] ?? false): ?>
                                    <span class="status-badge featured">Featured</span>
                            <?php endif; ?>
                            <?php if ($product['sale'] ?? false): ?>
                                    <span class="status-badge sale">On Sale</span>
                                <?php endif; ?>
                                <?php if (!($product['featured'] ?? false) && !($product['sale'] ?? false)): ?>
                                    <span class="status-badge active">Active</span>
                                <?php endif; ?>
                            </div>

                            <!-- Colors -->
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <?php if (isset($product['color']) && !empty($product['color'])): ?>
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo htmlspecialchars($product['color']); ?>; border: 1px solid #ddd;"></div>
                                <?php endif; ?>
                                <?php if (isset($product['color_variants']) && !empty($product['color_variants'])): ?>
                                    <?php foreach (array_slice($product['color_variants'], 0, 3) as $variant): ?>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo htmlspecialchars($variant['color']); ?>; border: 1px solid #ddd;" title="<?php echo htmlspecialchars($variant['name']); ?>"></div>
                                    <?php endforeach; ?>
                                    <?php if (count($product['color_variants']) > 3): ?>
                                        <small style="color: #718096;">+<?php echo count($product['color_variants']) - 3; ?> more</small>
                                    <?php endif; ?>
                            <?php endif; ?>
                        </div>

                            <!-- Actions -->
                            <div class="product-actions">
                                <a href="edit-product.php?id=<?php echo $product['_id']; ?>" class="action-btn edit" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="view-product.php?id=<?php echo $product['_id']; ?>" class="action-btn view" title="View Product Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="action-btn delete" title="Delete Product" onclick="showDeleteModal('<?php echo $product['_id']; ?>', '<?php echo htmlspecialchars($product['name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>


            <?php endif; ?>
        </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product "<span id="deleteProductName"></span>"?</p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form method="POST" id="deleteForm" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" id="deleteProductId">
                    <button type="submit" class="btn-delete">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
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

        // Product highlighting and scrolling functionality
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const highlightProductId = urlParams.get('highlight');
            const highlightAction = urlParams.get('action');

            if (highlightProductId && highlightAction) {
                // Find the product row to highlight
                const productRow = document.querySelector(`[data-product-id="${highlightProductId}"]`);
                
                if (productRow) {
                    // Add highlighting class
                    productRow.classList.add('highlighted');
                    
                    // Scroll to the highlighted product
                    setTimeout(() => {
                        productRow.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 500);

                    // Remove highlight after 5 seconds
                    setTimeout(() => {
                        productRow.classList.remove('highlighted');
                    }, 5000);

                    // Store highlight info in session storage so it persists on reload
                    sessionStorage.setItem('highlightedProduct', JSON.stringify({
                        id: highlightProductId,
                        action: highlightAction,
                        timestamp: Date.now()
                    }));
                }
            } else {
                // Check if there's a highlighted product in session storage
                const storedHighlight = sessionStorage.getItem('highlightedProduct');
                if (storedHighlight) {
                    const highlightData = JSON.parse(storedHighlight);
                    const productRow = document.querySelector(`[data-product-id="${highlightData.id}"]`);
                    
                    if (productRow) {
                        // Add highlighting class
                        productRow.classList.add('highlighted');
                        
                        // Scroll to the highlighted product
                        setTimeout(() => {
                            productRow.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }, 500);

                        // Remove highlight after 5 seconds
                        setTimeout(() => {
                            productRow.classList.remove('highlighted');
                            // Clear session storage after removing highlight
                            sessionStorage.removeItem('highlightedProduct');
                        }, 5000);
                    } else {
                        // Product not found, clear session storage
                        sessionStorage.removeItem('highlightedProduct');
                    }
                }
            }
        });
        
        // Modal functions
        function showDeleteModal(productId, productName) {
            document.getElementById('deleteProductId').value = productId;
            document.getElementById('deleteProductName').textContent = productName;
            document.getElementById('deleteModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });


    </script>
</body>
</html> 
