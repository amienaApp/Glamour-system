// Men's Clothing Filter Script
console.log('🚀 Men\'s filter script loaded successfully!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded successfully!');

    // Filter state
    let filterState = {
        sizes: [],
        colors: [],
        price_ranges: [],
        categories: [],
        brands: [],
        styles: [],
        materials: [],
        fits: [],
        availability: []
    };

    // Get current subcategory from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSubcategory = urlParams.get('subcategory') || '';

    console.log('Current subcategory:', currentSubcategory);

    // Initialize filters
    initializeFilters();
    
    // Load initial products (all products)
    console.log('Loading initial products...');
    applyFilters();

    function initializeFilters() {
        console.log('Initializing filters...');
        
        // Add event listeners to all filter checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.hasAttribute('data-filter')) {
                const filterType = e.target.getAttribute('data-filter');
                const filterValue = e.target.value;
                const isChecked = e.target.checked;
                
                console.log(`Filter changed: ${filterType} = ${filterValue}, checked: ${isChecked}`);
                console.log('Current filterState:', filterState);
                
                // Update filter state
                const filterKey = filterType + 's';
                if (!filterState[filterKey]) {
                    filterState[filterKey] = [];
                }
                
                if (isChecked) {
                    if (!filterState[filterKey].includes(filterValue)) {
                        filterState[filterKey].push(filterValue);
                    }
                } else {
                    const index = filterState[filterKey].indexOf(filterValue);
                    if (index > -1) {
                        filterState[filterKey].splice(index, 1);
                    }
                }
                
                console.log('Current filter state:', filterState);
                
                // Apply filters
                applyFilters();
            }
        });
        
        // Clear filters button
        const clearFiltersBtn = document.querySelector('.clear-filters-btn');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                clearAllFilters();
            });
        }

        // Debug: Log all filter checkboxes
        const filterCheckboxes = document.querySelectorAll('input[data-filter]');
        console.log(`Found ${filterCheckboxes.length} filter checkboxes:`, filterCheckboxes);
        
        filterCheckboxes.forEach(checkbox => {
            console.log(`Filter checkbox: ${checkbox.getAttribute('data-filter')} = ${checkbox.value}`);
        });
    }

    function applyFilters() {
        console.log('Applying filters...');
        console.log('Filter state:', filterState);
        
        // Show loading state
        showFilterLoading();
        
        // Prepare filter data
        const filterData = {
            action: 'filter_products',
            subcategory: currentSubcategory,
            sizes: filterState.sizes,
            colors: filterState.colors,
            price_ranges: filterState.price_ranges,
            categories: filterState.categories,
            brands: filterState.brands,
            styles: filterState.styles,
            materials: filterState.materials,
            fits: filterState.fits,
            availability: filterState.availability
        };
        
        console.log('Sending filter data:', filterData);
        console.log('API URL:', window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/') + 'filter-api.php');
        console.log('About to make fetch request to: ./filter-api.php');
        
        // Send filter request
        fetch('./filter-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filterData)
        })
        .then(response => {
            console.log('Raw response:', response);
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Filter response:', data);
            
            if (data.success) {
                updateProductGrid(data.data.products);
                updateStyleCount(data.data.total_count);
                hideFilterLoading();
            } else {
                console.error('Filter error:', data.message);
                hideFilterLoading();
                showFilterError(data.message);
            }
        })
        .catch(error => {
            console.error('Filter request error:', error);
            console.error('Error details:', error.message);
            hideFilterLoading();
            showFilterError('Network error occurred: ' + error.message);
        });
    }

    function clearAllFilters() {
        console.log('Clearing all filters...');
        
        // Uncheck all filter checkboxes
        document.querySelectorAll('input[data-filter]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset filter state
        filterState = {
            sizes: [],
            colors: [],
            price_ranges: [],
            categories: [],
            brands: [],
            styles: [],
            materials: [],
            fits: [],
            availability: []
        };
        
        // Apply filters (will show all products)
        applyFilters();
    }

    function updateProductGrid(products) {
        console.log(`Updating product grid with ${products.length} products`);
        
        // Get the product grid
        const productGrid = document.querySelector('.product-grid') || 
                           document.querySelector('.products-container') ||
                           document.querySelector('.main-content') ||
                           document.querySelector('#filtered-products-grid');
        
        if (!productGrid) {
            console.error('Product grid not found. Available elements:');
            console.log('product-grid:', document.querySelector('.product-grid'));
            console.log('products-container:', document.querySelector('.products-container'));
            console.log('main-content:', document.querySelector('.main-content'));
            console.log('filtered-products-grid:', document.querySelector('#filtered-products-grid'));
            return;
        }
        
        console.log('Found product grid:', productGrid);
        
        // Clear existing products
        productGrid.innerHTML = '';
        
        if (products.length === 0) {
            productGrid.innerHTML = `
                <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p>No products found matching your filters.</p>
                    <button onclick="clearAllFilters()" class="clear-filters-btn">Clear Filters</button>
                </div>
            `;
            return;
        }
        
        // Add products to grid
        products.forEach(product => {
            const productCard = createProductCard(product);
            productGrid.appendChild(productCard);
        });
    }

    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.setAttribute('data-product-id', product.id);
        
        // Get the best image for the product
        const imagePath = getProductImage(product);
        
        card.innerHTML = `
            <div class="product-image">
                <div class="image-slider">
                    <img src="../${imagePath}" 
                         alt="${product.name}" 
                         class="active" 
                         data-color="${product.color}">
                </div>
                <button class="heart-button">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="${product.id}">Quick View</button>
                    ${product.available ? 
                        '<button class="add-to-bag">Add To Bag</button>' : 
                        '<button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>'
                    }
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    <span class="color-circle active" 
                          style="background-color: ${product.color};" 
                          title="${product.color}" 
                          data-color="${product.color}"></span>
                </div>
                <h3 class="product-name">${product.name}</h3>
                <div class="product-price">$${product.price}</div>
                ${!product.available ? 
                    '<div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>' : 
                    (product.stock <= 5 && product.stock > 0 ? 
                        `<div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only ${product.stock} left</div>` : 
                        ''
                    )
                }
            </div>
        `;
        
        return card;
    }

    function getProductImage(product) {
        if (product.front_image) {
            return product.front_image;
        } else if (product.back_image) {
            return product.back_image;
        } else if (product.color_variants && product.color_variants.length > 0) {
            return product.color_variants[0].front_image || product.color_variants[0].back_image || 'img/default-product.jpg';
        }
        return 'img/default-product.jpg';
    }

    function updateStyleCount(count) {
        const styleCountElement = document.querySelector('.style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${count} Styles`;
        }
    }

    function showFilterLoading() {
        // Add loading overlay to product grid
        const productGrid = document.querySelector('.product-grid') || 
                           document.querySelector('.products-container') ||
                           document.querySelector('.main-content');
        if (productGrid) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'filter-loading';
            loadingOverlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            `;
            loadingOverlay.innerHTML = `
                <div style="text-align: center;">
                    <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
                    <p>Filtering products...</p>
                </div>
            `;
            productGrid.style.position = 'relative';
            productGrid.appendChild(loadingOverlay);
            
            // Also add loading state to sidebar
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.add('filter-loading');
            }
        }
    }

    function hideFilterLoading() {
        const loadingOverlay = document.getElementById('filter-loading');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
        
        // Remove loading state from sidebar
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('filter-loading');
        }
    }

    function showFilterError(message) {
        // Show error notification
        console.error('Filter error:', message);
        alert('Filter error: ' + message);
    }

    // Size Filter Enhancement Functions
    function selectAllSizes() {
        console.log('Selecting all sizes...');
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sizeCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }

    function clearSizeFilters() {
        console.log('Clearing size filters...');
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sizeCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }

    function updateSizeCount() {
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]:checked');
        const sizeCountElement = document.getElementById('size-count');
        if (sizeCountElement) {
            const count = sizeCheckboxes.length;
            sizeCountElement.textContent = count > 0 ? `(${count} selected)` : '';
        }
    }

    // Make functions globally accessible
    window.selectAllSizes = selectAllSizes;
    window.clearSizeFilters = clearSizeFilters;
    window.updateSizeCount = updateSizeCount;
    window.clearAllFilters = clearAllFilters;

    // Add event listener for size count updates
    document.addEventListener('change', function(e) {
        if (e.target.closest('#size-filter')) {
            updateSizeCount();
        }
    });

    // Initialize size count on page load
    updateSizeCount();

    // Add CSS for loading animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    // Quick View functionality
    const sidebar = document.getElementById('quick-view-sidebar');
    const overlay = document.getElementById('quick-view-overlay');
    const closeBtn = document.getElementById('close-quick-view');
    
    console.log('Quick view elements found:', {
        sidebar: !!sidebar,
        overlay: !!overlay,
        closeBtn: !!closeBtn
    });
    
    // Quick View button click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
            e.preventDefault();
            console.log('Quick view button clicked!');
            const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
            const productId = button.getAttribute('data-product-id');
            console.log('Product ID:', productId);
            if (productId) {
                openQuickView(productId);
            } else {
                console.error('No product ID found');
            }
        }
    });
    
    // Close quick view
    if (closeBtn) closeBtn.addEventListener('click', closeQuickView);
    if (overlay) overlay.addEventListener('click', closeQuickView);
    
    function openQuickView(productId) {
        console.log('Opening quick view for:', productId);
        
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) return;
        
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        // Update quick view content
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        
        if (titleEl) titleEl.tex