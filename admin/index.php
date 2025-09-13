<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Try MongoDB first, fallback if it fails
require_once '../config1/mongodb.php';
require_once '../models/Category.php';
require_once '../models/Product.php';
require_once '../models/User.php';
$categoryModel = new Category();
$productModel = new Product();
$userModel = new User();

$categoryStats = $categoryModel->getCategorySummary();
$productStats = $productModel->getProductSummary();
$userStats = $userModel->getUserStatistics();

// Prepare data for charts
$categoryData = $categoryModel->getAll();
$productCategories = $productModel->getCategories();
$categoryCounts = [];
foreach ($productCategories as $cat) {
    $count = $productModel->getCount(['category' => $cat]);
    $categoryCounts[] = [
        'name' => $cat,
        'count' => $count
    ];
}

// Get recent products for the activity feed
$recentProducts = $productModel->getAll([], ['_id' => -1], 5);

// Prepare data for different chart types
$productStatusData = [
    ['name' => 'Active Products', 'count' => $productStats['total_products'] ?? 0],
    ['name' => 'Featured Products', 'count' => $productStats['featured_products'] ?? 0],
    ['name' => 'On Sale', 'count' => $productStats['products_on_sale'] ?? 0],
    ['name' => 'Out of Stock', 'count' => max(0, ($productStats['total_products'] ?? 0) - ($productStats['featured_products'] ?? 0))]
];

$userActivityData = [
    ['name' => 'Total Users', 'count' => $userStats['total_users'] ?? 0],
    ['name' => 'Active Users', 'count' => $userStats['active_users'] ?? 0],
    ['name' => 'New Users', 'count' => max(0, ($userStats['total_users'] ?? 0) - ($userStats['active_users'] ?? 0))],
    ['name' => 'Premium Users', 'count' => floor(($userStats['total_users'] ?? 0) * 0.2)]
];

$orderStatusData = [
    ['name' => 'Pending Orders', 'count' => rand(5, 25)],
    ['name' => 'Processing', 'count' => rand(3, 15)],
    ['name' => 'Shipped', 'count' => rand(10, 40)],
    ['name' => 'Delivered', 'count' => rand(20, 60)],
    ['name' => 'Cancelled', 'count' => rand(1, 8)]
];

$paymentMethodData = [
    ['name' => 'Cash on Delivery', 'count' => rand(30, 70)],
    ['name' => 'Mobile Money', 'count' => rand(20, 50)],
    ['name' => 'Bank Transfer', 'count' => rand(10, 30)],
    ['name' => 'Credit Card', 'count' => rand(5, 20)]
];

// Enhanced Business Intelligence Data
$salesData = [
    'total_sales' => 125000,
    'monthly_sales' => 24580,
    'growth_rate' => 15.2,
    'top_selling_products' => [
        ['name' => 'Designer Handbag', 'sales' => 89, 'revenue' => 8900],
        ['name' => 'Premium Watch', 'sales' => 67, 'revenue' => 13400],
        ['name' => 'Silk Dress', 'sales' => 156, 'revenue' => 15600],
        ['name' => 'Leather Jacket', 'sales' => 45, 'revenue' => 13500],
        ['name' => 'Diamond Ring', 'sales' => 23, 'revenue' => 46000]
    ],
    'category_performance' => [
        ['name' => 'Women\'s Fashion', 'sales' => 45, 'revenue' => 45000],
        ['name' => 'Accessories', 'sales' => 32, 'revenue' => 32000],
        ['name' => 'Men\'s Fashion', 'sales' => 28, 'revenue' => 28000],
        ['name' => 'Home & Living', 'sales' => 15, 'revenue' => 15000],
        ['name' => 'Electronics', 'sales' => 8, 'revenue' => 8000]
    ]
];

$userAnalytics = [
    'total_visitors' => 15420,
    'unique_visitors' => 8920,
    'returning_customers' => 2340,
    'new_customers' => 890,
    'top_regions' => [
        ['region' => 'Mogadishu', 'visitors' => 4560, 'percentage' => 29.6],
        ['region' => 'Hargeisa', 'visitors' => 2340, 'percentage' => 15.2],
        ['region' => 'Kismayo', 'visitors' => 1890, 'percentage' => 12.3],
        ['region' => 'Bosaso', 'visitors' => 1560, 'percentage' => 10.1],
        ['region' => 'Galkayo', 'visitors' => 1230, 'percentage' => 8.0]
    ],
    'user_behavior' => [
        ['metric' => 'Average Session Duration', 'value' => '4m 32s'],
        ['metric' => 'Pages per Session', 'value' => '6.8'],
        ['metric' => 'Bounce Rate', 'value' => '23.4%'],
        ['metric' => 'Conversion Rate', 'value' => '3.2%']
    ]
];

$inventoryInsights = [
    'total_products' => 2847,
    'low_stock_products' => 156,
    'out_of_stock' => 89,
    'fast_moving_items' => [
        ['name' => 'Summer Dresses', 'stock' => 45, 'demand' => 'High'],
        ['name' => 'Sneakers', 'stock' => 67, 'demand' => 'High'],
        ['name' => 'Handbags', 'stock' => 23, 'demand' => 'Medium'],
        ['name' => 'Watches', 'stock' => 34, 'demand' => 'Medium']
    ],
    'slow_moving_items' => [
        ['name' => 'Winter Coats', 'stock' => 89, 'demand' => 'Low'],
        ['name' => 'Formal Shoes', 'stock' => 123, 'demand' => 'Low']
    ]
];

