<?php
/**
 * Universal Cart Notification Include
 * Include this file in all pages to ensure cart notifications work properly
 */

// Function to get the correct asset path based on current directory
function getCartAssetPath($path) {
    $currentDir = dirname($_SERVER['PHP_SELF']);
    
    // Check if we're in a subdirectory by counting directory levels
    $directoryLevels = substr_count($currentDir, '/');
    
    // If we're more than 1 level deep (e.g., /Glamour-system/womenF/), we need to go up one level
    if ($directoryLevels > 1) {
        return '../' . $path;
    }
    
    return $path;
}
?>

<!-- Cart Notification System -->
<link rel="stylesheet" href="<?php echo getCartAssetPath('styles/cart-notification.css'); ?>" type="text/css">

<script>
// Try to load the cart notification manager with fallback
(function() {
    const currentPath = window.location.pathname;
    const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                           currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                           currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                           currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                           currentPath.includes('/bagsfolder/');
    
    const scriptPath = isInSubdirectory ? '../scripts/cart-notification-manager.js' : 'scripts/cart-notification-manager.js';
    
    const script = document.createElement('script');
    script.src = scriptPath + '?v=' + Date.now(); // Add cache busting
    script.onload = function() {
        console.log('Cart notification manager loaded successfully from:', scriptPath);
    };
    script.onerror = function() {
        console.error('Failed to load cart notification manager from:', scriptPath);
        // Try alternative path
        const altScript = document.createElement('script');
        altScript.src = '../scripts/cart-notification-manager.js?v=' + Date.now();
        altScript.onload = function() {
            console.log('Cart notification manager loaded from alternative path');
        };
        altScript.onerror = function() {
            console.error('Failed to load cart notification manager from alternative path');
            // Create fallback functions
            window.toggleCartDropdown = function() {
                console.warn('Cart notification manager not available - using fallback');
                window.location.href = isInSubdirectory ? '../cart-unified.php' : 'cart-unified.php';
            };
            window.addToCart = function() {
                console.warn('Cart notification manager not available - add to cart disabled');
                return false;
            };
        };
        document.head.appendChild(altScript);
    };
    document.head.appendChild(script);
})();
</script>

<script>
// Ensure cart notification system is initialized only once
if (!window.cartNotificationInitialized) {
    window.cartNotificationInitialized = true;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize immediately for faster loading
        if (!window.cartNotificationManager && typeof CartNotificationManager !== 'undefined') {
            window.cartNotificationManager = new CartNotificationManager();
        }
        
        // Listen for cart updates from other components
        document.addEventListener('cart:itemAdded', function(e) {
            if (window.cartNotificationManager) {
                window.cartNotificationManager.handleCartUpdate(e.detail);
            }
        });
        
        document.addEventListener('cart:itemRemoved', function(e) {
            if (window.cartNotificationManager) {
                window.cartNotificationManager.handleCartUpdate(e.detail);
            }
        });
        
        document.addEventListener('cart:quantityUpdated', function(e) {
            if (window.cartNotificationManager) {
                window.cartNotificationManager.handleCartUpdate(e.detail);
            }
        });
        
        document.addEventListener('cart:cleared', function(e) {
            if (window.cartNotificationManager) {
                window.cartNotificationManager.handleCartUpdate(e.detail);
            }
        });
    });
}

// Global functions for backward compatibility
window.addToCartGlobal = function(productId, quantity = 1, color = '', size = '', price = null) {
    if (window.cartNotificationManager) {
        return window.cartNotificationManager.addToCart(productId, quantity, color, size, price);
    }
    return false;
};

window.removeFromCartGlobal = function(productId) {
    if (window.cartNotificationManager) {
        return window.cartNotificationManager.removeFromCart(productId);
    }
    return false;
};

window.updateCartQuantityGlobal = function(productId, quantity) {
    if (window.cartNotificationManager) {
        return window.cartNotificationManager.updateQuantity(productId, quantity);
    }
    return false;
};

window.refreshCartCountGlobal = function() {
    if (window.cartNotificationManager) {
        window.cartNotificationManager.refreshCartCount();
    }
};

window.getCartCountGlobal = function() {
    if (window.cartNotificationManager) {
        return window.cartNotificationManager.getCartCount();
    }
    return 0;
};
</script>

<!-- Cart Notification Styles -->
<style>
/* Cart count badge animations */
@keyframes cartCountBounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes cartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Cart icon states */
.shopping-cart.has-items {
    animation: cartPulse 2s infinite;
}

/* Cart count badge */
.cart-count, .cart-badge, [data-cart-count] {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e53e3e;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    font-weight: bold;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    animation: cartCountBounce 0.3s ease-in-out;
}

/* Loading state for cart */
.cart-loading {
    opacity: 0.6;
    pointer-events: none;
}

.cart-loading .cart-count {
    background: #6c757d;
}

/* OPTIMIZED: Enhanced loading states */
.shopping-cart.loading {
    opacity: 0.7;
    cursor: wait;
    pointer-events: none;
    transition: all 0.2s ease;
}

.shopping-cart.loading .cart-count {
    background: #ffc107;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Optimized cart icon transitions */
.shopping-cart {
    transition: all 0.2s ease;
}

.shopping-cart:hover {
    transform: scale(1.05);
}

.cart-count {
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 11px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Error state for cart */
.cart-error .cart-count {
    background: #dc3545;
    animation: none;
}

/* Cart notification toast */
.cart-notification {
    position: fixed;
    top: 20px;
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
    animation: slideInRight 0.3s ease-out;
}

.cart-notification.error {
    background: #dc3545;
}

.cart-notification.warning {
    background: #ffc107;
    color: #212529;
}

.cart-notification.info {
    background: #17a2b8;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification-content i {
    font-size: 16px;
}

/* Responsive design */
@media (max-width: 768px) {
    .cart-notification {
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
