<?php
/**
 * Unified Cart Page
 * Combines all cart functionality: display, add to cart, checkout, and management
 */

session_start();
require_once 'config/mongodb.php';
require_once 'models/Cart.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/Payment.php';

// Include cart configuration for consistent user ID
if (file_exists('cart-config.php')) {
    require_once 'cart-config.php';
}

// Get user ID from session - allow cart operations without auth
$userId = $_SESSION['user_id'] ?? null;

$cartModel = new Cart();
$productModel = new Product();
$orderModel = new Order();

// Get current cart (use session-based cart for unauthenticated users)
$cartUserId = $userId ?: 'session_' . session_id();
$cart = $cartModel->getCart($cartUserId);

// Get return URL from session or default to main page
$returnUrl = $_SESSION['return_url'] ?? 'index.php';

// If return URL is the same as current page, default to main page
if ($returnUrl === 'cart-unified.php' || $returnUrl === $_SERVER['REQUEST_URI']) {
    $returnUrl = 'index.php';
}

// Clean up the return URL to prevent potential security issues
$returnUrl = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');
if (empty($returnUrl) || strpos($returnUrl, '..') !== false || strpos($returnUrl, '://') !== false) {
    $returnUrl = 'index.php';
}

// Clear the return URL from session after using it
unset($_SESSION['return_url']);

// Get current page mode
$mode = $_GET['mode'] ?? 'view'; // view, add, checkout
$productId = $_GET['product_id'] ?? null;

// Get product details if in add mode
$product = null;
if ($mode === 'add' && $productId) {
    $product = $productModel->getById($productId);
    if (!$product) {
        $mode = 'view';
    }
}

