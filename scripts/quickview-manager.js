/**
 * Unified Quickview Manager
 * Handles dynamic product loading and interactions across all categories
 */

class QuickviewManager {
    constructor() {
        this.currentProduct = null;
        this.selectedColor = null;
        this.selectedSize = null;
        this.isLoading = false;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupQuickviewElements();
    }

    bindEvents() {
        // Close quickview events
        document.addEventListener('click', (e) => {
            if (e.target.id === 'close-quick-view' || e.target.closest('#close-quick-view')) {
                this.closeQuickview();
            }
            if (e.target.id === 'quick-view-overlay') {
                this.closeQuickview();
            }
        });

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isQuickviewOpen()) {
                this.closeQuickview();
            }
        });

        // Add to cart and wishlist events
        document.addEventListener('click', (e) => {
            if (e.target.id === 'add-to-bag-quick' || e.target.closest('#add-to-bag-quick')) {
                this.addToCart();
            }
            if (e.target.id === 'add-to-wishlist-quick' || e.target.closest('#add-to-wishlist-quick')) {
                this.addToWishlist();
            }
        });
    }

    setupQuickviewElements() {
        // Ensure quickview elements exist
        this.sidebar = document.getElementById('quick-view-sidebar');
        this.overlay = document.getElementById('quick-view-overlay');
        
        if (!this.sidebar || !this.overlay) {
            console.warn('Quickview elements not found');
            return;
        }

        // Initialize color and size selection
        this.setupColorSelection();
        this.setupSizeSelection();
    }

    setupColorSelection() {
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (colorSelection) {
            colorSelection.addEventListener('click', (e) => {
                if (e.target.classList.contains('quick-view-color-circle')) {
                    this.selectColor(e.target.getAttribute('data-color'));
                }
            });
        }
    }

    setupSizeSelection() {
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            sizeSelection.addEventListener('click', (e) => {
                if (e.target.classList.contains('quick-view-size-btn') && !e.target.classList.contains('sold-out')) {
                    this.selectSize(e.target.textContent);
                }
            });
        }
    }

    async openQuickview(productId) {
        
        if (this.isLoading) {
            return;
        }
        
        this.isLoading = true;
        this.showLoadingState();

        try {
            // Fetch product data from API
            // Determine the correct API path based on current page location
            let apiUrl;
            if (window.location.pathname.includes('/womenF/')) {
                apiUrl = `../get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/menfolder/')) {
                apiUrl = `../get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/accessories/')) {
                apiUrl = `../get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/bagsfolder/')) {
                apiUrl = `../get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/perfumes/')) {
                apiUrl = `../get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/kidsfolder/')) {
                apiUrl = `get-product-details-simple.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/beautyfolder/')) {
                apiUrl = `get-product-details.php?product_id=${productId}`;
            } else if (window.location.pathname.includes('/homedecor/')) {
                apiUrl = `get-product-details.php?product_id=${productId}`;
            } else {
                apiUrl = `get-product-details.php?product_id=${productId}`;
            }
            
            const response = await fetch(apiUrl);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (data.success && data.product) {
                this.currentProduct = data.product;
                this.populateQuickview(data.product);
                this.showQuickview();
            } else {
                throw new Error(data.message || 'Failed to load product');
            }
        } catch (error) {
            console.error('QuickviewManager: Error loading product:', error);
            console.error('QuickviewManager: Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            let errorMessage = 'Failed to load product';
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                errorMessage = 'Network error - please check your connection';
            } else if (error.message.includes('HTTP error')) {
                errorMessage = `Server error: ${error.message}`;
            } else {
                errorMessage = error.message;
            }
            
            this.showErrorState(errorMessage);
        } finally {
            this.isLoading = false;
        }
    }

    populateQuickview(product) {
        
        // Set product title and price
        this.setElementText('quick-view-title', product.name);
        this.setElementText('quick-view-price', this.formatPrice(product.price, product.salePrice));
        this.setElementText('quick-view-description', product.description);

        // Populate images
        this.populateImages(product.images);

        // Populate colors
        this.populateColors(product.colors);

        // Populate sizes
        this.populateSizes(product.sizes);

        // Set initial selections
        this.selectedColor = product.defaultColor || (product.colors[0]?.value);
        this.selectedSize = product.defaultSize || (product.sizes[0]?.name);

        // Update color and size displays
        this.updateColorSelection();
        this.updateSizeSelection();

        // Update availability
        this.updateAvailability();
    }

    populateImages(images) {
        const mainImageContainer = document.getElementById('quick-view-main-image');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');

        if (!mainImageContainer || !thumbnailsContainer) return;

        // Helper function to check if file is video
        const isVideoFile = (filePath) => {
            if (!filePath) return false;
            const videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
            const extension = filePath.split('.').pop().toLowerCase();
            return videoExtensions.includes(extension);
        };

        // Set main image/video
        if (images.length > 0) {
            const firstImage = images[0];
            mainImageContainer.innerHTML = '';
            
            if (isVideoFile(firstImage.src)) {
                const video = document.createElement('video');
                video.src = firstImage.src;
                video.alt = firstImage.alt;
                video.controls = true;
                video.muted = true;
                video.loop = true;
                video.style.display = 'block';
                video.style.maxWidth = '100%';
                video.style.height = 'auto';
                mainImageContainer.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = firstImage.src;
                img.alt = firstImage.alt;
                img.style.display = 'block';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                mainImageContainer.appendChild(img);
            }
        }

        // Clear and populate thumbnails
        thumbnailsContainer.innerHTML = '';

        images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
            
            if (isVideoFile(image.src)) {
                const video = document.createElement('video');
                video.src = image.src;
                video.alt = image.alt;
                video.muted = true;
                video.loop = true;
                video.setAttribute('data-index', index);
                video.style.maxWidth = '100%';
                video.style.height = 'auto';
                thumbnail.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.alt;
                img.setAttribute('data-index', index);
                thumbnail.appendChild(img);
            }
            
            thumbnail.addEventListener('click', () => this.selectImage(index));
            thumbnailsContainer.appendChild(thumbnail);
        });
    }

    populateColors(colors) {
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (!colorSelection) return;

        colorSelection.innerHTML = '';

        colors.forEach((color, index) => {
            const colorCircle = document.createElement('div');
            colorCircle.className = `quick-view-color-circle ${index === 0 ? 'active' : ''}`;
            colorCircle.style.backgroundColor = color.hex;
            colorCircle.setAttribute('data-color', color.value);
            colorCircle.title = color.name;
            
            colorSelection.appendChild(colorCircle);
        });
    }

    populateSizes(sizes) {
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (!sizeSelection) return;

        sizeSelection.innerHTML = '';

        sizes.forEach((size, index) => {
            const sizeBtn = document.createElement('button');
            sizeBtn.className = `quick-view-size-btn ${index === 0 ? 'active' : ''}`;
            sizeBtn.textContent = size.name;
            
            if (!size.available || size.stock <= 0) {
                sizeBtn.classList.add('sold-out');
                sizeBtn.textContent += ' (Out of Stock)';
            }
            
            sizeSelection.appendChild(sizeBtn);
        });
    }

    selectColor(colorValue) {
        this.selectedColor = colorValue;
        this.updateColorSelection();
        this.updateImagesForColor(colorValue);
        this.updateAvailability();
    }

    selectSize(sizeName) {
        this.selectedSize = sizeName;
        this.updateSizeSelection();
        this.updateAvailability();
    }

    selectImage(imageIndex) {
        const mainImageContainer = document.getElementById('quick-view-main-image');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        
        if (mainImageContainer && this.currentProduct.images[imageIndex]) {
            const selectedImage = this.currentProduct.images[imageIndex];
            
            // Helper function to check if file is video
            const isVideoFile = (filePath) => {
                if (!filePath) return false;
                const videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
                const extension = filePath.split('.').pop().toLowerCase();
                return videoExtensions.includes(extension);
            };
            
            // Clear container and add new media
            mainImageContainer.innerHTML = '';
            
            if (isVideoFile(selectedImage.src)) {
                const video = document.createElement('video');
                video.src = selectedImage.src;
                video.alt = selectedImage.alt;
                video.controls = true;
                video.muted = true;
                video.loop = true;
                video.style.display = 'block';
                video.style.maxWidth = '100%';
                video.style.height = 'auto';
                mainImageContainer.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = selectedImage.src;
                img.alt = selectedImage.alt;
                img.style.display = 'block';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                mainImageContainer.appendChild(img);
            }
        }

        // Update active thumbnail
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === imageIndex);
        });
    }

    updateColorSelection() {
        const colorCircles = document.querySelectorAll('.quick-view-color-circle');
        colorCircles.forEach(circle => {
            circle.classList.toggle('active', circle.getAttribute('data-color') === this.selectedColor);
        });
    }

    updateSizeSelection() {
        const sizeButtons = document.querySelectorAll('.quick-view-size-btn');
        sizeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.textContent.replace(' (Out of Stock)', '') === this.selectedSize);
        });
    }

    updateImagesForColor(colorValue) {
        if (!this.currentProduct) return;

        const selectedColor = this.currentProduct.colors.find(c => c.value === colorValue);
        if (selectedColor && selectedColor.images.length > 0) {
            this.populateImages(selectedColor.images);
        }
    }

    updateAvailability() {
        if (!this.currentProduct || !this.selectedColor || !this.selectedSize) return;

        const selectedColor = this.currentProduct.colors.find(c => c.value === this.selectedColor);
        const selectedSize = selectedColor?.sizes.find(s => s.name === this.selectedSize);

        const availabilityEl = document.getElementById('quick-view-availability');
        if (availabilityEl) {
            if (selectedSize && selectedSize.available && selectedSize.stock > 0) {
                availabilityEl.textContent = `In Stock (${selectedSize.stock} available)`;
                availabilityEl.className = 'quick-view-stock in-stock';
            } else {
                availabilityEl.textContent = 'Out of Stock';
                availabilityEl.className = 'quick-view-stock out-of-stock';
            }
        }
    }

    async addToCart() {
        if (!this.currentProduct || !this.selectedColor || !this.selectedSize) {
            alert('Please select both color and size');
            return;
        }

        try {
            const response = await fetch('../cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${this.currentProduct.id}&quantity=1&color=${this.selectedColor}&size=${this.selectedSize}`
            });

            const data = await response.json();
            
            if (data.success) {
                alert('Product added to cart successfully!');
                this.updateCartCount(data.cart_count);
                this.closeQuickview();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            alert('Error adding product to cart');
        }
    }

    async addToWishlist() {
        if (!this.currentProduct) return;

        try {
            const productId = this.currentProduct._id;
            if (!productId) {
                throw new Error('No product ID available');
            }
            
            if (window.wishlistManager) {
                window.wishlistManager.toggleWishlist(productId, document.getElementById('add-to-wishlist-quick'));
            } else {
                // Fallback - simple localStorage approach
                const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                const existingIndex = wishlist.findIndex(item => item.id === productId);
                
                if (existingIndex > -1) {
                    wishlist.splice(existingIndex, 1);
                    alert('Removed from wishlist!');
                } else {
                    const productDetails = {
                        id: productId,
                        name: this.currentProduct.name || 'Product',
                        price: this.currentProduct.price || '0',
                        image: this.currentProduct.front_image || this.currentProduct.images?.[0] || '',
                        category: this.currentProduct.category || 'General',
                        addedAt: new Date().toISOString()
                    };
                    wishlist.push(productDetails);
                    alert('Added to wishlist!');
                }
                
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                
                // Update wishlist count
                if (typeof updateWishlistCount === 'function') {
                    updateWishlistCount();
                }
            }
        } catch (error) {
            console.error('Error adding to wishlist:', error);
            alert('Error adding product to wishlist');
        }
    }

    updateCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    showQuickview() {
        
        if (this.sidebar && this.overlay) {
            this.sidebar.classList.add('active');
            this.overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            console.error('QuickviewManager: Missing sidebar or overlay elements');
        }
    }

    closeQuickview() {
        if (this.sidebar && this.overlay) {
            this.sidebar.classList.remove('active');
            this.overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        this.currentProduct = null;
        this.selectedColor = null;
        this.selectedSize = null;
    }

    isQuickviewOpen() {
        return this.sidebar && this.sidebar.classList.contains('active');
    }

    showLoadingState() {
        const content = document.querySelector('.quick-view-content');
        if (content) {
            content.innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading product details...</p>
                </div>
            `;
        }
    }

    showErrorState(message) {
        const content = document.querySelector('.quick-view-content');
        if (content) {
            content.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error loading product</p>
                    <p>${message}</p>
                    <button onclick="this.closeQuickview()">Close</button>
                </div>
            `;
        }
    }

    setElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    formatPrice(price, salePrice = null) {
        if (salePrice && salePrice < price) {
            return `<span class="original-price">$${price.toFixed(2)}</span> <span class="sale-price">$${salePrice.toFixed(2)}</span>`;
        }
        return `$${price.toFixed(2)}`;
    }
}

// Initialize quickview manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.quickviewManager = new QuickviewManager();
    } catch (error) {
        console.error('Quickview Manager: Failed to initialize:', error);
    }
});

// Global function for opening quickview (for backward compatibility)
window.openQuickView = function(productId) {
    if (window.quickviewManager) {
        window.quickviewManager.openQuickview(productId);
    }
};
