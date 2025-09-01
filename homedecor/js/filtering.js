/**
 * Home Decor Filtering System
 * Handles all sidebar filtering functionality
 */

class HomeDecorFilter {
    constructor() {
        this.filters = {
            category: [],
            price: [],
            color: [],
            material: [],
            availability: []
        };
        
        this.allProducts = [];
        this.filteredProducts = [];
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadAllProducts();
        this.updateStyleCount();
        this.initializeDefaultFilters();
    }
    
    setupEventListeners() {
        // Category filter checkboxes
        document.querySelectorAll('input[data-filter="category"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleFilterChange('category', checkbox));
        });
        
        // Price filter checkboxes
        document.querySelectorAll('input[data-filter="price"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleFilterChange('price', checkbox));
        });
        
        // Color filter checkboxes
        document.querySelectorAll('input[data-filter="color"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleFilterChange('color', checkbox));
        });
        
        // Material filter checkboxes
        document.querySelectorAll('input[data-filter="material"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleFilterChange('material', checkbox));
        });
        
        // Availability filter checkboxes
        document.querySelectorAll('input[data-filter="availability"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleFilterChange('availability', checkbox));
        });
        
        // Clear all filters button
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => this.clearAllFilters());
        }
    }
    
    loadAllProducts() {
        // Get all product cards from both grids
        const filteredGrid = document.getElementById('filtered-products-grid');
        const allProductsGrid = document.getElementById('all-home-decor-grid');
        
        if (filteredGrid) {
            this.allProducts = Array.from(filteredGrid.querySelectorAll('.product-card'));
            console.log('Found products in filtered grid:', this.allProducts.length);
        } else if (allProductsGrid) {
            this.allProducts = Array.from(allProductsGrid.querySelectorAll('.product-card'));
            console.log('Found products in all products grid:', this.allProducts.length);
        } else {
            console.log('No product grids found');
        }
        
        this.filteredProducts = [...this.allProducts];
        
        // Debug: Log product data
        if (this.allProducts.length > 0) {
            console.log('Sample product data:', {
                id: this.allProducts[0].dataset.productId,
                category: this.allProducts[0].dataset.category,
                price: this.allProducts[0].dataset.price,
                color: this.allProducts[0].dataset.color,
                material: this.allProducts[0].dataset.material,
                availability: this.allProducts[0].dataset.availability
            });
        }
    }
    
    handleFilterChange(filterType, checkbox) {
        const value = checkbox.value;
        const isChecked = checkbox.checked;
        
        if (isChecked) {
            if (value === '') {
                // If "All" option is selected, clear this filter
                this.filters[filterType] = [];
            } else {
                // For radio buttons, replace the entire array with just this value
                this.filters[filterType] = [value];
            }
        }
        
        this.applyFilters();
    }
    
    applyFilters() {
        this.filteredProducts = this.allProducts.filter(product => {
            return this.productMatchesFilters(product);
        });
        
        this.updateProductDisplay();
        this.updateStyleCount();
    }
    
    productMatchesFilters(product) {
        // Category filter
        if (this.filters.category.length > 0) {
            const productCategory = product.dataset.category;
            if (!this.filters.category.includes(productCategory)) {
                return false;
            }
        }
        
        // Price filter
        if (this.filters.price.length > 0) {
            const productPrice = parseFloat(product.dataset.price);
            let priceMatch = false;
            
            for (const priceRange of this.filters.price) {
                if (this.priceInRange(productPrice, priceRange)) {
                    priceMatch = true;
                    break;
                }
            }
            
            if (!priceMatch) {
                return false;
            }
        }
        
        // Color filter
        if (this.filters.color.length > 0) {
            const productColor = product.dataset.color;
            if (!this.filters.color.includes(productColor)) {
                return false;
            }
        }
        
        // Material filter
        if (this.filters.material.length > 0) {
            const productMaterial = product.dataset.material;
            if (!this.filters.material.includes(productMaterial)) {
                return false;
            }
        }
        
        // Availability filter
        if (this.filters.availability.length > 0) {
            const productAvailability = product.dataset.availability;
            const productSale = product.dataset.sale;
            const productNew = product.dataset.new;
            
            let availabilityMatch = false;
            
            for (const availability of this.filters.availability) {
                if (availability === 'in-stock' && productAvailability === 'in-stock') {
                    availabilityMatch = true;
                    break;
                } else if (availability === 'on-sale' && productSale === 'on-sale') {
                    availabilityMatch = true;
                    break;
                } else if (availability === 'new-arrival' && productNew === 'new-arrival') {
                    availabilityMatch = true;
                    break;
                }
            }
            
            if (!availabilityMatch) {
                return false;
            }
        }
        
        return true;
    }
    
    priceInRange(price, range) {
        if (range === '900+') {
            return price >= 900;
        }
        
        const [min, max] = range.split('-').map(Number);
        return price >= min && price <= max;
    }
    
    updateProductDisplay() {
        // Hide all products first
        this.allProducts.forEach(product => {
            product.style.display = 'none';
        });
        
        // Show filtered products
        this.filteredProducts.forEach(product => {
            product.style.display = 'block';
        });
        
        // Show "no products" message if no results
        this.showNoProductsMessage();
    }
    
    showNoProductsMessage() {
        // Remove existing no products message
        const existingMessage = document.querySelector('.no-filtered-products');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        if (this.filteredProducts.length === 0) {
            const message = document.createElement('div');
            message.className = 'no-filtered-products';
            message.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>No products match your filters</h3>
                    <p>Try adjusting your filter criteria or <button onclick="homeDecorFilter.clearAllFilters()" style="background: none; border: none; color: #007bff; text-decoration: underline; cursor: pointer;">clear all filters</button></p>
                </div>
            `;
            
            // Insert message into the appropriate grid
            const filteredGrid = document.getElementById('filtered-products-grid');
            const allProductsGrid = document.getElementById('all-home-decor-grid');
            
            if (filteredGrid) {
                filteredGrid.appendChild(message);
            } else if (allProductsGrid) {
                allProductsGrid.appendChild(message);
            }
        }
    }
    
    updateStyleCount() {
        const styleCountElement = document.getElementById('style-count');
        if (styleCountElement) {
            const count = this.filteredProducts.length;
            styleCountElement.textContent = `${count} Style${count !== 1 ? 's' : ''}`;
        }
    }
    
    clearAllFilters() {
        // Uncheck all filter radio buttons and checkboxes
        document.querySelectorAll('input[data-filter]').forEach(input => {
            input.checked = false;
        });
        
        // Check the "All" options (empty values) by default
        document.querySelectorAll('input[data-filter][value=""]').forEach(input => {
            input.checked = true;
        });
        
        // Reset filters object
        this.filters = {
            category: [],
            price: [],
            color: [],
            material: [],
            availability: []
        };
        
        // Show all products
        this.filteredProducts = [...this.allProducts];
        this.updateProductDisplay();
        this.updateStyleCount();
        
        // Remove no products message
        const existingMessage = document.querySelector('.no-filtered-products');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
    
    // Public method to get current filters
    getCurrentFilters() {
        return this.filters;
    }
    
    // Public method to get filtered products count
    getFilteredCount() {
        return this.filteredProducts.length;
    }
    
    initializeDefaultFilters() {
        // Check the "All" options (empty values) by default
        document.querySelectorAll('input[data-filter][value=""]').forEach(input => {
            input.checked = true;
        });
    }
}

// Initialize the filter system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.homeDecorFilter = new HomeDecorFilter();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomeDecorFilter;
}
