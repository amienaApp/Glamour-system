
<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Get query parameters for filtering
$gender = $_GET['gender'] ?? null;
$category = $_GET['category'] ?? null;
$color = $_GET['color'] ?? null;
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;
$sort = $_GET['sort'] ?? 'oldest';
$limit = intval($_GET['limit'] ?? 60);
$skip = intval($_GET['skip'] ?? 0);

// Build filters
$filters = [];
$filters['category'] = 'Bags'; // Always filter for bags
if ($subcategory) $filters['subcategory'] = ucfirst($subcategory);
if ($gender) $filters['gender'] = $gender;
if ($category) $filters['subcategory'] = ucfirst($category); // Use subcategory for category filter
if ($color) {
    // Define color groups - map color names to hex codes (existing + future products)
    $colorGroups = [
        'black' => ['#000000', '#181a1a', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c'],
        'beige' => ['#e1c9c9', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65'],
        'blue' => ['#414c61', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
        'brown' => ['#8b4f33', '#5d3c3c', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
        'gold' => ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
        'green' => ['#82ff4d', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
        'grey' => ['#575759', '#4a4142', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
        'orange' => ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
        'pink' => ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
        'purple' => ['#373645', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
        'red' => ['#5a2b34', '#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
        'silver' => ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
        'taupe' => ['#b38f65', '#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
        'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc'],
        'yellow' => ['#ffff00', '#ffd700', '#ffeb3b', '#ffc107', '#ffa000', '#ff8f00', '#ff6f00', '#ffea00']
    ];
    
    // Get the hex codes for the selected color group
    $hexCodes = $colorGroups[$color] ?? [$color];
    
    // Use $in operator to match any of the hex codes in the group
    $filters['color'] = ['$in' => $hexCodes];
}
if ($minPrice !== null) {
    $filters['price'] = ['$gte' => floatval($minPrice)];
    if ($maxPrice !== null) {
        $filters['price']['$lte'] = floatval($maxPrice);
    }
}

// Build sort options
$sortOptions = [];
switch ($sort) {
    case 'oldest':
        $sortOptions = ['_id' => 1]; // Ascending order by ID - oldest first
        break;
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


// Get bags from database
$products = $productModel->getAll($filters, $sortOptions, $limit, $skip);
$total = $productModel->getCount($filters);

// Set page title
$pageTitle = $subcategory ? ucfirst($subcategory) : "Bags";

// Get all handbags from the database
$handbags = $productModel->getBySubcategory('Handbags');

// Get all backpacks from the database
$backpacks = $productModel->getBySubcategory('Backpacks');

// Get all clutches from the database
$clutches = $productModel->getBySubcategory('Clutches');

?>

<!-- Main Content Section -->
<main class="main-content">
    <!-- Products Section -->
    <div class="content-header" id="products-section">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select-bags">Sort:</label>
                <select id="sort-select-bags" class="sort-select" onchange="updateSort(this.value)">
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                </select>
            </div>
            
            <!-- Mobile Filters Hamburger -->
            <div class="mobile-filters-btn" onclick="toggleMobileFilters()">
                <div class="hamburger-bars">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span class="filters-text">Refine By</span>
            </div>
        </div>
        
        <!-- Mobile Filters Panel -->
        <div class="mobile-filters-panel" id="mobile-filters-panel">
            <div class="mobile-filters-header">
                <h3>Refine By</h3>
                <button class="close-filters" onclick="toggleMobileFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-filters-content">
                <!-- Include the actual sidebar content -->
                <?php include 'includes/sidebar.php'; ?>
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
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>">
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
                <p>No products found for this category.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- All Bags Display -->
    <div class="product-grid" id="all-bags-grid">
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
                     data-product-sizes="<?php echo htmlspecialchars(json_encode($product['sizes'] ?? $product['selected_sizes'] ?? [])); ?>"
                     data-product-selected-sizes="<?php echo htmlspecialchars(json_encode($product['selected_sizes'] ?? [])); ?>"
                     data-product-variants="<?php echo htmlspecialchars(json_encode($product['color_variants'] ?? [])); ?>"
                     data-product-options="<?php echo htmlspecialchars(json_encode($product['options'] ?? [])); ?>">
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
                <p>No bags found.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total > $limit): ?>
        <div class="pagination">
            <?php 
            $totalPages = ceil($total / $limit);
            $currentPage = floor($skip / $limit) + 1;
            
            for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="#" class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>" 
                   onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></a>
            <?php endfor; ?>
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
        <!-- Product Images -->
        <div class="quick-view-images">
            <div class="main-image-container">
                <img id="quick-view-main-image" src="" alt="Product Image">
                <video id="quick-view-main-video" style="display: none;" muted loop></video>
            </div>
            <div class="thumbnail-images" id="quick-view-thumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="quick-view-details">
            <h2 id="quick-view-title"></h2>
            <div class="quick-view-brand" id="quick-view-brand"></div>
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
            
            <!-- Availability -->
            <div class="quick-view-availability" id="quick-view-availability">
                <!-- Availability will be populated by JavaScript -->
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
                <p id="quick-view-description-text">A beautiful bag perfect for any occasion. Features a durable design and comfortable carrying experience.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->

<script>
// Color filtering logic - show only the color variant that matches the selected filter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedColor = urlParams.get('color');
    
    if (selectedColor) {
        // Define color groups
        const colorGroups = {
            'black': ['#000000', '#181a1a', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c'],
            'beige': ['#e1c9c9', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65'],
            'blue': ['#414c61', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
            'brown': ['#8b4f33', '#5d3c3c', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
            'gold': ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
            'green': ['#82ff4d', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
            'grey': ['#575759', '#4a4142', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
            'orange': ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
            'pink': ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
            'purple': ['#373645', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
            'red': ['#5a2b34', '#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
            'silver': ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
            'taupe': ['#b38f65', '#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
            'white': ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc'],
            'yellow': ['#ffff00', '#ffd700', '#ffeb3b', '#ffc107', '#ffa000', '#ff8f00', '#ff6f00', '#ffea00']
        };
        
        const allowedColors = colorGroups[selectedColor] || [selectedColor];
        
        // Process each product card
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(function(card) {
            // Find all color variant elements (images, color swatches, etc.)
            const colorElements = card.querySelectorAll('[data-color]');
            let hasMatchingColor = false;
            
            // First, hide all color variants
            colorElements.forEach(function(element) {
                element.style.display = 'none';
            });
            
            // Then show only the matching color variants
            colorElements.forEach(function(element) {
                const elementColor = element.getAttribute('data-color');
                if (elementColor && allowedColors.includes(elementColor)) {
                    element.style.display = 'block';
                    hasMatchingColor = true;
                }
            });
            
            // If no color variants match, show the main product image
            if (!hasMatchingColor) {
                const mainImage = card.querySelector('.product-image img:not([data-color])');
                if (mainImage) {
                    mainImage.style.display = 'block';
                }
            }
        });
        
        // Also filter color swatches in product details
        const colorSwatches = document.querySelectorAll('.color-swatch');
        colorSwatches.forEach(function(swatch) {
            const swatchColor = swatch.getAttribute('data-color');
            if (swatchColor && !allowedColors.includes(swatchColor)) {
                swatch.style.display = 'none';
            }
        });
    }
});
</script>
<div class="quick-view-overlay" id="quick-view-overlay"></div> 