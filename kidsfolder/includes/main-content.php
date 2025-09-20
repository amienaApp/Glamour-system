<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Get sort parameter
$sort = $_GET['sort'] ?? 'newest';

// Build sort options
$sortOptions = [];
switch ($sort) {
    case 'newest':
        $sortOptions = ['_id' => -1]; // Descending order by ID - newest first
        break;
    case 'price-low':
        $sortOptions = ['price' => 1];
        break;
    case 'price-high':
        $sortOptions = ['price' => -1];
        break;
    case 'popular':
        $sortOptions = ['featured' => -1, '_id' => -1];
        break;
    default: // newest
        $sortOptions = ['_id' => -1]; // Descending order by ID - newest first
        break;
}

// Get products based on subcategory or all kids clothing products
if ($subcategory) {
    // Use the subcategory as-is since it's already properly capitalized from the header
    $products = $productModel->getBySubcategory($subcategory, $sortOptions);
    $pageTitle = $subcategory;
} else {
    // Get ALL kids clothing products from the database
    $products = $productModel->getByCategory("Kids' Clothing", $sortOptions);
    $pageTitle = "Kids' Clothing";
}

// Get all boys products from the database
$boys = $productModel->getBySubcategory('Boys');

// Get all girls products from the database
$girls = $productModel->getBySubcategory('Girls');

?>

<!-- Main Content Section -->
<main class="main-content">
    <!-- Products Section -->
    <div class="content-header" id="products-section">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="content-controls">
            <!-- Mobile Filter Button -->
            <button class="mobile-filter-btn" id="mobile-filter-btn">
                <i class="fas fa-filter"></i>
                <span>Filters</span>
            </button>
            
            <div class="sort-control">
                <label for="sort-select-kids">Sort:</label>
                <select id="sort-select-kids" class="sort-select" onchange="updateSort(this.value)">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                </select>
            </div>
        </div>
    </div>

    <?php if ($subcategory): ?>
    <!-- Filtered Products Grid -->
    <div class="product-grid" id="filtered-products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <?php
                // Determine stock status
                $stock = (int)($product['stock'] ?? 0);
                $isSoldOut = $stock <= 0;
                $isLowStock = $stock > 0 && $stock <= 7;
                ?>
                <div class="product-card <?php echo $isSoldOut ? 'sold-out' : ''; ?>" 
                     data-product-id="<?php echo $product['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-product-price="<?php echo $product['price']; ?>"
                     data-product-sale-price="<?php echo $product['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($product['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($product['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $product['stock'] ?? 0; ?>"
                     data-product-available="<?php echo ($product['available'] ?? true) ? 'true' : 'false'; ?>"
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
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov', 'avi', 'mkv'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop
                                           controls>
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
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov', 'avi', 'mkv'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop
                                           controls>
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
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <?php if ($isSoldOut): ?>
                                <button class="add-to-bag sold-out-btn" disabled>Sold Out</button>
                            <?php else: ?>
                                <button class="add-to-bag" 
                                        data-product-id="<?php echo $product['_id']; ?>"
                                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-product-price="<?php echo htmlspecialchars($product['price']); ?>"
                                        data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                                        data-product-stock="<?php echo $stock; ?>">Add To Bag</button>
                            <?php endif; ?>
                            <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ($isLowStock ? 'low-stock-text' : ''); ?>" style="<?php echo ($isSoldOut || $isLowStock) ? '' : 'display: none;'; ?>">
                                <?php if ($isSoldOut): ?>
                                    SOLD OUT
                                <?php elseif ($isLowStock): ?>
                                    ⚠️ Only <?php echo $stock; ?> left in stock!
                                <?php endif; ?>
                            </div>
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
                <?php
                // Determine stock status
                $stock = (int)($product['stock'] ?? 0);
                $isSoldOut = $stock <= 0;
                $isLowStock = $stock > 0 && $stock <= 7;
                ?>
                <div class="product-card <?php echo $isSoldOut ? 'sold-out' : ''; ?>" 
                     data-product-id="<?php echo $product['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-product-price="<?php echo $product['price']; ?>"
                     data-product-sale-price="<?php echo $product['sale_price'] ?? ''; ?>"
                     data-product-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                     data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-featured="<?php echo ($product['featured'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-on-sale="<?php echo ($product['on_sale'] ?? false) ? 'true' : 'false'; ?>"
                     data-product-stock="<?php echo $product['stock'] ?? 0; ?>"
                     data-product-available="<?php echo ($product['available'] ?? true) ? 'true' : 'false'; ?>"
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
                                if (in_array(strtolower($frontExtension), ['mp4', 'webm', 'mov', 'avi', 'mkv'])): ?>
                                    <video src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                           class="active" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop
                                           controls>
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
                                if (in_array(strtolower($backExtension), ['mp4', 'webm', 'mov', 'avi', 'mkv'])): ?>
                                    <video src="../<?php echo htmlspecialchars($backImage); ?>" 
                                           alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                           data-color="<?php echo htmlspecialchars($product['color']); ?>"
                                           muted
                                           loop
                                           controls>
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
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <?php if ($isSoldOut): ?>
                                <button class="add-to-bag sold-out-btn" disabled>Sold Out</button>
                            <?php else: ?>
                                <button class="add-to-bag" 
                                        data-product-id="<?php echo $product['_id']; ?>"
                                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-product-price="<?php echo htmlspecialchars($product['price']); ?>"
                                        data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                                        data-product-stock="<?php echo $stock; ?>">Add To Bag</button>
                            <?php endif; ?>
                            <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ($isLowStock ? 'low-stock-text' : ''); ?>" style="<?php echo ($isSoldOut || $isLowStock) ? '' : 'display: none;'; ?>">
                                <?php if ($isSoldOut): ?>
                                    SOLD OUT
                                <?php elseif ($isLowStock): ?>
                                    ⚠️ Only <?php echo $stock; ?> left in stock!
                                <?php endif; ?>
                            </div>
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
                <p>We couldn't find any kids clothing products.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>

