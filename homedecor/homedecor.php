<?php
session_start();
$page_title = 'Galamor palace';

// Load filter data helper
require_once '../includes/filter-data-helper.php';

// Get dynamic filter data for sidebar
$filterData = getFilterData('Home & Living');

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}

// Extract all unique colors from ALL home decor products for dynamic color filter
require_once '../models/Product.php';
$productModel = new Product();
$allHomeDecorProducts = $productModel->getByCategory("Home & Living");
$allColors = [];

foreach ($allHomeDecorProducts as $product) {
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

// Define home decor subcategory sizes based on admin panel structure
$homeDecorSubcategorySizes = [
    'bedding' => ['Single', 'Double', 'Queen', 'King', 'Super King'],
    'living room' => ['Small', 'Medium', 'Large', 'Extra Large', 'Sectional'],
    'dinning room' => ['2 Seater', '4 Seater', '6 Seater', '8 Seater', '10 Seater'],
    'kitchen' => ['Compact', 'Standard', 'Large', 'Commercial'],
    'artwork' => ['8x10', '11x14', '16x20', '18x24', '24x36', '30x40'],
    'lightinning' => ['Small', 'Medium', 'Large', 'Extra Large']
];

// Extract actual sizes from products that have them in the database
$databaseSizes = [];
foreach ($allHomeDecorProducts as $product) {
    // Get sizes from main sizes field
    if (!empty($product['sizes'])) {
        $sizes = is_string($product['sizes']) ? 
            json_decode($product['sizes'], true) : $product['sizes'];
        if (is_array($sizes)) {
            foreach ($sizes as $size) {
                if (!empty($size)) {
                    $databaseSizes[] = $size;
                }
            }
        }
    }
    
    // Get sizes from selected_sizes field
    if (!empty($product['selected_sizes'])) {
        $selectedSizes = is_string($product['selected_sizes']) ? 
            json_decode($product['selected_sizes'], true) : $product['selected_sizes'];
        if (is_array($selectedSizes)) {
            foreach ($selectedSizes as $size) {
                if (!empty($size)) {
                    $databaseSizes[] = $size;
                }
            }
        }
    }
}

// Get all unique sizes from database
$databaseSizes = array_unique($databaseSizes);

// If we have database sizes, use them. Otherwise, use subcategory-specific sizes
if (!empty($databaseSizes)) {
    $allSizes = $databaseSizes;
} else {
    // Combine all subcategory sizes as fallback
    $allSizes = [];
    foreach ($homeDecorSubcategorySizes as $subcategory => $sizes) {
        $allSizes = array_merge($allSizes, $sizes);
    }
    $allSizes = array_unique($allSizes);
}

sort($allSizes);

// Debug: Log the sizes found
error_log('Home Decor subcategory sizes: ' . json_encode($homeDecorSubcategorySizes));
error_log('Home Decor database sizes: ' . json_encode($databaseSizes));
error_log('Home Decor final sizes: ' . json_encode($allSizes));
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
    <link rel="stylesheet" href="styles/featured-products.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/quick-view.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="simple-filter.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-wishlist-fix.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/sold-out-manager.js?v=<?php echo time(); ?>"></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                <!-- Image Bar Section -->
                <div class="image-bar" >
                    <a href="homedecor.php" class="image-item">
                        <img src="../img/home-decor1.webp" alt="Home Decor">
                        <h3>Shop All</h3>
                    </a>
                    <a href="homedecor.php?subcategory=bedding" class="image-item">
                        <img src="../img/home-decor/bedroom/7.jpg" alt="Bedding">
                        <h3>Bedding</h3>
                    </a>
                    <a href="homedecor.php?subcategory=living room" class="image-item">
                        <img src="../img/home-decor/livingroom/1.jpg" alt="Living Room">
                        <h3>Living Room</h3>
                    </a>
                    <a href="homedecor.php?subcategory=dinning room" class="image-item">
                        <img src="../img/home-decor/diningarea/4.jpg" alt="Dining Room">
                        <h3>Dining Room</h3>
                    </a>
                    <a href="homedecor.php?subcategory=kitchen" class="image-item">
                        <img src="../img/home-decor/kitchen/8.jpg" alt="Kitchen">
                        <h3>Kitchen</h3>
                    </a>
                    <a href="homedecor.php?subcategory=lightinning" class="image-item">
                        <img src="../img/home-decor/light/3.webp" alt="Lighting">
                        <h3>Lighting</h3>
                    </a>
                    <a href="homedecor.php?subcategory=artwork" class="image-item">
                        <img src="../img/home-decor/artwork/21.jpg" alt="Artwork">
                        <h3>Artwork</h3>
                    </a>
                 
                    
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
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
                            loadProductFeatures(productId, 'Home Decor', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Home Decor', subcategory = 'General') {
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


        <!-- Scripts -->
        <script src="script.js?v=<?php echo time(); ?>" defer></script>
        <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
        <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>

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
        
        // Dynamic size updates - check for new sizes every 10 seconds
        let currentSizeCount = <?php echo count($allSizes); ?>;
        
        function checkForNewSizes() {
            fetch('get-sizes-api.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count > currentSizeCount) {
                        console.log('New sizes detected! Refreshing page...');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.log('Error checking for new sizes:', error);
                });
        }
        
        // Check for new colors and sizes every 10 seconds
        setInterval(checkForNewColors, 10000);
        setInterval(checkForNewSizes, 10000);
        </script>

</body>
</html> 