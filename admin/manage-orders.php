<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
require_once '../models/Order.php';
require_once '../models/Payment.php';
require_once '../models/Product.php';
require_once '../models/User.php';

$orderModel = new Order();
$paymentModel = new Payment();
$productModel = new Product();
$userModel = new User();

// Helper function to safely convert BSONArray to regular array
function ensureArray($data) {
    // Check if MongoDB classes are available
    if (class_exists('MongoDB\Model\BSONArray') && $data instanceof MongoDB\Model\BSONArray) {
        return $data->toArray();
    } elseif (is_array($data)) {
        return $data;
    } else {
        return [];
    }
}

$message = '';
$messageType = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = $_POST['order_id'] ?? '';
    $newStatus = $_POST['new_status'] ?? '';
    
    if ($orderModel->updateStatus($orderId, $newStatus)) {
        $message = 'Order status updated successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to update order status.';
        $messageType = 'error';
    }
}

 // Handle delete action
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
     $orderId = $_POST['order_id'] ?? '';
     if ($orderModel->delete($orderId)) {
         $message = 'Order deleted successfully!';
         $messageType = 'success';
     } else {
         $message = 'Failed to delete order.';
         $messageType = 'error';
     }
 }
 
 // Handle bulk delete action
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
     $orderIds = $_POST['order_ids'] ?? '';
     if (!empty($orderIds)) {
         $orderIdArray = explode(',', $orderIds);
         $successCount = 0;
         $totalCount = count($orderIdArray);
         
         foreach ($orderIdArray as $orderId) {
             $orderId = trim($orderId);
             if (!empty($orderId) && $orderModel->delete($orderId)) {
                 $successCount++;
             }
         }
         
         if ($successCount === $totalCount) {
             $message = "Successfully deleted {$successCount} order(s)!";
             $messageType = 'success';
         } elseif ($successCount > 0) {
             $message = "Partially successful: {$successCount} out of {$totalCount} orders deleted.";
             $messageType = 'success';
         } else {
             $message = 'Failed to delete any orders.';
             $messageType = 'error';
         }
     } else {
         $message = 'No orders selected for deletion.';
         $messageType = 'error';
     }
 }

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$paymentFilter = $_GET['payment_method'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$searchQuery = $_GET['search'] ?? '';



// Build filter criteria
$filters = [];
if ($statusFilter) {
    $filters['status'] = $statusFilter;
}
if ($dateFilter) {
    $filters['date'] = $dateFilter;
}

// Get all orders with filters (excluding search for now)
$orders = $orderModel->getAllOrders($filters, '');

// Apply payment method filter and search with customer data
if (($paymentFilter || $searchQuery) && !empty($orders)) {
    $filteredOrders = [];
    foreach ($orders as $order) {
        $payment = $paymentModel->getByOrderId($order['_id'])[0] ?? null;
        $includeOrder = true;
        
        // Check payment method filter
        if ($paymentFilter) {
            if (!$payment || $payment['payment_method'] !== $paymentFilter) {
                $includeOrder = false;
            }
        }
        
        // Check search query against order data and customer data
        if ($searchQuery) {
            $searchLower = strtolower(trim($searchQuery));
            $foundMatch = false;
            
            // Search in order data first
            $orderNumber = strtolower($order['order_number'] ?? '');
            $userId = strtolower($order['user_id'] ?? '');
            $shippingAddress = strtolower($order['shipping_address'] ?? '');
            
            if (strpos($orderNumber, $searchLower) !== false ||
                strpos($userId, $searchLower) !== false ||
                strpos($shippingAddress, $searchLower) !== false) {
                $foundMatch = true;
            }
            
            // If no match in order data, search in customer data from payment
            if (!$foundMatch && $payment) {
                $customerName = strtolower($payment['payment_details']['full_name'] ?? $payment['user_name'] ?? '');
                $customerEmail = strtolower($payment['payment_details']['email'] ?? $payment['user_email'] ?? '');
                $customerPhone = strtolower($payment['payment_details']['phone_number'] ?? $payment['phone_number'] ?? '');
                
                if (strpos($customerName, $searchLower) !== false ||
                    strpos($customerEmail, $searchLower) !== false ||
                    strpos($customerPhone, $searchLower) !== false) {
                    $foundMatch = true;
                }
            }
            
            if (!$foundMatch) {
                $includeOrder = false;
            }
        }
        
        if ($includeOrder) {
            $filteredOrders[] = $order;
        }
    }
    $orders = $filteredOrders;
}


$totalOrders = count($orders);



// Calculate statistics
$totalRevenue = 0;
$pendingOrders = 0;
$completedOrders = 0;
$cancelledOrders = 0;

foreach ($orders as $order) {
    $totalRevenue += $order['total_amount'] ?? 0;
    switch ($order['status']) {
        case 'pending':
            $pendingOrders++;
            break;
        case 'completed':
            $completedOrders++;
            break;
        case 'cancelled':
            $cancelledOrders++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Glamour Admin</title>
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
            overflow-y: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #3E2723;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title i {
            color: #FF6B9D;
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(62, 39, 35, 0.15);
        }

        .stat-card.revenue {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
        }

        .stat-card.pending {
            background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);
            color: white;
        }

        .stat-card.completed {
            background: linear-gradient(135deg, #2196F3 0%, #42A5F5 100%);
            color: white;
        }

        .stat-card.cancelled {
            background: linear-gradient(135deg, #F44336 0%, #EF5350 100%);
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        /* Filters Section */
        .filters-section {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }

        .filters-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #3E2723;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #5D4037;
        }

        .filter-input, .filter-select {
            padding: 12px 15px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 12px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #FF6B9D;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .filter-input::placeholder {
            color: #8D6E63;
            opacity: 0.7;
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 12px;
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
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #5D4037;
            border: 2px solid rgba(62, 39, 35, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 1);
            border-color: #FF6B9D;
        }

                 /* Orders Table */
         .orders-container {
             background: rgba(255, 255, 255, 0.9);
             border-radius: 20px;
             box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
             backdrop-filter: blur(10px);
             overflow: hidden;
             max-width: 100%;
         }

        .orders-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .orders-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #3E2723;
        }

        .orders-count {
            font-size: 0.9rem;
            color: #8D6E63;
            background: rgba(255, 107, 157, 0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

                 .table-container {
             max-width: 100%;
         }
         
         .orders-table {
             width: 100%;
             border-collapse: collapse;
             table-layout: fixed;
         }

                 .orders-table th {
             background: rgba(255, 107, 157, 0.05);
             padding: 8px 4px;
             text-align: left;
             font-weight: 600;
             color: #5D4037;
             border-bottom: 1px solid rgba(62, 39, 35, 0.1);
             white-space: nowrap;
         }

                                            .orders-table td {
             padding: 8px 4px;
             border-bottom: 1px solid rgba(62, 39, 35, 0.05);
             vertical-align: top;
             word-wrap: break-word;
             max-width: 0;
         }

        .orders-table tr:hover {
            background: rgba(255, 107, 157, 0.02);
        }

        .order-id {
            font-weight: 600;
            color: #FF6B9D;
            font-family: 'Courier New', monospace;
        }

                 .customer-info {
             display: flex;
             flex-direction: column;
             gap: 2px;
         }

                 .customer-name {
             font-weight: 600;
             color: #3E2723;
             font-size: 0.85rem;
         }

         .customer-email {
             font-size: 0.75rem;
             color: #8D6E63;
         }

         .customer-phone {
             font-size: 0.75rem;
             color: #8D6E63;
         }

                                            .order-items {
             width: 100%;
             max-width: 180px;
         }

                 .order-item {
             display: flex;
             align-items: center;
             gap: 6px;
             padding: 4px 0;
             border-bottom: 1px solid rgba(62, 39, 35, 0.05);
             width: 100%;
         }

        .order-item:last-child {
            border-bottom: none;
        }

                 .item-image {
             width: 35px;
             height: 35px;
             border-radius: 6px;
             object-fit: cover;
             border: 1px solid rgba(62, 39, 35, 0.1);
             background-color: #f5f5f5;
             display: block;
             flex-shrink: 0;
             transition: transform 0.2s ease;
         }
         
         .item-image:hover {
             transform: scale(1.1);
         }

                 .item-details {
             flex: 1;
             min-width: 0;
             overflow: hidden;
             display: flex;
             flex-direction: column;
             gap: 2px;
             width: 100%;
         }

                                            .item-name {
             font-weight: 500;
             color: #3E2723;
             font-size: 0.7rem;
             line-height: 1.1;
             word-wrap: break-word;
             overflow-wrap: break-word;
             width: 100%;
         }

                                            .item-price {
             font-size: 0.65rem;
             color: #8D6E63;
             word-wrap: break-word;
             overflow-wrap: break-word;
         }

                 .order-total {
             font-weight: 600;
             color: #3E2723;
             font-size: 0.9rem;
         }

                 .status-badge {
             padding: 4px 8px;
             border-radius: 12px;
             font-size: 0.7rem;
             font-weight: 600;
             text-transform: uppercase;
             letter-spacing: 0.3px;
         }

        .status-pending {
            background: rgba(255, 152, 0, 0.1);
            color: #F57C00;
        }

        .status-processing {
            background: rgba(33, 150, 243, 0.1);
            color: #1976D2;
        }

        .status-completed {
            background: rgba(76, 175, 80, 0.1);
            color: #388E3C;
        }

        .status-cancelled {
            background: rgba(244, 67, 54, 0.1);
            color: #D32F2F;
        }

                 .payment-method {
             display: flex;
             align-items: center;
             gap: 4px;
             font-weight: 500;
             font-size: 0.8rem;
         }

                 .payment-icon {
             width: 16px;
             height: 16px;
             border-radius: 3px;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 0.7rem;
             color: white;
         }

        .payment-waafi {
            background: #0066cc;
        }

        .payment-card {
            background: #6f42c1;
        }

        .payment-bank {
            background: #17a2b8;
        }

                 .order-actions {
             display: flex;
             gap: 6px;
             flex-wrap: wrap;
             justify-content: center;
         }

                 .action-btn {
             padding: 8px;
             border: none;
             border-radius: 6px;
             font-size: 0.9rem;
             cursor: pointer;
             transition: all 0.3s ease;
             text-decoration: none;
             display: inline-flex;
             align-items: center;
             justify-content: center;
             min-width: 32px;
             height: 32px;
             white-space: nowrap;
             position: relative;
         }

        .action-btn.view {
            background: rgba(33, 150, 243, 0.1);
            color: #1976D2;
        }

        .action-btn.view:hover {
            background: rgba(33, 150, 243, 0.2);
        }

        .action-btn.edit {
            background: rgba(255, 152, 0, 0.1);
            color: #F57C00;
        }

        .action-btn.edit:hover {
            background: rgba(255, 152, 0, 0.2);
        }

        .action-btn.delete {
            background: rgba(244, 67, 54, 0.1);
            color: #D32F2F;
        }

                 .action-btn.delete:hover {
             background: rgba(244, 67, 54, 0.2);
         }
         
         /* Bulk Actions Styles */
         .bulk-actions {
             background: rgba(255, 107, 157, 0.1);
             border: 1px solid rgba(255, 107, 157, 0.2);
             border-radius: 12px;
             padding: 15px 20px;
             margin-bottom: 15px;
             display: flex;
             justify-content: space-between;
             align-items: center;
         }
         
         .bulk-actions-content {
             display: flex;
             justify-content: space-between;
             align-items: center;
             width: 100%;
         }
         
         .selected-count {
             font-weight: 600;
             color: #3E2723;
             font-size: 0.9rem;
         }
         
         .bulk-buttons {
             display: flex;
             gap: 10px;
         }
         
         .btn-danger {
             background: linear-gradient(135deg, #F44336 0%, #EF5350 100%);
             color: white;
             box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
         }
         
         .btn-danger:hover {
             transform: translateY(-2px);
             box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
         }
         
         /* Checkbox Styles */
         .order-checkbox {
             width: 18px;
             height: 18px;
             cursor: pointer;
             accent-color: #FF6B9D;
         }
         
         .order-checkbox:checked {
             background-color: #FF6B9D;
             border-color: #FF6B9D;
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
            background: rgba(255, 255, 255, 0.95);
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3E2723;
        }

        .close {
            color: #8D6E63;
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

        .order-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-section {
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(62, 39, 35, 0.1);
        }

        .detail-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(62, 39, 35, 0.05);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #5D4037;
        }

        .detail-value {
            color: #3E2723;
            font-weight: 600;
        }

        /* Message Styles */
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #388E3C;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .message.error {
            background: rgba(244, 67, 54, 0.1);
            color: #D32F2F;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

                 /* Responsive Design */
         @media (max-width: 1200px) {
             .order-items {
                 max-width: 150px;
             }
             
             .action-btn {
                 padding: 3px 4px;
                 font-size: 0.65rem;
             }
         }
         
         @media (max-width: 768px) {
             .sidebar {
                 transform: translateX(-100%);
                 transition: transform 0.3s ease;
             }

             .sidebar.active {
                 transform: translateX(0);
             }

             .main-content {
                 margin-left: 0;
                 padding: 15px;
             }

             .stats-grid {
                 grid-template-columns: 1fr;
             }

             .filters-grid {
                 grid-template-columns: 1fr;
             }

             .order-details-grid {
                 grid-template-columns: 1fr;
             }

             .orders-table {
                 font-size: 0.7rem;
             }

             .orders-table th,
             .orders-table td {
                 padding: 4px 2px;
             }
             
             .order-items {
                 width: 100%;
                 max-width: 120px;
             }
             
             .item-name {
                 font-size: 0.6rem;
                 width: 100%;
             }
             
             .order-actions {
                 flex-direction: column;
                 gap: 1px;
             }
             
             .action-btn {
                 padding: 3px 4px;
                 font-size: 0.6rem;
             }
             
             .page-title {
                 font-size: 1.8rem;
             }
             
             .filters-section {
                 padding: 20px;
             }
         }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>

    <div class="main-content">
                 <div class="page-header">
             <h1 class="page-title">
                 <i class="fas fa-shopping-cart"></i>
                 Manage Orders
             </h1>
         </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>



        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number">$<?php echo number_format($totalRevenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $pendingOrders; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $completedOrders; ?></div>
                <div class="stat-label">Completed Orders</div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-number"><?php echo $cancelledOrders; ?></div>
                <div class="stat-label">Cancelled Orders</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <h3 class="filters-title">
                <i class="fas fa-filter"></i>
                Filter Orders
            </h3>
            <form method="GET" action="">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" name="search" class="filter-input" placeholder="Search by order number, customer name, email, or phone" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Payment Method</label>
                        <select name="payment_method" class="filter-select">
                            <option value="">All Methods</option>
                            <option value="waafi" <?php echo $paymentFilter === 'waafi' ? 'selected' : ''; ?>>Waafi</option>
                            <option value="card" <?php echo $paymentFilter === 'card' ? 'selected' : ''; ?>>Card</option>
                            <option value="bank" <?php echo $paymentFilter === 'bank' ? 'selected' : ''; ?>>Bank Transfer</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date</label>
                        <input type="date" name="date" class="filter-input" value="<?php echo $dateFilter; ?>">
                    </div>
                    <div class="filter-group filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="manage-orders.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="orders-container">
            <div class="orders-header">
                <h3 class="orders-title">
                    <i class="fas fa-list"></i>
                    All Orders
                </h3>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                    <span class="orders-count"><?php echo $totalOrders; ?> orders</span>
                    <?php if ($searchQuery || $statusFilter || $paymentFilter || $dateFilter): ?>
                        <small style="color: #8D6E63; font-size: 0.8rem;">
                            <?php 
                            $activeFilters = [];
                            if ($searchQuery) $activeFilters[] = "Search: '$searchQuery'";
                            if ($statusFilter) $activeFilters[] = "Status: " . ucfirst($statusFilter);
                            if ($paymentFilter) $activeFilters[] = "Payment: " . ucfirst($paymentFilter);
                            if ($dateFilter) $activeFilters[] = "Date: " . date('M j, Y', strtotime($dateFilter));
                            echo implode(', ', $activeFilters);
                            ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
            
                         <div class="table-container">
                 <!-- Bulk Actions -->
                 <div class="bulk-actions" id="bulkActions" style="display: none;">
                     <div class="bulk-actions-content">
                         <span class="selected-count" id="selectedCount">0 orders selected</span>
                         <div class="bulk-buttons">
                             <button class="btn btn-danger" onclick="deleteSelectedOrders()">
                                 <i class="fas fa-trash"></i>
                                 Delete Selected
                             </button>
                             <button class="btn btn-secondary" onclick="clearSelection()">
                                 <i class="fas fa-times"></i>
                                 Clear Selection
                             </button>
                         </div>
                     </div>
                 </div>
                 
                                   <table class="orders-table">
                      <thead>
                          <tr>
                              <th style="width: 50px;">
                                  <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                              </th>
                              <th>Order ID</th>
                              <th>Customer</th>
                              <th>Items</th>
                              <th>Total</th>
                              <th>Status</th>
                              <th>Payment</th>
                              <th>Date</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                    <tbody>
                                                 <?php if (empty($orders)): ?>
                         <tr>
                             <td colspan="9" style="text-align: center; padding: 40px; color: #8D6E63;">
                                 <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                 <?php if ($searchQuery || $statusFilter || $paymentFilter || $dateFilter): ?>
                                     No orders found matching your filters
                                     <br><small style="margin-top: 10px; display: block;">
                                         <a href="manage-orders.php" style="color: #FF6B9D; text-decoration: none;">Clear all filters</a>
                                     </small>
                                 <?php else: ?>
                                     No orders found
                                 <?php endif; ?>
                             </td>
                         </tr>
                        <?php else: ?>
                                                 <?php foreach ($orders as $order): ?>
                         <tr>
                             <td>
                                 <input type="checkbox" class="order-checkbox" value="<?php echo $order['_id']; ?>" onchange="updateBulkActions()">
                             </td>
                             <td>
                                <span class="order-id">#<?php echo substr($order['_id'], -8); ?></span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <?php 
                                    // Get customer information from payment details
                                    $payment = $paymentModel->getByOrderId($order['_id'])[0] ?? null;
                                    $customerName = 'N/A';
                                    $customerEmail = 'N/A';
                                    $customerPhone = 'N/A';
                                    

                                    
                                    if ($payment) {
                                        if (isset($payment['payment_details']['full_name']) && !empty($payment['payment_details']['full_name'])) {
                                            $customerName = $payment['payment_details']['full_name'];
                                        } elseif (isset($payment['user_name']) && !empty($payment['user_name'])) {
                                            $customerName = $payment['user_name'];
                                        }
                                        
                                        if (isset($payment['payment_details']['email']) && !empty($payment['payment_details']['email'])) {
                                            $customerEmail = $payment['payment_details']['email'];
                                        } elseif (isset($payment['user_email']) && !empty($payment['user_email'])) {
                                            $customerEmail = $payment['user_email'];
                                        }
                                        
                                        if (isset($payment['payment_details']['phone_number']) && !empty($payment['payment_details']['phone_number'])) {
                                            $customerPhone = $payment['payment_details']['phone_number'];
                                        } elseif (isset($payment['phone_number']) && !empty($payment['phone_number'])) {
                                            $customerPhone = $payment['phone_number'];
                                        }
                                    } else {
                                        // If no payment found, try to get customer info from User model
                                        $customerName = 'Customer (ID: ' . substr($order['user_id'], -6) . ')';
                                        $customerEmail = 'No payment info';
                                        $customerPhone = 'No payment info';
                                        
                                        // Try to get user information from database
                                        if (!empty($order['user_id'])) {
                                            try {
                                                $user = $userModel->getById($order['user_id']);
                                                if ($user) {
                                                    if (!empty($user['username'])) {
                                                        $customerName = $user['username'];
                                                    } elseif (!empty($user['email'])) {
                                                        $customerName = $user['email'];
                                                    }
                                                    
                                                    if (!empty($user['email'])) {
                                                        $customerEmail = $user['email'];
                                                    }
                                                    
                                                    if (!empty($user['contact_number'])) {
                                                        $customerPhone = $user['contact_number'];
                                                    } elseif (!empty($user['phone'])) {
                                                        $customerPhone = $user['phone'];
                                                    }
                                                } else {
                                                    // User not found in database
                                                    $customerName = 'Guest Customer (ID: ' . substr($order['user_id'], -6) . ')';
                                                    $customerEmail = 'Guest order - no account';
                                                    $customerPhone = 'Guest order - no account';
                                                }
                                            } catch (Exception $e) {
                                                // If user lookup fails, show guest customer info
                                                $customerName = 'Guest Customer (ID: ' . substr($order['user_id'], -6) . ')';
                                                $customerEmail = 'Guest order - lookup failed';
                                                $customerPhone = 'Guest order - lookup failed';
                                            }
                                        }
                                        
                                        // Add a note about the order status
                                        if ($order['status'] === 'pending') {
                                            $customerName .= ' (Pending Payment)';
                                        }
                                    }
                                    ?>
                                    <div class="customer-name"><?php echo htmlspecialchars($customerName); ?></div>
                                    <div class="customer-email"><?php echo htmlspecialchars($customerEmail); ?></div>
                                    <div class="customer-phone"><?php echo htmlspecialchars($customerPhone); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="order-items">
                                    <?php 
                                    $orderItems = $order['items'] ?? [];
                                    

                                    
                                    // Convert to array safely
                                    if (is_object($orderItems) && method_exists($orderItems, 'toArray')) {
                                        $orderItems = $orderItems->toArray();
                                    } elseif (!is_array($orderItems)) {
                                        $orderItems = [];
                                    }
                                    
                                    foreach (array_slice($orderItems, 0, 3) as $item): 

                        
                                        
                                        // Try to get product from database first
                                        $product = null;
                                        if (isset($item['product_id']) && !empty($item['product_id'])) {
                                            $product = $productModel->getById($item['product_id']);
                                        }
                                        
                                        // If product not found in database, use item data as fallback
                                        if (!$product) {

                                            // Create a fallback product using item data
                                            $product = [
                                                'name' => $item['name'] ?? $item['product_name'] ?? 'Unknown Product',
                                                'image' => $item['image'] ?? null,
                                                'front_image' => $item['front_image'] ?? null,
                                                'images' => $item['images'] ?? []
                                            ];
                                        }
                                        
                                        // Ensure we have a product name
                                        $productName = $product['name'] ?? $item['name'] ?? $item['product_name'] ?? 'Unknown Product';
                                        $itemQuantity = $item['quantity'] ?? 1;
                                        $itemPrice = $item['price'] ?? 0;
                                    ?>
                                    <div class="order-item">
                                        <?php 
                                        // Use the same image logic as cart.php
                                        $imageSrc = '../img/women/1.jpg'; // Default fallback
                                        
                                        if ($product) {
                                            // Try the most common image fields first
                                            $productImage = '';
                                            if (!empty($product['front_image'])) {
                                                $productImage = $product['front_image'];
                                            } elseif (!empty($product['image_front'])) {
                                                $productImage = $product['image_front'];
                                            } elseif (!empty($product['image'])) {
                                                $productImage = $product['image'];
                                            } elseif (isset($product['images']) && is_array($product['images'])) {
                                                if (isset($product['images']['front'])) {
                                                    $productImage = $product['images']['front'];
                                                } elseif (!empty($product['images'])) {
                                                    $productImage = $product['images'][0];
                                                }
                                            }
                                            
                                            if (!empty($productImage)) {
                                                // Normalize the image path
                                                $productImage = ltrim($productImage, '/.');
                                                
                                                // If path already starts with ../, remove it first
                                                if (strpos($productImage, '../') === 0) {
                                                    $productImage = substr($productImage, 3);
                                                }
                                                
                                                // If path doesn't start with 'uploads/' or 'img/', add appropriate prefix
                                                if (strpos($productImage, 'uploads/') !== 0 && strpos($productImage, 'img/') !== 0) {
                                                    // Check if it's an uploaded product image
                                                    if (strpos($productImage, 'products/') !== false) {
                                                        $productImage = 'uploads/' . $productImage;
                                                    } else {
                                                        $productImage = 'img/' . $productImage;
                                                    }
                                                }
                                                
                                                // Add '../' prefix for admin directory
                                                $imageSrc = '../' . $productImage;
                                                
                                                // Check if the image file exists, if not use simple fallback
                                                if (!file_exists($imageSrc)) {
                                                    $imageSrc = '../img/women/1.jpg';
                                                }
                                            }
                                        }
                                        ?>
                                        <img src="<?php echo $imageSrc; ?>" 
                                             alt="<?php echo htmlspecialchars($productName); ?>" 
                                             class="item-image" 
                                             onerror="this.onerror=null; this.src='../img/women/1.jpg';"
                                             onload="this.style.opacity='1';"
                                             style="opacity: 0; transition: opacity 0.3s ease;">
                                        <div class="item-details">
                                            <div class="item-name"><?php echo htmlspecialchars($productName); ?></div>
                                            <div class="item-price">Qty: <?php echo $itemQuantity; ?> × $<?php echo number_format($itemPrice, 2); ?></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (count($orderItems) > 3): ?>
                                    <div style="font-size: 0.8rem; color: #8D6E63; margin-top: 5px;">
                                        +<?php echo count($orderItems) - 3; ?> more items
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="order-total">$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status'] ?? 'pending'; ?>">
                                    <?php echo ucfirst($order['status'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $payment = $paymentModel->getByOrderId($order['_id'])[0] ?? null;
                                if ($payment): 
                                ?>
                                <div class="payment-method">
                                    <div class="payment-icon payment-<?php echo $payment['payment_method']; ?>">
                                        <i class="fas fa-<?php echo $payment['payment_method'] === 'waafi' ? 'mobile-alt' : ($payment['payment_method'] === 'card' ? 'credit-card' : 'university'); ?>"></i>
                                    </div>
                                    <?php echo ucfirst($payment['payment_method']); ?>
                                </div>
                                <?php else: ?>
                                <span style="color: #8D6E63;">No payment</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($order['created_at'] ?? 'now')); ?>
                            </td>
                            <td>
                                <div class="order-actions">
                                    <button class="action-btn view" onclick="viewOrder('<?php echo $order['_id']; ?>')" title="View Order Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn edit" onclick="editOrderStatus('<?php echo $order['_id']; ?>', '<?php echo $order['status'] ?? 'pending'; ?>')" title="Update Order Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteOrder('<?php echo $order['_id']; ?>')" title="Delete Order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Order Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="orderModalBody">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Update Order Status</h2>
                <span class="close" onclick="closeStatusModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="statusOrderId">
                    
                    <div class="filter-group">
                        <label class="filter-label">New Status</label>
                        <select name="new_status" class="filter-select" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Status
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // View Order Details
        function viewOrder(orderId) {
            // Redirect to order details page
            window.location.href = 'order-details.php?id=' + orderId;
        }

        // Edit Order Status
        function editOrderStatus(orderId, currentStatus) {
            document.getElementById('statusOrderId').value = orderId;
            document.querySelector('select[name="new_status"]').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
        }

        // Delete Order
        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="order_id" value="${orderId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close Modals
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const orderModal = document.getElementById('orderModal');
            const statusModal = document.getElementById('statusModal');
            
            if (event.target === orderModal) {
                orderModal.style.display = 'none';
            }
            if (event.target === statusModal) {
                statusModal.style.display = 'none';
            }
        }

                 // Bulk Actions Functions
         function toggleSelectAll() {
             const selectAllCheckbox = document.getElementById('selectAll');
             const orderCheckboxes = document.querySelectorAll('.order-checkbox');
             
             orderCheckboxes.forEach(checkbox => {
                 checkbox.checked = selectAllCheckbox.checked;
             });
             
             updateBulkActions();
         }
         
         function updateBulkActions() {
             const orderCheckboxes = document.querySelectorAll('.order-checkbox');
             const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
             const bulkActions = document.getElementById('bulkActions');
             const selectedCount = document.getElementById('selectedCount');
             const selectAllCheckbox = document.getElementById('selectAll');
             
             const selectedCountValue = selectedCheckboxes.length;
             
             if (selectedCountValue > 0) {
                 bulkActions.style.display = 'flex';
                 selectedCount.textContent = selectedCountValue + ' order' + (selectedCountValue === 1 ? '' : 's') + ' selected';
             } else {
                 bulkActions.style.display = 'none';
             }
             
             // Update select all checkbox state
             if (selectedCountValue === 0) {
                 selectAllCheckbox.checked = false;
                 selectAllCheckbox.indeterminate = false;
             } else if (selectedCountValue === orderCheckboxes.length) {
                 selectAllCheckbox.checked = true;
                 selectAllCheckbox.indeterminate = false;
             } else {
                 selectAllCheckbox.checked = false;
                 selectAllCheckbox.indeterminate = true;
             }
         }
         
         function clearSelection() {
             const orderCheckboxes = document.querySelectorAll('.order-checkbox');
             const selectAllCheckbox = document.getElementById('selectAll');
             
             orderCheckboxes.forEach(checkbox => {
                 checkbox.checked = false;
             });
             
             selectAllCheckbox.checked = false;
             selectAllCheckbox.indeterminate = false;
             
             updateBulkActions();
         }
         
         function deleteSelectedOrders() {
             const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
             const selectedOrderIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
             
             if (selectedOrderIds.length === 0) {
                 alert('Please select at least one order to delete.');
                 return;
             }
             
             const confirmMessage = `Are you sure you want to delete ${selectedOrderIds.length} order${selectedOrderIds.length === 1 ? '' : 's'}? This action cannot be undone.`;
             
             if (confirm(confirmMessage)) {
                 // Create form to submit multiple order IDs
                 const form = document.createElement('form');
                 form.method = 'POST';
                 form.innerHTML = `
                     <input type="hidden" name="action" value="bulk_delete">
                     <input type="hidden" name="order_ids" value="${selectedOrderIds.join(',')}">
                 `;
                 document.body.appendChild(form);
                 form.submit();
             }
         }
         
         // Auto-refresh page after status update
         <?php if ($message && $messageType === 'success'): ?>
         setTimeout(function() {
             window.location.href = 'manage-orders.php';
         }, 2000);
         <?php endif; ?>
    </script>
    
    <!-- Sidebar JavaScript -->
    <script src="includes/admin-sidebar.js"></script>
</body>
</html>
