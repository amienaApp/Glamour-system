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
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/quickview-manager.js"></script>
</head>
<body>
                    <?php include '../heading/header.php'; ?>

                <!-- Image Bar Section -->
                <div class="image-bar">
                    <a href="shoes.php" class="image-item">
                        <img src="../img/shoes/1.webp" alt="All Shoes">
                        <h3>Shop All</h3>
                    </a>
                    <a href="shoes.php?subcategory=menshoes" class="image-item">
                        <img src="../img/shoes/menshoes/1.0.jpg" alt="Men's Shoes">
                        <h3>Men's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=womenshoes" class="image-item">
                        <img src="../img/shoes/womenshoes/1.0.avif" alt="Women's Shoes">
                        <h3>Women's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=childrenshoes" class="image-item">
                        <img src="../img/shoes/boy/1.0.avif" alt="Children's Shoes">
                        <h3>Children's Shoes</h3>
                    </a>
                    <a href="shoes.php?subcategory=formalshoes" class="image-item">
                        <img src="../img/shoes/menshoes/1.1.jpg" alt="Formal Shoes">
                        <h3>Formal Shoes</h3>
                    </a>
                </div>

                <div class="page-layout">
                    <?php include 'includes/sidebar.php'; ?>
                    <?php include 'includes/main-content.php'; ?>
                </div>

            <!-- Enhanced Features Scripts -->
        
        <script>
            // Initialize page when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                
                // Initialize any page-specific functionality here
                const productCards = document.querySelectorAll('.product-card');
            });
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