<?php
session_start();
$page_title = 'Galamor palace';

// Get subcategory from URL parameter
$subcategory = $_GET['subcategory'] ?? '';

// Set page title based on subcategory
if ($subcategory) {
    $page_title = ucfirst($subcategory) . ' - ' . $page_title;
}
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
            <link rel="stylesheet" href="styles/featured-products.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="styles/quick-view.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
        <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
        <script src="script.js?v=<?php echo time(); ?>" defer></script>
        <script src="js/filtering.js?v=<?php echo time(); ?>" defer></script>
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
                    <a href="homedecor.php?subcategory=living room" class="image-item">
                        <img src="../img/home-decor/livingroom/8.jpg" alt="Living Room">
                        <h3>Living Room</h3>
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

        <!-- Quick View Overlay -->
        <div id="quick-view-overlay" class="quickview-overlay"></div>

</body>
</html> 