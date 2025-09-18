<?php
// Ensure vendor autoload is included first
require_once __DIR__ . '/../vendor/autoload.php';

// Function to get the correct path for assets
function getAssetPath($path) {
    // Check if we're in a subdirectory by looking at the current script's directory
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
    $isInSubdirectory = (substr_count($currentDir, '/') > 1);
    
    return $isInSubdirectory ? '../' . $path : $path;
}
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
$isLoggedIn = isset($_SESSION['user_id']);

// Function to get the correct URL for each category
function getCategoryUrl($categoryName) {
    $categoryMap = [
        "Women's Clothing" => 'womenF/women.php',
        "Men's Clothing" => 'menfolder/men.php',
        "Kids' Clothing" => 'kidsfolder/kids.php',
        "Accessories" => 'accessories/accessories.php',
        "Home & Living" => 'homedecor/homedecor.php',
        "Beauty & Cosmetics" => 'beautyfolder/beauty.php',
        "Perfumes" => 'perfumes/index.php',
        "Shoes" => 'shoess/shoes.php',
        "Bags" => 'bagsfolder/bags.php'
    ];
    
    $path = $categoryMap[$categoryName] ?? '#';
    return $path === '#' ? '#' : getAssetPath($path);
}


// Define region options to avoid duplication
$regionOptions = [
    'banadir' => 'Banadir',
    'bari' => 'Bari',
    'bay' => 'Bay',
    'galguduud' => 'Galguduud',
    'gedo' => 'Gedo',
    'hiran' => 'Hiran',
    'jubbada-dhexe' => 'Jubbada Dhexe',
    'jubbada-hoose' => 'Jubbada Hoose',
    'mudug' => 'Mudug',
    'nugaal' => 'Nugaal',
    'sanaag' => 'Sanaag',
    'shabeellaha-dhexe' => 'Shabeellaha Dhexe',
    'shabeellaha-hoose' => 'Shabeellaha Hoose',
    'sool' => 'Sool',
    'togdheer' => 'Togdheer',
    'woqooyi-galbeed' => 'Woqooyi Galbeed'
];
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- INSTANT Cart Preloader - Loads first for zero delay -->
<script src="<?php echo getAssetPath('scripts/cart-preloader.js'); ?>"></script>

<!-- Top Navigation Bar -->
<nav class="top-nav">
    <!-- Logo Container - Left Side -->
    <div class="logo-container">
        <div class="logo">
            <a href="<?php echo getAssetPath('index.php'); ?>" class="logo-text">
                <span class="logo-main">Glamour</span>
                <span class="logo-accent">Palace</span>
            </a>
        </div>
    </div>

    <!-- Hamburger Menu Button - Mobile Only -->
    <div class="hamburger-menu" id="hamburger-menu">
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
    </div>

    <!-- Navigation Menu - Center -->
    <div class="nav-menu-container">
        <ul class="nav-menu">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="<?php echo getCategoryUrl($category['name']); ?>" class="nav-link">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static menu if no categories found -->
                <li><a href="<?php echo getAssetPath('womenF/women.php'); ?>" class="nav-link">Women's Clothing</a></li>
                <li><a href="<?php echo getAssetPath('menfolder/men.php'); ?>" class="nav-link">Men's Clothing</a></li>
                <li><a href="<?php echo getAssetPath('beautyfolder/beauty.php'); ?>" class="nav-link">Beauty & Cosmetics</a></li>
                <li><a href="<?php echo getAssetPath('shoess/shoes.php'); ?>" class="nav-link">Shoes</a></li>
                <li><a href="<?php echo getAssetPath('bagsfolder/bags.php'); ?>" class="nav-link">Bags</a></li>
                <li><a href="<?php echo getAssetPath('accessories/accessories.php'); ?>" class="nav-link">Accessories</a></li>
                <li><a href="<?php echo getAssetPath('homedecor/homedecor.php'); ?>" class="nav-link">Home & Living</a></li>
                <li><a href="<?php echo getAssetPath('perfumes/index.php'); ?>" class="nav-link">Perfumes</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay">
        <div class="mobile-nav-content">
            <div class="mobile-nav-header">
                <div class="mobile-nav-logo">
                    <span class="logo-main">Glamour</span>
                    <span class="logo-accent">Palace</span>
                </div>
                <button class="mobile-nav-close" id="mobile-nav-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-nav-menu">
                <ul class="mobile-nav-list">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?php echo getCategoryUrl($category['name']); ?>" class="mobile-nav-link">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static menu if no categories found -->
                        <li><a href="<?php echo getAssetPath('womenF/women.php'); ?>" class="mobile-nav-link">Women's Clothing</a></li>
                        <li><a href="<?php echo getAssetPath('menfolder/men.php'); ?>" class="mobile-nav-link">Men's Clothing</a></li>
                        <li><a href="<?php echo getAssetPath('beautyfolder/beauty.php'); ?>" class="mobile-nav-link">Beauty & Cosmetics</a></li>
                        <li><a href="<?php echo getAssetPath('shoess/shoes.php'); ?>" class="mobile-nav-link">Shoes</a></li>
                        <li><a href="<?php echo getAssetPath('bagsfolder/bags.php'); ?>" class="mobile-nav-link">Bags</a></li>
                        <li><a href="<?php echo getAssetPath('accessories/accessories.php'); ?>" class="mobile-nav-link">Accessories</a></li>
                        <li><a href="<?php echo getAssetPath('homedecor/homedecor.php'); ?>" class="mobile-nav-link">Home & Living</a></li>
                        <li><a href="<?php echo getAssetPath('perfumes/index.php'); ?>" class="mobile-nav-link">Perfumes</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Wishlist Scripts will be loaded by individual pages -->

    <!-- Right Side Elements - Compressed -->
    <div class="nav-right-container">
        <!-- Search Box - Compact -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search products...">
        </div>

        <!-- User Actions - Compact -->
        <div class="user-actions">
            <div class="user-dropdown-container">
                <div class="user-icon" id="user-icon" title="Account">
                    <i class="fas fa-user"></i>
                </div>
                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="user-dropdown">
                    <!-- Authentication Section -->
                    <?php if ($isLoggedIn): ?>
                        <div class="auth-section">
                            <div class="user-info">
                                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                            </div>
                            <a href="#" onclick="logout()" class="auth-btn logout-btn">
                                Sign Out
                            </a>
                        </div>
                        
                        <!-- Menu Items for Logged In Users -->
                        <div class="menu-items">
                            <a href="#" class="menu-item" id="dashboard-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="<?php echo getAssetPath('orders.php'); ?>" class="menu-item">
                                <i class="fas fa-box"></i>
                                <span>My orders</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Simple Sign In/Sign Up for Non-Logged Users -->
                        <div class="auth-section">
                            <button class="auth-btn signin-btn" id="signin-btn">
                                <i class="fas fa-sign-in-alt"></i>
                                Sign In
                            </button>
                            <button class="auth-btn signup-btn" id="signup-btn">
                                <i class="fas fa-user-plus"></i>
                                Sign Up
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="heart-icon" title="Wishlist" onclick="toggleWishlistDropdown()" style="cursor: pointer; position: relative;">
                <i class="fas fa-heart"></i>
                <span class="wishlist-count">0</span>
                
                <!-- Wishlist Dropdown -->
                <div class="wishlist-dropdown" id="wishlist-dropdown">
                    <div class="wishlist-dropdown-header">
                        <h3><i class="fas fa-heart"></i> My Wishlist</h3>
                        <button onclick="openWishlistPage()" class="view-all-btn">View All</button>
                    </div>
                    <div class="wishlist-dropdown-content" id="wishlist-dropdown-content">
                        <!-- Wishlist items will be loaded here -->
                    </div>
                    <div class="wishlist-dropdown-empty" id="wishlist-dropdown-empty" style="display: none;">
                        <i class="fas fa-heart"></i>
                        <p>Your wishlist is empty</p>
                        <small>Start adding items you love!</small>
                    </div>
                </div>
            </div>
            <div class="shopping-cart" title="Cart" style="position: relative; text-decoration: none; color: inherit; cursor: pointer;" onclick="window.location.href='<?php echo getAssetPath('cart-unified.php'); ?>'; return false;">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </div>
        </div>

        <!-- Somalia Flag - Compact (Disabled) -->
        <div class="flag-container" title="Region Settings (Coming Soon)">
            <img src="<?php echo getAssetPath('img/flag.jpg'); ?>" alt="Somalia Flag" class="flag" id="somalia-flag">
        </div>
    </div>
