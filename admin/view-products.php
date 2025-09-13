<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/Product.php';
require_once '../models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

// Helper function to get correct image path
function getImagePath($imagePath) {
    if (empty($imagePath)) return null;
    
    // If path already starts with uploads/products/, use as is
    if (strpos($imagePath, 'uploads/products/') === 0) {
        return '../' . $imagePath;
    }
    
    // If path starts with uploads/, add ../
    if (strpos($imagePath, 'uploads/') === 0) {
        return '../' . $imagePath;
    }
    
    // If path starts with img/, it's from the old static system
    // Try to find these images in the uploads/products directory
    if (strpos($imagePath, 'img/') === 0) {
        // Extract the filename from the img/ path
        $filename = basename($imagePath);
        
        // Check if this file exists in uploads/products/
        $fullPath = '../uploads/products/' . $filename;
        if (file_exists($fullPath)) {
            return $fullPath;
        }
        
        // If not found, return null to show placeholder
        return null;
    }
    
    // If it's just a filename, assume it's in uploads/products/
    if (strpos($imagePath, '/') === false) {
        return '../uploads/products/' . $imagePath;
    }
    
    // If it contains slashes but doesn't start with known prefixes, 
    // try to extract filename and look in uploads/products/
    $filename = basename($imagePath);
    $fullPath = '../uploads/products/' . $filename;
    if (file_exists($fullPath)) {
        return $fullPath;
    }
    
    // If still not found, return null
    return null;
}

// Helper function to check if a file is a video
function isVideoFile($filePath) {
    if (empty($filePath)) return false;
    
    $videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    return in_array($fileExtension, $videoExtensions);
}

