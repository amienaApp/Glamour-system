/**
 * Universal Quickview Wishlist Fix
 * This script fixes wishlist functionality in quickview across all frontend pages
 */

class QuickviewWishlistFix {
    constructor() {
        this.init();
    }
    
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupWishlistFix());
        } else {
            this.setupWishlistFix();
        }
    }
    
    setupWishlistFix() {
        // Find all quickview wishlist buttons and fix them
        this.fixExistingQuickviewButtons();
        
        // Monitor for dynamically added quickview buttons
        this.observeQuickviewChanges();
        
        // Fix any existing quickview instances
        this.fixQuickviewInstances();
    }
    
    fixExistingQuickviewButtons() {
        const wishlistButtons = document.querySelectorAll('#add-to-wishlist-quick, .add-to-wishlist-quick');
        
        wishlistButtons.forEach(button => {
            // Remove existing event listeners by cloning the element
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Add proper event listener
            newButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleWishlistClick(newButton);
            });
        });
    }
    
    observeQuickviewChanges() {
        // Use MutationObserver to watch for dynamically added quickview elements
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node is a quickview wishlist button
                        if (node.id === 'add-to-wishlist-quick' || node.classList.contains('add-to-wishlist-quick')) {
                            this.fixWishlistButton(node);
                        }
                        
                        // Check for quickview buttons within the added node
                        const wishlistButtons = node.querySelectorAll('#add-to-wishlist-quick, .add-to-wishlist-quick');
                        wishlistButtons.forEach(button => this.fixWishlistButton(button));
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    fixWishlistButton(button) {
        // Remove existing event listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        // Add proper event listener
        newButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.handleWishlistClick(newButton);
        });
    }
    
    handleWishlistClick(button) {
        // Get product ID from various possible sources
        const productId = this.getProductId(button);
        
        if (!productId) {
            console.error('QuickviewWishlistFix: No product ID found');
            this.showNotification('Error: Could not identify product', 'error');
            return;
        }
        
        // Use global wishlist manager if available
        if (window.wishlistManager) {
            window.wishlistManager.toggleQuickViewWishlist(productId, button);
        } else {
            // Fallback - simple localStorage approach
            this.toggleWishlistFallback(productId, button);
        }
    }
    
    getProductId(button) {
        // Try multiple methods to get product ID
        
        // Method 1: From quickview title element
        const titleEl = document.getElementById('quick-view-title');
        if (titleEl && titleEl.getAttribute('data-product-id')) {
            return titleEl.getAttribute('data-product-id');
        }
        
        // Method 2: From quickview product name element
        const nameEl = document.getElementById('quick-view-product-name');
        if (nameEl && nameEl.getAttribute('data-product-id')) {
            return nameEl.getAttribute('data-product-id');
        }
        
        // Method 3: From button data attributes
        if (button.getAttribute('data-product-id')) {
            return button.getAttribute('data-product-id');
        }
        
        // Method 4: From quickview sidebar data
        const sidebar = document.getElementById('quick-view-sidebar');
        if (sidebar && sidebar.getAttribute('data-product-id')) {
            return sidebar.getAttribute('data-product-id');
        }
        
        // Method 5: Try to get from global quickview instances
        if (window.quickViewSidebar && window.quickViewSidebar.currentProduct) {
            return window.quickViewSidebar.currentProduct.id || window.quickViewSidebar.currentProduct._id;
        }
        
        if (window.quickViewManager && window.quickViewManager.currentProduct) {
            return window.quickViewManager.currentProduct.id || window.quickViewManager.currentProduct._id;
        }
        
        return null;
    }
    
    toggleWishlistFallback(productId, button) {
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        const existingIndex = wishlist.findIndex(item => item.id === productId);
        
        if (existingIndex > -1) {
            // Remove from wishlist
            wishlist.splice(existingIndex, 1);
            this.updateButtonState(button, false);
            this.showNotification('Removed from wishlist!', 'info');
        } else {
            // Add to wishlist
            const productDetails = this.getProductDetails(productId);
            if (productDetails) {
                wishlist.push(productDetails);
                this.updateButtonState(button, true);
                this.showNotification('Added to wishlist!', 'success');
            } else {
                this.showNotification('Could not add to wishlist', 'error');
                return;
            }
        }
        
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        
        // Update wishlist count if function exists
        if (typeof updateWishlistCount === 'function') {
            updateWishlistCount();
        }
    }
    
    getProductDetails(productId) {
        // Try to get product details from various sources
        
        // Method 1: From product card element
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (productCard) {
            return {
                id: productId,
                name: productCard.getAttribute('data-product-name') || 'Product',
                price: productCard.getAttribute('data-product-price') || '0',
                image: productCard.querySelector('img')?.src || '',
                category: productCard.getAttribute('data-product-category') || 'General',
                addedAt: new Date().toISOString()
            };
        }
        
        // Method 2: From quickview elements
        const nameEl = document.getElementById('quick-view-product-name');
        const priceEl = document.getElementById('quick-view-price');
        const imageEl = document.getElementById('quick-view-main-image');
        
        if (nameEl) {
            return {
                id: productId,
                name: nameEl.textContent || 'Product',
                price: priceEl ? priceEl.textContent.replace('$', '').replace(',', '') : '0',
                image: imageEl ? imageEl.src : '',
                category: 'General',
                addedAt: new Date().toISOString()
            };
        }
        
        // Method 3: From global quickview instances
        if (window.quickViewSidebar && window.quickViewSidebar.currentProduct) {
            const product = window.quickViewSidebar.currentProduct;
            return {
                id: productId,
                name: product.name || 'Product',
                price: product.price || '0',
                image: product.front_image || product.images?.[0] || '',
                category: product.category || 'General',
                addedAt: new Date().toISOString()
            };
        }
        
        if (window.quickViewManager && window.quickViewManager.currentProduct) {
            const product = window.quickViewManager.currentProduct;
            return {
                id: productId,
                name: product.name || 'Product',
                price: product.price || '0',
                image: product.front_image || product.images?.[0] || '',
                category: product.category || 'General',
                addedAt: new Date().toISOString()
            };
        }
        
        return null;
    }
    
    updateButtonState(button, isInWishlist) {
        if (isInWishlist) {
            button.classList.add('active');
            button.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
        } else {
            button.classList.remove('active');
            button.innerHTML = '<i class="far fa-heart"></i> Add to Wishlist';
        }
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 10000;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    fixQuickviewInstances() {
        // Fix existing quickview instances
        if (window.quickViewSidebar) {
            this.fixQuickviewInstance(window.quickViewSidebar);
        }
        
        if (window.quickViewManager) {
            this.fixQuickviewInstance(window.quickViewManager);
        }
    }
    
    fixQuickviewInstance(instance) {
        // Override the addToWishlist method if it exists
        if (instance && typeof instance.addToWishlist === 'function') {
            const originalAddToWishlist = instance.addToWishlist.bind(instance);
            
            instance.addToWishlist = () => {
                const productId = instance.currentProduct?.id || instance.currentProduct?._id;
                if (!productId) {
                    console.error('QuickviewWishlistFix: No product ID in quickview instance');
                    return;
                }
                
                const button = document.getElementById('add-to-wishlist-quick');
                if (button) {
                    this.handleWishlistClick(button);
                } else {
                    // Fallback to original method
                    originalAddToWishlist();
                }
            };
        }
    }
}

// Initialize the fix
window.quickviewWishlistFix = new QuickviewWishlistFix();

console.log('QuickviewWishlistFix: Initialized');
