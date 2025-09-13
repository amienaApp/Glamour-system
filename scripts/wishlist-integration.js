/**
 * Wishlist Integration Script
 * Provides seamless wishlist functionality across all pages
 */

class WishlistIntegration {
    constructor() {
        this.isInitialized = false;
        this.init();
    }
    
    init() {
        if (this.isInitialized) return;
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupIntegration());
        } else {
            this.setupIntegration();
        }
        
        this.isInitialized = true;
    }
    
    setupIntegration() {
        this.injectWishlistButtons();
        this.setupProductCards();
        this.setupQuickViewIntegration();
        this.setupPageSpecificIntegration();
        this.setupAnalytics();
    }
    
    // Inject wishlist buttons into product cards
    injectWishlistButtons() {
        const productCards = document.querySelectorAll('.product-card:not([data-wishlist-injected])');
        
        productCards.forEach(card => {
            const productId = card.getAttribute('data-product-id');
            if (!productId) return;
            
            // Mark as injected to avoid duplicates
            card.setAttribute('data-wishlist-injected', 'true');
            
            // Find existing heart button or create one
            let heartButton = card.querySelector('.heart-button');
            if (!heartButton) {
                heartButton = this.createHeartButton(productId);
                
                // Find the best place to insert the button
                const imageContainer = card.querySelector('.product-image, .image-container');
                if (imageContainer) {
                    imageContainer.style.position = 'relative';
                    imageContainer.appendChild(heartButton);
                } else {
                    card.appendChild(heartButton);
                }
            }
            
            // Update button state
            this.updateButtonState(heartButton, productId);
        });
    }
    
    // Create heart button element
    createHeartButton(productId) {
        const button = document.createElement('button');
        button.className = 'heart-button';
        button.setAttribute('data-product-id', productId);
        button.setAttribute('aria-label', 'Add to wishlist');
        button.setAttribute('title', 'Add to wishlist');
        button.innerHTML = '<i class="far fa-heart"></i>';
        
        return button;
    }
    
    // Update button state based on wishlist
    updateButtonState(button, productId) {
        if (!window.wishlistManager) return;
        
        const isInWishlist = window.wishlistManager.isInWishlist(productId);
        const icon = button.querySelector('i');
        
        if (isInWishlist) {
            button.classList.add('active');
            button.innerHTML = '<i class="fas fa-heart" style="color: #e74c3c !important;"></i>';
            button.setAttribute('aria-label', 'Remove from wishlist');
            button.setAttribute('title', 'Remove from wishlist');
        } else {
            button.classList.remove('active');
            button.innerHTML = '<i class="far fa-heart" style="color: #666 !important;"></i>';
            button.setAttribute('aria-label', 'Add to wishlist');
            button.setAttribute('title', 'Add to wishlist');
        }
    }
    
    // Setup product cards with wishlist functionality
    setupProductCards() {
        // Add wishlist functionality to existing product cards
        document.addEventListener('click', (e) => {
            const heartButton = e.target.closest('.heart-button');
            if (heartButton && window.wishlistManager) {
                const productId = heartButton.getAttribute('data-product-id');
                if (productId) {
                    window.wishlistManager.toggleWishlist(productId, heartButton);
                }
            }
        });
        
        // Update all button states when wishlist changes
        document.addEventListener('wishlistChange', () => {
            this.updateAllButtonStates();
        });
    }
    
    // Update all wishlist button states
    updateAllButtonStates() {
        const heartButtons = document.querySelectorAll('.heart-button');
        heartButtons.forEach(button => {
            const productId = button.getAttribute('data-product-id');
            if (productId) {
                this.updateButtonState(button, productId);
            }
        });
    }
    
    // Setup quick view integration
    setupQuickViewIntegration() {
        // Listen for quick view events
        document.addEventListener('quickViewOpened', (e) => {
            this.setupQuickViewWishlist(e.detail.productId);
        });
        
        // Setup quick view wishlist button if it exists
        const quickViewWishlistBtn = document.getElementById('add-to-wishlist-quick');
        if (quickViewWishlistBtn && window.wishlistManager) {
            quickViewWishlistBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = window.wishlistManager.getQuickViewProductId();
                if (productId) {
                    window.wishlistManager.toggleQuickViewWishlist(productId, e.target);
                }
            });
        }
    }
    
    // Setup quick view wishlist functionality
    setupQuickViewWishlist(productId) {
        const quickViewWishlistBtn = document.getElementById('add-to-wishlist-quick');
        if (quickViewWishlistBtn && window.wishlistManager) {
            const isInWishlist = window.wishlistManager.isInWishlist(productId);
            
            if (isInWishlist) {
                quickViewWishlistBtn.innerHTML = '<i class="fas fa-heart" style="color: #e74c3c !important;"></i> Remove from Wishlist';
                quickViewWishlistBtn.classList.add('active');
            } else {
                quickViewWishlistBtn.innerHTML = '<i class="far fa-heart" style="color: #666 !important;"></i> Add to Wishlist';
                quickViewWishlistBtn.classList.remove('active');
            }
        }
    }
    
    // Setup page-specific integrations
    setupPageSpecificIntegration() {
        const currentPath = window.location.pathname;
        
        // Category pages
        if (currentPath.includes('/womenF/') || 
            currentPath.includes('/menfolder/') || 
            currentPath.includes('/kidsfolder/') || 
            currentPath.includes('/beautyfolder/') || 
            currentPath.includes('/perfumes/') || 
            currentPath.includes('/homedecor/') || 
            currentPath.includes('/shoess/') || 
            currentPath.includes('/accessories/') || 
            currentPath.includes('/bagsfolder/')) {
            this.setupCategoryPageIntegration();
        }
        
        // Wishlist page
        if (currentPath.includes('wishlist.php')) {
            this.setupWishlistPageIntegration();
        }
        
        // Product detail pages
        if (currentPath.includes('product-details') || currentPath.includes('product.php')) {
            this.setupProductDetailIntegration();
        }
    }
    
    // Setup category page integration
    setupCategoryPageIntegration() {
        // Add wishlist filters
        this.addWishlistFilters();
        
        // Setup bulk wishlist actions
        this.setupBulkWishlistActions();
        
        // Add wishlist sorting
        this.addWishlistSorting();
    }
    
    // Add wishlist filters
    addWishlistFilters() {
        const filterContainer = document.querySelector('.filter-container, .sidebar-filters');
        if (!filterContainer) return;
        
        const wishlistFilter = document.createElement('div');
        wishlistFilter.className = 'wishlist-filter';
        wishlistFilter.innerHTML = `
            <div class="filter-group">
                <h4>Wishlist</h4>
                <label class="filter-option">
                    <input type="checkbox" id="show-wishlist-only">
                    <span class="checkmark"></span>
                    Show wishlist items only
                </label>
            </div>
        `;
        
        filterContainer.appendChild(wishlistFilter);
        
        // Add filter functionality
        const checkbox = wishlistFilter.querySelector('#show-wishlist-only');
        checkbox.addEventListener('change', (e) => {
            this.toggleWishlistFilter(e.target.checked);
        });
    }
    
    // Toggle wishlist filter
    toggleWishlistFilter(showOnly) {
        const productCards = document.querySelectorAll('.product-card');
        
        if (!window.wishlistManager) return;
        
        productCards.forEach(card => {
            const productId = card.getAttribute('data-product-id');
            if (productId) {
                const isInWishlist = window.wishlistManager.isInWishlist(productId);
                
                if (showOnly) {
                    card.style.display = isInWishlist ? 'block' : 'none';
                } else {
                    card.style.display = 'block';
                }
            }
        });
    }
    
    // Setup bulk wishlist actions
    setupBulkWishlistActions() {
        const actionBar = document.querySelector('.action-bar, .bulk-actions');
        if (!actionBar) return;
        
        const wishlistActions = document.createElement('div');
        wishlistActions.className = 'wishlist-bulk-actions';
        wishlistActions.innerHTML = `
            <button class="btn-wishlist-all" onclick="addAllToWishlist()">
                <i class="fas fa-heart"></i> Add All to Wishlist
            </button>
            <button class="btn-wishlist-selected" onclick="addSelectedToWishlist()">
                <i class="fas fa-heart"></i> Add Selected to Wishlist
            </button>
        `;
        
        actionBar.appendChild(wishlistActions);
    }
    
    // Add wishlist sorting
    addWishlistSorting() {
        const sortContainer = document.querySelector('.sort-container, .sort-options');
        if (!sortContainer) return;
        
        const wishlistSort = document.createElement('option');
        wishlistSort.value = 'wishlist';
        wishlistSort.textContent = 'Wishlist Items First';
        
        const sortSelect = sortContainer.querySelector('select');
        if (sortSelect) {
            sortSelect.appendChild(wishlistSort);
            
            sortSelect.addEventListener('change', (e) => {
                if (e.target.value === 'wishlist') {
                    this.sortByWishlist();
                }
            });
        }
    }
    
    // Sort products by wishlist status
    sortByWishlist() {
        const productGrid = document.querySelector('.product-grid, .products-container');
        if (!productGrid || !window.wishlistManager) return;
        
        const productCards = Array.from(productGrid.querySelectorAll('.product-card'));
        
        productCards.sort((a, b) => {
            const aId = a.getAttribute('data-product-id');
            const bId = b.getAttribute('data-product-id');
            const aInWishlist = window.wishlistManager.isInWishlist(aId);
            const bInWishlist = window.wishlistManager.isInWishlist(bId);
            
            if (aInWishlist && !bInWishlist) return -1;
            if (!aInWishlist && bInWishlist) return 1;
            return 0;
        });
        
        productCards.forEach(card => productGrid.appendChild(card));
    }
    
    // Setup wishlist page integration
    setupWishlistPageIntegration() {
        // Add export/import functionality (disabled)
        // this.addWishlistExportImport();
        
        // Add wishlist sharing (disabled)
        // this.addWishlistSharing();
        
        // Add wishlist analytics (disabled - using built-in stats)
        // this.addWishlistAnalytics();
    }
    
    // Add export/import functionality
    addWishlistExportImport() {
        const wishlistActions = document.querySelector('.wishlist-actions');
        if (!wishlistActions) return;
        
        const exportImportDiv = document.createElement('div');
        exportImportDiv.className = 'wishlist-export-import';
        exportImportDiv.innerHTML = `
            <button class="btn-export" onclick="exportWishlist()">
                <i class="fas fa-download"></i> Export
            </button>
            <label class="btn-import">
                <i class="fas fa-upload"></i> Import
                <input type="file" accept=".json" onchange="importWishlist(this.files[0])" style="display: none;">
            </label>
        `;
        
        wishlistActions.appendChild(exportImportDiv);
    }
    
    // Add wishlist sharing
    addWishlistSharing() {
        const wishlistHeader = document.querySelector('.wishlist-header');
        if (!wishlistHeader) return;
        
        const shareButton = document.createElement('button');
        shareButton.className = 'btn-share-wishlist';
        shareButton.innerHTML = '<i class="fas fa-share-alt"></i> Share Wishlist';
        shareButton.onclick = () => this.shareWishlist();
        
        wishlistHeader.appendChild(shareButton);
    }
    
    // Share wishlist
    shareWishlist() {
        if (!window.wishlistManager) return;
        
        const wishlist = window.wishlistManager.getWishlist();
        const wishlistText = wishlist.map(item => `${item.name} - $${item.price}`).join('\n');
        const shareText = `Check out my wishlist from Glamour Palace:\n\n${wishlistText}`;
        
        if (navigator.share) {
            navigator.share({
                title: 'My Wishlist',
                text: shareText,
                url: window.location.href
            });
        } else {
            // Fallback to clipboard
            navigator.clipboard.writeText(shareText).then(() => {
                alert('Wishlist copied to clipboard!');
            });
        }
    }
    
    // Add wishlist analytics
    addWishlistAnalytics() {
        if (!window.wishlistManager) return;
        
        const stats = window.wishlistManager.getWishlistStats();
        const analyticsDiv = document.createElement('div');
        analyticsDiv.className = 'wishlist-analytics';
        analyticsDiv.innerHTML = `
            <div class="analytics-stats">
                <div class="stat-item">
                    <span class="stat-number">${stats.totalItems}</span>
                    <span class="stat-label">Items</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">$${stats.totalValue.toFixed(2)}</span>
                    <span class="stat-label">Total Value</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">${Object.keys(stats.categories).length}</span>
                    <span class="stat-label">Categories</span>
                </div>
            </div>
        `;
        
        const wishlistContainer = document.querySelector('.wishlist-container');
        if (wishlistContainer) {
            wishlistContainer.insertBefore(analyticsDiv, wishlistContainer.firstChild);
        }
    }
    
    // Setup product detail integration
    setupProductDetailIntegration() {
        // Add wishlist button to product detail page
        const productDetail = document.querySelector('.product-detail, .product-info');
        if (productDetail) {
            const wishlistButton = document.createElement('button');
            wishlistButton.className = 'btn-wishlist-detail';
            wishlistButton.innerHTML = '<i class="fas fa-heart"></i> Add to Wishlist';
            wishlistButton.onclick = () => {
                const productId = document.querySelector('[data-product-id]')?.getAttribute('data-product-id');
                if (productId && window.wishlistManager) {
                    window.wishlistManager.toggleWishlist(productId, wishlistButton);
                }
            };
            
            productDetail.appendChild(wishlistButton);
        }
    }
    
    // Setup analytics tracking
    setupAnalytics() {
        // Track wishlist events
        document.addEventListener('wishlistChange', (e) => {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'wishlist_' + e.detail.type, {
                    'event_category': 'engagement',
                    'event_label': e.detail.productId,
                    'value': e.detail.count
                });
            }
            
            // Track with other analytics services
            if (typeof fbq !== 'undefined') {
                fbq('track', 'AddToWishlist', {
                    content_ids: [e.detail.productId],
                    content_type: 'product'
                });
            }
        });
    }
}

