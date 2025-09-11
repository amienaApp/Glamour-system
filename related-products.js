/**
 * Related Products Manager
 * Handles related products functionality
 */

class RelatedProductsManager {
    constructor() {
        this.relatedProducts = new Map();
        this.initialized = false;
    }

    /**
     * Initialize the related products manager
     */
    init() {
        if (this.initialized) return;
        
        console.log('Related Products Manager initialized');
        this.initialized = true;
    }

    /**
     * Load related products for a product
     * @param {string} productId - The product ID
     * @param {string} category - The product category
     * @param {string} subcategory - The product subcategory
     * @returns {Promise<Array>} Array of related products
     */
    async loadRelatedProducts(productId, category = '', subcategory = '') {
        try {
            // For now, return mock data
            // In a real implementation, this would fetch from an API
            const mockRelatedProducts = [
                {
                    _id: 'related1',
                    name: 'Related Product 1',
                    price: 25.99,
                    front_image: 'img/beauty/related1.jpg',
                    category: category,
                    subcategory: subcategory
                },
                {
                    _id: 'related2',
                    name: 'Related Product 2',
                    price: 19.99,
                    front_image: 'img/beauty/related2.jpg',
                    category: category,
                    subcategory: subcategory
                },
                {
                    _id: 'related3',
                    name: 'Related Product 3',
                    price: 35.99,
                    front_image: 'img/beauty/related3.jpg',
                    category: category,
                    subcategory: subcategory
                }
            ];

            this.relatedProducts.set(productId, mockRelatedProducts);
            return mockRelatedProducts;
        } catch (error) {
            console.error('Error loading related products:', error);
            return [];
        }
    }

    /**
     * Get related products for a product
     * @param {string} productId - The product ID
     * @returns {Array} Array of related products
     */
    getRelatedProducts(productId) {
        return this.relatedProducts.get(productId) || [];
    }

    /**
     * Clear related products cache
     */
    clearCache() {
        this.relatedProducts.clear();
    }
}

// Create global instance
window.relatedProductsManager = new RelatedProductsManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.relatedProductsManager.init();
});



