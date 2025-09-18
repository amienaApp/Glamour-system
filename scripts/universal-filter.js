// Universal Filter System for All Category Pages
// This script provides a consistent filtering experience across all product category pages

class UniversalFilter {
    constructor(category, filterApiPath = 'filter-api.php') {
        this.category = category;
        this.filterApiPath = filterApiPath;
        this.currentFilters = {
            gender: '',
            category: '',
            color: '',
            minPrice: '',
            maxPrice: ''
        };
        
        this.init();
    }
    
    init() {
        // Initialize filter state from URL parameters
        this.loadFiltersFromURL();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Make functions globally accessible
        this.makeGlobalFunctions();
    }
    
    loadFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentFilters.gender = urlParams.get('gender') || '';
        this.currentFilters.category = urlParams.get('category') || '';
        this.currentFilters.color = urlParams.get('color') || '';
        this.currentFilters.minPrice = urlParams.get('min_price') || '';
        this.currentFilters.maxPrice = urlParams.get('max_price') || '';
    }
    
    setupEventListeners() {
        // Set up filter checkbox event listeners
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[name="gender[]"]')) {
                this.updateGenderFilter(e.target.value, e.target.checked);
            } else if (e.target.matches('input[name="category[]"]')) {
                this.updateCategoryFilter(e.target.value, e.target.checked);
            } else if (e.target.matches('input[name="color[]"]')) {
                this.updateColorFilter(e.target.value, e.target.checked);
            } else if (e.target.matches('input[name="price[]"]')) {
                this.handlePriceFilterChange(e.target);
            }
        });
        
        // Set up clear filters button
        document.addEventListener('click', (e) => {
            if (e.target.matches('#clear-filters') || e.target.closest('#clear-filters')) {
                e.preventDefault();
                this.clearAllFilters();
            }
        });
    }
    
    updateGenderFilter(gender, checked) {
        if (checked) {
            this.currentFilters.gender = gender;
            // Uncheck other gender options
            document.querySelectorAll('input[name="gender[]"]').forEach(input => {
                if (input.value !== gender) {
                    input.checked = false;
                }
            });
        } else {
            this.currentFilters.gender = '';
        }
        this.applyFilters();
    }
    
    updateCategoryFilter(category, checked) {
        if (checked) {
            this.currentFilters.category = category;
            // Uncheck other category options
            document.querySelectorAll('input[name="category[]"]').forEach(input => {
                if (input.value !== category) {
                    input.checked = false;
                }
            });
        } else {
            this.currentFilters.category = '';
        }
        this.applyFilters();
    }
    
    updateColorFilter(color, checked) {
        if (checked) {
            this.currentFilters.color = color;
            // Uncheck other color options
            document.querySelectorAll('input[name="color[]"]').forEach(input => {
                if (input.value !== color) {
                    input.checked = false;
                }
            });
        } else {
            this.currentFilters.color = '';
        }
        this.applyFilters();
    }
    
    handlePriceFilterChange(input) {
        const value = input.value;
        let minPrice = '';
        let maxPrice = '';
        
        if (value === '0-100') {
            minPrice = 0;
            maxPrice = 100;
        } else if (value === '100-200') {
            minPrice = 100;
            maxPrice = 200;
        } else if (value === '200-400') {
            minPrice = 200;
            maxPrice = 400;
        } else if (value === '400+') {
            minPrice = 400;
            maxPrice = null;
        }
        
        this.updatePriceFilter(minPrice, maxPrice, input.checked);
    }
    
    updatePriceFilter(minPrice, maxPrice, checked) {
        if (checked) {
            this.currentFilters.minPrice = minPrice;
            this.currentFilters.maxPrice = maxPrice;
            // Uncheck other price options
            document.querySelectorAll('input[name="price[]"]').forEach(input => {
                const inputValue = input.value;
                const expectedValue = maxPrice ? `${minPrice}-${maxPrice}` : `${minPrice}+`;
                if (inputValue !== expectedValue) {
                    input.checked = false;
                }
            });
        } else {
            this.currentFilters.minPrice = '';
            this.currentFilters.maxPrice = '';
        }
        this.applyFilters();
    }
    
    applyFilters() {
        console.log('Applying filters:', this.currentFilters);
        
        // Show loading state
        this.showFilterLoading();
        
        // Prepare filter data for API
        const filterData = {
            action: 'filter_products',
            subcategory: '',
            sizes: [],
            colors: [],
            price_ranges: [],
            categories: [],
            lengths: []
        };
        
        // Add gender filter
        if (this.currentFilters.gender) {
            filterData.gender = this.currentFilters.gender;
        }
        
        // Add category filter
        if (this.currentFilters.category) {
            filterData.categories = [this.currentFilters.category];
        }
        
        // Add color filter
        if (this.currentFilters.color) {
            filterData.colors = [this.currentFilters.color];
        }
        
        // Add price filter
        if (this.currentFilters.minPrice !== '') {
            if (this.currentFilters.maxPrice !== '') {
                filterData.price_ranges = [`${this.currentFilters.minPrice}-${this.currentFilters.maxPrice}`];
            } else {
                filterData.price_ranges = [`${this.currentFilters.minPrice}+`];
            }
        }
        
        // Make AJAX request to filter API
        fetch(this.filterApiPath, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filterData)
        })
        .then(response => {
            console.log('Filter response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Filter response:', data);
            
            if (data.success) {
                console.log('Successfully filtered products, count:', data.data.products.length);
                this.updateProductGrid(data.data.products);
                this.updateStyleCount(data.data.total_count);
                this.hideFilterLoading();
            } else {
                console.error('Filter error:', data.message);
                this.hideFilterLoading();
                this.showFilterError(data.message);
            }
        })
        .catch(error => {
            console.error('Filter request error:', error);
            this.hideFilterLoading();
            this.showFilterError('Network error occurred: ' + error.message);
        });
    }
    
    clearAllFilters() {
        console.log('Clearing all filters...');
        
        // Reset all filter checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset filter state
        this.currentFilters = {
            gender: '',
            category: '',
            color: '',
            minPrice: '',
            maxPrice: ''
        };
        
        // Show loading state
        this.showFilterLoading();
        
        // Make AJAX request to get all products
        fetch(this.filterApiPath, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'filter_products',
                subcategory: '',
                sizes: [],
                colors: [],
                price_ranges: [],
                categories: [],
                lengths: []
            })
        })
        .then(response => {
            console.log('Clear filters response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Clear filters response:', data);
            
            if (data.success) {
                console.log('Successfully cleared filters, products count:', data.data.products.length);
                this.updateProductGrid(data.data.products);
                this.updateStyleCount(data.data.total_count);
                this.hideFilterLoading();
            } else {
                console.error('Clear filters error:', data.message);
                this.hideFilterLoading();
                this.showFilterError(data.message);
            }
        })
        .catch(error => {
            console.error('Clear filters request error:', error);
            this.hideFilterLoading();
            this.showFilterError('Network error occurred: ' + error.message);
            
            // Fallback: reload the page to show all products
            console.log('Falling back to page reload...');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }
    
    showFilterLoading() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.add('filter-loading');
        }
        
        // Show loading indicator in product grid
        const productGrid = this.getProductGrid();
        if (productGrid) {
            productGrid.innerHTML = '<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading products...</div>';
        }
    }
    
    hideFilterLoading() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('filter-loading');
        }
    }
    
    showFilterError(message) {
        console.error('Filter error:', message);
        const productGrid = this.getProductGrid();
        if (productGrid) {
            productGrid.innerHTML = `<div class="error-message"><i class="fas fa-exclamation-triangle"></i> ${message}</div>`;
        }
    }
    
    getProductGrid() {
        return document.getElementById('all-products-grid') || 
               document.getElementById('filtered-products-grid') ||
               document.getElementById('all-bags-grid') ||
               document.getElementById('all-beauty-grid') ||
               document.getElementById('all-shoes-grid') ||
               document.getElementById('all-men-grid') ||
               document.getElementById('all-kids-grid') ||
               document.querySelector('.product-grid');
    }
    
    updateProductGrid(products) {
        console.log(`Updating product grid with ${products.length} products`);
        
        const productGrid = this.getProductGrid();
        if (!productGrid) {
            console.error('Product grid not found');
            return;
        }
        
        console.log('Found product grid:', productGrid);
        
        // Clear existing products
        productGrid.innerHTML = '';
        
        if (products.length === 0) {
            productGrid.innerHTML = '<div class="no-products"><p>No products found matching your filters.</p></div>';
            return;
        }
        
        // Generate product cards
        products.forEach(product => {
            const productCard = this.createProductCard(product);
            productGrid.appendChild(productCard);
        });
        
        console.log(`Successfully added ${products.length} products to grid`);
    }
    
    createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.setAttribute('data-product-id', product.id);
        card.setAttribute('data-product-sizes', JSON.stringify(product.sizes || []));
        card.setAttribute('data-product-selected-sizes', JSON.stringify(product.selected_sizes || []));
        card.setAttribute('data-product-variants', JSON.stringify(product.color_variants || []));
        card.setAttribute('data-product-options', JSON.stringify(product.options || []));
        
        const frontImage = product.front_image || '';
        const backImage = product.back_image || frontImage;
        const price = product.sale && product.salePrice ? product.salePrice : product.price;
        const originalPrice = product.sale ? product.price : null;
        
        card.innerHTML = `
            <div class="product-image">
                <div class="image-slider">
                    ${frontImage ? `
                        <img src="../${frontImage}" 
                             alt="${product.name} - Front" 
                             class="active" 
                             data-color="${product.color}">
                    ` : ''}
                    ${backImage && backImage !== frontImage ? `
                        <img src="../${backImage}" 
                             alt="${product.name} - Back" 
                             data-color="${product.color}">
                    ` : ''}
                </div>
                <button class="heart-button" data-product-id="${product.id}">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="${product.id}">Quick View</button>
                    ${product.available === false ? 
                        '<button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>' :
                        '<button class="add-to-bag">Add To Bag</button>'
                    }
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    ${product.color ? `
                        <span class="color-circle active" 
                              style="background-color: ${product.color};" 
                              title="${product.color}" 
                              data-color="${product.color}"></span>
                    ` : ''}
                </div>
                <h3 class="product-name">${product.name}</h3>
                <div class="product-price">
                    ${originalPrice ? `
                        <span class="sale-price">$${price.toFixed(0)}</span>
                        <span class="original-price">$${originalPrice.toFixed(0)}</span>
                    ` : `$${price.toFixed(0)}`}
                </div>
                ${product.available === false ? 
                    '<div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>' :
                    (product.stock && product.stock <= 5 && product.stock > 0 ? 
                        `<div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only ${product.stock} left</div>` : '')
                }
            </div>
        `;
        
        return card;
    }
    
    updateStyleCount(count) {
        const styleCountElement = document.querySelector('.style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${count} Styles`;
        }
    }
    
    makeGlobalFunctions() {
        // Make the functions globally accessible for backward compatibility
        window.clearAllFilters = () => this.clearAllFilters();
        window.updateGenderFilter = (gender, checked) => this.updateGenderFilter(gender, checked);
        window.updateCategoryFilter = (category, checked) => this.updateCategoryFilter(category, checked);
        window.updateColorFilter = (color, checked) => this.updateColorFilter(color, checked);
        window.updatePriceFilter = (minPrice, maxPrice, checked) => this.updatePriceFilter(minPrice, maxPrice, checked);
        window.applyFilters = () => this.applyFilters();
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Determine category from current page
    let category = 'Products';
    let filterApiPath = 'filter-api.php';
    
    // Detect category from URL or page structure
    if (window.location.pathname.includes('/bagsfolder/')) {
        category = 'Bags';
        filterApiPath = 'filter-api.php';
    } else if (window.location.pathname.includes('/beautyfolder/')) {
        category = 'Beauty & Cosmetics';
        filterApiPath = 'filter-api.php';
    } else if (window.location.pathname.includes('/shoess/')) {
        category = 'Shoes';
        filterApiPath = 'filter-api.php';
    } else if (window.location.pathname.includes('/menfolder/')) {
        category = 'Men';
        filterApiPath = 'filter-api.php';
    } else if (window.location.pathname.includes('/kidsfolder/')) {
        category = 'Kids';
        filterApiPath = 'filter-api.php';
    }
    
    // Initialize the universal filter system
    window.universalFilter = new UniversalFilter(category, filterApiPath);
    console.log('Universal filter system initialized for category:', category);
});

