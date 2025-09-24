/**
 * Quick View Sidebar System
 * Handles dynamic product loading and sidebar display
 */

console.log('Homedecor quick view script loading...');

class QuickViewSidebar {
    constructor() {
        this.sidebar = document.getElementById('quick-view-sidebar');
        this.overlay = document.getElementById('quick-view-overlay');
        this.currentProduct = null;
        this.currentImageIndex = 0;
        this.selectedColor = '';
        this.selectedSize = '';
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupQuickViewButtons();
    }
    
    setupEventListeners() {
        // Close sidebar when clicking overlay
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.close());
        }
        
        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        });
        
        // Close button
        const closeBtn = document.getElementById('close-quick-view');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
        
        // Add to bag button
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        if (addToBagBtn) {
            addToBagBtn.addEventListener('click', () => addToCartFromCard(addToBagBtn));
        }
        
        // Wishlist button
        const wishlistBtn = document.getElementById('add-to-wishlist-quick');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', () => this.addToWishlist());
        }
    }
    
    setupQuickViewButtons() {
        // Find all quick view buttons and add event listeners
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-view')) {
                const productId = e.target.getAttribute('data-product-id');
                if (productId) {
                    this.open(productId);
                }
            }
        });
    }
    
    async open(productId) {
        try {
            this.show();
            
            const productData = await this.fetchProductDetails(productId);
            if (productData) {
                this.currentProduct = productData;
                this.populateSidebar(productData);
            }
        } catch (error) {
            console.error('Error opening quick view:', error);
            this.showError('Failed to load product details');
        }
    }
    
    async fetchProductDetails(productId) {
        const response = await fetch(`get-product-details.php?product_id=${encodeURIComponent(productId)}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        return data;
    }
    
    populateSidebar(productData) {
        // Set basic product information
        document.getElementById('quick-view-title').textContent = productData.name;
        document.getElementById('quick-view-product-name').textContent = productData.name;
        document.getElementById('quick-view-price').textContent = `$${parseFloat(productData.price).toLocaleString()}`;
        document.getElementById('quick-view-description').innerHTML = `<p>${productData.description || 'No description available.'}</p>`;
        
        // Initialize wishlist button state
        this.initializeWishlistButton(productData.id || productData._id);
        
        // Update Add to Bag button data attributes and sold out state
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        console.log('Quick View: Button found:', addToBagBtn);
        console.log('Quick View: Product stock:', productData.stock);
        
        if (addToBagBtn) {
            addToBagBtn.setAttribute('data-product-id', productData.id);
            addToBagBtn.setAttribute('data-product-name', productData.name);
            addToBagBtn.setAttribute('data-product-price', productData.price);
            addToBagBtn.setAttribute('data-product-color', productData.color || '');
            addToBagBtn.setAttribute('data-product-stock', productData.stock || 0);
            
            // Check if product is sold out and update button accordingly
            const stock = parseInt(productData.stock) || 0;
            const isSoldOut = stock <= 0;
            
            console.log('Quick View: Stock:', stock, 'Is sold out:', isSoldOut);
            
            if (isSoldOut) {
                addToBagBtn.classList.add('sold-out-btn');
                addToBagBtn.disabled = true;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i>Sold Out';
                console.log('Quick View: Updated to sold out state');
            } else {
                addToBagBtn.classList.remove('sold-out-btn');
                addToBagBtn.disabled = false;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i>Add to Bag';
                console.log('Quick View: Updated to normal state');
            }
            
            console.log('Quick View: Final button classes:', addToBagBtn.className);
            console.log('Quick View: Final button innerHTML:', addToBagBtn.innerHTML);
        } else {
            console.error('Quick View: Add to bag button not found!');
        }
        
        // Handle images
        this.setupImages(productData.images);
        
        // Handle Home & Living specific details
        this.setupHomeLivingDetails(productData);
        
        // Handle color variants
        this.setupColorVariants(productData);

        // Handle size selection
        this.setupSizeSelection(productData);
        
        // Handle availability
        this.setupAvailability(productData);
    }
    
    setupImages(images) {
        if (!images || images.length === 0) {
            this.showNoImages();
            return;
        }
        
        const mainImage = document.getElementById('quick-view-main-image');
        const mainVideo = document.getElementById('quick-view-main-video');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        
        // Clear existing thumbnails
        thumbnailsContainer.innerHTML = '';
        
        // Set main image/video
        this.displayMainMedia(images[0], mainImage, mainVideo);
        
        // Create thumbnails
        images.forEach((image, index) => {
            const thumbnail = this.createThumbnail(image, index);
            thumbnailsContainer.appendChild(thumbnail);
        });
        
        // Set current image index
        this.currentImageIndex = 0;
    }
    
    displayMainMedia(imageData, mainImage, mainVideo) {
        if (imageData.type === 'video') {
            mainVideo.src = imageData.src;
            mainVideo.style.display = 'block';
            mainImage.style.display = 'none';
        } else {
            mainImage.src = imageData.src;
            mainImage.alt = imageData.alt;
            mainImage.style.display = 'block';
            mainVideo.style.display = 'none';
        }
    }
    
    createThumbnail(imageData, index) {
        const thumbnail = document.createElement('div');
        thumbnail.className = 'thumbnail-item';
        thumbnail.onclick = () => this.switchImage(index);
        
        if (imageData.type === 'video') {
            thumbnail.innerHTML = `
                <video src="${imageData.src}" muted loop>
                    <source src="${imageData.src}" type="video/mp4">
                </video>
            `;
        } else {
            thumbnail.innerHTML = `<img src="${imageData.src}" alt="${imageData.alt}">`;
        }
        
        return thumbnail;
    }
    
    switchImage(index) {
        if (!this.currentProduct || !this.currentProduct.images) return;
        
        this.currentImageIndex = index;
        const imageData = this.currentProduct.images[index];
        
        const mainImage = document.getElementById('quick-view-main-image');
        const mainVideo = document.getElementById('quick-view-main-video');
        
        this.displayMainMedia(imageData, mainImage, mainVideo);
        
        // Update thumbnail selection
        this.updateThumbnailSelection(index);
    }
    
    updateThumbnailSelection(activeIndex) {
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === activeIndex);
        });
    }
    
    setupHomeLivingDetails(productData) {
        const homeLivingDetails = document.getElementById('quick-view-home-living-details');
        
        if (productData.category && productData.category.toLowerCase() === 'home & living') {
            homeLivingDetails.style.display = 'block';
            
            // Set material
            if (productData.material) {
                document.getElementById('quick-view-material').textContent = productData.material;
            }
            
            // Set dimensions
            if (productData.length && productData.width) {
                document.getElementById('quick-view-dimensions').textContent = `${productData.length}cm × ${productData.width}cm`;
            }
            
            // Handle subcategory specific details
            this.setupSubcategoryDetails(productData);
        } else {
            homeLivingDetails.style.display = 'none';
        }
    }
    
    setupSubcategoryDetails(productData) {
        const subcategory = productData.subcategory?.toLowerCase();
        
        // Hide all subcategory detail sections
        document.getElementById('quick-view-bedding-details').style.display = 'none';
        document.getElementById('quick-view-dining-details').style.display = 'none';
        document.getElementById('quick-view-living-details').style.display = 'none';
        
        // Show relevant section based on subcategory
        if (subcategory === 'bedding' && productData.bedding_size) {
            document.getElementById('quick-view-bedding-details').style.display = 'block';
            document.getElementById('quick-view-bedding-size').textContent = productData.bedding_size;
        } else if ((subcategory === 'dinning room' || subcategory === 'dining room')) {
            document.getElementById('quick-view-dining-details').style.display = 'block';
            if (productData.chair_count) {
                document.getElementById('quick-view-chair-count').textContent = productData.chair_count;
            }
            if (productData.table_length && productData.table_width) {
                document.getElementById('quick-view-table-size').textContent = `${productData.table_length}cm × ${productData.table_width}cm`;
            }
        } else if (subcategory === 'living room' && productData.sofa_count) {
            document.getElementById('quick-view-living-details').style.display = 'block';
            document.getElementById('quick-view-sofa-count').textContent = productData.sofa_count;
        }
    }
    
    setupColorVariants(productData) {
        const colorSelection = document.getElementById('quick-view-color-selection');
        colorSelection.innerHTML = '';
        
        // Add main product color
        if (productData.color) {
            const colorCircle = this.createColorCircle(productData.color, true);
            colorSelection.appendChild(colorCircle);
            this.selectedColor = productData.color; // Set initial selected color
        }
        
        // Add color variant colors
        if (productData.color_variants && productData.color_variants.length > 0) {
            productData.color_variants.forEach(variant => {
                if (variant.color) {
                    const colorCircle = this.createColorCircle(variant.color, false);
                    colorSelection.appendChild(colorCircle);
                }
            });
        }
    }
    
    createColorCircle(color, isActive = false) {
        const circle = document.createElement('div');
        circle.className = `quick-view-color-circle ${isActive ? 'active' : ''}`;
        circle.style.backgroundColor = color;
        circle.onclick = () => this.selectColor(color, circle);
        return circle;
    }
    
    selectColor(color, circleElement) {
        // Remove active class from all color circles
        document.querySelectorAll('.quick-view-color-circle').forEach(circle => {
            circle.classList.remove('active');
        });
        
        // Add active class to selected color
        circleElement.classList.add('active');
        
        // Store selected color
        this.selectedColor = color;
        
        // Here you could implement color-specific image switching
        // console.log('Selected color:', color);
    }

    setupSizeSelection(productData) {
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            sizeSelection.innerHTML = '';
            
            // Get sizes from product data or use subcategory-specific sizes
            let sizes = [];
            
            // First, try to get sizes from product data attributes
            const productElement = document.querySelector(`[data-product-id="${productData.id}"]`);
            if (productElement) {
                const productSizesData = productElement.getAttribute('data-product-sizes');
                if (productSizesData) {
                    try {
                        const parsedSizes = JSON.parse(productSizesData);
                        if (Array.isArray(parsedSizes) && parsedSizes.length > 0) {
                            sizes = parsedSizes;
                        }
                    } catch (e) {
                        console.log('Error parsing product sizes:', e);
                    }
                }
            }
            
            // If no sizes from product data, use subcategory-specific sizes
            if (sizes.length === 0) {
                const subcategory = (productData.subcategory || '').toLowerCase();
                const homeDecorSubcategorySizes = {
                    'bedding': ['Single', 'Double', 'Queen', 'King', 'Super King'],
                    'living room': ['Small', 'Medium', 'Large', 'Extra Large', 'Sectional'],
                    'dinning room': ['2 Seater', '4 Seater', '6 Seater', '8 Seater', '10 Seater'],
                    'kitchen': ['Compact', 'Standard', 'Large', 'Commercial'],
                    'artwork': ['8x10', '11x14', '16x20', '18x24', '24x36', '30x40'],
                    'lightinning': ['Small', 'Medium', 'Large', 'Extra Large']
                };
                
                if (homeDecorSubcategorySizes[subcategory]) {
                    sizes = homeDecorSubcategorySizes[subcategory];
                } else {
                    // Final fallback
                    sizes = ['Small', 'Medium', 'Large', 'Extra Large'];
                }
            }
            
            sizes.forEach((size, index) => {
                const sizeBtn = document.createElement('div');
                sizeBtn.className = 'size-option';
                sizeBtn.textContent = size;
                sizeBtn.style.cssText = `
                    padding: 8px 12px;
                    border: 2px solid #ddd;
                    border-radius: 4px;
                    cursor: pointer;
                    margin: 0 5px 5px 0;
                    display: inline-block;
                    transition: all 0.3s ease;
                    font-size: 14px;
                    font-weight: 500;
                `;
                
                // Set default selected size (middle size)
                if (index === Math.floor(sizes.length / 2)) {
                    sizeBtn.style.border = '2px solid #333';
                    sizeBtn.style.backgroundColor = '#333';
                    sizeBtn.style.color = 'white';
                    this.selectedSize = size;
                }
                
                // Add click handler
                sizeBtn.addEventListener('click', () => {
                    // Remove active class from all size options
                    sizeSelection.querySelectorAll('.size-option').forEach(opt => {
                        opt.style.border = '2px solid #ddd';
                        opt.style.backgroundColor = 'transparent';
                        opt.style.color = '#333';
                    });
                    
                    // Add active class to clicked option
                    sizeBtn.style.border = '2px solid #333';
                    sizeBtn.style.backgroundColor = '#333';
                    sizeBtn.style.color = 'white';
                    
                    // Store selected size
                    this.selectedSize = size;
                });
                
                sizeSelection.appendChild(sizeBtn);
            });
        }
    }
    
    setupAvailability(productData) {
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        
        if (!productData.available || productData.stock <= 0) {
            addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Sold Out';
            addToBagBtn.disabled = true;
            addToBagBtn.style.opacity = '0.5';
        } else {
            addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Bag';
            addToBagBtn.disabled = false;
            addToBagBtn.style.opacity = '1';
        }
    }
    
    showError(message) {
        // Show error in the description area
        document.getElementById('quick-view-description').innerHTML = `<p style="color: #e53e3e;">${message}</p>`;
    }
    
    showNoImages() {
        document.getElementById('quick-view-main-image').src = '';
        document.getElementById('quick-view-main-image').alt = 'No image available';
        document.getElementById('quick-view-main-image').style.display = 'block';
        document.getElementById('quick-view-main-video').style.display = 'none';
        
        // Clear thumbnails
        document.getElementById('quick-view-thumbnails').innerHTML = '';
    }
    
    show() {
        if (this.overlay) {
            this.overlay.classList.add('active');
        }
        if (this.sidebar) {
            this.sidebar.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }
    
    close() {
        if (this.overlay) {
            this.overlay.classList.remove('active');
        }
        if (this.sidebar) {
            this.sidebar.classList.remove('active');
        }
        document.body.style.overflow = '';
        
        // Reset state
        this.currentProduct = null;
        this.currentImageIndex = 0;
        this.selectedColor = '';
        this.selectedSize = '';
    }
    
    async addToBag() {
        if (!this.currentProduct) return;
        
        // Check if variants are selected
        if (!this.selectedColor) {
            this.showErrorMessage('Please select a color first');
            return;
        }
        if (!this.selectedSize) {
            this.showErrorMessage('Please select a size first');
            return;
        }

        // Check if product is sold out using simplified stock logic
        const stock = parseInt(this.currentProduct.stock) || 0;
        const isSoldOut = stock <= 0;

        if (isSoldOut) {
            this.showErrorMessage('This product is currently sold out and cannot be added to cart.');
            return;
        }
        
        try {
            // Get quantity from the quick view
            const quantity = 1; // Default to 1 for quick view
            
            // Prepare cart data with selected variants
            const cartData = {
                action: 'add_to_cart',
                product_id: this.currentProduct.id,
                quantity: quantity,
                color: this.selectedColor,
                size: this.selectedSize,
                return_url: window.location.href
            };
            
            // Call the cart API
            const response = await fetch('../cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(cartData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                this.showSuccessMessage(`Product added to cart successfully! (${this.selectedColor}, ${this.selectedSize})`);
                
                // Update cart count if available
                if (result.cart_count !== undefined) {
                    this.updateCartCount(result.cart_count);
                }
                
                // Close the quick view after a short delay
                setTimeout(() => {
                    this.close();
                }, 1500);
            } else {
                // Show error message
                this.showErrorMessage(result.message || 'Failed to add to cart');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showErrorMessage('Failed to add to cart. Please try again.');
        }
    }
    
    showSuccessMessage(message) {
        // Create a success notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification success';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
    
    showErrorMessage(message) {
        // Create an error notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification error';
        notification.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    
    updateCartCount(count) {
        // Update cart count in header if it exists
        const cartCountElements = document.querySelectorAll('.cart-count, .cart-badge, [data-cart-count]');
        cartCountElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline' : 'none';
        });
    }
    
    addToWishlist() {
        if (!this.currentProduct) return;
        
        const productId = this.currentProduct.id || this.currentProduct._id;
        if (!productId) {
            console.error('No product ID available for wishlist');
            return;
        }
        
        // Use the global wishlist manager if available
        if (window.wishlistManager) {
            const wishlistBtn = document.getElementById('add-to-wishlist-quick');
            window.wishlistManager.toggleQuickViewWishlist(productId, wishlistBtn);
        } else {
            // Fallback - simple localStorage approach
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            const existingIndex = wishlist.findIndex(item => item.id === productId);
            
            if (existingIndex > -1) {
                // Remove from wishlist
                wishlist.splice(existingIndex, 1);
                this.updateWishlistButtonState(false);
                this.showNotification('Removed from wishlist!', 'info');
            } else {
                // Add to wishlist
                const productDetails = {
                    id: productId,
                    name: this.currentProduct.name || 'Product',
                    price: this.currentProduct.price || '0',
                    image: this.currentProduct.front_image || this.currentProduct.images?.[0] || '',
                    category: this.currentProduct.category || 'General',
                    addedAt: new Date().toISOString()
                };
                wishlist.push(productDetails);
                this.updateWishlistButtonState(true);
                this.showNotification('Added to wishlist!', 'success');
            }
            
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            // Update wishlist count if function exists
            if (typeof updateWishlistCount === 'function') {
                updateWishlistCount();
            }
        }
    }
    
    initializeWishlistButton(productId) {
        if (!productId) return;
        
        // Check if product is in wishlist
        let isInWishlist = false;
        
        if (window.wishlistManager) {
            isInWishlist = window.wishlistManager.isInWishlist(productId);
        } else {
            // Fallback - check localStorage
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            isInWishlist = wishlist.some(item => item.id === productId);
        }
        
        this.updateWishlistButtonState(isInWishlist);
    }
    
    updateWishlistButtonState(isInWishlist) {
        const wishlistBtn = document.getElementById('add-to-wishlist-quick');
        if (wishlistBtn) {
            if (isInWishlist) {
                wishlistBtn.classList.add('active');
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
            } else {
                wishlistBtn.classList.remove('active');
                wishlistBtn.innerHTML = '<i class="far fa-heart"></i> Add to Wishlist';
            }
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
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.quickViewSidebar = new QuickViewSidebar();
});

console.log('addToCartFromCard function defined');

// Global function for adding to cart from product cards
async function addToCartFromCard(buttonElement) {
    console.log('addToCartFromCard function called', buttonElement);
    try {
        // Get product data from button attributes
        const productId = buttonElement.getAttribute('data-product-id');
        const productName = buttonElement.getAttribute('data-product-name');
        const productPrice = buttonElement.getAttribute('data-product-price');
        const productColor = buttonElement.getAttribute('data-product-color');
        const productStock = parseInt(buttonElement.getAttribute('data-product-stock')) || 0;
        
        if (!productId) {
            console.error('Product ID not found');
            return;
        }

        // Check if product is sold out using simplified stock logic
        const isSoldOut = productStock <= 0;

        if (isSoldOut) {
            alert('This product is currently sold out and cannot be added to cart.');
            return;
        }
        
        // Prepare cart data
        const cartData = {
            action: 'add_to_cart',
            product_id: productId,
            quantity: 1,
            color: productColor || '',
            size: '', // Home & Living products typically don't have sizes
            return_url: window.location.href
        };
        
        // Call the cart API
        const response = await fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(cartData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            showCartNotification(result.message, 'success');
            
            // Update cart count if available
            if (result.cart_count !== undefined) {
                updateCartCount(result.cart_count);
            }
            
            // Disable button temporarily to prevent double-clicking
            buttonElement.disabled = true;
            buttonElement.textContent = 'Added!';
            buttonElement.style.opacity = '0.7';
            
            // Re-enable button after 2 seconds
            setTimeout(() => {
                buttonElement.disabled = false;
                buttonElement.textContent = 'Add To Bag';
                buttonElement.style.opacity = '1';
            }, 2000);
            
        } else {
            // Show error message
            showCartNotification(result.message || 'Failed to add to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showCartNotification('Failed to add to cart. Please try again.', 'error');
    }
}

// Global function for showing cart notifications
function showCartNotification(message, type = 'success') {
    // Create a notification
    const notification = document.createElement('div');
    notification.className = `cart-notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after appropriate time
    const duration = type === 'success' ? 3000 : 5000;
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, duration);
}

// Global function for updating cart count
function updateCartCount(count) {
    // Update cart count in header if it exists
    const cartCountElements = document.querySelectorAll('.cart-count, .cart-badge, [data-cart-count]');
    cartCountElements.forEach(element => {
        element.textContent = count;
        element.style.display = count > 0 ? 'inline' : 'none';
    });
}
