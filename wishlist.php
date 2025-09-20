<?php
session_start();
$page_title = 'My Wishlist - Glamour Palace';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="heading/header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/responsive.css?v=<?php echo time(); ?>">
    <script src="scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
    <script src="scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>
    <?php include 'includes/cart-notification-include.php'; ?>
    <style>
        .wishlist-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 100px;
        }
        
        .wishlist-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .wishlist-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .wishlist-count {
            color: #666;
            font-size: 1.1rem;
        }
        
        .wishlist-stats {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .category-breakdown {
            margin-top: 20px;
        }
        
        .category-breakdown h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .category-item {
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
            font-size: 0.9rem;
            color: #666;
        }
        
        .category-item .count {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .wishlist-item {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }
        
        .wishlist-item-checkbox {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
        }
        
        .wishlist-item-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #007bff;
        }
        
        .wishlist-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .wishlist-item-image-container {
            position: relative;
        }
        
        .wishlist-item-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f8f9fa;
        }
        
        .heart-button-wishlist {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .heart-button-wishlist:hover {
            background-color: #fff;
            transform: scale(1.03);
        }
        
        .heart-button-wishlist i {
            color: #e74c3c;
            font-size: 14px;
            transition: color 0.15s ease;
        }
        
        .heart-button-wishlist:hover i {
            color: #c0392b;
        }
        
        /* Heart button active state (when in wishlist) */
        .heart-button-wishlist.active {
            background-color: #e74c3c !important;
            border-color: #e74c3c !important;
        }
        
        .heart-button-wishlist.active i {
            color: white !important;
        }
        
        .wishlist-item-content {
            padding: 20px;
        }
        
        .wishlist-item-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .wishlist-item-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        
        .wishlist-item-category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-transform: capitalize;
        }
        
        .wishlist-item-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-add-to-cart {
            flex: 1;
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-add-to-cart:hover {
            background: #0056b3;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 50px;
        }
        
        .btn-remove:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }
        
        .btn-remove:active {
            transform: translateY(0);
        }
        
        .empty-wishlist {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-wishlist i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-wishlist h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-wishlist p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .btn-shop-now {
            background: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s ease;
        }
        
        .btn-shop-now:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        
        .wishlist-actions {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .wishlist-buttons-container {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: center;
            gap: 20px;
        }
        
        .wishlist-buttons-container > * {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .select-all-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
            color: #333;
        }
        
        .select-all-container input[type="checkbox"] {
            display: none;
        }
        
        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            background: white;
            transition: all 0.3s ease;
        }
        
        .select-all-container input[type="checkbox"]:checked + .checkmark {
            background: #007bff;
            border-color: #007bff;
        }
        
        .select-all-container input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .btn-delete-selected {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-delete-selected:hover:not(:disabled) {
            background: #c82333;
        }
        
        .btn-delete-selected:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .btn-clear-wishlist {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-clear-wishlist:hover {
            background: #5a6268;
        }
        
        
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.error {
            background: #dc3545;
        }
        
        .notification.info {
            background: #17a2b8;
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease;
        }
        
        /* Mobile responsive styles for modal */
        @media (max-width: 768px) {
            .modal-content {
                max-width: 100%;
                width: 100%;
                margin: 0;
                border-radius: 0;
                height: 100vh;
                max-height: 100vh;
            }
        }
        
        @media (max-width: 480px) {
            .modal-content {
                max-width: 100%;
                width: 100%;
                margin: 0;
                border-radius: 0;
                height: 100vh;
                max-height: 100vh;
            }
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .modal-header h3 i {
            color: #e74c3c;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .modal-close:hover {
            background: #f0f0f0;
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-body p {
            margin: 0;
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .btn-confirm-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease;
        }
        
        .btn-confirm-delete:hover {
            background: #c82333;
        }
        
        @media (max-width: 768px) {
            .wishlist-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .wishlist-item-actions {
                flex-direction: column;
            }
            
            .wishlist-header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .stat-item {
                padding: 10px;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .wishlist-buttons-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .wishlist-buttons-container > * {
                flex: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'heading/header.php'; ?>
    
    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1><i class="fas fa-heart"></i> My Wishlist</h1>
            <p class="wishlist-count" id="wishlist-count">0 item(s) in your wishlist</p>
        </div>
        
        <!-- Wishlist Statistics -->
        <div class="wishlist-stats" id="wishlist-stats" style="display: none;">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number" id="total-items">0</span>
                    <span class="stat-label">Items</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="total-value">$0.00</span>
                    <span class="stat-label">Total Value</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="categories-count">0</span>
                    <span class="stat-label">Categories</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="avg-price">$0.00</span>
                    <span class="stat-label">Avg Price</span>
                </div>
            </div>
            <div class="category-breakdown" id="category-breakdown">
                <!-- Category breakdown will be loaded here -->
            </div>
        </div>
        
        <div class="wishlist-actions" id="wishlist-actions" style="display: none;">
            <div class="wishlist-buttons-container">
                <label class="select-all-container">
                    <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll()">
                    <span class="checkmark"></span>
                    Select All
                </label>
                
                <button class="btn-delete-selected" id="btn-delete-selected" onclick="deleteSelected()" disabled>
                    <i class="fas fa-trash"></i> Delete Selected (<span id="selected-count">0</span>)
                </button>
                
                <button class="btn-clear-wishlist" onclick="clearWishlist()">
                    <i class="fas fa-trash"></i> Clear All
                </button>
            </div>
        </div>
        
        <div class="wishlist-grid" id="wishlist-grid">
            <!-- Wishlist items will be loaded here -->
        </div>
        
        <div class="empty-wishlist" id="empty-wishlist" style="display: none;">
            <i class="fas fa-heart"></i>
            <h2>Your wishlist is empty</h2>
            <p>Start adding items you love to your wishlist!</p>
            <a href="index.php" class="btn-shop-now">Start Shopping</a>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="delete-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p id="delete-message">Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
    
    <!-- Notification -->
    <div id="notification" class="notification"></div>
    
    <script>
        // Load wishlist from localStorage
        function loadWishlist() {
            // Use WishlistManager if available, otherwise fallback to localStorage
            let wishlist;
            if (window.wishlistManager) {
                wishlist = window.wishlistManager.getWishlist();
            } else {
                wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            }
            
            const wishlistGrid = document.getElementById('wishlist-grid');
            const emptyWishlist = document.getElementById('empty-wishlist');
            const wishlistActions = document.getElementById('wishlist-actions');
            const wishlistCount = document.getElementById('wishlist-count');
            
            // Update count
            wishlistCount.textContent = `${wishlist.length} item(s) in your wishlist`;
            
            if (wishlist.length === 0) {
                wishlistGrid.style.display = 'none';
                emptyWishlist.style.display = 'block';
                wishlistActions.style.display = 'none';
            } else {
                wishlistGrid.style.display = 'grid';
                emptyWishlist.style.display = 'none';
                wishlistActions.style.display = 'block';
                
                // Render wishlist items
                wishlistGrid.innerHTML = wishlist.map(item => `
                    <div class="wishlist-item" data-product-id="${item.id}">
                        <div class="wishlist-item-checkbox">
                            <input type="checkbox" class="item-checkbox" data-product-id="${item.id}" onchange="updateSelectedCount()">
                        </div>
                        <div class="wishlist-item-image-container">
                            <img src="${item.image}" 
                                 alt="${item.name}" 
                                 class="wishlist-item-image"
                                 onerror="this.src='https://via.placeholder.com/300x250?text=No+Image'">
                            <button class="heart-button-wishlist" data-product-id="${item.id}" onclick="toggleWishlistFromWishlistPage('${item.id}')">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        
                        <div class="wishlist-item-content">
                            <h3 class="wishlist-item-name">${item.name}</h3>
                            <div class="wishlist-item-price">$${item.price}</div>
                            <div class="wishlist-item-category">${item.category}</div>
                            ${item.selectedColor ? `<div class="wishlist-item-color" style="margin: 8px 0; font-size: 0.9rem; color: #666;">
                                <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background-color: ${item.selectedColor}; margin-right: 8px; border: 1px solid #ddd;"></span>
                                Color: ${item.selectedColor}
                            </div>` : ''}
                            
                            <div class="wishlist-item-actions">
                                <button class="btn-add-to-cart" onclick="addToCart('${item.id}')">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <button class="btn-remove" onclick="removeFromWishlist('${item.id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            // Update statistics
            updateWishlistStats();
        }
        
        // Add to cart functionality
        function addToCart(productId) {
            console.log('addToCart called with productId:', productId);
            
            // Use cart notification manager if available
            if (window.cartNotificationManager) {
                console.log('Using cartNotificationManager to add to cart');
                const success = window.cartNotificationManager.addToCart(productId);
                
                if (success) {
                    // Remove from wishlist after successfully adding to cart
                    if (window.wishlistManager) {
                        window.wishlistManager.removeFromWishlist(productId);
                        showNotification('Product added to cart and removed from wishlist!', 'success');
                        // Reload wishlist to update display
                        loadWishlist();
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
        
        // Global variables for modal
        let pendingDeleteAction = null;
        let pendingDeleteProductId = null;
        
        // Show delete confirmation modal
        function showDeleteModal(message, action, productId = null) {
            document.getElementById('delete-message').textContent = message;
            pendingDeleteAction = action;
            pendingDeleteProductId = productId;
            document.getElementById('delete-modal').style.display = 'flex';
        }
        
        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('delete-modal').style.display = 'none';
            pendingDeleteAction = null;
            pendingDeleteProductId = null;
        }
        
        // Confirm delete action
        function confirmDelete() {
            if (pendingDeleteAction) {
                pendingDeleteAction();
            }
            closeDeleteModal();
        }
        
        // Remove from wishlist
        function removeFromWishlist(productId) {
            console.log('removeFromWishlist called with productId:', productId);
            
            showDeleteModal('Are you sure you want to remove this item from your wishlist?', function() {
                // Use the enhanced WishlistManager
                if (window.wishlistManager) {
                    console.log('Using WishlistManager to remove item');
                    const success = window.wishlistManager.removeFromWishlist(productId);
                    if (success) {
                        // Reload wishlist to update the display
                        loadWishlist();
                        // Update statistics
                        updateWishlistStats();
                        console.log('Item removed successfully via WishlistManager');
                    } else {
                        console.log('Failed to remove item via WishlistManager');
                        showNotification('Failed to remove item from wishlist', 'error');
                    }
                } else {
                    console.log('WishlistManager not available, using fallback');
                    // Fallback to direct localStorage approach
                    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                    const initialLength = wishlist.length;
                    wishlist = wishlist.filter(item => item.id !== productId);
                    
                    if (wishlist.length < initialLength) {
                        localStorage.setItem('wishlist', JSON.stringify(wishlist));
                        showNotification('Item removed from wishlist', 'success');
                        loadWishlist();
                        updateWishlistCount();
                        updateWishlistStats();
                        console.log('Item removed successfully via fallback');
                    } else {
                        showNotification('Item not found in wishlist', 'error');
                        console.log('Item not found in wishlist');
                    }
                }
            }, productId);
        }
        
        function toggleWishlistFromWishlistPage(productId) {
            if (window.wishlistManager) {
                window.wishlistManager.toggleWishlist(productId);
                loadWishlist();
            }
        }
        
        // Clear wishlist
        function clearWishlist() {
            showDeleteModal('Are you sure you want to clear your entire wishlist?', function() {
                // Use the enhanced WishlistManager
                if (window.wishlistManager) {
                    window.wishlistManager.clearWishlist();
                    // Reload wishlist to update the display
                    loadWishlist();
                    // Update statistics
                    updateWishlistStats();
                } else {
                    // Fallback to direct localStorage approach
                    localStorage.removeItem('wishlist');
                    showNotification('Wishlist cleared', 'info');
                    loadWishlist();
                    updateWishlistCount();
                    updateWishlistStats();
                }
            });
        }
        
        // Toggle select all
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateSelectedCount();
        }
        
        // Update selected count
        function updateSelectedCount() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;
            const deleteBtn = document.getElementById('btn-delete-selected');
            const countSpan = document.getElementById('selected-count');
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const totalCheckboxes = document.querySelectorAll('.item-checkbox');
            
            // Update count display
            countSpan.textContent = selectedCount;
            
            // Enable/disable delete button
            deleteBtn.disabled = selectedCount === 0;
            
            // Update select all checkbox state
            if (selectedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedCount === totalCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        // Delete selected items
        function deleteSelected() {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;
            
            if (selectedCount === 0) {
                showNotification('No items selected', 'warning');
                return;
            }
            
            showDeleteModal(`Are you sure you want to delete ${selectedCount} selected item(s) from your wishlist?`, function() {
                let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.productId);
                
                // Remove selected items
                wishlist = wishlist.filter(item => !selectedIds.includes(item.id));
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                
                showNotification(`${selectedCount} item(s) removed from wishlist`, 'info');
                loadWishlist();
                updateWishlistCount();
                updateWishlistStats();
            });
        }
        
        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Update wishlist count in header
        function updateWishlistCount() {
            // Use WishlistManager if available, otherwise fallback to localStorage
            let wishlist;
            if (window.wishlistManager) {
                wishlist = window.wishlistManager.getWishlist();
            } else {
                wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            }
            
            const countElement = document.querySelector('.wishlist-count');
            if (countElement) {
                if (wishlist.length > 0) {
                    countElement.textContent = wishlist.length;
                    countElement.style.display = 'flex';
                } else {
                    countElement.style.display = 'none';
                }
            }
        }
        
        // Update wishlist statistics
        function updateWishlistStats() {
            // Use WishlistManager if available, otherwise fallback to localStorage
            let wishlist;
            if (window.wishlistManager) {
                wishlist = window.wishlistManager.getWishlist();
            } else {
                wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            }
            
            const statsElement = document.getElementById('wishlist-stats');
            const totalItemsElement = document.getElementById('total-items');
            const totalValueElement = document.getElementById('total-value');
            const categoriesCountElement = document.getElementById('categories-count');
            const avgPriceElement = document.getElementById('avg-price');
            const categoryBreakdownElement = document.getElementById('category-breakdown');
            
            if (wishlist.length === 0) {
                // Hide stats if wishlist is empty
                if (statsElement) {
                    statsElement.style.display = 'none';
                }
                return;
            }
            
            // Show stats if wishlist has items
            if (statsElement) {
                statsElement.style.display = 'block';
            }
            
            // Calculate statistics
            const categories = {};
            let totalValue = 0;
            
            wishlist.forEach(item => {
                // Count by category
                categories[item.category] = (categories[item.category] || 0) + 1;
                
                // Calculate total value
                const price = parseFloat(item.price) || 0;
                totalValue += price;
            });
            
            const totalItems = wishlist.length;
            const categoriesCount = Object.keys(categories).length;
            const avgPrice = totalItems > 0 ? totalValue / totalItems : 0;
            
            // Update stat elements
            if (totalItemsElement) {
                totalItemsElement.textContent = totalItems;
            }
            if (totalValueElement) {
                totalValueElement.textContent = `$${totalValue.toFixed(2)}`;
            }
            if (categoriesCountElement) {
                categoriesCountElement.textContent = categoriesCount;
            }
            if (avgPriceElement) {
                avgPriceElement.textContent = `$${avgPrice.toFixed(2)}`;
            }
            
            // Update category breakdown
            if (categoryBreakdownElement) {
                const categoryList = Object.entries(categories)
                    .sort((a, b) => b[1] - a[1]) // Sort by count descending
                    .map(([category, count]) => 
                        `<span class="category-item">${category} <span class="count">${count}</span></span>`
                    ).join('');
                
                categoryBreakdownElement.innerHTML = `
                    <h4>Categories Breakdown</h4>
                    <div class="category-list">${categoryList}</div>
                `;
            }
        }
        
        // Load wishlist when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadWishlist();
            updateWishlistCount();
            updateSelectedCount();
            
            // Listen for wishlist changes from other tabs or components
            window.addEventListener('storage', function(e) {
                if (e.key === 'wishlist') {
                    loadWishlist();
                    updateWishlistCount();
                    updateWishlistStats();
                }
            });
            
            // Listen for custom wishlist change events
            document.addEventListener('wishlistChange', function(e) {
                console.log('Wishlist change detected:', e.detail);
                loadWishlist();
                updateWishlistCount();
                updateWishlistStats();
            });
            
            // Close modal when clicking outside
            document.getElementById('delete-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });
        });
    </script>
</body>
</html>
</html>