<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include required files
require_once 'vendor/autoload.php';
require_once 'config1/mongodb.php';
require_once 'models/User.php';
require_once 'models/Order.php';
require_once 'models/Product.php';
require_once 'models/Cart.php';

// Initialize models
$userModel = new User();
$orderModel = new Order();
$productModel = new Product();
$cartModel = new Cart();

// Get user data
$userId = $_SESSION['user_id'];
$user = $userModel->getById($userId);

if (!$user) {
    // User not found, redirect to login
    session_destroy();
    header('Location: index.php');
    exit();
}

// Get user statistics
$userStats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'completed_orders' => 0,
    'total_spent' => 0,
    'wishlist_count' => 0,
    'cart_count' => 0
];

try {
    // Get order statistics
    $orderStats = $orderModel->getOrderStatistics($userId);
    $userStats['total_orders'] = $orderStats['total'];
    $userStats['pending_orders'] = $orderStats['pending'];
    $userStats['completed_orders'] = $orderStats['completed'];
    
    // Calculate total spent
    $userOrders = $orderModel->getUserOrders($userId);
    $totalSpent = 0;
    foreach ($userOrders as $order) {
        if ($order['status'] === 'completed') {
            $totalSpent += floatval($order['total_amount'] ?? 0);
        }
    }
    $userStats['total_spent'] = $totalSpent;
    
    // Get cart count
    $cartData = $cartModel->getCart($userId);
    $userStats['cart_count'] = $cartData['item_count'] ?? 0;
    
    // Get wishlist count from localStorage (will be handled by JavaScript)
    
} catch (Exception $e) {
    // Handle errors gracefully
    error_log("Dashboard error: " . $e->getMessage());
}

// Get recent orders (last 5)
$recentOrders = array_slice($userOrders, 0, 5);

// Get recent products for recommendations
$recentProducts = $productModel->getFeatured(6);

// Function to get asset path
function getAssetPath($path) {
    return $path;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Glamour Palace</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-dark);
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), #f4d03f);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .dashboard-header p {
            font-size: 1.1rem;
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stats-icon.orders { background: linear-gradient(45deg, #3498db, #2980b9); }
        .stats-icon.spent { background: linear-gradient(45deg, #27ae60, #229954); }
        .stats-icon.cart { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .stats-icon.wishlist { background: linear-gradient(45deg, #9b59b6, #8e44ad); }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .stats-label {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-item {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .product-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .product-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .product-info {
            padding: 1rem;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .product-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn.primary {
            background: var(--primary-color);
            color: white;
        }

        .action-btn.secondary {
            background: var(--secondary-color);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: white;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--secondary-color), #34495e);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .welcome-section h2 {
            margin: 0 0 0.5rem 0;
            font-size: 1.8rem;
        }

        .welcome-section p {
            margin: 0;
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .quick-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include 'heading/home-header.php'; ?>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-tachometer-alt"></i> Welcome Back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                    <p>Here's what's happening with your account</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div class="text-end">
                            <div class="fw-bold">Member since</div>
                            <div class="small"><?php echo date('M Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon orders">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="stats-number"><?php echo $userStats['total_orders']; ?></h3>
                    <p class="stats-label">Total Orders</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon spent">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="stats-number">$<?php echo number_format($userStats['total_spent'], 2); ?></h3>
                    <p class="stats-label">Total Spent</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon cart">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="stats-number" id="cart-count"><?php echo $userStats['cart_count']; ?></h3>
                    <p class="stats-label">Items in Cart</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon wishlist">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="stats-number" id="wishlist-count">0</h3>
                    <p class="stats-label">Wishlist Items</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mb-4">
            <a href="cart-unified.php" class="action-btn primary">
                <i class="fas fa-shopping-cart"></i>
                View Cart
            </a>
            <a href="wishlist.php" class="action-btn secondary">
                <i class="fas fa-heart"></i>
                My Wishlist
            </a>
            <a href="orders.php" class="action-btn secondary">
                <i class="fas fa-box"></i>
                Order History
            </a>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8">
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Recent Orders
                    </h3>
                    
                    <?php if (empty($recentOrders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-bag"></i>
                            <h4>No orders yet</h4>
                            <p>Start shopping to see your orders here</p>
                            <a href="index.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="order-item">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded p-2">
                                                <i class="fas fa-receipt fa-2x text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Order #<?php echo htmlspecialchars($order['order_number']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                            <small class="text-muted"><?php echo $order['item_count']; ?> items</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <span class="order-status status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center mt-3">
                            <a href="orders.php" class="btn btn-outline-primary">View All Orders</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Info & Recommendations -->
            <div class="col-lg-4">
                <!-- Account Information -->
                <div class="section-card mb-4">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Account Info
                    </h3>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small text-muted">Username</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Email</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Phone</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($user['contact_number'] ?? 'Not provided'); ?></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Location</label>
                            <div class="fw-bold">
                                <?php 
                                $location = [];
                                if (!empty($user['city'])) $location[] = ucfirst($user['city']);
                                if (!empty($user['region'])) $location[] = ucfirst($user['region']);
                                echo htmlspecialchars(implode(', ', $location) ?: 'Not provided');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="editProfile()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                        <button class="btn btn-outline-secondary btn-sm w-100" onclick="changePassword()">
                            <i class="fas fa-lock"></i> Change Password
                        </button>
                    </div>
                </div>

                <!-- Featured Products -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Recommended for You
                    </h3>
                    
                    <?php if (!empty($recentProducts)): ?>
                        <div class="row g-3">
                            <?php foreach (array_slice($recentProducts, 0, 3) as $product): ?>
                                <div class="col-12">
                                    <div class="product-card">
                                        <div class="product-image" style="background-image: url('<?php echo htmlspecialchars($product['image'] ?? 'img/placeholder.jpg'); ?>')"></div>
                                        <div class="product-info">
                                            <h6 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-outline-primary btn-sm">View All Products</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-star"></i>
                            <p>No recommendations available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Profile Edit Modal -->
    <?php include 'profile-edit-modal.php'; ?>

    <!-- Include Footer -->
    <?php include 'footer/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dashboard Widgets -->
    <script src="dashboard-widgets.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Update wishlist count from localStorage
        function updateWishlistCount() {
            try {
                const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                const wishlistCountElement = document.getElementById('wishlist-count');
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = wishlist.length;
                }
            } catch (error) {
                console.error('Error updating wishlist count:', error);
            }
        }

        // Update cart count
        function updateCartCount() {
            // Determine the correct path to cart API
            const currentPath = window.location.pathname;
            const isInSubdirectory = currentPath.includes('/dashboard.php');
            const cartApiPath = isInSubdirectory ? 'cart-api.php' : 'cart-api.php';
            
            fetch(cartApiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_count'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cart_count;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
        }

        // Edit profile function is now handled by profile-edit-modal.php

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Initial updates
            updateWishlistCount();
            updateCartCount();
            
            // Dashboard widgets will handle periodic updates
            // No need for manual setInterval here
        });

        // Listen for storage changes (when wishlist is updated in other tabs)
        window.addEventListener('storage', function(e) {
            if (e.key === 'wishlist') {
                updateWishlistCount();
            }
        });
    </script>
</body>
</html>
