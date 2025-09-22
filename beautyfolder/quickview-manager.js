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

        // Update Add to Bag button data attributes and sold out state
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        console.log('Beauty Quick View: Button found:', addToBagBtn);
        console.log('Beauty Quick View: Product stock:', product.stock);
        
        if (addToBagBtn) {
            addToBagBtn.setAttribute('data-product-id', product.id);
            addToBagBtn.setAttribute('data-product-name', product.name);
            addToBagBtn.setAttribute('data-product-price', product.price);
            addToBagBtn.setAttribute('data-product-color', product.color || '');
            addToBagBtn.setAttribute('data-product-stock', product.stock || 0);
            
            // Check if product is sold out and update button accordingly
            const stock = parseInt(product.stock) || 0;
            const isSoldOut = stock <= 0;
            
            console.log('Beauty Quick View: Stock:', stock, 'Is sold out:', isSoldOut);
            
            if (isSoldOut) {
                addToBagBtn.classList.add('sold-out-btn');
                addToBagBtn.disabled = true;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i>Sold Out';
                console.log('Beauty Quick View: Updated to sold out state');
            } else {
                addToBagBtn.classList.remove('sold-out-btn');
                addToBagBtn.disabled = false;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i>Add to Bag';
                console.log('Beauty Quick View: Updated to normal state');
            }
            
            console.log('Beauty Quick View: Final button classes:', addToBagBtn.className);
            console.log('Beauty Quick View: Final button innerHTML:', addToBagBtn.innerHTML);
        } else {
            console.error('Beauty Quick View: Add to bag button not found!');
        }
    }

    populateImages(images) {
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');

        if (!mainImage || !thumbnailsContainer) return;

        // Set main image
        if (images.length > 0) {
            mainImage.src = images[0].src;
            mainImage.alt = images[0].alt;
            mainImage.style.display = 'block';
        }

        // Clear and populate thumbnails
        thumbnailsContainer.innerHTML = '';

        images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
            
            const img = document.createElement('img');
            img.src = image.src;
            img.alt = image.alt;
            img.setAttribute('data-index', index);
            
            thumbnail.appendChild(img);
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
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        
        if (mainImage && this.currentProduct.images[imageIndex]) {
            mainImage.src = this.currentProduct.images[imageIndex].src;
            mainImage.alt = this.currentProduct.images[imageIndex].alt;
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

        // Check if product is sold out using simplified stock logic
        const stock = parseInt(this.currentProduct.stock) || 0;
        const isSoldOut = stock <= 0;

        if (isSoldOut) {
            alert('This product is currently sold out and cannot be added to cart.');
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
            // Implement wishlist functionality
            alert('Product added to wishlist!');
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
