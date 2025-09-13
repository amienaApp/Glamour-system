<?php
session_start();
$page_title = 'Galamor palace';

// Handle subcategory parameter
$subcategory = $_GET['subcategory'] ?? '';
$page_title = $subcategory ? ucfirst($subcategory) . ' Beauty - ' . $page_title : 'Beauty & Cosmetics - ' . $page_title;
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
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="../reviews-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../related-products.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="quickview-manager.js?v=<?php echo time(); ?>"></script>
    <script src="cart-manager.js?v=<?php echo time(); ?>"></script>
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

</body>
</html>
