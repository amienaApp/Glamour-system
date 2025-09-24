/**
 * Enhanced Client-Side Wishlist Manager
 * Uses localStorage to store user's favorite products with full integration
 */
if (typeof WishlistManager === 'undefined') {
    class WishlistManager {
    constructor() {
        this.storageKey = 'wishlist';
        this.maxItems = 100; // Maximum items in wishlist
        this.selectedProducts = new Set(); // Track selected products for bulk operations
        this.bulkMode = false; // Whether we're in bulk selection mode
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateWishlistCount();
        this.initializeButtonStates();
        this.setupStorageListener();
    }
    
    bindEvents() {
        // Bind heart button clicks with improved event handling
        document.addEventListener('click', (e) => {
            const heartButton = e.target.closest('.heart-button');
            const wishlistBtn = e.target.closest('.wishlist-btn');
            const quickViewBtn = e.target.closest('#add-to-wishlist-quick') || e.target.id === 'add-to-wishlist-quick';
            
            if (heartButton || wishlistBtn) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getProductId(e.target);
                if (productId) {
                    this.toggleWishlist(productId, e.target);
                }
            } else if (quickViewBtn) {
                e.preventDefault();
                e.stopPropagation();
                const productId = this.getQuickViewProductId();
                if (productId) {
                    this.toggleQuickViewWishlist(productId, e.target);
                }
            }
        });
        
        // Bind keyboard events for accessibility
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const heartButton = e.target.closest('.heart-button');
                const wishlistBtn = e.target.closest('.wishlist-btn');
                const quickViewBtn = e.target.closest('#add-to-wishlist-quick') || e.target.id === 'add-to-wishlist-quick';
                
                if (heartButton || wishlistBtn) {
                    e.preventDefault();
                    const productId = this.getProductId(e.target);
                    if (productId) {
                        this.toggleWishlist(productId, e.target);
                    }
                } else if (quickViewBtn) {
                    e.preventDefault();
                    const productId = this.getQuickViewProductId();
                    if (productId) {
                        this.toggleQuickViewWishlist(productId, e.target);
                    }
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
        const mainImageContainer = document.querySelector('.main-image-container');
        if (mainImageContainer) {
            const activeImage = mainImageContainer.querySelector('img, video');
            if (activeImage) {
                variantImage = activeImage.src;
            }
        }
        
        // Fallback to main image if no variant image found
        if (!variantImage) {
            if (quickViewMainImage && quickViewMainImage.src) {
                variantImage = quickViewMainImage.src;
            } else if (quickViewMainVideo && quickViewMainVideo.src) {
                variantImage = quickViewMainVideo.src;
            }
        }
        
        // Get selected color from quickview
        let selectedColor = '';
        const activeColorCircle = document.querySelector('#quick-view-color-selection .quick-view-color-circle.active');
        if (activeColorCircle) {
            selectedColor = activeColorCircle.getAttribute('data-color') || '';
        }
        
        // Get selected size from quickview
        let selectedSize = '';
        const activeSizeBtn = document.querySelector('#quick-view-size-selection .quick-view-size-btn.active');
        if (activeSizeBtn) {
            selectedSize = activeSizeBtn.textContent.replace(' (Out of Stock)', '').trim();
        }
        
        return {
            id: productId,
            name: name,
            price: price.replace(/[^0-9.]/g, '') || '0',
            image: variantImage,
            category: 'General',
            selectedColor: selectedColor,
            selectedSize: selectedSize,
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
                    // Could not parse variants data
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
            this.saveWishlist(wishlist);
            this.updateButtonState(button, false);
            this.showNotification('Removed from wishlist', 'info');
            this.triggerWishlistEvent('removed', { productId, wishlist });
        } else {
            // Check if wishlist is full
            if (wishlist.length >= this.maxItems) {
                this.showNotification(`Wishlist is full (max ${this.maxItems} items). Remove some items first.`, 'warning');
                return;
            }
            
            // Add to wishlist
            const productDetails = this.getProductDetails(productId);
            if (productDetails) {
                // Check for duplicates by ID and selected color
                const duplicateIndex = wishlist.findIndex(item => 
                    item.id === productId && item.selectedColor === productDetails.selectedColor
                );
                
                if (duplicateIndex > -1) {
                    this.showNotification('This item is already in your wishlist', 'info');
                    return;
                }
                
                wishlist.push(productDetails);
                this.saveWishlist(wishlist);
                this.updateButtonState(button, true);
                const colorInfo = productDetails.selectedColor ? ` (${productDetails.selectedColor})` : '';
                this.showNotification(`Added to wishlist${colorInfo}`, 'success');
                this.triggerWishlistEvent('added', { productId, product: productDetails, wishlist });
            } else {
                this.showNotification('Could not add to wishlist', 'error');
                return;
            }
        }
        
        this.updateWishlistCount();
        
        // Update dropdown if it's open
        if (typeof loadWishlistDropdown === 'function') {
            loadWishlistDropdown();
        }
    }
    
    toggleQuickViewWishlist(productId, button) {
        const wishlist = this.getWishlist();
        
        // Get product details from quickview
        const productDetails = this.getQuickViewProductDetails(productId);
        if (!productDetails) {
            this.showNotification('Could not get product details', 'error');
            return;
        }
        
        // Check for existing item with same ID and selected color/size
        const existingIndex = wishlist.findIndex(item => 
            item.id === productId && 
            item.selectedColor === productDetails.selectedColor &&
            item.selectedSize === productDetails.selectedSize
        );
        
        if (existingIndex > -1) {
            // Remove from wishlist
            wishlist.splice(existingIndex, 1);
            this.saveWishlist(wishlist);
            this.updateButtonState(button, false);
            this.showNotification('Removed from wishlist', 'info');
            this.triggerWishlistEvent('removed', { productId, wishlist });
        } else {
            // Check if wishlist is full
            if (wishlist.length >= this.maxItems) {
                this.showNotification(`Wishlist is full (max ${this.maxItems} items). Remove some items first.`, 'warning');
                return;
            }
            
            // Add to wishlist with quickview details
            wishlist.push(productDetails);
            this.saveWishlist(wishlist);
            this.updateButtonState(button, true);
            const colorInfo = productDetails.selectedColor ? ` (${productDetails.selectedColor})` : '';
            const sizeInfo = productDetails.selectedSize ? ` - Size ${productDetails.selectedSize}` : '';
            this.showNotification(`Added to wishlist${colorInfo}${sizeInfo}`, 'success');
            this.triggerWishlistEvent('added', { productId, product: productDetails, wishlist });
        }
        
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
        if (!button) return;
        
        // Force a clean state - remove all classes and clear content
        button.className = button.className.replace(/active/g, '').trim();
        button.innerHTML = '';
        
        if (isInWishlist) {
            button.classList.add('active');
            // For quickview buttons, show text with filled red heart
            if (button.classList.contains('add-to-wishlist-quick') || button.id === 'add-to-wishlist-quick') {
                button.innerHTML = '<i class="fas fa-heart" style="color: #e74c3c !important;"></i> Remove from Wishlist';
            } else {
                // For product card buttons, show only filled red heart icon
                button.innerHTML = '<i class="fas fa-heart" style="color: #e74c3c !important;"></i>';
            }
        } else {
            // For quickview buttons, show text with outline gray heart
            if (button.classList.contains('add-to-wishlist-quick') || button.id === 'add-to-wishlist-quick') {
                button.innerHTML = '<i class="far fa-heart" style="color: #666 !important;"></i> Add to Wishlist';
            } else {
                // For product card buttons, show only outline gray heart icon
                button.innerHTML = '<i class="far fa-heart" style="color: #666 !important;"></i>';
            }
        }
    }
    
    showNotification(message, type = 'success') {
        // Use simple notification if available
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            // Show notification
        }
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
        const heartButtons = document.querySelectorAll('.heart-button, .wishlist-btn, #add-to-wishlist-quick');
        
        heartButtons.forEach(button => {
            const productId = this.getProductId(button);
            if (productId) {
                const isInWishlist = productIds.includes(productId);
                this.updateButtonState(button, isInWishlist);
            }
        });
    }
    
    // Setup storage listener for cross-tab synchronization
    setupStorageListener() {
        window.addEventListener('storage', (e) => {
            if (e.key === this.storageKey) {
                this.updateWishlistCount();
                this.initializeButtonStates();
                if (typeof loadWishlistDropdown === 'function') {
                    loadWishlistDropdown();
                }
            }
        });
    }
    
    // Trigger custom events for wishlist changes
    triggerWishlistEvent(eventType, data) {
        const event = new CustomEvent('wishlistChange', {
            detail: {
                type: eventType,
                productId: data.productId,
                product: data.product,
                wishlist: data.wishlist,
                count: data.wishlist ? data.wishlist.length : this.getWishlist().length
            }
        });
        document.dispatchEvent(event);
    }
    
    // Get wishlist statistics
    getWishlistStats() {
        const wishlist = this.getWishlist();
        const categories = {};
        let totalValue = 0;
        
        wishlist.forEach(item => {
            // Count by category
            categories[item.category] = (categories[item.category] || 0) + 1;
            
            // Calculate total value
            const price = parseFloat(item.price) || 0;
            totalValue += price;
        });
        
        return {
            totalItems: wishlist.length,
            totalValue: totalValue,
            categories: categories,
            isEmpty: wishlist.length === 0,
            isFull: wishlist.length >= this.maxItems
        };
    }
    
    // Clear wishlist
    clearWishlist() {
        localStorage.removeItem(this.storageKey);
        this.updateWishlistCount();
        this.initializeButtonStates();
        this.showNotification('Wishlist cleared', 'info');
        this.triggerWishlistEvent('cleared', { wishlist: [] });
        
        if (typeof loadWishlistDropdown === 'function') {
            loadWishlistDropdown();
        }
    }
    
    // Toggle bulk selection mode
    toggleBulkMode() {
        this.bulkMode = !this.bulkMode;
        this.selectedProducts.clear();
        
        if (this.bulkMode) {
            this.showBulkControls();
            this.addBulkSelectionHandlers();
        } else {
            this.hideBulkControls();
            this.removeBulkSelectionHandlers();
        }
        
        this.updateBulkSelectionUI();
    }
    
    // Show bulk selection controls
    showBulkControls() {
        // Create bulk controls if they don't exist
        if (!document.getElementById('bulk-wishlist-controls')) {
            const controlsHTML = `
                <div id="bulk-wishlist-controls" class="bulk-wishlist-controls">
                    <div class="bulk-controls-header">
                        <h3>Bulk Wishlist Selection</h3>
                        <button id="close-bulk-mode" class="close-bulk-btn">×</button>
                    </div>
                    <div class="bulk-controls-content">
                        <div class="bulk-stats">
                            <span id="selected-count">0 products selected</span>
                        </div>
                        <div class="bulk-actions">
                            <button id="add-selected-to-wishlist" class="bulk-btn add-btn" disabled>
                                <i class="fas fa-heart"></i> Add Selected to Wishlist
                            </button>
                            <button id="remove-selected-from-wishlist" class="bulk-btn remove-btn" disabled>
                                <i class="far fa-heart"></i> Remove Selected from Wishlist
                            </button>
                            <button id="select-all-visible" class="bulk-btn select-all-btn">
                                <i class="fas fa-check-square"></i> Select All Visible
                            </button>
                            <button id="clear-selection" class="bulk-btn clear-btn" disabled>
                                <i class="fas fa-times"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', controlsHTML);
        }
        
        document.getElementById('bulk-wishlist-controls').style.display = 'block';
    }
    
    // Hide bulk selection controls
    hideBulkControls() {
        const controls = document.getElementById('bulk-wishlist-controls');
        if (controls) {
            controls.style.display = 'none';
        }
    }
    
    // Add bulk selection event handlers
    addBulkSelectionHandlers() {
        // Close bulk mode
        document.getElementById('close-bulk-mode').onclick = () => this.toggleBulkMode();
        
        // Add selected to wishlist
        document.getElementById('add-selected-to-wishlist').onclick = () => this.addSelectedToWishlist();
        
        // Remove selected from wishlist
        document.getElementById('remove-selected-from-wishlist').onclick = () => this.removeSelectedFromWishlist();
        
        // Select all visible
        document.getElementById('select-all-visible').onclick = () => this.selectAllVisible();
        
        // Clear selection
        document.getElementById('clear-selection').onclick = () => this.clearSelection();
        
        // Add click handlers to product cards
        document.addEventListener('click', this.handleBulkSelectionClick.bind(this));
    }
    
    // Remove bulk selection event handlers
    removeBulkSelectionHandlers() {
        document.removeEventListener('click', this.handleBulkSelectionClick.bind(this));
    }
    
    // Handle bulk selection clicks
    handleBulkSelectionClick(e) {
        if (!this.bulkMode) return;
        
        const productCard = e.target.closest('.product-card');
        if (productCard) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = productCard.getAttribute('data-product-id');
            if (productId) {
                this.toggleProductSelection(productId, productCard);
            }
        }
    }
    
    // Toggle individual product selection
    toggleProductSelection(productId, productCard) {
        if (this.selectedProducts.has(productId)) {
            this.selectedProducts.delete(productId);
            productCard.classList.remove('bulk-selected');
        } else {
            this.selectedProducts.add(productId);
            productCard.classList.add('bulk-selected');
        }
        
        this.updateBulkSelectionUI();
    }
    
    // Update bulk selection UI
    updateBulkSelectionUI() {
        const selectedCount = this.selectedProducts.size;
        const countElement = document.getElementById('selected-count');
        const addBtn = document.getElementById('add-selected-to-wishlist');
        const removeBtn = document.getElementById('remove-selected-from-wishlist');
        const clearBtn = document.getElementById('clear-selection');
        
        if (countElement) {
            countElement.textContent = `${selectedCount} product${selectedCount !== 1 ? 's' : ''} selected`;
        }
        
        if (addBtn) addBtn.disabled = selectedCount === 0;
        if (removeBtn) removeBtn.disabled = selectedCount === 0;
        if (clearBtn) clearBtn.disabled = selectedCount === 0;
    }
    
    // Add selected products to wishlist
    addSelectedToWishlist() {
        const wishlist = this.getWishlist();
        const selectedIds = Array.from(this.selectedProducts);
        let addedCount = 0;
        let skippedCount = 0;
        
        selectedIds.forEach(productId => {
            // Check if already in wishlist
            const existingIndex = wishlist.findIndex(item => item.id === productId);
            if (existingIndex === -1) {
                // Check if wishlist is full
                if (wishlist.length >= this.maxItems) {
                    this.showNotification(`Wishlist is full (max ${this.maxItems} items). Cannot add more.`, 'warning');
                    return;
                }
                
                const productDetails = this.getProductDetails(productId);
                if (productDetails) {
                    wishlist.push(productDetails);
                    addedCount++;
                }
            } else {
                skippedCount++;
            }
        });
        
        if (addedCount > 0) {
            this.saveWishlist(wishlist);
            this.updateWishlistCount();
            this.initializeButtonStates();
            this.showNotification(`${addedCount} product${addedCount !== 1 ? 's' : ''} added to wishlist${skippedCount > 0 ? ` (${skippedCount} already in wishlist)` : ''}`, 'success');
            this.triggerWishlistEvent('bulk-added', { addedCount, skippedCount, wishlist });
        } else {
            this.showNotification('No new products added to wishlist', 'info');
        }
        
        this.clearSelection();
    }
    
    // Remove selected products from wishlist
    removeSelectedFromWishlist() {
        const wishlist = this.getWishlist();
        const selectedIds = Array.from(this.selectedProducts);
        let removedCount = 0;
        
        selectedIds.forEach(productId => {
            const existingIndex = wishlist.findIndex(item => item.id === productId);
            if (existingIndex > -1) {
                wishlist.splice(existingIndex, 1);
                removedCount++;
            }
        });
        
        if (removedCount > 0) {
            this.saveWishlist(wishlist);
            this.updateWishlistCount();
            this.initializeButtonStates();
            this.showNotification(`${removedCount} product${removedCount !== 1 ? 's' : ''} removed from wishlist`, 'success');
            this.triggerWishlistEvent('bulk-removed', { removedCount, wishlist });
        } else {
            this.showNotification('No products were removed from wishlist', 'info');
        }
        
        this.clearSelection();
    }
    
    // Select all visible products
    selectAllVisible() {
        const productCards = document.querySelectorAll('.product-card');
        this.selectedProducts.clear();
        
        productCards.forEach(card => {
            const productId = card.getAttribute('data-product-id');
            if (productId) {
                this.selectedProducts.add(productId);
                card.classList.add('bulk-selected');
            }
        });
        
        this.updateBulkSelectionUI();
        this.showNotification(`${this.selectedProducts.size} products selected`, 'info');
    }
    
    // Clear current selection
    clearSelection() {
        this.selectedProducts.clear();
        document.querySelectorAll('.product-card.bulk-selected').forEach(card => {
            card.classList.remove('bulk-selected');
        });
        this.updateBulkSelectionUI();
    }
    
    // Remove specific item from wishlist
    removeFromWishlist(productId) {
        const wishlist = this.getWishlist();
        const filteredWishlist = wishlist.filter(item => item.id !== productId);
        
        if (filteredWishlist.length !== wishlist.length) {
            this.saveWishlist(filteredWishlist);
            this.updateWishlistCount();
            this.initializeButtonStates();
            this.showNotification('Item removed from wishlist', 'info');
            this.triggerWishlistEvent('removed', { productId, wishlist: filteredWishlist });
            
            if (typeof loadWishlistDropdown === 'function') {
                loadWishlistDropdown();
            }
            return true;
        }
        return false;
    }
    
    // Check if product is in wishlist
    isInWishlist(productId, selectedColor = '', selectedSize = '') {
        const wishlist = this.getWishlist();
        return wishlist.some(item => 
            item.id === productId && 
            (selectedColor === '' || item.selectedColor === selectedColor) &&
            (selectedSize === '' || item.selectedSize === selectedSize)
        );
    }
    
    // Get wishlist items by category
    getWishlistByCategory(category) {
        const wishlist = this.getWishlist();
        return wishlist.filter(item => item.category === category);
    }
    
    // Export wishlist data
    exportWishlist() {
        const wishlist = this.getWishlist();
        const dataStr = JSON.stringify(wishlist, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = `wishlist-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    // Import wishlist data
    importWishlist(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const importedData = JSON.parse(e.target.result);
                    if (Array.isArray(importedData)) {
                        // Validate imported data
                        const validData = importedData.filter(item => 
                            item.id && item.name && item.price !== undefined
                        );
                        
                        if (validData.length > 0) {
                            this.saveWishlist(validData);
                            this.updateWishlistCount();
                            this.initializeButtonStates();
                            this.showNotification(`${validData.length} items imported to wishlist`, 'success');
                            this.triggerWishlistEvent('imported', { wishlist: validData });
                            resolve(validData);
                        } else {
                            reject(new Error('No valid items found in imported data'));
                        }
                    } else {
                        reject(new Error('Invalid file format'));
                    }
                } catch (error) {
                    reject(new Error('Failed to parse imported file'));
                }
            };
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsText(file);
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
    
    // Make functions available globally
    window.openWishlist = () => window.wishlistManager.openWishlist();
    window.clearWishlist = () => window.wishlistManager.clearWishlist();
    window.removeFromWishlist = (productId) => window.wishlistManager.removeFromWishlist(productId);
    window.isInWishlist = (productId, color) => window.wishlistManager.isInWishlist(productId, color);
    window.getWishlistStats = () => window.wishlistManager.getWishlistStats();
    window.exportWishlist = () => window.wishlistManager.exportWishlist();
    window.importWishlist = (file) => window.wishlistManager.importWishlist(file);
    window.toggleBulkMode = () => window.wishlistManager.toggleBulkMode();
    
    // Listen for wishlist change events
    document.addEventListener('wishlistChange', (e) => {
        // Wishlist changed
        
        // Update any analytics or tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'wishlist_' + e.detail.type, {
                'event_category': 'engagement',
                'event_label': e.detail.productId,
                'value': e.detail.count
            });
        }
    });
});

