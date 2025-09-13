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
        
        .wishlist-item-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f8f9fa;
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
            transition: background 0.3s ease;
        }
        
        .btn-remove:hover {
            background: #c82333;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .bulk-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .wishlist-actions-right {
            display: flex;
            align-items: center;
            gap: 15px;
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
        
        <div class="wishlist-actions" id="wishlist-actions" style="display: none;">
            <div class="bulk-actions">
                <label class="select-all-container">
                    <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll()">
                    <span class="checkmark"></span>
                    Select All
                </label>
            </div>
            
            <div class="wishlist-actions-right">
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
    
    <!-- Notification -->
    <div id="notification" class="notification"></div>
    
    <script>
        // Load wishlist from localStorage
        function loadWishlist() {
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
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
                        <img src="${item.image}" 
                             alt="${item.name}" 
                             class="wishlist-item-image"
                             onerror="this.src='https://via.placeholder.com/300x250?text=No+Image'">
                        
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
        }
        
        // Add to cart functionality
        function addToCart(productId) {
            // Try to use existing cart functionality
            if (typeof addToCart === 'function') {
                window.addToCart(productId);
            } else {
                // Fallback - just show notification
                showNotification('Product added to cart!', 'success');
            }
        }
        
        // Remove from wishlist
        function removeFromWishlist(productId) {
            if (confirm('Are you sure you want to remove this item from your wishlist?')) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                wishlist = wishlist.filter(item => item.id !== productId);
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                
                showNotification('Item removed from wishlist', 'info');
                loadWishlist();
                updateWishlistCount();
            }
        }
        
        // Clear wishlist
        function clearWishlist() {
            if (confirm('Are you sure you want to clear your entire wishlist?')) {
                localStorage.removeItem('wishlist');
                showNotification('Wishlist cleared', 'info');
                loadWishlist();
                updateWishlistCount();
            }
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
            
            if (confirm(`Are you sure you want to delete ${selectedCount} selected item(s) from your wishlist?`)) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.productId);
                
                // Remove selected items
                wishlist = wishlist.filter(item => !selectedIds.includes(item.id));
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                
                showNotification(`${selectedCount} item(s) removed from wishlist`, 'info');
                loadWishlist();
                updateWishlistCount();
            }
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
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
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
        
        // Load wishlist when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadWishlist();
            updateWishlistCount();
            updateSelectedCount();
        });
    </script>
</body>
</html>
</html>