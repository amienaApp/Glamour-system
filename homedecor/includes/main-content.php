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

// Get products based on subcategory or all home decor products from ALL subcategories
if ($subcategory) {
    $products = $productModel->getBySubcategory(ucfirst($subcategory), $sortOptions);
    $pageTitle = ucfirst($subcategory);
} else {
    // Get all home decor/home and living products from ALL subcategories
    $allHomeDecorProducts = [];
    
    // Try to get products from main category first - use exact name from Category model
    $mainCategoryProducts = $productModel->getByCategory("Home & Living", $sortOptions);
    if (!empty($mainCategoryProducts)) {
        $allHomeDecorProducts = array_merge($allHomeDecorProducts, $mainCategoryProducts);
    }
    
    // If no products found with "Home & Living", try alternative categories
    if (empty($mainCategoryProducts)) {
        $altCategoryProducts = $productModel->getByCategory("Home Decor", $sortOptions);
        if (!empty($altCategoryProducts)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $altCategoryProducts);
        }
    }
    if (empty($mainCategoryProducts) && empty($altCategoryProducts)) {
        $altCategoryProducts2 = $productModel->getByCategory("Home and Living", $sortOptions);
        if (!empty($altCategoryProducts2)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $altCategoryProducts2);
        }
    }
    if (empty($mainCategoryProducts) && empty($altCategoryProducts) && empty($altCategoryProducts2)) {
        $altCategoryProducts3 = $productModel->getByCategory("Home", $sortOptions);
        if (!empty($altCategoryProducts3)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $altCategoryProducts3);
        }
    }
    
    // Also get products from specific subcategories to ensure we catch everything
               // Use EXACT subcategory names from Category.php model
           $subcategories = ['Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture'];
    foreach ($subcategories as $subcat) {
        $subcatProducts = $productModel->getBySubcategory($subcat);
        if (!empty($subcatProducts)) {
            $allHomeDecorProducts = array_merge($allHomeDecorProducts, $subcatProducts);
        }
    }
    
    // Remove duplicates based on product ID
    $uniqueProducts = [];
    $seenIds = [];
    foreach ($allHomeDecorProducts as $product) {
        $productId = (string)$product['_id'];
        if (!in_array($productId, $seenIds)) {
            $uniqueProducts[] = $product;
            $seenIds[] = $productId;
        }
    }
    
    $products = $uniqueProducts;
    $pageTitle = "Home & Living - All Products";
}

