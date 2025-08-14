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

                            <button class="add-to-cart" onclick="addToCart(<?php echo $product['_id']; ?>)">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
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
            // This would integrate with your cart system
            alert('Product added to cart! (Product ID: ' + productId + ')');
        }
    </script>
</body>
</html>

