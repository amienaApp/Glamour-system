<?php
require_once __DIR__ . '/../../config/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Get products based on subcategory or all shoes
if ($subcategory) {
    $shoesProducts = $productModel->getBySubcategory(ucfirst($subcategory));
    $pageTitle = ucfirst($subcategory) . ' Shoes';
} else {
    // Get all shoes products
    $shoesProducts = $productModel->getByCategory('Shoes');
    $pageTitle = 'Shoes';
}

// Helper function to convert BSONArray to regular array
function toArray($value) {
    if (is_object($value) && method_exists($value, 'toArray')) {
        $result = $value->toArray();
        return is_array($result) ? $result : (array)$result;
    }
    return $value;
}

// Helper function to get count of array or BSONArray
function getCount($value) {
    if (is_object($value) && method_exists($value, 'count')) {
        return $value->count();
    }
    return count($value);
}
?>

<!-- Main Content Section -->
<main class="main-content">
    <div class="content-header">
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
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
        <?php if (!empty($shoesProducts)): ?>
            <?php foreach ($shoesProducts as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['_id']; ?>">
                    <div class="product-image">
                        <div class="image-slider">
                            <?php 
                            $displayImage = '';
                            $colorVariants = [];
                            
                            // Try to get images from color variants first
                            if (isset($product['color_variants']) && !empty($product['color_variants'])) {
                                $colorVariants = (array)$product['color_variants'];
                                if (!empty($colorVariants)) {
                                    $firstVariant = $colorVariants[0];
                                    if (isset($firstVariant['images']) && !empty($firstVariant['images'])) {
                                        $images = (array)$firstVariant['images'];
                                        if (!empty($images)) {
                                            $displayImage = $images[0];
                                        }
                                    }
                                }
                            }
                            
                            // Fallback to front_image if no color variant images
                            if (empty($displayImage)) {
                                $displayImage = $product['front_image'] ?? $product['image_front'] ?? '';
                            }
                            
                            if (!empty($displayImage)) {
                                // Handle both ../img/ and img/ paths
                                if (strpos($displayImage, '../') === 0) {
                                    // Already has ../ prefix, use as is
                                    $imagePath = $displayImage;
                                } else {
                                    // Add ../ prefix
                                    $imagePath = '../' . $displayImage;
                                }
                                echo "<img src=\"{$imagePath}\" alt=\"{$product['name']}\" class=\"active\" data-color=\"default\">";
                            } else {
                                // Final fallback with an existing image
                                echo "<img src=\"../img/shoes/womenshoes/1.avif\" alt=\"{$product['name']}\" class=\"active\" data-color=\"default\">";
                            }
                            ?>
                        </div>
                        <button class="heart-button">
                            <i class="fas fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $product['_id']; ?>">Quick View</button>
                            <button class="add-to-bag">Add To Bag</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="color-options">
                            <?php 
                            if (!empty($colorVariants)) {
                                foreach ($colorVariants as $index => $variant) {
                                    $isActive = $index === 0 ? 'active' : '';
                                    $colorCode = $variant['color'] ?? '#000';
                                    $colorName = $variant['name'] ?? 'Unknown';
                                    echo "<span class=\"color-circle {$isActive}\" style=\"background-color: {$colorCode};\" title=\"{$colorName}\" data-color=\"{$colorName}\"></span>";
                                }
                            } else {
                                // Fallback for products without color variants
                                if (!empty($product['color'])) {
                                    echo "<span class=\"color-circle active\" style=\"background-color: {$product['color']};\" title=\"Default\" data-color=\"default\"></span>";
                                } else {
                                    echo "<span class=\"color-circle active\" style=\"background-color: #000;\" title=\"Default\" data-color=\"default\"></span>";
                                }
                            }
                            ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No shoes products found.</p>
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