// Global functions for bulk actions
window.addAllToWishlist = function() {
    if (!window.wishlistManager) return;
    
    const productCards = document.querySelectorAll('.product-card');
    let addedCount = 0;
    
    productCards.forEach(card => {
        const productId = card.getAttribute('data-product-id');
        if (productId && !window.wishlistManager.isInWishlist(productId)) {
            const heartButton = card.querySelector('.heart-button');
            if (heartButton) {
                window.wishlistManager.toggleWishlist(productId, heartButton);
                addedCount++;
            }
        }
    });
    
    if (addedCount > 0) {
        alert(`${addedCount} items added to wishlist!`);
    } else {
        alert('All visible items are already in your wishlist!');
    }
};

window.addSelectedToWishlist = function() {
    if (!window.wishlistManager) return;
    
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    let addedCount = 0;
    
    selectedCheckboxes.forEach(checkbox => {
        const productCard = checkbox.closest('.product-card');
        const productId = productCard?.getAttribute('data-product-id');
        if (productId && !window.wishlistManager.isInWishlist(productId)) {
            const heartButton = productCard.querySelector('.heart-button');
            if (heartButton) {
                window.wishlistManager.toggleWishlist(productId, heartButton);
                addedCount++;
            }
        }
    });
    
    if (addedCount > 0) {
        alert(`${addedCount} selected items added to wishlist!`);
    } else {
        alert('No new items to add to wishlist!');
    }
};

// Initialize wishlist integration
new WishlistIntegration();

// Add CSS for wishlist integration
const integrationStyles = `
<style>
.wishlist-filter {
    margin: 15px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.wishlist-filter h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.wishlist-bulk-actions {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}

.btn-wishlist-all,
.btn-wishlist-selected {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.2s ease;
}

.btn-wishlist-all:hover,
.btn-wishlist-selected:hover {
    background: #c0392b;
}

.wishlist-export-import {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}

.btn-export,
.btn-import {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.2s ease;
}

.btn-export:hover,
.btn-import:hover {
    background: #5a6268;
}

.btn-share-wishlist {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s ease;
}

.btn-share-wishlist:hover {
    background: #0056b3;
}

.wishlist-analytics {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.analytics-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
}

.stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-wishlist-detail {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.2s ease;
    margin: 10px 0;
}

.btn-wishlist-detail:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

.btn-wishlist-detail.active {
    background: #27ae60;
}

.btn-wishlist-detail.active:hover {
    background: #229954;
}

@media (max-width: 768px) {
    .wishlist-bulk-actions,
    .wishlist-export-import {
        flex-direction: column;
    }
    
    .analytics-stats {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', integrationStyles);
