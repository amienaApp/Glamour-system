<?php
$page_title = 'Galamor palace';
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
    <!-- Centralized Header CSS -->
    <link rel="stylesheet" href="../heading/header.css?v=<?php echo time(); ?>">
    <!-- Page-specific CSS -->
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/responsive.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
    <?php include '../heading/header.php'; ?>

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
                            loadProductFeatures(productId, 'Accessories', 'General');
                        }, 100);
                    }
                });
                
                // Mobile Filter Functionality
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
                                    case '0-50':
                                        return productPrice >= 0 && productPrice <= 50;
                                    case '50-100':
                                        return productPrice > 50 && productPrice <= 100;
                                    case '100-200':
                                        return productPrice > 100 && productPrice <= 200;
                                    case '200-500':
                                        return productPrice > 200 && productPrice <= 500;
                                    case '500+':
                                        return productPrice > 500;
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
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Accessories', subcategory = 'General') {
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
        <script src="script.js?v=<?php echo time(); ?>"></script>
        <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
        <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
        <script src="../scripts/quickview-manager.js?v=<?php echo time(); ?>"></script>
        <script src="search.js?v=<?php echo time(); ?>"></script>

</body>
</html> 