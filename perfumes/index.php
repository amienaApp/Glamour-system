<?php
session_start();
$page_title = 'Galamor palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}

// Extract all unique colors from ALL perfume products for dynamic color filter
require_once '../models/Product.php';
$productModel = new Product();
$allPerfumeProducts = $productModel->getByCategory("Perfumes");
$allColors = [];

foreach ($allPerfumeProducts as $product) {
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
error_log('Perfumes dynamic colors found: ' . json_encode($allColors));

// Load categories and subcategories from database
require_once '../config1/mongodb.php';
require_once '../models/Category.php';
require_once '../includes/filter-data-helper.php';

// Get dynamic filter data for sidebar
$filterData = getFilterData('Perfumes');

$categoryModel = new Category();
$perfumesCategory = $categoryModel->getByName("Perfumes");
$subcategories = [];

if ($perfumesCategory && isset($perfumesCategory['subcategories'])) {
    // Convert BSONArray to regular array if needed
    $allSubcategories = [];
    foreach ($perfumesCategory['subcategories'] as $sub) {
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
    'Men\'s Perfumes' => '../img/perfumes/1.jpg',
    'Women\'s Perfumes' => '../img/perfumes/2.jpg',
    'Unisex Perfumes' => '../img/perfumes/3.jpg',
    'Luxury Perfumes' => '../img/perfumes/4.jpg',
    'Designer Perfumes' => '../img/perfumes/5.jpg',
    'Celebrity Perfumes' => '../img/perfumes/6.jpg'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($page_title) ? $page_title : 'Lulus - Women\'s Clothing & Fashion'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../heading/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/responsive.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="simple-filter.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/sold-out-manager.js?v=<?php echo time(); ?>"></script>
    <script src="search.js?v=<?php echo time(); ?>" defer></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

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
                                <li><a href="../kidsfolder/kids.php" class="mobile-nav-link">Kids</a></li>
                                <li><a href="../beautyfolder/beauty.php" class="mobile-nav-link">Beauty</a></li>
                                <li><a href="../bagsfolder/bags.php" class="mobile-nav-link">Bags</a></li>
                                <li><a href="../shoess/shoes.php" class="mobile-nav-link">Shoes</a></li>
                                <li><a href="../accessories/accessories.php" class="mobile-nav-link">Accessories</a></li>
                                <li><a href="index.php" class="mobile-nav-link">Perfumes</a></li>
                                <li><a href="../homedecor/homedecor.php" class="mobile-nav-link">Home Decor</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/perfumes.php'; ?>
                </div>

            <!-- Enhanced Features Scripts (Reviews & Related Products Only) -->
        <script src="../reviews-manager.js"></script>
        <script src="../related-products.js"></script>
        
        <script>
            // Initialize enhanced features when page loads
            document.addEventListener('DOMContentLoaded', function() {
                // Load reviews and related products for all product cards
                const productCards = document.querySelectorAll('.product-card');
                productCards.forEach(card => {
                    const productId = card.getAttribute('data-product-id');
                    if (productId) {
                        setTimeout(() => {
                            loadProductFeatures(productId, 'Perfumes', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Perfumes', subcategory = 'General') {
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
                        <p id="quick-view-description-text">A beautiful perfume perfect for any occasion. Features a long-lasting fragrance and elegant design.</p>
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
        <div id="quick-view-overlay" class="quick-view-overlay"></div>

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