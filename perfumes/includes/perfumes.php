<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Perfume.php';

$perfumeModel = new Perfume();

// Get query parameters for filtering
$gender = $_GET['gender'] ?? null;
$brand = $_GET['brand'] ?? null;
$size = $_GET['size'] ?? null;
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;
$sort = $_GET['sort'] ?? 'featured';
$limit = intval($_GET['limit'] ?? 12);
$skip = intval($_GET['skip'] ?? 0);

// Build filters
$filters = [];
if ($gender) $filters['gender'] = $gender;
if ($brand) $filters['brand'] = $brand;
if ($size) $filters['size'] = $size;
if ($minPrice !== null && $maxPrice !== null) {
    $filters['price'] = [
        '$gte' => floatval($minPrice),
        '$lte' => floatval($maxPrice)
    ];
}

// Build sort options
$sortOptions = [];
switch ($sort) {
    case 'newest':
        $sortOptions = ['createdAt' => -1];
        break;
    case 'price-low':
        $sortOptions = ['price' => 1];
        break;
    case 'price-high':
        $sortOptions = ['price' => -1];
        break;
    case 'popular':
        $sortOptions = ['featured' => -1, 'createdAt' => -1];
        break;
    default: // featured
        $sortOptions = ['featured' => -1, 'createdAt' => -1];
        break;
}

// Get perfumes from database
$perfumes = $perfumeModel->getAllPerfumes($filters, $sortOptions, $limit, $skip);
$total = $perfumeModel->getCount(array_merge($filters, ['category' => 'Perfumes']));

// Get available brands and sizes for sidebar
$brands = $perfumeModel->getPerfumeBrands();
$sizes = $perfumeModel->getPerfumeSizes();
?>

<!-- Main Content Section -->
<main class="main-content">
    <div class="content-header">
        <h1 class="page-title">Perfumes</h1>
        <div class="content-controls">
            <div class="sort-control">
                <label for="sort-select">Sort:</label>
                <select id="sort-select" class="sort-select" onchange="updateSort(this.value)">
                    <option value="featured" <?php echo $sort === 'featured' ? 'selected' : ''; ?>>Featured</option>
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                </select>
            </div>
            <div class="view-control">
                <span>View:</span>
                <a href="#" class="view-option <?php echo $limit === 60 ? 'active' : ''; ?>" onclick="updateLimit(60)">60</a>
                <span>|</span>
                <a href="#" class="view-option <?php echo $limit === 120 ? 'active' : ''; ?>" onclick="updateLimit(120)">120</a>
            </div>
        </div>
    </div>

    <div class="product-grid" id="perfumes-grid">
        <?php if (!empty($perfumes)): ?>
            <?php foreach ($perfumes as $index => $perfume): ?>
                <div class="product-card" 
                     data-product-id="<?php echo $perfume['_id']; ?>" 
                     data-gender="<?php echo htmlspecialchars($perfume['gender'] ?? ''); ?>" 
                     data-brand="<?php echo htmlspecialchars($perfume['brand'] ?? ''); ?>" 
                     data-size="<?php echo htmlspecialchars($perfume['size'] ?? ''); ?>" 
                     data-price="<?php echo $perfume['price']; ?>">
            <div class="product-image">
                <div class="image-slider">
                            <?php 
                            // Main product images
                            $frontImage = $perfume['front_image'] ?? $perfume['image_front'] ?? '';
                            $backImage = $perfume['back_image'] ?? $perfume['image_back'] ?? '';
                            
                            // If no back image, use front image for both
                            if (empty($backImage) && !empty($frontImage)) {
                                $backImage = $frontImage;
                            }
                            
                            if ($frontImage): ?>
                                <img src="../<?php echo htmlspecialchars($frontImage); ?>" 
                                     alt="<?php echo htmlspecialchars($perfume['name']); ?> - Front" 
                                     class="active" 
                                     data-color="<?php echo htmlspecialchars($perfume['color']); ?>">
                            <?php endif; ?>
                            
                            <?php if ($backImage): ?>
                                <img src="../<?php echo htmlspecialchars($backImage); ?>" 
                                     alt="<?php echo htmlspecialchars($perfume['name']); ?> - Back" 
                                     data-color="<?php echo htmlspecialchars($perfume['color']); ?>">
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant images
                            if (!empty($perfume['color_variants'])):
                                foreach ($perfume['color_variants'] as $variant):
                                    $variantFrontImage = $variant['front_image'] ?? '';
                                    $variantBackImage = $variant['back_image'] ?? '';
                                    
                                    // If no back image for variant, use front image for both
                                    if (empty($variantBackImage) && !empty($variantFrontImage)) {
                                        $variantBackImage = $variantFrontImage;
                                    }
                                    
                                    if ($variantFrontImage): ?>
                                        <img src="../<?php echo htmlspecialchars($variantFrontImage); ?>" 
                                             alt="<?php echo htmlspecialchars($perfume['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Front" 
                                             data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                    <?php endif; ?>
                                    
                                    <?php if ($variantBackImage): ?>
                                        <img src="../<?php echo htmlspecialchars($variantBackImage); ?>" 
                                             alt="<?php echo htmlspecialchars($perfume['name']); ?> - <?php echo htmlspecialchars($variant['name']); ?> - Back" 
                                             data-color="<?php echo htmlspecialchars($variant['color']); ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                </div>
                <button class="heart-button">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                            <button class="quick-view" data-product-id="<?php echo $perfume['_id']; ?>">Quick View</button>
                            <?php if (($perfume['available'] ?? true) === false): ?>
                                <button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>
                            <?php else: ?>
                                <button class="add-to-bag" onclick="addToCart('<?php echo $perfume['_id']; ?>')">Add To Bag</button>
                            <?php endif; ?>
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                            <?php 
                            // Main product color
                            if (!empty($perfume['color'])): ?>
                                <span class="color-circle <?php echo $index === 0 ? 'active' : ''; ?>" 
                                      style="background-color: <?php echo htmlspecialchars($perfume['color']); ?>;" 
                                      title="<?php echo htmlspecialchars($perfume['color']); ?>" 
                                      data-color="<?php echo htmlspecialchars($perfume['color']); ?>"></span>
                            <?php endif; ?>
                            
                            <?php 
                            // Color variant colors
                            if (!empty($perfume['color_variants'])):
                                foreach ($perfume['color_variants'] as $variant):
                                    if (!empty($variant['color'])): ?>
                                        <span class="color-circle" 
                                              style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;" 
                                              title="<?php echo htmlspecialchars($variant['name']); ?>" 
                                              data-color="<?php echo htmlspecialchars($variant['color']); ?>"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($perfume['name']); ?></h3>
                        <div class="product-brand"><?php echo htmlspecialchars($perfume['brand'] ?? ''); ?></div>
                        <div class="product-size"><?php echo htmlspecialchars($perfume['size'] ?? ''); ?></div>
                        <div class="product-price">$<?php echo number_format($perfume['price'], 0); ?></div>
                        <?php if (($perfume['available'] ?? true) === false): ?>
                            <div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>
                        <?php elseif (($perfume['stock'] ?? 0) <= 5 && ($perfume['stock'] ?? 0) > 0): ?>
                            <div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only <?php echo $perfume['stock']; ?> left</div>
                        <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No perfumes available at the moment.</p>
                <button onclick="initializePerfumes()" class="btn btn-primary">Initialize Sample Perfumes</button>
                </div>
        <?php endif; ?>
        </div>

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
            </div>
            <div class="thumbnail-images" id="quick-view-thumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="quick-view-details">
            <h2 id="quick-view-title"></h2>
            <div class="quick-view-brand" id="quick-view-brand"></div>
            <div class="quick-view-size" id="quick-view-size"></div>
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
                <p id="quick-view-description">A beautiful fragrance perfect for any occasion.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Overlay -->
