<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Glamour Shopping'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <?php if (isset($additional_css)) echo $additional_css; ?>
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <div class="banner-content">
            <span class="banner-text">Free US shipping on orders over $75</span>
            <a href="#" class="banner-link">see details</a>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <div class="logo">
                <h1 class="logo-text">Glamour</h1>
                <span class="logo-subtitle">shopping</span>
            </div>

            <!-- Main Navigation -->
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="sale.php">Sale</a></li>
                    <li><a href="dresses.php">Dresses</a></li>
                    <li><a href="tops.php">Tops</a></li>
                    <li><a href="bottoms.php">Bottoms</a></li>
                    <li><a href="shoes.php">Shoes</a></li>
                    <li><a href="accessories.php">Accessories</a></li>
                </ul>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Search -->
                <div class="search-container">
                    <input type="text" placeholder="Search..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- User Actions -->
                <div class="user-actions">
                    <a href="#" class="action-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="country">US</span>
                    </a>
                    <a href="#" class="action-link">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="#" class="action-link">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count">0</span>
                    </a>
                    <a href="#" class="action-link">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sale Banner -->
    <?php if (isset($show_sale_banner) && $show_sale_banner): ?>
    <div class="sale-banner">
        <div class="sale-content">
            <div class="sale-left">
                <span class="sale-text">Summer Savings</span>
            </div>
            <div class="sale-center">
                <h2 class="sale-title">Buy 2, Get 1 Free All Sale Items</h2>
                <p class="sale-terms">TERMS APPLY. SEE DETAILS.</p>
            </div>
            <div class="sale-right">
                <span class="sale-unlimited">Unlimited Times Per Order</span>
                <a href="sale.php" class="sale-btn">Shop Now</a>
            </div>
        </div>
    </div>
    <?php endif; ?> 