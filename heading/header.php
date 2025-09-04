<?php
session_start();
// Ensure vendor autoload is included first
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
$isLoggedIn = isset($_SESSION['user_id']);

// Function to get the correct URL for each category
function getCategoryUrl($categoryName) {
    $categoryMap = [
        "Women's Clothing" => '../womenF/women.php',
        "Men's Clothing" => '../menfolder/men.php',
        "Kids' Clothing" => '#kids',
        "Accessories" => '../accessories/accessories.php',
        "Home & Living" => '../homedecor/homedecor.php',
        "Beauty & Cosmetics" => '../perfumes/index.php',
        "Sports & Fitness" => '#sports',
        "Perfumes" => '../perfumes/index.php',
        "Shoes" => '../shoess/shoes.php',
        "Bags" => '../bagsfolder/bags.php'
    ];
    
    return $categoryMap[$categoryName] ?? '#';
}

    // Function to get the correct URL for subcategories
    function getSubcategoryUrl($categoryName, $subcategoryName) {
        $subcategoryMap = [
            "Women's Clothing" => [
                "Dresses" => '../womenF/women.php#products-section',
                "Tops" => '../womenF/women.php#tops-section',
                "Bottoms" => '../womenF/women.php?subcategory=bottoms',
                "Outerwear" => '../womenF/women.php?subcategory=outerwear',
                "Activewear" => '../womenF/women.php?subcategory=activewear',
                "Lingerie" => '../womenF/women.php?subcategory=lingerie',
                "Swimwear" => '../womenF/women.php?subcategory=swimwear',
                "Wedding Guest" => '../womenF/women.php?subcategory=wedding-guest',
                "Wedding-dress" => '../womenF/women.php?subcategory=wedding-dress',
                "Abaya" => '../womenF/women.php?subcategory=abaya',
                "Summer-dresses" => '../womenF/women.php?subcategory=summer-dresses',
                "Homecoming" => '../womenF/women.php?subcategory=homecoming'
            ],
            "Men's Clothing" => [
                "Shirts" => '../menfolder/men.php#shirts-section',
                "T-Shirts" => '../menfolder/men.php#tshirts-section',
                "Suits" => '../menfolder/men.php#suits-section',
                "Pants" => '../menfolder/men.php#pants-section',
                "Shorts" => '../menfolder/men.php#shorts-section',
                "Hoodies & Sweatshirts" => '../menfolder/men.php#hoodies-section',
                "Jackets" => '../menfolder/men.php?subcategory=jackets',
                "Activewear" => '../menfolder/men.php?subcategory=activewear',
                "Underwear" => '../menfolder/men.php?subcategory=underwear',
                "Swimwear" => '../menfolder/men.php?subcategory=swimwear'
            ],
        "Accessories" => [
            "Shoes" => '../shoess/shoes.php',
            "Bags" => '../bagsfolder/bags.php',
            "Jewelry" => '#jewelry',
            "Hats" => '#hats',
            "Scarves" => '#scarves',
            "Belts" => '#belts'
        ],
        "Shoes" => [
            "Men's Shoes" => '../shoess/shoes.php?subcategory=menshoes',
            "Women's Shoes" => '../shoess/shoes.php?subcategory=womenshoes',
            "Children's Shoes" => '../shoess/shoes.php?subcategory=childrenshoes',
            "Sports Shoes" => '../shoess/shoes.php?subcategory=sportsshoes',
            "Formal Shoes" => '../shoess/shoes.php?subcategory=formalshoes',
            "Casual Shoes" => '../shoess/shoes.php?subcategory=casualshoes'
        ],
        "Beauty & Cosmetics" => [
            "Fragrances" => '../perfumes/index.php',
            "Skincare" => '#skincare',
            "Makeup" => '#makeup',
            "Hair Care" => '#hair-care',
            "Tools" => '#tools'
        ],
        "Home & Living" => [
            "Bedding" => '../homedecor/homedecor.php?subcategory=bedding',
            "Dining Room" => '../homedecor/homedecor.php?subcategory=dining-room',
            "Living Room" => '../homedecor/homedecor.php?subcategory=living-room',
            "Kitchen" => '../homedecor/homedecor.php?subcategory=kitchen',
            "Bathroom" => '../homedecor/homedecor.php?subcategory=bathroom',
            "Outdoor" => '../homedecor/homedecor.php?subcategory=outdoor'
        ],
        "Bags" => [
            "Handbags" => '../bagsfolder/bags.php?subcategory=handbags',
            "Backpacks" => '../bagsfolder/bags.php?subcategory=backpacks',
            "Totes" => '../bagsfolder/bags.php?subcategory=totes',
            "Clutches" => '../bagsfolder/bags.php?subcategory=clutches',
            "Crossbody" => '../bagsfolder/bags.php?subcategory=crossbody',
            "Travel Bags" => '../bagsfolder/bags.php?subcategory=travel'
        ]
    ];
    
    return $subcategoryMap[$categoryName][$subcategoryName] ?? getCategoryUrl($categoryName);
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
            <a href="../index.php" class="logo-text">
                <span class="logo-main">Glamour</span>
                <span class="logo-accent">Palace</span>
            </a>
        </div>
    </div>

    <!-- Navigation Menu - Center -->
    <div class="nav-menu-container">
        <ul class="nav-menu">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <li class="nav-item-modal">
                        <a href="<?php echo getCategoryUrl($category['name']); ?>" class="nav-link modal-trigger" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                        <!-- Category Modal -->
                        <div class="category-modal" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                            <div class="modal-header">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                            </div>
                            <?php if (!empty($category['subcategories'])): ?>
                                <div class="modal-content">
                                    <div class="subcategories-grid">
                                        <?php foreach ($category['subcategories'] as $subcategory): ?>
                                            <a href="<?php echo getSubcategoryUrl($category['name'], $subcategory); ?>" class="subcategory-item" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-subcategory="<?php echo htmlspecialchars($subcategory); ?>">
                                                <div class="subcategory-icon">
                                                    <i class="fas fa-chevron-right"></i>
                                                </div>
                                                <div class="subcategory-info">
                                                    <h4><?php echo htmlspecialchars($subcategory); ?></h4>
                                                    <span>Shop <?php echo htmlspecialchars($subcategory); ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>Dresses</h4>
                                        <span>Shop Dresses</span>
                                    </div>
                                </a>
                                <a href="#" class="subcategory-item">
                                    <div class="subcategory-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>Clothing</h4>
                                        <span>Shop Clothing</span>
                                    </div>
                                </a>
                                <a href="#" class="subcategory-item">
                                    <div class="subcategory-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>Tops</h4>
                                        <span>Shop Tops</span>
                                    </div>
                                </a>
                                <a href="#" class="subcategory-item">
                                    <div class="subcategory-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>Accessories</h4>
                                        <span>Shop Accessories</span>
                                    </div>
                                </a>
                                <a href="#" class="subcategory-item">
                                    <div class="subcategory-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>Sale</h4>
                                        <span>Shop Sale</span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static menu if no categories found -->
                <li><a href="../womenF/women.php" class="nav-link">Women's Clothing</a></li>
                <li><a href="../menfolder/men.php" class="nav-link">Men's Clothing</a></li>
                <li><a href="../perfumes/index.php" class="nav-link">Perfumes</a></li>
                <li><a href="../shoess/shoes.php" class="nav-link">Shoes</a></li>
                <li><a href="../accessories/accessories.php" class="nav-link">Accessories</a></li>
            <?php endif; ?>
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
                            <a href="../orders.php" class="menu-item">
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
            <div class="heart-icon" title="Wishlist">
                <i class="fas fa-heart"></i>
            </div>
            <div class="shopping-cart" title="Cart" style="position: relative;">
                <a href="../cart-unified.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </div>
        </div>

        <!-- Somalia Flag - Compact (Disabled) -->
        <div class="flag-container" title="Region Settings (Coming Soon)">
            <img src="../img/flag.jpg" alt="Somalia Flag" class="flag" id="somalia-flag">
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
                                <img src="../img/flag.jpg" alt="Somali Flag" class="flag-icon">
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

<!-- Chat Button -->
<div class="chat-button">
    <i class="fas fa-comments"></i>
</div> 

<!-- Cart Functionality Script -->
<script>
    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCartCount();
    });

    function loadCartCount() {
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_cart_count'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        })
        .catch(error => {
            // Silent error handling
        });
    }

    function updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        
        if (cartCountElement) {
            cartCountElement.textContent = count;
            
            // Add visual indicator if count > 0
            if (count > 0) {
                cartCountElement.style.cssText = `
                    display: flex !important;
                    background: #e53e3e;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    font-size: 12px;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    z-index: 10;
                `;
            } else {
                cartCountElement.style.display = 'none';
            }
        } else {
            console.error('Cart count element not found!');
        }
    }

    // Function to be called from other pages when adding to cart
    function addToCart(productId) {
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add_to_cart&product_id=${productId}&quantity=1&return_url=${encodeURIComponent(window.location.href)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
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
        if (path.includes('/childrenfolder/')) return 'children';
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
            'shoes': ['shoes', 'boots', 'sneakers', 'heels', 'flats', 'sandals', 'loafers', 'pumps', 'athletic'],
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
            'children': '/Glamour-system/childrenfolder/'
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
</script> 