<?php
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
    <link rel="stylesheet" href="../enhanced-features.css?v=<?php echo time(); ?>">
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/quickview-manager.js?v=<?php echo time(); ?>"></script>
    <script src="search.js?v=<?php echo time(); ?>" defer></script>
    <?php include '../includes/cart-notification-include.php'; ?>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/perfumes.php'; ?>
                </div>

            <!-- Enhanced Features Scripts (Reviews & Related Products Only) -->
        <script src="scripts/simple-notification.js"></script>\n<script src="../reviews-manager.js"></script>
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

</body>
</html> 