</nav>

    <!-- Cart Notification Manager will be loaded by cart-notification-include.php -->

<script>
// Initialize cart functionality
document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.querySelector('.shopping-cart');
    if (cartIcon) {
        
        // Add click event listener for cart functionality (INSTANT)
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get cart path
            const currentPath = window.location.pathname;
            const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                                   currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                                   currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                                   currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                                   currentPath.includes('/bagsfolder/');
            
            // INSTANT redirect - no visual changes, no delays, no waiting
            window.location.href = isInSubdirectory ? '../cart-unified.php' : 'cart-unified.php';
        });
        
        // Add a small tooltip to show cart is clickable
        cartIcon.title = 'Click to view cart';
        
        // Add hover effects for better UX
        cartIcon.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0, 123, 255, 0.1)';
        });
        
        cartIcon.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
        
        // Cart count updates are now handled by cart notification manager
        // The manager will automatically load and update cart count
        
        // Cart count updates are now handled by cart notification manager
        // No need for periodic updates as the manager handles real-time updates
    }
    
        // Legacy updateCartCount function - now handled by cart notification manager
        function updateCartCount() {
            if (window.cartNotificationManager) {
                return window.cartNotificationManager.refreshCart();
            } else {
                console.warn('Cart notification manager not available for cart count update');
            }
        }
    
    // Cart notification function
    function showCartNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'info' ? '#17a2b8' : '#6c757d',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            zIndex: '10000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            maxWidth: '300px',
            fontSize: '14px',
            fontWeight: '500'
        });
        
        // Style the notification content
        const content = notification.querySelector('.notification-content');
        Object.assign(content.style, {
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
        });
        
        document.body.appendChild(notification);
        
        // Show notification (instant)
        notification.style.transform = 'translateX(0)';
        
        // Hide notification
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Legacy updateCartCount function - now handled by cart notification manager
    function updateCartCount() {
        if (window.cartNotificationManager) {
            return window.cartNotificationManager.refreshCart();
        } else {
            console.warn('Cart notification manager not available for cart count update');
        }
    }
    
    // Legacy cart count functions - now handled by cart notification manager
    function addToCartCount() {
        if (window.cartNotificationManager) {
            return window.cartNotificationManager.refreshCart();
        }
    }
    
    function removeFromCartCount() {
        if (window.cartNotificationManager) {
            return window.cartNotificationManager.refreshCart();
        }
    }

    // Make functions available globally
    window.updateCartCount = updateCartCount;
    window.addToCartCount = addToCartCount;
    window.removeFromCartCount = removeFromCartCount;
    window.showCartNotification = showCartNotification;
    
    // Wishlist dropdown functionality
    function toggleWishlistDropdown() {
        const dropdown = document.getElementById('wishlist-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                loadWishlistDropdown();
            }
        }
    }
    
    function openWishlistPage() {
        // Check if we're in a subfolder and adjust path accordingly
        const currentPath = window.location.pathname;
        const isInSubfolder = currentPath.includes('/kidsfolder/') || currentPath.includes('/beautyfolder/') || 
                             currentPath.includes('/womenF/') || currentPath.includes('/menfolder/') || 
                             currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                             currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                             currentPath.includes('/bagsfolder/');
        
        if (isInSubfolder) {
            window.location.href = '../wishlist.php';
        } else {
            window.location.href = 'wishlist.php';
        }
    }
    
    function loadWishlistDropdown() {
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        const content = document.getElementById('wishlist-dropdown-content');
        const empty = document.getElementById('wishlist-dropdown-empty');
        
        if (wishlist.length === 0) {
            content.style.display = 'none';
            empty.style.display = 'block';
        } else {
            content.style.display = 'block';
            empty.style.display = 'none';
            
            // Show only first 3 items in dropdown
            const displayItems = wishlist.slice(0, 3);
            content.innerHTML = displayItems.map(item => `
                <div class="wishlist-dropdown-item">
                    <div class="wishlist-dropdown-item-image-container">
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                        <button class="heart-button-dropdown" data-product-id="${item.id}" onclick="toggleWishlistFromDropdown('${item.id}')">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="wishlist-dropdown-item-info">
                        <div class="wishlist-dropdown-item-name">${item.name}</div>
                        <div class="wishlist-dropdown-item-price">$${item.price}</div>
                        <div class="wishlist-dropdown-item-category">${item.category}</div>
                    </div>
                    <div class="wishlist-dropdown-item-actions">
                        <button class="btn-add-cart" onclick="addToCartFromDropdown('${item.id}')">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <button class="btn-remove" onclick="removeFromWishlistDropdown('${item.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            
            // Show "View All" button if there are more than 3 items
            if (wishlist.length > 3) {
                content.innerHTML += `
                    <div class="wishlist-dropdown-item" style="justify-content: center; border-top: 2px solid #eee;">
                        <button onclick="openWishlistPage()" style="background: #f8f9fa; border: 1px solid #ddd; padding: 8px 20px; border-radius: 6px; cursor: pointer; color: #666;">
                            View All ${wishlist.length} Items
                        </button>
                    </div>
                `;
            }
        }
    }
    
    function addToCartFromDropdown(productId) {
        console.log('addToCartFromDropdown called with productId:', productId);
        
        // Use cart notification manager if available
        if (window.cartNotificationManager) {
            console.log('Using cartNotificationManager to add to cart from dropdown');
            const success = window.cartNotificationManager.addToCart(productId);
            
            if (success) {
                // Remove from wishlist after successfully adding to cart
                if (window.wishlistManager) {
                    window.wishlistManager.removeFromWishlist(productId);
                    showNotification('Product added to cart and removed from wishlist!', 'success');
                    // Reload wishlist dropdown to update display
                    loadWishlistDropdown();
                    updateWishlistCount();
                } else {
                    showNotification('Product added to cart!', 'success');
                }
            } else {
                showNotification('Failed to add product to cart', 'error');
            }
        } else {
            console.log('cartNotificationManager not available, using fallback');
            // Fallback - just show notification
            showNotification('Product added to cart!', 'success');
        }
    }
    
    function removeFromWishlistDropdown(productId) {
        if (confirm('Remove from wishlist?')) {
            let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            wishlist = wishlist.filter(item => item.id !== productId);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            showNotification('Removed from wishlist', 'info');
            loadWishlistDropdown();
            updateWishlistCount();
        }
    }
    
    function toggleWishlistFromDropdown(productId) {
        if (window.wishlistManager) {
            window.wishlistManager.toggleWishlist(productId);
            loadWishlistDropdown();
            updateWishlistCount();
        }
    }
    
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `wishlist-notification ${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
            zIndex: '1000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            fontSize: '14px',
            fontWeight: '500'
        });
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Hide notification
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Update wishlist count from localStorage
    function updateWishlistCount() {
        const wishlistCountElement = document.querySelector('.wishlist-count');
        if (wishlistCountElement) {
            try {
                const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                if (wishlist.length > 0) {
                    wishlistCountElement.textContent = wishlist.length;
                    wishlistCountElement.style.display = 'flex';
                } else {
                    wishlistCountElement.style.display = 'none';
                }
            } catch (error) {
                wishlistCountElement.style.display = 'none';
            }
        }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('wishlist-dropdown');
        const heartIcon = document.querySelector('.heart-icon');
        
        if (dropdown && heartIcon && !heartIcon.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Load wishlist count on page load
    updateWishlistCount();
    
    // Make functions available globally
    window.toggleWishlistDropdown = toggleWishlistDropdown;
    window.openWishlistPage = openWishlistPage;
    window.updateWishlistCount = updateWishlistCount;
});
</script>

<!-- Region Selection Modal -->
<div class="modal" id="region-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Choose Region</h3>
            <button class="close-btn" id="close-region-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="region-select">Choose a region (Somalia regions):</label>
                <select id="region-select" class="form-input">
                    <option value="">Select a region</option>
                    <?php foreach ($regionOptions as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="currency-select">Choose currency:</label>
                <select id="currency-select" class="form-input">
                    <option value="">Select currency</option>
                    <option value="usd">US Dollar ($)</option>
                    <option value="sos">Somali Shilling (SOS)</option>
                </select>
            </div>
            <button class="save-btn">Save Settings</button>
        </div>
    </div>
</div>



<!-- Validation Modal -->
<div class="validation-modal" id="validation-modal">
    <div class="validation-content">
        <div class="validation-header">
            <i class="validation-icon" id="validation-icon"></i>
            <h3 class="validation-title" id="validation-title">Validation Error</h3>
        </div>
        <div class="validation-body">
            <p class="validation-message" id="validation-message">Please check your input and try again.</p>
        </div>
        <div class="validation-footer">
            <button class="validation-btn" id="validation-btn">OK</button>
        </div>
    </div>
</div>

<!-- User Authentication Modal -->
<div class="modal" id="user-modal">
    <div class="modal-content user-auth-modal">
        <!-- Login Form -->
        <div class="auth-form" id="login-form">
            <div class="modal-header">
                <button class="close-btn" id="close-login-modal">
                    <i class="fas fa-times"></i>
                </button>
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Sign in to your Glamour Palace account</p>
            </div>
            <div class="modal-body">
                <form class="login-form">
                    <div class="form-group">
                        <input type="text" id="login-username" class="form-input" placeholder="Username or Email *" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <div class="password-container">
                            <input type="password" id="login-password" class="form-input" placeholder="Password *" autocomplete="off" required>
                            <span class="show-password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
                <div class="auth-switch">
                    <p>Don't have an account? <a href="#" id="switch-to-register">Sign Up</a></p>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="auth-form" id="register-form" style="display: none;">
            <div class="modal-header">
                <button class="close-btn" id="close-register-modal">
                    <i class="fas fa-times"></i>
                </button>
                <h2 class="modal-title">Sign Up</h2>
                <p class="modal-subtitle">Join Glamour Palace and start shopping with style</p>
            </div>
            <div class="modal-body">
                <form class="user-registration-form">
                    <div class="form-group">
                        <input type="text" id="username" class="form-input" placeholder="Username *" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" class="form-input" placeholder="Email Address *" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <div class="contact-input-container">
                            <div class="flag-prefix">
                                <img src="<?php echo getAssetPath('img/flag.jpg'); ?>" alt="Somali Flag" class="flag-icon">
                                <span class="country-code">+252</span>
                            </div>
                            <input type="tel" id="contact-number" class="form-input contact-input" placeholder="XXX XXX XXX" maxlength="9" pattern="[0-9]{9}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="gender" value="male" required>
                                Male
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="gender" value="female" required>
                                Female
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <select id="region" class="form-input" required>
                            <option value="">Select Region *</option>
                            <?php foreach ($regionOptions as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select id="city" class="form-input" required disabled>
                            <option value="">Select Region First</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="password-container">
                            <input type="password" id="password" class="form-input" placeholder="Password *" required>
                            <span class="show-password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="password-container">
                            <input type="password" id="confirm-password" class="form-input" placeholder="Confirm Password *" required>
                            <span class="show-password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-user-plus"></i>
                       Sign Up
                    </button>
                </form>
                <div class="auth-switch">
                    <p>Already have an account? <a href="#" id="switch-to-login">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Cart Functionality Script -->
<script>
        // Cart count loading is now handled by cart notification manager

    // Legacy addToCart function - now handled by cart notification manager
    function addToCart(productId) {
        // Delegate to the cart notification manager if available
        if (window.cartNotificationManager) {
            return window.cartNotificationManager.addToCart(productId);
        } else {
            console.warn('Cart notification manager not available, using fallback');
            // Fallback to simple notification
            showCartNotification('Adding to cart...', 'info');
        }
    }

            // User Dropdown Functionality
        let isTransitioningToLogin = false; // Flag to prevent login form from being hidden
        
        document.addEventListener('DOMContentLoaded', function() {
        const signinBtn = document.getElementById('signin-btn');
        const signupBtn = document.getElementById('signup-btn');
        const userModal = document.getElementById('user-modal');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const userIcon = document.getElementById('user-icon');

        // Ensure modal is hidden by default
        if (userModal) {
            userModal.style.display = 'none';
        }

        // User icon click functionality - ONLY shows/hides dropdown
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle dropdown visibility ONLY
                const userDropdown = document.getElementById('user-dropdown');
                
                if (userDropdown) {
                    const isVisible = userDropdown.classList.contains('show');
                    
                    if (isVisible) {
                        // Hide dropdown
                        userDropdown.classList.remove('show');
                        setTimeout(() => {
                            userDropdown.style.opacity = '0';
                            userDropdown.style.visibility = 'hidden';
                            userDropdown.style.transform = 'translateY(-10px)';
                        }, 300);
                    } else {
                        // Show dropdown
                        userDropdown.style.opacity = '1';
                        userDropdown.style.visibility = 'visible';
                        userDropdown.style.transform = 'translateY(0)';
                        userDropdown.classList.add('show');
                    }
                }
            });
        }

        // Sign In button functionality - shows login modal
        if (signinBtn) {
            signinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Hide dropdown first
                const userDropdown = document.getElementById('user-dropdown');
                if (userDropdown) {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.visibility = 'hidden';
                    userDropdown.style.transform = 'translateY(-10px)';
                    userDropdown.classList.remove('show');
                }
                
                // Show login modal immediately
                if (userModal) {
                    userModal.style.display = 'flex';
                    userModal.classList.add('show');
                    if (loginForm) {
                        loginForm.style.display = 'flex';
                        loginForm.classList.add('show');
                        
                        // Clear login form fields
                        const loginUsername = document.getElementById('login-username');
                        const loginPassword = document.getElementById('login-password');
                        if (loginUsername) loginUsername.value = '';
                        if (loginPassword) loginPassword.value = '';
                    }
                    if (registerForm) {
                        registerForm.style.display = 'none';
                        registerForm.classList.remove('show');
                    }
                }
            });
        }

        // Sign Up button functionality - shows register modal
        if (signupBtn) {
            signupBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Hide dropdown first
                const userDropdown = document.getElementById('user-dropdown');
                if (userDropdown) {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.visibility = 'hidden';
                    userDropdown.style.transform = 'translateY(-10px)';
                    userDropdown.classList.remove('show');
                }
                
                // Show register modal immediately
                if (userModal) {
                    userModal.style.display = 'flex';
                    userModal.classList.add('show');
                    if (loginForm) {
                        loginForm.style.display = 'none';
                        loginForm.classList.remove('show');
                    }
                    if (registerForm) {
                        registerForm.style.display = 'flex';
                        registerForm.classList.add('show');
                        
                        // Clear registration form fields
                        const username = document.getElementById('username');
                        const email = document.getElementById('email');
                        const contactNumber = document.getElementById('contact-number');
                        const password = document.getElementById('password');
                        const confirmPassword = document.getElementById('confirm-password');
                        
                        if (username) username.value = '';
                        if (email) email.value = '';
                        if (contactNumber) contactNumber.value = '';
                        if (password) password.value = '';
                        if (confirmPassword) confirmPassword.value = '';
                        
                        // Clear radio buttons
                        const genderRadios = document.querySelectorAll('input[name="gender"]');
                        genderRadios.forEach(radio => radio.checked = false);
                        
                        // Reset select fields
                        const region = document.getElementById('region');
                        const city = document.getElementById('city');
                        if (region) region.value = '';
                        if (city) city.value = '';
                    }
                }
            });
        }

        // Close modal functionality
        const closeButtons = document.querySelectorAll('.close-btn');
        closeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (userModal && !isTransitioningToLogin) {
                    userModal.classList.remove('show');
                    userModal.style.display = 'none';
                }
            });
        });

        // Close dropdown and modal when clicking outside
        document.addEventListener('click', function(e) {
            const userDropdown = document.getElementById('user-dropdown');
            const userIcon = document.getElementById('user-icon');
            
            // Close dropdown when clicking outside
            if (userDropdown && userIcon) {
                if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
                    userDropdown.classList.remove('show');
                    setTimeout(() => {
                        userDropdown.style.opacity = '0';
                        userDropdown.style.visibility = 'hidden';
                        userDropdown.style.transform = 'translateY(-10px)';
                    }, 300);
                }
            }
            
            // Close modal when clicking outside
            if (userModal && e.target === userModal && !isTransitioningToLogin) {
                userModal.classList.remove('show');
                userModal.style.display = 'none';
            }
        });

        // Switch between login and register forms
        const switchToRegister = document.getElementById('switch-to-register');
        const switchToLogin = document.getElementById('switch-to-login');

        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                if (loginForm) {
                    loginForm.style.display = 'none';
                    loginForm.classList.remove('show');
                }
                if (registerForm) {
                    registerForm.style.display = 'flex';
                    registerForm.classList.add('show');
                    
                    // Clear registration form fields
                    const username = document.getElementById('username');
                    const email = document.getElementById('email');
                    const contactNumber = document.getElementById('contact-number');
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm-password');
                    
                    if (username) username.value = '';
                    if (email) email.value = '';
                    if (contactNumber) contactNumber.value = '';
                    if (password) password.value = '';
                    if (confirmPassword) confirmPassword.value = '';
                    
                    // Clear radio buttons
                    const genderRadios = document.querySelectorAll('input[name="gender"]');
                    genderRadios.forEach(radio => radio.checked = false);
                    
                    // Reset select fields
                    const region = document.getElementById('region');
                    const city = document.getElementById('city');
                    if (region) region.value = '';
                    if (city) city.value = '';
                }
            });
        }

        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                if (registerForm) {
                    registerForm.style.display = 'none';
                    registerForm.classList.remove('show');
                }
                if (loginForm) {
                    loginForm.style.display = 'flex';
                    loginForm.classList.add('show');
                    
                    // Clear login form fields
                    const loginUsername = document.getElementById('login-username');
                    const loginPassword = document.getElementById('login-password');
                    if (loginUsername) loginUsername.value = '';
                    if (loginPassword) loginPassword.value = '';
                }
            });
        }

        // Add keyboard support for closing modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userModal && userModal.style.display === 'flex') {
                userModal.classList.remove('show');
                userModal.style.display = 'none';
            }
        });

        // Close dropdown when scrolling
        let scrollTimeout;
        document.addEventListener('scroll', function() {
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
                setTimeout(() => {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.visibility = 'hidden';
                    userDropdown.style.transform = 'translateY(-10px)';
                }, 300);
            }
        }, { passive: true });

        // Validation modal close functionality
        const validationBtn = document.getElementById('validation-btn');
        if (validationBtn) {
            validationBtn.addEventListener('click', function() {
                const validationModal = document.getElementById('validation-modal');
                if (validationModal) {
                    validationModal.classList.remove('show');
                    setTimeout(() => {
                        validationModal.style.display = 'none';
                    }, 300);
                }
            });
        }



        // Menu item click handlers (placeholder functionality)
        const menuItems = ['dashboard-link', 'my-info-link', 'notifications-link', 'notify-me-link', 'gift-cards-link'];
        menuItems.forEach(itemId => {
            const element = document.getElementById(itemId);
            if (element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Feature coming soon
                });
            }
        });

        // Prevent form submission for now (placeholder)
        const loginFormElement = document.querySelector('.login-form');
        const registerFormElement = document.querySelector('.user-registration-form');

        if (loginFormElement) {
            loginFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = {
                    username: document.getElementById('login-username').value.trim(),
                    password: document.getElementById('login-password').value
                };
                
                // Basic validation
                if (!formData.username || !formData.password) {
                    return;
                }
                

                
                // Disable submit button during request
                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                
                // Send login request
                fetch('login-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showValidationMessage('success', 'Login Successful', data.message);
                        
                        // Clear the login form
                        loginFormElement.reset();
                        
                        // Close the modal after a short delay
                        setTimeout(() => {
                            if (userModal) {
                                userModal.classList.remove('show');
                                userModal.style.display = 'none';
                            }
                            
                            // Refresh page to show logged-in state
                            window.location.reload();
                        }, 1500);
                    } else {
                        showValidationMessage('error', 'Login Failed', data.message);
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    showValidationMessage('error', 'Login Error', 'An error occurred during login. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }

        if (registerFormElement) {
            registerFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = {
                    username: document.getElementById('username').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    contact_number: '+252' + document.getElementById('contact-number').value,
                    gender: document.querySelector('input[name="gender"]:checked')?.value,
                    region: document.getElementById('region').value,
                    city: document.getElementById('city').value,
                    password: document.getElementById('password').value,
                    confirm_password: document.getElementById('confirm-password').value
                };
                
                // Basic validation
                if (!formData.username || !formData.email || !formData.contact_number || 
                    !formData.gender || !formData.region || !formData.city || 
                    !formData.password || !formData.confirm_password) {
                    return;
                }
                
                if (formData.password !== formData.confirm_password) {
                    return;
                }
                

                
                // Disable submit button during request
                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                
                // Send registration request
                fetch('register-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showValidationMessage('success', 'Registration Successful', data.message);
                        
                        // Set flag to prevent login form from being hidden
                        isTransitioningToLogin = true;
                        
                        // Clear the registration form
                        registerFormElement.reset();
                        
                        // Hide registration form and show login form
                        if (registerForm) {
                            registerForm.style.display = 'none';
                            registerForm.classList.remove('show');
                        }
                        if (loginForm) {
                            loginForm.style.display = 'flex';
                            loginForm.classList.add('show');
                        }
                        
                        // Keep the modal open for login
                        if (userModal) {
                            userModal.style.display = 'flex';
                            userModal.classList.add('show');
                        }
                        
                        // Double-check login form is visible after a short delay
                        setTimeout(() => {
                            if (loginForm && loginForm.style.display !== 'flex') {
                                loginForm.style.display = 'flex';
                                loginForm.classList.add('show');
                            }
                        }, 100);
                        
                        // Reset flag after a delay
                        setTimeout(() => {
                            isTransitioningToLogin = false;
                        }, 2000);
                    } else {
                        showValidationMessage('error', 'Registration Failed', data.message);
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    showValidationMessage('error', 'Registration Error', 'An error occurred during registration. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }



        // Password visibility toggle functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.show-password')) {
                const passwordContainer = e.target.closest('.password-container');
                const passwordInput = passwordContainer.querySelector('input');
                const toggleBtn = passwordContainer.querySelector('.show-password i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleBtn.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    toggleBtn.className = 'fas fa-eye';
                }
            }
        });

        // Region-City functionality for registration form
        const regionSelect = document.getElementById('region');
        const citySelect = document.getElementById('city');

        if (regionSelect && citySelect) {
            // Cities for each region
            const citiesByRegion = {
                'banadir': ['Mogadishu', 'Afgooye', 'Marka', 'Wanlaweyn'],
                'bari': ['Bosaso', 'Qardho', 'Caluula', 'Iskushuban', 'Bandarbeyla'],
                'bay': ['Baidoa', 'Burdhubo', 'Dinsor', 'Qansaxdheere'],
                'galguduud': ['Dhusamareb', 'Adado', 'Abudwaq', 'Galgadud'],
                'gedo': ['Garbahaarrey', 'Bardhere', 'Luuq', 'El Wak', 'Dolow'],
                'hiran': ['Beledweyne', 'Buloburde', 'Jalalaqsi', 'Mahas'],
                'jubbada-dhexe': ['Bu\'aale', 'Jilib', 'Sakow', 'Dujuma'],
                'jubbada-hoose': ['Kismayo', 'Jamame', 'Badhaadhe', 'Afmadow'],
                'mudug': ['Galkayo', 'Hobyo', 'Harardhere', 'Jariiban'],
                'nugaal': ['Garowe', 'Eyl', 'Burtinle', 'Dangorayo'],
                'sanaag': ['Erigavo', 'Badhan', 'Laasqoray', 'Dhahar'],
                'shabeellaha-dhexe': ['Jowhar', 'Balcad', 'Adale', 'Warsheikh'],
                'shabeellaha-hoose': ['Merca', 'Baraawe', 'Kurtunwaarey', 'Qoryooley'],
                'sool': ['Laascaanood', 'Taleex', 'Xudun', 'Caynabo'],
                'togdheer': ['Burao', 'Oodweyne', 'Sheikh', 'Buhoodle'],
                'woqooyi-galbeed': ['Hargeisa', 'Berbera', 'Borama', 'Gabiley', 'Baki']
            };

            regionSelect.addEventListener('change', function() {
                const selectedRegion = this.value;
                citySelect.innerHTML = '<option value="">Select city</option>';
                citySelect.disabled = true;

                if (selectedRegion && citiesByRegion[selectedRegion]) {
                    citiesByRegion[selectedRegion].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.toLowerCase().replace(/\s+/g, '-');
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                }
            });
        }

        // Show validation message function
        function showValidationMessage(type, title, message) {
            const validationModal = document.getElementById('validation-modal');
            const validationIcon = document.getElementById('validation-icon');
            const validationTitle = document.getElementById('validation-title');
            const validationMessage = document.getElementById('validation-message');
            const validationBtn = document.getElementById('validation-btn');
            
            if (validationModal && validationIcon && validationTitle && validationMessage && validationBtn) {
                // Set icon and title based on type
                if (type === 'success') {
                    validationIcon.className = 'fas fa-check-circle';
                    validationIcon.style.color = '#28a745';
                    validationTitle.textContent = title;
                    validationMessage.textContent = message;
                } else {
                    validationIcon.className = 'fas fa-exclamation-circle';
                    validationIcon.style.color = '#dc3545';
                    validationTitle.textContent = title;
                    validationMessage.textContent = message;
                }
                
                // Show modal
                validationModal.style.display = 'flex';
                validationModal.classList.add('show');
                
                // Auto-hide after 3 seconds
                setTimeout(() => {
                    validationModal.classList.remove('show');
                    setTimeout(() => {
                        validationModal.style.display = 'none';
                    }, 300);
                }, 3000);
            }
        }

        // Logout function
        window.logout = function() {
            fetch('logout-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showValidationMessage('success', 'Logout Successful', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showValidationMessage('error', 'Logout Failed', data.message);
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                showValidationMessage('error', 'Logout Error', 'An error occurred during logout. Please try again.');
            });
        }



    });
