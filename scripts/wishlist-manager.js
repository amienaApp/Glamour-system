/**
 * Simple Client-Side Wishlist Manager
 * Uses localStorage to store user's favorite products
 */
class WishlistManager {
    constructor() {
        this.storageKey = 'wishlist';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateWishlistCount();
    }
    
    bindEvents() {
        // Bind heart button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.heart-button') || e.target.closest('.wishlist-btn')) {
                e.preventDefault();
                const productId = this.getProductId(e.target);
                if (productId) {
                    this.toggleWishlist(productId, e.target);
                }
            }
        });
        
        // Bind quick view wishlist buttons
        document.addEventListener('click', (e) => {
            if (e.target.id === 'add-to-wishlist-quick' || e.target.closest('#add-to-wishlist-quick')) {
                e.preventDefault();
                const productId = this.getQuickViewProductId();
                if (productId) {
                    this.toggleQuickViewWishlist(productId, e.target);
                }
            }
        });
    }
    
    getProductId(element) {
        // Try to get product ID from various data attributes
        const productCard = element.closest('.product-card, .wishlist-item');
        if (productCard) {
            return productCard.getAttribute('data-product-id');
        }
        
        // Try to get from button data attribute
        const button = element.closest('button');
        if (button) {
            return button.getAttribute('data-product-id');
        }
        
        return null;
    }
    
    getQuickViewProductId() {
        // Get product ID from quick view modal
        const quickViewTitle = document.getElementById('quick-view-title');
        if (quickViewTitle) {
            return quickViewTitle.getAttribute('data-product-id');
        }
        
        // Try to get from quick view container
        const quickView = document.getElementById('quick-view-sidebar');
        if (quickView) {
            return quickView.getAttribute('data-product-id');
        }
        
        return null;
    }
    
    getQuickViewProductDetails(productId) {
        // Get product details from quickview
        const quickViewTitle = document.getElementById('quick-view-title');
        const quickViewPrice = document.getElementById('quick-view-price');
        const quickViewMainImage = document.getElementById('quick-view-main-image');
        const quickViewMainVideo = document.getElementById('quick-view-main-video');
        
        if (!quickViewTitle) return null;
        
        const name = quickViewTitle.textContent?.trim() || 'Product';
        const price = quickViewPrice?.textContent?.trim() || '$0.00';
        
        // Get the currently displayed image/video in quickview
        let variantImage = '';
        if (quickViewMainImage && quickViewMainImage.style.display !== 'none') {
            variantImage = quickViewMainImage.src;
        } else if (quickViewMainVideo && quickViewMainVideo.style.display !== 'none') {
            variantImage = quickViewMainVideo.src;
        }
        
        // Get selected color from quickview
        let selectedColor = '';
        const activeColorOption = document.querySelector('#quick-view-color-selection .color-option.active');
        if (activeColorOption) {
            selectedColor = activeColorOption.getAttribute('data-color') || '';
        }
        
        return {
            id: productId,
            name: name,
            price: price.replace(/[^0-9.]/g, '') || '0',
            image: variantImage,
            category: 'General',
            selectedColor: selectedColor,
            addedAt: new Date().toISOString()
        };
    }
    
    getProductDetails(productId) {
        // Try to get product details from the page
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) return null;
        
        const name = productCard.querySelector('.product-name, .product-title, h3, h4')?.textContent?.trim() || 'Product';
        const price = productCard.querySelector('.product-price, .price')?.textContent?.trim() || '$0.00';
        const category = productCard.getAttribute('data-category') || 'General';
        
        // Get selected color and variant image
        let selectedColor = '';
        let variantImage = '';
        let variantPrice = price.replace(/[^0-9.]/g, '') || '0';
        
        // Check if there's an active color circle
        const activeColorCircle = productCard.querySelector('.color-circle.active');
        if (activeColorCircle) {
            selectedColor = activeColorCircle.getAttribute('data-color') || '';
            
            // Get the currently active/visible image from the image slider
            const imageSlider = productCard.querySelector('.image-slider');
            if (imageSlider) {
                const activeImage = imageSlider.querySelector('img.active, video.active');
                if (activeImage) {
                    variantImage = activeImage.src;
                } else {
                    // Fallback: find image with matching data-color
                    const colorImage = imageSlider.querySelector(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                    if (colorImage) {
                        variantImage = colorImage.src;
                    }
                }
            }
            
            // Get variant price if available
            const variantsData = productCard.getAttribute('data-product-variants');
            if (variantsData && variantsData !== '[]' && variantsData !== 'null') {
                try {
                    const variants = JSON.parse(variantsData);
                    if (Array.isArray(variants)) {
                        const selectedVariant = variants.find(variant => variant.color === selectedColor);
                        if (selectedVariant && selectedVariant.price) {
                            variantPrice = selectedVariant.price;
                        }
                    }
                } catch (e) {
                    console.log('Could not parse variants data:', e);
                }
            }
        }
        
        // Fallback to main product image if no variant image found
        if (!variantImage) {
            variantImage = productCard.querySelector('.product-image img, img')?.src || '';
        }
        
        return {
            id: productId,
            name: name,
            price: variantPrice,
            image: variantImage,
            category: category,
            selectedColor: selectedColor,
            addedAt: new Date().toISOString()
        };
    }
    
    toggleWishlist(productId, button) {
        const wishlist = this.getWishlist();
        const existingIndex = wishlist.findIndex(item => item.id === productId);
        
        if (existingIndex > -1) {
            // Remove from wishlist
            wishlist.splice(existingIndex, 1);
            this.updateButtonState(button, false);
            this.showNotification('Removed from wishlist', 'info');
        } else {
            // Add to wishlist
            const productDetails = this.getProductDetails(productId);
            if (productDetails) {
                wishlist.push(productDetails);
                this.updateButtonState(button, true);
                const colorInfo = productDetails.selectedColor ? ` (${productDetails.selectedColor})` : '';
                this.showNotification(`Added to wishlist${colorInfo}`, 'success');
            } else {
                this.showNotification('Could not add to wishlist', 'error');
                return;
            }
        }
        
        this.saveWishlist(wishlist);
        this.updateWishlistCount();
        
        // Update dropdown if it's open
        if (typeof loadWishlistDropdown === 'function') {
            loadWishlistDropdown();
        }
    }
    
    toggleQuickViewWishlist(productId, button) {
        const wishlist = this.getWishlist();
        const existingIndex = wishlist.findIndex(item => item.id === productId);
        
        if (existingIndex > -1) {
            // Remove from wishlist
            wishlist.splice(existingIndex, 1);
            this.updateButtonState(button, false);
            this.showNotification('Removed from wishlist', 'info');
        } else {
            // Add to wishlist with quickview details
            const productDetails = this.getQuickViewProductDetails(productId);
            if (productDetails) {
                wishlist.push(productDetails);
                this.updateButtonState(button, true);
                const colorInfo = productDetails.selectedColor ? ` (${productDetails.selectedColor})` : '';
                this.showNotification(`Added to wishlist${colorInfo}`, 'success');
            } else {
                this.showNotification('Could not add to wishlist', 'error');
                return;
            }
        }
        
        this.saveWishlist(wishlist);
        this.updateWishlistCount();
        
        // Update dropdown if it's open
        if (typeof loadWishlistDropdown === 'function') {
            loadWishlistDropdown();
        }
    }
    
    getWishlist() {
        try {
            return JSON.parse(localStorage.getItem(this.storageKey) || '[]');
        } catch (error) {
            console.error('Error loading wishlist:', error);
            return [];
        }
    }
    
    saveWishlist(wishlist) {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(wishlist));
        } catch (error) {
            console.error('Error saving wishlist:', error);
        }
    }
    
    isInWishlist(productId) {
        const wishlist = this.getWishlist();
        return wishlist.some(item => item.id === productId);
    }
    
    updateWishlistCount() {
        const wishlist = this.getWishlist();
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
    
    updateButtonState(button, isInWishlist) {
        const icon = button.querySelector('i');
        if (!icon) return;
        
        if (isInWishlist) {
            button.classList.add('active');
            icon.classList.remove('far');
            icon.classList.add('fas');
            if (button.textContent.includes('Add')) {
                button.textContent = button.textContent.replace('Add', 'Remove');
            }
        } else {
            button.classList.remove('active');
            icon.classList.remove('fas');
            icon.classList.add('far');
            if (button.textContent.includes('Remove')) {
                button.textContent = button.textContent.replace('Remove', 'Add');
            }
        }
    }
    
    showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `wishlist-notification ${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
            zIndex: '1000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            fontSize: '14px',
            fontWeight: '500'
        });
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
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
    
    // Public method to open wishlist page
    openWishlist() {
        // Check if we're in a subfolder and adjust path accordingly
        const currentPath = window.location.pathname;
        const isInSubfolder = currentPath.includes('/kidsfolder/') || currentPath.includes('/beautyfolder/') || 
                             currentPath.includes('/womenF/') || currentPath.includes('/menfolder/') || 
                             currentPath.includes('/perfumes/') || currentPath.includes('/homedecor/') ||
                             currentPath.includes('/shoess/') || currentPath.includes('/accessories/') ||
                             currentPath.includes('/bagsfolder/');
        
        if (isInSubfolder) {
            window.location.href = '../wishlist.php';
        } else {
            window.location.href = 'wishlist.php';
        }
    }
    
    // Initialize button states based on current wishlist
    initializeButtonStates() {
        const wishlist = this.getWishlist();
        const productIds = wishlist.map(item => item.id);
        
        // Update all heart buttons
        document.querySelectorAll('.heart-button, .wishlist-btn').forEach(button => {
            const productId = this.getProductId(button);
            if (productId) {
                this.updateButtonState(button, productIds.includes(productId));
            }
        });
    }
}

// Initialize wishlist manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.wishlistManager = new WishlistManager();
    
    // Initialize button states after a short delay to ensure all elements are loaded
    setTimeout(() => {
        window.wishlistManager.initializeButtonStates();
    }, 500);
    
    // Make openWishlist available globally
    window.openWishlist = () => window.wishlistManager.openWishlist();
});

// CSS for wishlist buttons
const wishlistStyles = `
<style>
.heart-button.active,
.wishlist-btn.active {
    background: #e74c3c !important;
    color: white !important;
}

.heart-button.active i,
.wishlist-btn.active i {
    color: white !important;
}

.wishlist-notification {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', wishlistStyles);