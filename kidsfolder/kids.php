<?php
session_start();
$page_title = 'Glamour Palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}

// Load categories and subcategories from database
require_once '../config1/mongodb.php';
require_once '../models/Category.php';
require_once '../includes/filter-data-helper.php';

// Get dynamic filter data for sidebar
$filterData = getFilterData('Kids\' Clothing');

// Extract all unique colors from ALL kids products for dynamic color filter
require_once '../models/Product.php';
$productModel = new Product();
$allKidsProducts = $productModel->getByCategory('Kids\' Clothing');

$allColors = [];

foreach ($allKidsProducts as $product) {
    // Get color from main color field
    if (!empty($product['color'])) {
        $allColors[] = $product['color'];
    }

    // Get colors from color_variants
    if (!empty($product['color_variants'])) {
        $colorVariants = is_string($product['color_variants']) ?
            json_decode($product['color_variants'], true) : $product['color_variants'];

        if (is_array($colorVariants)) {
            foreach ($colorVariants as $variant) {
                if (!empty($variant['color'])) {
                    $allColors[] = $variant['color'];
                }
            }
        }
    }
}

// Remove duplicates and sort colors
$allColors = array_unique($allColors);
sort($allColors);

$categoryModel = new Category();
$kidsCategory = $categoryModel->getByName("Kids' Clothing");
$subcategories = [];

if ($kidsCategory && isset($kidsCategory['subcategories'])) {
    // Convert BSONArray to regular array if needed
    $allSubcategories = [];
    foreach ($kidsCategory['subcategories'] as $sub) {
        if (is_array($sub) && isset($sub['name'])) {
            $allSubcategories[] = $sub['name'];
        } elseif (is_object($sub) && isset($sub['name'])) {
            $allSubcategories[] = $sub['name'];
        } else {
            $allSubcategories[] = $sub;
        }
    }
    
    // Filter out unwanted subcategories if needed
    $excludedSubcategories = [];
    $subcategories = array_filter($allSubcategories, function($subcategory) use ($excludedSubcategories) {
        return !in_array($subcategory, $excludedSubcategories);
    });
}

// Define image mapping for subcategories
$subcategoryImages = [
    'Boys' => '../img/child/1.webp',
    'Girls' => '../img/child/2.webp',
    'Baby' => '../img/child/3.webp',
    'Toddler' => '../img/child/4.webp',
    'Accessories' => '../img/child/5.webp',
    'Shoes' => '../img/child/6.webp'
];

// Extract all unique colors from ALL kids products for dynamic color filter
require_once '../models/Product.php';
$productModel = new Product();
$allKidsProducts = $productModel->getByCategory("Kids' Clothing");
$allColors = [];

foreach ($allKidsProducts as $product) {
    // Get color from main color field
    if (!empty($product['color'])) {
        $allColors[] = $product['color'];
    }

    // Get colors from color_variants
    if (!empty($product['color_variants'])) {
        $colorVariants = is_string($product['color_variants']) ?
            json_decode($product['color_variants'], true) : $product['color_variants'];

        if (is_array($colorVariants)) {
            foreach ($colorVariants as $variant) {
                if (!empty($variant['color'])) {
                    $allColors[] = $variant['color'];
                }
            }
        }
    }
}

// Remove duplicates and sort colors
$allColors = array_unique($allColors);
sort($allColors);

