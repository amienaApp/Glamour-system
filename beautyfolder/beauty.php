<?php
session_start();
$page_title = 'Galamor palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}

// Load categories and subcategories from database
require_once '../config1/mongodb.php';
require_once '../models/Category.php';

$categoryModel = new Category();
$beautyCategory = $categoryModel->getByName("Beauty & Cosmetics");
$subcategories = [];

if ($beautyCategory && isset($beautyCategory['subcategories'])) {
    // Convert BSONArray to regular array if needed
    $allSubcategories = [];
    foreach ($beautyCategory['subcategories'] as $sub) {
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
    'Makeup' => '../img/beauty/1.png',
    'Skincare' => '../img/beauty/2.png',
    'Hair Care' => '../img/beauty/3.png',
    'Fragrance' => '../img/beauty/4.png',
    'Tools & Brushes' => '../img/beauty/5.png',
    'Nail Care' => '../img/beauty/6.png'
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
    <link rel="stylesheet" href="styles/filter-styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/sold-out-manager.js?v=<?php echo time(); ?>"></script>
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
                        echo '<h1>Beauty & Cosmetics - Glamour Palace</h1>';
                        echo '<nav><a href="../index.php">Home</a> | <a href="beauty.php">Beauty</a></nav>';
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
                                <li><a href="../kidsfolder/kids.php" class="mobile-nav-link">Kids</a></li>
                                <li><a href="beauty.php" class="mobile-nav-link">Beauty</a></li>
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
                    <a href="beauty.php" class="image-item">
                        <img src="../img/beauty/1.png" alt="Beauty & Cosmetics">
                        <h3>Shop All</h3>
                    </a>
                    <a href="beauty.php?subcategory=Makeup" class="image-item">
                        <img src="../img/beauty/makeup/face/foundation/1.webp" alt="Makeup">
                        <h3>Makeup</h3>
                    </a>
                    <a href="beauty.php?subcategory=Skincare" class="image-item">
                        <img src="../img/beauty/skincare/mostruiser/1.webp" alt="Skincare">
                        <h3>Skincare</h3>
                    </a>
                    <a href="beauty.php?subcategory=Hair Care" class="image-item">
                        <img src="../img/beauty/hair/shampoo/1.webp" alt="Hair Care">
                        <h3>Hair Care</h3>
                    </a>
                    <a href="beauty.php?subcategory=Bath & Body" class="image-item">
                        <img src="../img/beauty/bathbody/showergel/1.webp" alt="Bath & Body">
                        <h3>Bath & Body</h3>
                    </a>
                    <a href="beauty.php?subcategory=tools" class="image-item">
                        <img src="../img/beauty/makeup/face/brushes/1.webp" alt="Beauty Tools">
                        <h3>Beauty Tools</h3>
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
                                        case '0-25':
                                            return productPrice >= 0 && productPrice <= 25;
                                        case '25-50':
                                            return productPrice > 25 && productPrice <= 50;
                                        case '50-100':
                                            return productPrice > 50 && productPrice <= 100;
                                        case '100-200':
                                            return productPrice > 100 && productPrice <= 200;
                                        case '200+':
                                            return productPrice > 200;
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
            // Initialize enhanced features when page loads
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Beauty page DOM loaded');
                
                // Debug: Check if sidebar filters exist
                const filterCheckboxes = document.querySelectorAll('input[data-filter]');
                console.log('Found filter checkboxes in beauty page:', filterCheckboxes.length);
                
                // Debug: Check if clear filters button exists
                const clearBtn = document.getElementById('clear-filters');
                console.log('Clear filters button found:', !!clearBtn);
                
                // Load reviews and related products for all product cards
                const productCards = document.querySelectorAll('.product-card');
                console.log('Found product cards:', productCards.length);
                productCards.forEach(card => {
                    const productId = card.getAttribute('data-product-id');
                    if (productId) {
                        setTimeout(() => {
                            loadProductFeatures(productId, 'Beauty & Cosmetics', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Beauty & Cosmetics', subcategory = 'General') {
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
        <div id="quick-view-sidebar" class="quickview-sidebar">
            <button class="close-btn" onclick="closeQuickView()">×</button>
            <div class="quickview-content">
                <div class="product-images">
                    <div class="main-image">
                        <img id="quick-view-main-image" src="" alt="">
                        <video id="quick-view-main-video" style="display: none;" muted loop></video>
                    </div>
                    <div class="image-thumbnails" id="quick-view-thumbnails"></div>
                </div>
                
                <div class="product-info">
                    <h2 id="quick-view-title"></h2>
                    <div class="price-section">
                        <span id="quick-view-price" class="price"></span>
                        <span id="quick-view-sale-price" class="sale-price" style="display: none;"></span>
                    </div>
                    
                    <div class="rating-section">
                        <div class="stars" id="quick-view-stars"></div>
                        <span id="quick-view-review-count"></span>
                    </div>
                    
                    <p id="quick-view-description"></p>
                    
                    <div class="color-section">
                        <h4>Color:</h4>
                        <div class="color-selection" id="quick-view-color-selection"></div>
                    </div>
                    
                    <div class="size-section">
                        <h4>Size:</h4>
                        <div class="size-selection" id="quick-view-size-selection"></div>
                    </div>
                    
                    <div class="quantity-section">
                        <label for="quick-view-quantity">Quantity:</label>
                        <input type="number" id="quick-view-quantity" value="1" min="1" max="99">
                    </div>
                    
                    <div class="action-buttons">
                        <button id="add-to-bag-quick" class="add-to-cart-btn">Add to Cart</button>
                        <button id="add-to-wishlist-quick" class="wishlist-btn">
                            <i class="fas fa-heart"></i> Add to Wishlist
                        </button>
                    </div>
                    
                    <!-- Availability Status -->
                    <div class="quick-view-availability" id="quick-view-availability" style="margin-top: 15px; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600;">
                        <!-- Availability will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overlay -->
        <div id="quick-view-overlay" class="quickview-overlay"></div>

        <!-- Simple Mobile Filters Toggle -->
        <script>
        function toggleMobileFilters() {
            try {
                console.log('toggleMobileFilters called');
                const panel = document.getElementById('mobile-filters-panel');
                const btn = document.querySelector('.mobile-filters-btn');
                
                console.log('Panel found:', !!panel);
                console.log('Button found:', !!btn);
                
                if (panel) {
                    // Debug panel content
                    const content = panel.querySelector('.mobile-filters-content');
                    console.log('Content found:', !!content);
                    if (content) {
                        console.log('Content HTML length:', content.innerHTML.length);
                        console.log('Content children count:', content.children.length);
                        console.log('Content visible:', content.offsetWidth > 0 && content.offsetHeight > 0);
                    }
                    
                    if (panel.classList.contains('active')) {
                        console.log('Closing panel');
                        panel.classList.remove('active');
                        document.body.style.overflow = '';
                    } else {
                        console.log('Opening panel');
                        panel.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        
                        // Force content to be visible for debugging
                        if (content) {
                            content.style.display = 'block';
                            content.style.visibility = 'visible';
                            content.style.opacity = '1';
                            content.style.zIndex = '99999';
                            content.style.position = 'relative';
                            content.style.background = 'white';
                            content.style.border = '3px solid red';
                            content.style.padding = '20px';
                            content.style.minHeight = '300px';
                        }
                    }
                } else {
                    console.error('Panel not found!');
                }
            } catch (error) {
                console.error('Error in toggleMobileFilters:', error);
            }
        }
        
        // Close mobile filters function
        function closeMobileFilters() {
            console.log('closeMobileFilters called');
            const panel = document.getElementById('mobile-filters-panel');
            if (panel) {
                panel.classList.remove('active');
                document.body.style.overflow = '';
                console.log('Panel closed');
            }
        }
        
        // Close filters when clicking outside
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('mobile-filters-panel');
            const btn = document.querySelector('.mobile-filters-btn');
            
            if (panel && panel.classList.contains('active') && 
                !btn.contains(e.target) && 
                !panel.contains(e.target)) {
                closeMobileFilters();
            }
        });
        
        // Debug on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, checking elements:');
            console.log('Panel:', document.getElementById('mobile-filters-panel'));
            console.log('Button:', document.querySelector('.mobile-filters-btn'));
            
            // Initialize mobile filters panel
            const panel = document.getElementById('mobile-filters-panel');
            if (panel) {
                console.log('Mobile filters panel initialized');
                
                // Check content panel
                const content = panel.querySelector('.mobile-filters-content');
                if (content) {
                    console.log('Content panel found and ready');
                }
            }
        });
        
        // Initialize style count on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Count the initial products displayed
            const productCards = document.querySelectorAll('.product-card');
            const initialCount = productCards.length;
            updateStyleCount(initialCount);
        });
        </script>

</body>
</html>
