/**
 * Sold Out Manager
 * Handles sold out functionality for product cards
 */
class SoldOutManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.initializeSoldOutStates();
        this.bindEvents();
        this.startPeriodicRefresh();
    }
    
    /**
     * Initialize sold out states for all product cards
     */
    initializeSoldOutStates() {
        const productCards = document.querySelectorAll('.product-card');
        console.log(`SoldOutManager: Found ${productCards.length} product cards`);
        
        productCards.forEach((card, index) => {
            const productId = card.getAttribute('data-product-id');
            const stock = card.getAttribute('data-product-stock');
            const available = card.getAttribute('data-product-available');
            console.log(`Product ${index + 1} (ID: ${productId}): Stock=${stock}, Available=${available}`);
            this.updateProductCardState(card);
        });
    }
    
    /**
     * Update a single product card's sold out state
     */
    updateProductCardState(card) {
        const stock = parseInt(card.getAttribute('data-product-stock')) || 0;
        const availableAttr = card.getAttribute('data-product-available');
        const productId = card.getAttribute('data-product-id');
        
        // Use robust availability check (same as PHP logic)
        const isAvailable = (availableAttr === 'true' || availableAttr === true || availableAttr === '1' || availableAttr === 1);
        
        // Remove existing sold-out class
        card.classList.remove('sold-out');
        
        // Check if product is sold out
        if (stock <= 0 || !isAvailable) {
            this.makeProductSoldOut(card, productId);
        } else {
            this.makeProductAvailable(card, productId, stock);
        }
    }
    
    /**
     * Make a product appear as sold out
     */
    makeProductSoldOut(card, productId) {
        // Add sold-out class
        card.classList.add('sold-out');
        
        // Update button
        const addToBagBtn = card.querySelector('.add-to-bag');
        if (addToBagBtn) {
            addToBagBtn.disabled = true;
            addToBagBtn.classList.add('sold-out-btn');
            addToBagBtn.textContent = 'Sold Out';
            addToBagBtn.removeAttribute('data-product-id');
        }
        
        // Update availability text
        let availabilityDiv = card.querySelector('.product-availability');
        if (!availabilityDiv) {
            availabilityDiv = document.createElement('div');
            availabilityDiv.className = 'product-availability sold-out-text';
            card.querySelector('.product-info').appendChild(availabilityDiv);
        }
        availabilityDiv.textContent = 'SOLD OUT';
        availabilityDiv.className = 'product-availability sold-out-text';
        availabilityDiv.style.display = 'block';
        availabilityDiv.style.color = '#e53e3e';
        availabilityDiv.style.fontSize = '0.75rem';
        availabilityDiv.style.fontWeight = '600';
        
        // Disable heart button
        const heartBtn = card.querySelector('.heart-button');
        if (heartBtn) {
            heartBtn.style.opacity = '0.5';
            heartBtn.style.cursor = 'not-allowed';
        }
        
        // Update data attributes
        card.setAttribute('data-product-available', 'false');
        card.setAttribute('data-product-stock', '0');
    }
    
    /**
     * Make a product appear as available
     */
    makeProductAvailable(card, productId, stock) {
        // Remove sold-out class
        card.classList.remove('sold-out');
        
        // Update button
        const addToBagBtn = card.querySelector('.add-to-bag');
        if (addToBagBtn) {
            addToBagBtn.disabled = false;
            addToBagBtn.classList.remove('sold-out-btn');
            addToBagBtn.textContent = 'Add To Bag';
            addToBagBtn.setAttribute('data-product-id', productId);
        }
        
        // Update availability text
        let availabilityDiv = card.querySelector('.product-availability');
        if (!availabilityDiv) {
            availabilityDiv = document.createElement('div');
            availabilityDiv.className = 'product-availability';
            card.querySelector('.product-info').appendChild(availabilityDiv);
        }
        
        // Clear all sold-out styling first
        availabilityDiv.className = 'product-availability';
        availabilityDiv.style.color = '';
        availabilityDiv.style.fontSize = '';
        availabilityDiv.style.fontWeight = '';
        
        // Show stock status based on quantity
        if (stock <= 2) {
            availabilityDiv.textContent = `⚠️ Only ${stock} left in stock!`;
            availabilityDiv.className = 'product-availability low-stock';
            availabilityDiv.style.display = 'block';
        } else {
            availabilityDiv.textContent = '';
            availabilityDiv.style.display = 'none';
        }
        
        // Enable heart button
        const heartBtn = card.querySelector('.heart-button');
        if (heartBtn) {
            heartBtn.style.opacity = '1';
            heartBtn.style.cursor = 'pointer';
        }
        
        // Update data attributes
        card.setAttribute('data-product-available', 'true');
        card.setAttribute('data-product-stock', stock.toString());
    }
    
    /**
     * Start periodic refresh to check for product updates
     */
    startPeriodicRefresh() {
        // TEMPORARILY COMMENTED OUT FOR DEBUGGING
        // Refresh every 30 seconds to check for product updates
        // setInterval(() => {
        //     console.log('SoldOutManager: Starting periodic refresh...');
        //     this.refreshAllProductsFromServer();
        // }, 30000); // 30 seconds
        
        console.log('SoldOutManager: Periodic refresh TEMPORARILY DISABLED FOR DEBUGGING');
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Listen for add to cart clicks on sold out items
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('sold-out-btn')) {
                e.preventDefault();
                e.stopPropagation();
                this.showSoldOutMessage();
                return false;
            }
        });
        
        // Listen for heart button clicks on sold out items
        document.addEventListener('click', (e) => {
            if (e.target.closest('.sold-out .heart-button')) {
                e.preventDefault();
                e.stopPropagation();
                this.showSoldOutMessage();
                return false;
            }
        });
    }
    
    /**
     * Show sold out message to user
     */
    showSoldOutMessage() {
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'sold-out-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-exclamation-triangle"></i>
                <span>This item is currently sold out</span>
            </div>
        `;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '100px',
            right: '20px',
            background: '#e53e3e',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
            zIndex: '10000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease',
            fontSize: '14px',
            fontWeight: '500',
            maxWidth: '300px'
        });
        
        // Style the content
        const content = notification.querySelector('.notification-content');
        Object.assign(content.style, {
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
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
    
    /**
     * Update stock for a specific product
     */
    updateStock(productId, newStock) {
        const card = document.querySelector(`[data-product-id="${productId}"]`);
        if (card) {
            card.setAttribute('data-product-stock', newStock.toString());
            this.updateProductCardState(card);
        }
    }
    
    /**
     * Set product as sold out
     */
    setSoldOut(productId) {
        const card = document.querySelector(`[data-product-id="${productId}"]`);
        if (card) {
            card.setAttribute('data-product-stock', '0');
            card.setAttribute('data-product-available', 'false');
            this.updateProductCardState(card);
        }
    }
    
    /**
     * Set product as available
     */
    setAvailable(productId, stock = 1) {
        const card = document.querySelector(`[data-product-id="${productId}"]`);
        if (card) {
            card.setAttribute('data-product-stock', stock.toString());
            card.setAttribute('data-product-available', 'true');
            this.updateProductCardState(card);
        }
    }
    
    /**
     * Get all sold out products
     */
    getSoldOutProducts() {
        const soldOutCards = document.querySelectorAll('.product-card.sold-out');
        return Array.from(soldOutCards).map(card => ({
            id: card.getAttribute('data-product-id'),
            name: card.querySelector('.product-name')?.textContent || '',
            stock: parseInt(card.getAttribute('data-product-stock')) || 0
        }));
    }
    
    /**
     * Get all available products
     */
    getAvailableProducts() {
        const availableCards = document.querySelectorAll('.product-card:not(.sold-out)');
        return Array.from(availableCards).map(card => ({
            id: card.getAttribute('data-product-id'),
            name: card.querySelector('.product-name')?.textContent || '',
            stock: parseInt(card.getAttribute('data-product-stock')) || 0
        }));
    }

    /**
     * Refresh all product cards (useful after product updates)
     */
    refreshAllProducts() {
        console.log('SoldOutManager: Refreshing all product cards...');
        this.initializeSoldOutStates();
    }

    /**
     * Update a specific product by ID
     */
    updateProductById(productId, stock, available) {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (productCard) {
            productCard.setAttribute('data-product-stock', stock);
            productCard.setAttribute('data-product-available', available);
            this.updateProductCardState(productCard);
        }
    }
}

// Initialize sold out manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // TEMPORARILY DISABLED - was overriding available products
    // window.soldOutManager = new SoldOutManager();
    // console.log('SoldOutManager: Initialized successfully');
    console.log('SoldOutManager: TEMPORARILY DISABLED - was overriding available products');
});

// Global function to refresh sold out status (can be called from anywhere)
window.refreshSoldOutStatus = function() {
    if (window.soldOutManager) {
        window.soldOutManager.refreshAllProducts();
    } else {
        console.warn('SoldOutManager not available');
    }
};

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SoldOutManager;
}

