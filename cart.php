<?php
/**
 * Shopping Cart Page
 * Displays user's cart items and allows checkout
 */

session_start();
require_once 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/Product.php';

// Get user ID from session or use demo user
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'demo_user_123';

$cartModel = new Cart();
$cart = $cartModel->getCart($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Glamour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header h1 {
            color: #0066cc;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: #f8f9fa;
            color: #0066cc;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #0066cc;
            color: white;
            border-color: #0066cc;
        }

        .cart-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .cart-items {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }

        .cart-items h2 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }

        .item-details h3 {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .item-options {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .item-price {
            color: #0066cc;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 8px;
        }

        .quantity-controls button {
            width: 30px;
            height: 30px;
            border: none;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            color: #0066cc;
        }

        .quantity-controls button:hover {
            background: #0066cc;
            color: white;
        }

        .quantity-controls input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: none;
            background: white;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .remove-btn:hover {
            background: #c82333;
        }

        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .cart-summary h2 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .summary-item.total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0066cc;
            border-top: 2px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }

        .checkout-btn {
            width: 100%;
            padding: 18px 30px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .checkout-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .clear-cart-btn {
            flex: 1;
            padding: 18px 30px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .clear-cart-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            color: #666;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #999;
            margin-bottom: 30px;
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .shop-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .cart-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .item-quantity {
                grid-column: 1 / -1;
                justify-content: center;
                margin-top: 15px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }


        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
            <a href="products.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Continue Shopping
            </a>
        </div>

        <?php if (empty($cart['items'])): ?>
            <!-- Empty Cart -->
            <div class="cart-items">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="products.php" class="shop-btn">
                        <i class="fas fa-shopping-bag"></i>
                        Start Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <!-- Cart Items -->
                <div class="cart-items">
                    <h2>Cart Items (<?php echo count($cart['items']); ?>)</h2>
                    
                    <?php foreach ($cart['items'] as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['product']['front_image'] ?? $item['product']['image_front'] ?? ''); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product']['name']); ?>" 
                                 class="item-image">
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                                <div class="item-options">
                                    <?php if (!empty($item['color'])): ?>
                                        Color: <span style="display: inline-block; width: 15px; height: 15px; background-color: <?php echo htmlspecialchars($item['color']); ?>; border-radius: 50%; vertical-align: middle; margin-right: 5px;"></span>
                                        <?php echo htmlspecialchars($item['color']); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($item['size'])): ?>
                                        | Size: <?php echo htmlspecialchars($item['size']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item['product']['price'], 2); ?>
                                </div>
                            </div>
                            
                            <div class="item-quantity">
                                <div class="quantity-controls">
                                    <button onclick="updateQuantity('<?php echo $item['product']['_id']; ?>', -1)">-</button>
                                    <input type="number" value="<?php echo $item['quantity']; ?>" 
                                           onchange="updateQuantity('<?php echo $item['product']['_id']; ?>', 0, this.value)" 
                                           min="1" max="10">
                                    <button onclick="updateQuantity('<?php echo $item['product']['_id']; ?>', 1)">+</button>
                                </div>
                                <button class="remove-btn" onclick="removeItem('<?php echo $item['product']['_id']; ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-item total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>

                    <div class="action-buttons">
                        <button class="checkout-btn" onclick="proceedToCheckout()">
                            <i class="fas fa-credit-card"></i>
                            Proceed to Checkout
                        </button>
                        <button class="clear-cart-btn" onclick="clearCart()">
                            <i class="fas fa-trash"></i>
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Update quantity
        function updateQuantity(productId, change, newValue = null) {
            let quantity;
            if (newValue !== null) {
                quantity = parseInt(newValue);
            } else {
                const input = event.target.parentElement.querySelector('input');
                quantity = parseInt(input.value) + change;
            }
            
            quantity = Math.max(1, Math.min(10, quantity));

            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_quantity&product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating quantity');
            });
        }

        // Remove item
        function removeItem(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('cart-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove_item&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
            }
        }

        // Clear cart
        function clearCart() {
            if (confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
                fetch('cart-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_cart'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cart cleared successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error clearing cart');
                });
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
