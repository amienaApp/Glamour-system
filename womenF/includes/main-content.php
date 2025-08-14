<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get all dresses from the database
$dresses = $productModel->getBySubcategory('Dresses');

// Debug output
echo "<!-- DEBUG: Found " . count($dresses) . " dresses -->";
?>



<!-- Main Content Section -->
<main class="main-content">
    <div class="content-header">
        <h1 class="page-title">Dresses</h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select">Sort:</label>
                <select id="sort-select" class="sort-select">
                    <option value="featured" selected>Featured</option>
                    <option value="newest">Newest</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
            <div class="view-control">
                <span>View:</span>
                <a href="#" class="view-option active">60</a>
                <span>|</span>
                <a href="#" class="view-option">120</a>
            </div>
        </div>
    </div>

    <div class="product-grid">
        <?php if (!empty($dresses)): ?>
            <?php echo "<!-- DEBUG: Rendering " . count($dresses) . " dresses -->"; ?>
            <?php foreach ($dresses as $index => $dress): ?>
                <div class="product-card" data-product-id="<?php echo $dress['_id']; ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            // Main product images
                            $frontImage = $dress['front_image'] ?? $dress['image_front'] ?? '';
                            $backImage = $dress['back_image'] ?? $dress['image_back'] ?? '';
                            
                            // If no back image, use front image for both
                            if (empty($backImage) && !empty($frontImage)) {
                                $backImage = $frontImage;
                            }
                            
                            if ($frontImage): ?>
                                <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                     alt="<?php echo htmlspecialchars($dress['name']); ?> - Front" 
                                     class="active" 
                                     data-color="<?php echo htmlspecialchars($dress['color']); ?>">
                            <?php endif; ?>
                            
                            <?php if ($backImage): ?>
                                <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                     alt="<?php echo htmlspecialchars($dress['name']); ?> - Back" 
                                     data-color="<?php echo htmlspecialchars($dress['color']); ?>">
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant images
                            if (!empty($dress['color_variants'])):
                                foreach ($dress['color_variants'] as $variant):
                                    $variantFrontImage = $variant['front_image'] ?? '';
                                    $variantBackImage = $variant['back_image'] ?? '';
                                    
                                    // If no back image for variant, use front image for both
                                    if (empty($variantBackImage) && !empty($variantFrontImage)) {
                                        $variantBackImage = $variantFrontImage;
                                    }
                                    
                                    if ($variantFrontImage): ?>
                                        <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                             alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                             data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): ?>
                                        <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                             alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                             data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $dress['_id']; ?>">Quick View</button>
                            <?php if (($dress['available'] ?? true) === false): ?>
                                <button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>
                            <?php else: ?>
                                <button class="add-to-bag">Add To Bag</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="color-options">
                            <?php 
                            // Main product color
                            if (!empty($dress['color'])): ?>
                                <span class="color-circle <?php echo $index === 0 ? 'active' : ''; ?>" 
                                      style="background-color: <?php echo htmlspecialchars($dress['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($dress['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($dress['color']); ?>"></span>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($dress['color_variants'])):
                                foreach ($dress['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($dress['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($dress['price'], 0); ?></div>
                        <?php if (($dress['available'] ?? true) === false): ?>
                            <div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>
                        <?php elseif (($dress['stock'] ?? 0) <= 5 && ($dress['stock'] ?? 0) > 0): ?>
                            <div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only <?php echo $dress['stock']; ?> left</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No dresses available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Quick View Sidebar -->
<div class="quick-view-sidebar" id="quick-view-sidebar">
    <div class="quick-view-header">
        <button class="close-quick-view" id="close-quick-view">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="quick-view-content">
        <!-- Product Images -->
        <div class="quick-view-images">
            <div class="main-image-container">
                <img id="quick-view-main-image" src="" alt="Product Image">
            </div>
            <div class="thumbnail-images" id="quick-view-thumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="quick-view-details">
            <h2 id="quick-view-title"></h2>
            <div class="quick-view-price" id="quick-view-price"></div>
            <div class="quick-view-reviews">
                <span class="stars">★★★★★</span>
                <span class="review-count">(0 Reviews)</span>
            </div>
            
            <!-- Color Selection -->
            <div class="quick-view-colors">
                <h4>Color</h4>
                <div class="color-selection" id="quick-view-color-selection">
                    <!-- Colors will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Size Selection -->
            <div class="quick-view-sizes">
                <h4>Size</h4>
                <div class="size-selection" id="quick-view-size-selection">
                    <!-- Sizes will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="quick-view-actions">
                <button class="add-to-bag-quick" id="add-to-bag-quick">
                    <i class="fas fa-shopping-bag"></i>
                    Add to Bag
                </button>
                <button class="add-to-wishlist-quick" id="add-to-wishlist-quick">
                    <i class="fas fa-heart"></i>
                    + Wishlist
                </button>
            </div>
            
            <!-- Availability Status -->
            <div class="quick-view-availability" id="quick-view-availability" style="margin-top: 15px; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600;">
                <!-- Availability will be populated by JavaScript -->
            </div>
            
            <!-- Product Description -->
            <div class="quick-view-description">
                <p id="quick-view-description">A beautiful dress perfect for any occasion. Features a flattering fit and comfortable fabric.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div> 
