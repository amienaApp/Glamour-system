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
require_once '../includes/filter-data-helper.php';

// Get dynamic filter data for sidebar
$filterData = getFilterData('Women\'s Clothing');

// Extract all unique colors from ALL women's products for dynamic color filter
require_once '../models/Product.php';
$productModel = new Product();
$allWomenProducts = $productModel->getByCategory('Women\'s Clothing');

$allColors = [];

foreach ($allWomenProducts as $product) {
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

$categoryModel = new Category();
$womenCategory = $categoryModel->getByName("Women's Clothing");
$subcategories = [];

if ($womenCategory && isset($womenCategory['subcategories'])) {
    // Convert BSONArray to regular array if needed
    $allSubcategories = [];
    foreach ($womenCategory['subcategories'] as $sub) {
        if (is_array($sub) && isset($sub['name'])) {
            $allSubcategories[] = $sub['name'];
        } elseif (is_object($sub) && isset($sub['name'])) {
            $allSubcategories[] = $sub['name'];
        } else {
            $allSubcategories[] = $sub;
        }
    }
    
    // Filter out unwanted subcategories
    $excludedSubcategories = ['Outerwear', 'Bottoms'];
    $subcategories = array_filter($allSubcategories, function($subcategory) use ($excludedSubcategories) {
        return !in_array($subcategory, $excludedSubcategories);
    });
}

// Define image mapping for subcategories
$subcategoryImages = [
    'Dresses' => '../img/women/13.webp',
    'Tops' => '../img/women/tops/1.webp',
    'Activewear' => '../img/women/tops/1.webp', // Use tops image since activewear doesn't exist
    'Wedding Dress' => '../img/women/wedding/1.webp',
    'Bridesmaid Wear' => '../img/women/brides maid/1.jpg', // Fixed: brides maid (with space)
    'Wedding Guest' => '../img/women/14.avif',
    'Summer Dresses' => '../img/women/dresses/20.1.webp'
];

// Default image for subcategories without specific images
$defaultImage = '../img/women/dresses/12.webp';
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
    <script src="simple-filter.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
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
                                <li><a href="women.php" class="mobile-nav-link">Women</a></li>
                                <li><a href="../menfolder/men.php" class="mobile-nav-link">Men</a></li>
                                <li><a href="../kidsfolder/kids.php" class="mobile-nav-link">Kids</a></li>
                                <li><a href="../beautyfolder/beauty.php" class="mobile-nav-link">Beauty</a></li>
                                <li><a href="../bagsfolder/bags.php" class="mobile-nav-link">Bags</a></li>
                                <li><a href="../shoess/shoes.php" class="mobile-nav-link">Shoes</a></li>
                                <li><a href="../accessories/accessories.php" class="mobile-nav-link">Accessories</a></li>
                                <li><a href="../perfumes/perfumes.php" class="mobile-nav-link">Perfumes</a></li>
                                <li><a href="../homedecor/homedecor.php" class="mobile-nav-link">Home Decor</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Image Bar Section -->
                <div class="image-bar">
                    <!-- Shop All Link -->
                    <a href="women.php" class="image-item">
                        <img src="../img/women/dresses/12.webp" alt="Shop All Women's Clothing">
                        <h3>Shop All</h3>
                    </a>
                    
                    <?php if (!empty($subcategories)): ?>
                        <?php foreach ($subcategories as $subcategoryName): ?>
                            <?php
                            // Convert subcategory name to URL-friendly format
                            $subcategoryUrl = strtolower(str_replace([' ', '&'], ['-', 'and'], $subcategoryName));
                            
                            // Get image for this subcategory
                            $subcategoryImage = $subcategoryImages[$subcategoryName] ?? $defaultImage;
                            
                            // Convert display name (handle special cases)
                            $displayName = $subcategoryName;
                            if ($subcategoryName === 'Wedding Dress') {
                                $displayName = 'Wedding Dress';
                            } elseif ($subcategoryName === 'Bridesmaid Wear') {
                                $displayName = 'Bridesmaid Wear';
                            }
                            ?>
                            <a href="women.php?subcategory=<?php echo urlencode($subcategoryUrl); ?>" class="image-item">
                                <img src="<?php echo htmlspecialchars($subcategoryImage); ?>" 
                                     alt="<?php echo htmlspecialchars($subcategoryName); ?>"
                                     onerror="this.src='<?php echo $defaultImage; ?>'">
                                <h3><?php echo htmlspecialchars($displayName); ?></h3>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback if no subcategories found -->
                        <a href="women.php?subcategory=dresses" class="image-item">
                            <img src="../img/women/13.webp" alt="Dresses">
                            <h3>Dresses</h3>
                        </a>
                        <a href="women.php?subcategory=tops" class="image-item">
                            <img src="../img/women/tops/1.webp" alt="Tops">
                            <h3>Tops</h3>
                        </a>
                        <a href="women.php?subcategory=bottoms" class="image-item">
                            <img src="../img/women/jeans/1.webp" alt="Bottoms">
                            <h3>Bottoms</h3>
                        </a>
                    <?php endif; ?>
                </div>

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
                            loadProductFeatures(productId, 'Women', 'General');
                        }, 100);
                    }
                });
            });
            
            // Function to load reviews and related products for product cards
            function loadProductFeatures(productId, category = 'Women', subcategory = 'General') {
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

        <!-- Mobile Navigation JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const mobileNavOverlay = document.getElementById('mobile-nav-overlay');
            const mobileNavClose = document.getElementById('mobile-nav-close');
            const body = document.body;

            // Open mobile navigation
            if (hamburgerMenu) {
                hamburgerMenu.addEventListener('click', function() {
                    mobileNavOverlay.classList.add('active');
                    body.classList.add('mobile-nav-open');
                    hamburgerMenu.classList.add('active');
                });
            }

            // Close mobile navigation
            if (mobileNavClose) {
                mobileNavClose.addEventListener('click', function() {
                    mobileNavOverlay.classList.remove('active');
                    body.classList.remove('mobile-nav-open');
                    if (hamburgerMenu) {
                        hamburgerMenu.classList.remove('active');
                    }
                });
            }

            // Close mobile navigation when clicking overlay
            if (mobileNavOverlay) {
                mobileNavOverlay.addEventListener('click', function(e) {
                    if (e.target === mobileNavOverlay) {
                        mobileNavOverlay.classList.remove('active');
                        body.classList.remove('mobile-nav-open');
                        if (hamburgerMenu) {
                            hamburgerMenu.classList.remove('active');
                        }
                    }
                });
            }

            // Close mobile navigation when clicking on a link
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileNavOverlay.classList.remove('active');
                    body.classList.remove('mobile-nav-open');
                    if (hamburgerMenu) {
                        hamburgerMenu.classList.remove('active');
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    mobileNavOverlay.classList.remove('active');
                    body.classList.remove('mobile-nav-open');
                    if (hamburgerMenu) {
                        hamburgerMenu.classList.remove('active');
                    }
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

        <script>
            // Function to populate mobile filter options
            function populateMobileFilters() {
                // Get filter options from the sidebar
                const sidebar = document.querySelector('.sidebar');
                if (!sidebar) return;

                // Populate category filters
                const categoryOptions = sidebar.querySelectorAll('input[name="subcategory[]"]');
                const mobileCategoryFilter = document.getElementById('mobile-category-filter');
                if (mobileCategoryFilter && categoryOptions.length > 0) {
                    mobileCategoryFilter.innerHTML = '';
                    categoryOptions.forEach(option => {
                        const filterOption = document.createElement('div');
                        filterOption.className = 'mobile-filter-option';
                        filterOption.innerHTML = `
                            <input type="checkbox" id="mobile-${option.value}" value="${option.value}" data-filter="category">
                            <label for="mobile-${option.value}">${option.nextElementSibling.textContent.trim()}</label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        `;
                        mobileCategoryFilter.appendChild(filterOption);
                    });
                }

                // Populate color filters
                const colorOptions = sidebar.querySelectorAll('.color-option');
                const mobileColorFilter = document.getElementById('mobile-color-filter');
                if (mobileColorFilter && colorOptions.length > 0) {
                    mobileColorFilter.innerHTML = '';
                    colorOptions.forEach(option => {
                        const color = option.getAttribute('data-color');
                        const filterOption = document.createElement('div');
                        filterOption.className = 'mobile-color-option';
                        filterOption.innerHTML = `
                            <input type="checkbox" id="mobile-color-${color}" value="${color}" data-filter="color">
                            <label for="mobile-color-${color}">
                                <span class="color-swatch" style="background-color: ${color};"></span>
                                <span class="color-name">${color}</span>
                            </label>
                            <i class="fas fa-check mobile-checkmark"></i>
                        `;
                        mobileColorFilter.appendChild(filterOption);
                    });
                }
            }

            // Function to sync mobile filter state with sidebar
            function syncMobileFilterState() {
                const sidebar = document.querySelector('.sidebar');
                if (!sidebar) return;

                // Sync category filters
                const sidebarCategoryOptions = sidebar.querySelectorAll('input[name="subcategory[]"]');
                sidebarCategoryOptions.forEach(sidebarOption => {
                    const mobileOption = document.getElementById(`mobile-${sidebarOption.value}`);
                    if (mobileOption) {
                        mobileOption.checked = sidebarOption.checked;
                    }
                });

                // Sync color filters
                const sidebarColorOptions = sidebar.querySelectorAll('.color-option');
                sidebarColorOptions.forEach(sidebarOption => {
                    const color = sidebarOption.getAttribute('data-color');
                    const mobileOption = document.getElementById(`mobile-color-${color}`);
                    if (mobileOption) {
                        mobileOption.checked = sidebarOption.classList.contains('selected');
                    }
                });
            }

            // Mobile Filter Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const mobileFilterBtn = document.getElementById('mobile-filter-btn');
                const mobileFilterOverlay = document.getElementById('mobile-filter-overlay');
                const mobileFilterClose = document.getElementById('mobile-filter-close');
                const mobileClearFilters = document.getElementById('mobile-clear-filters');
                const mobileApplyFilters = document.getElementById('mobile-apply-filters');
                const body = document.body;

                // Populate mobile filter options
                populateMobileFilters();
                
                // Sync mobile filter state with sidebar
                syncMobileFilterState();

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
