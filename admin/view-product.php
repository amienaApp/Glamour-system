<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Product.php';
require_once '../models/Category.php';

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

$productModel = new Product();
$categoryModel = new Category();

// Get filter parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$subcategory = $_GET['subcategory'] ?? '';

// Build filter array
$filters = [];
if (!empty($search)) {
    $filters['name'] = ['$regex' => $search, '$options' => 'i'];
}
if (!empty($category)) {
    $filters['category'] = $category;
}
if (!empty($subcategory)) {
    $filters['subcategory'] = $subcategory;
}

// Get all products with filters (newest first)
$products = $productModel->getAll($filters, ['createdAt' => -1]);
$totalProducts = count($products);

// Get all categories and subcategories for filters
$allCategories = $categoryModel->getAll();
$categorySubcategories = [];
foreach ($allCategories as $cat) {
    if (!empty($cat['subcategories'])) {
        $categorySubcategories[$cat['name']] = is_string($cat['subcategories']) ? 
            json_decode($cat['subcategories'], true) : $cat['subcategories'];
    }
}

 // Get highlight product if specified
 $highlightProductId = $_GET['highlight'] ?? '';
 
 // Handle delete action
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
     $productId = $_POST['product_id'] ?? '';
     if (!empty($productId)) {
         $deleted = $productModel->delete($productId);
         if ($deleted) {
             // Redirect to refresh the page and show success message
             header('Location: view-product.php?deleted=1');
             exit;
         } else {
             // Redirect with error message
             header('Location: view-product.php?error=delete_failed');
             exit;
         }
     }
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Products - Glamour Admin</title>
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
        
        .header { 
            background: rgba(255,255,255,0.98); 
            backdrop-filter: blur(15px); 
            border-radius: 25px; 
            padding: 40px; 
            margin-bottom: 30px; 
            text-align: center; 
            box-shadow: 0 15px 35px rgba(62,39,35,0.1); 
            border: 1px solid rgba(255,255,255,0.3); 
        }
        
                 .header h1 { color: #3E2723; font-size: 2.8rem; margin-bottom: 15px; font-weight: 700; letter-spacing: -0.5px; }
         .header p { color: #3E2723; font-size: 1.2rem; opacity: 0.8; margin-bottom: 30px; }
         
         /* Search and Filter Styles */
         .search-filter-section {
             background: rgba(255, 255, 255, 0.95);
             border-radius: 15px;
             padding: 25px;
             margin-top: 20px;
             box-shadow: 0 8px 25px rgba(62, 39, 35, 0.08);
             border: 1px solid rgba(255, 255, 255, 0.3);
         }
         
         .search-filter-form {
             width: 100%;
         }
         
         .filter-row {
             display: flex;
             gap: 20px;
             align-items: center;
             flex-wrap: wrap;
         }
         
         .search-group {
             display: flex;
             align-items: center;
             background: rgba(255, 255, 255, 0.9);
             border-radius: 12px;
             border: 2px solid rgba(41, 182, 246, 0.2);
             overflow: hidden;
             flex: 1;
             min-width: 300px;
         }
         
         .search-input {
             flex: 1;
             padding: 12px 16px;
             border: none;
             background: transparent;
             font-size: 1rem;
             color: #3E2723;
             outline: none;
         }
         
         .search-input::placeholder {
             color: #718096;
         }
         
         .search-btn {
             background: linear-gradient(135deg, #29B6F6, #0288D1);
             color: white;
             border: none;
             padding: 12px 16px;
             cursor: pointer;
             transition: all 0.3s ease;
             font-size: 1rem;
         }
         
         .search-btn:hover {
             background: linear-gradient(135deg, #0288D1, #29B6F6);
             transform: translateY(-1px);
         }
         
         .filter-group {
             display: flex;
             gap: 15px;
             align-items: center;
             flex-wrap: wrap;
         }
         
         .filter-select {
             padding: 12px 16px;
             border: 2px solid rgba(41, 182, 246, 0.2);
             border-radius: 12px;
             background: rgba(255, 255, 255, 0.9);
             color: #3E2723;
             font-size: 1rem;
             cursor: pointer;
             transition: all 0.3s ease;
             min-width: 150px;
         }
         
         .filter-select:focus {
             outline: none;
             border-color: #29B6F6;
             box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1);
         }
         
         .clear-filters-btn {
             background: linear-gradient(135deg, #e53e3e, #c53030);
             color: white;
             text-decoration: none;
             padding: 12px 16px;
             border-radius: 12px;
             font-size: 1rem;
             font-weight: 600;
             transition: all 0.3s ease;
             display: flex;
             align-items: center;
             gap: 8px;
         }
         
         .clear-filters-btn:hover {
             background: linear-gradient(135deg, #c53030, #e53e3e);
             transform: translateY(-1px);
             color: white;
         }
         
         .active-filters {
             margin-top: 20px;
             padding-top: 20px;
             border-top: 1px solid rgba(62, 39, 35, 0.1);
             display: flex;
             align-items: center;
             gap: 15px;
             flex-wrap: wrap;
         }
         
         .filter-label {
             font-weight: 600;
             color: #3E2723;
             font-size: 0.9rem;
         }
         
         .filter-tag {
             background: linear-gradient(135deg, #29B6F6, #0288D1);
             color: white;
             padding: 6px 12px;
             border-radius: 20px;
             font-size: 0.8rem;
             font-weight: 600;
         }
        
        .content { 
            background: rgba(255,255,255,0.98); 
            backdrop-filter: blur(15px); 
            border-radius: 25px; 
            padding: 35px; 
            box-shadow: 0 15px 35px rgba(62,39,35,0.08); 
            border: 1px solid rgba(255,255,255,0.3); 
        }

        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #667eea; 
            text-decoration: none; 
            font-weight: 500; 
            padding: 10px 20px;
            background: rgba(255,255,255,0.8);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover { 
            color: #764ba2; 
            background: rgba(255,255,255,0.95);
            transform: translateX(-5px);
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-item {
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border-left: 5px solid #29B6F6;
            box-shadow: 0 8px 25px rgba(62,39,35,0.05);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #3E2723;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-label {
            color: #3E2723;
            font-size: 1rem;
            font-weight: 600;
            opacity: 0.8;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(62, 39, 35, 0.15);
        }

        .product-card.highlighted {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05));
            border: 2px solid #4CAF50;
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.3);
        }

        .product-card.highlighted::before {
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

        .product-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .product-image-container {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            width: 100%;
            height: 100%;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 8px 20px rgba(62, 39, 35, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .product-image-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 15px;
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            font-size: 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .product-basic-info {
            flex: 1;
        }

        .product-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 8px;
        }

        .product-category {
            color: #667eea;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #29B6F6;
        }

        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 12px;
            border-radius: 10px;
            border-left: 3px solid #29B6F6;
        }

        .detail-label {
            font-weight: 600;
            color: #3E2723;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #2d3748;
            font-size: 1rem;
        }

        .detail-value.stock {
            font-weight: 600;
        }

        .detail-value.stock.sold-out {
            color: #e53e3e;
        }

        .detail-value.stock.low-stock {
            color: #d69e2e;
        }

        .detail-value.stock.in-stock {
            color: #38a169;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .product-colors {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .color-swatch {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 2px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .product-sizes {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 15px;
        }

        .size-tag {
            background: rgba(41, 182, 246, 0.1);
            color: #0288D1;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(41, 182, 246, 0.2);
        }

        .product-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn.edit {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
        }

        .action-btn.edit:hover {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
            transform: translateY(-2px);
        }

        .action-btn.delete {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .action-btn.delete:hover {
            background: linear-gradient(135deg, #c53030, #e53e3e);
            transform: translateY(-2px);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-link {
            background: #f8f9fa;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: #667eea;
            color: white;
        }

        .page-link.active {
            background: #667eea;
            color: white;
        }

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
                gap: 20px;
            }

            .product-header {
                flex-direction: column;
                text-align: center;
            }

            .product-details {
                grid-template-columns: 1fr;
            }

                         .product-actions {
                 justify-content: center;
             }
             
             .filter-row {
                 flex-direction: column;
                 align-items: stretch;
             }
             
             .search-group {
                 min-width: auto;
             }
             
             .filter-group {
                 justify-content: center;
             }
             
             .filter-select {
                 min-width: auto;
                 flex: 1;
             }
             
             .active-filters {
                 justify-content: center;
                 text-align: center;
             }
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
         
         /* Message Styles */
         .message {
             padding: 15px 20px;
             border-radius: 12px;
             margin-bottom: 20px;
             display: flex;
             align-items: center;
             gap: 12px;
             font-weight: 600;
             animation: messageSlideIn 0.3s ease-out;
         }
         
         @keyframes messageSlideIn {
             from {
                 opacity: 0;
                 transform: translateY(-20px);
             }
             to {
                 opacity: 1;
                 transform: translateY(0);
             }
         }
         
         .success-message {
             background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.05));
             color: #2e7d32;
             border: 2px solid #4caf50;
         }
         
         .error-message {
             background: linear-gradient(135deg, rgba(229, 62, 62, 0.1), rgba(229, 62, 62, 0.05));
             color: #c62828;
             border: 2px solid #e53e3e;
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
                         <a href="manage-products.php" class="back-link">
                 <i class="fas fa-arrow-left"></i> Back to Manage Products
             </a>
             
             <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
                 <div class="message success-message">
                     <i class="fas fa-check-circle"></i>
                     Product deleted successfully!
                 </div>
             <?php endif; ?>
             
             <?php if (isset($_GET['error']) && $_GET['error'] == 'delete_failed'): ?>
                 <div class="message error-message">
                     <i class="fas fa-exclamation-circle"></i>
                     Failed to delete product. Please try again.
                 </div>
             <?php endif; ?>

                         <div class="header">
                 <h1><i class="fas fa-eye"></i> View All Products</h1>
                 <p>Comprehensive view of all products with detailed information</p>
                 
                 <!-- Search and Filter Section -->
                 <div class="search-filter-section">
                     <form method="GET" class="search-filter-form">
                         <div class="filter-row">
                             <div class="search-group">
                                 <input type="text" name="search" placeholder="Search products..." 
                                        value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                                 <button type="submit" class="search-btn">
                                     <i class="fas fa-search"></i>
                                 </button>
                             </div>
                             
                             <div class="filter-group">
                                 <select name="category" class="filter-select" onchange="this.form.submit()">
                                     <option value="">All Categories</option>
                                     <?php foreach ($allCategories as $cat): ?>
                                         <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                                                 <?php echo ($category === $cat['name']) ? 'selected' : ''; ?>>
                                             <?php echo htmlspecialchars($cat['name']); ?>
                                         </option>
                                     <?php endforeach; ?>
                                 </select>
                                 
                                 <select name="subcategory" class="filter-select" onchange="this.form.submit()">
                                     <option value="">All Subcategories</option>
                                     <?php if (!empty($category) && isset($categorySubcategories[$category])): ?>
                                         <?php foreach ($categorySubcategories[$category] as $subcat): ?>
                                             <option value="<?php echo htmlspecialchars($subcat); ?>" 
                                                     <?php echo ($subcategory === $subcat) ? 'selected' : ''; ?>>
                                                 <?php echo htmlspecialchars($subcat); ?>
                                             </option>
                                         <?php endforeach; ?>
                                     <?php endif; ?>
                                 </select>
                                 
                                 <a href="view-product.php" class="clear-filters-btn">
                                     <i class="fas fa-times"></i> Clear Filters
                                 </a>
                             </div>
                         </div>
                     </form>
                     
                     <?php if (!empty($search) || !empty($category) || !empty($subcategory)): ?>
                         <div class="active-filters">
                             <span class="filter-label">Active Filters:</span>
                             <?php if (!empty($search)): ?>
                                 <span class="filter-tag">Search: "<?php echo htmlspecialchars($search); ?>"</span>
                             <?php endif; ?>
                             <?php if (!empty($category)): ?>
                                 <span class="filter-tag">Category: <?php echo htmlspecialchars($category); ?></span>
                             <?php endif; ?>
                             <?php if (!empty($subcategory)): ?>
                                 <span class="filter-tag">Subcategory: <?php echo htmlspecialchars($subcategory); ?></span>
                             <?php endif; ?>
                         </div>
                     <?php endif; ?>
                 </div>
             </div>
            
            <div class="content">
                                 <!-- Stats Bar -->
                 <div class="stats-bar">
                     <div class="stat-item">
                         <div class="stat-number"><?php echo $totalProducts; ?></div>
                         <div class="stat-label">
                             <?php if (!empty($search) || !empty($category) || !empty($subcategory)): ?>
                                 Filtered Products
                             <?php else: ?>
                                 Total Products
                             <?php endif; ?>
                         </div>
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



                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                    <div style="text-align: center; padding: 60px 20px; color: #718096;">
                        <i class="fas fa-box-open" style="font-size: 4rem; margin-bottom: 20px; color: #cbd5e0;"></i>
                        <h2>No Products Found</h2>
                        <p>You haven't added any products yet.</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card <?php echo ($highlightProductId === $product['_id']) ? 'highlighted' : ''; ?>" data-product-id="<?php echo $product['_id']; ?>">
                                <div class="product-header">
                                    <div class="product-image-container">
                                        <?php 
                                        $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
                                        if (!empty($frontImage)): 
                                            $imagePath = "../" . $frontImage;
                                        ?>
                                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-image">
                                        <?php else: ?>
                                            <div class="product-image-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-basic-info">
                                        <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
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
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-price">
                                            $<?php echo number_format($product['price'], 2); ?>
                                            <?php if (isset($product['salePrice'])): ?>
                                                <br><small style="color: #e53e3e; text-decoration: line-through;">$<?php echo number_format($product['salePrice'], 2); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="product-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Stock</div>
                                        <div class="detail-value stock <?php 
                                            if (($product['available'] ?? true) === false) echo 'sold-out';
                                            elseif (($product['stock'] ?? 0) <= 5) echo 'low-stock';
                                            else echo 'in-stock';
                                        ?>">
                                            <?php echo $product['stock'] ?? 0; ?> pcs
                                            <?php if (($product['available'] ?? true) === false): ?>
                                                - SOLD OUT
                                            <?php elseif (($product['stock'] ?? 0) <= 5): ?>
                                                - LOW STOCK
                                            <?php else: ?>
                                                - IN STOCK
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            <?php if ($product['featured'] ?? false): ?>
                                                <span class="status-badge featured">Featured</span>
                                            <?php elseif ($product['sale'] ?? false): ?>
                                                <span class="status-badge sale">On Sale</span>
                                            <?php else: ?>
                                                <span class="status-badge active">Active</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Description</div>
                                        <div class="detail-value">
                                            <?php echo htmlspecialchars(substr($product['description'] ?? 'No description', 0, 50)); ?>
                                            <?php if (strlen($product['description'] ?? '') > 50): ?>...<?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Created</div>
                                        <div class="detail-value">
                                            <?php echo isset($product['createdAt']) ? date('M j, Y', strtotime($product['createdAt'])) : 'N/A'; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($product['category'] === 'Perfumes'): ?>
                                    <div class="detail-item">
                                        <div class="detail-label">Perfume Details</div>
                                        <div class="detail-value">
                                            <?php if (!empty($product['brand'])): ?>
                                                <div><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($product['gender'])): ?>
                                                <div><strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($product['gender'])); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($product['size'])): ?>
                                                <div><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php 
                                $selectedSizes = [];
                                if (!empty($product['selected_sizes'])) {
                                    if (is_string($product['selected_sizes'])) {
                                        $selectedSizes = json_decode($product['selected_sizes'], true) ?: [];
                                    } elseif (is_array($product['selected_sizes'])) {
                                        $selectedSizes = $product['selected_sizes'];
                                    }
                                }
                                ?>
                                <?php if (!empty($selectedSizes)): ?>
                                <div class="product-sizes">
                                    <?php foreach (array_slice($selectedSizes, 0, 5) as $size): ?>
                                        <span class="size-tag"><?php echo htmlspecialchars($size); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($selectedSizes) > 5): ?>
                                        <span class="size-tag">+<?php echo count($selectedSizes) - 5; ?> more</span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <?php 
                                $colorVariants = [];
                                if (!empty($product['color_variants'])) {
                                    if (is_string($product['color_variants'])) {
                                        $colorVariants = json_decode($product['color_variants'], true) ?: [];
                                    } else {
                                        $colorVariants = toArray($product['color_variants']);
                                    }
                                }
                                ?>
                                <?php if (!empty($colorVariants) || !empty($product['color'])): ?>
                                <div class="product-colors">
                                    <?php if (!empty($colorVariants)): ?>
                                        <?php foreach (array_slice($colorVariants, 0, 5) as $variant): ?>
                                            <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($variant['color']); ?>" title="<?php echo htmlspecialchars($variant['name']); ?>"></div>
                                        <?php endforeach; ?>
                                        <?php 
                                        $totalVariants = getCount($product['color_variants']);
                                        if ($totalVariants > 5): 
                                        ?>
                                            <small style="color: #718096; align-self: center;">+<?php echo $totalVariants - 5; ?> more</small>
                                        <?php endif; ?>
                                    <?php elseif (!empty($product['color'])): ?>
                                        <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($product['color']); ?>" title="Main Color"></div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                                                 <div class="product-actions">
                                     <a href="edit-product.php?id=<?php echo $product['_id']; ?>" class="action-btn edit">
                                         <i class="fas fa-edit"></i> Edit
                                     </a>
                                     <button type="button" class="action-btn delete" onclick="showDeleteModal('<?php echo $product['_id']; ?>', '<?php echo htmlspecialchars($product['name']); ?>')">
                                         <i class="fas fa-trash"></i> Delete
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

                 // Product highlighting functionality
         document.addEventListener('DOMContentLoaded', function() {
             const urlParams = new URLSearchParams(window.location.search);
             const highlightProductId = urlParams.get('highlight');

             if (highlightProductId) {
                 const productCard = document.querySelector(`[data-product-id="${highlightProductId}"]`);
                 
                 if (productCard) {
                     // Scroll to the highlighted product
                     setTimeout(() => {
                         productCard.scrollIntoView({
                             behavior: 'smooth',
                             block: 'center'
                         });
                     }, 500);

                     // Remove highlight after 5 seconds
                     setTimeout(() => {
                         productCard.classList.remove('highlighted');
                     }, 5000);
                 }
             }
             
             // Handle category change for dynamic subcategory loading
             const categorySelect = document.querySelector('select[name="category"]');
             const subcategorySelect = document.querySelector('select[name="subcategory"]');
             
             if (categorySelect && subcategorySelect) {
                 categorySelect.addEventListener('change', function() {
                     // Clear subcategory when category changes
                     subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                     
                     // If a category is selected, load its subcategories
                     if (this.value) {
                         loadSubcategories(this.value);
                     }
                 });
             }
         });
         
         // Function to load subcategories dynamically
         function loadSubcategories(categoryName) {
             fetch(`get-subcategories.php?category=${encodeURIComponent(categoryName)}`)
                 .then(response => response.json())
                 .then(data => {
                     const subcategorySelect = document.querySelector('select[name="subcategory"]');
                     subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
                     
                     if (data.subcategories && data.subcategories.length > 0) {
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
                 });
         }
         
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
