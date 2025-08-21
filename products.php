<?php
require_once 'config/database.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

// Get all products
$products = $productModel->getAll();

// Get categories for filtering
$categories = $categoryModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Glamour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #4a5568;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .test-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }

        .filters {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group select {
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-image .no-image {
            width: 100%;
            height: 100%;
            background: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            font-size: 0.9rem;
        }

        .product-image .no-image i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .product-info {
            padding: 20px;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .product-category {
            color: #667eea;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .product-price .sale-price {
            text-decoration: line-through;
            color: #718096;
            font-size: 1rem;
            margin-left: 10px;
        }

        .product-description {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .product-colors {
            margin-bottom: 15px;
        }

        .color-variants {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .color-variant {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            background: #f1f5f9;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid #ddd;
        }

        .product-badges {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge.featured {
            background: #667eea;
            color: white;
        }

        .badge.sale {
            background: #f093fb;
            color: white;
        }

        .add-to-cart {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #cbd5e0;
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-bag"></i> Our Products</h1>
            <p>Discover our amazing collection of products</p>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="category-filter">Filter by Category:</label>
                <select id="category-filter" onchange="filterProducts()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="sort-filter">Sort by:</label>
                <select id="sort-filter" onchange="filterProducts()">
                    <option value="name">Name</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h2>No Products Available</h2>
                <p>We're working on adding amazing products. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                        <div class="product-image">
                            <?php 
                            // Handle both field name formats
                            $frontImage = $product['front_image'] ?? $product['image_front'] ?? '';
                            
                            if (!empty($frontImage)): ?>
                                <img src="<?php echo htmlspecialchars($frontImage); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i><div>No Image</div></div>'">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <div>No Image</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <div class="product-name">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </div>

                            <div class="product-category">
                                <i class="fas fa-tag"></i> 
                                <?php echo htmlspecialchars($product['category']); ?>
                                <?php if (!empty($product['subcategory'])): ?>
                                    <i class="fas fa-chevron-right"></i> 
                                    <?php echo htmlspecialchars($product['subcategory']); ?>
                                <?php endif; ?>
                            </div>

                            <div class="product-price">
                                $<?php echo number_format($product['price'], 2); ?>
                                <?php if (isset($product['salePrice'])): ?>
                                    <span class="sale-price">$<?php echo number_format($product['salePrice'], 2); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($product['description'])): ?>
                                <div class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>
                                    <?php if (strlen($product['description']) > 100): ?>...<?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($product['color_variants']) && !empty($product['color_variants'])): ?>
                                <div class="product-colors">
                                    <strong>Available Colors:</strong>
                                    <div class="color-variants">
                                        <?php foreach ($product['color_variants'] as $variant): ?>
                                            <span class="color-variant">
                                                <div class="color-dot" style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;"></div>
                                                <?php echo htmlspecialchars($variant['name']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="product-badges">
                                <?php if ($product['featured'] ?? false): ?>
                                    <span class="badge featured">
                                        <i class="fas fa-star"></i> Featured
                                    </span>
                                <?php endif; ?>
                                <?php if ($product['sale'] ?? false): ?>
                                    <span class="badge sale">
                                        <i class="fas fa-percentage"></i> On Sale
                                    </span>
                                <?php endif; ?>
                            </div>

                            <button class="add-to-cart" onclick="openAddToCartModal('<?php echo $product['_id']; ?>')" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 5px;">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Quick View Modal and Functions - Define these first
        let currentProduct = null;
        let selectedColor = null;
        let selectedSize = null;

        // Open quick view modal
        function openQuickView(productId) {
    
            
            // Show modal with loading state
            const modal = document.getElementById('quickViewModal');
            if (!modal) {
                console.error('Quick view modal not found!');
                alert('Quick view modal not found. Please refresh the page.');
                return;
            }
            
            modal.style.display = 'block';
            
            // Show loading in modal
            const modalBody = document.querySelector('.quick-view-body');
            if (modalBody) {
                modalBody.innerHTML = '<div style="text-align: center; padding: 50px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #007bff;"></i><p>Loading product details...</p></div>';
            }
            
            // Fetch product data
            fetchProductData(productId);
        }

        // Close quick view modal
        function closeQuickView() {
            document.getElementById('quickViewModal').style.display = 'none';
            currentProduct = null;
            selectedColor = null;
            selectedSize = null;
        }

        // Fetch product data for quick view
        async function fetchProductData(productId) {
            try {
                const response = await fetch(`get-product-details.php?id=${productId}`);
                const data = await response.json();
                
                if (data.success) {
                    populateQuickView(data.product);
                } else {
                    showNotification('Error loading product details', 'error');
                }
            } catch (error) {
                console.error('Error fetching product:', error);
                showNotification('Error loading product details', 'error');
            }
        }

        // Populate quick view with product data
        function populateQuickView(product) {
            currentProduct = product;
            
            // Restore modal body content
            const modalBody = document.querySelector('.quick-view-body');
            modalBody.innerHTML = `
                <div class="product-images">
                    <img id="quickViewImage" src="" alt="Product Image" class="main-image">
                    <div class="image-thumbnails" id="imageThumbnails">
                        <!-- Thumbnails will be populated here -->
                    </div>
                </div>
                <div class="product-details">
                    <h2 id="quickViewName"></h2>
                    <div class="price-section">
                        <span id="quickViewPrice" class="price"></span>
                        <span id="quickViewSalePrice" class="sale-price" style="display: none;"></span>
                    </div>
                    
                    <div class="color-section">
                        <h4>Color:</h4>
                        <div class="color-options" id="quickViewColors">
                            <!-- Color options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="size-section">
                        <h4>Size:</h4>
                        <div class="size-options" id="quickViewSizes">
                            <!-- Size options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="quantity-section">
                        <h4>Quantity:</h4>
                        <div class="quantity-controls">
                            <button onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quickViewQuantity" value="1" min="1" max="10">
                            <button onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="add-to-cart-final" onclick="addToCartFromQuickView()">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="wishlist-btn-final" onclick="toggleWishlistFromQuickView()">
                            <i class="fa fa-heart"></i> Wishlist
                        </button>
                    </div>
                </div>
            `;
            
            // Set basic product info
            document.getElementById('quickViewName').textContent = product.name;
            
            // Handle pricing
            const priceElement = document.getElementById('quickViewPrice');
            const salePriceElement = document.getElementById('quickViewSalePrice');
            
            if (product.sale && product.salePrice) {
                priceElement.textContent = `$${parseFloat(product.price).toFixed(2)}`;
                priceElement.style.textDecoration = 'line-through';
                priceElement.style.color = '#999';
                salePriceElement.textContent = `$${parseFloat(product.salePrice).toFixed(2)}`;
                salePriceElement.style.display = 'inline';
            } else {
                priceElement.textContent = `$${parseFloat(product.price).toFixed(2)}`;
                priceElement.style.textDecoration = 'none';
                priceElement.style.color = '#333';
                salePriceElement.style.display = 'none';
            }
            
            // Set main image
            const mainImage = product.images.front || product.images.back || '';
            document.getElementById('quickViewImage').src = mainImage;
            
            // Populate image thumbnails
            const thumbnailContainer = document.getElementById('imageThumbnails');
            thumbnailContainer.innerHTML = '';
            
            if (product.images.front) {
                addThumbnail(product.images.front, thumbnailContainer, true);
            }
            if (product.images.back) {
                addThumbnail(product.images.back, thumbnailContainer, false);
            }
            
            // Populate color options
            const colorContainer = document.getElementById('quickViewColors');
            colorContainer.innerHTML = '';
            
            const colors = product.colors || ['#000000'];
            colors.forEach((color, index) => {
                const colorOption = document.createElement('div');
                colorOption.className = 'color-option' + (index === 0 ? ' active' : '');
                colorOption.style.backgroundColor = color;
                colorOption.onclick = () => selectColor(color, colorOption);
                colorContainer.appendChild(colorOption);
            });
            
            // Populate size options
            const sizeContainer = document.getElementById('quickViewSizes');
            sizeContainer.innerHTML = '';
            
            const sizes = product.sizes || ['S', 'M', 'L'];
            const defaultSizeIndex = Math.floor(sizes.length / 2); // Default to middle size
            
            sizes.forEach((size, index) => {
                const sizeOption = document.createElement('div');
                sizeOption.className = 'size-option' + (index === defaultSizeIndex ? ' active' : '');
                sizeOption.textContent = size;
                sizeOption.onclick = () => selectSize(size, sizeOption);
                sizeContainer.appendChild(sizeOption);
            });
            
            // Set default selections
            selectedColor = colors[0];
            selectedSize = sizes[defaultSizeIndex];
        }

        // Add thumbnail image
        function addThumbnail(imageSrc, container, isActive = false) {
            const thumbnail = document.createElement('img');
            thumbnail.src = imageSrc;
            thumbnail.className = 'thumbnail' + (isActive ? ' active' : '');
            thumbnail.onclick = () => {
                document.getElementById('quickViewImage').src = imageSrc;
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            };
            container.appendChild(thumbnail);
        }

        // Select color
        function selectColor(color, element) {
            selectedColor = color;
            
            // Update active state
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
        }

        // Select size
        function selectSize(size, element) {
            selectedSize = size;
            
            // Update active state
            document.querySelectorAll('.size-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
        }

        // Change quantity
        function changeQuantity(change) {
            const quantityInput = document.getElementById('quickViewQuantity');
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity = Math.max(1, Math.min(10, currentQuantity + change));
            quantityInput.value = currentQuantity;
        }

        // Add to cart from quick view
        function addToCartFromQuickView() {
            if (!currentProduct) return;
            
            const quantity = parseInt(document.getElementById('quickViewQuantity').value);
            
            // Validate selections
            if (!selectedColor || !selectedSize) {
                showNotification('Please select color and size', 'error');
                return;
            }
            
            // Add to cart with selected options
            addToCartWithOptions(currentProduct.id, {
                color: selectedColor,
                size: selectedSize,
                quantity: quantity
            });
            
            // Close modal
            closeQuickView();
        }

        // Add to cart with options
        function addToCartWithOptions(productId, options = {}) {
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}&quantity=${options.quantity}&color=${options.color}&size=${options.size}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cart_count);
                    showNotification('Product added to cart successfully!', 'success');
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding product to cart', 'error');
            });
        }

        // Toggle wishlist from quick view
        function toggleWishlistFromQuickView() {
            const btn = document.querySelector('.wishlist-btn-final');
            btn.classList.toggle('active');
            
            if (btn.classList.contains('active')) {
                showNotification('Added to wishlist!', 'success');
            } else {
                showNotification('Removed from wishlist!', 'info');
            }
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            // Add styles
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                font-weight: 600;
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            
            if (type === 'success') {
                notification.style.background = '#28a745';
            } else if (type === 'error') {
                notification.style.background = '#dc3545';
            } else {
                notification.style.background = '#17a2b8';
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('quickViewModal');
            
            if (event.target === modal) {
                closeQuickView();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('quickViewModal');
                if (modal.style.display === 'block') {
                    closeQuickView();
                }
            }
        });

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize quick view functions
        });

        function filterProducts() {
            const categoryFilter = document.getElementById('category-filter').value;
            const sortFilter = document.getElementById('sort-filter').value;
            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const category = product.getAttribute('data-category');
                
                if (!categoryFilter || category === categoryFilter) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });

            // Simple sorting (in a real app, you'd want server-side sorting)
            const productsArray = Array.from(products);
            const productsGrid = document.querySelector('.products-grid');
            
            if (sortFilter === 'price-low') {
                productsArray.sort((a, b) => {
                    const priceA = parseFloat(a.querySelector('.product-price').textContent.replace('$', ''));
                    const priceB = parseFloat(b.querySelector('.product-price').textContent.replace('$', ''));
                    return priceA - priceB;
                });
            } else if (sortFilter === 'price-high') {
                productsArray.sort((a, b) => {
                    const priceA = parseFloat(a.querySelector('.product-price').textContent.replace('$', ''));
                    const priceB = parseFloat(b.querySelector('.product-price').textContent.replace('$', ''));
                    return priceB - priceA;
                });
            }

            productsArray.forEach(product => {
                productsGrid.appendChild(product);
            });
        }

        function addToCart(productId) {
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header if it exists
                    updateCartCount(data.cart_count);
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

        function updateCartCount(count) {
            // Update cart count in header if cart icon exists
            const cartIcons = document.querySelectorAll('.fa-shopping-cart');
            cartIcons.forEach(icon => {
                const parent = icon.parentElement;
                if (parent) {
                    // Remove existing badge
                    const existingBadge = parent.querySelector('.cart-badge');
                    if (existingBadge) {
                        existingBadge.remove();
                    }
                    
                    // Add new badge if count > 0
                    if (count > 0) {
                        const badge = document.createElement('span');
                        badge.className = 'cart-badge';
                        badge.textContent = count;
                        badge.style.cssText = `
                            position: absolute;
                            top: -8px;
                            right: -8px;
                            background: #e53e3e;
                            color: white;
                            border-radius: 50%;
                            width: 20px;
                            height: 20px;
                            font-size: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: bold;
                        `;
                        parent.style.position = 'relative';
                        parent.appendChild(badge);
                    }
                }
            });
        }

        // Load cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('cart-api.php', {
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
                console.error('Error loading cart count:', error);
            });
        });

        // Add to Cart Modal Functions
        function openAddToCartModal(productId) {
            // Set product ID in modal for later use
            document.getElementById('addToCartModal').setAttribute('data-product-id', productId);
            
            // Fetch product details
            fetch(`get-product-details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        populateAddToCartModal(product);
                        document.getElementById('addToCartModal').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    } else {
                        alert('Error loading product details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading product details');
                });
        }

        function closeAddToCartModal() {
            document.getElementById('addToCartModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            // Reset selections
            window.selectedColor = '';
            window.selectedSize = '';
            window.selectedQuantity = 1;
        }

        function populateAddToCartModal(product) {
            // Set product details
            document.getElementById('addToCartProductName').textContent = product.name;
            document.getElementById('addToCartProductImage').src = product.front_image || product.image_front || '';
            document.getElementById('addToCartProductPrice').textContent = `$${parseFloat(product.price).toFixed(2)}`;
            
            // Populate colors
            const colorContainer = document.getElementById('addToCartColors');
            colorContainer.innerHTML = '';
            const colors = product.colors || ['#000000'];
            colors.forEach(color => {
                const colorDiv = document.createElement('div');
                colorDiv.className = 'color-option';
                colorDiv.style.backgroundColor = color;
                colorDiv.onclick = () => selectAddToCartColor(colorDiv, color);
                colorContainer.appendChild(colorDiv);
            });
            
            // Populate sizes
            const sizeContainer = document.getElementById('addToCartSizes');
            sizeContainer.innerHTML = '';
            const sizes = product.sizes || ['S', 'M', 'L'];
            sizes.forEach(size => {
                const sizeDiv = document.createElement('div');
                sizeDiv.className = 'size-option';
                sizeDiv.textContent = size;
                sizeDiv.onclick = () => selectAddToCartSize(sizeDiv, size);
                sizeContainer.appendChild(sizeDiv);
            });
            
            // Reset selections
            window.selectedColor = '';
            window.selectedSize = '';
            window.selectedQuantity = 1;
            updateAddToCartSummary();
        }

        function selectAddToCartColor(element, color) {
            document.querySelectorAll('#addToCartColors .color-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            window.selectedColor = color;
            updateAddToCartSummary();
        }

        function selectAddToCartSize(element, size) {
            document.querySelectorAll('#addToCartSizes .size-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            window.selectedSize = size;
            updateAddToCartSummary();
        }

        function changeAddToCartQuantity(change) {
            const newQuantity = Math.max(1, Math.min(10, window.selectedQuantity + change));
            window.selectedQuantity = newQuantity;
            document.getElementById('addToCartQuantity').value = newQuantity;
            updateAddToCartSummary();
        }

        function updateAddToCartSummary() {
            document.getElementById('addToCartSelectedColor').textContent = window.selectedColor || 'Please select a color';
            document.getElementById('addToCartSelectedSize').textContent = window.selectedSize || 'Please select a size';
            document.getElementById('addToCartSelectedQuantity').textContent = window.selectedQuantity;
            
            // Enable/disable add to cart button
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (window.selectedColor && window.selectedSize) {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Please Select Options First';
            }
        }

        function addToCartFromModal() {
            if (!window.selectedColor || !window.selectedSize) {
                alert('Please select both color and size');
                return;
            }
            
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            // Get product ID from the modal
            const productId = document.getElementById('addToCartModal').getAttribute('data-product-id');
            
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}&quantity=${window.selectedQuantity}&color=${window.selectedColor}&size=${window.selectedSize}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart successfully!');
                    closeAddToCartModal();
                    updateCartCount(data.cart_count || 0);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product to cart');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const quickViewModal = document.getElementById('quickViewModal');
            if (event.target === quickViewModal) {
                closeQuickView();
            }
            
            const addToCartModal = document.getElementById('addToCartModal');
            if (event.target === addToCartModal) {
                closeAddToCartModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const quickViewModal = document.getElementById('quickViewModal');
                if (quickViewModal.style.display === 'block') {
                    closeQuickView();
                }
                
                const addToCartModal = document.getElementById('addToCartModal');
                if (addToCartModal.style.display === 'block') {
                    closeAddToCartModal();
                }
            }
        });

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="quick-view-modal">
        <div class="quick-view-content">
            <div class="quick-view-header">
                <h3>Product Details</h3>
                <button class="close-quick-view" onclick="closeQuickView()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="quick-view-body">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Add to Cart Modal -->
    <div id="addToCartModal" class="add-to-cart-modal">
        <div class="add-to-cart-content">
            <div class="add-to-cart-header">
                <h3><i class="fas fa-shopping-cart"></i> Add to Cart</h3>
                <button class="close-add-to-cart" onclick="closeAddToCartModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="add-to-cart-body">
                <div class="product-preview">
                    <img id="addToCartProductImage" src="" alt="Product Image" class="preview-image">
                    <div class="product-info-preview">
                        <h2 id="addToCartProductName"></h2>
                        <div class="price-preview">
                            <span id="addToCartProductPrice"></span>
                        </div>
                    </div>
                </div>
                
                <div class="options-section">
                    <div class="selection-required">
                        <i class="fas fa-info-circle"></i>
                        <strong>Required:</strong> Please select both a color and size before adding to cart.
                    </div>
                    
                    <div class="option-group">
                        <h4>Color</h4>
                        <div class="color-options" id="addToCartColors">
                            <!-- Color options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <h4>Size</h4>
                        <div class="size-options" id="addToCartSizes">
                            <!-- Size options will be populated here -->
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <h4>Quantity</h4>
                        <div class="quantity-controls">
                            <button onclick="changeAddToCartQuantity(-1)">-</button>
                            <input type="number" id="addToCartQuantity" value="1" min="1" max="10" readonly>
                            <button onclick="changeAddToCartQuantity(1)">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="summary-section">
                    <div class="summary-item">
                        <span>Selected Color:</span>
                        <span id="addToCartSelectedColor">Please select a color</span>
                    </div>
                    <div class="summary-item">
                        <span>Selected Size:</span>
                        <span id="addToCartSelectedSize">Please select a size</span>
                    </div>
                    <div class="summary-item">
                        <span>Quantity:</span>
                        <span id="addToCartSelectedQuantity">1</span>
                    </div>
                </div>
                
                <div class="action-section">
                    <button class="add-to-cart-final-btn" onclick="addToCartFromModal()" id="addToCartBtn" disabled>
                        <i class="fas fa-shopping-cart"></i>
                        Please Select Options First
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Quick View Modal Styles */
        .quick-view-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            backdrop-filter: blur(5px);
        }

        .quick-view-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .quick-view-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
        }

        .quick-view-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .close-quick-view {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 5px;
        }

        .close-quick-view:hover {
            color: #333;
        }

        .quick-view-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .image-thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .thumbnail.active {
            border-color: #007bff;
        }

        .product-details h2 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.8rem;
        }

        .price-section {
            margin-bottom: 20px;
        }

        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .sale-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
            margin-left: 10px;
        }

        .color-section,
        .size-section,
        .quantity-section {
            margin-bottom: 20px;
        }

        .color-section h4,
        .size-section h4,
        .quantity-section h4 {
            margin: 0 0 10px 0;
            color: #555;
            font-size: 1rem;
        }

        .color-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .color-option.active {
            border-color: #333;
        }

        .size-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .size-option {
            padding: 8px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .size-option:hover {
            border-color: #007bff;
        }

        .size-option.active {
            border-color: #007bff;
            background: #007bff;
            color: white;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls button {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .quantity-controls button:hover {
            background: #f8f9fa;
        }

        .quantity-controls input {
            width: 60px;
            height: 35px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .add-to-cart-final {
            flex: 1;
            padding: 15px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .add-to-cart-final:hover {
            background: #0056b3;
        }

        .wishlist-btn-final {
            padding: 15px 20px;
            background: white;
            color: #666;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .wishlist-btn-final:hover {
            border-color: #007bff;
            color: #007bff;
        }

        .wishlist-btn-final.active {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .quick-view-body {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }

            .main-image {
                height: 300px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Add to Cart Modal Styles */
        .add-to-cart-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .add-to-cart-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .add-to-cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: linear-gradient(135deg, #0066cc 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .add-to-cart-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-add-to-cart {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .close-add-to-cart:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .add-to-cart-body {
            padding: 30px;
        }

        .product-preview {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }

        .product-info-preview h2 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
            color: #333;
        }

        .price-preview {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0066cc;
        }

        .options-section {
            margin-bottom: 30px;
        }

        .selection-required {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
            font-size: 0.9rem;
        }

        .option-group {
            margin-bottom: 25px;
        }

        .option-group h4 {
            margin: 0 0 15px 0;
            color: #555;
            font-size: 1rem;
            font-weight: 500;
        }

        .color-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.active {
            border-color: #0066cc;
            transform: scale(1.1);
            opacity: 1;
        }

        .size-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .size-option {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
            opacity: 0.7;
        }

        .size-option:hover {
            border-color: #0066cc;
            background: #f8f9fa;
        }

        .size-option.active {
            border-color: #0066cc;
            background: #0066cc;
            color: white;
            opacity: 1;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 10px;
            width: fit-content;
        }

        .quantity-controls button {
            width: 35px;
            height: 35px;
            border: none;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            color: #0066cc;
            transition: all 0.3s ease;
        }

        .quantity-controls button:hover {
            background: #0066cc;
            color: white;
        }

        .quantity-controls input {
            width: 60px;
            height: 35px;
            text-align: center;
            border: none;
            background: white;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .summary-item:last-child {
            margin-bottom: 0;
        }

        .action-section {
            text-align: center;
        }

        .add-to-cart-final-btn {
            width: 100%;
            padding: 15px 30px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .add-to-cart-final-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .add-to-cart-final-btn:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
            transform: none !important;
            opacity: 0.6 !important;
        }

        .add-to-cart-final-btn:disabled:hover {
            background: #ccc !important;
            transform: none !important;
        }

        @media (max-width: 768px) {
            .add-to-cart-content {
                width: 95%;
                margin: 10% auto;
            }

            .product-preview {
                flex-direction: column;
                text-align: center;
            }

            .preview-image {
                width: 120px;
                height: 120px;
                margin: 0 auto;
            }
        }
    </style>
</body>
</html>

