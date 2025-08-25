<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Include required files with correct paths
require_once '../models/Order.php';
require_once '../models/Cart.php';
require_once '../models/Product.php';

$orderModel = new Order();
$orders = $orderModel->getUserOrders($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Glamour Palace</title>
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

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #0066cc;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(0, 102, 204, 0.1);
            transform: translateX(-5px);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
        }

        .orders-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 102, 204, 0.1);
            border: 1px solid #e1e8ed;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e1e8ed;
        }

        .order-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
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
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-info p {
            color: #666;
            line-height: 1.5;
        }

        .order-items {
            margin-bottom: 20px;
        }

        .order-items h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-details {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #e1e8ed;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .item-quantity {
            color: #666;
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 600;
            color: #0066cc;
            font-size: 1.1rem;
            text-align: right;
            min-width: 80px;
        }

        .order-total {
            text-align: right;
            padding-top: 15px;
            border-top: 2px solid #e1e8ed;
        }

        .total-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
        }

        .no-orders {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 102, 204, 0.1);
        }

        .no-orders i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-orders h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .no-orders p {
            color: #666;
            margin-bottom: 30px;
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
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
                align-items: flex-start;
                gap: 10px;
            }

            .item-details {
                width: 100%;
            }

            .item-price {
                align-self: flex-end;
                text-align: right;
            }

            .item-image {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
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
                    <a href="index.php" class="shop-btn">
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

                        <?php if (!empty($order['items'])): ?>
                            <div class="order-items">
                                <h4><i class="fas fa-box"></i> Order Items</h4>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="item">
                                        <div class="item-details">
                                            <div class="item-image">
                                                <?php 
                                                $imagePath = '';
                                                if (isset($item['product']['images']) && !empty($item['product']['images'])) {
                                                    $imagePath = '../uploads/products/' . $item['product']['images'][0];
                                                } elseif (isset($item['product']['image'])) {
                                                    $imagePath = '../uploads/products/' . $item['product']['image'];
                                                } elseif (isset($item['image'])) {
                                                    $imagePath = '../uploads/products/' . $item['image'];
                                                } else {
                                                    $imagePath = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0zMCAyNUMzMy44NjYgMjUgMzcgMjguMTM0IDM3IDMyQzM3IDM1Ljg2NiAzMy44NjYgMzkgMzAgMzlDMjYuMTM0IDM5IDIzIDM1Ljg2NiAyMyAzMkMyMyAyOC4xMzQgMjYuMTM0IDI1IDMwIDI1WiIgZmlsbD0iI0NDQyIvPgo8cGF0aCBkPSJNNDAgNDVIMjBDMTguODk1NCA0NSAxOCA0NC4xMDQ2IDE4IDQzVjM3QzE4IDM1Ljg5NTQgMTguODk1NCAzNSAyMCAzNUg0MEM0MS4xMDQ2IDM1IDQyIDM1Ljg5NTQgNDIgMzdWNDNDNDIgNDQuMTA0NiA0MS4xMDQ2IDQ1IDQwIDQ1WiIgZmlsbD0iI0NDQyIvPgo8L3N2Zz4K';
                                                }
                                                ?>
                                                                                                 <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                                      alt="<?php echo htmlspecialchars($productName ?? 'Product'); ?>"
                                                      onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0zMCAyNUMzMy44NjYgMjUgMzcgMjguMTM0IDM3IDMyQzM3IDM1Ljg2NiAzMy44NjYgMzkgMzAgMzlDMjYuMTM0IDM5IDIzIDM1Ljg2NiAyMyAzMkMyMyAyOC4xMzQgMjYuMTM0IDI1IDMwIDI1WiIgZmlsbD0iI0NDQyIvPgo8cGF0aCBkPSJNNDAgNDVIMjBDMTguODk1NCA0NSAxOCA0NC4xMDQ2IDE4IDQzVjM3QzE4IDM1Ljg5NTQgMTguODk1NCAzNSAyMCAzNUg0MEM0MS4xMDQ2IDM1IDQyIDM1Ljg5NTQgNDIgMzdWNDNDNDIgNDQuMTA0NiA0MS4xMDQ2IDQ1IDQwIDQ1WiIgZmlsbD0iI0NDQyIvPgo8L3N2Zz4K'">
                                            </div>
                                            <div class="item-info">
                                                <div class="item-name">
                                                    <?php 
                                                    $productName = 'Unknown Product';
                                                    if (isset($item['product']['name'])) {
                                                        $productName = $item['product']['name'];
                                                    } elseif (isset($item['name'])) {
                                                        $productName = $item['name'];
                                                    }
                                                    echo htmlspecialchars($productName); 
                                                    ?>
                                                </div>
                                                <div class="item-quantity">
                                                    <span style="color: #666; font-size: 0.9rem;">Quantity: <?php echo $item['quantity']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-price">
                                            $<?php 
                                            $price = 0;
                                            if (isset($item['product']['price'])) {
                                                $price = $item['product']['price'];
                                            } elseif (isset($item['price'])) {
                                                $price = $item['price'];
                                            } elseif (isset($item['subtotal'])) {
                                                $price = $item['subtotal'] / $item['quantity'];
                                            }
                                            echo number_format($price * $item['quantity'], 2); 
                                            ?>
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
