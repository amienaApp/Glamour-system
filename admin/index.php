<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../models/Product.php';
require_once '../models/User.php';

$categoryModel = new Category();
$productModel = new Product();
$userModel = new User();

$categoryStats = $categoryModel->getCategorySummary();
$productStats = $productModel->getProductSummary();
$userStats = $userModel->getUserStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glamour System</title>
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
            margin-left: 0;
            padding: 30px;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.1);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header h1 {
            color: #3E2723;
            font-size: 2.8rem;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header p {
            color: #3E2723;
            font-size: 1.2rem;
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .header-btn {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(41, 182, 246, 0.3);
        }

        .header-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 182, 246, 0.4);
            text-decoration: none;
            color: white;
        }

        .header-btn:nth-child(2) {
            background: linear-gradient(135deg, #3E2723, #5D4037);
        }

        .header-btn:nth-child(2):hover {
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.4);
        }

        .header-btn:nth-child(3) {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
        }

        .header-btn:nth-child(3):hover {
            box-shadow: 0 8px 25px rgba(2, 136, 209, 0.4);
        }


        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(62, 39, 35, 0.15);
            border-color: rgba(129, 212, 250, 0.3);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #29B6F6, #0288D1);
        }
        
        .stat-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #3E2723;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        
        .stat-label {
            font-size: 1.2rem;
            color: #3E2723;
            font-weight: 600;
            opacity: 0.8;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .action-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(62, 39, 35, 0.15);
            text-decoration: none;
            color: inherit;
            border-color: rgba(129, 212, 250, 0.3);
        }
        
        .action-icon {
            font-size: 3rem;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }
        
        .action-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }
        
        .action-description {
            color: #3E2723;
            line-height: 1.6;
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .quick-actions {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .quick-actions h2 {
            color: #3E2723;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .quick-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .quick-btn {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(41, 182, 246, 0.3);
        }

        .quick-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(41, 182, 246, 0.4);
            text-decoration: none;
            color: white;
        }

        .quick-btn.secondary {
            background: linear-gradient(135deg, #0288D1, #29B6F6);
        }

        .quick-btn.secondary:hover {
            box-shadow: 0 15px 35px rgba(2, 136, 209, 0.4);
        }

        .quick-btn.success {
            background: linear-gradient(135deg, #3E2723, #5D4037);
        }

        .quick-btn.success:hover {
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.4);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-buttons {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->


    <!-- Main Content -->
    <div class="main-content">
    <div class="admin-container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-crown"></i> Glamour Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>! Manage your e-commerce store with ease</p>
            
            <!-- Header Action Buttons -->
            <div class="header-actions">
                <a href="add-product.php" class="header-btn">
                    <i class="fas fa-plus"></i> Add Product
                </a>
                <a href="manage-products.php" class="header-btn">
                    <i class="fas fa-boxes"></i> Manage Products
                </a>
                <a href="manage-orders.php" class="header-btn">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
                <a href="manage-categories.php" class="header-btn">
                    <i class="fas fa-folder"></i> Manage Categories
                </a>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-number"><?php echo $productStats['total_products']; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-number"><?php echo $categoryStats['total_categories']; ?></div>
                <div class="stat-label">Categories</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $userStats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-number"><?php echo $userStats['active_users']; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>
        
        <!-- Main Actions -->
        <div class="actions-grid">
            <a href="add-product.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-title">Add Products</div>
                <div class="action-description">
                    Add new products to your store with images, descriptions, and pricing information.
                </div>
            </a>
            
            <a href="manage-products.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-title">Manage Products</div>
                <div class="action-description">
                    View, edit, and manage all your products. Update prices, descriptions, and status.
                </div>
            </a>
            
            <a href="manage-categories.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="action-title">Manage Categories</div>
                <div class="action-description">
                    Organize your products with categories and subcategories for better navigation.
                </div>
            </a>

            <a href="manage-users.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-title">Manage Users</div>
                <div class="action-description">
                    View and manage registered users, track their activities, and control account status.
                </div>
            </a>

            <a href="manage-orders.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="action-title">Manage Orders</div>
                <div class="action-description">
                    View, track, and manage all customer orders. Update status, view details, and process payments.
                </div>
            </a>

            <a href="manage-payments.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-title">Manage Payments</div>
                <div class="action-description">
                    Monitor payment transactions, track payment status, and manage payment methods.
                </div>
            </a>

        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="quick-buttons">
                <a href="add-product.php" class="quick-btn">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
                <a href="manage-orders.php" class="quick-btn">
                    <i class="fas fa-shopping-cart"></i> View Orders
                </a>
                <a href="manage-categories.php" class="quick-btn secondary">
                    <i class="fas fa-folder-plus"></i> Add Category
                </a>
                <a href="../index.html" class="quick-btn">
                    <i class="fas fa-eye"></i> View Frontend
                </a>
            </div>
            </div>
        </div>
    </div>
    
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });

            // Add click effects to action cards
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-5px)';
                }, 150);
                });
            });
        });
    </script>
</body>
</html> 
