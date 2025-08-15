<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
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
            <div class="user-icon" id="user-icon" title="Account">
                <i class="fas fa-user"></i>
            </div>
            <div class="heart-icon" title="Wishlist">
                <i class="fas fa-heart"></i>
            </div>
            <div class="shopping-cart" title="Cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
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

<!-- User Registration Modal -->
<div class="modal" id="user-modal">
    <div class="modal-content user-registration-modal">
                            <div class="modal-header">
                        <button class="close-btn" id="close-user-modal">
                            <i class="fas fa-times"></i>
                        </button>
                        <h2 class="modal-title">Create Account</h2>
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
                                <input type="tel" id="contact-number" class="form-input" placeholder="Contact Number * (+252 XXX XXX XXX)" required>
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
                                Create Account
                            </button>
                        </form>
                    </div>
    </div>
</div>

<!-- Chat Button -->
<div class="chat-button">
    <i class="fas fa-comments"></i>
</div> 
</div> 