// Get filters from URL parameters
$category = $_GET['category'] ?? '';
$subcategory = $_GET['subcategory'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '_id';
$order = $_GET['order'] ?? 'desc';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;

// Build filters
$filters = [];
if (!empty($category)) $filters['category'] = $category;
if (!empty($subcategory)) $filters['subcategory'] = $subcategory;
if (!empty($search)) $filters['name'] = ['$regex' => $search, '$options' => 'i'];


// Build sort options
$sortOptions = [];
if ($sort === 'price') {
    $sortOptions['price'] = $order === 'asc' ? 1 : -1;
} elseif ($sort === 'createdAt') {
    $sortOptions['createdAt'] = $order === 'asc' ? 1 : -1;
} elseif ($sort === '_id') {
    $sortOptions['_id'] = $order === 'asc' ? 1 : -1;
} else {
    $sortOptions['name'] = $order === 'asc' ? 1 : -1;
}

// Get paginated products
$result = $productModel->getPaginated($page, $perPage, $filters, $sortOptions);
$products = $result['products'];
$totalProducts = $result['total'];
$totalPages = $result['pages'];


// Get categories and subcategories for filters
$categories = $categoryModel->getAll();
$subcategories = [];
if ($category) {
    $subcategories = $categoryModel->getSubcategories($category);
}



// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $productId = $_POST['product_id'] ?? '';
        
        switch ($_POST['action']) {
            case 'delete':
                if ($productModel->delete($productId)) {
                    $successMessage = "Product deleted successfully!";
                } else {
                    $errorMessage = "Failed to delete product.";
                }
                break;
                
            case 'toggle_featured':
                $product = $productModel->getById($productId);
                if ($product) {
                    $newFeatured = !($product['featured'] ?? false);
                    if ($productModel->update($productId, ['featured' => $newFeatured])) {
                        $successMessage = "Product featured status updated!";
                    } else {
                        $errorMessage = "Failed to update featured status.";
                    }
                }
                break;
                
            case 'toggle_sale':
                $product = $productModel->getById($productId);
                if ($product) {
                    $newSale = !($product['sale'] ?? false);
                    if ($productModel->update($productId, ['sale' => $newSale])) {
                        $successMessage = "Product sale status updated!";
                    } else {
                        $errorMessage = "Failed to update sale status.";
                    }
                }
                break;
        }
        
        // Redirect to refresh the page
        header("Location: view-products.php?" . http_build_query($_GET));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <style>
        /* Main Content Styles */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2.5rem;
            color: #3E2723;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .page-subtitle {
            color: #8D6E63;
            font-size: 1.1rem;
            margin: 0;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .filter-group select,
        .filter-group input {
            padding: 12px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #FF6B9D;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }
        
        .filter-group select:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .filter-group select:disabled + label {
            color: #6c757d;
        }
        
        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        
        .filter-tag:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .filter-tag a:hover {
            opacity: 0.8;
        }
        
        .active-filters {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9E 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.4);
        }

        .btn-secondary {
            background: #6C757D;
            color: white;
        }

        .btn-secondary:hover {
            background: #5A6268;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #DC3545;
            color: white;
        }

        .btn-danger:hover {
            background: #C82333;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28A745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #FFC107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #E0A800;
            transform: translateY(-2px);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .product-images {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .product-image.active {
            opacity: 1;
        }

        .product-image:not(.active) {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
        }

        .color-variants {
            position: absolute;
            bottom: 15px;
            left: 15px;
            display: flex;
            gap: 8px;
        }

        .color-circle {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .color-circle:hover {
            transform: scale(1.2);
        }

        .color-circle.active {
            border-color: #FF6B9D;
            transform: scale(1.1);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }

        .badge-featured {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9E 100%);
        }

        .badge-sale {
            background: linear-gradient(135deg, #28A745 0%, #20C997 100%);
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
            color: #212529;
        }

        .product-info {
            padding: 20px;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #3E2723;
            margin: 0 0 10px 0;
            line-height: 1.3;
        }

        .product-category {
            color: #8D6E63;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #FF6B9D;
        }

        .original-price {
            font-size: 1rem;
            color: #8D6E63;
            text-decoration: line-through;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .quick-view-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3E2723;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Bulk Selection Styles */
        .product-selector {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
        }
        
        .product-selector input[type="checkbox"] {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #FF6B9D;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .product-selector input[type="checkbox"]:checked {
            background: #FF6B9D;
            border-color: #FF6B9D;
        }
        
        .product-selector input[type="checkbox"]:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.3);
        }
        
        .select-all-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
        }
        
        .select-all-header label {
            transition: all 0.3s ease;
        }
        
        .select-all-header label:hover {
            color: #FF6B9D;
        }
        
        .bulk-actions-bar {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 2px solid #FF6B9D;
            animation: slideDown 0.3s ease;
        }
        
        .bulk-actions-bar .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .bulk-actions-bar .btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        .btn-outline-primary {
            background: transparent;
            color: #FF6B9D;
            border: 2px solid #FF6B9D;
        }
        
        .btn-outline-primary:hover {
            background: #FF6B9D;
            color: white;
        }
        
        .btn-outline-secondary {
            background: transparent;
            color: #6C757D;
            border: 2px solid #6C757D;
        }
        
        .btn-outline-secondary:hover {
            background: #6C757D;
            color: white;
        }
        
        .btn-sm {
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .quick-view-btn:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Quick View Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 20px 30px;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3E2723;
            margin: 0;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #FF6B9D;
        }

        .modal-body {
            padding: 30px;
        }

        .product-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .product-images-modal {
            position: relative;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
        }

        .product-image-modal {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .product-image-modal.active {
            opacity: 1;
        }

        .product-image-modal:not(.active) {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
        }

        .color-variants-modal {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .color-circle-modal {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .color-circle-modal:hover {
            transform: scale(1.2);
        }

        .color-circle-modal.active {
            border-color: #FF6B9D;
            transform: scale(1.1);
        }

        .product-info-modal h3 {
            font-size: 1.8rem;
            color: #3E2723;
            margin: 0 0 15px 0;
        }

        .product-info-modal .category {
            color: #8D6E63;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .product-info-modal .price {
            font-size: 2rem;
            font-weight: 700;
            color: #FF6B9D;
            margin-bottom: 20px;
        }

        .product-info-modal .description {
            color: #5D4037;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .product-actions-modal {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            text-decoration: none;
            color: #5D4037;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            border-color: #FF6B9D;
            color: #FF6B9D;
        }

        .pagination .current {
            background: #FF6B9D;
            border-color: #FF6B9D;
            color: white;
        }

        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }

        .message.error {
            background: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }

            .product-details-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .product-images-modal {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .filters-section {
                padding: 20px;
            }

            .product-actions {
                flex-direction: column;
            }

            .btn-sm {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">View Products</h1>
            <p class="page-subtitle">Manage and view all products in your store</p>
        </div>

        <?php if (isset($successMessage)): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        
        <!-- Success message from edit page -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'updated'): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Product updated successfully! 
            <a href="view-products.php" style="color: inherit; text-decoration: underline;">View all products</a>
        </div>
        <?php endif; ?>
        
        <!-- Success message from bulk operations -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'bulk_updated'): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> 
            <?php echo isset($_GET['count']) ? $_GET['count'] . ' products' : 'Products'; ?> updated successfully! 
            <a href="view-products.php" style="color: inherit; text-decoration: underline;">View all products</a>
        </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="view-products.php" id="filterForm">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="category">Category</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                        <?php echo $category === $cat['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="subcategory">Subcategory</label>
                        <select name="subcategory" id="subcategory" <?php echo empty($category) ? 'disabled' : ''; ?>>
                            <option value="">All Subcategories</option>
                            <?php if (!empty($category) && !empty($subcategories)): ?>
                                <?php foreach ($subcategories as $subcat): ?>
                                    <option value="<?php echo htmlspecialchars($subcat); ?>" 
                                            <?php echo $subcategory === $subcat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subcat); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="search">Search Products</label>
                        <div style="position: relative;">
                            <input type="text" name="search" id="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by product name..."
                                   style="padding-right: 40px;">
                            <button type="submit" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #FF6B9D; cursor: pointer; padding: 5px;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort">
                            <option value="_id" <?php echo $sort === '_id' ? 'selected' : ''; ?>>Date Added</option>
                            <option value="createdAt" <?php echo $sort === 'createdAt' ? 'selected' : ''; ?>>Date Created</option>
                            <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="price" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Price</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="order">Order</label>
                        <select name="order" id="order">
                            <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="view-products.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                        <a href="add-product.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Product
                         </a>
                    </div>
                </div>
            </form>
            
            <!-- Active Filters Summary -->
            <?php if (!empty($category) || !empty($subcategory) || !empty($search)): ?>
                <div class="active-filters" style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 8px; border-left: 4px solid #2196f3;">
                    <h4 style="margin: 0 0 10px 0; color: #1976d2;">
                        <i class="fas fa-filter"></i> Active Filters:
                    </h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php if (!empty($category)): ?>
                            <span class="filter-tag" style="background: #2196f3; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem;">
                                Category: <?php echo htmlspecialchars($category); ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => ''])); ?>" style="color: white; text-decoration: none; margin-left: 8px;">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($subcategory)): ?>
                            <span class="filter-tag" style="background: #4caf50; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem;">
                                Subcategory: <?php echo htmlspecialchars($subcategory); ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['subcategory' => ''])); ?>" style="color: white; text-decoration: none; margin-left: 8px;">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($search)): ?>
                            <span class="filter-tag" style="background: #ff9800; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem;">
                                Search: "<?php echo htmlspecialchars($search); ?>"
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['search' => ''])); ?>" style="color: white; text-decoration: none; margin-left: 8px;">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <a href="view-products.php" style="background: #6c757d; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; text-decoration: none;">
                            <i class="fas fa-times"></i> Clear All
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bulk Actions Bar -->
        <?php if (!empty($products)): ?>
            <div class="bulk-actions-bar" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 25px; display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="selected-count" style="font-weight: 600; color: #3E2723;">
                            <span id="selectedCount">0</span> products selected
                        </span>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary" onclick="editSelectedProducts()" id="editSelectedBtn" disabled>
                            <i class="fas fa-edit"></i> Edit Selected
                        </button>
                        <button type="button" class="btn btn-success" onclick="bulkToggleFeatured()" id="toggleFeaturedBtn" disabled>
                            <i class="fas fa-star"></i> Toggle Featured
                        </button>
                        <button type="button" class="btn btn-warning" onclick="bulkToggleSale()" id="toggleSaleBtn" disabled>
                            <i class="fas fa-tag"></i> Toggle Sale
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteSelectedProducts()" id="deleteSelectedBtn" disabled>
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearSelection()">
                            <i class="fas fa-times"></i> Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #8D6E63;">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 20px;"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or add some products to get started.</p>
                </div>
            <?php else: ?>
                <!-- Select All Header -->
                <div class="select-all-header" style="grid-column: 1 / -1; background: white; padding: 15px 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; color: #3E2723;">
                                <input type="checkbox" id="selectAll" style="width: 18px; height: 18px; cursor: pointer;">
                                Select All Products
                            </label>
                            <span style="color: #8D6E63; font-size: 0.9rem;">
                                (<?php echo count($products); ?> products available)
                            </span>
                        </div>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectPage()">
                                <i class="fas fa-check-square"></i> Select Page
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="invertSelection()">
                                <i class="fas fa-exchange-alt"></i> Invert Selection
                            </button>
                        </div>
                    </div>
                </div>
                
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['_id']; ?>">
                        <!-- Product Selection Checkbox -->
                        <div class="product-selector" style="position: absolute; top: 15px; left: 15px; z-index: 10;">
                            <input type="checkbox" class="product-checkbox" value="<?php echo $product['_id']; ?>" style="width: 18px; height: 18px; cursor: pointer; transform: scale(1.2);">
                        </div>
                        <div class="product-images">
                                                                                       <?php
                              $images = [];
                              $colorVariants = $product['color_variants'] ?? [];
                              
                              // Get main product images first (these will be shown by default)
                              if (!empty($product['front_image'])) {
                                  $frontImagePath = getImagePath($product['front_image']);
                                  if ($frontImagePath) {
                                      $images[] = ['src' => $frontImagePath, 'color' => 'main', 'type' => 'front'];
                                  }
                              }
                              if (!empty($product['back_image'])) {
                                  $backImagePath = getImagePath($product['back_image']);
                                  if ($backImagePath) {
                                      $images[] = ['src' => $backImagePath, 'color' => 'main', 'type' => 'back'];
                                  }
                              }
                              
                              // Get color variant images
                              foreach ($colorVariants as $variant) {
                                  if (!empty($variant['front_image'])) {
                                      $variantFrontImagePath = getImagePath($variant['front_image']);
                                      if ($variantFrontImagePath) {
                                          $images[] = ['src' => $variantFrontImagePath, 'color' => $variant['color'] ?? 'main', 'type' => 'front'];
                                      }
                                  }
                                  if (!empty($variant['back_image'])) {
                                      $variantBackImagePath = getImagePath($variant['back_image']);
                                      if ($variantBackImagePath) {
                                          $images[] = ['src' => $variantBackImagePath, 'color' => $variant['color'] ?? 'main', 'type' => 'back'];
                                      }
                                  }
                              }
                              
                              // Display first image as active
                              if (!empty($images)) {
                                  foreach ($images as $index => $image) {
                                      $isActive = $index === 0 ? 'active' : '';
                                      $isVideo = isVideoFile($image['src']);
                                      
                                      if ($isVideo) {
                                          echo '<video controls class="product-image ' . $isActive . '" ' .
                                                    'data-color="' . htmlspecialchars($image['color']) . '" ' .
                                                    'data-type="' . htmlspecialchars($image['type']) . '" ' .
                                                    'style="max-width: 100%; height: auto;" ' .
                                                    'onerror="this.parentElement.innerHTML=\'<img src=\\\'../img/placeholder.jpg\\\' alt=\\\'Video Error\\\' class=\\\'product-image active\\\'>\'">' .
                                                    '<source src="' . htmlspecialchars($image['src']) . '" type="video/' . pathinfo($image['src'], PATHINFO_EXTENSION) . '">' .
                                                    'Your browser does not support the video tag.' .
                                                '</video>';
                                      } else {
                                          echo '<img src="' . htmlspecialchars($image['src']) . '" ' .
                                                    'alt="' . htmlspecialchars($product['name']) . '" ' .
                                                    'class="product-image ' . $isActive . '" ' .
                                                    'data-color="' . htmlspecialchars($image['color']) . '" ' .
                                                    'data-type="' . htmlspecialchars($image['type']) . '" ' .
                                                    'onerror="this.src=\'../img/placeholder.jpg\'">';
                                      }
                                  }
                              } else {
                                  echo '<img src="../img/placeholder.jpg" alt="No image available" class="product-image active">';
                              }
                              ?>
                            
                                                                                                                     <!-- Color Variants -->
                                                                                               <?php 
                                // Show color circles for all products with color variants
                                if (!empty($colorVariants)): 
                                    // Get unique colors from variants
                                    $uniqueColors = [];
                                    foreach ($colorVariants as $variant) {
                                        if (isset($variant['color']) && !in_array($variant['color'], $uniqueColors)) {
                                            $uniqueColors[] = $variant['color'];
                                        }
                                    }
                                    
                                    // Always show color circles if we have color variants, even if just one color
                                    if (!empty($colorVariants)):
                                ?>
                                    <div class="color-variants">
                                                                                 <!-- Main product color circle (shows main product images) -->
                                         <?php if (!empty($product['front_image']) || !empty($product['back_image'])): ?>
                                             <?php 
                                             // Read the ACTUAL main product color directly from the database
                                             $mainCircleColor = '#8D6E63'; // Default fallback
                                             
                                             // Check if the main product has a color field in the database
                                             if (isset($product['color']) && !empty($product['color']) && $product['color'] !== '') {
                                                 // Use the main product's own color from the database
                                                 $mainCircleColor = $product['color'];
                                             }
                                             // If no main color, fall back to first variant color
                                             elseif (!empty($colorVariants)) {
                                                 foreach ($colorVariants as $variant) {
                                                     if (isset($variant['color']) && !empty($variant['color']) && $variant['color'] !== '') {
                                                         $mainCircleColor = $variant['color'];
                                                         break;
                                                     }
                                                 }
                                             }
                                             ?>
                                             <div class="color-circle active" 
                                                  data-color="main" 
                                                  style="background-color: <?php echo htmlspecialchars($mainCircleColor); ?>;"
                                                  title="Main Product (<?php echo htmlspecialchars($mainCircleColor); ?>)"></div>
                                         <?php endif; ?>
                                        
                                        <!-- Color variant circles -->
                                        <?php foreach ($uniqueColors as $index => $color): ?>
                                            <div class="color-circle" 
                                                 data-color="<?php echo htmlspecialchars($color); ?>" 
                                                 style="background-color: <?php echo htmlspecialchars($color); ?>;"
                                                 title="<?php echo htmlspecialchars($color); ?>"></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php endif; ?>
                             
                                                           <!-- Quick View Button -->
                              <button class="quick-view-btn" onclick="openQuickView('<?php echo $product['_id']; ?>')" style="top: 15px; right: 15px;">
                                  <i class="fas fa-eye"></i>
                              </button>
                            
                                                         <!-- Product Badges -->
                             <?php if ($product['featured'] ?? false): ?>
                                 <div class="product-badge badge-featured">Featured</div>
                             <?php endif; ?>
                             <?php if ($product['sale'] ?? false): ?>
                                 <div class="product-badge badge-sale">Sale</div>
                             <?php endif; ?>
                             
                                                           <!-- Image Issue Warning Badge -->
                              <?php 
                              $hasImageIssues = false;
                              // Check if any images use the old static system paths
                              if (!empty($product['front_image']) && strpos($product['front_image'], 'img/') === 0) $hasImageIssues = true;
                              if (!empty($product['back_image']) && strpos($product['back_image'], 'img/') === 0) $hasImageIssues = true;
                              if (!empty($product['color_variants'])) {
                                  foreach ($product['color_variants'] as $variant) {
                                      if ((!empty($variant['front_image']) && strpos($variant['front_image'], 'img/') === 0) ||
                                          (!empty($variant['back_image']) && strpos($variant['back_image'], 'img/') === 0)) {
                                          $hasImageIssues = true;
                                          break;
                                      }
                                  }
                              }
                              ?>
                              <?php if ($hasImageIssues): ?>
                                  <div class="product-badge badge-warning" title="This product uses the old static image system. Consider updating to the new upload system.">
                                      <i class="fas fa-exclamation-triangle"></i> Old System
                                  </div>
                              <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category'] ?? ''); ?>
                                <?php if (!empty($product['subcategory'])): ?>
                                    > <?php echo htmlspecialchars($product['subcategory']); ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-price">
                                <?php if ($product['sale'] ?? false): ?>
                                    <span class="current-price">$<?php echo number_format($product['salePrice'] ?? $product['price'], 2); ?></span>
                                    <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="product-actions">
                                <a href="edit-product.php?id=<?php echo $product['_id']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                                    <input type="hidden" name="action" value="toggle_featured">
                                    <button type="submit" class="btn <?php echo ($product['featured'] ?? false) ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                        <i class="fas fa-star"></i> 
                                        <?php echo ($product['featured'] ?? false) ? 'Unfeature' : 'Feature'; ?>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                                    <input type="hidden" name="action" value="toggle_sale">
                                    <button type="submit" class="btn <?php echo ($product['sale'] ?? false) ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                        <i class="fas fa-tag"></i> 
                                        <?php echo ($product['sale'] ?? false) ? 'Remove Sale' : 'Mark Sale'; ?>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['_id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Results Summary -->
        <div style="text-align: center; margin-top: 30px; color: #8D6E63;">
            <p>Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> products</p>
            
            
                    </div>
                    
    </div>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Product Details</h2>
                <span class="close" onclick="closeQuickView()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Color variant switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle color circle clicks
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('color-circle')) {
                    const productCard = e.target.closest('.product-card');
                    if (!productCard) return;
                    
                    const selectedColor = e.target.getAttribute('data-color');
                    const imageSlider = productCard.querySelector('.product-images');
                    
                    if (imageSlider) {
                        // Remove active class from all color circles in this product
                        const allColorCircles = productCard.querySelectorAll('.color-circle');
                        allColorCircles.forEach(circle => circle.classList.remove('active'));
                        
                        // Add active class to clicked color circle
                        e.target.classList.add('active');
                        
                        // Hide all images
                        const allImages = imageSlider.querySelectorAll('.product-image');
                        allImages.forEach(img => {
                            img.style.display = 'none';
                            img.classList.remove('active');
                        });
                        
                                                 // Show images for selected color
                         const selectedImages = imageSlider.querySelectorAll(`[data-color="${selectedColor}"]`);
                         if (selectedImages.length > 0) {
                             selectedImages.forEach(img => {
                                 img.style.display = 'block';
                                 img.classList.add('active');
                             });
                         } else {
                             // If no images for selected color, show first available color
                             const firstColorCircle = imageSlider.querySelector('.color-circle');
                             if (firstColorCircle) {
                                 const firstColor = firstColorCircle.getAttribute('data-color');
                                 const firstColorImages = imageSlider.querySelectorAll(`[data-color="${firstColor}"]`);
                                 if (firstColorImages.length > 0) {
                                     firstColorImages.forEach(img => {
                                         img.style.display = 'block';
                                         img.classList.add('active');
                                     });
                                 }
                             }
                         }
                    }
                }
            });

            // Dynamic subcategory loading
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            
            if (categorySelect && subcategorySelect) {
                categorySelect.addEventListener('change', function() {
                    const selectedCategory = this.value;
                    
                    // Clear subcategory and enable/disable based on selection
                    subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                    
                    if (selectedCategory) {
                        // Enable subcategory select and show loading
                        subcategorySelect.disabled = false;
                        subcategorySelect.innerHTML = '<option value="">Loading...</option>';
                        
                        // Fetch subcategories for selected category
                        fetch(`get-subcategories.php?category=${encodeURIComponent(selectedCategory)}`)
                            .then(response => response.json())
                            .then(data => {
                                // Clear loading and add subcategories
                                subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                                
                                if (data.success && data.subcategories) {
                                    data.subcategories.forEach(subcat => {
                                        const option = document.createElement('option');
                                        option.value = subcat;
                                        option.textContent = subcat;
                                        subcategorySelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error loading subcategories:', error);
                                // Show user-friendly error message
                                subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                            });
                    } else {
                        // Disable subcategory select when no category is selected
                        subcategorySelect.disabled = true;
                        subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                    }
                });
                
                // Trigger change event on page load if category is pre-selected
                if (categorySelect.value) {
                    categorySelect.dispatchEvent(new Event('change'));
                }
            }
            

            
            // Handle search input with Enter key
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        // Remove page parameter when searching
                        const url = new URL(window.location);
                        url.searchParams.delete('page');
                        window.location.href = url.toString();
                    }
                });
            }
            
            
        
        // Initialize bulk selection functionality
        initializeBulkSelection();
    });
    
    // Bulk Selection Functions
    function initializeBulkSelection() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const bulkActionsBar = document.querySelector('.bulk-actions-bar');
        const selectedCountSpan = document.getElementById('selectedCount');
        const editSelectedBtn = document.getElementById('editSelectedBtn');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        
        if (!selectAllCheckbox) return;
        
        // Select All functionality
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
        });
        
        // Individual checkbox functionality
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActions();
                updateSelectAllState();
            });
        });
        
        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const selectedCount = checkedBoxes.length;
            
            // Update selected count
            if (selectedCountSpan) {
                selectedCountSpan.textContent = selectedCount;
            }
            
            // Show/hide bulk actions bar
            if (bulkActionsBar) {
                bulkActionsBar.style.display = selectedCount > 0 ? 'block' : 'none';
            }
            
            // Enable/disable action buttons
            if (editSelectedBtn) {
                editSelectedBtn.disabled = selectedCount === 0;
            }
            if (deleteSelectedBtn) {
                deleteSelectedBtn.disabled = selectedCount === 0;
            }
            
            // Enable/disable toggle buttons
            const toggleFeaturedBtn = document.getElementById('toggleFeaturedBtn');
            const toggleSaleBtn = document.getElementById('toggleSaleBtn');
            
            if (toggleFeaturedBtn) {
                toggleFeaturedBtn.disabled = selectedCount === 0;
            }
            if (toggleSaleBtn) {
                toggleSaleBtn.disabled = selectedCount === 0;
            }
        }
        
        function updateSelectAllState() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const totalBoxes = productCheckboxes.length;
            
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === totalBoxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }
    
            function clearSelection() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            productCheckboxes.forEach(checkbox => checkbox.checked = false);
            
            // Hide bulk actions bar
            const bulkActionsBar = document.querySelector('.bulk-actions-bar');
            if (bulkActionsBar) bulkActionsBar.style.display = 'none';
        }
        
        function selectPage() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateBulkActions();
            updateSelectAllState();
        }
        
        function invertSelection() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
            updateBulkActions();
            updateSelectAllState();
        }
    
    function getSelectedProductIds() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        return Array.from(checkedBoxes).map(checkbox => checkbox.value);
    }
    
    function editSelectedProducts() {
        const selectedIds = getSelectedProductIds();
        if (selectedIds.length === 0) {
            alert('Please select products to edit.');
            return;
        }
        
        if (selectedIds.length === 1) {
            // Single product - open edit page in new tab
            window.open(`edit-product.php?id=${selectedIds[0]}`, '_blank');
        } else {
            // Multiple products - show bulk edit modal
            showBulkEditModal(selectedIds);
        }
    }
    
    function deleteSelectedProducts() {
        const selectedIds = getSelectedProductIds();
        if (selectedIds.length === 0) {
            alert('Please select products to delete.');
            return;
        }
        
        const confirmMessage = selectedIds.length === 1 
            ? 'Are you sure you want to delete this product? This action cannot be undone.'
            : `Are you sure you want to delete ${selectedIds.length} products? This action cannot be undone.`;
            
        if (confirm(confirmMessage)) {
            // Submit bulk delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'bulk-delete-products.php';
            
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
    
    function showBulkEditModal(selectedIds) {
        // Create and show bulk edit modal
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.display = 'block';
        modal.innerHTML = `
            <div class="modal-content" style="max-width: 600px;">
                <div class="modal-header">
                    <h2 class="modal-title">Bulk Edit Products</h2>
                    <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                </div>
                <div class="modal-body">
                    <p>You have selected <strong>${selectedIds.length}</strong> products for bulk editing.</p>
                    <p>Choose an action:</p>
                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <button class="btn btn-primary" onclick="bulkEditCategory('${selectedIds.join(',')}')">
                            <i class="fas fa-tags"></i> Edit Category
                        </button>
                        <button class="btn btn-success" onclick="bulkEditPrice('${selectedIds.join(',')}')">
                            <i class="fas fa-dollar-sign"></i> Edit Price
                        </button>
                        <button class="btn btn-warning" onclick="bulkEditStatus('${selectedIds.join(',')}')">
                            <i class="fas fa-toggle-on"></i> Edit Status
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal when clicking outside
        modal.onclick = function(event) {
            if (event.target === modal) {
                modal.remove();
            }
        };
    }
    
    function bulkEditCategory(selectedIds) {
        const category = prompt('Enter new category for all selected products:');
        if (category && category.trim()) {
            window.open(`bulk-edit-products.php?action=category&ids=${selectedIds}&value=${encodeURIComponent(category.trim())}`, '_blank');
        }
    }
    
    function bulkEditPrice(selectedIds) {
        const price = prompt('Enter new price for all selected products:');
        if (price && !isNaN(parseFloat(price))) {
            window.open(`bulk-edit-products.php?action=price&ids=${selectedIds}&value=${encodeURIComponent(price)}`, '_blank');
        } else if (price !== null) {
            alert('Please enter a valid price.');
        }
    }
    
            function bulkEditStatus(selectedIds) {
            const status = prompt('Enter new status (available/unavailable) for all selected products:');
            if (status && ['available', 'unavailable'].includes(status.toLowerCase())) {
                window.open(`bulk-edit-products.php?action=status&ids=${selectedIds}&value=${encodeURIComponent(status.toLowerCase())}`, '_blank');
            } else if (status !== null) {
                alert('Please enter either "available" or "unavailable".');
            }
        }
        
        function bulkToggleFeatured() {
            const selectedIds = getSelectedProductIds();
            if (selectedIds.length === 0) {
                alert('Please select products to toggle featured status.');
                return;
            }
            
            if (confirm(`Toggle featured status for ${selectedIds.length} selected product(s)?`)) {
                window.open(`bulk-edit-products.php?action=toggle_featured&ids=${selectedIds.join(',')}`, '_blank');
            }
        }
        
        function bulkToggleSale() {
            const selectedIds = getSelectedProductIds();
            if (selectedIds.length === 0) {
                alert('Please select products to toggle sale status.');
                return;
            }
            
            if (confirm(`Toggle sale status for ${selectedIds.length} selected product(s)?`)) {
                window.open(`bulk-edit-products.php?action=toggle_sale&ids=${selectedIds.join(',')}`, '_blank');
            }
        }

        // Quick View Modal Functions
        function openQuickView(productId) {
            // Fetch product details
            fetch(`get-product-variants.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.product) {
                        const product = data.product;
                        displayQuickView(product);
                        document.getElementById('quickViewModal').style.display = 'block';
                    } else {
                        alert('Failed to load product details');
                    }
                })
                .catch(error => {
                    console.error('Error loading product:', error);
                    alert('Error loading product details');
                });
        }

        function closeQuickView() {
            document.getElementById('quickViewModal').style.display = 'none';
        }

        function displayQuickView(product) {
            const modalContent = document.getElementById('modalContent');
            
            // Helper function to get correct image path
            function getImagePath(imagePath) {
                if (!imagePath) return null;
                
                // If path already starts with uploads/products/, use as is
                if (imagePath.startsWith('uploads/products/')) {
                    return '../' + imagePath;
                }
                
                // If path starts with uploads/, add ../
                if (imagePath.startsWith('uploads/')) {
                    return '../' + imagePath;
                }
                
                // If path starts with img/, it's from the old static system
                // Try to find these images in the uploads/products directory
                if (imagePath.startsWith('img/')) {
                    // Extract the filename from the img/ path
                    const filename = imagePath.split('/').pop();
                    return '../uploads/products/' + filename;
                }
                
                // If it's just a filename, assume it's in uploads/products/
                if (!imagePath.includes('/')) {
                    return '../uploads/products/' + imagePath;
                }
                
                // If it contains slashes but doesn't start with known prefixes, 
                // try to extract filename and look in uploads/products/
                const filename = imagePath.split('/').pop();
                return '../uploads/products/' + filename;
            }
            
                         // Build color variants HTML
             let colorVariantsHTML = '';
             if (product.color_variants && product.color_variants.length > 0) {
                 // Get unique colors from variants
                 let uniqueColors = [];
                 product.color_variants.forEach(variant => {
                     if (variant.color && !uniqueColors.includes(variant.color)) {
                         uniqueColors.push(variant.color);
                     }
                 });
                 
                 // Always show color circles if we have color variants, even if just one color
                 colorVariantsHTML = `
                     <div class="color-variants-modal">
                 `;
                 
                                   // Add main product color circle if main images exist
                  if (product.front_image || product.back_image) {
                      // Read the ACTUAL main product color directly from the database
                      let mainCircleColor = '#8D6E63'; // Default fallback
                      
                      // Check if the main product has a color field in the database
                      if (product.color && product.color.trim() && product.color !== '') {
                          // Use the main product's own color from the database
                          mainCircleColor = product.color;
                      }
                      // If no main color, fall back to first variant color
                      else if (product.color_variants && product.color_variants.length > 0) {
                          for (let variant of product.color_variants) {
                              if (variant.color && variant.color.trim() && variant.color !== '') {
                                  mainCircleColor = variant.color;
                                  break;
                              }
                          }
                      }
                      colorVariantsHTML += `
                          <div class="color-circle-modal active" data-color="main" style="background-color: ${mainCircleColor};" title="Main Product (${mainCircleColor})"></div>
                      `;
                  }
                 
                 // Add color variant circles
                 uniqueColors.forEach((color, index) => {
                     colorVariantsHTML += `
                         <div class="color-circle-modal" data-color="${color}" style="background-color: ${color};" title="${color}"></div>
                     `;
                 });
                 colorVariantsHTML += '</div>';
             }

            // Helper function to check if file is video
            function isVideoFile(filePath) {
                if (!filePath) return false;
                const videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
                const extension = filePath.split('.').pop().toLowerCase();
                return videoExtensions.includes(extension);
            }
            
            // Build images HTML
            let imagesHTML = '';
            if (product.front_image) {
                const frontImagePath = getImagePath(product.front_image);
                if (frontImagePath) {
                    if (isVideoFile(frontImagePath)) {
                        const extension = frontImagePath.split('.').pop().toLowerCase();
                        imagesHTML += `<video controls class="product-image-modal active" data-color="default" data-type="front" style="max-width: 100%; height: auto;" onerror="this.parentElement.innerHTML='<img src=\\'../img/placeholder.jpg\\' alt=\\'Video Error\\' class=\\'product-image-modal active\\'>'"><source src="${frontImagePath}" type="video/${extension}">Your browser does not support the video tag.</video>`;
                    } else {
                        imagesHTML += `<img src="${frontImagePath}" alt="${product.name}" class="product-image-modal active" data-color="default" data-type="front" onerror="this.src='../img/placeholder.jpg'">`;
                    }
                }
            }
            if (product.back_image) {
                const backImagePath = getImagePath(product.back_image);
                if (backImagePath) {
                    if (isVideoFile(backImagePath)) {
                        const extension = backImagePath.split('.').pop().toLowerCase();
                        imagesHTML += `<video controls class="product-image-modal" data-color="default" data-type="back" style="max-width: 100%; height: auto;" onerror="this.parentElement.innerHTML='<img src=\\'../img/placeholder.jpg\\' alt=\\'Video Error\\' class=\\'product-image-modal\\'>'"><source src="${backImagePath}" type="video/${extension}">Your browser does not support the video tag.</video>`;
                    } else {
                        imagesHTML += `<img src="${backImagePath}" alt="${product.name}" class="product-image-modal" data-color="default" data-type="back" onerror="this.src='../img/placeholder.jpg'">`;
                    }
                }
            }
            
            // Add color variant images
            if (product.color_variants) {
                product.color_variants.forEach(variant => {
                    if (variant.front_image) {
                        const variantFrontImagePath = getImagePath(variant.front_image);
                        if (variantFrontImagePath) {
                            if (isVideoFile(variantFrontImagePath)) {
                                const extension = variantFrontImagePath.split('.').pop().toLowerCase();
                                imagesHTML += `<video controls class="product-image-modal" data-color="${variant.color}" data-type="front" style="max-width: 100%; height: auto;" onerror="this.parentElement.innerHTML='<img src=\\'../img/placeholder.jpg\\' alt=\\'Video Error\\' class=\\'product-image-modal\\'>'"><source src="${variantFrontImagePath}" type="video/${extension}">Your browser does not support the video tag.</video>`;
                            } else {
                                imagesHTML += `<img src="${variantFrontImagePath}" alt="${product.name} - ${variant.name || variant.color}" class="product-image-modal" data-color="${variant.color}" data-type="front" onerror="this.src='../img/placeholder.jpg'">`;
                            }
                        }
                    }
                    if (variant.back_image) {
                        const variantBackImagePath = getImagePath(variant.back_image);
                        if (variantBackImagePath) {
                            if (isVideoFile(variantBackImagePath)) {
                                const extension = variantBackImagePath.split('.').pop().toLowerCase();
                                imagesHTML += `<video controls class="product-image-modal" data-color="${variant.color}" data-type="back" style="max-width: 100%; height: auto;" onerror="this.parentElement.innerHTML='<img src=\\'../img/placeholder.jpg\\' alt=\\'Video Error\\' class=\\'product-image-modal\\'>'"><source src="${variantBackImagePath}" type="video/${extension}">Your browser does not support the video tag.</video>`;
                            } else {
                                imagesHTML += `<img src="${variantBackImagePath}" alt="${product.name} - ${variant.name || variant.color}" class="product-image-modal" data-color="${variant.color}" data-type="back" onerror="this.src='../img/placeholder.jpg'">`;
                            }
                        }
                    }
                });
            }

            modalContent.innerHTML = `
                <div class="product-details-grid">
                    <div class="product-images-modal">
                        ${imagesHTML}
                        ${colorVariantsHTML}
                    </div>
                    <div class="product-info-modal">
                        <h3>${product.name}</h3>
                        <div class="category">${product.category || ''} ${product.subcategory ? '> ' + product.subcategory : ''}</div>
                        <div class="price">
                            ${product.sale ? `<span style="text-decoration: line-through; color: #8D6E63; font-size: 1.5rem; margin-right: 10px;">$${parseFloat(product.price).toFixed(2)}</span>` : ''}
                            $${parseFloat(product.salePrice || product.price).toFixed(2)}
                        </div>
                        <div class="description">${product.description || 'No description available'}</div>
                        <div class="product-actions-modal">
                            <a href="edit-product.php?id=${product._id}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-edit"></i> Edit Product
                            </a>
                            <button class="btn btn-secondary" onclick="closeQuickView()">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Add color variant switching for modal
            setTimeout(() => {
                const modalColorCircles = document.querySelectorAll('.color-circle-modal');
                modalColorCircles.forEach(circle => {
                    circle.addEventListener('click', function() {
                        const selectedColor = this.getAttribute('data-color');
                        const imageContainer = document.querySelector('.product-images-modal');
                        
                        // Remove active class from all color circles
                        modalColorCircles.forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Hide all images
                        const allImages = imageContainer.querySelectorAll('.product-image-modal');
                        allImages.forEach(img => {
                            img.style.display = 'none';
                            img.classList.remove('active');
                        });
                        
                                                 // Show images for selected color
                         const selectedImages = imageContainer.querySelectorAll(`[data-color="${selectedColor}"]`);
                         if (selectedImages.length > 0) {
                             selectedImages.forEach(img => {
                                 img.style.display = 'block';
                                 img.classList.add('active');
                             });
                         } else {
                             // Show first available color images
                             const firstColorCircle = imageContainer.querySelector('.color-circle-modal');
                             if (firstColorCircle) {
                                 const firstColor = firstColorCircle.getAttribute('data-color');
                                 const firstColorImages = imageContainer.querySelectorAll(`[data-color="${firstColor}"]`);
                                 if (firstColorImages.length > 0) {
                                     firstColorImages.forEach(img => {
                                         img.style.display = 'block';
                                         img.classList.add('active');
                                     });
                                 }
                             }
                         }
                    });
                });
            }, 100);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('quickViewModal');
            if (event.target === modal) {
                closeQuickView();
            }
        }
    </script>
</body>
</html>
