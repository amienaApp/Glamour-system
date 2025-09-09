<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Get products based on subcategory or all beauty products
if ($subcategory) {
    // Use the subcategory as-is since it's already properly capitalized from the header
    $products = $productModel->getBySubcategory($subcategory);
    $pageTitle = $subcategory;
} else {
    // Get ALL beauty products from the database
    $products = $productModel->getByCategory("Beauty & Cosmetics");
    $pageTitle = "Beauty & Cosmetics";
}

// Get all makeup products from the database
$makeup = $productModel->getBySubcategory('Makeup');

// Get all skincare products from the database
$skincare = $productModel->getBySubcategory('Skincare');

?>

<!-- Main Content Section -->
<main class="main-content">
    <!-- Products Section -->
    <div class="content-header" id="products-section">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select-beauty">Sort:</label>
                <select id="sort-select-beauty" class="sort-select">
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

    <?php if ($subcategory): ?>
    <!-- Filtered Products Grid -->
    <div class="product-grid" id="filtered-products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card" 
                     data-product-id="<?php echo $product['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-price="<?php echo $product['price']; ?>"
                     data-product-sale-price="<?php echo $product['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($product['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($product['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $product['stock'] ?? 0; ?>"
                     data-product-rating="<?php echo $product['rating'] ?? 0; ?>"
                     data-product-review-count="<?php echo $product['review_count'] ?? 0; ?>"
                     data-product-front-image="<?php echo htmlspecialchars($product['front_image'] ?? $product['image_front'] ?? ''); ?>"
                     data-product-back-image="<?php echo htmlspecialchars($product['back_image'] ?? $product['image_back'] ?? ''); ?>"
                     data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                     data-product-images="<?php echo htmlspecialchars(json_encode($product['images'] ?? [])); ?>"
                     data-product-color-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-size-category="<?php echo htmlspecialchars($product['size_category'] ?? ''); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['variants'] ?? [])); ?>"
                     data-product-product-variants="<?php echo htmlspecialchars(json_encode($product['product_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-product-product-options="<?php echo htmlspecialchars(json_encode($product['product_options'] ?? [])); ?>"
                     data-product-image-front="<?php echo htmlspecialchars($product['image_front'] ?? ''); ?>"
                     data-product-image-back="<?php echo htmlspecialchars($product['image_back'] ?? ''); ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            // Main product images
                            $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
                            $backImage = $product['back_image'] ?? $product['image_back'] ?? '';
                            
                            // If no back image, use front image for both
                            if (empty($backImage) && !empty($frontImage)) {
                                $backImage = $frontImage;
                            }
                            
                            if ($frontImage): 
                                $frontExtension = pathinfo($frontImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                         class="active" 
                                         data-color="<?php echo htmlspecialchars($product['color']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($backImage): 
                                $backExtension = pathinfo($backImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                         data-color="<?php echo htmlspecialchars($product['color']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant images
                            if (!empty($product['color_variants'])):
                                foreach ($product['color_variants'] as $variant):
                                    $variantFrontImage = $variant['front_image'] ?? '';
                                    $variantBackImage = $variant['back_image'] ?? '';
                                    
                                    // If no back image for variant, use front image for both
                                    if (empty($variantBackImage) && !empty($variantFrontImage)) {
                                        $variantBackImage = $variantFrontImage;
                                    }
                                    
                                    if ($variantFrontImage): 
                                        $variantFrontExtension = pathinfo($variantFrontImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantFrontExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): 
                                        $variantBackExtension = pathinfo($variantBackImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantBackExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <?php if (($product['available'] ?? true) === false): ?>
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
                            if (!empty($product['color'])): ?>
                                <span class="color-circle active" 
                                      style="background-color: <?php echo htmlspecialchars($product['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($product['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($product['color']); ?>"></span>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($product['color_variants'])):
                                foreach ($product['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 0); ?></div>
                        <?php if (($product['available'] ?? true) === false): ?>
                            <div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>
                        <?php elseif (($product['stock'] ?? 0) <= 5 && ($product['stock'] ?? 0) > 0): ?>
                            <div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only <?php echo $product['stock']; ?> left</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <h3>No products found</h3>
                <p>We couldn't find any products in this category.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- All Beauty Products Grid -->
    <div class="product-grid" id="all-products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card" 
                     data-product-id="<?php echo $product['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-price="<?php echo $product['price']; ?>"
                     data-product-sale-price="<?php echo $product['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($product['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($product['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $product['stock'] ?? 0; ?>"
                     data-product-rating="<?php echo $product['rating'] ?? 0; ?>"
                     data-product-review-count="<?php echo $product['review_count'] ?? 0; ?>"
                     data-product-front-image="<?php echo htmlspecialchars($product['front_image'] ?? $product['image_front'] ?? ''); ?>"
                     data-product-back-image="<?php echo htmlspecialchars($product['back_image'] ?? $product['image_back'] ?? ''); ?>"
                     data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                     data-product-images="<?php echo htmlspecialchars(json_encode($product['images'] ?? [])); ?>"
                     data-product-color-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-size-category="<?php echo htmlspecialchars($product['size_category'] ?? ''); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['variants'] ?? [])); ?>"
                     data-product-product-variants="<?php echo htmlspecialchars(json_encode($product['product_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-product-product-options="<?php echo htmlspecialchars(json_encode($product['product_options'] ?? [])); ?>"
                     data-product-image-front="<?php echo htmlspecialchars($product['image_front'] ?? ''); ?>"
                     data-product-image-back="<?php echo htmlspecialchars($product['image_back'] ?? ''); ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            // Main product images
                            $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
                            $backImage = $product['back_image'] ?? $product['image_back'] ?? '';
                            
                            // If no back image, use front image for both
                            if (empty($backImage) && !empty($frontImage)) {
                                $backImage = $frontImage;
                            }
                            
                            if ($frontImage): 
                                $frontExtension = pathinfo($frontImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                         class="active" 
                                         data-color="<?php echo htmlspecialchars($product['color']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($backImage): 
                                $backExtension = pathinfo($backImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                         data-color="<?php echo htmlspecialchars($product['color']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant images
                            if (!empty($product['color_variants'])):
                                foreach ($product['color_variants'] as $variant):
                                    $variantFrontImage = $variant['front_image'] ?? '';
                                    $variantBackImage = $variant['back_image'] ?? '';
                                    
                                    // If no back image for variant, use front image for both
                                    if (empty($variantBackImage) && !empty($variantFrontImage)) {
                                        $variantBackImage = $variantFrontImage;
                                    }
                                    
                                    if ($variantFrontImage): 
                                        $variantFrontExtension = pathinfo($variantFrontImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantFrontExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): 
                                        $variantBackExtension = pathinfo($variantBackImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantBackExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <?php if (($product['available'] ?? true) === false): ?>
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
                            if (!empty($product['color'])): ?>
                                <span class="color-circle active" 
                                      style="background-color: <?php echo htmlspecialchars($product['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($product['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($product['color']); ?>"></span>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($product['color_variants'])):
                                foreach ($product['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 0); ?></div>
                        <?php if (($product['available'] ?? true) === false): ?>
                            <div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>
                        <?php elseif (($product['stock'] ?? 0) <= 5 && ($product['stock'] ?? 0) > 0): ?>
                            <div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only <?php echo $product['stock']; ?> left</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <h3>No products found</h3>
                <p>We couldn't find any beauty products.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>

<!-- Quick View Sidebar -->
<div class="quick-view-sidebar" id="quick-view-sidebar">
    <div class="quick-view-header">
        <button class="close-quick-view" id="close-quick-view">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="quick-view-content">
        <!-- Product Media -->
        <div class="quick-view-images">
            <div class="main-image-container">
                <img id="quick-view-main-image" src="" alt="" class="main-image">
                <div class="image-thumbnails" id="quick-view-thumbnails"></div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="quick-view-info">
            <h2 id="quick-view-title" class="product-title"></h2>
            <div class="product-price" id="quick-view-price"></div>
            
            <!-- Color Selection -->
            <div class="quick-view-color-selection" id="quick-view-color-selection">
                <h4>Color</h4>
                <div class="color-options"></div>
            </div>
            
            <!-- Size Selection -->
            <div class="quick-view-size-selection" id="quick-view-size-selection">
                <h4>Size</h4>
                <div class="size-options"></div>
            </div>
            
            <!-- Product Description -->
            <div class="product-description" id="quick-view-description"></div>
            
            <!-- Action Buttons -->
            <div class="quick-view-actions">
                <button class="add-to-bag-quick" id="add-to-bag-quick">
                    <i class="fas fa-shopping-bag"></i>
                    Add to Bag
                </button>
                <button class="add-to-wishlist-quick" id="add-to-wishlist-quick">
                    <i class="fas fa-heart"></i>
                    Add to Wishlist
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div>
