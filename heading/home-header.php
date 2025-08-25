<?php
// Check if session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if MongoDB extension is available before loading autoloader
$mongodbAvailable = extension_loaded('mongodb');

if ($mongodbAvailable) {
    // Only include autoloader if MongoDB extension is available
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Try to load MongoDB dependencies
    $categories = [];
    $isLoggedIn = isset($_SESSION['user_id']);
    
    try {
        require_once __DIR__ . '/../config/mongodb.php';
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
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li class="nav-item-modal">
                <a href="#" class="nav-link">Categories</a>
                <!-- Categories Dropdown -->
                <div class="category-dropdown">
                    <div class="dropdown-item">
                        <a href=womenF/index.php" class="dropdown-link">
                            <i class="fas fa-female"></i>
                            <span>Women Clothing</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="menfolder/men.php" class="dropdown-link">
                            <i class="fas fa-male"></i>
                            <span>Men Clothing</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="childrenfolder/children.php" class="dropdown-link">
                            <i class="fas fa-child"></i>
                            <span>Kids Collection</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="shoess/men.php" class="dropdown-link">
                            <i class="fas fa-shoe-prints"></i>
                            <span>Shoes</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="perfumes/index.php" class="dropdown-link">
                            <i class="fas fa-spray-can"></i>
                            <span>Perfumes</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="accessories/accessories.php" class="dropdown-link">
                            <i class="fas fa-gem"></i>
                            <span>Accessories</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="#" class="dropdown-link">
                            <i class="fas fa-home"></i>
                            <span>Home Decor</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="Glamour-system/bagsfolder/bags.php" class="dropdown-link">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Bags</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="#" class="dropdown-link">
                            <i class="fas fa-palette"></i>
                            <span>Beauty & Cosmetics</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="#" class="dropdown-link">
                            <i class="fas fa-running"></i>
                            <span>Sports Items</span>
                        </a>
                    </div>
                </div>
            </li>
            <li><a href="pages/contact.php" class="nav-link">Contact us</a></li>
            <li><a href="pages/about.php" class="nav-link">About us</a></li>
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
                <a href="../cart.php" style="text-decoration: none; color: inherit;">
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
                fetch('./login-handler.php', {
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
                fetch('./register-handler.php', {
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
            fetch('./logout-handler.php', {
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



    });
</script> 