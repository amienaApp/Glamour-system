
<?php
/**
 * Orders Page
 * Displays user orders and their status
 */

session_start();

require_once 'models/Order.php';
require_once 'models/Payment.php';
require_once 'models/Product.php';
require_once 'models/Cart.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: womenF/login.php');
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];
$orderModel = new Order();
$paymentModel = new Payment();
$cartModel = new Cart();

$orders = $orderModel->getUserOrders($userId);

// Check if user was redirected after payment (using session variable)
$paymentSuccess = isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true;

// Clear cart if payment was successful
if ($paymentSuccess) {
    $cartCleared = $cartModel->clearCart($userId);
    if ($cartCleared) {
        // Log successful cart clearing
        // Cart cleared successfully
    } else {
        // Log failed cart clearing
        // Failed to clear cart
    }
    
    // Clear the payment success flag from session after processing
    unset($_SESSION['payment_success']);
    unset($_SESSION['payment_success_time']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Glamour System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .header h1 {
            color: #0066cc;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: white;
            color: #0066cc;
            border: 2px solid #0066cc;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #0066cc;
            color: white;
            transform: translateY(-2px);
        }

        .orders-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .order-card {
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.1);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e1e8ed;
        }

        .order-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0066cc;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .order-info h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .order-info p {
            color: #666;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .order-items {
            margin-top: 20px;
        }

        .order-items h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #fafafa;
        }

        .item:last-child {
            margin-bottom: 0;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-category {
            display: block;
            font-size: 0.8rem;
            color: #666;
            font-weight: 400;
        }

        .item-quantity {
            font-size: 0.9rem;
            color: #666;
        }

        .item-price {
            color: #0066cc;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .order-total {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e1e8ed;
        }

        .total-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0066cc;
        }

        .no-orders {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-orders i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-orders h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .shop-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 102, 204, 0.3);
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-details {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .item {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .item-image {
                width: 80px;
                height: 80px;
            }

            .item-details {
                text-align: center;
            }

            .item-price {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header removed as requested -->
    
    <div class="container">
                        <a href="womenF/women.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="header">
            <h1>My Orders</h1>
            <p>Track your order status and history</p>
        </div>


        <div class="orders-container">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="womenF/women.php" class="shop-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-number"><?php echo htmlspecialchars($order['order_number'] ?? 'Order #' . substr($order['_id'], -6)); ?></div>
                                <div class="order-date"><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="order-info">
                                <h4><i class="fas fa-map-marker-alt"></i> Shipping Address</h4>
                                <p><?php echo htmlspecialchars($order['shipping_address'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="order-info">
                                <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                                <p><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'Not specified')); ?></p>
                            </div>
                        </div>

                        <?php 
                        // Safely convert items to array if needed
                        $orderItems = $order['items'] ?? [];
                        if (is_object($orderItems) && method_exists($orderItems, 'toArray')) {
                            $orderItems = $orderItems->toArray();
                        } elseif (!is_array($orderItems)) {
                            $orderItems = [];
                        }
                        
                        if (!empty($orderItems)): ?>
                            <div class="order-items">
                                <h4><i class="fas fa-box"></i> Order Items</h4>
                                <?php foreach ($orderItems as $item): ?>
                                    <?php 
                                    // Get product information
                                    $productName = 'Unknown Product';
                                    $productImage = 'img/download.webp'; // Default fallback
                                    $productCategory = 'Unknown Category';
                                    $price = 0;
                                    
                                    if (isset($item['product'])) {
                                        $product = $item['product'];
                                        $productName = $product['name'] ?? 'Unknown Product';
                                        $productCategory = $product['category'] ?? 'Unknown Category';
                                        $price = $product['price'] ?? 0;
                                        
                                        // Get product image
                                        if (isset($product['front_image']) && !empty($product['front_image'])) {
                                            $productImage = $product['front_image'];
                                        } elseif (isset($product['image_front']) && !empty($product['image_front'])) {
                                            $productImage = $product['image_front'];
                                        } elseif (isset($product['image']) && !empty($product['image'])) {
                                            $productImage = $product['image'];
                                        } elseif (isset($product['images']) && is_array($product['images'])) {
                                            if (isset($product['images']['front']) && !empty($product['images']['front'])) {
                                                $productImage = $product['images']['front'];
                                            } elseif (isset($product['images'][0]) && !empty($product['images'][0])) {
                                                $productImage = $product['images'][0];
                                            }
                                        }
                                    } elseif (isset($item['price'])) {
                                        $price = $item['price'];
                                    } elseif (isset($item['subtotal'])) {
                                        $price = $item['subtotal'] / $item['quantity'];
                                    }
                                    
                                    // Clean image path
                                    if (!empty($productImage) && $productImage !== 'img/download.webp') {
                                        // If it's already a full path, use it as is
                                        if (strpos($productImage, 'http') === 0) {
                                            // Full URL, use as is
                                        } elseif (strpos($productImage, '/') === 0) {
                                            // Absolute path, use as is
                                        } elseif (strpos($productImage, 'uploads/') === 0) {
                                            // Already has uploads/ prefix, use as is
                                        } elseif (strpos($productImage, 'img/') === 0) {
                                            // Already has img/ prefix, use as is
                                        } else {
                                            // Relative path, add uploads/ prefix
                                            $productImage = 'uploads/' . $productImage;
                                        }
                                    }
                                    
                                    // Fallback: If no product data, try to get from Product model
                                    if ($productName === 'Unknown Product' && isset($item['product_id'])) {
                                        $productModel = new Product();
                                        $fallbackProduct = $productModel->getById($item['product_id']);
                                        if ($fallbackProduct) {
                                            $productName = $fallbackProduct['name'] ?? 'Unknown Product';
                                            $productCategory = $fallbackProduct['category'] ?? 'Unknown Category';
                                            $price = $fallbackProduct['price'] ?? $price;
                                            
                                            if ($productImage === 'img/download.webp') {
                                                if (isset($fallbackProduct['front_image']) && !empty($fallbackProduct['front_image'])) {
                                                    $productImage = $fallbackProduct['front_image'];
                                                } elseif (isset($fallbackProduct['image_front']) && !empty($fallbackProduct['image_front'])) {
                                                    $productImage = $fallbackProduct['image_front'];
                                                } elseif (isset($fallbackProduct['image']) && !empty($fallbackProduct['image'])) {
                                                    $productImage = $fallbackProduct['image'];
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="item">
                                        <div class="item-image">
                                            <img src="<?php echo htmlspecialchars($productImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($productName); ?>"
                                                 onerror="this.src='img/download.webp';">
                                        </div>
                                        <div class="item-details">
                                            <div class="item-name">
                                                <?php echo htmlspecialchars($productName); ?>
                                                <span class="item-category"><?php echo htmlspecialchars($productCategory); ?></span>
                                            </div>
                                            <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                        </div>
                                        <div class="item-price">
                                            $<?php echo number_format($price * $item['quantity'], 2); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="order-total">
                            <div class="total-amount">Total: $<?php echo number_format($order['total_amount'], 2); ?></div>
                        </div>

                        <?php if (!empty($order['notes'])): ?>
                            <div class="order-info" style="margin-top: 15px;">
                                <h4><i class="fas fa-sticky-note"></i> Order Notes</h4>
                                <p><?php echo htmlspecialchars($order['notes']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
