/**
 * Enhanced Cart Notification Manager
 * Provides real-time cart updates, notifications, and management like the wishlist system
 */
class CartNotificationManager {
    constructor() {
        this.apiPath = this.getApiPath();
        this.cartCount = 0;
        this.cartItems = [];
        this.isLoading = false;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupStorageListener();
        this.initializeCartDropdown();
        // Load cart count with a small delay to prevent blocking page load
        setTimeout(() => this.loadCartCount(), 100);
    }
    
    getApiPath() {
        const currentPath = window.location.pathname;
        const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                               currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                               currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                               currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                               currentPath.includes('/bagsfolder/');
        
        return isInSubdirectory ? '../cart-api.php' : 'cart-api.php';
    }
    
    bindEvents() {
        // Listen for cart updates from other components
        document.addEventListener('cart:itemAdded', (e) => {
            this.handleCartUpdate(e.detail);
        });
        
        document.addEventListener('cart:itemRemoved', (e) => {
            this.handleCartUpdate(e.detail);
        });
        
        document.addEventListener('cart:quantityUpdated', (e) => {
            this.handleCartUpdate(e.detail);
        });
        
        // Listen for page visibility changes to refresh cart
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.loadCartCount();
            }
        });
        
        // Listen for storage changes (cross-tab synchronization)
        window.addEventListener('storage', (e) => {
            if (e.key === 'cart_updated') {
                this.loadCartCount();
            }
        });
        
        // Listen for cart icon clicks
        const cartIcon = document.querySelector('.shopping-cart');
        if (cartIcon) {
            cartIcon.addEventListener('click', (e) => {
                e.preventDefault();
                this.openCartPage();
            });
        }
    }
    
    async loadCartCount() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        
        // OPTIMIZATION: Show loading state immediately
        this.showCartLoadingState();
        
        try {
            // OPTIMIZATION: Use fast cart summary API
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_summary'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Ensure cart count is properly parsed as integer
                this.cartCount = parseInt(data.cart_count) || 0;
                this.cartTotal = parseFloat(data.cart_total) || 0;
                this.updateCartCountDisplay();
            } else {
                throw new Error(data.message || 'Failed to load cart count');
            }
        } catch (error) {
            console.error('Error loading cart count:', error);
            this.cartCount = 0;
            this.cartTotal = 0;
            this.updateCartCountDisplay();
        } finally {
            this.isLoading = false;
            this.hideCartLoadingState();
        }
    }
    
    async loadCartItems() {
        try {
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.cartItems = data.data?.items || [];
                return this.cartItems;
            } else {
                throw new Error(data.message || 'Failed to load cart items');
            }
        } catch (error) {
            console.error('Error loading cart items:', error);
            this.cartItems = [];
            return [];
        }
    }
    
    updateCartCountDisplay() {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            if (this.cartCount > 0) {
                cartCountElement.textContent = this.cartCount;
                cartCountElement.style.display = 'flex';
                
                // Add subtle update animation
                cartCountElement.classList.add('cart-count-updated');
                setTimeout(() => {
                    cartCountElement.classList.remove('cart-count-updated');
                }, 300);
            } else {
                cartCountElement.style.display = 'none';
            }
        }
    }
    
    showCartLoadingState() {
        const cartIcon = document.querySelector('.shopping-cart');
        const cartCountElement = document.querySelector('.cart-count');
        
        if (cartIcon) {
            cartIcon.style.opacity = '0.7';
            cartIcon.style.cursor = 'wait';
        }
        
        if (cartCountElement) {
            cartCountElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            cartCountElement.style.display = 'flex';
        }
    }
    
    hideCartLoadingState() {
        const cartIcon = document.querySelector('.shopping-cart');
        
        if (cartIcon) {
            cartIcon.style.opacity = '1';
            cartIcon.style.cursor = 'pointer';
        }
    }
    
    // Removed loading states for instant cart updates
    
    handleCartUpdate(detail) {
        if (detail && detail.cart_count !== undefined) {
            // Ensure cart count is properly parsed as integer
            this.cartCount = parseInt(detail.cart_count) || 0;
            this.updateCartCountDisplay();
        } else {
            // Refresh cart count if no specific count provided
            this.loadCartCount();
        }
        
        // Trigger cross-tab synchronization
        localStorage.setItem('cart_updated', Date.now().toString());
    }
    
    async addToCart(productId, quantity = 1, color = '', size = '', price = null, variantName = '', variantStock = null, variantImage = '') {
        // Prevent duplicate calls
        if (this.isLoading) {
            return false;
        }
        
        this.isLoading = true;
        
        // Show instant visual feedback
        this.cartCount += quantity;
        this.updateCartCountDisplay();
        this.showNotification('Product added to cart!', 'success');
        
        try {
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    color: color,
                    size: size,
                    price: price || '',
                    variant_name: variantName,
                    variant_stock: variantStock || '',
                    variant_image: variantImage,
                    return_url: window.location.href
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Update with actual cart count from server
                this.cartCount = parseInt(data.cart_count) || 0;
                this.updateCartCountDisplay();
                
                // Trigger custom event
                this.triggerCartEvent('itemAdded', {
                    productId,
                    cartCount: this.cartCount,
                    message: data.message
                });
                
                return true;
            } else {
                // Revert optimistic update on failure
                this.cartCount = Math.max(0, this.cartCount - quantity);
                this.updateCartCountDisplay();
                throw new Error(data.message || 'Failed to add product to cart');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            // Revert optimistic update on error
            this.cartCount = Math.max(0, this.cartCount - quantity);
            this.updateCartCountDisplay();
            this.showNotification('Error adding product to cart', 'error');
            return false;
        } finally {
            this.isLoading = false;
        }
    }
    
    async removeFromCart(productId) {
        // Prevent duplicate calls
        if (this.isLoading) {
            return false;
        }
        
        this.isLoading = true;
        
        // Show instant visual feedback
        this.cartCount = Math.max(0, this.cartCount - 1);
        this.updateCartCountDisplay();
        this.showNotification('Item removed from cart', 'info');
        
        try {
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'remove_item',
                    product_id: productId
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Update with actual cart count from server
                this.cartCount = parseInt(data.cart_count) || 0;
                this.updateCartCountDisplay();
                
                // Trigger custom event
                this.triggerCartEvent('itemRemoved', {
                    productId,
                    cartCount: this.cartCount,
                    message: data.message
                });
                
                return true;
            } else {
                // Revert optimistic update on failure
                this.cartCount += 1;
                this.updateCartCountDisplay();
                throw new Error(data.message || 'Failed to remove item from cart');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            // Revert optimistic update on error
            this.cartCount += 1;
            this.updateCartCountDisplay();
            this.showNotification('Error removing item from cart', 'error');
            return false;
        } finally {
            this.isLoading = false;
        }
    }
    
    async updateQuantity(productId, quantity) {
        // Prevent duplicate calls
        if (this.isLoading) {
            return false;
        }
        
        this.isLoading = true;
        
        try {
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'update_quantity',
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Update with actual cart count from server
                this.cartCount = parseInt(data.cart_count) || 0;
                this.updateCartCountDisplay();
                this.showNotification('Quantity updated', 'success');
                
                // Trigger custom event
                this.triggerCartEvent('quantityUpdated', {
                    productId,
                    quantity,
                    cartCount: this.cartCount,
                    message: data.message
                });
                
                return true;
            } else {
                throw new Error(data.message || 'Failed to update quantity');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            this.showNotification('Error updating quantity', 'error');
            return false;
        } finally {
            this.isLoading = false;
        }
    }
    
    async clearCart() {
        // Prevent duplicate calls
        if (this.isLoading) {
            return false;
        }
        
        this.isLoading = true;
        
        // Show instant visual feedback
        this.cartCount = 0;
        this.cartItems = [];
        this.updateCartCountDisplay();
        this.showNotification('Cart cleared', 'info');
        
        try {
            const response = await fetch(this.apiPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear_cart'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Update with actual cart count from server
                this.cartCount = parseInt(data.cart_count) || 0;
                this.updateCartCountDisplay();
                
                // Trigger custom event
                this.triggerCartEvent('cartCleared', {
                    cartCount: this.cartCount,
                    message: data.message
                });
                
                return true;
            } else {
                throw new Error(data.message || 'Failed to clear cart');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            this.showNotification('Error clearing cart', 'error');
            return false;
        } finally {
            this.isLoading = false;
        }
    }
    
    showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'info' ? '#17a2b8' : '#6c757d',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            zIndex: '10000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            maxWidth: '300px',
            fontSize: '14px',
            fontWeight: '500',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        });
        
        // Style the notification content
        const content = notification.querySelector('.notification-content');
        Object.assign(content.style, {
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
        });
        
        document.body.appendChild(notification);
        
        // Show notification
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Hide notification
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    triggerCartEvent(eventType, detail) {
        const event = new CustomEvent(`cart:${eventType}`, {
            detail: detail
        });
        document.dispatchEvent(event);
    }
    
    setupStorageListener() {
        // Listen for cart updates from other tabs
        window.addEventListener('storage', (e) => {
            if (e.key === 'cart_updated') {
                this.loadCartCount();
            }
        });
    }
    
    initializeCartDropdown() {
        // Create cart dropdown if it doesn't exist
        const cartIcon = document.querySelector('.shopping-cart');
        if (cartIcon && !document.getElementById('cart-dropdown')) {
            const dropdown = document.createElement('div');
            dropdown.id = 'cart-dropdown';
            dropdown.className = 'cart-dropdown';
            dropdown.innerHTML = `
                <div class="cart-dropdown-header">
                    <h3><i class="fas fa-shopping-cart"></i> My Cart</h3>
                    <button onclick="window.cartNotificationManager.openCartPage()" class="view-cart-btn">View Cart</button>
                </div>
                <div class="cart-dropdown-content" id="cart-dropdown-content">
                    <!-- Cart items will be loaded here -->
                </div>
                <div class="cart-dropdown-empty" id="cart-dropdown-empty" style="display: none;">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <small>Start adding items you love!</small>
                </div>
            `;
            
            cartIcon.appendChild(dropdown);
        }
    }
    
    async loadCartDropdown() {
        const content = document.getElementById('cart-dropdown-content');
        const empty = document.getElementById('cart-dropdown-empty');
        
        if (!content || !empty) return;
        
        const items = await this.loadCartItems();
        
        if (items.length === 0) {
            content.style.display = 'none';
            empty.style.display = 'block';
        } else {
            content.style.display = 'block';
            empty.style.display = 'none';
            
            // Show only first 3 items in dropdown
            const displayItems = items.slice(0, 3);
            content.innerHTML = displayItems.map(item => `
                <div class="cart-dropdown-item">
                    <img src="${item.variant_image || item.image || 'https://via.placeholder.com/60x60?text=No+Image'}" 
                         alt="${item.name}" 
                         onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                    <div class="cart-dropdown-item-info">
                        <div class="cart-dropdown-item-name">${item.name}</div>
                        <div class="cart-dropdown-item-price">$${item.price}</div>
                        <div class="cart-dropdown-item-quantity">Qty: ${item.quantity}</div>
                        ${item.color ? `<div class="cart-dropdown-item-color">Color: ${item.color}</div>` : ''}
                        ${item.size ? `<div class="cart-dropdown-item-size">Size: ${item.size}</div>` : ''}
                    </div>
                    <div class="cart-dropdown-item-actions">
                        <button class="btn-update-qty" onclick="window.cartNotificationManager.updateQuantity('${item.product_id}', ${item.quantity + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn-remove" onclick="window.cartNotificationManager.removeFromCart('${item.product_id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            
            // Show "View All" button if there are more than 3 items
            if (items.length > 3) {
                content.innerHTML += `
                    <div class="cart-dropdown-item" style="justify-content: center; border-top: 2px solid #eee;">
                        <button onclick="window.cartNotificationManager.openCartPage()" 
                                style="background: #f8f9fa; border: 1px solid #ddd; padding: 8px 20px; border-radius: 6px; cursor: pointer; color: #666;">
                            View All ${items.length} Items
                        </button>
                    </div>
                `;
            }
        }
    }
    
    toggleCartDropdown() {
        const dropdown = document.getElementById('cart-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                this.loadCartDropdown();
            }
        }
    }
    
    openCartPage() {
        // OPTIMIZATION: Instant redirect without waiting for API calls
        const currentPath = window.location.pathname;
        const isInSubdirectory = currentPath.includes('/womenF/') || currentPath.includes('/kidsfolder/') || 
                               currentPath.includes('/beautyfolder/') || currentPath.includes('/menfolder/') || 
                               currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                               currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                               currentPath.includes('/bagsfolder/');
        
        // Show immediate visual feedback
        this.showCartLoadingState();
        
        // Instant redirect - no API calls needed
        setTimeout(() => {
            if (isInSubdirectory) {
                window.location.href = '../cart-unified.php';
            } else {
                window.location.href = 'cart-unified.php';
            }
        }, 50); // Minimal delay for visual feedback
    }
    
    // Public methods for external use
    getCartCount() {
        return this.cartCount;
    }
    
    getCartItems() {
        return this.cartItems;
    }
    
    refreshCart() {
        this.loadCartCount();
    }
}

// Initialize cart notification manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (!window.cartNotificationManager) {
        console.log('Initializing cart notification manager...');
        window.cartNotificationManager = new CartNotificationManager();
        
        // Make functions available globally
        window.addToCart = (productId, quantity, color, size, price, variantName, variantStock, variantImage) => {
            return window.cartNotificationManager.addToCart(productId, quantity, color, size, price, variantName, variantStock, variantImage);
        };
        
        window.removeFromCart = (productId) => {
            return window.cartNotificationManager.removeFromCart(productId);
        };
        
        window.updateCartQuantity = (productId, quantity) => {
            return window.cartNotificationManager.updateQuantity(productId, quantity);
        };
        
        window.clearCart = () => {
            return window.cartNotificationManager.clearCart();
        };
        
        window.toggleCartDropdown = () => {
            return window.cartNotificationManager.toggleCartDropdown();
        };
        
        window.openCartPage = () => {
            return window.cartNotificationManager.openCartPage();
        };
        
        window.refreshCart = () => {
            return window.cartNotificationManager.refreshCart();
        };
        
        console.log('Cart notification manager initialized successfully');
    } else {
        console.log('Cart notification manager already initialized, skipping...');
    }
});