</script>

<!-- Global Search Functionality -->
<script>
// Global Search Functionality
class GlobalSearch {
    constructor() {
        this.searchInput = document.querySelector('.search-input');
        this.searchResults = null;
        this.currentCategory = this.getCurrentCategory();
        this.init();
    }

    getCurrentCategory() {
        const path = window.location.pathname;
        if (path.includes('/perfumes/')) return 'perfumes';
        if (path.includes('/accessories/')) return 'accessories';
        if (path.includes('/bagsfolder/')) return 'bags';
        if (path.includes('/homedecor/')) return 'home-decor';
        if (path.includes('/shoess/')) return 'shoes';
        if (path.includes('/menfolder/')) return 'men';
        if (path.includes('/womenF/')) return 'women';
        if (path.includes('/kidsfolder/')) return 'children';
        return 'all';
    }

    init() {
        if (this.searchInput) {
            this.createSearchResultsContainer();
            this.bindEvents();
        }
    }

    createSearchResultsContainer() {
        // Create search results container
        this.searchResults = document.createElement('div');
        this.searchResults.className = 'search-results-container';
        this.searchResults.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;
        
        // Insert after search container
        const searchContainer = this.searchInput.closest('.search-container');
        if (searchContainer) {
            searchContainer.style.position = 'relative';
            searchContainer.appendChild(this.searchResults);
        }
    }

