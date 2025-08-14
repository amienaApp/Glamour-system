<?php
function renderProductCard($product) {
    $id = $product['_id'];
    $name = htmlspecialchars($product['name']);
    $price = number_format($product['price'], 2);
    $color = $product['color'] ?? '#000000';
    $frontImage = $product['images']['front'] ?? '';
    $backImage = $product['images']['back'] ?? '';
    $salePrice = $product['salePrice'] ?? null;
    $isOnSale = $product['sale'] ?? false;
    $isFeatured = $product['featured'] ?? false;
    
    // Determine display price
    $displayPrice = $isOnSale && $salePrice ? $salePrice : $product['price'];
    $displayPrice = number_format($displayPrice, 2);
    
    // Generate unique ID for this product card
    $cardId = 'product-' . $id;
    ?>
    
    <div class="product-card" data-product-id="<?php echo $id; ?>">
        <?php if ($isFeatured): ?>
            <div class="featured-badge">Featured</div>
        <?php endif; ?>
        
        <?php if ($isOnSale): ?>
            <div class="sale-badge">Sale</div>
        <?php endif; ?>
        
        <div class="product-image-container">
            <img src="<?php echo $frontImage; ?>" 
                 alt="<?php echo $name; ?>" 
                 class="product-image front-image"
                 data-back-image="<?php echo $backImage; ?>">
            
            <div class="product-actions">
                <button class="wishlist-btn" onclick="toggleWishlist('<?php echo $id; ?>')">
                    <i class="fa fa-heart"></i>
                </button>
                <button class="quick-view-btn" onclick="quickView('<?php echo $id; ?>')">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
        
        <div class="product-info">
            <h3 class="product-name"><?php echo $name; ?></h3>
            
            <div class="product-price">
                <?php if ($isOnSale && $salePrice): ?>
                    <span class="original-price">$<?php echo $price; ?></span>
                    <span class="sale-price">$<?php echo number_format($salePrice, 2); ?></span>
                <?php else: ?>
                    <span class="current-price">$<?php echo $displayPrice; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="color-options">
                <div class="color-circle active" 
                     style="background-color: <?php echo $color; ?>"
                     data-color="<?php echo $color; ?>"
                     title="Available Color"></div>
            </div>
            
            <div class="product-actions-bottom">
                <button class="add-to-cart-btn" onclick="addToCart('<?php echo $id; ?>')">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>
    
    <style>
        .product-card {
            position: relative;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .featured-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }
        
        .sale-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4757;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }
        
        .product-image-container {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .product-card:hover .product-actions {
            opacity: 1;
        }
        
        .wishlist-btn,
        .quick-view-btn {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 50%;
            background: white;
            color: #333;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .wishlist-btn:hover,
        .quick-view-btn:hover {
            background: #007bff;
            color: white;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            line-height: 1.3;
            height: 42px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-price {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .current-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .original-price {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
        }
        
        .sale-price {
            font-size: 18px;
            font-weight: bold;
            color: #ff4757;
        }
        
        .color-options {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .color-circle {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .color-circle.active {
            border-color: #333;
            transform: scale(1.1);
        }
        
        .color-circle:hover {
            transform: scale(1.2);
        }
        
        .product-actions-bottom {
            display: flex;
            gap: 10px;
        }
        
        .add-to-cart-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .add-to-cart-btn:hover {
            background: #0056b3;
        }
        
        @media (max-width: 768px) {
            .product-name {
                font-size: 14px;
                height: 38px;
            }
            
            .current-price {
                font-size: 16px;
            }
            
            .product-info {
                padding: 12px;
            }
        }
    </style>
    
    <script>
        // Add to cart functionality
        function addToCart(productId) {
            // Add your cart logic here
            console.log('Adding product to cart:', productId);
            
            // Show notification
            showNotification('Product added to cart!', 'success');
            
            // Update cart count
            updateCartCount();
        }
        
        // Toggle wishlist
        function toggleWishlist(productId) {
            console.log('Toggling wishlist for product:', productId);
            
            const btn = event.target.closest('.wishlist-btn');
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                showNotification('Added to wishlist!', 'success');
            } else {
                showNotification('Removed from wishlist!', 'info');
            }
        }
        
        // Quick view functionality
        function quickView(productId) {
            console.log('Quick view for product:', productId);
            // Implement quick view modal
            showNotification('Quick view coming soon!', 'info');
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            // Add styles
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                font-weight: 600;
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            
            if (type === 'success') {
                notification.style.background = '#28a745';
            } else if (type === 'error') {
                notification.style.background = '#dc3545';
            } else {
                notification.style.background = '#17a2b8';
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Update cart count
        function updateCartCount() {
            // Implement cart count update logic
            console.log('Updating cart count...');
        }
        
        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
    
    <?php
}
?> 