<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Top Navigation Bar -->
<nav class="top-nav">
    <!-- Logo Container - Left Edge -->
    <div class="logo-container">
        <div class="logo">
            <a href="#" class="logo-text">Glomour</a>
        </div>
    </div>

    <!-- Navigation Menu - Center -->
    <div class="nav-menu-container">
        <ul class="nav-menu">
            <li><a href="#" class="nav-link">Home</a></li>
            <li><a href="#" class="nav-link">Dresses</a></li>
            <li><a href="#" class="nav-link">Clothing</a></li>
            <li><a href="#" class="nav-link">Tops</a></li>
            <li><a href="#" class="nav-link">Accessories</a></li>
            <li><a href="#" class="nav-link">Sale</a></li>
        </ul>
    </div>

    <!-- Right Side Elements - Right Edge -->
    <div class="nav-right-container">
        <!-- Search Box -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search">
            <i class="fas fa-search search-icon"></i>
        </div>

        <!-- Somalia Flag -->
        <div class="flag-container">
            <img src="../img/flag.jpg" alt="Somalia Flag" class="flag" id="somalia-flag">
        </div>

        <!-- User Icon -->
        <div class="user-icon" id="user-icon">
            <i class="fas fa-user"></i>
        </div>

        <!-- Heart Icon -->
        <div class="heart-icon">
            <i class="fas fa-heart"></i>
        </div>

        <!-- Shopping Cart -->
        <div class="shopping-cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
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
                    <?php
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
                    foreach ($regionOptions as $value => $label): ?>
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

<!-- User Login Modal -->
<div class="modal" id="user-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Welcome, love!</h3>
            <button class="close-btn" id="close-user-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="tab-container">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="signin">Sign In</button>
                    <button class="tab-btn" data-tab="signup">Create Account</button>
                </div>

                <!-- Sign In Tab -->
                <div class="tab-content active" id="signin-tab">
                    <form class="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-container">
                                <input type="password" id="password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <button type="submit" class="signin-btn">Sign In</button>
                        <a href="#" class="forgot-password">Forgot your Password?</a>
                    </form>
                </div>

                <!-- Sign Up Tab -->
                <div class="tab-content" id="signup-tab">
                    <form class="signup-form">
                        <div class="form-group">
                            <label for="signup-name">Full Name</label>
                            <input type="text" id="signup-name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <input type="email" id="signup-email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <div class="password-container">
                                <input type="password" id="signup-password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-confirm-password">Confirm Password</label>
                            <div class="password-container">
                                <input type="password" id="signup-confirm-password" class="form-input" required>
                                <span class="show-password">Show</span>
                            </div>
                        </div>
                        <button type="submit" class="signup-btn">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
