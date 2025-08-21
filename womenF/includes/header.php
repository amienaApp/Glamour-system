<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            <li><a href="#" class="nav-link">Home</a></li>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <li class="nav-item-modal">
                        <a href="#" class="nav-link modal-trigger" data-category="<?php echo htmlspecialchars($category['name']); ?>">
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
                                            <a href="#<?php echo strtolower(str_replace(' ', '-', $subcategory)); ?>-section" class="subcategory-item" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-subcategory="<?php echo htmlspecialchars($subcategory); ?>">
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
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static menu if no categories found -->
                <li><a href="#" class="nav-link">Dresses</a></li>
                <li><a href="#" class="nav-link">Clothing</a></li>
                <li><a href="#" class="nav-link">Tops</a></li>
                <li><a href="#" class="nav-link">Accessories</a></li>
                <li><a href="#" class="nav-link">Sale</a></li>
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
                            <a href="logout-handler.php" class="auth-btn logout-btn">
                                Sign Out
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="auth-section">
                            <button class="auth-btn signin-btn" id="signin-btn">
                                Sign In
                            </button>
                            <button class="auth-btn signup-btn" id="signup-btn">
                                Sign Up
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Menu Items -->
                    <?php if ($isLoggedIn): ?>
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
                    <option value="banadir">Banadir</option>
                    <option value="bari">Bari</option>
                    <option value="bay">Bay</option>
                    <option value="galguduud">Galguduud</option>
                    <option value="gedo">Gedo</option>
                    <option value="hiran">Hiran</option>
                    <option value="jubbada-dhexe">Jubbada Dhexe</option>
                    <option value="jubbada-hoose">Jubbada Hoose</option>
                    <option value="mudug">Mudug</option>
                    <option value="nugaal">Nugaal</option>
                    <option value="sanaag">Sanaag</option>
                    <option value="shabeellaha-dhexe">Shabeellaha Dhexe</option>
                    <option value="shabeellaha-hoose">Shabeellaha Hoose</option>
                    <option value="sool">Sool</option>
                    <option value="togdheer">Togdheer</option>
                    <option value="woqooyi-galbeed">Woqooyi Galbeed</option>
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