// Debug: Log the colors found
error_log('Kids dynamic colors found: ' . json_encode($allColors));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($page_title) ? $page_title : 'Kids\' Clothing - Glamour Palace'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../heading/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/responsive.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/mobile-filter-responsive.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="../reviews-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../related-products.js?v=<?php echo time(); ?>"></script>
    <script src="simple-filter.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-wishlist-fix.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/sold-out-manager.js?v=<?php echo time(); ?>"></script>
    <script src="cart-manager.js?v=<?php echo time(); ?>"></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
                    <?php 
                    // Include header with error handling
                    try {
                        include '../heading/header.php'; 
                    } catch (Exception $e) {
                        // If header fails due to MongoDB issues, show a simple header
                        echo '<div class="simple-header">';
                        echo '<h1>Kids\' Clothing - Glamour Palace</h1>';
                        echo '<nav><a href="../index.php">Home</a> | <a href="kids.php">Kids</a></nav>';
                        echo '</div>';
                        echo '<style>.simple-header { background: #ff6b9d; color: white; padding: 20px; text-align: center; } .simple-header nav a { color: white; text-decoration: none; margin: 0 10px; }</style>';
                    }
                    ?>

                <!-- Mobile Navigation Overlay -->
                <div class="mobile-nav-overlay" id="mobile-nav-overlay">
                    <div class="mobile-nav-content">
                        <div class="mobile-nav-header">
                            <div class="mobile-nav-logo">
                                <div class="logo-main">Glamour Palace</div>
                                <div class="logo-accent">FASHION & LIFESTYLE</div>
                            </div>
                            <button class="mobile-nav-close" id="mobile-nav-close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mobile-nav-menu">
                            <ul class="mobile-nav-list">
                                <li><a href="../index.php" class="mobile-nav-link">Home</a></li>
                                <li><a href="../womenF/women.php" class="mobile-nav-link">Women</a></li>
                                <li><a href="../menfolder/men.php" class="mobile-nav-link">Men</a></li>
                                <li><a href="kids.php" class="mobile-nav-link">Kids</a></li>
                                <li><a href="../beautyfolder/beauty.php" class="mobile-nav-link">Beauty</a></li>
                                <li><a href="../bagsfolder/bags.php" class="mobile-nav-link">Bags</a></li>
                                <li><a href="../shoess/shoes.php" class="mobile-nav-link">Shoes</a></li>
                                <li><a href="../accessories/accessories.php" class="mobile-nav-link">Accessories</a></li>
                                <li><a href="../perfumes/index.php" class="mobile-nav-link">Perfumes</a></li>
                                <li><a href="../homedecor/homedecor.php" class="mobile-nav-link">Home Decor</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Image Bar Section -->
                <div class="image-bar">
                    <a href="kids.php" class="image-item">
                        <img src="../img/category/kidcollection.jpg" alt="Kids Clothing" onerror="this.src='https://picsum.photos/200/150?random=1'">
                        <h3>Shop All</h3>
                    </a>
                    <a href="kids.php?subcategory=Boys" class="image-item">
                        <img src="../img/shoes/boy/1.avif" alt="Boys" onerror="this.src='https://picsum.photos/200/150?random=2'">
                        <h3>Boys</h3>
                    </a>
                    <a href="kids.php?subcategory=Girls" class="image-item">
                        <img src="../img/children/girls/1.jpeg" alt="Girls" onerror="this.src='https://picsum.photos/200/150?random=3'">
                        <h3>Girls</h3>
                    </a>
                    <a href="kids.php?subcategory=Toddlers" class="image-item">
                        <img src="../img/shoes/infant/1.webp" alt="Toddlers" onerror="this.src='https://picsum.photos/200/150?random=4'">
                        <h3>Toddlers</h3>
                    </a>
                    <a href="kids.php?subcategory=Baby" class="image-item">
                        <img src="../img/child/1.webp" alt="Baby" onerror="this.src='https://picsum.photos/200/150?random=5'">
                        <h3>Baby</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php 
                    // Pass subcategory to main content
                    $GLOBALS['current_subcategory'] = $subcategory;
                    include 'includes/main-content.php'; 
                    ?>
                </div>

            <!-- Enhanced Features Scripts (Reviews & Related Products Only) -->
        
        <script>
            // Initialize enhanced features when page loads
            document.addEventListener('DOMContentLoaded', function() {
                
                
                // Load reviews and related products for all product cards
                const productCards = document.querySelectorAll('.product-card');
                console.log('Found product cards:', productCards.length);
                productCards.forEach(card => {
                    const productId = card.getAttribute('data-product-id');
                    if (productId) {
                        setTimeout(() => {
                            loadProductFeatures(productId, 'Kids\' Clothing', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Kids\' Clothing', subcategory = 'General') {
                // Update mini reviews display
                const reviewsMini = document.getElementById(`reviews-mini-${productId}`);
                if (reviewsMini && typeof reviewsManager !== 'undefined') {
                    reviewsManager.loadReviews(productId).then(reviews => {
                        if (reviews.length > 0) {
                            const avgRating = reviews.reduce((sum, review) => sum + review.rating, 0) / reviews.length;
                            const stars = '★'.repeat(Math.floor(avgRating)) + '☆'.repeat(5 - Math.floor(avgRating));
                            reviewsMini.querySelector('.rating-stars').textContent = stars;
                            reviewsMini.querySelector('.review-count').textContent = `(${reviews.length} reviews)`;
                        }
                    });
                }
                
                // Update mini related products
                const relatedMini = document.getElementById(`mini-related-${productId}`);
                if (relatedMini && typeof relatedProductsManager !== 'undefined') {
                    relatedProductsManager.loadRelatedProducts(productId, category, subcategory).then(products => {
                        if (products.length > 0) {
                            const miniProducts = products.slice(0, 3); // Show only 3 mini products
                            relatedMini.innerHTML = miniProducts.map(product => `
                                <div class="mini-product-card" onclick="openQuickView('${product._id}')">
                                    <img src="${product.front_image || 'https://picsum.photos/100/60?random=30'}" 
                                         alt="${product.name}"
                                         onerror="this.src='https://picsum.photos/100/60?random=${Math.floor(Math.random() * 100)}'">
                                    <h5>${product.name}</h5>
                                    <span class="price">$${product.price.toFixed(2)}</span>
                                </div>
                            `).join('');
                        }
                    });
                }
            }
        </script>

        <!-- Simple Sorting Function -->
        <script>
        function updateSort(sortValue) {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', sortValue);
            
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newUrl);
            window.location.reload();
        }
        </script>

        <!-- Quick View Sidebar -->
        <div id="quick-view-sidebar" class="quick-view-sidebar">
            <div class="quick-view-header">
                <h2 id="quick-view-title">Product Name</h2>
                <button id="close-quick-view" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="quick-view-content">
                <div class="quick-view-images">
                    <div class="main-image-container">
                        <img id="quick-view-main-image" src="" alt="Product Image">
                        <video id="quick-view-main-video" style="display: none;" muted loop></video>
                    </div>
                    <div class="thumbnail-images" id="quick-view-thumbnails">
                        <!-- Thumbnails will be populated by JavaScript -->
                    </div>
                </div>
                
                <div class="quick-view-details">
                    <div class="quick-view-price-section">
                        <span id="quick-view-price" class="price">$0.00</span>
                        <span id="quick-view-sale-price" class="sale-price" style="display: none;">$0.00</span>
                    </div>
                    
                    <div class="quick-view-rating">
                        <div class="stars" id="quick-view-stars">
                            <span class="rating-stars">★★★★★</span>
                            <span class="review-count" id="quick-view-review-count">(0 reviews)</span>
                        </div>
                    </div>
                    
                    <div class="quick-view-availability" id="quick-view-availability">
                        <!-- Availability will be populated by JavaScript -->
                    </div>
                    
                    <div class="quick-view-description">
                        <p id="quick-view-description-text">A beautiful product perfect for any occasion. Features a durable design and comfortable experience.</p>
                    </div>
                    
                    <div class="quick-view-options">
                        <div class="color-section">
                            <h4>Color:</h4>
                            <div class="color-selection" id="quick-view-color-selection">
                                <!-- Colors will be populated by JavaScript -->
                            </div>
                        </div>
                        
                        <div class="size-section">
                            <h4>Size:</h4>
                            <div class="size-selection" id="quick-view-size-selection">
                                <!-- Sizes will be populated by JavaScript -->
                            </div>
                        </div>
                        
                        <div class="quantity-section">
                            <label for="quick-view-quantity">Quantity:</label>
                            <input type="number" id="quick-view-quantity" value="1" min="1" max="99">
                        </div>
                    </div>
                    
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
                </div>
            </div>
        </div>
        
        <!-- Overlay -->
        <div id="quick-view-overlay" class="quickview-overlay"></div>

        <script>
            // Mobile Filter Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const mobileFilterBtn = document.getElementById('mobile-filter-btn');
                const mobileFilterOverlay = document.getElementById('mobile-filter-overlay');
                const mobileFilterClose = document.getElementById('mobile-filter-close');
                const mobileClearFilters = document.getElementById('mobile-clear-filters');
                const mobileApplyFilters = document.getElementById('mobile-apply-filters');
                const body = document.body;

                // Open mobile filter menu
                if (mobileFilterBtn) {
                    mobileFilterBtn.addEventListener('click', function() {
                        mobileFilterOverlay.classList.add('active');
                        body.classList.add('mobile-filter-open');
                    });
                }

                // Close mobile filter menu
                if (mobileFilterClose) {
                    mobileFilterClose.addEventListener('click', function() {
                        mobileFilterOverlay.classList.remove('active');
                        body.classList.remove('mobile-filter-open');
                    });
                }

                // Close mobile filter when clicking overlay
                if (mobileFilterOverlay) {
                    mobileFilterOverlay.addEventListener('click', function(e) {
                        if (e.target === mobileFilterOverlay) {
                            mobileFilterOverlay.classList.remove('active');
                            body.classList.remove('mobile-filter-open');
                        }
                    });
                }

                // Clear all filters
                if (mobileClearFilters) {
                    mobileClearFilters.addEventListener('click', function() {
                        const checkboxes = mobileFilterOverlay.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    });
                }

                // Apply filters
                if (mobileApplyFilters) {
                    mobileApplyFilters.addEventListener('click', function() {
                        // Get selected filters
                        const selectedFilters = {};
                        const checkboxes = mobileFilterOverlay.querySelectorAll('input[type="checkbox"]:checked');
                        
                        checkboxes.forEach(checkbox => {
                            const filterType = checkbox.getAttribute('data-filter');
                            if (!selectedFilters[filterType]) {
                                selectedFilters[filterType] = [];
                            }
                            selectedFilters[filterType].push(checkbox.value);
                        });

                        // Apply filters to products
                        applyFilters(selectedFilters);
                        
                        // Close filter menu
                        mobileFilterOverlay.classList.remove('active');
                        body.classList.remove('mobile-filter-open');
                    });
                }

                // Function to update category filter
                function updateCategoryFilter(category, isChecked) {
                    console.log('Category filter updated:', category, isChecked);
                    // This function can be expanded to handle category filtering
                    // For now, it just logs the change
                }

                // Function to clear all filters
                function clearAllFiltersSimple() {
                    console.log('Clearing all filters');
                    // Clear all checkboxes
                    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }

                // Function to select all sizes
                function selectAllSizes() {
                    console.log('Selecting all sizes');
                    const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
                    sizeCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                }

                // Function to clear size filters
                function clearSizeFilters() {
                    console.log('Clearing size filters');
                    const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
                    sizeCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }

                // Function to apply filters
                function applyFilters(filters) {
                    const productCards = document.querySelectorAll('.product-card');
                    
                    productCards.forEach(card => {
                        let shouldShow = true;
                        
                        // Check category filters
                        if (filters.category && filters.category.length > 0) {
                            const productCategory = card.getAttribute('data-product-subcategory');
                            const categoryMatch = filters.category.some(filter => {
                                return productCategory && productCategory.toLowerCase().includes(filter.toLowerCase());
                            });
                            if (!categoryMatch) shouldShow = false;
                        }
                        
                        // Check color filters
                        if (filters.color && filters.color.length > 0) {
                            const productColor = card.getAttribute('data-product-color');
                            const colorMatch = filters.color.some(filter => {
                                return productColor && productColor.toLowerCase() === filter.toLowerCase();
                            });
                            if (!colorMatch) shouldShow = false;
                        }
                        
                        // Check price filters
                        if (filters.price_range && filters.price_range.length > 0) {
                            const productPrice = parseFloat(card.getAttribute('data-product-price'));
                            const priceMatch = filters.price_range.some(filter => {
                                switch(filter) {
                                    case '0-100':
                                        return productPrice >= 0 && productPrice <= 100;
                                    case '100-200':
                                        return productPrice > 100 && productPrice <= 200;
                                    case '200-400':
                                        return productPrice > 200 && productPrice <= 400;
                                    case '400+':
                                        return productPrice > 400;
                                    case 'on-sale':
                                        // You can add sale logic here
                                        return false;
                                    default:
                                        return true;
                                }
                            });
                            if (!priceMatch) shouldShow = false;
                        }
                        
                        // Show or hide product card
                        if (shouldShow) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024) {
                        mobileFilterOverlay.classList.remove('active');
                        body.classList.remove('mobile-filter-open');
                    }
                });
            });
        </script>

        <script>
        // Dynamic color updates - check for new colors every 10 seconds
        let currentColorCount = <?php echo count($allColors); ?>;
        
        function checkForNewColors() {
            fetch('get-colors-api.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count > currentColorCount) {
                        console.log('New colors detected! Refreshing page...');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.log('Error checking for new colors:', error);
                });
        }
        
        // Check for new colors every 10 seconds
        setInterval(checkForNewColors, 10000);
        </script>

</body>
</html>
