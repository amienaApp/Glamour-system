<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Order.php';
require_once '../models/Payment.php';
require_once '../models/Product.php';

$orderModel = new Order();
$paymentModel = new Payment();
$productModel = new Product();

$orderId = $_GET['id'] ?? '';
$message = '';
$messageType = '';

if (!$orderId) {
    header('Location: manage-orders.php');
    exit;
}

// Get order details
$order = $orderModel->getOrderById($orderId);
if (!$order) {
    header('Location: manage-orders.php');
    exit;
}

// Get payment details
$payments = $paymentModel->getByOrderId($orderId);
$payment = $payments[0] ?? null;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $newStatus = $_POST['new_status'] ?? '';
    
    if ($orderModel->updateStatus($orderId, $newStatus)) {
        $message = 'Order status updated successfully!';
        $messageType = 'success';
        // Refresh order data
        $order = $orderModel->getOrderById($orderId);
    } else {
        $message = 'Failed to update order status.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Glamour Admin</title>
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

        /* Order Details Grid */
        .order-details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .order-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .section-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            background: rgba(255, 107, 157, 0.05);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #3E2723;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #FF6B9D;
        }

        .section-content {
            padding: 30px;
        }

        /* Order Info */
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #8D6E63;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3E2723;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            color: #FF6B9D;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            width: fit-content;
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

        /* Order Items */
        .order-items {
            margin-bottom: 30px;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            border: 1px solid rgba(62, 39, 35, 0.1);
            border-radius: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.5);
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(62, 39, 35, 0.1);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 5px;
        }

        .item-category {
            font-size: 0.9rem;
            color: #8D6E63;
            margin-bottom: 8px;
        }

        .item-price {
            font-size: 1rem;
            color: #5D4037;
            font-weight: 500;
        }

        .item-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #FF6B9D;
        }

        /* Customer Details */
        .customer-details {
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(62, 39, 35, 0.05);
        }

        .detail-row:last-child {
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

        .detail-value.pending-payment {
            color: #8D6E63;
            font-style: italic;
        }

        /* Payment Details */
        .payment-details {
            margin-bottom: 30px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(62, 39, 35, 0.1);
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
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

        .payment-info {
            flex: 1;
        }

        .payment-method-name {
            font-weight: 600;
            color: #3E2723;
            margin-bottom: 5px;
        }

        .payment-status {
            font-size: 0.9rem;
            color: #8D6E63;
        }

        /* Order Summary */
        .order-summary {
            background: rgba(255, 107, 157, 0.05);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(255, 107, 157, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(62, 39, 35, 0.05);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: #FF6B9D;
        }

        /* Status Update Form */
        .status-update {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(62, 39, 35, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #5D4037;
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(62, 39, 35, 0.1);
            border-radius: 12px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: #FF6B9D;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        /* Responsive Design */
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
            }

            .order-details-grid {
                grid-template-columns: 1fr;
            }

            .order-info {
                grid-template-columns: 1fr;
            }

            .item {
                flex-direction: column;
                text-align: center;
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
                Order Details
            </h1>
            <div class="header-actions">
                <a href="manage-orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Orders
                </a>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="order-details-grid">
            <!-- Main Order Information -->
            <div class="order-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Order Information
                    </h2>
                </div>
                <div class="section-content">
                    <!-- Order Info -->
                    <div class="order-info">
                        <div class="info-item">
                            <div class="info-label">Order ID</div>
                            <div class="info-value order-id">#<?php echo substr($order['_id'], -8); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Order Number</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['order_number'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-<?php echo $order['status'] ?? 'pending'; ?>">
                                    <?php echo ucfirst($order['status'] ?? 'pending'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Date Created</div>
                            <div class="info-value"><?php echo date('M j, Y g:i A', strtotime($order['created_at'] ?? 'now')); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value"><?php echo date('M j, Y g:i A', strtotime($order['updated_at'] ?? 'now')); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total Amount</div>
                            <div class="info-value">$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items">
                        <h3 style="margin-bottom: 20px; color: #3E2723; font-size: 1.2rem;">
                            <i class="fas fa-box"></i>
                            Order Items (<?php echo count($order['items'] ?? []); ?>)
                        </h3>
                        
                        <?php if (!empty($order['items'])): ?>
                            <?php foreach ($order['items'] as $item): ?>
                                <?php 
                                $product = $productModel->getById($item['product_id']); 
                                
                                // Create fallback if product not found
                                if (!$product) {
                    
                                    $product = [
                                        'name' => $item['name'] ?? 'Unknown Product',
                                        'category' => 'Unknown Category',
                                        'image' => null,
                                        'front_image' => null,
                                        'images' => []
                                    ];
                                }
                                ?>
                                <div class="item">
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
                                             alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>" 
                                             class="item-image" 
                                             onerror="this.onerror=null; this.src='../img/women/1.jpg';"
                                             onload="this.style.opacity='1';"
                                             style="opacity: 0; transition: opacity 0.3s ease;">
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></div>
                                        <div class="item-category"><?php echo htmlspecialchars($product['category'] ?? 'Category'); ?></div>
                                        <div class="item-price">$<?php echo number_format($item['price'] ?? 0, 2); ?> each</div>
                                    </div>
                                    <div class="item-total">
                                        $<?php echo number_format(($item['price'] ?? 0) * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: #8D6E63; padding: 20px;">No items found</p>
                        <?php endif; ?>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h3 style="margin-bottom: 20px; color: #3E2723; font-size: 1.2rem;">
                            <i class="fas fa-calculator"></i>
                            Order Summary
                        </h3>
                        
                        <div class="summary-row">
                            <span>Items Count:</span>
                            <span><?php echo $order['item_count'] ?? count($order['items'] ?? []); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row">
                            <span>Total:</span>
                            <span>$<?php echo number_format($order['total_amount'] ?? 0, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div>
                <!-- Customer Details -->
                <div class="order-section" style="margin-bottom: 30px;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-user"></i>
                            Customer Details
                        </h2>
                    </div>
                    <div class="section-content">
                        <div class="customer-details">
                            <?php if (!$payment): ?>
                            <div style="background: rgba(255, 152, 0, 0.1); border: 1px solid rgba(255, 152, 0, 0.3); color: #F57C00; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Customer information will be available once payment is processed.
                            </div>
                            <?php endif; ?>
                            <?php 
                            // Get customer information from payment details
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
                                // If no payment found, show order information
                                $customerName = 'Customer (ID: ' . substr($order['user_id'], -6) . ')';
                                $customerEmail = 'Payment pending';
                                $customerPhone = 'Payment pending';
                                
                                // Add a note about the order status
                                if ($order['status'] === 'pending') {
                                    $customerName .= ' (Pending Payment)';
                                }
                            }
                            
                            // Add CSS class for styling based on data source
                            $customerNameClass = $payment ? 'detail-value' : 'detail-value pending-payment';
                            $customerEmailClass = $payment ? 'detail-value' : 'detail-value pending-payment';
                            $customerPhoneClass = $payment ? 'detail-value' : 'detail-value pending-payment';
                            ?>
                            <div class="detail-row">
                                <span class="detail-label">Full Name:</span>
                                <span class="<?php echo $customerNameClass; ?>"><?php echo htmlspecialchars($customerName); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="<?php echo $customerEmailClass; ?>"><?php echo htmlspecialchars($customerEmail); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone:</span>
                                <span class="<?php echo $customerPhoneClass; ?>"><?php echo htmlspecialchars($customerPhone); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Shipping Address:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['shipping_address'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Billing Address:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['billing_address'] ?? 'N/A'); ?></span>
                            </div>
                            <?php if (!empty($order['notes'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Order Notes:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['notes']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="order-section" style="margin-bottom: 30px;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Details
                        </h2>
                    </div>
                    <div class="section-content">
                        <?php if ($payment): ?>
                            <div class="payment-details">
                                <div class="payment-method">
                                    <div class="payment-icon payment-<?php echo $payment['payment_method']; ?>">
                                        <i class="fas fa-<?php echo $payment['payment_method'] === 'waafi' ? 'mobile-alt' : ($payment['payment_method'] === 'card' ? 'credit-card' : 'university'); ?>"></i>
                                    </div>
                                    <div class="payment-info">
                                        <div class="payment-method-name"><?php echo ucfirst($payment['payment_method']); ?></div>
                                        <div class="payment-status"><?php echo ucfirst($payment['status'] ?? 'pending'); ?></div>
                                    </div>
                                </div>
                                
                                <?php if (isset($payment['transaction_id'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Transaction ID:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($payment['transaction_id']); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (isset($payment['mobile_service'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Mobile Service:</span>
                                    <span class="detail-value"><?php echo ucfirst($payment['mobile_service']); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Amount:</span>
                                    <span class="detail-value">$<?php echo number_format($payment['amount'] ?? 0, 2); ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Payment Date:</span>
                                    <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($payment['created_at'] ?? 'now')); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; color: #8D6E63; padding: 20px;">No payment information available</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Update -->
                <div class="status-update">
                    <h3 style="margin-bottom: 20px; color: #3E2723; font-size: 1.2rem;">
                        <i class="fas fa-edit"></i>
                        Update Order Status
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        
                        <div class="form-group">
                            <label class="form-label">New Status</label>
                            <select name="new_status" class="form-select" required>
                                <option value="pending" <?php echo ($order['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo ($order['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo ($order['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($order['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-save"></i>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page after status update
        <?php if ($message && $messageType === 'success'): ?>
        setTimeout(function() {
            window.location.href = 'order-details.php?id=<?php echo $orderId; ?>';
        }, 2000);
        <?php endif; ?>
    </script>
    
    <!-- Sidebar JavaScript -->
    <script src="includes/admin-sidebar.js"></script>
</body>
</html>