// Function to get the best available image for a product
function getProductImage($product, $itemColor = '') {
    $imagePath = '';
    
    // First, try to get image from color variants if color is specified
    if (!empty($itemColor) && isset($product['color_variants']) && is_array($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (isset($variant['color']) && $variant['color'] === $itemColor) {
                if (!empty($variant['front_image'])) {
                    $imagePath = $variant['front_image'];
                    break;
                }
                if (isset($variant['images']) && is_array($variant['images']) && !empty($variant['images'])) {
                    $imagePath = $variant['images'][0];
                    break;
                }
            }
        }
    }
    
    // If no color-specific image found, try to get from any color variant
    if (empty($imagePath) && isset($product['color_variants']) && is_array($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            if (!empty($variant['front_image'])) {
                $imagePath = $variant['front_image'];
                break;
            }
            if (isset($variant['images']) && is_array($variant['images']) && !empty($variant['images'])) {
                $imagePath = $variant['images'][0];
                break;
            }
        }
    }
    
    // Fallback to main product images
    if (empty($imagePath)) {
        if (!empty($product['front_image'])) {
            $imagePath = $product['front_image'];
        } elseif (!empty($product['image_front'])) {
            $imagePath = $product['image_front'];
        } elseif (isset($product['images']) && is_array($product['images'])) {
            if (isset($product['images']['front'])) {
                $imagePath = $product['images']['front'];
            } elseif (!empty($product['images'])) {
                $imagePath = $product['images'][0];
            }
        }
    }
    
    // Normalize the image path
    if (!empty($imagePath)) {
        $imagePath = ltrim($imagePath, '/.');
        
        // Check if it's already a full URL
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Check if it's an uploads path
        if (strpos($imagePath, 'uploads/') === 0) {
            return $imagePath;
        }
        
        // Check if it's an img path
        if (strpos($imagePath, 'img/') === 0) {
            return $imagePath;
        }
        
        // Default to uploads directory
        return 'uploads/' . $imagePath;
    }
    
    return 'img/default-product.jpg';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        switch($mode) {
            case 'add': echo 'Add to Cart - ' . htmlspecialchars($product['name']); break;
            case 'checkout': echo 'Checkout - Glamour'; break;
            default: echo 'Shopping Cart - Glamour'; break;
        }
        ?>
    </title>
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

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .nav-tab {
            padding: 12px 24px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tab.active {
            background: #0066cc;
            color: white;
            border-color: #0066cc;
        }

        .nav-tab:hover {
            background: #0066cc;
            color: white;
            border-color: #0066cc;
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

        .content-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            margin-bottom: 20px;
        }

        /* Cart View Styles */
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }

        .cart-items {
            display: grid;
            gap: 20px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto auto;
            gap: 20px;
            align-items: center;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: #fafbfc;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .item-details p {
            color: #666;
            font-size: 0.9rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls button {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-controls input {
            width: 50px;
            height: 35px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .remove-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .remove-btn i {
            font-size: 0.8rem;
        }

        .remove-btn span {
            display: none;
        }

        .cart-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            position: sticky;
            top: 20px;
            text-align: center;
        }

        .cart-summary h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 1.2rem;
            color: #0066cc;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Add to Cart Styles */
        .product-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .product-image {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .product-info h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .product-price {
            font-size: 1.5rem;
            color: #0066cc;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .product-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .options-section {
            margin-bottom: 30px;
        }

        .option-group {
            margin-bottom: 20px;
        }

        .option-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .option-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .option-group select:focus {
            outline: none;
            border-color: #0066cc;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-selector input {
            width: 80px;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            font-size: 1rem;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 15px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .add-to-cart-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .add-to-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* Checkout Styles */
        .checkout-form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
        }

        .payment-methods {
            display: grid;
            gap: 15px;
            margin: 20px 0;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #0066cc;
            background: #f8f9fa;
        }

        .payment-method input[type="radio"] {
            margin: 0;
        }

        .payment-method.selected {
            border-color: #0066cc;
            background: #e6f3ff;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .modal h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .modal p {
            color: #666;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cart-summary {
                position: static;
                order: -1;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .cart-item img {
                width: 80px;
                height: 80px;
            }

            .quantity-controls {
                grid-column: 1 / -1;
                justify-content: center;
            }

            .remove-btn {
                grid-column: 1 / -1;
                justify-content: center;
            }

            .product-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            margin-bottom: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Handle messages from URL parameters
        $message = $_GET['message'] ?? '';
        if ($message === 'empty_cart'): ?>
        <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Empty Cart:</strong> You need to add items to your cart before proceeding to payment.
        </div>
        <?php endif; ?>
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-shopping-cart"></i>
                <?php 
                switch($mode) {
                    case 'add': echo 'Add to Cart'; break;
                    case 'checkout': echo 'Checkout'; break;
                    default: echo 'Shopping Cart'; break;
                }
                ?>
            </h1>
            <a href="<?php echo $returnUrl; ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Shopping
            </a>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <a href="?mode=view" class="nav-tab <?php echo $mode === 'view' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Cart (<?php echo count($cart['items']); ?>)
            </a>
            <?php if ($mode === 'add'): ?>
            <a href="?mode=add&product_id=<?php echo $productId; ?>" class="nav-tab active">
                <i class="fas fa-plus"></i> Add Item
            </a>
            <?php endif; ?>
            <?php if ($mode === 'checkout'): ?>
            <a href="?mode=checkout" class="nav-tab active">
                <i class="fas fa-credit-card"></i> Checkout
            </a>
            <?php endif; ?>
        </div>

        <!-- Content Sections -->
        <?php if ($mode === 'view'): ?>
        <!-- Cart View -->
        <div class="content-section">
            <?php if (empty($cart['items'])): ?>
            <!-- Authenticated but empty cart -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    Continue Shopping
                </a>
            </div>
            <?php else: ?>
            <div class="cart-layout">
                <div class="cart-items">
                    <?php foreach ($cart['items'] as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo getProductImage($item['product'], $item['color'] ?? ''); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                        
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                            <p>Price: $<?php echo number_format($item['product']['price'], 2); ?></p>
                            <p>Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></p>
                            <?php if (!empty($item['color'])): ?>
                            <p>Color: <?php echo htmlspecialchars($item['color']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($item['size'])): ?>
                            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="quantity-controls">
                            <button onclick="updateQuantity('<?php echo $item['product_id']; ?>', -1)">-</button>
                            <input type="number" value="<?php echo $item['quantity']; ?>" 
                                   onchange="updateQuantity('<?php echo $item['product_id']; ?>', 0, this.value)" 
                                   min="1" max="10">
                            <button onclick="updateQuantity('<?php echo $item['product_id']; ?>', 1)">+</button>
                        </div>
                        
                        <button class="remove-btn" onclick="removeItem('<?php echo $item['product_id']; ?>')" title="Remove item from cart">
                            <i class="fas fa-trash"></i>
                            <span>Remove</span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3>Cart Summary</h3>
                    <div class="summary-row">
                        <span>Items (<?php echo $cart['item_count']; ?>):</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>
                    
                                         <div class="action-buttons">
                         <button onclick="proceedToCheckout()" class="btn btn-primary">
                             <i class="fas fa-credit-card"></i>
                             Proceed to Checkout
                         </button>
                        <button onclick="clearCart()" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($mode === 'add' && $product): ?>
        <!-- Add to Cart -->
        <div class="content-section">
            <div class="product-section">
                <div>
                    <img src="<?php echo getProductImage($product); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                </div>
                
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="product-description">
                        <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                    </div>
                    
                    <form id="addToCartForm">
                        <div class="options-section">
                            <?php if (isset($product['color_variants']) && !empty($product['color_variants'])): ?>
                            <div class="option-group">
                                <label for="color">Color *</label>
                                <select id="color" name="color" required onchange="updateAddToCartButton()">
                                    <option value="">Select Color</option>
                                    <?php foreach ($product['color_variants'] as $variant): ?>
                                    <option value="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php echo htmlspecialchars($variant['color']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($product['sizes']) && !empty($product['sizes'])): ?>
                            <div class="option-group">
                                <label for="size">Size *</label>
                                <select id="size" name="size" required onchange="updateAddToCartButton()">
                                    <option value="">Select Size</option>
                                    <?php foreach ($product['sizes'] as $size): ?>
                                    <option value="<?php echo htmlspecialchars($size); ?>">
                                        <?php echo htmlspecialchars($size); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <div class="option-group">
                                <label for="quantity">Quantity</label>
                                <div class="quantity-selector">
                                    <button type="button" onclick="changeQuantity(-1)">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
                                    <button type="button" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="add-to-cart-btn" onclick="addToCart()" id="addToCartBtn" disabled>
                            <i class="fas fa-shopping-cart"></i>
                            Please Select Options First
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mode === 'checkout'): ?>
        <!-- Checkout -->
        <div class="content-section">
            <form id="checkoutForm" class="checkout-form">
                <div class="form-group">
                    <label for="shipping_address">Shipping Address *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required 
                              placeholder="Enter your complete shipping address"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="billing_address">Billing Address</label>
                    <textarea id="billing_address" name="billing_address" rows="3" 
                              placeholder="Enter your billing address (optional)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <div class="payment-methods">
                        <div class="payment-method" onclick="selectPaymentMethod('cash_on_delivery')">
                            <input type="radio" id="cash_on_delivery" name="payment_method" value="cash_on_delivery" checked>
                            <label for="cash_on_delivery">Cash on Delivery</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('waafi')">
                            <input type="radio" id="waafi" name="payment_method" value="waafi">
                            <label for="waafi">Waafi</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('sahal')">
                            <input type="radio" id="sahal" name="payment_method" value="sahal">
                            <label for="sahal">SAHAL</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('saad')">
                            <input type="radio" id="saad" name="payment_method" value="saad">
                            <label for="saad">SAAD</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('evc')">
                            <input type="radio" id="evc" name="payment_method" value="evc">
                            <label for="evc">EVC</label>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('edahab')">
                            <input type="radio" id="edahab" name="payment_method" value="edahab">
                            <label for="edahab">EDAHAB</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Order Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Any special instructions for your order"></textarea>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Items (<?php echo $cart['item_count']; ?>):</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($cart['total'], 2); ?></span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i>
                        Place Order
                    </button>
                    <a href="?mode=view" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Cart
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Confirm Action</h3>
            <p id="modalMessage">Are you sure you want to proceed?</p>
            <div class="modal-buttons">
                <button onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="confirmAction()" class="btn btn-primary" id="confirmBtn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Authentication Modal -->
    <div id="authModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #0066cc; margin: 0;">Authentication Required</h3>
                <button onclick="closeAuthModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>
            </div>
            
            <p style="color: #666; margin-bottom: 20px; text-align: center;">
                You need to sign in or create an account to proceed with checkout.
            </p>

            <!-- Login Form -->
            <div id="loginForm" style="display: block;">
                <form id="checkoutLoginForm">
                    <div class="form-group">
                        <label for="checkoutUsername">Username or Email *</label>
                        <input type="text" id="checkoutUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="checkoutPassword">Password *</label>
                        <input type="password" id="checkoutPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 15px;">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                <div style="text-align: center;">
                    <p style="color: #666; margin: 10px 0;">Don't have an account?</p>
                    <button onclick="switchToRegister()" class="btn btn-secondary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </div>
            </div>

            <!-- Register Form -->
            <div id="registerForm" style="display: none;">
                <form id="checkoutRegisterForm">
                    <div class="form-group">
                        <label for="checkoutRegUsername">Username *</label>
                        <input type="text" id="checkoutRegUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegEmail">Email Address *</label>
                        <input type="email" id="checkoutRegEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegContact">Contact Number *</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="background: #f8f9fa; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; color: #666;">+252</span>
                            <input type="tel" id="checkoutRegContact" name="contact_number" placeholder="123456789" maxlength="9" pattern="[0-9]{9}" required style="flex: 1;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <div style="display: flex; gap: 20px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 5px; font-weight: normal;">
                                <input type="radio" name="gender" value="male" required> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px; font-weight: normal;">
                                <input type="radio" name="gender" value="female" required> Female
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegRegion">Region *</label>
                        <select id="checkoutRegRegion" name="region" required>
                            <option value="">Select Region</option>
                            <option value="banadir">Banadir</option>
                            <option value="bari">Bari</option>
                            <option value="bay">Bay</option>
                            <option value="galguduud">Galguduud</option>
                            <option value="gedo">Gedo</option>
                            <option value="hiran">Hiran</option>
                            <option value="jubbada-dhexe">Jubbada Dhexe</option>
                            <option value="jubbada-hoose">Jubbada Hoose</option>
                            <option value="mudug">Mudug</option>
                            <option value="nugaal">Nugaal</option>
                            <option value="sanaag">Sanaag</option>
                            <option value="shabeellaha-dhexe">Shabeellaha Dhexe</option>
                            <option value="shabeellaha-hoose">Shabeellaha Hoose</option>
                            <option value="sool">Sool</option>
                            <option value="togdheer">Togdheer</option>
                            <option value="woqooyi-galbeed">Woqooyi Galbeed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegCity">City *</label>
                        <input type="text" id="checkoutRegCity" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegPassword">Password *</label>
                        <input type="password" id="checkoutRegPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="checkoutRegConfirmPassword">Confirm Password *</label>
                        <input type="password" id="checkoutRegConfirmPassword" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 15px;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                <div style="text-align: center;">
                    <p style="color: #666; margin: 10px 0;">Already have an account?</p>
                    <button onclick="switchToLogin()" class="btn btn-secondary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAction = null;
        let currentCallback = null;

        // Modal functions
        function showModal(title, message, callback) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modal').style.display = 'block';
            currentCallback = callback;
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
            currentCallback = null;
        }

        function confirmAction() {
            if (currentCallback) {
                currentCallback();
            }
            closeModal();
        }

        // Cart functions
        function removeItem(productId) {
            showModal(
                'Remove Item', 
                'Are you sure you want to remove this item from your cart?',
                function() {
                    closeModal();
                    
                    const formData = new FormData();
                    formData.append('action', 'remove_item');
                    formData.append('product_id', productId);
                    
                    fetch('cart-api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                location.reload();
                            } else if (data.requires_auth) {
                                showAuthenticationModal();
                            } else {
                                showModal('Error', 'Error: ' + data.message, closeModal);
                            }
                        } catch (e) {
                            showModal('Error', 'Invalid response from server', closeModal);
                        }
                    })
                    .catch(error => {
                        showModal('Error', 'Network error: ' + error.message, closeModal);
                    });
                }
            );
        }

        function updateQuantity(productId, change, newValue = null) {
            let quantity;
            if (newValue !== null) {
                quantity = parseInt(newValue);
            } else {
                const input = event.target.parentElement.querySelector('input');
                quantity = parseInt(input.value) + change;
            }
            
            quantity = Math.max(1, Math.min(10, quantity));

            const formData = new FormData();
            formData.append('action', 'update_quantity');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('cart-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        location.reload();
                    } else if (data.requires_auth) {
                        showAuthenticationModal();
                    } else {
                        showModal('Error', 'Error: ' + data.message, closeModal);
                    }
                } catch (e) {
                    showModal('Error', 'Invalid response from server', closeModal);
                }
            })
            .catch(error => {
                showModal('Error', 'Error updating quantity', closeModal);
            });
        }

        function clearCart() {
            showModal(
                'Clear Cart', 
                'Are you sure you want to clear your entire cart? This action cannot be undone.',
                function() {
                    closeModal();
                    
                    const formData = new FormData();
                    formData.append('action', 'clear_cart');
                    
                    fetch('cart-api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                showModal('Success', 'Cart cleared successfully!', function() {
                                    closeModal();
                                    location.reload();
                                });
                            } else if (data.requires_auth) {
                                showAuthenticationModal();
                            } else {
                                showModal('Error', 'Error: ' + data.message, closeModal);
                            }
                        } catch (e) {
                            showModal('Error', 'Invalid response from server', closeModal);
                        }
                    })
                    .catch(error => {
                        showModal('Error', 'Error clearing cart', closeModal);
                    });
                }
            );
        }

        // Add to cart functions
        function changeQuantity(change) {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value) + change;
            value = Math.max(1, Math.min(10, value));
            input.value = value;
        }

        function updateAddToCartButton() {
            const colorSelect = document.getElementById('color');
            const sizeSelect = document.getElementById('size');
            const addToCartBtn = document.getElementById('addToCartBtn');
            
            if (colorSelect && sizeSelect) {
                if (colorSelect.value && sizeSelect.value) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Please Select Options First';
                }
            } else if (colorSelect || sizeSelect) {
                const requiredSelect = colorSelect || sizeSelect;
                if (requiredSelect.value) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Please Select Options First';
                }
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            }
        }

        function addToCart() {
            const colorSelect = document.getElementById('color');
            const sizeSelect = document.getElementById('size');
            const quantityInput = document.getElementById('quantity');
            
            if (colorSelect && !colorSelect.value) {
                alert('Please select a color!');
                return;
            }
            
            if (sizeSelect && !sizeSelect.value) {
                alert('Please select a size!');
                return;
            }
            
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            const formData = new FormData();
            formData.append('action', 'add_to_cart');
            formData.append('product_id', '<?php echo $productId; ?>');
            formData.append('quantity', quantityInput.value);
            formData.append('color', colorSelect ? colorSelect.value : '');
            formData.append('size', sizeSelect ? sizeSelect.value : '');
            formData.append('return_url', 'cart-unified.php?mode=view');
            
            fetch('cart-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModal('Success', 'Product added to cart successfully!', function() {
                        closeModal();
                        window.location.href = 'cart-unified.php?mode=view';
                    });
                } else if (data.requires_auth) {
                    showAuthenticationModal();
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                } else {
                    showModal('Error', 'Error adding product to cart: ' + data.message, closeModal);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                showModal('Error', 'Error adding product to cart: ' + error.message, closeModal);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // Checkout functions
        function proceedToCheckout() {
            // Check if user is authenticated
            checkAuthenticationStatus()
                .then(isAuthenticated => {
                    if (isAuthenticated) {
                        // User is logged in, proceed with checkout
                        createOrderAndRedirect();
                    } else {
                        // User is not logged in, redirect to registration
                        window.location.href = 'menfolder/register.php?redirect=cart';
                    }
                })
                .catch(error => {
                    console.error('Error checking authentication:', error);
                    // If there's an error checking auth, redirect to registration as fallback
                    window.location.href = 'menfolder/register.php?redirect=cart';
                });
        }

        // Check authentication status
        function checkAuthenticationStatus() {
            return fetch('auth-check.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                return data.authenticated === true;
            })
            .catch(error => {
                console.error('Auth check failed:', error);
                return false;
            });
        }

        // Create order and redirect (original checkout logic)
        function createOrderAndRedirect() {
            const formData = new FormData();
            formData.append('action', 'place_order');
            formData.append('shipping_address', 'Will be collected during payment');
            formData.append('payment_method', 'pending');
            
            fetch('cart-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to payment page with order ID
                    window.location.href = 'payment.php?order_id=' + data.order_id;
                } else {
                    showModal('Error', 'Error creating order: ' + data.message, closeModal);
                }
            })
            .catch(error => {
                showModal('Error', 'Error creating order: ' + error.message, closeModal);
            });
        }

        // Show authentication modal
        function showAuthenticationModal() {
            const authModal = document.getElementById('authModal');
            if (authModal) {
                authModal.style.display = 'block';
            }
        }

        // Close authentication modal
        function closeAuthModal() {
            const authModal = document.getElementById('authModal');
            if (authModal) {
                authModal.style.display = 'none';
            }
        }

        // Switch to login form in auth modal
        function switchToLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }

        // Switch to register form in auth modal
        function switchToRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }

        // Handle successful authentication
        function onAuthenticationSuccess(redirectUrl = null) {
            closeAuthModal();
            
            if (redirectUrl) {
                // Redirect to the specified URL (e.g., back to cart)
                window.location.href = redirectUrl;
            } else {
                // Default behavior: proceed with checkout
                createOrderAndRedirect();
            }
        }

        function selectPaymentMethod(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }

        // Only add checkout form event listener if checkout form exists
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'place_order');
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                fetch('cart-api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showModal('Success', 'Order placed successfully! Your order ID is: ' + data.order_id, function() {
                            closeModal();
                            window.location.href = 'orders.php?order_id=' + data.order_id;
                        });
                    } else if (data.requires_auth) {
                        showAuthenticationModal();
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    } else {
                        showModal('Error', 'Error placing order: ' + data.message, closeModal);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    showModal('Error', 'Error placing order: ' + error.message, closeModal);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }

        // Initialize add to cart button state
        <?php if ($mode === 'add'): ?>
        updateAddToCartButton();
        <?php endif; ?>

        // Authentication form handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Login form handler
            const checkoutLoginForm = document.getElementById('checkoutLoginForm');
            if (checkoutLoginForm) {
                checkoutLoginForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const loginData = {
                        username: formData.get('username'),
                        password: formData.get('password'),
                        redirect: 'cart'
                    };
                    
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                    
                    try {
                        const response = await fetch('menfolder/login-handler.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(loginData)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showModal('Success', result.message, function() {
                                closeModal();
                                // Pass the redirect URL from the server response
                                onAuthenticationSuccess(result.redirect);
                            });
                        } else {
                            showModal('Error', result.message, closeModal);
                        }
                    } catch (error) {
                        showModal('Error', 'Login failed: ' + error.message, closeModal);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }

            // Register form handler
            const checkoutRegisterForm = document.getElementById('checkoutRegisterForm');
            if (checkoutRegisterForm) {
                checkoutRegisterForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const registerData = {
                        username: formData.get('username'),
                        email: formData.get('email'),
                        contact_number: '+252' + formData.get('contact_number'),
                        gender: formData.get('gender'),
                        region: formData.get('region'),
                        city: formData.get('city'),
                        password: formData.get('password'),
                        confirm_password: formData.get('confirm_password')
                    };
                    
                    // Validate passwords match
                    if (registerData.password !== registerData.confirm_password) {
                        showModal('Error', 'Passwords do not match', closeModal);
                        return;
                    }
                    
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                    
                    try {
                        const response = await fetch('menfolder/register-handler.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(registerData)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showModal('Success', result.message, function() {
                                closeModal();
                                // Switch to login form after successful registration
                                switchToLogin();
                                showModal('Info', 'Please sign in with your new account to continue checkout.', closeModal);
                            });
                        } else {
                            showModal('Error', result.message, closeModal);
                        }
                    } catch (error) {
                        showModal('Error', 'Registration failed: ' + error.message, closeModal);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }
        });
    </script>
</body>
</html>