    bindEvents() {
        // Search input events
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.trim()) {
                this.showResults();
            }
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideResults();
            }
        });

        // Handle search icon click
        const searchIcon = document.querySelector('.search-icon');
        if (searchIcon) {
            searchIcon.addEventListener('click', () => {
                this.performSearch();
            });
        }

        // Handle Enter key
        this.searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.performSearch();
            }
        });
    }

    handleSearch(query) {
        if (query.trim().length < 2) {
            this.hideResults();
            return;
        }

        const results = this.searchProducts(query);
        this.displayResults(results, query);
    }

    searchProducts(query) {
        const searchTerm = query.toLowerCase().trim();
        const results = [];

        // Search in current page products
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const productPrice = card.querySelector('.product-price')?.textContent.toLowerCase() || '';
            const productId = card.getAttribute('data-product-id');
            const productGender = card.getAttribute('data-gender');
            const productCategory = card.getAttribute('data-category');

            if (productName.includes(searchTerm) || 
                productPrice.includes(searchTerm) ||
                productGender?.includes(searchTerm) ||
                productCategory?.includes(searchTerm)) {
                
                results.push({
                    id: productId,
                    name: card.querySelector('.product-name')?.textContent || '',
                    price: card.querySelector('.product-price')?.textContent || '',
                    image: card.querySelector('.product-image img')?.src || '',
                    gender: productGender,
                    category: productCategory,
                    type: 'current-page'
                });
            }
        });

        // Add suggestions for other categories
        const suggestions = this.getSearchSuggestions(searchTerm);
        results.push(...suggestions);

        return results;
    }

    getSearchSuggestions(query) {
        const suggestions = [];
        const searchTerm = query.toLowerCase();

        // Define product categories and their common terms
        const categories = {
            'perfumes': ['perfume', 'cologne', 'fragrance', 'scent', 'aroma', 'eau de toilette', 'parfum'],
            'accessories': ['belt', 'watch', 'sunglasses', 'jewelry', 'necklace', 'bracelet', 'ring', 'earrings', 'socks', 'hat', 'cap', 'tie', 'cufflinks'],
            'bags': ['bag', 'purse', 'handbag', 'tote', 'backpack', 'clutch', 'wallet', 'shoulder bag', 'crossbody'],
            'shoes': ['shoes', 'boots', 'sneakers', 'heels', 'flats', 'sandals', 'loafers', 'pumps'],
            'home-decor': ['decor', 'decoration', 'home', 'furniture', 'lamp', 'vase', 'cushion', 'curtain', 'rug'],
            'clothing': ['dress', 'shirt', 'pants', 'jeans', 'skirt', 'blouse', 'jacket', 'coat', 'sweater']
        };

        // Check if search term matches any category
        Object.entries(categories).forEach(([category, terms]) => {
            if (terms.some(term => term.includes(searchTerm) || searchTerm.includes(term))) {
                suggestions.push({
                    id: `suggestion-${category}`,
                    name: `Search ${category}`,
                    price: '',
                    image: '',
                    gender: '',
                    category: category,
                    type: 'suggestion',
                    url: this.getCategoryUrl(category)
                });
            }
        });

        return suggestions;
    }

    getCategoryUrl(category) {
        const baseUrl = window.location.origin;
        const categoryUrls = {
            'perfumes': '/Glamour-system/perfumes/',
            'accessories': '/Glamour-system/accessories/',
            'bags': '/Glamour-system/bagsfolder/',
            'shoes': '/Glamour-system/shoess/',
            'home-decor': '/Glamour-system/homedecor/',
            'men': '/Glamour-system/menfolder/',
            'women': '/Glamour-system/womenF/',
            'children': '/Glamour-system/kidsfolder/'
        };
        return baseUrl + (categoryUrls[category] || '/');
    }

    displayResults(results, query) {
        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="search-no-results">
                    <p>No results found for "${query}"</p>
                    <p>Try searching for different keywords</p>
                </div>
            `;
        } else {
            let html = '';
            
            // Group results by type
            const currentPageResults = results.filter(r => r.type === 'current-page');
            const suggestions = results.filter(r => r.type === 'suggestion');

            // Current page results
            if (currentPageResults.length > 0) {
                html += '<div class="search-section"><h4>Products on this page</h4>';
                currentPageResults.forEach(result => {
                    html += this.createResultItem(result);
                });
                html += '</div>';
            }

            // Suggestions
            if (suggestions.length > 0) {
                html += '<div class="search-section"><h4>Search other categories</h4>';
                suggestions.forEach(result => {
                    html += this.createResultItem(result);
                });
                html += '</div>';
            }

            this.searchResults.innerHTML = html;
        }

        this.showResults();
        this.bindResultEvents();
    }

    createResultItem(result) {
        if (result.type === 'suggestion') {
            return `
                <div class="search-result-item suggestion" data-url="${result.url}">
                    <div class="result-content">
                        <div class="result-info">
                            <h5>${result.name}</h5>
                            <p>Browse ${result.category} products</p>
                        </div>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            `;
        }

        return `
            <div class="search-result-item product" data-product-id="${result.id}">
                <div class="result-image">
                    <img src="${result.image}" alt="${result.name}" onerror="this.style.display='none'">
                </div>
                <div class="result-content">
                    <div class="result-info">
                        <h5>${result.name}</h5>
                        <p class="result-price">${result.price}</p>
                        <p class="result-category">${result.category} • ${result.gender}</p>
                    </div>
                    <button class="quick-view-btn" data-product-id="${result.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        `;
    }

    bindResultEvents() {
        // Handle suggestion clicks
        this.searchResults.querySelectorAll('.search-result-item.suggestion').forEach(item => {
            item.addEventListener('click', () => {
                const url = item.getAttribute('data-url');
                window.location.href = url;
            });
        });

        // Handle product quick view
        this.searchResults.querySelectorAll('.quick-view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const productId = btn.getAttribute('data-product-id');
                this.openQuickView(productId);
            });
        });

        // Handle product item clicks
        this.searchResults.querySelectorAll('.search-result-item.product').forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.getAttribute('data-product-id');
                this.openQuickView(productId);
            });
        });
    }

    openQuickView(productId) {
        // Trigger the existing quick view functionality
        const quickViewBtn = document.querySelector(`[data-product-id="${productId}"] .quick-view`);
        if (quickViewBtn) {
            quickViewBtn.click();
        }
        this.hideResults();
    }

    performSearch() {
        const query = this.searchInput.value.trim();
        if (query) {
            // If we're on a specific category page, search within that category
            if (this.currentCategory !== 'all') {
                this.searchInCategory(query);
            } else {
                // Global search - redirect to search results page
                this.redirectToSearch(query);
            }
        }
    }

    searchInCategory(query) {
        // Filter products on current page
        const productCards = document.querySelectorAll('.product-card');
        let hasResults = false;

        productCards.forEach(card => {
            const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const productPrice = card.querySelector('.product-price')?.textContent.toLowerCase() || '';
            const productGender = card.getAttribute('data-gender')?.toLowerCase() || '';
            const productCategory = card.getAttribute('data-category')?.toLowerCase() || '';

            const searchTerm = query.toLowerCase();
            const isMatch = productName.includes(searchTerm) || 
                           productPrice.includes(searchTerm) ||
                           productGender.includes(searchTerm) ||
                           productCategory.includes(searchTerm);

            if (isMatch) {
                card.style.display = 'block';
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        this.showNoResultsMessage(!hasResults, query);
    }

    showNoResultsMessage(show, query) {
        let noResultsMsg = document.querySelector('.no-results-message');
        
        if (show) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.style.cssText = `
                    text-align: center;
                    padding: 40px 20px;
                    color: #666;
                    font-size: 16px;
                `;
                
                const productGrid = document.querySelector('.product-grid');
                if (productGrid) {
                    productGrid.appendChild(noResultsMsg);
                }
            }
            noResultsMsg.innerHTML = `
                <h3>No results found for "${query}"</h3>
                <p>Try adjusting your search terms or browse our categories</p>
                <button onclick="window.location.reload()" style="
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                ">Show All Products</button>
            `;
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    redirectToSearch(query) {
        // For global search, you could redirect to a search results page
        // For now, we'll just show an alert
        alert(`Global search for "${query}" - This would redirect to a search results page`);
    }

    showResults() {
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        this.searchResults.style.display = 'none';
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new GlobalSearch();
});

// Add CSS styles for search results
const searchStyles = `
<style>
.search-results-container {
    font-family: Arial, sans-serif;
}

.search-section {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.search-section:last-child {
    border-bottom: none;
}

.search-section h4 {
    margin: 0 15px 10px;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item.suggestion {
    justify-content: space-between;
}

.search-result-item.product {
    gap: 12px;
}

.result-image {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.result-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.result-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.result-info h5 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.result-price {
    margin: 0 0 2px 0;
    font-size: 13px;
    font-weight: 600;
    color: #007bff;
}

.result-category {
    margin: 0;
    font-size: 11px;
    color: #666;
    text-transform: capitalize;
}

.quick-view-btn {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    padding: 5px;
    border-radius: 3px;
    transition: background-color 0.2s;
}

.quick-view-btn:hover {
    background-color: #e3f2fd;
}

.search-no-results {
    padding: 20px;
    text-align: center;
    color: #666;
}

.search-no-results p {
    margin: 5px 0;
}

.search-results-container::-webkit-scrollbar {
    width: 6px;
}

.search-results-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.search-results-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.search-results-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
`;

// Inject styles into head
document.head.insertAdjacentHTML('beforeend', searchStyles);

// Password toggle functionality
function togglePasswordVisibility(element) {
    const passwordInput = element.closest('.password-container').querySelector('input');
    const eyeIcon = element.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Add click event listeners to all password toggle buttons
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.show-password');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            togglePasswordVisibility(this);
        });
    });
});