$trendingData = [
    'trending_products' => [
        ['name' => 'Sustainable Fashion', 'growth' => '+45%'],
        ['name' => 'Athleisure Wear', 'growth' => '+32%'],
        ['name' => 'Minimalist Jewelry', 'growth' => '+28%'],
        ['name' => 'Home Office Furniture', 'growth' => '+67%']
    ],
    'seasonal_trends' => [
        ['season' => 'Spring', 'demand' => 'High', 'products' => 'Dresses, Light Jackets'],
        ['season' => 'Summer', 'demand' => 'Peak', 'products' => 'Swimwear, Sandals'],
        ['season' => 'Autumn', 'demand' => 'Medium', 'products' => 'Sweaters, Boots'],
        ['season' => 'Winter', 'demand' => 'Low', 'products' => 'Heavy Coats, Scarves']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glamour System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #2d3748;
            display: flex;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }

                    /* Mobile sidebar styles */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }
        }
        
        .admin-container {
            max-width: 1800px;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 50%, #f8b5d3 100%);
            color: white;
            border-radius: 25px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(255, 107, 157, 0.4);
            text-align: center;
            position: relative;
            overflow: hidden;
            font-family: 'Roboto', sans-serif;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.15"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.4;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 800;
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
            font-family: 'Roboto', sans-serif;
            color: white;
        }

        .header p {
            font-size: 1.3rem;
            opacity: 0.95;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            font-family: 'Roboto', sans-serif;
            color: white;
        }



        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary::before { background: linear-gradient(90deg, #667eea, #764ba2); }
        .stat-card.success::before { background: linear-gradient(90deg, #48bb78, #38a169); }
        .stat-card.warning::before { background: linear-gradient(90deg, #ed8936, #dd6b20); }
        .stat-card.info::before { background: linear-gradient(90deg, #4299e1, #3182ce); }
        
        .stat-icon {
            font-size: 4rem;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
        }

        .stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #48bb78, #38a169); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #ed8936, #dd6b20); }
        .stat-card.info .stat-icon { background: linear-gradient(135deg, #4299e1, #3182ce); }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 15px;
            letter-spacing: -2px;
        }
        
        .stat-label {
            font-size: 1.3rem;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-change {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #48bb78;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Charts Grid Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .chart-card h3 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            min-width: 0;
        }

        /* Activity Feed */
        .activity-feed {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
            grid-column: 1 / -1;
        }

        .activity-feed h3 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-radius: 15px;
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .activity-icon.product { background: linear-gradient(135deg, #667eea, #764ba2); }
        .activity-icon.order { background: linear-gradient(135deg, #48bb78, #38a169); }
        .activity-icon.user { background: linear-gradient(135deg, #ed8936, #dd6b20); }

        .activity-content h4 {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .activity-content p {
            color: #718096;
            font-size: 0.9rem;
        }

        .activity-time {
            color: #a0aec0;
            font-size: 0.8rem;
            margin-left: auto;
        }

        /* Actions Grid with Charts */
        .actions-charts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .action-chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }
        
        .action-chart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .action-chart-card:hover::before {
            transform: scaleX(1);
        }
        
        .action-chart-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .action-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .action-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
        }

        .action-chart-card:hover .action-icon {
            transform: scale(1.1);
        }
        
        .action-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2d3748;
            letter-spacing: -0.5px;
        }
        
        .action-description {
            color: #4a5568;
            line-height: 1.6;
            font-size: 1rem;
            margin-bottom: 25px;
        }

        .action-chart-container {
            height: 250px;
            position: relative;
        }

        /* Quick Actions */
        .quick-actions {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .quick-actions h2 {
            color: #2d3748;
            margin-bottom: 30px;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quick-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .quick-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 20px 30px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .quick-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            color: white;
        }

        .quick-btn.secondary {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .quick-btn.secondary:hover {
            box-shadow: 0 20px 40px rgba(72, 187, 120, 0.4);
        }

        .quick-btn.success {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
        }

        .quick-btn.success:hover {
            box-shadow: 0 20px 40px rgba(237, 137, 54, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .charts-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .actions-charts-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .actions-charts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .quick-buttons {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            

        }

        /* Loading Animation */
        .loading {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chart Tooltips */
        .chartjs-tooltip {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* System Reports Styles */
        .system-reports {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
            margin-top: 40px;
        }

        .system-reports h2 {
            color: #2d3748;
            margin-bottom: 30px;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Report Tabs */
        .report-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
            padding-bottom: 20px;
        }

        .report-tab {
            background: rgba(102, 126, 234, 0.1);
            color: #4a5568;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-tab:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .report-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* Report Panels */
        .report-panel {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .report-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* System Overview */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .report-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .report-card h3 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Health Indicators */
        .health-indicators {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .health-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            background: rgba(72, 187, 120, 0.1);
            border: 1px solid rgba(72, 187, 120, 0.2);
        }

        .health-item.warning {
            background: rgba(237, 137, 54, 0.1);
            border-color: rgba(237, 137, 54, 0.2);
        }

        .health-item i {
            font-size: 1.2rem;
            color: #48bb78;
        }

        .health-item.warning i {
            color: #ed8936;
        }

        .status-badge {
            margin-left: auto;
            background: #48bb78;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .health-item.warning .status-badge {
            background: #ed8936;
        }

        /* Database Stats */
        .db-stats {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-row strong {
            color: #667eea;
            font-weight: 700;
        }

        /* Uptime Info */
        .uptime-info {
            text-align: center;
        }

        .uptime-display {
            margin-bottom: 20px;
        }

        .uptime-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 900;
            color: #48bb78;
            margin-bottom: 5px;
        }

        .uptime-label {
            color: #4a5568;
            font-size: 1rem;
            font-weight: 600;
        }

        .uptime-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .uptime-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #718096;
        }

        /* Performance Metrics */
        .performance-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .metric-card h4 {
            color: #4a5568;
            margin-bottom: 15px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .metric-value {
                font-size: 2rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .metric-trend {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .metric-trend.positive {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
        }

        .metric-trend.negative {
            background: rgba(245, 101, 101, 0.1);
            color: #e53e3e;
        }

        .metric-trend.neutral {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .performance-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Inventory Analysis */
        .inventory-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .summary-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .alert-count {
            font-size: 2.5rem;
            font-weight: 900;
            color: #ed8936;
            margin-bottom: 15px;
        }

        .alert-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .alert-item {
            color: #718096;
            font-size: 0.9rem;
            padding: 8px 12px;
            background: rgba(237, 137, 54, 0.1);
            border-radius: 8px;
        }

        .category-breakdown {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-bar {
            flex: 1;
            height: 8px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .inventory-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* User Analytics */
        .user-analytics {
            margin-bottom: 30px;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }

        .analytics-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .analytics-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .growth-chart {
            height: 250px;
        }

        .demographics {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .demo-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .demo-item:last-child {
            border-bottom: none;
        }

        /* Financial Reports */
        .financial-overview {
            margin-bottom: 30px;
        }

        .financial-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .financial-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .financial-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .revenue-stats {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .revenue-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .revenue-item:last-child {
            border-bottom: none;
        }

        .revenue-item strong {
            color: #667eea;
            font-weight: 700;
        }

        .positive {
            color: #48bb78 !important;
        }

        .top-categories {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .category-rank {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
        }

        .rank {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .revenue {
            margin-left: auto;
            color: #667eea;
            font-weight: 700;
        }

        .financial-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Security Status */
        .security-overview {
            margin-bottom: 30px;
        }

        .security-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .security-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .security-card.good {
            border-color: rgba(72, 187, 120, 0.3);
        }

        .security-card.warning {
            border-color: rgba(237, 137, 54, 0.3);
        }

        .security-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .security-score {
            font-size: 2rem;
            font-weight: 900;
            color: #48bb78;
            margin-bottom: 20px;
            text-align: center;
        }

        .security-card.warning .security-score {
            color: #ed8936;
        }

        .security-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .security-details span {
            color: #718096;
            font-size: 0.9rem;
            padding: 6px 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
        }

        .security-log {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .security-log h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .log-entries {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .log-entry {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
        }

        .log-time {
            color: #a0aec0;
            font-size: 0.8rem;
            min-width: 100px;
        }

        .log-event {
            flex: 1;
            color: #4a5568;
            font-size: 0.9rem;
        }

        .log-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .log-status.success {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
        }

        .log-status.warning {
            background: rgba(237, 137, 54, 0.1);
            color: #ed8936;
        }

        /* Report Actions */
        .report-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid rgba(102, 126, 234, 0.1);
        }

        .report-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .report-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }

        .report-btn.secondary {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .report-btn.secondary:hover {
            box-shadow: 0 12px 35px rgba(72, 187, 120, 0.4);
        }

        .report-btn.success {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
        }

        .report-btn.success:hover {
            box-shadow: 0 12px 35px rgba(237, 137, 54, 0.4);
        }

        /* Sales Analytics Styles */
        .sales-overview {
            margin-bottom: 30px;
        }

        .sales-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .sales-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
            text-align: center;
        }

        .sales-card.primary {
            border-color: rgba(102, 126, 234, 0.3);
        }

        .sales-card.success {
            border-color: rgba(72, 187, 120, 0.3);
        }

        .sales-card.warning {
            border-color: rgba(237, 137, 54, 0.3);
        }

        .sales-card h4 {
            color: #4a5568;
            margin-bottom: 15px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sales-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .sales-trend {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .sales-trend.positive {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
        }

        .sales-trend.neutral {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .top-products {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
            margin-bottom: 30px;
        }

        .top-products h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .rank {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .product-name {
            flex: 1;
            color: #2d3748;
            font-weight: 600;
        }

        .sales-count {
            color: #667eea;
            font-weight: 600;
        }

        .revenue {
            color: #48bb78;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sales-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Product Performance Styles */
        .product-performance {
            margin-bottom: 30px;
        }

        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .performance-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .performance-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .category-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
        }

        .category-name {
            flex: 1;
            color: #2d3748;
            font-weight: 600;
        }

        .category-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .category-stats .sales {
            color: #667eea;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .category-stats .revenue {
            color: #48bb78;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .alert-summary {
            margin-bottom: 20px;
        }

        .alert-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .alert-item.warning {
            background: rgba(237, 137, 54, 0.1);
            border: 1px solid rgba(237, 137, 54, 0.2);
        }

        .alert-item.danger {
            background: rgba(245, 101, 101, 0.1);
            border: 1px solid rgba(245, 101, 101, 0.2);
        }

        .alert-icon {
            color: #ed8936;
        }

        .alert-item.danger .alert-icon {
            color: #f56565;
        }

        .fast-moving {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .item-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.05);
        }

        .item-status.high {
            border-left: 4px solid #48bb78;
        }

        .item-status.medium {
            border-left: 4px solid #ed8936;
        }

        .item-status.low {
            border-left: 4px solid #f56565;
        }

        .product-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Customer Insights Styles */
        .customer-insights {
            margin-bottom: 30px;
        }

        .customer-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .behavior-analysis {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
            margin-bottom: 30px;
        }

        .behavior-analysis h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .behavior-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .behavior-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .behavior-metric {
            color: #4a5568;
            font-weight: 600;
        }

        .behavior-value {
            color: #667eea;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .customer-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Regional Analysis Styles */
        .regional-analysis {
            margin-bottom: 30px;
        }

        .region-overview {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
            margin-bottom: 30px;
        }

        .region-overview h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .region-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .region-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .region-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .region-name {
            color: #2d3748;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .region-visitors {
            color: #667eea;
            font-weight: 600;
        }

        .region-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .percentage {
            color: #48bb78;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .region-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Market Trends Styles */
        .market-trends {
            margin-bottom: 30px;
        }

        .trends-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .trends-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .trends-card h4 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trending-list, .seasonal-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .trend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .trend-name {
            color: #2d3748;
            font-weight: 600;
        }

        .trend-growth {
            color: #48bb78;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .season-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .season-item.high {
            background: rgba(72, 187, 120, 0.1);
            border-color: rgba(72, 187, 120, 0.2);
        }

        .season-item.peak {
            background: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .season-item.medium {
            background: rgba(237, 137, 54, 0.1);
            border-color: rgba(237, 137, 54, 0.2);
        }

        .season-item.low {
            background: rgba(245, 101, 101, 0.1);
            border-color: rgba(245, 101, 101, 0.2);
        }

        .season-name {
            color: #2d3748;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .demand-level {
            color: #667eea;
            font-weight: 600;
        }

        .season-products {
            color: #718096;
            font-size: 0.9rem;
        }

        .trends-chart {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 300px;
        }

        /* Responsive Design for Reports */
        @media (max-width: 768px) {
            .report-tabs {
                flex-direction: column;
            }
            
            .report-grid {
                grid-template-columns: 1fr;
            }
            
            .sales-metrics {
                grid-template-columns: 1fr;
            }
            
            .performance-grid {
                grid-template-columns: 1fr;
            }
            
            .customer-metrics {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .behavior-grid {
                grid-template-columns: 1fr;
            }
            
            .trends-grid {
                grid-template-columns: 1fr;
            }
            
            .performance-metrics {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .inventory-summary {
                grid-template-columns: 1fr;
            }
            
            .analytics-grid {
                grid-template-columns: 1fr;
            }
            
            .financial-metrics {
                grid-template-columns: 1fr;
            }
            
            .security-status {
                grid-template-columns: 1fr;
            }
            
            .report-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Include Admin Sidebar -->
    <?php include 'includes/admin-sidebar.php'; ?>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
    <div class="admin-container">
        <!-- Header -->
        <div class="header">
                            <h1><i class="fas fa-gem"></i> Glamour Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>! Here's what's happening with your store today</p>
            
            
        </div>
        
            <!-- Statistics Cards -->
        <div class="stats-grid">
                <div class="stat-card primary loading">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                    <div class="stat-number"><?php echo $productStats['total_products'] ?? 0; ?></div>
                <div class="stat-label">Total Products</div>
                    <div class="stat-change">+12%</div>
            </div>
            
                <div class="stat-card success loading">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                    <div class="stat-number"><?php echo $categoryStats['total_categories'] ?? 0; ?></div>
                <div class="stat-label">Categories</div>
                    <div class="stat-change">+5%</div>
            </div>
            
                <div class="stat-card warning loading">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                    <div class="stat-number"><?php echo $userStats['total_users'] ?? 0; ?></div>
                <div class="stat-label">Total Users</div>
                    <div class="stat-change">+8%</div>
            </div>
            
                <div class="stat-card info loading">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                    <div class="stat-number"><?php echo $userStats['active_users'] ?? 0; ?></div>
                <div class="stat-label">Active Users</div>
                    <div class="stat-change">+15%</div>
            </div>
        </div>
        
            <!-- Charts Grid Section -->
            <div class="charts-grid">
                <div class="chart-card loading">
                    <h3><i class="fas fa-chart-pie"></i> Product Distribution by Category</h3>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card loading">
                    <h3><i class="fas fa-chart-pie"></i> Product Status Overview</h3>
                    <div class="chart-container">
                        <canvas id="productStatusChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card loading">
                    <h3><i class="fas fa-chart-pie"></i> User Activity Distribution</h3>
                    <div class="chart-container">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card loading">
                    <h3><i class="fas fa-chart-pie"></i> Order Status Overview</h3>
                    <div class="chart-container">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Actions Grid with Charts -->
            <div class="actions-charts-grid">
                <a href="add-product.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-title">Add Products</div>
                    </div>
                <div class="action-description">
                        Add new products to your store with images, descriptions, and pricing information. Support for multiple variants and categories.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="addProductChart"></canvas>
                </div>
            </a>
            
                <a href="manage-products.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-title">Manage Products</div>
                    </div>
                <div class="action-description">
                        View, edit, and manage all your products. Update prices, descriptions, status, and manage inventory levels.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="manageProductsChart"></canvas>
                </div>
            </a>
            
                <a href="manage-categories.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="action-title">Manage Categories</div>
                    </div>
                <div class="action-description">
                        Organize your products with categories and subcategories for better navigation and customer experience.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="manageCategoriesChart"></canvas>
                </div>
            </a>

                <a href="manage-users.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-title">Manage Users</div>
                    </div>
                <div class="action-description">
                        View and manage registered users, track their activities, and control account status and permissions.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="manageUsersChart"></canvas>
                </div>
            </a>

                <a href="manage-orders.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="action-title">Manage Orders</div>
                    </div>
                <div class="action-description">
                        View, track, and manage all customer orders. Update status, view details, and process payments efficiently.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="manageOrdersChart"></canvas>
                </div>
            </a>

                <a href="manage-payments.php" class="action-chart-card loading">
                    <div class="action-header">
                <div class="action-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-title">Manage Payments</div>
                    </div>
                <div class="action-description">
                        Monitor payment transactions, track payment status, and manage payment methods and security.
                    </div>
                    <div class="action-chart-container">
                        <canvas id="managePaymentsChart"></canvas>
                </div>
            </a>
            </div>
            
            <!-- Activity Feed -->
            <div class="activity-feed loading">
                <h3><i class="fas fa-activity"></i> Recent Activity</h3>
                <div class="activity-grid">
                    <?php if (!empty($recentProducts)): ?>
                        <?php foreach ($recentProducts as $product): ?>
                            <div class="activity-item">
                                <div class="activity-icon product">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New Product Added</h4>
                                    <p><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></p>
                                </div>
                                <div class="activity-time">Just now</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-icon product">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="activity-content">
                                <h4>No Recent Activity</h4>
                                <p>Start adding products to see activity here</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
        </div>
        
        <!-- Quick Actions -->
            <div class="quick-actions loading">
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
                    <a href="../index.php" class="quick-btn success">
                        <i class="fas fa-eye"></i> View Store
                </a>
            </div>
            </div>

            <!-- System Reports Section -->
            <div class="system-reports loading">
                <h2><i class="fas fa-chart-line"></i> System Reports & Analytics</h2>
                
                <!-- Report Navigation Tabs -->
                <div class="report-tabs">
                    <button class="report-tab active" data-tab="sales">
                        <i class="fas fa-chart-line"></i> Sales Analytics
                    </button>
                    <button class="report-tab" data-tab="products">
                        <i class="fas fa-box"></i> Product Performance
                    </button>
                    <button class="report-tab" data-tab="customers">
                        <i class="fas fa-users"></i> Customer Insights
                    </button>
                    <button class="report-tab" data-tab="regions">
                        <i class="fas fa-map-marker-alt"></i> Regional Analysis
                    </button>
                    <button class="report-tab" data-tab="trends">
                        <i class="fas fa-trending-up"></i> Market Trends
                    </button>
                    <button class="report-tab" data-tab="inventory">
                        <i class="fas fa-boxes"></i> Inventory Analysis
                    </button>
                    <button class="report-tab" data-tab="performance">
                        <i class="fas fa-rocket"></i> Performance Metrics
                    </button>
                    <button class="report-tab" data-tab="financial">
                        <i class="fas fa-dollar-sign"></i> Financial Reports
                    </button>
                    <button class="report-tab" data-tab="security">
                        <i class="fas fa-shield-alt"></i> Security Status
                    </button>
                </div>

                <!-- Report Content -->
                <div class="report-content">
                    <!-- Sales Analytics Tab -->
                    <div class="report-panel active" id="sales">
                        <div class="sales-overview">
                            <div class="sales-metrics">
                                <div class="sales-card primary">
                                    <h4><i class="fas fa-dollar-sign"></i> Total Sales</h4>
                                    <div class="sales-value">$<?php echo number_format($salesData['total_sales']); ?></div>
                                    <div class="sales-trend positive">
                                        <i class="fas fa-arrow-up"></i> <?php echo $salesData['growth_rate']; ?>% growth
                                    </div>
                                </div>
                                <div class="sales-card success">
                                    <h4><i class="fas fa-chart-line"></i> Monthly Sales</h4>
                                    <div class="sales-value">$<?php echo number_format($salesData['monthly_sales']); ?></div>
                                    <div class="sales-trend positive">
                                        <i class="fas fa-arrow-up"></i> This month
                                    </div>
                                </div>
                                <div class="sales-card warning">
                                    <h4><i class="fas fa-shopping-cart"></i> Orders</h4>
                                    <div class="sales-value"><?php echo array_sum(array_column($salesData['top_selling_products'], 'sales')); ?></div>
                                    <div class="sales-trend neutral">
                                        <i class="fas fa-minus"></i> Total orders
                                    </div>
                                </div>
                            </div>
                            
                            <div class="top-products">
                                <h4><i class="fas fa-trophy"></i> Top Selling Products</h4>
                                <div class="product-list">
                                    <?php foreach ($salesData['top_selling_products'] as $index => $product): ?>
                                        <div class="product-item">
                                            <span class="rank">#<?php echo $index + 1; ?></span>
                                            <span class="product-name"><?php echo $product['name']; ?></span>
                                            <span class="sales-count"><?php echo $product['sales']; ?> sold</span>
                                            <span class="revenue">$<?php echo number_format($product['revenue']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="sales-chart">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Product Performance Tab -->
                    <div class="report-panel" id="products">
                        <div class="product-performance">
                            <div class="performance-grid">
                                <div class="performance-card">
                                    <h4><i class="fas fa-boxes"></i> Category Performance</h4>
                                    <div class="category-list">
                                        <?php foreach ($salesData['category_performance'] as $category): ?>
                                            <div class="category-item">
                                                <span class="category-name"><?php echo $category['name']; ?></span>
                                                <div class="category-stats">
                                                    <span class="sales"><?php echo $category['sales']; ?>%</span>
                                                    <span class="revenue">$<?php echo number_format($category['revenue']); ?></span>
                                                </div>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $category['sales']; ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="performance-card">
                                    <h4><i class="fas fa-exclamation-triangle"></i> Inventory Alerts</h4>
                                    <div class="alert-summary">
                                        <div class="alert-item warning">
                                            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                            <span class="alert-text">Low Stock: <?php echo $inventoryInsights['low_stock_products']; ?> products</span>
                                        </div>
                                        <div class="alert-item danger">
                                            <span class="alert-icon"><i class="fas fa-times-circle"></i></span>
                                            <span class="alert-text">Out of Stock: <?php echo $inventoryInsights['out_of_stock']; ?> products</span>
                                        </div>
                                    </div>
                                    
                                    <h5>Fast Moving Items</h5>
                                    <div class="fast-moving">
                                        <?php foreach ($inventoryInsights['fast_moving_items'] as $item): ?>
                                            <div class="item-status <?php echo strtolower($item['demand']); ?>">
                                                <span class="item-name"><?php echo $item['name']; ?></span>
                                                <span class="stock-level">Stock: <?php echo $item['stock']; ?></span>
                                                <span class="demand-level"><?php echo $item['demand']; ?> Demand</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-chart">
                                <canvas id="productPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Insights Tab -->
                    <div class="report-panel" id="customers">
                        <div class="customer-insights">
                            <div class="customer-metrics">
                                <div class="metric-card">
                                    <h4><i class="fas fa-users"></i> Total Visitors</h4>
                                    <div class="metric-value"><?php echo number_format($userAnalytics['total_visitors']); ?></div>
                                    <div class="metric-trend positive">
                                        <i class="fas fa-arrow-up"></i> All time
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class="fas fa-user-plus"></i> Unique Visitors</h4>
                                    <div class="metric-value"><?php echo number_format($userAnalytics['unique_visitors']); ?></div>
                                    <div class="metric-trend positive">
                                        <i class="fas fa-arrow-up"></i> New users
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class="fas fa-user-check"></i> Returning Customers</h4>
                                    <div class="metric-value"><?php echo number_format($userAnalytics['returning_customers']); ?></div>
                                    <div class="metric-trend positive">
                                        <i class="fas fa-arrow-up"></i> Loyal customers
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class="fas fa-user-clock"></i> New Customers</h4>
                                    <div class="metric-value"><?php echo number_format($userAnalytics['new_customers']); ?></div>
                                    <div class="metric-trend positive">
                                        <i class="fas fa-arrow-up"></i> This month
                                    </div>
                                </div>
                            </div>
                            
                            <div class="behavior-analysis">
                                <h4><i class="fas fa-chart-bar"></i> User Behavior Analysis</h4>
                                <div class="behavior-grid">
                                    <?php foreach ($userAnalytics['user_behavior'] as $behavior): ?>
                                        <div class="behavior-item">
                                            <span class="behavior-metric"><?php echo $behavior['metric']; ?></span>
                                            <span class="behavior-value"><?php echo $behavior['value']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="customer-chart">
                                <canvas id="customerChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Regional Analysis Tab -->
                    <div class="report-panel" id="regions">
                        <div class="regional-analysis">
                            <div class="region-overview">
                                <h4><i class="fas fa-map"></i> Regional Visitor Distribution</h4>
                                <div class="region-list">
                                    <?php foreach ($userAnalytics['top_regions'] as $region): ?>
                                        <div class="region-item">
                                            <div class="region-info">
                                                <span class="region-name"><?php echo $region['region']; ?></span>
                                                <span class="region-visitors"><?php echo number_format($region['visitors']); ?> visitors</span>
                                            </div>
                                            <div class="region-stats">
                                                <span class="percentage"><?php echo $region['percentage']; ?>%</span>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $region['percentage']; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="region-chart">
                                <canvas id="regionalChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Market Trends Tab -->
                    <div class="report-panel" id="trends">
                        <div class="market-trends">
                            <div class="trends-grid">
                                <div class="trends-card">
                                    <h4><i class="fas fa-fire"></i> Trending Products</h4>
                                    <div class="trending-list">
                                        <?php foreach ($trendingData['trending_products'] as $trend): ?>
                                            <div class="trend-item">
                                                <span class="trend-name"><?php echo $trend['name']; ?></span>
                                                <span class="trend-growth positive"><?php echo $trend['growth']; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="trends-card">
                                    <h4><i class="fas fa-calendar-alt"></i> Seasonal Trends</h4>
                                    <div class="seasonal-list">
                                        <?php foreach ($trendingData['seasonal_trends'] as $season): ?>
                                            <div class="season-item <?php echo strtolower($season['demand']); ?>">
                                                <span class="season-name"><?php echo $season['season']; ?></span>
                                                <span class="demand-level"><?php echo $season['demand']; ?> Demand</span>
                                                <span class="season-products"><?php echo $season['products']; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="trends-chart">
                                <canvas id="trendsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics Tab -->
                    <div class="report-panel" id="performance">
                        <div class="performance-metrics">
                            <div class="metric-card">
                                <h4><i class="fas fa-tachometer-alt"></i> Response Time</h4>
                                <div class="metric-value">127ms</div>
                                <div class="metric-trend positive">
                                    <i class="fas fa-arrow-up"></i> 12% improvement
                                </div>
                            </div>
                            <div class="metric-card">
                                <h4><i class="fas fa-memory"></i> Memory Usage</h4>
                                <div class="metric-value">68%</div>
                                <div class="metric-trend neutral">
                                    <i class="fas fa-minus"></i> Stable
                                </div>
                            </div>
                            <div class="metric-card">
                                <h4><i class="fas fa-hdd"></i> Disk I/O</h4>
                                <div class="metric-value">45 MB/s</div>
                                <div class="metric-trend positive">
                                    <i class="fas fa-arrow-up"></i> 8% faster
                                </div>
                            </div>
                            <div class="metric-card">
                                <h4><i class="fas fa-network-wired"></i> Network Load</h4>
                                <div class="metric-value">2.3 GB</div>
                                <div class="metric-trend negative">
                                    <i class="fas fa-arrow-down"></i> 5% decrease
                                </div>
                            </div>
                        </div>
                        <div class="performance-chart">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Inventory Analysis Tab -->
                    <div class="report-panel" id="inventory">
                        <div class="inventory-summary">
                            <div class="summary-card">
                                <h4>Low Stock Alert</h4>
                                <div class="alert-count">12</div>
                                <div class="alert-items">
                                    <span class="alert-item">Electronics (3 items)</span>
                                    <span class="alert-item">Clothing (5 items)</span>
                                    <span class="alert-item">Accessories (4 items)</span>
                                </div>
                            </div>
                            <div class="summary-card">
                                <h4>Category Distribution</h4>
                                <div class="category-breakdown">
                                    <div class="category-item">
                                        <span>Women's Fashion</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 35%"></div>
                                        </div>
                                        <span>35%</span>
                                    </div>
                                    <div class="category-item">
                                        <span>Men's Fashion</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 28%"></div>
                                        </div>
                                        <span>28%</span>
                                    </div>
                                    <div class="category-item">
                                        <span>Accessories</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 22%"></div>
                                        </div>
                                        <span>22%</span>
                                    </div>
                                    <div class="category-item">
                                        <span>Home & Living</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 15%"></div>
                                        </div>
                                        <span>15%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="inventory-chart">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>

                    <!-- User Analytics Tab -->
                    <div class="report-panel" id="users">
                        <div class="user-analytics">
                            <div class="analytics-grid">
                                <div class="analytics-card">
                                    <h4>User Growth</h4>
                                    <div class="growth-chart">
                                        <canvas id="userGrowthChart"></canvas>
                                    </div>
                                </div>
                                <div class="analytics-card">
                                    <h4>User Demographics</h4>
                                    <div class="demographics">
                                        <div class="demo-item">
                                            <span>Age 18-25:</span>
                                            <span>45%</span>
                                        </div>
                                        <div class="demo-item">
                                            <span>Age 26-35:</span>
                                            <span>32%</span>
                                        </div>
                                        <div class="demo-item">
                                            <span>Age 36-45:</span>
                                            <span>18%</span>
                                        </div>
                                        <div class="demo-item">
                                            <span>Age 45+:</span>
                                            <span>5%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Reports Tab -->
                    <div class="report-panel" id="financial">
                        <div class="financial-overview">
                            <div class="financial-metrics">
                                <div class="financial-card">
                                    <h4>Revenue Overview</h4>
                                    <div class="revenue-stats">
                                        <div class="revenue-item">
                                            <span>This Month:</span>
                                            <strong>$24,580</strong>
                                        </div>
                                        <div class="revenue-item">
                                            <span>Last Month:</span>
                                            <strong>$21,340</strong>
                                        </div>
                                        <div class="revenue-item">
                                            <span>Growth:</span>
                                            <strong class="positive">+15.2%</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="financial-card">
                                    <h4>Top Performing Categories</h4>
                                    <div class="top-categories">
                                        <div class="category-rank">
                                            <span class="rank">1</span>
                                            <span>Women's Fashion</span>
                                            <span class="revenue">$8,420</span>
                                        </div>
                                        <div class="category-rank">
                                            <span class="rank">2</span>
                                            <span>Accessories</span>
                                            <span class="revenue">$6,180</span>
                                        </div>
                                        <div class="category-rank">
                                            <span class="rank">3</span>
                                            <span>Men's Fashion</span>
                                            <span class="revenue">$5,890</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="financial-chart">
                                <canvas id="financialChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Security Status Tab -->
                    <div class="report-panel" id="security">
                        <div class="security-overview">
                            <div class="security-status">
                                <div class="security-card good">
                                    <h4><i class="fas fa-shield-check"></i> Authentication</h4>
                                    <div class="security-score">95/100</div>
                                    <div class="security-details">
                                        <span>2FA Enabled: Yes</span>
                                        <span>Password Policy: Strong</span>
                                        <span>Session Timeout: 30 min</span>
                                    </div>
                                </div>
                                <div class="security-card good">
                                    <h4><i class="fas fa-lock"></i> Data Protection</h4>
                                    <div class="security-score">88/100</div>
                                    <div class="security-details">
                                        <span>Encryption: AES-256</span>
                                        <span>Backup: Daily</span>
                                        <span>SSL: Enabled</span>
                                    </div>
                                </div>
                                <div class="security-card warning">
                                    <h4><i class="fas fa-eye"></i> Access Control</h4>
                                    <div class="security-score">72/100</div>
                                    <div class="security-details">
                                        <span>Admin Users: 3</span>
                                        <span>Last Audit: 5 days ago</span>
                                        <span>Permissions: Review needed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="security-log">
                                <h4>Recent Security Events</h4>
                                <div class="log-entries">
                                    <div class="log-entry">
                                        <span class="log-time">2 hours ago</span>
                                        <span class="log-event">Admin login from 192.168.1.100</span>
                                        <span class="log-status success">Success</span>
                                    </div>
                                    <div class="log-entry">
                                        <span class="log-time">1 day ago</span>
                                        <span class="log-event">Failed login attempt from 203.0.113.45</span>
                                        <span class="log-status warning">Blocked</span>
                                    </div>
                                    <div class="log-entry">
                                        <span class="log-time">3 days ago</span>
                                        <span class="log-event">Database backup completed</span>
                                        <span class="log-status success">Success</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Actions -->
                <div class="report-actions">
                    <button class="report-btn primary" onclick="generateReport()">
                        <i class="fas fa-download"></i> Generate Full Report
                    </button>
                    <button class="report-btn secondary" onclick="exportData()">
                        <i class="fas fa-file-export"></i> Export Data
                    </button>
                    <button class="report-btn success" onclick="scheduleReport()">
                        <i class="fas fa-calendar"></i> Schedule Reports
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize charts and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate loading elements
            const loadingElements = document.querySelectorAll('.loading');
            loadingElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.animationDelay = `${index * 0.1}s`;
                    }, 100);
            });

            // Chart.js global configuration
            Chart.defaults.font.family = "'Circular Std', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
            Chart.defaults.color = '#4a5568';

            // Color palettes for charts
            const colorPalettes = {
                primary: ['#667eea', '#764ba2', '#48bb78', '#38a169', '#ed8936', '#dd6b20', '#4299e1', '#3182ce', '#9f7aea', '#805ad5'],
                success: ['#48bb78', '#38a169', '#9ae6b4', '#68d391', '#4fd1c7', '#38b2ac', '#81e6d9', '#6bcfc7'],
                warning: ['#ed8936', '#dd6b20', '#f6ad55', '#ed8936', '#f6e05e', '#d69e2e', '#fbd38d', '#f6ad55'],
                info: ['#4299e1', '#3182ce', '#63b3ed', '#4299e1', '#90cdf4', '#63b3ed', '#bee3f8', '#90cdf4']
            };

            // Helper function to create pie charts
            function createPieChart(canvasId, data, colors, title) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.name),
                        datasets: [{
                            data: data.map(item => item.count),
                            backgroundColor: colors,
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverBorderWidth: 5,
                            hoverBorderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#667eea',
                                borderWidth: 2,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        cutout: '65%'
                    }
                });
            }

            // Create all charts
            const categoryChart = createPieChart('categoryChart', 
                <?php echo json_encode($categoryCounts); ?>, 
                colorPalettes.primary, 
                'Product Distribution by Category'
            );

            const productStatusChart = createPieChart('productStatusChart', 
                <?php echo json_encode($productStatusData); ?>, 
                colorPalettes.success, 
                'Product Status Overview'
            );

            const userActivityChart = createPieChart('userActivityChart', 
                <?php echo json_encode($userActivityData); ?>, 
                colorPalettes.warning, 
                'User Activity Distribution'
            );

            const orderStatusChart = createPieChart('orderStatusChart', 
                <?php echo json_encode($orderStatusData); ?>, 
                colorPalettes.info, 
                'Order Status Overview'
            );

            // Action-specific charts with sample data
            const addProductChart = createPieChart('addProductChart', 
                [
                    {name: 'Clothing', count: 45},
                    {name: 'Electronics', count: 23},
                    {name: 'Home & Living', count: 18},
                    {name: 'Accessories', count: 32}
                ], 
                colorPalettes.primary, 
                'Product Categories'
            );

            const manageProductsChart = createPieChart('manageProductsChart', 
                [
                    {name: 'Active', count: 78},
                    {name: 'Draft', count: 12},
                    {name: 'Archived', count: 8},
                    {name: 'Out of Stock', count: 15}
                ], 
                colorPalettes.success, 
                'Product Status'
            );

            const manageCategoriesChart = createPieChart('manageCategoriesChart', 
                [
                    {name: 'Main Categories', count: 8},
                    {name: 'Subcategories', count: 24},
                    {name: 'Featured', count: 5},
                    {name: 'Active', count: 32}
                ], 
                colorPalettes.warning, 
                'Category Overview'
            );

            const manageUsersChart = createPieChart('manageUsersChart', 
                [
                    {name: 'Active Users', count: 156},
                    {name: 'New Users', count: 23},
                    {name: 'Premium', count: 45},
                    {name: 'Inactive', count: 12}
                ], 
                colorPalettes.info, 
                'User Distribution'
            );

            const manageOrdersChart = createPieChart('manageOrdersChart', 
                [
                    {name: 'Pending', count: 18},
                    {name: 'Processing', count: 12},
                    {name: 'Shipped', count: 25},
                    {name: 'Delivered', count: 67}
                ], 
                colorPalettes.success, 
                'Order Status'
            );

            const managePaymentsChart = createPieChart('managePaymentsChart', 
                [
                    {name: 'Cash on Delivery', count: 45},
                    {name: 'Mobile Money', count: 32},
                    {name: 'Bank Transfer', count: 18},
                    {name: 'Credit Card', count: 12}
                ], 
                colorPalettes.primary, 
                'Payment Methods'
            );

            // Add interactive effects to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-15px) scale(1.03)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click effects to action chart cards
            const actionChartCards = document.querySelectorAll('.action-chart-card');
            actionChartCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-8px) scale(1.02)';
                }, 150);
                });
            });

            // Add ripple effect to quick buttons
            const quickBtns = document.querySelectorAll('.quick-btn');
            quickBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });

        // Real-time updates simulation
        setInterval(() => {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const currentValue = parseInt(stat.textContent);
                if (Math.random() > 0.7) {
                    const newValue = currentValue + Math.floor(Math.random() * 5) + 1;
                    stat.textContent = newValue;
                    
                    // Add pulse animation
                    stat.style.animation = 'pulse 0.5s ease';
                    setTimeout(() => {
                        stat.style.animation = '';
                    }, 500);
                }
            });
        }, 10000);

        // Add pulse animation CSS
        const pulseStyle = document.createElement('style');
        pulseStyle.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(pulseStyle);

        // Mobile menu toggle functionality
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');

        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            
            // Update toggle button icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            const icon = mobileMenuToggle.querySelector('i');
            icon.className = 'fas fa-bars';
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.className = 'fas fa-bars';
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.className = 'fas fa-bars';
            }
        });

        // Report Tab Functionality
        const reportTabs = document.querySelectorAll('.report-tab');
        const reportPanels = document.querySelectorAll('.report-panel');

        reportTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and panels
                reportTabs.forEach(t => t.classList.remove('active'));
                reportPanels.forEach(p => p.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding panel
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        // Business Intelligence Charts
        const salesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($salesData['top_selling_products'], 'name')); ?>,
                datasets: [{
                    label: 'Sales Count',
                    data: <?php echo json_encode(array_column($salesData['top_selling_products'], 'sales')); ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(72, 187, 120, 0.8)',
                        'rgba(237, 137, 54, 0.8)',
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(159, 122, 234, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Top Selling Products Performance',
                        font: {
                            size: 16,
                            weight: '600'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const productPerformanceChart = new Chart(document.getElementById('productPerformanceChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($salesData['category_performance'], 'name')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($salesData['category_performance'], 'revenue')); ?>,
                    backgroundColor: [
                        '#667eea',
                        '#48bb78',
                        '#ed8936',
                        '#f56565',
                        '#9f7aea'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Revenue by Category',
                        font: {
                            size: 16,
                            weight: '600'
                        }
                    }
                },
                cutout: '60%'
            }
        });

        const customerChart = new Chart(document.getElementById('customerChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Total Visitors',
                    data: [12000, 13500, 14200, 14800, 15100, 15420],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Unique Visitors',
                    data: [8000, 8500, 8700, 8800, 8900, 8920],
                    borderColor: '#48bb78',
                    backgroundColor: 'rgba(72, 187, 120, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: true,
                        text: 'Visitor Growth Trends',
                        font: {
                            size: 16,
                            weight: '600'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    }
                }
            }
        });

        const regionalChart = new Chart(document.getElementById('regionalChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($userAnalytics['top_regions'], 'region')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($userAnalytics['top_regions'], 'visitors')); ?>,
                    backgroundColor: [
                        '#667eea',
                        '#48bb78',
                        '#ed8936',
                        '#f56565',
                        '#9f7aea'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Regional Visitor Distribution',
                        font: {
                            size: 16,
                            weight: '600'
                        }
                    }
                }
            }
        });

        const trendsChart = new Chart(document.getElementById('trendsChart').getContext('2d'), {
            type: 'radar',
            data: {
                labels: ['Sustainable Fashion', 'Athleisure Wear', 'Minimalist Jewelry', 'Home Office Furniture'],
                datasets: [{
                    label: 'Growth Rate',
                    data: [45, 32, 28, 67],
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderColor: '#667eea',
                    borderWidth: 3,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Product Category Trends',
                        font: {
                            size: 16,
                            weight: '600'
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        },
                        pointLabels: {
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        // Additional Charts for Reports
        const performanceChart = new Chart(document.getElementById('performanceChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: [150, 140, 135, 130, 128, 127],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    }
                }
            }
        });

        const inventoryChart = new Chart(document.getElementById('inventoryChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Women\'s Fashion', 'Men\'s Fashion', 'Accessories', 'Home & Living', 'Electronics'],
                datasets: [{
                    label: 'Stock Level',
                    data: [85, 72, 68, 45, 38],
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(72, 187, 120, 0.8)',
                        'rgba(237, 137, 54, 0.8)',
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(159, 122, 234, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const userGrowthChart = new Chart(document.getElementById('userGrowthChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Users',
                    data: [45, 52, 48, 61, 73, 89],
                    borderColor: '#48bb78',
                    backgroundColor: 'rgba(72, 187, 120, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(72, 187, 120, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(72, 187, 120, 0.1)'
                        }
                    }
                }
            }
        });

        const financialChart = new Chart(document.getElementById('financialChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Women\'s Fashion', 'Accessories', 'Men\'s Fashion', 'Home & Living', 'Electronics'],
                datasets: [{
                    data: [35, 25, 24, 10, 6],
                    backgroundColor: [
                        '#667eea',
                        '#48bb78',
                        '#ed8936',
                        '#f56565',
                        '#9f7aea'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Report Functions
        window.generateReport = function() {
            alert('Generating comprehensive system report...\nThis will include all metrics, charts, and analytics data.');
        };

        window.exportData = function() {
            alert('Exporting system data...\nData will be downloaded in CSV format.');
        };

        window.scheduleReport = function() {
            alert('Scheduling automated reports...\nReports will be generated and sent to your email on a regular basis.');
        };
    </script>
</body>
</html> 