// Enhanced CSS for wishlist integration
const wishlistStyles = `
<style>
/* Heart button active state (when in wishlist) - Only for product cards */
.heart-button.active {
    background-color: #ffffff !important;
    border-color: #e74c3c !important;
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3) !important;
    animation: heartPulse 0.3s ease-in-out;
}

.heart-button.active i {
    color: #e74c3c !important;
    animation: heartBeat 0.6s ease-in-out;
}

/* Bulk Selection Styles */
.bulk-wishlist-controls {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    z-index: 10000;
    min-width: 400px;
    max-width: 90vw;
    max-height: 80vh;
    overflow: hidden;
    animation: bulkControlsSlideIn 0.3s ease-out;
}

.bulk-controls-header {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bulk-controls-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.close-bulk-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.close-bulk-btn:hover {
    background-color: rgba(255,255,255,0.2);
}

.bulk-controls-content {
    padding: 20px;
}

.bulk-stats {
    text-align: center;
    margin-bottom: 20px;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.bulk-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.bulk-btn {
    padding: 12px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.bulk-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.bulk-btn.add-btn {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.bulk-btn.add-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #c0392b, #a93226);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
}

.bulk-btn.remove-btn {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    color: white;
}

.bulk-btn.remove-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(127, 140, 141, 0.4);
}

.bulk-btn.select-all-btn {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.bulk-btn.select-all-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #2980b9, #21618c);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}

.bulk-btn.clear-btn {
    background: linear-gradient(135deg, #e67e22, #d35400);
    color: white;
}

.bulk-btn.clear-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #d35400, #ba4a00);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(230, 126, 34, 0.4);
}

/* Product card selection state */
.product-card.bulk-selected {
    border: 3px solid #e74c3c !important;
    box-shadow: 0 0 20px rgba(231, 76, 60, 0.5) !important;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.product-card.bulk-selected::before {
    content: '✓';
    position: absolute;
    top: 10px;
    left: 10px;
    background: #e74c3c;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    z-index: 10;
}

/* Bulk mode overlay */
.bulk-mode-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    animation: fadeIn 0.3s ease-out;
}

/* Animations */
@keyframes bulkControlsSlideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Heart button hover effects */
.heart-button:hover {
    transform: scale(1.1);
    transition: all 0.2s ease;
}

.heart-button:hover i {
    color: #e74c3c !important;
}

/* Wishlist button states */
.wishlist-btn.active {
    background-color: #e74c3c !important;
    color: white !important;
    border-color: #e74c3c !important;
}

.wishlist-btn:hover {
    background-color: #c0392b !important;
    color: white !important;
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

/* Wishlist dropdown enhancements - REMOVED TO AVOID CONFLICTS */

/* REMOVED TO AVOID CONFLICTS */

.wishlist-dropdown-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.wishlist-dropdown-item:hover {
    background-color: #f8f9fa;
}

.wishlist-dropdown-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 12px;
}

.wishlist-dropdown-item-info {
    flex: 1;
    min-width: 0;
}

.wishlist-dropdown-item-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wishlist-dropdown-item-price {
    font-size: 13px;
    font-weight: 600;
    color: #e74c3c;
    margin-bottom: 2px;
}

.wishlist-dropdown-item-category {
    font-size: 11px;
    color: #666;
    text-transform: capitalize;
}

.wishlist-dropdown-item-actions {
    display: flex;
    gap: 8px;
}

.wishlist-dropdown-item-actions button {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s ease;
}

.btn-add-cart {
    background-color: #007bff;
    color: white;
}

.btn-add-cart:hover {
    background-color: #0056b3;
}

.btn-remove {
    background-color: #dc3545;
    color: white;
}

.btn-remove:hover {
    background-color: #c82333;
}

/* Animations */
@keyframes heartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1); }
    75% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes countBounce {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Notification styles */
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

/* Responsive design */
@media (max-width: 768px) {
    .wishlist-notification {
        right: 10px;
        left: 10px;
        max-width: none;
    }
    
    .bulk-wishlist-controls {
        min-width: 100%;
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        height: 100vh;
        max-height: 100vh;
    }
}

@media (max-width: 480px) {
    .wishlist-dropdown-item {
        padding: 10px;
    }
    
    .wishlist-dropdown-item img {
        width: 40px;
        height: 40px;
    }
    
    .bulk-wishlist-controls {
        min-width: 100%;
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        height: 100vh;
        max-height: 100vh;
    }
    
    .bulk-actions {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .bulk-btn {
        padding: 10px 12px;
        font-size: 13px;
    }
}

/* Accessibility improvements */
.heart-button:focus,
.wishlist-btn:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

.heart-button:focus:not(:focus-visible),
.wishlist-btn:focus:not(:focus-visible) {
    outline: none;
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
</style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', wishlistStyles);
}