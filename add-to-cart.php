<?php
/**
 * Professional Add to Cart Page
 * Allows users to select product options before adding to cart
 */

session_start();
require_once 'config/mongodb.php';
require_once 'models/Product.php';
require_once 'models/Cart.php';

// Get product ID from URL
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: products.php');
    exit;
}

$productModel = new Product();
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Get user ID from session or use demo user
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'demo_user_123';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Cart - <?php echo htmlspecialchars($product['name']); ?> | Glamour</title>
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

        .product-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            margin-bottom: 30px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .product-images {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .image-thumbnails {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail.active {
            border-color: #0066cc;
            transform: scale(1.05);
        }

        .product-details {
            padding: 20px 0;
        }

        .product-name {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .product-category {
            color: #666;
            font-size: 1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-category i {
            color: #0066cc;
        }

        .price-section {
            margin-bottom: 30px;
        }

        .current-price {
            font-size: 2rem;
            font-weight: 700;
            color: #0066cc;
        }

        .original-price {
            font-size: 1.5rem;
            color: #999;
            text-decoration: line-through;
            margin-left: 15px;
        }

        .sale-price {
            font-size: 2rem;
            font-weight: 700;
            color: #dc3545;
            margin-left: 15px;
        }

        .product-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .options-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            margin-bottom: 30px;
        }

        .options-section h2 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .option-group {
            margin-bottom: 30px;
        }

        .option-group h3 {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .color-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            opacity: 0.7;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.active {
            border-color: #0066cc;
            transform: scale(1.1);
            opacity: 1;
        }

        .color-option.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .size-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .size-option {
            padding: 15px 25px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
            opacity: 0.7;
        }

        .size-option:hover {
            border-color: #0066cc;
            background: #f8f9fa;
        }

        .size-option.active {
            border-color: #0066cc;
            background: #0066cc;
            color: white;
            opacity: 1;
        }

        .quantity-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 10px;
        }

        .quantity-controls button {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            color: #0066cc;
            transition: all 0.3s ease;
        }

        .quantity-controls button:hover {
            background: #0066cc;
            color: white;
        }

        .quantity-controls input {
            width: 80px;
            height: 40px;
            text-align: center;
            border: none;
            background: white;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .stock-info {
            color: #666;
            font-size: 0.9rem;
        }

        .action-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
            position: sticky;
            top: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .add-to-cart-btn {
            flex: 1;
            padding: 18px 30px;
            background: #0066cc;
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

        .add-to-cart-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .add-to-cart-btn:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
            transform: none !important;
            opacity: 0.6 !important;
            pointer-events: none !important;
        }

        .add-to-cart-btn:disabled:hover {
            background: #ccc !important;
            transform: none !important;
            opacity: 0.6 !important;
        }

        .wishlist-btn {
            padding: 18px 25px;
            background: white;
            color: #666;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .wishlist-btn:hover {
            border-color: #0066cc;
            color: #0066cc;
        }

        .wishlist-btn.active {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .product-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .summary-item:last-child {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #0066cc;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .main-image {
                height: 300px;
            }

            .action-buttons {
                flex-direction: column;
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
            <h1><i class="fas fa-shopping-cart"></i> Add to Cart</h1>
            <a href="products.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Products
            </a>
        </div>

        <!-- Product Section -->
        <div class="product-section">
            <div class="product-grid">
                <!-- Product Images -->
                <div class="product-images">
                    <img id="mainImage" src="<?php echo htmlspecialchars($product['front_image'] ?? $product['image_front'] ?? ''); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="main-image">
                    
                    <div class="image-thumbnails">
                        <?php if (!empty($product['front_image'] ?? $product['image_front'])): ?>
                            <img src="<?php echo htmlspecialchars($product['front_image'] ?? $product['image_front']); ?>" 
                                 alt="Front" class="thumbnail active" onclick="changeImage(this)">
                        <?php endif; ?>
                        
                        <?php if (!empty($product['back_image'] ?? $product['image_back'])): ?>
                            <img src="<?php echo htmlspecialchars($product['back_image'] ?? $product['image_back']); ?>" 
                                 alt="Back" class="thumbnail" onclick="changeImage(this)">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="product-details">
                    <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-category">
                        <i class="fas fa-tag"></i>
                        <?php echo htmlspecialchars($product['category']); ?>
                        <?php if (!empty($product['subcategory'])): ?>
                            <i class="fas fa-chevron-right"></i>
                            <?php echo htmlspecialchars($product['subcategory']); ?>
                        <?php endif; ?>
                    </div>

                    <div class="price-section">
                        <?php if (($product['sale'] ?? false) && isset($product['salePrice'])): ?>
                            <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <span class="sale-price">$<?php echo number_format($product['salePrice'], 2); ?></span>
                        <?php else: ?>
                            <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($product['description'])): ?>
                        <div class="product-description">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Options Section -->
        <div class="options-section">
            <h2>Select Your Options</h2>
            <div class="selection-required" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
                <i class="fas fa-info-circle"></i>
                <strong>Required:</strong> Please select both a color and size before adding to cart.
            </div>
            
            <!-- Color Selection -->
            <div class="option-group">
                <h3>Color</h3>
                <div class="color-options" id="colorOptions">
                    <?php 
                    $colors = $product['colors'] ?? ['#000000'];
                    foreach ($colors as $index => $color): 
                    ?>
                        <div class="color-option" 
                             style="background-color: <?php echo htmlspecialchars($color); ?>"
                             onclick="selectColor(this, '<?php echo htmlspecialchars($color); ?>')"
                             data-color="<?php echo htmlspecialchars($color); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Size Selection -->
            <div class="option-group">
                <h3>Size</h3>
                <div class="size-options" id="sizeOptions">
                    <?php 
                    $sizes = $product['sizes'] ?? ['S', 'M', 'L'];
                    foreach ($sizes as $index => $size): 
                    ?>
                        <div class="size-option" 
                             onclick="selectSize(this, '<?php echo htmlspecialchars($size); ?>')"
                             data-size="<?php echo htmlspecialchars($size); ?>">
                            <?php echo htmlspecialchars($size); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quantity Selection -->
            <div class="option-group">
                <h3>Quantity</h3>
                <div class="quantity-section">
                    <div class="quantity-controls">
                        <button onclick="changeQuantity(-1)">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="10" readonly>
                        <button onclick="changeQuantity(1)">+</button>
                    </div>
                    <div class="stock-info">
                        <?php if (($product['stock'] ?? 0) > 0): ?>
                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                            In Stock (<?php echo $product['stock']; ?> available)
                        <?php else: ?>
                            <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                            Out of Stock
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="action-section">
            <div class="error-message" id="errorMessage"></div>
            <div class="success-message" id="successMessage"></div>

            <div class="product-summary">
                <div class="summary-item">
                    <span>Selected Color:</span>
                    <span id="selectedColor">Please select a color</span>
                </div>
                <div class="summary-item">
                    <span>Selected Size:</span>
                    <span id="selectedSize">Please select a size</span>
                </div>
                <div class="summary-item">
                    <span>Quantity:</span>
                    <span id="selectedQuantity">1</span>
                </div>
                <div class="summary-item">
                    <span>Total Price:</span>
                    <span id="totalPrice">
                        $<?php echo number_format(($product['sale'] ?? false) && isset($product['salePrice']) ? $product['salePrice'] : $product['price'], 2); ?>
                    </span>
                </div>
            </div>

            <div class="action-buttons">
                <button class="add-to-cart-btn" onclick="addToCart()" id="addToCartBtn" disabled>
                    <i class="fas fa-shopping-cart"></i>
                    Please Select Options First
                </button>
                <button class="wishlist-btn" onclick="toggleWishlist()" id="wishlistBtn">
                    <i class="fas fa-heart"></i>
                    Wishlist
                </button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let selectedColor = '';
        let selectedSize = '';
        let quantity = 1;
        let productPrice = <?php echo ($product['sale'] ?? false) && isset($product['salePrice']) ? $product['salePrice'] : $product['price']; ?>;

        // Change main image
        function changeImage(thumbnail) {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
            document.getElementById('mainImage').src = thumbnail.src;
        }

        // Select color
        function selectColor(element, color) {
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            selectedColor = color;
            updateSummary();
        }

        // Select size
        function selectSize(element, size) {
            document.querySelectorAll('.size-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            selectedSize = size;
            updateSummary();
        }

        // Change quantity
        function changeQuantity(change) {
            const newQuantity = Math.max(1, Math.min(10, quantity + change));
            quantity = newQuantity;
            document.getElementById('quantity').value = quantity;
            updateSummary();
        }

        // Update summary
        function updateSummary() {
    
            
            document.getElementById('selectedColor').textContent = selectedColor || 'Please select a color';
            document.getElementById('selectedSize').textContent = selectedSize || 'Please select a size';
            document.getElementById('selectedQuantity').textContent = quantity;
            document.getElementById('totalPrice').textContent = '$' + (productPrice * quantity).toFixed(2);
            
            // Enable/disable add to cart button
            const addToCartBtn = document.getElementById('addToCartBtn');
            
            if (selectedColor && selectedSize) {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Please Select Options First';
            }
        }

        // Add to cart
        function addToCart() {
            
            // Double-check that both selections are made
            if (!selectedColor || !selectedSize) {
                alert('Please select both a color and size before adding to cart!');
                return;
            }
            
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=<?php echo $product['_id']; ?>&quantity=${quantity}&color=${selectedColor}&size=${selectedSize}&return_url=${encodeURIComponent(window.location.href)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Product added to cart successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'cart.php';
                    }, 2000);
                } else {
                    showMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error adding product to cart', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // Toggle wishlist
        function toggleWishlist() {
            const btn = document.getElementById('wishlistBtn');
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                showMessage('Added to wishlist!', 'success');
            } else {
                showMessage('Removed from wishlist!', 'success');
            }
        }

        // Show message
        function showMessage(message, type) {
            const errorMsg = document.getElementById('errorMessage');
            const successMsg = document.getElementById('successMessage');
            
            if (type === 'error') {
                errorMsg.textContent = message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            } else {
                successMsg.textContent = message;
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
            }
            
            setTimeout(() => {
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
            }, 5000);
        }

        // Initialize page state
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
        });
    </script>
</body>
</html>
