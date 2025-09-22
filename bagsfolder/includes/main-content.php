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

// Convert URL-friendly subcategory back to database format
$subcategoryForQuery = '';
if ($subcategory) {
    // Convert URL format back to database format
    $subcategoryForQuery = str_replace(['-', 'and'], [' ', '&'], $subcategory);
    $subcategoryForQuery = ucwords($subcategoryForQuery);
}

// Get products based on subcategory or all bags
if ($subcategoryForQuery) {
    $products = $productModel->getBySubcategory($subcategoryForQuery, $sortOptions);
    $pageTitle = $subcategoryForQuery;
} else {
    // Get all bags products
    $products = $productModel->getByCategory("Bags", $sortOptions);
    $pageTitle = "Bags";
}

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
                <label for="sort-select-bags">Sort:</label>
                <select id="sort-select-bags" class="sort-select" onchange="updateSort(this.value)">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="product-grid" id="bags-products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <?php 
                $stock = (int)($product['stock'] ?? 0);
                $isSoldOut = $stock <= 0;
                $isLowStock = $stock > 0 && $stock <= 7;
                ?>
                <div class="product-card <?php echo $isSoldOut ? 'sold-out' : ''; ?>" 
                     data-product-id="<?php echo $product['_id']; ?>"
                     data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-product-price="<?php echo $product['price']; ?>"
                     data-product-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                     data-product-subcategory="<?php echo htmlspecialchars($product['subcategory'] ?? ''); ?>"
                     data-product-color="<?php echo htmlspecialchars($product['color'] ?? ''); ?>"
                     data-product-stock="<?php echo $product['stock'] ?? 0; ?>">
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
                            
                            if ($frontImage): ?>
                                <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - Front" 
                                     class="active" 
                                     data-color="<?php echo htmlspecialchars($product['color']); ?>">
                            <?php endif; ?>
                            
                            <?php if ($backImage): ?>
                                <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - Back" 
                                     data-color="<?php echo htmlspecialchars($product['color']); ?>">
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
                    </div>
                    <div class="product-info">
                        <div class="color-options">
                            <?php if (!empty($product['color'])): ?>
                                <span class="color-circle active" 
                                      style="background-color: <?php echo htmlspecialchars($product['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($product['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($product['color']); ?>"></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 0); ?></div>
                        <div class="product-availability <?php echo $isSoldOut ? 'sold-out-text' : ($isLowStock ? 'low-stock-text' : ''); ?>" style="<?php echo ($isSoldOut || $isLowStock) ? '' : 'display: none;'; ?>">
                            <?php if ($isSoldOut): ?>
                                SOLD OUT
                            <?php elseif ($isLowStock): ?>
                                ⚠️ Only <?php echo $stock; ?> left in stock!
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <h3>No products found</h3>
                <p>We couldn't find any bags in this category.</p>
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
        <!-- Product Media -->
        <div class="quick-view-images">
            <div class="main-image-container">
                <img id="quick-view-main-image" src="" alt="Product Media">
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
            
            <!-- Action Buttons -->
            <div class="quick-view-actions">
                <button class="add-to-bag-quick" id="add-to-bag-quick">
                    <i class="fas fa-shopping-bag"></i>
                    Add to Bag
                </button>
                <button class="add-to-wishlist-quick" id="add-to-wishlist-quick">
                    <i class="far fa-heart"></i>
                    Add to Wishlist
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

<!-- Mobile Filter Overlay -->
<div class="mobile-filter-overlay" id="mobile-filter-overlay">
    <div class="mobile-filter-content">
        <div class="mobile-filter-header">
            <h3>Filters</h3>
            <button class="mobile-filter-close" id="mobile-filter-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-filter-body">
            <!-- Copy all sidebar filters here -->
            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Category</h4>
                    </div>
                    <div class="mobile-filter-options" id="mobile-category-filter">
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="shoulder-bags" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Shoulder Bags
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="clutches" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Clutches
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="tote-bags" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Tote Bags
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="crossbody-bags" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Crossbody Bags
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="backpacks" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Backpacks
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="briefcases" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Briefcases
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="laptop-bags" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Laptop Bags
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="waist-bags" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Waist Bags
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="category[]" value="wallets" data-filter="category">
                            <span class="mobile-checkmark"></span>
                            Wallets
                        </label>
                    </div>
                </div>
            </div>

            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Color</h4>
                    </div>
                    <div class="mobile-filter-options">
                        <div class="mobile-color-grid" id="mobile-color-filter">
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#f5f5dc" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #f5f5dc;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#000000" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #000000;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#0066cc" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #0066cc;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#8b4513" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #8b4513;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#ffd700" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #ffd700;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#228b22" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #228b22;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#808080" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #808080;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#ffa500" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #ffa500;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#ffc0cb" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #ffc0cb;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#800080" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #800080;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#ff0000" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #ff0000;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#c0c0c0" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #c0c0c0;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#483c32" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #483c32;"></span>
                            </label>
                            <label class="mobile-color-option">
                                <input type="checkbox" name="color[]" value="#ffffff" data-filter="color">
                                <span class="mobile-color-swatch" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mobile-filter-section">
                <div class="mobile-filter-group">
                    <div class="mobile-filter-header">
                        <h4>Price</h4>
                    </div>
                    <div class="mobile-filter-options" id="mobile-price-filter">
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="price[]" value="on-sale" data-filter="price_range">
                            <span class="mobile-checkmark"></span>
                            On Sale
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="price[]" value="0-100" data-filter="price_range">
                            <span class="mobile-checkmark"></span>
                            $0 - $100
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="price[]" value="100-200" data-filter="price_range">
                            <span class="mobile-checkmark"></span>
                            $100 - $200
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="price[]" value="200-400" data-filter="price_range">
                            <span class="mobile-checkmark"></span>
                            $200 - $400
                        </label>
                        <label class="mobile-filter-option">
                            <input type="checkbox" name="price[]" value="400+" data-filter="price_range">
                            <span class="mobile-checkmark"></span>
                            $400+
                        </label>
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