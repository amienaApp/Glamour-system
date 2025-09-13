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
            console.log('QuickviewManager: Click detected on:', e.target);
            if (e.target.id === 'close-quick-view' || e.target.closest('#close-quick-view')) {
                console.log('QuickviewManager: Close button clicked via event listener');
                e.preventDefault();
                e.stopPropagation();
                this.closeQuickview();
            }
            if (e.target.id === 'quick-view-overlay') {
                console.log('QuickviewManager: Overlay clicked');
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
            console.log('QuickviewManager: Click detected on:', e.target);
            if (e.target.id === 'add-to-bag-quick' || e.target.closest('#add-to-bag-quick')) {
                console.log('QuickviewManager: Add to bag button clicked');
                this.addToCart();
            }
            if (e.target.id === 'add-to-wishlist-quick' || e.target.closest('#add-to-wishlist-quick')) {
                console.log('QuickviewManager: Add to wishlist button clicked');
                this.addToWishlist();
            }
        });
    }

    setupQuickviewElements() {
        // Ensure quickview elements exist
        this.sidebar = document.getElementById('quick-view-sidebar');
        this.overlay = document.getElementById('quick-view-overlay');
        
        console.log('QuickviewManager: setupQuickviewElements called');
        console.log('QuickviewManager: Sidebar found:', this.sidebar);
        console.log('QuickviewManager: Overlay found:', this.overlay);
        
        // Check for wishlist button
        const wishlistBtn = document.getElementById('add-to-wishlist-quick');
        console.log('QuickviewManager: Wishlist button found:', wishlistBtn);
        
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
            
            console.log('QuickviewManager: Fetching product data from:', apiUrl);
            const response = await fetch(apiUrl);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('QuickviewManager: API response:', data);

            if (data.success && data.product) {
                console.log('QuickviewManager: Product data received:', data.product);
                this.currentProduct = data.product;
                console.log('QuickviewManager: currentProduct set to:', this.currentProduct);
                
                // Validate required data
                if (!data.product.images) {
                    console.warn('QuickviewManager: No images data, using empty array');
                    data.product.images = [];
                }
                if (!data.product.colors) {
                    console.warn('QuickviewManager: No colors data, using empty array');
                    data.product.colors = [];
                }
                if (!data.product.sizes) {
                    console.warn('QuickviewManager: No sizes data, using empty array');
                    data.product.sizes = [];
                }
                
                this.populateQuickview(data.product);
                this.showQuickview();
            } else {
                console.error('QuickviewManager: API returned error:', data.message);
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
        console.log('QuickviewManager: Starting populateQuickview with product:', product);
        
        try {
            // First, restore the proper content structure if it was replaced by loading state
            this.restoreContentStructure();
            
            // Set product title and price
            console.log('QuickviewManager: Setting title and price');
            this.setElementText('quick-view-title', product.name);
            this.setElementText('quick-view-price', this.formatPrice(product.price, product.salePrice));
            this.setElementText('quick-view-description', product.description);

            // Populate images
            console.log('QuickviewManager: Populating images');
            this.populateImages(product.images);

            // Populate colors
            console.log('QuickviewManager: Populating colors');
            this.populateColors(product.colors);

            // Populate sizes
            console.log('QuickviewManager: Populating sizes');
            this.populateSizes(product.sizes);

            // Set initial selections
            console.log('QuickviewManager: Setting initial selections');
            this.selectedColor = product.defaultColor || (product.colors && product.colors[0]?.value);
            this.selectedSize = product.defaultSize || (product.sizes && product.sizes[0]?.name);
            console.log('QuickviewManager: Selected color:', this.selectedColor, 'Selected size:', this.selectedSize);

            // Update color and size displays
            console.log('QuickviewManager: Updating color and size displays');
            this.updateColorSelection();
            this.updateSizeSelection();

            // Update availability and sold out status
            console.log('QuickviewManager: Updating availability');
            this.updateAvailability();
            this.updateSoldOutStatus(product);
            
            // Initialize wishlist button state
            console.log('QuickviewManager: Initializing wishlist button state');
            this.initializeWishlistButton(product);
            
            console.log('QuickviewManager: populateQuickview completed successfully');
        } catch (error) {
            console.error('QuickviewManager: Error in populateQuickview:', error);
            throw error;
        }
    }

    populateImages(images) {
        const mainImageContainer = document.querySelector('.main-image-container');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');

        console.log('QuickviewManager: Main image container:', mainImageContainer);
        console.log('QuickviewManager: Thumbnails container:', thumbnailsContainer);

        if (!mainImageContainer || !thumbnailsContainer) {
            console.error('QuickviewManager: Missing image containers');
            return;
        }

        // Helper function to fix image paths based on current page location
        const fixImagePath = (imagePath) => {
            if (!imagePath) return '';
            // If the path already starts with http or /, use it as is
            if (imagePath.startsWith('http') || imagePath.startsWith('/')) {
                return imagePath;
            }
            // Otherwise, make it relative to the root directory
            return '../' + imagePath;
        };

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
            console.log('QuickviewManager: Setting main image:', firstImage);
            console.log('QuickviewManager: Fixed image path:', fixImagePath(firstImage.src));
            mainImageContainer.innerHTML = '';
            
            if (isVideoFile(firstImage.src)) {
                const video = document.createElement('video');
                video.src = fixImagePath(firstImage.src);
                video.alt = firstImage.alt;
                video.controls = true;
                video.muted = true;
                video.loop = true;
                video.style.display = 'block';
                video.style.maxWidth = '100%';
                video.style.height = 'auto';
                mainImageContainer.appendChild(video);
                console.log('QuickviewManager: Added video to main container');
            } else {
                const img = document.createElement('img');
                img.src = fixImagePath(firstImage.src);
                img.alt = firstImage.alt;
                img.style.display = 'block';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                
                // Add error handling
                img.onload = function() {
                    console.log('QuickviewManager: Main image loaded successfully');
                };
                img.onerror = function() {
                    console.error('QuickviewManager: Main image failed to load:', img.src);
                };
                
                mainImageContainer.appendChild(img);
                console.log('QuickviewManager: Added image to main container:', img.src);
            }
        } else {
            console.log('QuickviewManager: No images to display');
        }

        // Clear and populate thumbnails
        thumbnailsContainer.innerHTML = '';

        images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
            
            if (isVideoFile(image.src)) {
                const video = document.createElement('video');
                video.src = fixImagePath(image.src);
                video.alt = image.alt;
                video.muted = true;
                video.loop = true;
                video.setAttribute('data-index', index);
                video.style.maxWidth = '100%';
                video.style.height = 'auto';
                thumbnail.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = fixImagePath(image.src);
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

        if (!colors || !Array.isArray(colors)) {
            console.warn('QuickviewManager: No colors data provided');
            return;
        }

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

        if (!sizes || !Array.isArray(sizes)) {
            console.warn('QuickviewManager: No sizes data provided');
            return;
        }

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
        const mainImageContainer = document.querySelector('.main-image-container');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        
        if (mainImageContainer && this.currentProduct.images[imageIndex]) {
            const selectedImage = this.currentProduct.images[imageIndex];
            
            // Helper function to fix image paths
            const fixImagePath = (imagePath) => {
                if (!imagePath) return '';
                if (imagePath.startsWith('http') || imagePath.startsWith('/')) {
                    return imagePath;
                }
                return '../' + imagePath;
            };
            
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
                video.src = fixImagePath(selectedImage.src);
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
                img.src = fixImagePath(selectedImage.src);
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

        if (!this.currentProduct.colors || !Array.isArray(this.currentProduct.colors)) {
            console.warn('QuickviewManager: No colors data available for availability check');
            return;
        }

        const selectedColor = this.currentProduct.colors.find(c => c.value === this.selectedColor);
        const selectedSize = selectedColor?.sizes?.find(s => s.name === this.selectedSize);

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

    updateSoldOutStatus(product) {
        // Check if product is sold out using simplified stock logic
        const stock = parseInt(product.stock) || 0;
        const isSoldOut = stock <= 0;

        // Get the Add to Bag button
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        const addToWishlistBtn = document.getElementById('add-to-wishlist-quick');

        if (addToBagBtn) {
            if (isSoldOut) {
                // Disable the button and show sold out state
                addToBagBtn.disabled = true;
                addToBagBtn.classList.add('sold-out-btn');
                addToBagBtn.innerHTML = '<i class="fas fa-times"></i> Sold Out';
            } else {
                // Enable the button and show normal state
                addToBagBtn.disabled = false;
                addToBagBtn.classList.remove('sold-out-btn');
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Bag';
            }
        }

        if (addToWishlistBtn) {
            if (isSoldOut) {
                // Disable wishlist button for sold out products
                addToWishlistBtn.disabled = true;
                addToWishlistBtn.style.opacity = '0.5';
                addToWishlistBtn.style.cursor = 'not-allowed';
            } else {
                // Enable wishlist button for available products
                addToWishlistBtn.disabled = false;
                addToWishlistBtn.style.opacity = '1';
                addToWishlistBtn.style.cursor = 'pointer';
            }
        }

        // Update availability display
        const availabilityEl = document.getElementById('quick-view-availability');
        if (availabilityEl) {
            if (isSoldOut) {
                availabilityEl.textContent = 'SOLD OUT';
                availabilityEl.className = 'quick-view-stock sold-out';
            } else if (stock <= 2) {
                availabilityEl.textContent = `⚠️ Only ${stock} left in stock!`;
                availabilityEl.className = 'quick-view-stock low-stock';
            } else if (stock <= 5) {
                availabilityEl.textContent = `Only ${stock} left`;
                availabilityEl.className = 'quick-view-stock low-stock';
            } else {
                availabilityEl.textContent = 'In Stock';
                availabilityEl.className = 'quick-view-stock in-stock';
            }
        }
    }

    initializeWishlistButton(product) {
        const wishlistBtn = document.getElementById('add-to-wishlist-quick');
        const productId = product.id || product._id;
        if (wishlistBtn && window.wishlistManager && productId) {
            const isInWishlist = window.wishlistManager.isInWishlist(productId);
            window.wishlistManager.updateButtonState(wishlistBtn, isInWishlist);
            console.log('QuickviewManager: Wishlist button initialized, isInWishlist:', isInWishlist);
        }
    }

    async addToCart() {
        console.log('QuickviewManager: addToCart called');
        console.log('QuickviewManager: currentProduct:', this.currentProduct);
        console.log('QuickviewManager: selectedColor:', this.selectedColor);
        console.log('QuickviewManager: selectedSize:', this.selectedSize);
        
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

        // Use unified cart notification manager if available
        if (window.cartNotificationManager) {
            const success = await window.cartNotificationManager.addToCart(
                this.currentProduct.id,
                1,
                this.selectedColor,
                this.selectedSize,
                this.currentProduct.price
            );
            
            if (success) {
                this.closeQuickview();
            }
            return;
        }

        // Fallback to direct API call
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
                if (window.showSuccessNotification) {
                    window.showSuccessNotification('Product added to cart successfully!');
                } else {
                    alert('Product added to cart successfully!');
                }
                this.updateCartCount(data.cart_count);
                this.closeQuickview();
            } else {
                if (window.showErrorNotification) {
                    window.showErrorNotification('Error: ' + data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            if (window.showErrorNotification) {
                window.showErrorNotification('Error adding product to cart');
            } else {
                alert('Error adding product to cart');
            }
        }
    }

    async addToWishlist() {
        console.log('QuickviewManager: addToWishlist called');
        console.log('QuickviewManager: currentProduct:', this.currentProduct);
        
        if (!this.currentProduct) return;

        try {
            const productId = this.currentProduct.id || this.currentProduct._id;
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
        // Use the unified cart notification manager if available
        if (window.cartNotificationManager) {
            window.cartNotificationManager.cartCount = count;
            window.cartNotificationManager.updateCartCountDisplay();
        } else {
            // Fallback to direct update
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = count;
                element.style.display = count > 0 ? 'flex' : 'none';
            });
        }
    }

    showQuickview() {
        console.log('QuickviewManager: showQuickview called');
        console.log('QuickviewManager: Sidebar element:', this.sidebar);
        console.log('QuickviewManager: Overlay element:', this.overlay);
        
        if (this.sidebar && this.overlay) {
            console.log('QuickviewManager: Adding active classes');
            this.sidebar.classList.add('active');
            this.overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Debug: Check computed styles
            const sidebarStyles = window.getComputedStyle(this.sidebar);
            console.log('QuickviewManager: Sidebar computed styles:', {
                position: sidebarStyles.position,
                right: sidebarStyles.right,
                width: sidebarStyles.width,
                height: sidebarStyles.height,
                zIndex: sidebarStyles.zIndex,
                display: sidebarStyles.display,
                visibility: sidebarStyles.visibility,
                opacity: sidebarStyles.opacity
            });
            
            // Force the sidebar to be visible if CSS isn't working
            if (sidebarStyles.right === '-500px') {
                console.log('QuickviewManager: CSS transition not working, forcing visibility');
                this.sidebar.style.right = '0px';
                this.sidebar.style.transition = 'right 0.3s ease';
            }
            
            console.log('QuickviewManager: Quickview should now be visible');
        } else {
            console.error('QuickviewManager: Missing sidebar or overlay elements');
        }
    }

    closeQuickview() {
        console.log('QuickviewManager: closeQuickview called');
        console.log('QuickviewManager: Sidebar element:', this.sidebar);
        console.log('QuickviewManager: Overlay element:', this.overlay);
        
        if (this.sidebar && this.overlay) {
            console.log('QuickviewManager: Removing active classes');
            console.log('QuickviewManager: Sidebar classes before:', this.sidebar.className);
            console.log('QuickviewManager: Overlay classes before:', this.overlay.className);
            
            this.sidebar.classList.remove('active');
            this.overlay.classList.remove('active');
            
            // Remove inline styles that might override CSS
            this.sidebar.style.right = '';
            this.sidebar.style.transition = '';
            
            console.log('QuickviewManager: Sidebar classes after:', this.sidebar.className);
            console.log('QuickviewManager: Overlay classes after:', this.overlay.className);
            console.log('QuickviewManager: Sidebar computed style right:', window.getComputedStyle(this.sidebar).right);
            
            document.body.style.overflow = '';
            console.log('QuickviewManager: Close completed');
        } else {
            console.error('QuickviewManager: Sidebar or overlay not found');
        }
        console.log('QuickviewManager: Clearing currentProduct in closeQuickview');
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

    restoreContentStructure() {
        const content = document.querySelector('.quick-view-content');
        if (content && content.innerHTML.includes('loading-state')) {
            console.log('QuickviewManager: Restoring content structure');
            content.innerHTML = `
                <!-- Product Media -->
                <div class="quick-view-images">
                    <div class="main-image-container">
                        <img id="quick-view-main-image" src="" alt="Product Media">
                        <video id="quick-view-main-video" src="" muted loop style="display: none; max-width: 100%; border-radius: 8px;"></video>
                    </div>
                    <div class="thumbnail-images" id="quick-view-thumbnails">
                        <!-- Thumbnails will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="quick-view-details">
                    <h2 id="quick-view-title"></h2>
                    <div class="quick-view-price" id="quick-view-price"></div>
                    <div class="quick-view-reviews">
                        <span class="stars" id="quick-view-stars"></span>
                        <span class="review-count" id="quick-view-review-count"></span>
                    </div>
                    
                    <!-- Color Selection -->
                    <div class="quick-view-colors">
                        <h4>Color</h4>
                        <div class="color-selection" id="quick-view-color-selection">
                            <!-- Colors will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Size Selection -->
                    <div class="quick-view-sizes">
                        <h4>Size</h4>
                        <div class="size-selection" id="quick-view-size-selection">
                            <!-- Sizes will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="quick-view-actions">
                        <button class="add-to-bag-quick" id="add-to-bag-quick">
                            <i class="fas fa-shopping-bag"></i>
                            Add to Bag
                        </button>
                        <button class="add-to-wishlist-quick" id="add-to-wishlist-quick">
                            <i class="far fa-heart"></i>
                            Add to Wishlist
                        </button>
                    </div>
                    
                    <!-- Availability Status -->
                    <div class="quick-view-availability" id="quick-view-availability" style="margin-top: 15px; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600;">
                        <!-- Availability will be populated by JavaScript -->
                    </div>
                    
                    <!-- Product Description -->
                    <div class="quick-view-description">
                        <p id="quick-view-description"></p>
                    </div>
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
        // Add a small delay to ensure all elements are rendered
        setTimeout(() => {
            window.quickviewManager = new QuickviewManager();
            console.log('QuickviewManager: Initialized successfully');
        }, 100);
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

// Global function for closing quickview (for backward compatibility)
window.closeQuickView = function() {
    console.log('Global closeQuickView called');
    if (window.quickviewManager) {
        console.log('Calling quickviewManager.closeQuickview()');
        window.quickviewManager.closeQuickview();
    } else {
        console.error('quickviewManager not available');
    }
};
