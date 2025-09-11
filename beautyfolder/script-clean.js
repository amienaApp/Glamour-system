// Simple Script

// Filter Functionality - Define before DOMContentLoaded
function initializeFilters() {
    console.log('Initializing filters...');
    
    // Get current subcategory from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSubcategory = urlParams.get('subcategory') || '';
    
    console.log('Current subcategory:', currentSubcategory);
    
    // Filter state
    let filterState = {
        beauty_category: [],
        makeup_type: [],
        color: [],
        price_range: [],
        brand: [],
        skin_type: [],
        hair_type: [],
        sub_subcategory: []
    };
    
    // Check if filter checkboxes exist
    const filterCheckboxes = document.querySelectorAll('input[data-filter]');
    console.log('Found filter checkboxes:', filterCheckboxes.length);
    
    // Add event listeners to all filter checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.hasAttribute('data-filter')) {
            const filterType = e.target.getAttribute('data-filter');
            const filterValue = e.target.value;
            const isChecked = e.target.checked;
            
            console.log('Filter changed:', filterType, filterValue, isChecked);
            
            // Update filter state
            if (isChecked) {
                if (!filterState[filterType].includes(filterValue)) {
                    filterState[filterType].push(filterValue);
                }
            } else {
                const index = filterState[filterType].indexOf(filterValue);
                if (index > -1) {
                    filterState[filterType].splice(index, 1);
                }
            }
            
            // Apply filters
            applyFilters();
        }
    });
    
    // Clear filters button
    const clearFiltersBtn = document.getElementById('clear-filters');
    console.log('Clear filters button found:', !!clearFiltersBtn);
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            console.log('Clear filters button clicked');
            clearAllFilters();
        });
    }
    
    function applyFilters() {
        console.log('Applying filters:', filterState);
        
        // Show loading state
        showFilterLoading();
        
        // Prepare filter data
        const filterData = {
            action: 'filter_products',
            subcategory: currentSubcategory,
            beauty_categories: filterState.beauty_category,
            makeup_types: filterState.makeup_type,
            colors: filterState.color,
            price_ranges: filterState.price_range,
            brands: filterState.brand,
            skin_types: filterState.skin_type,
            hair_types: filterState.hair_type,
            sub_subcategories: filterState.sub_subcategory
        };
        
        console.log('Sending filter data:', filterData);
        
        // Send filter request
        fetch('filter-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filterData)
        })
        .then(response => response.json())
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
            hideFilterLoading();
            showFilterError('Network error occurred');
        });
    }
    
    function clearAllFilters() {
        // Uncheck all filter checkboxes
        document.querySelectorAll('input[data-filter]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset filter state
        filterState = {
            beauty_category: [],
            makeup_type: [],
            color: [],
            price_range: [],
            brand: [],
            skin_type: [],
            hair_type: [],
            sub_subcategory: []
        };
        
        // Apply filters (will show all products)
        applyFilters();
    }
    
    function updateProductGrid(products) {
        // Get the appropriate product grid based on current subcategory
        let productGrid;
        if (currentSubcategory) {
            productGrid = document.getElementById('filtered-products-grid');
        } else {
            // Try different grid IDs for main page
            productGrid = document.getElementById('dresses-grid') || 
                         document.getElementById('filtered-products-grid') ||
                         document.querySelector('.product-grid');
        }
        
        if (!productGrid) {
            console.error('Product grid not found');
            return;
        }
        
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
        
        // Reinitialize product cards
        const newProductCards = productGrid.querySelectorAll('.product-card');
        newProductCards.forEach(card => {
            initializeProductCard(card);
        });
    }
    
    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.setAttribute('data-product-id', product.id);
        card.setAttribute('data-product-name', product.name);
        card.setAttribute('data-product-price', product.price);
        card.setAttribute('data-product-sale-price', product.sale_price || '');
        card.setAttribute('data-product-category', product.category);
        card.setAttribute('data-product-subcategory', product.subcategory);
        card.setAttribute('data-product-featured', product.featured ? 'true' : 'false');
        card.setAttribute('data-product-on-sale', product.on_sale ? 'true' : 'false');
        card.setAttribute('data-product-stock', product.stock);
        card.setAttribute('data-product-color', product.color);
        card.setAttribute('data-product-front-image', product.front_image);
        card.setAttribute('data-product-back-image', product.back_image);
        
        const frontImage = product.front_image || product.image_front || '';
        const backImage = product.back_image || product.image_back || frontImage;
        
        card.innerHTML = `
            <div class="product-image">
                <div class="image-slider">
                    ${frontImage ? `<img src="../${frontImage}" alt="${product.name} - Front" class="active" data-color="${product.color}">` : ''}
                    ${backImage && backImage !== frontImage ? `<img src="../${backImage}" alt="${product.name} - Back" data-color="${product.color}">` : ''}
                </div>
                <button class="heart-button">
                    <i class="fas fa-heart"></i>
                </button>
                <div class="product-actions">
                    <button class="quick-view" data-product-id="${product.id}">Quick View</button>
                    ${product.available ? '<button class="add-to-bag">Add To Bag</button>' : '<button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>'}
                </div>
            </div>
            <div class="product-info">
                <div class="color-options">
                    ${product.color ? `<span class="color-circle active" style="background-color: ${product.color};" title="${product.color}" data-color="${product.color}"></span>` : ''}
                </div>
                <h3 class="product-name">${product.name}</h3>
                <div class="product-price">$${Math.round(product.price)}</div>
                ${!product.available ? '<div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>' : ''}
                ${product.stock <= 5 && product.stock > 0 ? `<div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only ${product.stock} left</div>` : ''}
            </div>
        `;
        
        return card;
    }
    
    function initializeProductCard(card) {
        // Add event listeners for heart button, quick view, and add to bag
        const heartBtn = card.querySelector('.heart-button');
        const quickViewBtn = card.querySelector('.quick-view');
        const addToBagBtn = card.querySelector('.add-to-bag');
        
        if (heartBtn) {
            heartBtn.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        }
        
        if (quickViewBtn) {
            quickViewBtn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                if (productId) {
                    openQuickView(productId);
                }
            });
        }
        
        if (addToBagBtn && !addToBagBtn.disabled) {
            addToBagBtn.addEventListener('click', function() {
                const productId = card.getAttribute('data-product-id');
                if (productId) {
                    // Add to cart functionality
                    console.log('Adding product to cart:', productId);
                }
            });
        }
    }
    
    function updateStyleCount(count) {
        const styleCountElement = document.getElementById('style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${count} Beauty Products`;
        }
    }
    
    function showFilterLoading() {
        const productGrid = document.getElementById('filtered-products-grid') || 
                           document.getElementById('all-products-grid') ||
                           document.querySelector('.product-grid');
        
        if (productGrid) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'filter-loading';
            loadingOverlay.innerHTML = `
                <div class="loading-content">
                    <div class="spinner"></div>
                    <p>Filtering products...</p>
                </div>
            `;
            productGrid.appendChild(loadingOverlay);
        }
        
        // Also add loading class to sidebar
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.add('filter-loading');
        }
    }
    
    function hideFilterLoading() {
        const loadingOverlay = document.getElementById('filter-loading');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
        
        // Remove loading class from sidebar
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('filter-loading');
        }
    }
    
    function showFilterError(message) {
        console.error('Filter error:', message);
        // You can add a notification system here
    }
    
    // Make functions globally accessible
    window.clearAllFilters = clearAllFilters;
    window.applyFilters = applyFilters;
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize category modal functionality
    initializeCategoryModals();
    
    // Initialize header modals functionality
    initializeHeaderModals();
    
    // Initialize filter functionality
    initializeFilters();
    
    // Quick View functionality
    const sidebar = document.getElementById('quick-view-sidebar');
    const overlay = document.getElementById('quick-view-overlay');
    const closeBtn = document.getElementById('close-quick-view');
    
    console.log('Quick View elements found:', {
        sidebar: !!sidebar,
        overlay: !!overlay,
        closeBtn: !!closeBtn
    });
    
    // Quick View button click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
            e.preventDefault();
            const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
            const productId = button.getAttribute('data-product-id');
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
    
    // Global variables to track selected variants in quick view
    let selectedQuickViewColor = '';
    let selectedQuickViewSize = '';

    function openQuickView(productId) {
        try {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (!productCard) {
                console.error('Product card not found for ID:', productId);
                return;
            }
            
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        // Update quick view content
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        
        if (titleEl) titleEl.textContent = name;
        if (priceEl) priceEl.textContent = price;
        if (addToBagBtn) addToBagBtn.setAttribute('data-product-id', productId);
        
        // Show quick view
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        
        } catch (error) {
            console.error('Error opening quick view:', error);
        }
    }

    function closeQuickView() {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
    }
    
    // Make functions globally accessible
    window.openQuickView = openQuickView;
    window.closeQuickView = closeQuickView;
    
    // Heart button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.heart-button')) {
            e.target.closest('.heart-button').classList.toggle('active');
        }
    });
    
    // Color circle functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-circle')) {
            const productCard = e.target.closest('.product-card');
            if (productCard) {
                // Remove active class from all color circles in this product
                productCard.querySelectorAll('.color-circle').forEach(circle => {
                    circle.classList.remove('active');
                });
                // Add active class to clicked circle
                e.target.classList.add('active');
                
                // Change product image based on color
                const color = e.target.getAttribute('data-color');
                const images = productCard.querySelectorAll('.image-slider img, .image-slider video');
                images.forEach(img => {
                    if (img.getAttribute('data-color') === color) {
                        img.classList.add('active');
                    } else {
                        img.classList.remove('active');
                    }
                });
            }
        }
    });
    
    // Load cart count
    function loadCartCount() {
        fetch('../cart-api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartBadge = document.querySelector('.cart-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.count;
                    }
                }
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
    }
    
    // Load cart count on page load
    loadCartCount();
    
    // Placeholder functions for modal functionality
    function initializeCategoryModals() {
        console.log('Category modals initialized');
    }
    
    function initializeHeaderModals() {
        console.log('Header modals initialized');
    }
});