<!-- Success Notification -->
<div class="success-notification" id="success-notification">
    <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span class="notification-text">Registration Successful!</span>
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
                            <option value="banadir">Banadir</option>
                            <option value="bari">Bari</option>
                            <option value="bay">Bay</option>
                            <option value="galguduud">Galguduud</option>
                            <option value="gedo">Gedo</option>
                            <option value="hiran">Hiran</option>
                            <option value="jubbada-dhexe">Jubbada Dhexe</option>
                            <option value="jubbada-hoose">Jubbada Hoose</option>
                            <option value="mudug">Mudug</option>
                            <option value="nugaal">Nugaal</option>
                            <option value="sanaag">Sanaag</option>
                            <option value="shabeellaha-dhexe">Shabeellaha Dhexe</option>
                            <option value="shabeellaha-hoose">Shabeellaha Hoose</option>
                            <option value="sool">Sool</option>
                            <option value="togdheer">Togdheer</option>
                            <option value="woqooyi-galbeed">Woqooyi Galbeed</option>
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
        console.log('Loading cart count...');
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_cart_count'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Cart API response:', data);
            if (data.success) {
                updateCartCount(data.cart_count);
            } else {
                console.error('Cart API error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
    }

    function updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        console.log('Updating cart count:', count, 'Element found:', !!cartCountElement);
        
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
            body: `action=add_to_cart&product_id=${productId}&quantity=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                // Show success message
                if (typeof showNotification === 'function') {
                    showNotification('Product added to cart successfully!', 'success');
                } else {
                    alert('Product added to cart successfully!');
                }
            } else {
                if (typeof showNotification === 'function') {
                    showNotification('Error: ' + data.message, 'error');
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showNotification === 'function') {
                showNotification('Error adding product to cart', 'error');
            } else {
                alert('Error adding product to cart');
            }
        });
    }

    // User Dropdown Functionality
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

        // User icon click functionality
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('User icon clicked');
                if (userModal) {
                    userModal.style.display = 'flex';
                    // Trigger reflow to ensure display change is applied
                    userModal.offsetHeight;
                    userModal.classList.add('show');
                    // Show login form by default
                    if (loginForm) loginForm.style.display = 'flex';
                    if (registerForm) registerForm.style.display = 'none';
                }
            });
        }

        // Sign In button functionality
        if (signinBtn) {
            signinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Sign In button clicked');
                if (userModal) {
                    userModal.style.display = 'flex';
                    // Trigger reflow to ensure display change is applied
                    userModal.offsetHeight;
                    userModal.classList.add('show');
                    if (loginForm) loginForm.style.display = 'flex';
                    if (registerForm) registerForm.style.display = 'none';
                }
            });
        }

        // Sign Up button functionality
        if (signupBtn) {
            signupBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Sign Up button clicked');
                if (userModal) {
                    userModal.style.display = 'flex';
                    // Trigger reflow to ensure display change is applied
                    userModal.offsetHeight;
                    userModal.classList.add('show');
                    if (loginForm) loginForm.style.display = 'none';
                    if (registerForm) registerForm.style.display = 'flex';
                }
            });
        }

        // Close modal functionality
        const closeButtons = document.querySelectorAll('.close-btn');
        closeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Close button clicked');
                if (userModal) {
                    userModal.classList.remove('show');
                    setTimeout(() => {
                        userModal.style.display = 'none';
                    }, 300);
                }
            });
        });

        // Close modal when clicking outside
        if (userModal) {
            userModal.addEventListener('click', function(e) {
                if (e.target === userModal) {
                    userModal.classList.remove('show');
                    setTimeout(() => {
                        userModal.style.display = 'none';
                    }, 300);
                }
            });
        }

        // Switch between login and register forms
        const switchToRegister = document.getElementById('switch-to-register');
        const switchToLogin = document.getElementById('switch-to-login');

        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                if (loginForm) loginForm.style.display = 'none';
                if (registerForm) registerForm.style.display = 'flex';
            });
        }

        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                if (registerForm) registerForm.style.display = 'none';
                if (loginForm) loginForm.style.display = 'flex';
            });
        }

        // Add keyboard support for closing modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userModal && userModal.style.display === 'flex') {
                userModal.classList.remove('show');
                setTimeout(() => {
                    userModal.style.display = 'none';
                }, 300);
            }
        });

        // Add smooth transitions
        if (userModal) {
            userModal.addEventListener('transitionend', function() {
                if (userModal.style.display === 'none') {
                    userModal.style.visibility = 'hidden';
                }
            });
        }

        // Menu item click handlers
        const dashboardLink = document.getElementById('dashboard-link');
        const myInfoLink = document.getElementById('my-info-link');
        const notificationsLink = document.getElementById('notifications-link');
        const notifyMeLink = document.getElementById('notify-me-link');
        const giftCardsLink = document.getElementById('gift-cards-link');

        // Dashboard link (placeholder)
        if (dashboardLink) {
            dashboardLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Dashboard feature coming soon!');
            });
        }

        // My Info link (placeholder)
        if (myInfoLink) {
            myInfoLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('My Info feature coming soon!');
            });
        }

        // Notifications link (placeholder)
        if (notificationsLink) {
            notificationsLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Notifications feature coming soon!');
            });
        }

        // Notify Me List link (placeholder)
        if (notifyMeLink) {
            notifyMeLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Notify Me List feature coming soon!');
            });
        }

        // Gift Cards link (placeholder)
        if (giftCardsLink) {
            giftCardsLink.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Gift Cards feature coming soon!');
            });
        }

        // Prevent form submission for now (placeholder)
        const loginFormElement = document.querySelector('.login-form');
        const registerFormElement = document.querySelector('.user-registration-form');

        if (loginFormElement) {
            loginFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                // Show success message
                showNotification('Login functionality coming soon!', 'info');
            });
        }

        if (registerFormElement) {
            registerFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                // Show success message
                showNotification('Registration functionality coming soon!', 'info');
            });
        }

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('success-notification');
            if (notification) {
                const textElement = notification.querySelector('.notification-text');
                if (textElement) {
                    textElement.textContent = message;
                }
                
                // Update notification style based on type
                notification.className = `success-notification ${type}`;
                notification.classList.add('show');
                
                // Hide notification after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            } else {
                // Fallback to alert if notification element not found
                alert(message);
            }
        }
    });
</script>
</div> 
</div> 