<div class="quick-view-overlay" id="quick-view-overlay"></div> 

<script>
// Store current filters and pagination state
let currentFilters = {
    gender: '<?php echo $gender; ?>',
    brand: '<?php echo $brand; ?>',
    size: '<?php echo $size; ?>',
    minPrice: '<?php echo $minPrice; ?>',
    maxPrice: '<?php echo $maxPrice; ?>',
    sort: '<?php echo $sort; ?>',
    limit: <?php echo $limit; ?>,
    skip: <?php echo $skip; ?>
};

// Function to update sort
function updateSort(sort) {
    currentFilters.sort = sort;
    currentFilters.skip = 0; // Reset to first page
    loadPerfumes();
}

// Function to update limit
function updateLimit(limit) {
    currentFilters.limit = limit;
    currentFilters.skip = 0; // Reset to first page
    loadPerfumes();
}

// Function to go to specific page
function goToPage(page) {
    currentFilters.skip = (page - 1) * currentFilters.limit;
    loadPerfumes();
}

// Function to load perfumes with current filters
function loadPerfumes() {
    const params = new URLSearchParams();
    
    if (currentFilters.gender) params.append('gender', currentFilters.gender);
    if (currentFilters.brand) params.append('brand', currentFilters.brand);
    if (currentFilters.size) params.append('size', currentFilters.size);
    if (currentFilters.minPrice) params.append('min_price', currentFilters.minPrice);
    if (currentFilters.maxPrice) params.append('max_price', currentFilters.maxPrice);
    if (currentFilters.sort) params.append('sort', currentFilters.sort);
    if (currentFilters.limit) params.append('limit', currentFilters.limit);
    if (currentFilters.skip) params.append('skip', currentFilters.skip);
    
    // Update URL without reloading
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    
    // Reload the page to show filtered results
    window.location.reload();
}

// Function to add to cart
function addToCart(productId) {
    fetch('../cart-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_cart&product_id=${productId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cart_count;
                if (data.cart_count > 0) {
                    cartCountElement.style.display = 'flex';
                }
            }
            alert('Product added to cart successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product to cart');
    });
}

// Function to initialize sample perfumes
function initializePerfumes() {
    fetch('../perfumes-api.php?action=initialize_perfumes')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error initializing perfumes');
    });
}

// Load cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('../cart-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_cart_count'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cart_count;
                if (data.cart_count > 0) {
                    cartCountElement.style.display = 'flex';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error loading cart count:', error);
    });
});
</script> 