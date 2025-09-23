// Universal AJAX Filter System for Glamour Palace
// This script provides instant filtering without page reloads

class UniversalFilterSystem {
    constructor() {
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
        // Initialize filter state from URL
        this.loadFiltersFromURL();
        
        // Add event listeners
        this.addEventListeners();
        
        // Set initial checkbox states
        this.setInitialStates();
    }

    loadFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentFilters.gender = urlParams.get('gender') || '';
        this.currentFilters.category = urlParams.get('category') || '';
        this.currentFilters.color = urlParams.get('color') || '';
        this.currentFilters.minPrice = urlParams.get('min_price') || '';
        this.currentFilters.maxPrice = urlParams.get('max_price') || '';
    }

    addEventListeners() {
        // Category filters
        document.querySelectorAll('input[name="category[]"], input[name="kids_category[]"], input[name="beauty_categories[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateCategoryFilter(e.target.value, e.target.checked);
            });
        });

        // Color filters
        document.querySelectorAll('input[name="color[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateColorFilter(e.target.value, e.target.checked);
            });
        });

        // Price filters
        document.querySelectorAll('input[name="price[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updatePriceFilter(e.target.value, e.target.checked);
            });
        });

        // Gender filters
        document.querySelectorAll('input[name="gender[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateGenderFilter(e.target.value, e.target.checked);
            });
        });
    }

    updateCategoryFilter(category, checked) {
        this.uncheckOthers('input[name="category[]"], input[name="kids_category[]"], input[name="beauty_categories[]"]');
        
        if (checked) {
            this.currentFilters.category = category;
            document.querySelector(`input[value="${category}"]`).checked = true;
        } else {
            this.currentFilters.category = '';
        }
        this.applyFilters();
    }

    updateColorFilter(color, checked) {
        this.uncheckOthers('input[name="color[]"]');
        
        if (checked) {
            this.currentFilters.color = color;
            document.querySelector(`input[name="color[]"][value="${color}"]`).checked = true;
        } else {
            this.currentFilters.color = '';
        }
        this.applyFilters();
    }

    updatePriceFilter(priceValue, checked) {
        this.uncheckOthers('input[name="price[]"]');
        
        if (checked) {
            if (priceValue === 'on-sale') {
                this.currentFilters.minPrice = 'sale';
                this.currentFilters.maxPrice = null;
            } else if (priceValue.includes('+')) {
                this.currentFilters.minPrice = parseInt(priceValue.replace('+', ''));
                this.currentFilters.maxPrice = null;
            } else if (priceValue.includes('-')) {
                const [minPrice, maxPrice] = priceValue.split('-').map(p => parseInt(p));
                this.currentFilters.minPrice = minPrice;
                this.currentFilters.maxPrice = maxPrice;
            }
            document.querySelector(`input[name="price[]"][value="${priceValue}"]`).checked = true;
        } else {
            this.currentFilters.minPrice = '';
            this.currentFilters.maxPrice = '';
        }
        this.applyFilters();
    }

    updateGenderFilter(gender, checked) {
        this.uncheckOthers('input[name="gender[]"]');
        
        if (checked) {
            this.currentFilters.gender = gender;
            document.querySelector(`input[name="gender[]"][value="${gender}"]`).checked = true;
        } else {
            this.currentFilters.gender = '';
        }
        this.applyFilters();
    }

    uncheckOthers(selector) {
        document.querySelectorAll(selector).forEach(input => {
            input.checked = false;
        });
    }

    applyFilters() {
        // Update URL without reload
        this.updateURL();
        
        // Make AJAX request instantly (no loading state)
        this.fetchFilteredProducts();
    }

    updateURL() {
        const params = new URLSearchParams();
        
        // Preserve existing subcategory
        const existingSubcategory = new URLSearchParams(window.location.search).get('subcategory');
        if (existingSubcategory) {
            params.append('subcategory', existingSubcategory);
        }
        
        // Add filter parameters
        if (this.currentFilters.gender) params.append('gender', this.currentFilters.gender);
        if (this.currentFilters.category) params.append('category', this.currentFilters.category);
        if (this.currentFilters.color) params.append('color', this.currentFilters.color);
        if (this.currentFilters.minPrice) params.append('min_price', this.currentFilters.minPrice);
        if (this.currentFilters.maxPrice) params.append('max_price', this.currentFilters.maxPrice);
        
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
    }

    // Removed loading states for instant response

    fetchFilteredProducts() {
        const existingSubcategory = new URLSearchParams(window.location.search).get('subcategory') || '';
        
        fetch('filter-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'filter_products',
                subcategory: existingSubcategory,
                gender: this.currentFilters.gender,
                category: this.currentFilters.category,
                color: this.currentFilters.color,
                min_price: this.currentFilters.minPrice,
                max_price: this.currentFilters.maxPrice
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateProductGrid(data.products);
                this.updateStyleCount(data.products.length);
            } else {
                this.showError(data.message || 'Filter failed');
            }
        })
        .catch(error => {
            this.showError('Network error occurred');
            console.error('Filter error:', error);
        });
    }

    updateProductGrid(products) {
        const productGrid = document.querySelector('.product-grid, .pro-container, .products-grid');
        if (!productGrid) return;
        
        productGrid.innerHTML = '';
        
        if (products.length === 0) {
            productGrid.innerHTML = '<div class="no-products" style="text-align: center; padding: 40px; color: #666;">No products found matching your filters.</div>';
            return;
        }
        
        products.forEach(product => {
            const productCard = this.createProductCard(product);
            productGrid.appendChild(productCard);
        });
    }

    createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'pro';
        card.innerHTML = `
            <img src="${product.image || 'img/products/default.jpg'}" alt="${product.name}" loading="lazy">
            <div class="des">
                <span>${product.brand || ''}</span>
                <h5>${product.name}</h5>
                <div class="star">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h4>$${product.price}</h4>
            </div>
            <a href="#" class="cart" onclick="addToCart('${product._id}')">
                <i class="fas fa-shopping-cart"></i>
            </a>
        `;
        return card;
    }

    updateStyleCount(count) {
        const styleCount = document.getElementById('style-count');
        if (styleCount) {
            styleCount.textContent = `${count} Styles`;
        }
    }

    showError(message) {
        console.error('Filter error:', message);
        // You can add toast notifications here
    }

    clearAllFilters() {
        // Reset all checkboxes and radio buttons
        document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
            input.checked = false;
        });
        
        // Reset filters
        this.currentFilters = {
            gender: '',
            category: '',
            color: '',
            minPrice: '',
            maxPrice: ''
        };
        
        // Clear URL and reload all products instantly
        const baseUrl = window.location.pathname;
        window.history.replaceState({}, document.title, baseUrl);
        
        this.fetchFilteredProducts();
    }

    setInitialStates() {
        // Set checkbox states based on current filters
        if (this.currentFilters.gender) {
            const genderCheckbox = document.querySelector(`input[name="gender[]"][value="${this.currentFilters.gender}"]`);
            if (genderCheckbox) genderCheckbox.checked = true;
        }
        
        if (this.currentFilters.category) {
            const categoryCheckbox = document.querySelector(`input[value="${this.currentFilters.category}"]`);
            if (categoryCheckbox) categoryCheckbox.checked = true;
        }
        
        if (this.currentFilters.color) {
            const colorCheckbox = document.querySelector(`input[name="color[]"][value="${this.currentFilters.color}"]`);
            if (colorCheckbox) colorCheckbox.checked = true;
        }
        
        if (this.currentFilters.minPrice && this.currentFilters.maxPrice) {
            const priceValue = `${this.currentFilters.minPrice}-${this.currentFilters.maxPrice}`;
            const priceCheckbox = document.querySelector(`input[name="price[]"][value="${priceValue}"]`);
            if (priceCheckbox) priceCheckbox.checked = true;
        } else if (this.currentFilters.minPrice && !this.currentFilters.maxPrice) {
            const priceValue = `${this.currentFilters.minPrice}+`;
            const priceCheckbox = document.querySelector(`input[name="price[]"][value="${priceValue}"]`);
            if (priceCheckbox) priceCheckbox.checked = true;
        }
    }
}

// Initialize the filter system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.filterSystem = new UniversalFilterSystem();
    
    // Make clearAllFilters globally accessible
    window.clearAllFiltersSimple = function() {
        window.filterSystem.clearAllFilters();
    };
});
