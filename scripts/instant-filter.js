/**
 * Instant Filter System - No Page Reloads
 * Provides real-time filtering without page refreshes
 */

class InstantFilter {
    constructor() {
        this.originalProducts = [];
        this.filteredProducts = [];
        this.currentFilters = {
            category: [],
            color: [],
            size: [],
            price: [],
            onSale: false
        };
        this.init();
    }

    init() {
        this.loadOriginalProducts();
        this.bindEvents();
        this.updateProductDisplay();
    }

    loadOriginalProducts() {
        const productCards = document.querySelectorAll('.product-card');
        this.originalProducts = Array.from(productCards).map(card => ({
            element: card,
            id: card.getAttribute('data-product-id'),
            name: card.getAttribute('data-product-name'),
            price: parseFloat(card.getAttribute('data-product-price')),
            salePrice: card.getAttribute('data-product-sale-price'),
            category: card.getAttribute('data-product-category'),
            subcategory: card.getAttribute('data-product-subcategory'),
            color: card.getAttribute('data-product-color'),
            sizes: JSON.parse(card.getAttribute('data-product-sizes') || '[]'),
            onSale: card.getAttribute('data-product-on-sale') === 'true',
            featured: card.getAttribute('data-product-featured') === 'true'
        }));
        this.filteredProducts = [...this.originalProducts];
    }

    bindEvents() {
        // Category filters
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[data-filter="category"]')) {
                this.updateCategoryFilter(e.target.value, e.target.checked);
            }
        });

        // Color filters
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[data-filter="color"]')) {
                this.updateColorFilter(e.target.value, e.target.checked);
            }
        });

        // Size filters
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[data-filter="size"]')) {
                this.updateSizeFilter(e.target.value, e.target.checked);
            }
        });

        // Price filters
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[data-filter="price_range"]')) {
                this.updatePriceFilter(e.target.value, e.target.checked);
            }
        });

        // Clear all filters
        document.addEventListener('click', (e) => {
            if (e.target.matches('#clear-filters, .clear-filters-btn')) {
                e.preventDefault();
                this.clearAllFilters();
            }
        });
    }

    updateCategoryFilter(category, isChecked) {
        if (isChecked) {
            this.currentFilters.category.push(category);
        } else {
            this.currentFilters.category = this.currentFilters.category.filter(c => c !== category);
        }
        this.applyFilters();
    }

    updateColorFilter(color, isChecked) {
        if (isChecked) {
            this.currentFilters.color.push(color);
        } else {
            this.currentFilters.color = this.currentFilters.color.filter(c => c !== color);
        }
        this.applyFilters();
    }

    updateSizeFilter(size, isChecked) {
        if (isChecked) {
            this.currentFilters.size.push(size);
        } else {
            this.currentFilters.size = this.currentFilters.size.filter(s => s !== size);
        }
        this.applyFilters();
    }

    updatePriceFilter(priceRange, isChecked) {
        if (isChecked) {
            if (priceRange === 'on-sale') {
                this.currentFilters.onSale = true;
            } else {
                this.currentFilters.price.push(priceRange);
            }
        } else {
            if (priceRange === 'on-sale') {
                this.currentFilters.onSale = false;
            } else {
                this.currentFilters.price = this.currentFilters.price.filter(p => p !== priceRange);
            }
        }
        this.applyFilters();
    }

    applyFilters() {
        this.filteredProducts = this.originalProducts.filter(product => {
            // Category filter
            if (this.currentFilters.category.length > 0) {
                const categoryMatch = this.currentFilters.category.some(filterCategory => {
                    return product.subcategory && product.subcategory.toLowerCase().includes(filterCategory.toLowerCase());
                });
                if (!categoryMatch) return false;
            }

            // Color filter
            if (this.currentFilters.color.length > 0) {
                if (!this.currentFilters.color.includes(product.color)) return false;
            }

            // Size filter
            if (this.currentFilters.size.length > 0) {
                const sizeMatch = this.currentFilters.size.some(filterSize => {
                    return product.sizes && product.sizes.includes(filterSize);
                });
                if (!sizeMatch) return false;
            }

            // Price filter
            if (this.currentFilters.price.length > 0) {
                const priceMatch = this.currentFilters.price.some(priceRange => {
                    return this.isPriceInRange(product.price, priceRange);
                });
                if (!priceMatch) return false;
            }

            // On sale filter
            if (this.currentFilters.onSale && !product.onSale) {
                return false;
            }

            return true;
        });

        this.updateProductDisplay();
        this.updateProductCount();
    }

    isPriceInRange(price, range) {
        switch (range) {
            case '0-25':
                return price >= 0 && price <= 25;
            case '25-50':
                return price >= 25 && price <= 50;
            case '50-75':
                return price >= 50 && price <= 75;
            case '75-100':
                return price >= 75 && price <= 100;
            case '100+':
                return price >= 100;
            default:
                return true;
        }
    }

    updateProductDisplay() {
        const productGrid = document.querySelector('.product-grid, #all-products-grid, #filtered-products-grid');
        if (!productGrid) return;

        // Hide all products first
        this.originalProducts.forEach(product => {
            product.element.style.display = 'none';
        });

        // Show filtered products
        this.filteredProducts.forEach(product => {
            product.element.style.display = 'block';
        });

        // Add smooth transition effect
        productGrid.style.opacity = '0.7';
        setTimeout(() => {
            productGrid.style.opacity = '1';
        }, 150);
    }

    updateProductCount() {
        const countElement = document.getElementById('style-count');
        if (countElement) {
            const currentText = countElement.textContent;
            const baseText = currentText.replace(/\d+/, '').trim();
            countElement.textContent = `${this.filteredProducts.length} ${baseText}`;
        }
    }

    clearAllFilters() {
        // Reset all checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Reset filter state
        this.currentFilters = {
            category: [],
            color: [],
            size: [],
            price: [],
            onSale: false
        };

        // Show all products
        this.filteredProducts = [...this.originalProducts];
        this.updateProductDisplay();
        this.updateProductCount();
    }
}

// Initialize instant filtering when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on a category page with products
    if (document.querySelector('.product-card')) {
        window.instantFilter = new InstantFilter();
    }
});

// Override the old filter functions to use instant filtering
function updateCategoryFilter(category, isChecked) {
    if (window.instantFilter) {
        window.instantFilter.updateCategoryFilter(category, isChecked);
    }
}

function updateColorFilter(color, isChecked) {
    if (window.instantFilter) {
        window.instantFilter.updateColorFilter(color, isChecked);
    }
}

function updateSizeFilter(size, isChecked) {
    if (window.instantFilter) {
        window.instantFilter.updateSizeFilter(size, isChecked);
    }
}

function updatePriceFilter(minPrice, maxPrice, isChecked) {
    if (window.instantFilter) {
        const priceRange = minPrice === 'on-sale' ? 'on-sale' : `${minPrice}-${maxPrice || 'max'}`;
        window.instantFilter.updatePriceFilter(priceRange, isChecked);
    }
}

function clearAllFiltersSimple() {
    if (window.instantFilter) {
        window.instantFilter.clearAllFilters();
    }
}

// Make functions globally available
window.updateCategoryFilter = updateCategoryFilter;
window.updateColorFilter = updateColorFilter;
window.updateSizeFilter = updateSizeFilter;
window.updatePriceFilter = updatePriceFilter;
window.clearAllFiltersSimple = clearAllFiltersSimple;
