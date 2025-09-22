/**
 * Cart Preloader - Makes cart loading feel instant
 * This script runs immediately and provides instant visual feedback
 */
(function() {
    'use strict';
    
    // OPTIMIZATION: Preload cart data in background
    let cartPreloaded = false;
    let cartData = null;
    
    // Function to preload cart data
    function preloadCartData() {
        if (cartPreloaded) return;
        
        const apiPath = getApiPath();
        
        // Use fetch with low priority
        fetch(apiPath, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_cart_summary',
            priority: 'low'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                cartData = {
                    cartCount: parseInt(data.cart_count) || 0,
                    cartTotal: parseFloat(data.cart_total) || 0,
                    timestamp: Date.now()
                };
                
                // Store in sessionStorage for instant access
                try {
                    sessionStorage.setItem('cart_preload', JSON.stringify(cartData));
                } catch (e) {
                    // Ignore storage errors
                }
                
                // Update cart count if element exists
                updateCartCountDisplay(cartData.cartCount);
            }
        })
        .catch(error => {
            console.log('Cart preload failed:', error);
            // Set default values on error
            cartData = {
                cartCount: 0,
                cartTotal: 0,
                timestamp: Date.now()
            };
        })
        .finally(() => {
            cartPreloaded = true;
        });
    }
    
    function getApiPath() {
        const currentPath = window.location.pathname;
        const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                               currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                               currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                               currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                               currentPath.includes('/bagsfolder/');
        
        return isInSubdirectory ? '../cart-api.php' : 'cart-api.php';
    }
    
    function updateCartCountDisplay(count) {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            if (count > 0) {
                cartCountElement.textContent = count;
                cartCountElement.style.display = 'flex';
            } else {
                cartCountElement.style.display = 'none';
            }
        }
    }
    
    // INSTANT cart icon click handler - NO DELAYS, NO PROCESSING
    function setupInstantCartClick() {
        const cartIcon = document.querySelector('.shopping-cart');
        if (cartIcon) {
            // ALWAYS add instant click handler - override everything
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                // INSTANT redirect - no processing, no delays, no checks
                const currentPath = window.location.pathname;
                const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                                       currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                                       currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                                       currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                                       currentPath.includes('/bagsfolder/');
                
                // INSTANT redirect - ZERO processing time
                window.location.href = isInSubdirectory ? '../cart-unified.php' : 'cart-unified.php';
            }, true); // Use capture phase to ensure this runs first
        }
    }
    
    // OPTIMIZATION: Load from sessionStorage first
    function loadFromCache() {
        try {
            const cached = sessionStorage.getItem('cart_preload');
            if (cached) {
                const data = JSON.parse(cached);
                // Check if cache is still valid (5 minutes)
                if (Date.now() - data.timestamp < 300000) {
                    updateCartCountDisplay(data.cartCount);
                    return true;
                }
            }
        } catch (e) {
            // Ignore cache errors
        }
        return false;
    }
    
    // Initialize immediately
    function init() {
        // Setup instant click handler FIRST - before anything else
        setupInstantCartClick();
        
        // Try to load from cache
        loadFromCache();
        
        // Preload cart data in background (no visual feedback)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', preloadCartData);
        } else {
            preloadCartData();
        }
    }
    
    // Start immediately - even before DOM is ready
    init();
    
    // Also setup when DOM is ready as backup
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupInstantCartClick);
    }
    
    // Make functions available globally
    window.cartPreloader = {
        preloadCartData,
        getCartData: () => cartData,
        updateCartCountDisplay
    };
    
    // Provide toggleCartDropdown function for compatibility
    window.toggleCartDropdown = function() {
        // Check if cart notification manager is available
        if (window.cartNotificationManager && window.cartNotificationManager.toggleCartDropdown) {
            return window.cartNotificationManager.toggleCartDropdown();
        } else {
            // Fallback: just trigger a click on the cart icon
            const cartIcon = document.querySelector('.shopping-cart');
            if (cartIcon) {
                cartIcon.click();
            }
        }
    };
})();
