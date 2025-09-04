<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get filter parameters from URL
$subcategory = $_GET['subcategory'] ?? '';
$gender = $_GET['gender'] ?? '';
$size = $_GET['size'] ?? '';
$color = $_GET['color'] ?? '';
$priceRange = $_GET['price_range'] ?? '';

// Build filter conditions
$filters = ['category' => 'Shoes'];
$andConditions = [];

// Apply gender filter first (takes priority over subcategory)
if ($gender) {
    switch (strtolower($gender)) {
        case 'women':
            $filters['subcategory'] = "Women's shoes";
            $pageTitle = "Women's Shoes";
            break;
        case 'men':
            $filters['subcategory'] = "Men's shoes";
            $pageTitle = "Men's Shoes";
            break;
        case 'children':
            // For children, we want all kids' shoes EXCEPT infant shoes
            $filters['subcategory'] = "Kids' shoes";
            $pageTitle = "Children's Shoes";
            // Add conditions to exclude infant shoes
            $andConditions[] = [
                '$and' => [
                    // Exclude shoes with "infant" in the name
                    ['name' => ['$not' => new MongoDB\BSON\Regex('infant', 'i')]],
                    // Exclude shoes with infant sizes (≤ 25)
                    ['$or' => [
                        ['selected_sizes' => ['$not' => ['$elemMatch' => ['$lte' => '25']]]],
                        ['selected_sizes' => ['$exists' => false]]
                    ]],
                    // Exclude color variants with infant sizes
                    ['$or' => [
                        ['color_variants.selected_sizes' => ['$not' => ['$elemMatch' => ['$lte' => '25']]]],
                        ['color_variants' => ['$exists' => false]]
                    ]]
                ]
            ];
            break;
        case 'boys':
            $filters['subcategory'] = "Kids' shoes";
            $pageTitle = "Boys' Shoes";
            break;
        case 'girls':
            $filters['subcategory'] = "Kids' shoes";
            $pageTitle = "Girls' Shoes";
            break;
        case 'infant':
            // For infant, we need to filter by both subcategory and size/name criteria
            $filters['subcategory'] = "Kids' shoes";
            $pageTitle = "Infant Shoes";
            // Add additional conditions for infant-specific filtering
            $andConditions[] = [
                '$or' => [
                    // Shoes with infant sizes (≤ 25)
                    ['selected_sizes' => ['$elemMatch' => ['$lte' => '25']]],
                    // Shoes with "infant" in the name
                    ['name' => new MongoDB\BSON\Regex('infant', 'i')],
                    // Color variants with infant sizes
                    ['color_variants.selected_sizes' => ['$elemMatch' => ['$lte' => '25']]]
                ]
            ];
            break;
    }
} else if ($subcategory) {
    // Apply subcategory filter only if no gender filter is selected
    $filters['subcategory'] = ucfirst($subcategory);
    $pageTitle = ucfirst($subcategory);
} else {
    $pageTitle = "Shoes";
}

// Apply size filter
if ($size) {
    $andConditions[] = [
        '$or' => [
            ['sizes' => ['$elemMatch' => ['$eq' => $size]]],
            ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')],
            ['size_category' => $size]
        ]
    ];
}

// Apply color filter
if ($color) {
    // Define color groups - map color names to hex codes (existing + future products)
    $colorGroups = [
        'black' => ['#000000', '#050505', '#0c0c0e', '#080a11', '#2e2d30', '#1a1a1a', '#333333', '#2c2c2c'],
        'beige' => ['#ecbcaf', '#cba9a8', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460'],
        'blue' => ['#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
        'brown' => ['#8b4513', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
        'gold' => ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
        'green' => ['#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
        'grey' => ['#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
        'orange' => ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
        'pink' => ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
        'purple' => ['#422d58', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
        'red' => ['#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
        'silver' => ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
        'taupe' => ['#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
        'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc'],
        'yellow' => ['#ffff00', '#ffd700', '#ffeb3b', '#ffc107', '#ffa000', '#ff8f00', '#ff6f00', '#ffea00']
    ];
    
    // Get the hex codes for the selected color group
    $hexCodes = $colorGroups[$color] ?? [$color];
    
    $andConditions[] = [
        '$or' => [
            ['color' => ['$in' => $hexCodes]],
            ['color_variants.color' => ['$in' => $hexCodes]]
        ]
    ];
}

// Apply price range filter
if ($priceRange) {
    switch ($priceRange) {
        case 'on-sale':
            $andConditions[] = ['sale' => true];
            break;
        case '0-25':
            $andConditions[] = ['price' => ['$gte' => 0, '$lte' => 25]];
            break;
        case '25-50':
            $andConditions[] = ['price' => ['$gte' => 25, '$lte' => 50]];
            break;
        case '50-75':
            $andConditions[] = ['price' => ['$gte' => 50, '$lte' => 75]];
            break;
        case '75-100':
            $andConditions[] = ['price' => ['$gte' => 75, '$lte' => 100]];
            break;
        case '100+':
            $andConditions[] = ['price' => ['$gte' => 100]];
            break;
    }
}

// Combine all conditions
if (!empty($andConditions)) {
    $filters['$and'] = $andConditions;
}

// Get filtered products
$products = $productModel->getAll($filters, ['createdAt' => -1]);

// Get all men's shoes from the database
$mensShoes = $productModel->getBySubcategory("Men's shoes");

// Get all women's shoes from the database
$womensShoes = $productModel->getBySubcategory("Women's shoes");

// Get all children's shoes from the database
$childrensShoes = $productModel->getBySubcategory("Kids' shoes");

?>

<!-- Main Content Section -->
<main class="main-content">
    <!-- Products Section -->
    <div class="content-header" id="products-section">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select-shoes">Sort:</label>
                <select id="sort-select-shoes" class="sort-select">
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
                <div class="product-card" data-product-id="<?php echo $product['_id']; ?>">
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
                <p>No products found for this category.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- All Shoes Display -->
    <div class="product-grid" id="all-shoes-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['_id']; ?>">
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
                <p>No shoes found.</p>
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
            <div class="quick-view-category" id="quick-view-category"></div>
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
                <p>A beautiful dress perfect for any occasion. Features a flattering fit and comfortable fabric.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div>

<script>
// Color filtering logic - show only the color variant that matches the selected filter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedColor = urlParams.get('color');
    
    if (selectedColor) {
        // Define color groups
        const colorGroups = {
            'black': ['#000000', '#050505', '#0c0c0e', '#080a11', '#2e2d30', '#1a1a1a', '#333333', '#2c2c2c'],
            'beige': ['#ecbcaf', '#cba9a8', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460'],
            'blue': ['#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
            'brown': ['#8b4513', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
            'gold': ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
            'green': ['#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
            'grey': ['#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
            'orange': ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
            'pink': ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
            'purple': ['#422d58', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
            'red': ['#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
            'silver': ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
            'taupe': ['#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
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