<!-- Mobile Filter Overlay -->
<div class="mobile-filter-overlay" id="mobile-filter-overlay">
    <div class="mobile-filter-content">
        <div class="mobile-filter-header">
            <button class="mobile-filter-close" id="mobile-filter-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-filter-body">
            <!-- Category Filter -->
            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Category</h4>
                    </div>
                    <div class="mobile-filter-options" id="mobile-category-filter">
                        <!-- Category filter options will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Color Filter -->
            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Color</h4>
                    </div>
                    <div class="mobile-color-grid" id="mobile-color-filter">
                        <!-- Color filter options will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Price Filter -->
            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Price Range</h4>
                    </div>
                    <div class="mobile-filter-options" id="mobile-price-filter">
                        <div class="mobile-filter-option">
                            <input type="checkbox" id="price-0-100" value="0-100" data-filter="price_range">
                            <label for="price-0-100">$0 - $100</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        </div>
                        <div class="mobile-filter-option">
                            <input type="checkbox" id="price-100-200" value="100-200" data-filter="price_range">
                            <label for="price-100-200">$100 - $200</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        </div>
                        <div class="mobile-filter-option">
                            <input type="checkbox" id="price-200-400" value="200-400" data-filter="price_range">
                            <label for="price-200-400">$200 - $400</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        </div>
                        <div class="mobile-filter-option">
                            <input type="checkbox" id="price-400-plus" value="400+" data-filter="price_range">
                            <label for="price-400-plus">$400+</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        </div>
                        <div class="mobile-filter-option">
                            <input type="checkbox" id="price-on-sale" value="on-sale" data-filter="price_range">
                            <label for="price-on-sale">On Sale</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mobile-filter-footer">
            <button class="mobile-clear-filters-btn" id="mobile-clear-filters">Clear All</button>
            <button class="mobile-apply-filters-btn" id="mobile-apply-filters">Apply Filters</button>
        </div>
    </div>
</div>

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
            
            <!-- Product Description -->
            <div class="quick-view-description">
                <p></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div>