// Mobile Navigation Functionality
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const mobileNavOverlay = document.getElementById('mobile-nav-overlay');
    const mobileNavClose = document.getElementById('mobile-nav-close');
    const body = document.body;

    // Function to open mobile navigation
    function openMobileNav() {
        mobileNavOverlay.classList.add('active');
        body.classList.add('mobile-nav-open');
        hamburgerMenu.classList.add('active'); // This will hide the hamburger menu
        
        // Hide user actions
        const navRightContainer = document.querySelector('.nav-right-container');
        if (navRightContainer) navRightContainer.classList.add('active');
    }

    // Function to close mobile navigation
    function closeMobileNav() {
        mobileNavOverlay.classList.remove('active');
        body.classList.remove('mobile-nav-open');
        hamburgerMenu.classList.remove('active'); // This will show the hamburger menu again
        
        // Show user actions
        const navRightContainer = document.querySelector('.nav-right-container');
        if (navRightContainer) navRightContainer.classList.remove('active');
    }

    // Open mobile navigation when hamburger is clicked
    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openMobileNav();
        });
    }

    // Close mobile navigation when close button is clicked
    if (mobileNavClose) {
        mobileNavClose.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileNav();
        });
    }

    // Close mobile navigation when clicking on overlay (not content)
    if (mobileNavOverlay) {
        mobileNavOverlay.addEventListener('click', function(e) {
            if (e.target === mobileNavOverlay) {
                closeMobileNav();
            }
        });
    }

    // Close mobile navigation when clicking on navigation links
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeMobileNav();
        });
    });

    // Close mobile navigation on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileNavOverlay.classList.contains('active')) {
            closeMobileNav();
        }
    });

    // Close mobile navigation on window resize if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileNavOverlay.classList.contains('active')) {
            closeMobileNav();
        }
    });

    // Prevent body scroll when mobile nav is open
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (body.classList.contains('mobile-nav-open')) {
                    body.style.overflow = 'hidden';
                } else {
                    body.style.overflow = '';
                }
            }
        });
    });

    observer.observe(body, { attributes: true, attributeFilter: ['class'] });
});
</script> 