// Get featured home decor products for display - use exact category name from model
$featuredProducts = $productModel->getByCategoryAndFeatured("Home & Living", true);
if (empty($featuredProducts)) {
    $featuredProducts = $productModel->getByCategoryAndFeatured("Home Decor", true);
}
if (empty($featuredProducts)) {
    $featuredProducts = $productModel->getByCategoryAndFeatured("Home and Living", true);
}

       // Get products by specific home decor subcategories for sidebar display
       // Use EXACT subcategory names from Category.php model
       $beddingProducts = $productModel->getBySubcategory('Bedding');
       $livingroomProducts = $productModel->getBySubcategory('living room');
       $kitchenProducts = $productModel->getBySubcategory('Kitchen');
       $dinningroomProducts = $productModel->getBySubcategory('dinning room');
       $artworkProducts = $productModel->getBySubcategory('artwork');
       $lightinningProducts = $productModel->getBySubcategory('lightinning');

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
                <label for="sort-select-men">Sort:</label>
                <select id="sort-select-men" class="sort-select" onchange="updateSort(this.value)">
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
                     data-category="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-price="<?php echo $product['price'] ?? 0; ?>"
                     data-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                     data-material="<?php echo htmlspecialchars($product['material'] ?? ''); ?>"
                     data-availability="<?php echo ($product['available'] ?? true) ? 'in-stock' : 'sold-out'; ?>"
                     data-sale="<?php echo !empty($product['sale_price']) ? 'on-sale' : ''; ?>"
                     data-new="<?php echo (isset($product['created_at']) && (time() - strtotime($product['created_at'])) < (30 * 24 * 60 * 60)) ? 'new-arrival' : ''; ?>">
                    <div class="product-image">
                        <?php if (($product['featured'] ?? false) === true): ?>
                            <div class="featured-badge">Featured</div>
                        <?php endif; ?>
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
                                        data-product-price="<?php echo $product['price']; ?>"
                                        data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                                        onclick="addToCartFromCard(this)">
                                    Add To Bag
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ($isLowStock ? 'low-stock-text' : ''); ?>" style="<?php echo ($isSoldOut || $isLowStock) ? '' : 'display: none;'; ?>">
                            <?php if ($isSoldOut): ?>
                                SOLD OUT
                            <?php elseif ($isLowStock): ?>
                                ⚠️ Only <?php echo $stock; ?> left in stock!
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
                                      title="<?php echo htmlspecialchars($product['name']); ?>" 
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
                <p>No products found for this category.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- All Home Decor Products Display -->
    <div class="product-grid" id="all-home-decor-grid">
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
                     data-category="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>"
                     data-price="<?php echo $product['price'] ?? 0; ?>"
                     data-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                     data-material="<?php echo htmlspecialchars($product['material'] ?? ''); ?>"
                     data-availability="<?php echo ($product['available'] ?? true) ? 'in-stock' : 'sold-out'; ?>"
                     data-sale="<?php echo !empty($product['sale_price']) ? 'on-sale' : ''; ?>"
                     data-new="<?php echo (isset($product['created_at']) && (time() - strtotime($product['created_at'])) < (30 * 24 * 60 * 60)) ? 'new-arrival' : ''; ?>">
                    <div class="product-image">
                        <?php if (($product['featured'] ?? false) === true): ?>
                            <div class="featured-badge">Featured</div>
                        <?php endif; ?>
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
                                        data-product-price="<?php echo $product['price']; ?>"
                                        data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                                        onclick="addToCartFromCard(this)">
                                    Add To Bag
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ($isLowStock ? 'low-stock-text' : ''); ?>" style="<?php echo ($isSoldOut || $isLowStock) ? '' : 'display: none;'; ?>">
                            <?php if ($isSoldOut): ?>
                                SOLD OUT
                            <?php elseif ($isLowStock): ?>
                                ⚠️ Only <?php echo $stock; ?> left in stock!
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
                <p>No home decor products found.</p>
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
        <h2 id="quick-view-title">Product Details</h2>
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
            <h2 id="quick-view-product-name"></h2>
            <div class="quick-view-price" id="quick-view-price"></div>
            <div class="quick-view-reviews">
                <span class="stars">★★★★★</span>
                <span class="review-count">(0 Reviews)</span>
            </div>
            
            <!-- Home & Living specific details -->
            <div id="quick-view-home-living-details" style="display: none;">
                <div class="detail-row">
                    <span class="detail-label">Material:</span>
                    <span id="quick-view-material"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dimensions:</span>
                    <span id="quick-view-dimensions"></span>
                </div>
                <div id="quick-view-bedding-details" style="display: none;">
                    <div class="detail-row">
                        <span class="detail-label">Bedding Size:</span>
                        <span id="quick-view-bedding-size"></span>
                    </div>
                </div>
                <div id="quick-view-dining-details" style="display: none;">
                    <div class="detail-row">
                        <span class="detail-label">Chairs:</span>
                        <span id="quick-view-chair-count"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Table Size:</span>
                        <span id="quick-view-table-size"></span>
                    </div>
                </div>
                <div id="quick-view-living-details" style="display: none;">
                    <div class="detail-row">
                        <span class="detail-label">Sofas:</span>
                        <span id="quick-view-sofa-count"></span>
                    </div>
                </div>
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
                                            <button class="add-to-bag-quick" id="add-to-bag-quick" 
                                    data-product-id="" 
                                    data-product-name="" 
                                    data-product-price="" 
                                    data-product-color=""
                                    data-product-stock="">
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
                <p id="quick-view-description">A beautiful product perfect for any occasion. Features a flattering fit and comfortable fabric.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div> 