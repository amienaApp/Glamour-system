<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// MongoDB connection
require_once '../config1/mongodb.php';
require_once '../models/Product.php';
$productModel = new Product();

// Helper function to convert BSONArray to regular array
function toArray($value) {
    if (is_object($value)) {
        if (method_exists($value, 'toArray')) {
        $result = $value->toArray();
        // Ensure we get a proper array
        if (is_array($result)) {
            return $result;
        } else {
            // If toArray() returns an object, try to convert it
            return (array)$result;
            }
        } elseif (method_exists($value, 'getArrayCopy')) {
            return $value->getArrayCopy();
        } else {
            // Try to convert object to array
            return (array)$value;
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

// Check for bulk addition success message
if ($highlightAction === 'bulk_added') {
    $count = (int)($_GET['count'] ?? 0);
    if ($count > 0) {
        $message = "Successfully added $count product(s)!";
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

// Handle bulk delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    $productIds = $_POST['product_ids'] ?? [];
    $successCount = 0;
    $errorCount = 0;
    
    if (!empty($productIds)) {
        foreach ($productIds as $productId) {
            if ($productModel->delete($productId)) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        if ($successCount > 0) {
            $message = "Successfully deleted $successCount product" . ($successCount !== 1 ? 's' : '') . "!";
            if ($errorCount > 0) {
                $message .= " Failed to delete $errorCount product" . ($errorCount !== 1 ? 's' : '') . ".";
            }
            $messageType = 'success';
        } else {
            $message = 'Failed to delete any products.';
            $messageType = 'error';
        }
    } else {
        $message = 'No products selected for deletion.';
        $messageType = 'error';
    }
}

// Get all products without pagination (newest first)
// Use _id for sorting as it contains timestamp information and is always available
$products = $productModel->getAll([], ['_id' => -1]);
$totalProducts = count($products);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Circular Std', 'Segoe UI', sans-serif; background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%); min-height: 100vh; color: #3E2723; display: flex; }
        
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
            grid-template-columns: 40px 60px 2fr 1fr 1fr 100px 80px 80px 80px 100px;
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
            grid-template-columns: 40px 60px 2fr 1fr 1fr 100px 80px 80px 80px 100px;
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

        /* Bulk Actions Styles */
        .bulk-actions {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .bulk-actions-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .bulk-actions-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-delete, .btn-clear {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        }

        .btn-clear {
            background: rgba(62, 39, 35, 0.1);
            color: #3E2723;
        }

        .btn-clear:hover {
            background: rgba(62, 39, 35, 0.2);
            transform: translateY(-2px);
        }

        /* Checkbox Styles */
        .product-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #29B6F6;
        }

        .header-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #selectAll {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #29B6F6;
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

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
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
                grid-template-columns: 30px 50px 1fr 70px 70px 80px 70px 50px 70px;
                gap: 8px;
                padding: 12px;
            }
            
            .product-row {
                grid-template-columns: 30px 50px 1fr 70px 70px 80px 70px 50px 70px;
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
                        <div class="header-checkbox">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </div>
                        <h3>Product</h3>
                        <h3>Category</h3>
                        <h3>Price</h3>
                        <h3>Stock</h3>
                        <h3>Availability</h3>
                        <h3>Status</h3>
                        <h3>Colors</h3>
                        <h3>Actions</h3>
                </div>

                <!-- Bulk Actions Panel -->
                <div id="bulk-actions" class="bulk-actions" style="display: none;">
                    <div class="bulk-actions-content">
                        <span id="selected-count">0 products selected</span>
                        <div class="bulk-actions-buttons">
                            <button type="button" class="btn-delete" onclick="deleteSelectedProducts()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button type="button" class="btn-clear" onclick="clearSelection()">
                                <i class="fas fa-times"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                    <!-- Product Rows -->
                    <?php foreach ($products as $product): ?>
                        <div class="product-row" data-product-id="<?php echo $product['_id']; ?>">
                            <!-- Checkbox -->
                            <div class="product-checkbox">
                                <input type="checkbox" class="product-select" value="<?php echo $product['_id']; ?>" onchange="updateBulkActions()">
                            </div>
                            <!-- Product Image -->
                            <div class="product-image">
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
                                    $displayImage = $product['front_image'] ?? $product['image_front'] ?? '';
                                }
                                
                                if (!empty($displayImage)): 
                                    $imagePath = "../" . $displayImage;
                                    $fullImagePath = __DIR__ . "/../" . $displayImage;
                                    
                                    // Check if it's a video file
                                    $isVideo = false;
                                    $videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
                                    $fileExtension = strtolower(pathinfo($displayImage, PATHINFO_EXTENSION));
                                    if (in_array($fileExtension, $videoExtensions)) {
                                        $isVideo = true;
                                    }
                                ?>
                                    <?php if ($isVideo): ?>
                                        <video controls style="max-width: 100%; height: auto; max-height: 80px;" 
                                               onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-video\'></i><br><small>Video Error</small></div>'">
                                            <source src="<?php echo htmlspecialchars($imagePath); ?>" type="video/<?php echo $fileExtension; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i><br><small>Path: <?php echo htmlspecialchars($imagePath); ?></small></div>'"
                                             style="max-width: 100%; height: auto;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                        <br><small>No media available</small>
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
                                <?php elseif ($product['category'] === 'Perfumes' && !empty($product['brand'])): ?>
                                    <br><small style="color: #718096;">Brand: <?php echo htmlspecialchars($product['brand']); ?></small>
                                    <?php if (!empty($product['gender'])): ?>
                                        <br><small style="color: #718096;">Gender: <?php echo htmlspecialchars(ucfirst($product['gender'])); ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($product['size'])): ?>
                                        <br><small style="color: #718096;">Size: <?php echo htmlspecialchars($product['size']); ?></small>
                                    <?php endif; ?>
                                <?php elseif ($product['category'] === 'Shoes' && !empty($product['shoe_type'])): ?>
                                    <br><small style="color: #718096;">Type: <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $product['shoe_type']))); ?></small>
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
                        </div>

                            <!-- Availability -->
                            <div class="product-availability">
                                <?php if (($product['available'] ?? true) === false): ?>
                                    <div style="color: #e53e3e; font-size: 0.8rem; font-weight: 600; text-align: center; padding: 4px 8px; background: #fed7d7; border-radius: 12px;">SOLD OUT</div>
                                <?php elseif (($product['stock'] ?? 0) <= 2): ?>
                                    <div style="color: #d69e2e; font-size: 0.8rem; font-weight: 600; text-align: center; padding: 4px 8px; background: #fef5e7; border-radius: 12px;">⚠️ LOW STOCK</div>
                                <?php elseif (($product['stock'] ?? 0) <= 5): ?>
                                    <div style="color: #d69e2e; font-size: 0.8rem; font-weight: 600; text-align: center; padding: 4px 8px; background: #fef5e7; border-radius: 12px;">LOW STOCK</div>
                                <?php else: ?>
                                    <div style="color: #38a169; font-size: 0.8rem; font-weight: 600; text-align: center; padding: 4px 8px; background: #c6f6d5; border-radius: 12px;">IN STOCK</div>
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
                                    <?php 
                                    // Handle color variants - properly convert MongoDB objects to arrays
                                    $colorVariants = [];
                                    if (is_array($product['color_variants'])) {
                                        $colorVariants = $product['color_variants'];
                                    } elseif (is_string($product['color_variants'])) {
                                        $colorVariants = json_decode($product['color_variants'], true) ?: [];
                                    } elseif (is_object($product['color_variants'])) {
                                        // Handle MongoDB objects (BSONArray, etc.)
                                        if (method_exists($product['color_variants'], 'toArray')) {
                                            $colorVariants = $product['color_variants']->toArray();
                                        } elseif (method_exists($product['color_variants'], 'getArrayCopy')) {
                                            $colorVariants = $product['color_variants']->getArrayCopy();
                                        } else {
                                            // Try to convert object to array
                                            $colorVariants = (array)$product['color_variants'];
                                        }
                                    }
                                    
                                    // Ensure we have a proper array
                                    if (!is_array($colorVariants)) {
                                        $colorVariants = [];
                                    }
                                    
                                    if (!empty($colorVariants)) {
                                        $colorVariants = array_slice($colorVariants, 0, 3);
                                    }
                                    ?>
                                    <?php if (is_array($colorVariants) && !empty($colorVariants)): ?>
                                        <?php foreach ($colorVariants as $variant): ?>
                                            <?php if (isset($variant['color']) && !empty($variant['color'])): ?>
                                                <div style="width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo htmlspecialchars($variant['color']); ?>; border: 1px solid #ddd;" title="<?php echo htmlspecialchars($variant['name'] ?? $variant['color']); ?>"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php 
                                        // Calculate total variants properly
                                        $totalVariants = 0;
                                        if (isset($product['color_variants']) && !empty($product['color_variants'])) {
                                            if (is_array($product['color_variants'])) {
                                                $totalVariants = count($product['color_variants']);
                                            } elseif (is_object($product['color_variants'])) {
                                                if (method_exists($product['color_variants'], 'count')) {
                                                    $totalVariants = $product['color_variants']->count();
                                                } elseif (method_exists($product['color_variants'], 'toArray')) {
                                                    $totalVariants = count($product['color_variants']->toArray());
                                                }
                                            }
                                        }
                                        if ($totalVariants > 3): 
                                        ?>
                                            <small style="color: #718096;">+<?php echo $totalVariants - 3; ?> more</small>
                                        <?php endif; ?>
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
    
    <script src="includes/admin-sidebar.js"></script>
    <script>

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

        // Bulk Actions Functions
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const productCheckboxes = document.querySelectorAll('.product-select');
            
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateBulkActions();
        }

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.product-select:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            const selectedCountValue = selectedCheckboxes.length;
            selectedCount.textContent = `${selectedCountValue} product${selectedCountValue !== 1 ? 's' : ''} selected`;
            
            if (selectedCountValue > 0) {
                bulkActions.style.display = 'block';
            } else {
                bulkActions.style.display = 'none';
            }
            
            // Update select all checkbox state
            const totalCheckboxes = document.querySelectorAll('.product-select');
            selectAllCheckbox.checked = selectedCountValue === totalCheckboxes.length && totalCheckboxes.length > 0;
            selectAllCheckbox.indeterminate = selectedCountValue > 0 && selectedCountValue < totalCheckboxes.length;
        }

        function deleteSelectedProducts() {
            const selectedCheckboxes = document.querySelectorAll('.product-select:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            if (selectedIds.length === 0) {
                alert('Please select products to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${selectedIds.length} product${selectedIds.length !== 1 ? 's' : ''}? This action cannot be undone.`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'bulk_delete';
                form.appendChild(actionInput);
                
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'product_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        function clearSelection() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const productCheckboxes = document.querySelectorAll('.product-select');
            
            selectAllCheckbox.checked = false;
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateBulkActions();
        }


    </script>
</body>
</html> 
