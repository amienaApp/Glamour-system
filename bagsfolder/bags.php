<?php
session_start();
$page_title = 'Galamor palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' Bags - ' . $page_title;
} else {
    $page_title = 'Bags - ' . $page_title;
}

// Define bag subcategories
$subcategories = [
    'Shoulder Bags',
    'Clutches',
    'Tote Bags',
    'Crossbody Bags',
    'Backpacks',
    'Briefcases',
    'Laptop Bags',
    'Waist Bags',
    'Wallets'
];

// Define image mapping for subcategories
$subcategoryImages = [
    'Shoulder Bags' => '../img/bags/shoulder/1.webp',
    'Clutches' => '../img/bags/clutches/1.webp',
    'Tote Bags' => '../img/bags/tote/1.webp',
    'Crossbody Bags' => '../img/bags/crossbody/1.webp',
    'Backpacks' => '../img/bags/backpack/1.webp',
    'Briefcases' => '../img/bags/briefcase/1.webp',
    'Laptop Bags' => '../img/bags/laptop/1.webp',
    'Waist Bags' => '../img/bags/waist/1.webp',
    'Wallets' => '../img/bags/wallet/1.webp'
];

// Default image for subcategories without specific images
$defaultImage = '../img/bags/default/1.webp';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($page_title) ? $page_title : 'Bags - Glamour Palace'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../heading/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/responsive.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

            <!-- Enhanced Features Scripts (Reviews & Related Products Only) -->
        
        <script>
            // Initialize enhanced features when page loads
            document.addEventListener('DOMContentLoaded', function() {
                // Load reviews and related products for all product cards
                const productCards = document.querySelectorAll('.product-card');
                productCards.forEach(card => {
                    const productId = card.getAttribute('data-product-id');
                    if (productId) {
                        setTimeout(() => {
                            loadProductFeatures(productId, 'Bags', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Bags', subcategory = 'General') {
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


        <!-- Mobile Filter JavaScript -->
        <script>
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

        <!-- Mobile Filter JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileFilterBtn = document.getElementById('mobile-filter-btn');
            const mobileFilterOverlay = document.getElementById('mobile-filter-overlay');
            const mobileFilterClose = document.getElementById('mobile-filter-close');
            const mobileFilterApply = document.getElementById('mobile-filter-apply');
            const body = document.body;

            // Open mobile filter
            if (mobileFilterBtn) {
                mobileFilterBtn.addEventListener('click', function() {
                    mobileFilterOverlay.classList.add('active');
                    body.classList.add('mobile-filter-open');
                });
            }

            // Close mobile filter
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

            // Apply filters
            if (mobileFilterApply) {
                mobileFilterApply.addEventListener('click', function() {
                    // Get selected filters
                    const selectedFilters = getSelectedFilters();
                    
                    // Apply filters (you can implement your filter logic here)
                    applyFilters(selectedFilters);
                    
                    // Close the filter menu
                    mobileFilterOverlay.classList.remove('active');
                    body.classList.remove('mobile-filter-open');
                });
            }

            // Clear filters
            const mobileClearFilters = document.getElementById('mobile-clear-filters');
            if (mobileClearFilters) {
                mobileClearFilters.addEventListener('click', function() {
                    // Clear all checkboxes
                    const checkboxes = mobileFilterOverlay.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Apply cleared filters
                    applyFilters({});
                });
            }

            // Function to get selected filters
            function getSelectedFilters() {
                const filters = {
                    category: [],
                    color: [],
                    price: []
                };

                const categoryCheckboxes = mobileFilterOverlay.querySelectorAll('input[name="category[]"]:checked');
                const colorCheckboxes = mobileFilterOverlay.querySelectorAll('input[name="color[]"]:checked');
                const priceCheckboxes = mobileFilterOverlay.querySelectorAll('input[name="price[]"]:checked');

                categoryCheckboxes.forEach(checkbox => {
                    filters.category.push(checkbox.value);
                });

                colorCheckboxes.forEach(checkbox => {
                    filters.color.push(checkbox.value);
                });

                priceCheckboxes.forEach(checkbox => {
                    filters.price.push(checkbox.value);
                });

                return filters;
            }

            // Function to apply filters (implement your filter logic here)
            function applyFilters(filters) {
                console.log('Applying filters:', filters);
                // You can implement your actual filter logic here
                // For now, we'll just reload the page with filter parameters
                const params = new URLSearchParams(window.location.search);
                
                // Clear existing filter parameters
                params.delete('category');
                params.delete('color');
                params.delete('price');
                
                // Add new filter parameters
                if (filters.category.length > 0) {
                    params.set('category', filters.category.join(','));
                }
                if (filters.color.length > 0) {
                    params.set('color', filters.color.join(','));
                }
                if (filters.price.length > 0) {
                    params.set('price', filters.price.join(','));
                }
                
                // Reload page with new parameters
                window.location.href = window.location.pathname + '?' + params.toString();
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

        <!-- Mobile Filter Menu JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
            const mobileFilterOverlay = document.getElementById('mobile-filter-overlay');
            const mobileFilterClose = document.getElementById('mobile-filter-close');
            const mobileApplyFilters = document.getElementById('mobile-apply-filters');
            const mobileClearFilters = document.getElementById('mobile-clear-filters');
            const body = document.body;

            // Open mobile filter menu
            if (mobileFilterToggle) {
                mobileFilterToggle.addEventListener('click', function() {
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

            // Close mobile filter menu when clicking overlay
            if (mobileFilterOverlay) {
                mobileFilterOverlay.addEventListener('click', function(e) {
                    if (e.target === mobileFilterOverlay) {
                        mobileFilterOverlay.classList.remove('active');
                        body.classList.remove('mobile-filter-open');
                    }
                });
            }

            // Apply filters
            if (mobileApplyFilters) {
                mobileApplyFilters.addEventListener('click', function() {
                    // Get selected filters
                    const selectedCategories = Array.from(document.querySelectorAll('input[name="mobile-category[]"]:checked')).map(cb => cb.value);
                    const selectedColors = Array.from(document.querySelectorAll('input[name="mobile-color[]"]:checked')).map(cb => cb.value);
                    const selectedPrices = Array.from(document.querySelectorAll('input[name="mobile-price[]"]:checked')).map(cb => cb.value);
                    
                    // Build filter URL
                    const params = new URLSearchParams(window.location.search);
                    
                    // Clear existing filter params
                    params.delete('category');
                    params.delete('color');
                    params.delete('price');
                    
                    // Add new filter params
                    if (selectedCategories.length > 0) {
                        selectedCategories.forEach(cat => params.append('category', cat));
                    }
                    if (selectedColors.length > 0) {
                        selectedColors.forEach(color => params.append('color', color));
                    }
                    if (selectedPrices.length > 0) {
                        selectedPrices.forEach(price => params.append('price', price));
                    }
                    
                    // Redirect with filters
                    const newUrl = window.location.pathname + '?' + params.toString();
                    window.location.href = newUrl;
                });
            }

            // Clear all filters
            if (mobileClearFilters) {
                mobileClearFilters.addEventListener('click', function() {
                    // Uncheck all filter checkboxes
                    const allCheckboxes = document.querySelectorAll('.mobile-filter-content input[type="checkbox"]');
                    allCheckboxes.forEach(cb => cb.checked = false);
                    
                    // Redirect without any filters
                    const newUrl = window.location.pathname;
                    window.location.href = newUrl;
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

        <!-- Quick View Sidebar -->
        <div id="quick-view-sidebar" class="quickview-sidebar">
            <button class="close-btn" onclick="closeQuickView()">×</button>
            <div class="quickview-content">
                <div class="product-images">
                    <div class="main-image">
                        <img id="quick-view-main-image" src="" alt="">
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
                        <button id="add-to-bag-quick-alt" class="add-to-cart-btn">Add to Cart</button>
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

</body>
</html>