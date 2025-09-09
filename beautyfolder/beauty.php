<?php
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
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="quickview-manager.js?v=<?php echo time(); ?>" defer></script>
    <script src="cart-manager.js?v=<?php echo time(); ?>" defer></script>
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
                    <a href="beauty.php?subcategory=Hair" class="image-item">
                        <img src="../img/beauty/hair/shampoo/1.webp" alt="Hair Care">
                        <h3>Hair Care</h3>
                    </a>
                    <a href="beauty.php?subcategory=Bath & Body" class="image-item">
                        <img src="../img/beauty/bathbody/showergel/1.webp" alt="Bath & Body">
                        <h3>Bath & Body</h3>
                    </a>
                    <a href="beauty.php?subcategory=perfumes" class="image-item">
                        <img src="../img/perfumes/1.webp" alt="Perfumes">
                        <h3>Perfumes</h3>
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

</body>
</html>
