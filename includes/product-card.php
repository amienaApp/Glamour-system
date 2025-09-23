<?php
function renderProductCard($product) {
    $id = $product['_id'];
    $name = htmlspecialchars($product['name']);
    $price = number_format($product['price'], 2);
    $color = $product['color'] ?? '#000000';
    $frontImage = $product['images']['front'] ?? '';
    $backImage = $product['images']['back'] ?? '';
    
    // Add fallback images if the specified images don't exist
    $fallbackImage = '../img/placeholder.jpg'; // Default placeholder image
    
    // Check for specific problematic image patterns and replace them
    if (strpos($frontImage, '1757680016') !== false || strpos($frontImage, 'uploads/products/') !== false) {
        $frontImage = $fallbackImage;
    }
    if (strpos($backImage, '1757680016') !== false || strpos($backImage, 'uploads/products/') !== false) {
        $backImage = $fallbackImage;
    }
    
    // Check if front image exists, use fallback if not
    if ($frontImage && $frontImage !== $fallbackImage) {
        $frontImagePath = str_replace('../', '', $frontImage);
        if (!file_exists($frontImagePath) || !is_file($frontImagePath)) {
            $frontImage = $fallbackImage;
        }
    } elseif (!$frontImage) {
        $frontImage = $fallbackImage;
    }
    
    // Check if back image exists, use fallback if not
    if ($backImage && $backImage !== $fallbackImage) {
        $backImagePath = str_replace('../', '', $backImage);
        if (!file_exists($backImagePath) || !is_file($backImagePath)) {
            $backImage = $fallbackImage;
        }
    } elseif (!$backImage) {
        $backImage = $fallbackImage;
    }
    $salePrice = $product['salePrice'] ?? null;
    $isOnSale = $product['sale'] ?? false;
    $isFeatured = $product['featured'] ?? false;
    $stock = isset($product['stock']) ? (int)$product['stock'] : 0;
    $isAvailable = isset($product['available']) ? $product['available'] : true;
    $isSoldOut = $stock <= 0 || !$isAvailable;
    
    // Determine display price
    $displayPrice = $isOnSale && $salePrice ? $salePrice : $product['price'];
    $displayPrice = number_format($displayPrice, 2);
    
    // Generate unique ID for this product card
    $cardId = 'product-' . $id;
    ?>
    
    <div class="product-card <?php echo $isSoldOut ? 'sold-out' : ''; ?>" 
         data-product-id="<?php echo $id; ?>"
         data-product-name="<?php echo htmlspecialchars($name); ?>"
         data-product-price="<?php echo $product['price']; ?>"
         data-product-sale-price="<?php echo $salePrice ?? ''; ?>"
         data-product-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
         data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
         data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
         data-product-featured="<?php echo $isFeatured ? 'true' : 'false'; ?>"
         data-product-on-sale="<?php echo $isOnSale ? 'true' : 'false'; ?>"
         data-product-stock="<?php echo $stock; ?>"
         data-product-available="<?php echo $isAvailable ? 'true' : 'false'; ?>"
         data-product-rating="<?php echo $product['rating'] ?? 0; ?>"
         data-product-review-count="<?php echo $product['reviewCount'] ?? 0; ?>"
         data-product-front-image="<?php echo htmlspecialchars($frontImage); ?>"
         data-product-back-image="<?php echo htmlspecialchars($backImage); ?>"
         data-product-color="<?php echo htmlspecialchars($color); ?>"
         data-product-images="<?php echo htmlspecialchars(json_encode($product['images'] ?? [])); ?>"
         data-product-color-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
         data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? [])); ?>"
         data-product-variants="<?php echo htmlspecialchars(json_encode($product['variants'] ?? [])); ?>"
         data-product-product-variants="<?php echo htmlspecialchars(json_encode($product['product_variants'] ?? [])); ?>"
         data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
         data-product-product-options="<?php echo htmlspecialchars(json_encode($product['product_options'] ?? [])); ?>"
         data-product-image-front="<?php echo htmlspecialchars($product['image_front'] ?? ''); ?>"
         data-product-image-back="<?php echo htmlspecialchars($product['image_back'] ?? ''); ?>">
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
                <button class="quick-view-btn" onclick="openQuickView('<?php echo $id; ?>')">
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
            
            <!-- Product Availability Status -->
            <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ''; ?>" style="<?php echo $isSoldOut || ($stock > 0 && $stock <= 2) ? '' : 'display: none;'; ?>">
                <?php if ($isSoldOut): ?>
                    <span style="color: #dc3545; font-weight: bold;">SOLD OUT</span>
                <?php elseif ($stock > 0 && $stock <= 2): ?>
                    <span style="color: #ffc107; font-weight: bold;">⚠️ Only <?php echo $stock; ?> left in stock!</span>
                <?php endif; ?>
            </div>
            
            <div class="color-options">
                <div class="color-circle active" 
                     style="background-color: <?php echo $color; ?>"
                     data-color="<?php echo $color; ?>"
                     title="Available Color"></div>
            </div>
            
            <div class="product-actions-bottom">
                <?php if ($isSoldOut): ?>
                    <button class="add-to-cart-btn sold-out-btn" disabled style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <i class="fa fa-times"></i> Sold Out
                    </button>
                <?php else: ?>
                    <button class="add-to-cart-btn" onclick="openAddToCartModal('<?php echo $id; ?>')" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                <?php endif; ?>
            </div>
            
            <!-- Product Reviews Mini -->
            <div class="product-reviews-mini" id="reviews-mini-<?php echo $id; ?>">
                <div class="rating-stars">★★★★☆</div>
                <span class="review-count">(0 reviews)</span>
            </div>
            
            <!-- Related Products Mini -->
            <div class="related-products-mini" id="related-mini-<?php echo $id; ?>">
                <h4>You might also like:</h4>
                <div class="mini-related-grid" id="mini-related-<?php echo $id; ?>">
                    <!-- Related products will be populated here -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick View Modal -->
    <div id="quickViewModal" class="quick-view-modal">
        <div class="quick-view-content">
            <div class="quick-view-header">
                <h3>Product Details</h3>
                <button class="close-quick-view" onclick="closeQuickView()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="quick-view-body">
                <div class="product-images">
                    <img id="quickViewImage" src="" alt="Product Image" class="main-image">
                    <div class="image-thumbnails" id="imageThumbnails">
                        <!-- Thumbnails will be populated here -->
                    </div>
                </div>
                <div class="product-details">
                    <h2 id="quickViewName"></h2>
                    <div class="price-section">
                        <span id="quickViewPrice" class="price"></span>
                        <span id="quickViewSalePrice" class="sale-price" style="display: none;"></span>
                    </div>
                    
                    <div class="color-section">
                        <h4>Color:</h4>
                        <div class="color-options" id="quickViewColors">
                            <!-- Color options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="size-section">
                        <h4>Size:</h4>
                        <div class="size-options" id="quickViewSizes">
                            <!-- Size options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="quantity-section">
                        <h4>Quantity:</h4>
                        <div class="quantity-controls">
                            <button onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quickViewQuantity" value="1" min="1" max="10">
                            <button onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="add-to-cart-final" onclick="addToCartFromQuickView()">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="wishlist-btn-final" onclick="toggleWishlistFromQuickView()">
                            <i class="fa fa-heart"></i> Wishlist
                        </button>
                    </div>
                    
                    <!-- Reviews Section in Quick View -->
                    <div id="reviews-container-<?php echo $id; ?>" class="reviews-section-quick"></div>
                    
                    <!-- Related Products Section in Quick View -->
                    <div id="related-products-container-<?php echo $id; ?>" class="related-products-section-quick"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add to Cart Modal -->
    <div id="addToCartModal" class="add-to-cart-modal">
        <div class="add-to-cart-content">
            <div class="add-to-cart-header">
                <h3><i class="fas fa-shopping-cart"></i> Add to Cart</h3>
                <button class="close-add-to-cart" onclick="closeAddToCartModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="add-to-cart-body">
                <div class="product-preview">
                    <img id="addToCartProductImage" src="" alt="Product Image" class="preview-image">
                    <div class="product-info-preview">
                        <h2 id="addToCartProductName"></h2>
                        <div class="price-preview">
                            <span id="addToCartProductPrice"></span>
                        </div>
                    </div>
                </div>
                
                <div class="options-section">
                    <!-- Removed selection requirement message -->
                    
                    <div class="option-group">
                        <h4>Color</h4>
                        <div class="color-options" id="addToCartColors">
                            <!-- Color options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <h4>Size</h4>
                        <div class="size-options" id="addToCartSizes">
                            <!-- Size options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <h4>Quantity</h4>
                        <div class="quantity-controls">
                            <button onclick="changeAddToCartQuantity(-1)">-</button>
                            <input type="number" id="addToCartQuantity" value="1" min="1" max="10" readonly>
                            <button onclick="changeAddToCartQuantity(1)">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="summary-section">
                    <div class="summary-item">
                        <span>Selected Color:</span>
                        <span id="addToCartSelectedColor">Please select a color</span>
                    </div>
                    <div class="summary-item">
                        <span>Selected Size:</span>
                        <span id="addToCartSelectedSize">Please select a size</span>
                    </div>
                    <div class="summary-item">
                        <span>Quantity:</span>
                        <span id="addToCartSelectedQuantity">1</span>
                    </div>
                </div>
                
                <div class="action-section">
                    <button class="add-to-cart-final-btn" onclick="addToCartFromModal()" id="addToCartBtn" disabled>
                        <i class="fas fa-shopping-cart"></i>
                        Please Select Options First
                    </button>
                </div>
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

        /* Quick View Modal Styles */
        .quick-view-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            backdrop-filter: blur(5px);
        }

        .quick-view-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .quick-view-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
        }

        .quick-view-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .close-quick-view {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 5px;
        }

        .close-quick-view:hover {
            color: #333;
        }

        .quick-view-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .image-thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .thumbnail.active {
            border-color: #007bff;
        }

        .product-details h2 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.8rem;
        }

        .price-section {
            margin-bottom: 20px;
        }

        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .sale-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
            margin-left: 10px;
        }

        .color-section,
        .size-section,
        .quantity-section {
            margin-bottom: 20px;
        }

        .color-section h4,
        .size-section h4,
        .quantity-section h4 {
            margin: 0 0 10px 0;
            color: #555;
            font-size: 1rem;
        }

        .color-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .color-option.active {
            border-color: #333;
        }

        .size-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .size-option {
            padding: 8px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .size-option:hover {
            border-color: #007bff;
        }

        .size-option.active {
            border-color: #007bff;
            background: #007bff;
            color: white;
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
            font-size: 1.2rem;
        }

        .quantity-controls button:hover {
            background: #f8f9fa;
        }

        .quantity-controls input {
            width: 60px;
            height: 35px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .add-to-cart-final {
            flex: 1;
            padding: 15px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .add-to-cart-final:hover {
            background: #0056b3;
        }

        .wishlist-btn-final {
            padding: 15px 20px;
            background: white;
            color: #666;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .wishlist-btn-final:hover {
            border-color: #007bff;
            color: #007bff;
        }

        .wishlist-btn-final.active {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .quick-view-body {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }

            .main-image {
                height: 300px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        /* Add to Cart Modal Styles */
        .add-to-cart-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .add-to-cart-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .add-to-cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: linear-gradient(135deg, #0066cc 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .add-to-cart-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-add-to-cart {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .close-add-to-cart:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .add-to-cart-body {
            padding: 30px;
        }

        .product-preview {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }

        .product-info-preview h2 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
            color: #333;
        }

        .price-preview {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0066cc;
        }

        .options-section {
            margin-bottom: 30px;
        }

        .selection-required {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
            font-size: 0.9rem;
        }

        .option-group {
            margin-bottom: 25px;
        }

        .option-group h4 {
            margin: 0 0 15px 0;
            color: #555;
            font-size: 1rem;
            font-weight: 500;
        }

        .color-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
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

        .size-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .size-option {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
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

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 10px;
            width: fit-content;
        }

        .quantity-controls button {
            width: 35px;
            height: 35px;
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
            width: 60px;
            height: 35px;
            text-align: center;
            border: none;
            background: white;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .summary-item:last-child {
            margin-bottom: 0;
        }

        .action-section {
            text-align: center;
        }

        .add-to-cart-final-btn {
            width: 100%;
            padding: 15px 30px;
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

        .add-to-cart-final-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .add-to-cart-final-btn:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
            transform: none !important;
            opacity: 0.6 !important;
        }

        .add-to-cart-final-btn:disabled:hover {
            background: #ccc !important;
            transform: none !important;
        }

        @media (max-width: 768px) {
            .add-to-cart-content {
                width: 95%;
                margin: 10% auto;
            }

            .product-preview {
                flex-direction: column;
                text-align: center;
            }

            .preview-image {
                width: 120px;
                height: 120px;
                margin: 0 auto;
            }
        }
    </style>
    
    <script>
        // Add to cart functionality
        function addToCart(productId) {
            // Add your cart logic here
    
            
            // Show notification
            showNotification('Product added to cart!', 'success');
            
            // Update cart count
            updateCartCount();
        }
        
        // Toggle wishlist
        function toggleWishlist(productId) {
    
            
            const btn = event.target.closest('.wishlist-btn');
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                showNotification('Added to wishlist!', 'success');
            } else {
                showNotification('Removed from wishlist!', 'info');
            }
        }
        
        // Global variables for quick view
        let currentProduct = null;
        let selectedColor = null;
        let selectedSize = null;

        // Open quick view modal
        function openQuickView(productId) {
            // Show modal with loading state
            const modal = document.getElementById('quickViewModal');
            modal.style.display = 'block';
            
            // Show loading in modal
            const modalBody = document.querySelector('.quick-view-body');
            modalBody.innerHTML = '<div style="text-align: center; padding: 50px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #007bff;"></i><p>Loading product details...</p></div>';
            
            // Fetch product data
            fetchProductData(productId);
        }

        // Close quick view modal
        function closeQuickView() {
            document.getElementById('quickViewModal').style.display = 'none';
            currentProduct = null;
            selectedColor = null;
            selectedSize = null;
        }

        // Fetch product data for quick view
        async function fetchProductData(productId) {
            try {
                const response = await fetch(`get-product-details.php?id=${productId}`);
                const data = await response.json();
                
                if (data.success) {
                    populateQuickView(data.product);
                } else {
                    showNotification('Error loading product details', 'error');
                }
            } catch (error) {
                console.error('Error fetching product:', error);
                showNotification('Error loading product details', 'error');
            }
        }

        // Populate quick view with product data
        function populateQuickView(product) {
            currentProduct = product;
            
            // Restore modal body content
            const modalBody = document.querySelector('.quick-view-body');
            modalBody.innerHTML = `
                <div class="product-images">
                    <img id="quickViewImage" src="" alt="Product Image" class="main-image">
                    <div class="image-thumbnails" id="imageThumbnails">
                        <!-- Thumbnails will be populated here -->
                    </div>
                </div>
                <div class="product-details">
                    <h2 id="quickViewName"></h2>
                    <div class="price-section">
                        <span id="quickViewPrice" class="price"></span>
                        <span id="quickViewSalePrice" class="sale-price" style="display: none;"></span>
                    </div>
                    
                    <div class="color-section">
                        <h4>Color:</h4>
                        <div class="color-options" id="quickViewColors">
                            <!-- Color options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="size-section">
                        <h4>Size:</h4>
                        <div class="size-options" id="quickViewSizes">
                            <!-- Size options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="quantity-section">
                        <h4>Quantity:</h4>
                        <div class="quantity-controls">
                            <button onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quickViewQuantity" value="1" min="1" max="10">
                            <button onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="add-to-cart-final" onclick="addToCartFromQuickView()">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="wishlist-btn-final" onclick="toggleWishlistFromQuickView()">
                            <i class="fa fa-heart"></i> Wishlist
                        </button>
                    </div>
                </div>
            `;
            
            // Set basic product info
            document.getElementById('quickViewName').textContent = product.name;
            
            // Handle pricing
            const priceElement = document.getElementById('quickViewPrice');
            const salePriceElement = document.getElementById('quickViewSalePrice');
            
            if (product.sale && product.salePrice) {
                priceElement.textContent = `$${parseFloat(product.price).toFixed(2)}`;
                priceElement.style.textDecoration = 'line-through';
                priceElement.style.color = '#999';
                salePriceElement.textContent = `$${parseFloat(product.salePrice).toFixed(2)}`;
                salePriceElement.style.display = 'inline';
            } else {
                priceElement.textContent = `$${parseFloat(product.price).toFixed(2)}`;
                priceElement.style.textDecoration = 'none';
                priceElement.style.color = '#333';
                salePriceElement.style.display = 'none';
            }
            
            // Set main image
            const mainImage = product.images.front || product.images.back || '';
            document.getElementById('quickViewImage').src = mainImage;
            
            // Populate image thumbnails
            const thumbnailContainer = document.getElementById('imageThumbnails');
            thumbnailContainer.innerHTML = '';
            
            if (product.images.front) {
                addThumbnail(product.images.front, thumbnailContainer, true);
            }
            if (product.images.back) {
                addThumbnail(product.images.back, thumbnailContainer, false);
            }
            
            // Populate color options
            const colorContainer = document.getElementById('quickViewColors');
            colorContainer.innerHTML = '';
            
            const colors = product.colors || ['#000000'];
            colors.forEach((color, index) => {
                const colorOption = document.createElement('div');
                colorOption.className = 'color-option' + (index === 0 ? ' active' : '');
                colorOption.style.backgroundColor = color;
                colorOption.onclick = () => selectColor(color, colorOption);
                colorContainer.appendChild(colorOption);
            });
            
            // Populate size options
            const sizeContainer = document.getElementById('quickViewSizes');
            sizeContainer.innerHTML = '';
            
            const sizes = product.sizes || ['S', 'M', 'L'];
            const defaultSizeIndex = Math.floor(sizes.length / 2); // Default to middle size
            
            sizes.forEach((size, index) => {
                const sizeOption = document.createElement('div');
                sizeOption.className = 'size-option' + (index === defaultSizeIndex ? ' active' : '');
                sizeOption.textContent = size;
                sizeOption.onclick = () => selectSize(size, sizeOption);
                sizeContainer.appendChild(sizeOption);
            });
            
            // Set default selections
            selectedColor = colors[0];
            selectedSize = sizes[defaultSizeIndex];
            
            // Show modal
            document.getElementById('quickViewModal').style.display = 'block';
        }

        // Add thumbnail image
        function addThumbnail(imageSrc, container, isActive = false) {
            const thumbnail = document.createElement('img');
            thumbnail.src = imageSrc;
            thumbnail.className = 'thumbnail' + (isActive ? ' active' : '');
            thumbnail.onclick = () => {
                document.getElementById('quickViewImage').src = imageSrc;
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            };
            container.appendChild(thumbnail);
        }

        // Select color
        function selectColor(color, element) {
            selectedColor = color;
            
            // Update active state
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
        }

        // Select size
        function selectSize(size, element) {
            selectedSize = size;
            
            // Update active state
            document.querySelectorAll('.size-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
        }

        // Change quantity
        function changeQuantity(change) {
            const quantityInput = document.getElementById('quickViewQuantity');
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity = Math.max(1, Math.min(10, currentQuantity + change));
            quantityInput.value = currentQuantity;
        }

        // Add to cart from quick view
        function addToCartFromQuickView() {
            if (!currentProduct) return;
            
            const quantity = parseInt(document.getElementById('quickViewQuantity').value);
            
            // Removed color and size validation - no prompts
            
            // Add to cart with selected options
            addToCart(currentProduct.id, {
                color: selectedColor,
                size: selectedSize,
                quantity: quantity
            });
            
            // Close modal
            closeQuickView();
        }

        // Toggle wishlist from quick view
        function toggleWishlistFromQuickView() {
            const btn = document.querySelector('.wishlist-btn-final');
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                showNotification('Added to wishlist!', 'success');
            } else {
                showNotification('Removed from wishlist!', 'info');
            }
        }

        // Updated add to cart function
        function addToCart(productId, options = {}) {
            // Add your cart logic here with options
    
            
            // Show notification
            showNotification('Product added to cart!', 'success');
            
            // Update cart count
            updateCartCount();
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

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('quickViewModal');
            const modalContent = document.querySelector('.quick-view-content');
            
            if (event.target === modal) {
                closeQuickView();
            }
            
            const addToCartModal = document.getElementById('addToCartModal');
            const addToCartContent = document.querySelector('.add-to-cart-content');
            
            if (event.target === addToCartModal) {
                closeAddToCartModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('quickViewModal');
                if (modal.style.display === 'block') {
                    closeQuickView();
                }
                const addToCartModal = document.getElementById('addToCartModal');
                if (addToCartModal.style.display === 'block') {
                    closeAddToCartModal();
                }
            }
        });

        // Add to Cart Modal Functions
        function openAddToCartModal(productId) {
            // Set product ID in modal for later use
            document.getElementById('addToCartModal').setAttribute('data-product-id', productId);
            
            // Fetch product details
            fetch(`get-product-details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        populateAddToCartModal(product);
                        document.getElementById('addToCartModal').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    } else {
                        showNotification('Error loading product details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error loading product details', 'error');
                });
        }

        function closeAddToCartModal() {
            document.getElementById('addToCartModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            // Reset selections
            window.selectedColor = '';
            window.selectedSize = '';
            window.selectedQuantity = 1;
        }

        function populateAddToCartModal(product) {
            // Set product details
            document.getElementById('addToCartProductName').textContent = product.name;
            document.getElementById('addToCartProductImage').src = product.front_image || product.image_front || '';
            document.getElementById('addToCartProductPrice').textContent = `$${parseFloat(product.price).toFixed(2)}`;
            
            // Populate colors
            const colorContainer = document.getElementById('addToCartColors');
            colorContainer.innerHTML = '';
            const colors = product.colors || ['#000000'];
            colors.forEach(color => {
                const colorDiv = document.createElement('div');
                colorDiv.className = 'color-option';
                colorDiv.style.backgroundColor = color;
                colorDiv.onclick = () => selectAddToCartColor(colorDiv, color);
                colorContainer.appendChild(colorDiv);
            });
            
            // Populate sizes
            const sizeContainer = document.getElementById('addToCartSizes');
            sizeContainer.innerHTML = '';
            const sizes = product.sizes || ['S', 'M', 'L'];
            sizes.forEach(size => {
                const sizeDiv = document.createElement('div');
                sizeDiv.className = 'size-option';
                sizeDiv.textContent = size;
                sizeDiv.onclick = () => selectAddToCartSize(sizeDiv, size);
                sizeContainer.appendChild(sizeDiv);
            });
            
            // Reset selections
            window.selectedColor = '';
            window.selectedSize = '';
            window.selectedQuantity = 1;
            updateAddToCartSummary();
        }

        function selectAddToCartColor(element, color) {
            document.querySelectorAll('#addToCartColors .color-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            window.selectedColor = color;
            updateAddToCartSummary();
        }

        function selectAddToCartSize(element, size) {
            document.querySelectorAll('#addToCartSizes .size-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            window.selectedSize = size;
            updateAddToCartSummary();
        }

        function changeAddToCartQuantity(change) {
            const newQuantity = Math.max(1, Math.min(10, window.selectedQuantity + change));
            window.selectedQuantity = newQuantity;
            document.getElementById('addToCartQuantity').value = newQuantity;
            updateAddToCartSummary();
        }

        function updateAddToCartSummary() {
            document.getElementById('addToCartSelectedColor').textContent = window.selectedColor || 'Please select a color';
            document.getElementById('addToCartSelectedSize').textContent = window.selectedSize || 'Please select a size';
            document.getElementById('addToCartSelectedQuantity').textContent = window.selectedQuantity;
            
            // Enable/disable add to cart button
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (window.selectedColor && window.selectedSize) {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Please Select Options First';
            }
        }

        function addToCartFromModal() {
            // Removed color and size validation - no prompts
            
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            // Get product ID from the modal
            const productId = document.getElementById('addToCartModal').getAttribute('data-product-id');
            
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}&quantity=${window.selectedQuantity}&color=${window.selectedColor}&size=${window.selectedSize}&return_url=${encodeURIComponent(window.location.href)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product added to cart successfully!', 'success');
                    closeAddToCartModal();
                    updateCartCount();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding product to cart', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // Load reviews and related products for product cards
        function loadProductFeatures(productId, category = 'General', subcategory = 'General') {
            // Load reviews
            const reviewsContainer = document.getElementById(`reviews-container-${productId}`);
            if (reviewsContainer && typeof reviewsManager !== 'undefined') {
                reviewsManager.renderReviews(productId, reviewsContainer);
            }
            
            // Load related products
            const relatedContainer = document.getElementById(`related-products-container-${productId}`);
            if (relatedContainer && typeof relatedProductsManager !== 'undefined') {
                relatedProductsManager.renderRelatedProducts(productId, relatedContainer, category, subcategory);
            }
            
            // Update mini reviews display
            const reviewsMini = document.getElementById(`reviews-mini-${productId}`);
            if (reviewsMini && typeof reviewsManager !== 'undefined') {
                // Load a few reviews to show rating
                reviewsManager.loadReviews(productId).then(reviews => {
                    if (reviews.length > 0) {
                        const avgRating = reviews.reduce((sum, review) => sum + review.rating, 0) / reviews.length;
                        const stars = '★'.repeat(Math.floor(avgRating)) + '☆'.repeat(5 - Math.floor(avgRating));
                        reviewsMini.querySelector('.rating-stars').textContent = stars;
                        reviewsMini.querySelector('.review-count').textContent = `(${reviews.length} reviews)`;
                    }
                });
            }
            
            // Update mini related products
            const relatedMini = document.getElementById(`mini-related-${productId}`);
            if (relatedMini && typeof relatedProductsManager !== 'undefined') {
                relatedProductsManager.loadRelatedProducts(productId, category, subcategory).then(products => {
                    if (products.length > 0) {
                        const miniProducts = products.slice(0, 3); // Show only 3 mini products
                        relatedMini.innerHTML = miniProducts.map(product => `
                            <div class="mini-product-card" onclick="openQuickView('${product._id}')">
                                <img src="${product.front_image || 'https://picsum.photos/100/60?random=30'}" 
                                     alt="${product.name}"
                                     onerror="this.src='https://picsum.photos/100/60?random=${Math.floor(Math.random() * 100)}'">
                                <h5>${product.name}</h5>
                                <span class="price">$${product.price.toFixed(2)}</span>
                            </div>
                        `).join('');
                    }
                });
            }
        }

        // Auto-load features when product cards are displayed
        document.addEventListener('DOMContentLoaded', function() {
            // Find all product cards and load their features
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const productId = card.getAttribute('data-product-id');
                if (productId) {
                    // Load features after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        loadProductFeatures(productId);
                    }, 100);
                }
            });
        });
    </script>
    
    <?php
}
?> 