// INSTANT FILTER SYSTEM - Zero Loading Time
// This provides truly instant filtering with no network requests

class InstantFilterSystem {
    constructor() {
        this.currentFilters = {
            gender: '',
            category: '',
            color: '',
            minPrice: '',
            maxPrice: ''
        };
        this.allProducts = [];
        this.filteredProducts = [];
        this.init();
    }

    init() {
        // Load all products once on page load
        this.loadAllProducts();
        
        // Initialize filter state from URL
        this.loadFiltersFromURL();
        
        // Add event listeners
        this.addEventListeners();
        
        // Set initial checkbox states
        this.setInitialStates();
    }

    async loadAllProducts() {
        try {
            const response = await fetch('filter-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_all_products',
                    subcategory: new URLSearchParams(window.location.search).get('subcategory') || ''
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.allProducts = data.products;
                this.filteredProducts = [...this.allProducts];
                this.updateProductGrid(this.filteredProducts);
                this.updateStyleCount(this.filteredProducts.length);
            }
        } catch (error) {
            console.error('Error loading products:', error);
        }
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
        
        // INSTANT CLIENT-SIDE FILTERING - No network requests
        this.filterProducts();
    }

    filterProducts() {
        this.filteredProducts = this.allProducts.filter(product => {
            // Gender filter
            if (this.currentFilters.gender && product.gender !== this.currentFilters.gender) {
                return false;
            }
            
            // Category filter
            if (this.currentFilters.category && product.subcategory !== this.currentFilters.category) {
                return false;
            }
            
            // Color filter
            if (this.currentFilters.color) {
                const productColor = product.color || '';
                const colorGroups = {
                    'black': ['#000000', '#181a1a', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c'],
                    'beige': ['#e1c9c9', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65'],
                    'blue': ['#414c61', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
                    'brown': ['#8b4f33', '#5d3c3c', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
                    'gold': ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
                    'green': ['#82ff4d', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
                    'grey': ['#575759', '#4a4142', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
                    'orange': ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
                    'pink': ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
                    'purple': ['#373645', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
                    'red': ['#5a2b34', '#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
                    'silver': ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
                    'taupe': ['#b38f65', '#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
                    'white': ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc'],
                    'yellow': ['#ffff00', '#ffd700', '#ffeb3b', '#ffc107', '#ffa000', '#ff8f00', '#ff6f00', '#ffea00']
                };
                
                const hexCodes = colorGroups[this.currentFilters.color] || [this.currentFilters.color];
                if (!hexCodes.includes(productColor.toLowerCase())) {
                    return false;
                }
            }
            
            // Price filter
            if (this.currentFilters.minPrice) {
                const productPrice = parseFloat(product.price) || 0;
                
                if (this.currentFilters.minPrice === 'sale') {
                    // Handle sale filter - you might need to add a sale field to products
                    return true; // For now, show all products
                } else {
                    const minPrice = parseFloat(this.currentFilters.minPrice);
                    if (productPrice < minPrice) {
                        return false;
                    }
                    
                    if (this.currentFilters.maxPrice) {
                        const maxPrice = parseFloat(this.currentFilters.maxPrice);
                        if (productPrice > maxPrice) {
                            return false;
                        }
                    }
                }
            }
            
            return true;
        });
        
        // INSTANT UPDATE - No loading, no waiting
        this.updateProductGrid(this.filteredProducts);
        this.updateStyleCount(this.filteredProducts.length);
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
        
        // Clear URL and show all products INSTANTLY
        const baseUrl = window.location.pathname;
        window.history.replaceState({}, document.title, baseUrl);
        
        this.filteredProducts = [...this.allProducts];
        this.updateProductGrid(this.filteredProducts);
        this.updateStyleCount(this.filteredProducts.length);
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

// Initialize the instant filter system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.filterSystem = new InstantFilterSystem();
    
    // Make clearAllFilters globally accessible
    window.clearAllFiltersSimple = function() {
        window.filterSystem.clearAllFilters();
    };
});
