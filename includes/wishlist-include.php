<?php
/**
 * Universal Wishlist Include
 * Add this to any page to enable wishlist functionality
 */
?>

<!-- Wishlist Scripts -->
<script src="<?php echo (strpos($_SERVER['REQUEST_URI'], '/') !== 0) ? '../' : ''; ?>scripts/wishlist-manager.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo (strpos($_SERVER['REQUEST_URI'], '/') !== 0) ? '../' : ''; ?>scripts/wishlist-integration.js?v=<?php echo time(); ?>"></script>

<!-- Wishlist Styles -->
<style>
/* Universal wishlist button styles */
.heart-button {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    border: 2px solid #ddd;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #666;
    transition: all 0.3s ease;
    z-index: 10;
    backdrop-filter: blur(5px);
}

.heart-button:hover {
    background: rgba(255, 255, 255, 1);
    border-color: #e74c3c;
    transform: scale(1.1);
}

.heart-button.active {
    background: #e74c3c;
    border-color: #e74c3c;
    color: white;
    animation: heartPulse 0.3s ease-in-out;
}

.heart-button.active i {
    color: white !important;
}

/* Product card hover effect for wishlist button */
.product-card:hover .heart-button {
    opacity: 1;
    visibility: visible;
}

.heart-button {
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

/* Wishlist button in product cards */
.product-card {
    position: relative;
}

.product-card .product-image,
.product-card .image-container {
    position: relative;
    overflow: hidden;
}

/* Quick view wishlist button */
#add-to-wishlist-quick {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    justify-content: center;
}

#add-to-wishlist-quick:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

#add-to-wishlist-quick.active {
    background: #27ae60;
}

#add-to-wishlist-quick.active:hover {
    background: #229954;
}

/* Wishlist count badge */
.wishlist-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    animation: countBounce 0.3s ease-in-out;
}

/* Animations */
@keyframes heartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes countBounce {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Responsive design */
@media (max-width: 768px) {
    .heart-button {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    #add-to-wishlist-quick {
        padding: 10px 16px;
        font-size: 13px;
    }
}

/* Loading states */
.heart-button.loading {
    pointer-events: none;
    opacity: 0.6;
}

.heart-button.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Accessibility */
.heart-button:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

.heart-button:focus:not(:focus-visible) {
    outline: none;
}

/* Wishlist notification styles */
.wishlist-notification {
    position: fixed;
    top: 100px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 10000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    font-weight: 500;
    max-width: 300px;
}

.wishlist-notification.show {
    transform: translateX(0);
}

.wishlist-notification.error {
    background: #dc3545;
}

.wishlist-notification.warning {
    background: #ffc107;
    color: #212529;
}

.wishlist-notification.info {
    background: #17a2b8;
}

@media (max-width: 768px) {
    .wishlist-notification {
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>

<script>
// Universal wishlist initialization
document.addEventListener('DOMContentLoaded', function() {
    // Ensure wishlist manager is available
    if (typeof window.wishlistManager === 'undefined') {
        console.warn('Wishlist manager not loaded. Please include wishlist-manager.js');
        return;
    }
    
    // Initialize wishlist integration
    if (typeof WishlistIntegration !== 'undefined') {
        new WishlistIntegration();
    }
    
    // Add wishlist buttons to any product cards that don't have them
    const productCards = document.querySelectorAll('.product-card:not([data-wishlist-injected])');
    productCards.forEach(card => {
        const productId = card.getAttribute('data-product-id');
        if (productId && !card.querySelector('.heart-button')) {
            const heartButton = document.createElement('button');
            heartButton.className = 'heart-button';
            heartButton.setAttribute('data-product-id', productId);
            heartButton.setAttribute('aria-label', 'Add to wishlist');
            heartButton.setAttribute('title', 'Add to wishlist');
            heartButton.innerHTML = '<i class="far fa-heart"></i>';
            
            const imageContainer = card.querySelector('.product-image, .image-container');
            if (imageContainer) {
                imageContainer.style.position = 'relative';
                imageContainer.appendChild(heartButton);
            } else {
                card.appendChild(heartButton);
            }
            
            card.setAttribute('data-wishlist-injected', 'true');
        }
    });
    
    // Update all wishlist button states
    if (window.wishlistManager) {
        setTimeout(() => {
            window.wishlistManager.initializeButtonStates();
        }, 500);
    }
});

// Global wishlist functions for easy access
window.addToWishlist = function(productId) {
    if (window.wishlistManager) {
        const button = document.querySelector(`[data-product-id="${productId}"] .heart-button`);
        if (button) {
            window.wishlistManager.toggleWishlist(productId, button);
        }
    }
};

window.removeFromWishlist = function(productId) {
    if (window.wishlistManager) {
        window.wishlistManager.removeFromWishlist(productId);
    }
};

window.isInWishlist = function(productId) {
    if (window.wishlistManager) {
        return window.wishlistManager.isInWishlist(productId);
    }
    return false;
};

window.getWishlistCount = function() {
    if (window.wishlistManager) {
        return window.wishlistManager.getWishlist().length;
    }
    return 0;
};

// Show wishlist notification
window.showWishlistNotification = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `wishlist-notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Hide notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
};
</script>
