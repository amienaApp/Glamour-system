<?php
// Check if session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the correct path prefix based on current directory
$currentDir = dirname($_SERVER['PHP_SELF']);
$isInPagesDir = strpos($currentDir, '/pages') !== false;
$pathPrefix = $isInPagesDir ? '../' : '';
?>

<?php
// Check if MongoDB extension is available before loading autoloader
$mongodbAvailable = extension_loaded('mongodb');

if ($mongodbAvailable) {
    // Only include autoloader if MongoDB extension is available
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Try to load MongoDB dependencies
    $categories = [];
    $isLoggedIn = isset($_SESSION['user_id']);
    
    try {
        require_once __DIR__ . '/../config1/mongodb.php';
        require_once __DIR__ . '/../models/Category.php';
        
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
    } catch (Exception $e) {
        // If MongoDB is not available, use static categories
        $categories = [
            ['name' => 'Dresses', 'description' => 'Beautiful dresses for every occasion'],
            ['name' => 'Clothing', 'description' => 'Trendy clothing for all ages'],
            ['name' => 'Tops', 'description' => 'Stylish tops and blouses'],
            ['name' => 'Accessories', 'description' => 'Fashion accessories and jewelry'],
            ['name' => 'Shoes', 'description' => 'Comfortable and stylish footwear'],
            ['name' => 'Bags', 'description' => 'Elegant bags and purses'],
            ['name' => 'Perfumes', 'description' => 'Luxury fragrances'],
            ['name' => 'Beauty', 'description' => 'Beauty and cosmetics products']
        ];
    }
} else {
    // MongoDB extension not available, use static categories
    $categories = [
        ['name' => 'Dresses', 'description' => 'Beautiful dresses for every occasion'],
        ['name' => 'Clothing', 'description' => 'Trendy clothing for all ages'],
        ['name' => 'Tops', 'description' => 'Stylish tops and blouses'],
        ['name' => 'Accessories', 'description' => 'Fashion accessories and jewelry'],
        ['name' => 'Shoes', 'description' => 'Comfortable and stylish footwear'],
        ['name' => 'Bags', 'description' => 'Elegant bags and purses'],
        ['name' => 'Perfumes', 'description' => 'Luxury fragrances'],
        ['name' => 'Beauty', 'description' => 'Beauty and cosmetics products']
    ];
    $isLoggedIn = isset($_SESSION['user_id']);
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

<!-- Top Navigation Bar -->
<nav class="top-nav">
    <!-- Logo Container - Left Side -->
    <div class="logo-container">
        <div class="logo">
            <a href="#" class="logo-text">
                <span class="logo-main">Glamour</span>
                <span class="logo-accent">Palace</span>
            </a>
        </div>
    </div>

    <!-- Navigation Menu - Center -->
    <div class="nav-menu-container">
        <ul class="nav-menu">
            <li><a href="<?php echo $pathPrefix; ?>index.php" class="nav-link">Home</a></li>
            <li class="nav-item-modal">
                <a href="#" class="nav-link">Categories</a>
                <!-- Categories Dropdown -->
                <div class="category-dropdown">
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>womenF/women.php" class="dropdown-link">
                            <i class="fas fa-female"></i>
                            <span>Women Clothing</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>menfolder/men.php" class="dropdown-link">
                            <i class="fas fa-male"></i>
                            <span>Men Clothing</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>kidsfolder/kids.php" class="dropdown-link">
                            <i class="fas fa-child"></i>
                            <span>Kids Collection</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>shoess/shoes.php" class="dropdown-link">
                            <i class="fas fa-shoe-prints"></i>
                            <span>Shoes</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>perfumes/index.php" class="dropdown-link">
                            <i class="fas fa-spray-can"></i>
                            <span>Perfumes</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>accessories/accessories.php" class="dropdown-link">
                            <i class="fas fa-gem"></i>
                            <span>Accessories</span>
                        </a>
                    </div>
                                         <div class="dropdown-item">
                         <a href="<?php echo $pathPrefix; ?>homedecor/homedecor.php" class="dropdown-link">
                             <i class="fas fa-home"></i>
                             <span>Home Decor</span>
                         </a>
                     </div>
                     <div class="dropdown-item">
                         <a href="<?php echo $pathPrefix; ?>bagsfolder/bags.php" class="dropdown-link">
                             <i class="fas fa-shopping-bag"></i>
                             <span>Bags</span>
                         </a>
                     </div>
                    <div class="dropdown-item">
                        <a href="<?php echo $pathPrefix; ?>beautyfolder/beauty.php" class="dropdown-link">
                            <i class="fas fa-palette"></i>
                            <span>Beauty & Cosmetics</span>
                        </a>
                    </div>
                </div>
            </li>
            <li><a href="<?php echo $pathPrefix; ?>pages/contact.php" class="nav-link">Contact us</a></li>
            <li><a href="<?php echo $pathPrefix; ?>pages/about.php" class="nav-link">About us</a></li>
        </ul>
    </div>

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
                            <a href="<?php echo $pathPrefix; ?>orders.php" class="menu-item">
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
            <div class="shopping-cart" title="Cart" style="position: relative;">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </div>
        </div>

        <!-- Somalia Flag - Compact (Disabled) -->
        <div class="flag-container" title="Region Settings (Coming Soon)">
            <img src="<?php echo $pathPrefix; ?>img/flag.jpg" alt="Somalia Flag" class="flag" id="somalia-flag">
        </div>
    </div>
</nav>

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
                        <input type="text" id="login-username" class="form-input" placeholder="Username or Email *" required>
                    </div>
                    <div class="form-group">
                        <div class="password-container">
                            <input type="password" id="login-password" class="form-input" placeholder="Password *" required>
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
                        <input type="text" id="username" class="form-input" placeholder="Username *" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" class="form-input" placeholder="Email Address *" required>
                    </div>
                    <div class="form-group">
                        <div class="contact-input-container">
                            <div class="flag-prefix">
                                <img src="<?php echo $pathPrefix; ?>img/flag.jpg" alt="Somali Flag" class="flag-icon">
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


<!-- Include Cart Notification Manager -->
<script src="scripts/cart-notification-manager.js"></script>

<!-- Cart Functionality Script -->
<script>
    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Use the unified cart notification manager if available
        if (window.cartNotificationManager) {
            window.cartNotificationManager.loadCartCount();
        } else {
            loadCartCount();
        }
    });

    function loadCartCount() {
        fetch('<?php echo $pathPrefix; ?>cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_cart_count'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
    }

    // Function to update cart count (API-based like main header)
    function updateCartCount() {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            // Determine the correct path to cart API based on current URL
            const currentPath = window.location.pathname;
            const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                                   currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                                   currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                                   currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                                   currentPath.includes('/bagsfolder/');
            const cartApiPath = '<?php echo $pathPrefix; ?>cart-api.php';
            
            // Fetch current cart count from cart API
            fetch(cartApiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_count'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.cart_count > 0) {
                    cartCountElement.textContent = data.cart_count;
                    cartCountElement.style.display = 'flex';
            } else {
                    cartCountElement.textContent = '0';
                cartCountElement.style.display = 'none';
            }
            })
            .catch(error => {
                console.log('Cart count fetch error:', error);
                cartCountElement.style.display = 'none';
            });
        } else {
            console.log('Cart count element not found!');
        }
    }
    
    // Cart count helper functions
    function addToCartCount() {
        // This function is called when items are added to cart
        // It will trigger a refresh of the cart count from the API
        updateCartCount();
    }
    
    function removeFromCartCount() {
        // This function is called when items are removed from cart
        // It will trigger a refresh of the cart count from the API
        updateCartCount();
    }

    // Initialize cart count functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Update cart count from cart system
        updateCartCount();
        
        // Refresh cart count when page becomes visible (user returns from cart)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateCartCount();
            }
        });
        
        // Also refresh cart count when page loads
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from back-forward cache
                updateCartCount();
            }
        });
        
        // Refresh cart count every 10 seconds to keep it updated
        setInterval(updateCartCount, 10000);
    });

    // Initialize cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        const cartIcon = document.querySelector('.shopping-cart');
        if (cartIcon) {
            // Add click event listener for cart functionality
            cartIcon.addEventListener('click', function(e) {
                // Show loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.style.cursor = 'wait';
                
                // Redirect to cart-unified.php when cart icon is clicked (instant redirect)
                const currentPath = window.location.pathname;
                const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                                       currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                                       currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                                       currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                                       currentPath.includes('/bagsfolder/');
                window.location.href = '<?php echo $pathPrefix; ?>cart-unified.php';
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
        }
    });

    // Wishlist functionality
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
        
        window.location.href = '<?php echo $pathPrefix; ?>wishlist.php';
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
                    <img src="${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
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
        // Try to use existing cart functionality
        if (typeof addToCart === 'function') {
            addToCart(productId);
        } else {
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
    
    function showNotification(message, type = 'success') {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.wishlist-notification');
        existingNotifications.forEach(notification => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        });
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'wishlist-notification';
        
        // Set colors based on type
        let backgroundColor, icon;
        switch (type) {
            case 'success':
                backgroundColor = '#28a745';
                icon = '✓';
                break;
            case 'error':
                backgroundColor = '#dc3545';
                icon = '✗';
                break;
            case 'info':
            default:
                backgroundColor = '#17a2b8';
                icon = 'ℹ';
                break;
        }
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${backgroundColor};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-weight: 500;
            font-size: 14px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            word-wrap: break-word;
        `;
        
        notification.textContent = `${icon} ${message}`;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 2 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 2000);
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
    window.updateCartCount = updateCartCount;
    window.addToCartCount = addToCartCount;
    window.removeFromCartCount = removeFromCartCount;
    window.toggleWishlistDropdown = toggleWishlistDropdown;
    window.openWishlistPage = openWishlistPage;
    window.updateWishlistCount = updateWishlistCount;
    window.addToCartFromDropdown = addToCartFromDropdown;
    window.removeFromWishlistDropdown = removeFromWishlistDropdown;

    // Function to be called from other pages when adding to cart
    function addToCart(productId) {
        fetch('<?php echo $pathPrefix; ?>cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add_to_cart&product_id=${productId}&quantity=1&return_url=${encodeURIComponent(window.location.href)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                // Show success message
                alert('Product added to cart successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding product to cart');
        });
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
                    const isVisible = userDropdown.style.opacity === '1' || userDropdown.classList.contains('show');
                    
                    if (isVisible) {
                        // Hide dropdown
                        userDropdown.style.opacity = '0';
                        userDropdown.style.visibility = 'hidden';
                        userDropdown.style.transform = 'translateY(-10px)';
                        userDropdown.classList.remove('show');
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
                    userDropdown.style.opacity = '0';
                    userDropdown.style.visibility = 'hidden';
                    userDropdown.style.transform = 'translateY(-10px)';
                    userDropdown.classList.remove('show');
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
                fetch('<?php echo $pathPrefix; ?>login-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear the login form
                        loginFormElement.reset();
                        
                        // Close the modal immediately
                        if (userModal) {
                            userModal.classList.remove('show');
                            userModal.style.display = 'none';
                        }
                        
                        // Refresh page to show logged-in state
                        setTimeout(() => {
                            window.location.reload();
                        }, 300);
                    }
                })
                .catch(error => {
                    // Silent error handling
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
                fetch('<?php echo $pathPrefix; ?>register-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
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
                    }
                })
                .catch(error => {
                    // Silent error handling
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

        // Logout function
        window.logout = function() {
            fetch('<?php echo $pathPrefix; ?>logout-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            })
            .catch(error => {
                // Silent error handling
            });
        }

        // Initialize cart manager if it exists
        if (typeof cartManager !== 'undefined') {
            // Update cart count from localStorage
            const cartCount = cartManager.getCartCount();
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
                cartCountElement.style.display = cartCount > 0 ? 'block' : 'none';
            }
        }

    });
</script>

<!-- Cart Sidebar -->
<div class="cart-sidebar" id="cart-sidebar">
    <!-- Cart content will be populated by JavaScript -->
</div>

<!-- Cart Overlay -->
<div class="cart-overlay" id="cart-overlay" onclick="cartManager.closeCartSidebar()"></div> 

<style>
/* Wishlist Dropdown Styles */
.wishlist-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 350px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
    margin-top: 10px;
    max-height: 500px;
    overflow: hidden;
}

.wishlist-dropdown.show {
    display: block;
    animation: wishlistSlideDown 0.3s ease;
}

@keyframes wishlistSlideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.wishlist-dropdown-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
}

.wishlist-dropdown-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.wishlist-dropdown-header h3 i {
    color: #e74c3c;
}

.view-all-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.view-all-btn:hover {
    background: #0056b3;
}

.wishlist-dropdown-content {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px 0;
}

.wishlist-dropdown-empty {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.wishlist-dropdown-empty i {
    font-size: 2.5rem;
    color: #ddd;
    margin-bottom: 15px;
}

.wishlist-dropdown-empty p {
    margin: 0 0 5px 0;
    font-weight: 600;
}

.wishlist-dropdown-empty small {
    font-size: 0.85rem;
}

.wishlist-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

/* Heart icon hover effect */
.heart-icon:hover {
    background-color: rgba(231, 76, 60, 0.1);
    border-radius: 50%;
    transition: background-color 0.2s;
}

/* Cart count styles */
.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

/* Shopping cart hover effect */
.shopping-cart:hover {
    background-color: rgba(0, 123, 255, 0.1);
    border-radius: 50%;
    transition: background-color 0.2s;
}

/* Wishlist dropdown item styles */
.wishlist-dropdown-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
}

.wishlist-dropdown-item:hover {
    background: #f8f9fa;
}

.wishlist-dropdown-item:last-child {
    border-bottom: none;
}

.wishlist-dropdown-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
    background: #f8f9fa;
}

.wishlist-dropdown-item-info {
    flex: 1;
    min-width: 0;
}

.wishlist-dropdown-item-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wishlist-dropdown-item-price {
    font-size: 0.85rem;
    font-weight: 700;
    color: #e74c3c;
    margin: 0 0 5px 0;
}

.wishlist-dropdown-item-category {
    font-size: 0.8rem;
    color: #666;
    margin: 0;
    text-transform: capitalize;
}

.wishlist-dropdown-item-actions {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.wishlist-dropdown-item-actions button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background 0.2s ease;
    font-size: 0.8rem;
}

.wishlist-dropdown-item-actions .btn-add-cart {
    color: #007bff;
}

.wishlist-dropdown-item-actions .btn-add-cart:hover {
    background: #e3f2fd;
}

.wishlist-dropdown-item-actions .btn-remove {
    color: #dc3545;
}

.wishlist-dropdown-item-actions .btn-remove:hover {
    background: #f8d7da;
}

/* Show/hide dropdown */
.wishlist-dropdown.show {
    display: block !important;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .wishlist-dropdown {
        width: 300px;
        right: -50px;
    }
    
    .wishlist-dropdown-item {
        padding: 12px 15px;
    }
    
    .wishlist-dropdown-item img {
        width: 50px;
        height: 50px;
    }
}
</style>

<!-- Username Validation CSS -->
<link rel="stylesheet" href="<?php echo $pathPrefix; ?>styles/username-validation.css">

<!-- Username Validation JavaScript -->
<script src="<?php echo $pathPrefix; ?>scripts/username-validation.js"></script> 