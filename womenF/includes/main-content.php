<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Get products based on subcategory or all women's clothing
if ($subcategory) {
    $products = $productModel->getBySubcategory(ucfirst($subcategory));
    $pageTitle = ucfirst($subcategory);
} else {
    // Get all women's clothing products
    $products = $productModel->getByCategory("Women's Clothing");
    $pageTitle = "Women's Clothing";
}

// Get all dresses from the database
$dresses = $productModel->getBySubcategory('Dresses');

// Get all tops from the database
$tops = $productModel->getBySubcategory('Tops');

// Debug: Show what's in the database
if (!empty($products)) {
    echo "<!-- DEBUG: First product from database -->\n";
    echo "<!-- Product: " . htmlspecialchars(json_encode($products[0])) . " -->\n";
    echo "<!-- Color field: " . htmlspecialchars($products[0]['color'] ?? 'NULL') . " -->\n";
    echo "<!-- Color variants: " . htmlspecialchars(json_encode($products[0]['color_variants'] ?? [])) . " -->\n";
}



?>

<!-- Main Content Section -->
<main class="main-content">
    <!-- Products Section -->
    <div class="content-header" id="products-section">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select-dresses">Sort:</label>
                <select id="sort-select-dresses" class="sort-select">
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
                     
                     <!-- DEBUG: Raw color data -->
                     <!-- Color: <?php echo htmlspecialchars($product['color'] ?? 'NULL'); ?> -->
                     <!-- Color Variants: <?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?> -->
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-size-category="<?php echo htmlspecialchars($product['size_category'] ?? ''); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? $product['variants'] ?? [])); ?>"
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
                        <button class="heart-button" data-product-id="<?php echo $product['_id']; ?>">
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
                            $hasColorVariants = !empty($product['color_variants']);
                            $isFirstColor = true;
                            
                            // Main product color
                            if (!empty($product['color'])): ?>
                                <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                      style="background-color: <?php echo htmlspecialchars($product['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($product['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($product['color']); ?>"></span>
                                <?php $isFirstColor = false; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($product['color_variants'])):
                                foreach ($product['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                        <?php $isFirstColor = false; ?>
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
                <p>No products found in this category.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="product-grid" id="dresses-grid">
        <?php if (!empty($dresses)): ?>
            
            <?php foreach ($dresses as $index => $dress): ?>
                <div class="product-card" 
                     data-product-id="<?php echo $dress['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($dress['name']); ?>"
                     data-product-price="<?php echo $dress['price']; ?>"
                     data-product-sale-price="<?php echo $dress['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($dress['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($dress['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($dress['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($dress['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($dress['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $dress['stock'] ?? 0; ?>"
                     data-product-rating="<?php echo $dress['rating'] ?? 0; ?>"
                     data-product-review-count="<?php echo $dress['review_count'] ?? 0; ?>"
                     data-product-front-image="<?php echo htmlspecialchars($dress['front_image'] ?? $dress['image_front'] ?? ''); ?>"
                     data-product-back-image="<?php echo htmlspecialchars($dress['back_image'] ?? $dress['image_back'] ?? ''); ?>"
                     data-product-color="<?php echo htmlspecialchars($dress['color'] ?? ''); ?>"
                     data-product-images="<?php echo htmlspecialchars(json_encode($dress['images'] ?? [])); ?>"
                     data-product-color-variants="<?php echo htmlspecialchars(json_encode($dress['color_variants'] ?? [])); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($dress['sizes'] ?? $dress['selected_sizes'] ?? [])); ?>"
                     data-product-size-category="<?php echo htmlspecialchars($dress['size_category'] ?? ''); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($dress['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($dress['color_variants'] ?? $dress['variants'] ?? [])); ?>"
                     data-product-product-variants="<?php echo htmlspecialchars(json_encode($dress['product_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($dress['options'] ?? [])); ?>"
                     data-product-product-options="<?php echo htmlspecialchars(json_encode($dress['product_options'] ?? [])); ?>"
                     data-product-image-front="<?php echo htmlspecialchars($dress['image_front'] ?? ''); ?>"
                     data-product-image-back="<?php echo htmlspecialchars($dress['image_back'] ?? ''); ?>">
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
                            
                            if ($frontImage): 
                                $frontExtension = pathinfo($frontImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($dress['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($dress['color']); ?>"
                                           muted
                                           loop
                                           onerror="console.error('Failed to load video:', this.src);"
>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                         alt="<?php echo htmlspecialchars($dress['name']); ?> - Front" 
                                         class="active" 
                                         data-color="<?php echo htmlspecialchars($dress['color']); ?>"
                                         onerror="console.error('Failed to load image:', this.src);"
>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($backImage): 
                                $backExtension = pathinfo($backImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($dress['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($dress['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                         alt="<?php echo htmlspecialchars($dress['name']); ?> - Back" 
                                         data-color="<?php echo htmlspecialchars($dress['color']); ?>">
                                <?php endif; ?>
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
                                    
                                    if ($variantFrontImage): 
                                        $variantFrontExtension = pathinfo($variantFrontImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantFrontExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop
                                                   onerror="console.error('Failed to load variant video:', this.src);"
>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                 onerror="console.error('Failed to load variant image:', this.src);"
>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): 
                                        $variantBackExtension = pathinfo($variantBackImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantBackExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($dress['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button" data-product-id="<?php echo $product['_id']; ?>">
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
                            $hasColorVariants = !empty($dress['color_variants']);
                            $isFirstColor = true;
                            
                            // Main product color
                            if (!empty($dress['color'])): ?>
                                <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                      style="background-color: <?php echo htmlspecialchars($dress['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($dress['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($dress['color']); ?>"></span>
                                <?php $isFirstColor = false; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($dress['color_variants'])):
                                foreach ($dress['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                        <?php $isFirstColor = false; ?>
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

    <!-- Tops Section -->
    <div class="content-header" id="tops-section" style="margin-top: 60px;">
        <h1 class="page-title">Tops</h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select-tops">Sort:</label>
                <select id="sort-select-tops" class="sort-select">
                    <option value="featured" selected>Featured</option>
                    <option value="newest">Newest</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
           
        </div>
    </div>

    <div class="product-grid" id="tops-grid">
        <?php if (!empty($tops)): ?>
            
            <?php foreach ($tops as $index => $top): ?>
                <div class="product-card" 
                     data-product-id="<?php echo $top['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($top['name']); ?>"
                     data-product-price="<?php echo $top['price']; ?>"
                     data-product-sale-price="<?php echo $top['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($top['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($top['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($top['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($top['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($top['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $top['stock'] ?? 0; ?>"
                     data-product-rating="<?php echo $top['rating'] ?? 0; ?>"
                     data-product-review-count="<?php echo $top['review_count'] ?? 0; ?>"
                     data-product-front-image="<?php echo htmlspecialchars($top['front_image'] ?? $top['image_front'] ?? ''); ?>"
                     data-product-back-image="<?php echo htmlspecialchars($top['back_image'] ?? $top['image_back'] ?? ''); ?>"
                     data-product-color="<?php echo htmlspecialchars($top['color'] ?? ''); ?>"
                     data-product-images="<?php echo htmlspecialchars(json_encode($top['images'] ?? [])); ?>"
                     data-product-color-variants="<?php echo htmlspecialchars(json_encode($top['color_variants'] ?? [])); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($top['sizes'] ?? $top['selected_sizes'] ?? [])); ?>"
                     data-product-size-category="<?php echo htmlspecialchars($top['size_category'] ?? ''); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($top['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($top['color_variants'] ?? $top['variants'] ?? [])); ?>"
                     data-product-product-variants="<?php echo htmlspecialchars(json_encode($top['product_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($top['options'] ?? [])); ?>"
                     data-product-product-options="<?php echo htmlspecialchars(json_encode($top['product_options'] ?? [])); ?>"
                     data-product-image-front="<?php echo htmlspecialchars($top['image_front'] ?? ''); ?>"
                     data-product-image-back="<?php echo htmlspecialchars($top['image_back'] ?? ''); ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            // Main product images
                            $frontImage = $top['front_image'] ?? $top['image_front'] ?? '';
                            $backImage = $top['back_image'] ?? $top['image_back'] ?? '';
                            
                            // If no back image, use front image for both
                            if (empty($backImage) && !empty($frontImage)) {
                                $backImage = $frontImage;
                            }
                            
                            if ($frontImage): 
                                $frontExtension = pathinfo($frontImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($top['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($top['color']); ?>"
                                           muted
                                           loop
                                           onerror="console.error('Failed to load video:', this.src);"
>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                         alt="<?php echo htmlspecialchars($top['name']); ?> - Front" 
                                         class="active" 
                                         data-color="<?php echo htmlspecialchars($top['color']); ?>"
                                         onerror="console.error('Failed to load image:', this.src);"
>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($backImage): 
                                $backExtension = pathinfo($backImage, PATHINFO_EXTENSION);
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($top['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($top['color']); ?>"
                                           muted
                                           loop>
                                    </video>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                         alt="<?php echo htmlspecialchars($top['name']); ?> - Back" 
                                         data-color="<?php echo htmlspecialchars($top['color']); ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant images
                            if (!empty($top['color_variants'])):
                                foreach ($top['color_variants'] as $variant):
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
                                                   alt="<?php echo htmlspecialchars($top['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop
                                                   onerror="console.error('Failed to load variant video:', this.src);"
>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($top['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                 onerror="console.error('Failed to load variant image:', this.src);"
>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): 
                                        $variantBackExtension = pathinfo($variantBackImage, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($variantBackExtension), ['mp4', 'webm', 'mov'])): ?>
                                            <video src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                   alt="<?php echo htmlspecialchars($top['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                   data-color="<?php echo htmlspecialchars($variant['color']); ?>"
                                                   muted
                                                   loop>
                                            </video>
                                        <?php else: ?>
                                            <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($top['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                                 data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="heart-button" data-product-id="<?php echo $product['_id']; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $top['_id']; ?>">Quick View</button>
                            <?php if (($top['available'] ?? true) === false): ?>
                                <button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>
                            <?php else: ?>
                                <button class="add-to-bag">Add To Bag</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="color-options">
                            <?php 
                            $hasColorVariants = !empty($top['color_variants']);
                            $isFirstColor = true;
                            
                            // Main product color
                            if (!empty($top['color'])): ?>
                                <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                      style="background-color: <?php echo htmlspecialchars($top['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($top['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($top['color']); ?>"></span>
                                <?php $isFirstColor = false; ?>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($top['color_variants'])):
                                foreach ($top['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle <?php echo $isFirstColor ? 'active' : ''; ?>" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                        <?php $isFirstColor = false; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($top['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($top['price'], 0); ?></div>
                        <?php if (($top['available'] ?? true) === false): ?>
                            <div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>
                        <?php elseif (($top['stock'] ?? 0) <= 5 && ($top['stock'] ?? 0) > 0): ?>
                            <div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only <?php echo $top['stock']; ?> left</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No tops available at the moment.</p>
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
                <img id="quick-view-main-image" src="" alt="Product Media">
                <video id="quick-view-main-video" src="" muted loop style="display: none; max-width: 100%; border-radius: 8px;"></video>
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
                <span class="stars" id="quick-view-stars"></span>
                <span class="review-count" id="quick-view-review-count"></span>
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
                <p id="quick-view-description"></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div> 
