// Simple Professional Filter System - No AJAX, No Loading
// Direct DOM manipulation for instant results
// 
// FILTER BEHAVIOR:
// - Categories: Only ONE can be selected at a time (like radio buttons)
// - Sizes: MULTIPLE can be selected (like checkboxes)
// - Colors: MULTIPLE can be selected (like checkboxes)
// - Price: Only ONE can be selected at a time (like radio buttons)

let selectedCategories = [];
let selectedSizes = [];
let selectedColors = [];
let selectedPrice = null;

// Category Filter Function - Only one category at a time
function filterByCategory(category, isChecked) {
    if (isChecked) {
        // Uncheck all other category checkboxes
        document.querySelectorAll('input[name="category[]"]').forEach(checkbox => {
            if (checkbox.value !== category) {
                checkbox.checked = false;
            }
        });
        // Set only this category
        selectedCategories = [category];
    } else {
        // If unchecking, clear all categories
        selectedCategories = [];
    }
    
    console.log('Category filter:', category, isChecked, 'Selected categories:', selectedCategories);
    applyFilters();
}

// Size Filter Function - Multiple sizes allowed
function filterBySize(size, isChecked) {
    if (isChecked) {
        if (!selectedSizes.includes(size)) {
            selectedSizes.push(size);
        }
    } else {
        selectedSizes = selectedSizes.filter(s => s !== size);
    }
    
    console.log('Size filter:', size, isChecked, 'Selected sizes:', selectedSizes);
    applyFilters();
}

// Color Filter Function
function filterByColor(color, isChecked) {
    if (isChecked) {
        if (!selectedColors.includes(color)) {
            selectedColors.push(color);
        }
    } else {
        selectedColors = selectedColors.filter(c => c !== color);
    }
    
    applyFilters();
}

// Price Filter Function - Only one price range at a time
function filterByPrice(priceValue, isChecked) {
    if (isChecked) {
        // Uncheck all other price checkboxes
        document.querySelectorAll('input[name="price[]"]').forEach(checkbox => {
            if (checkbox.value !== priceValue) {
                checkbox.checked = false;
            }
        });
        // Set only this price range
        selectedPrice = priceValue;
    } else {
        // If unchecking, clear price selection
        selectedPrice = null;
    }
    
    console.log('Price filter:', priceValue, isChecked, 'Selected price:', selectedPrice);
    applyFilters();
}

// Main Filter Application
function applyFilters() {
    // Try to find the correct product grid
    const productGrid = document.getElementById('kids-products-grid') || 
                       document.getElementById('all-products-grid') || 
                       document.querySelector('.product-grid');
    
    if (!productGrid) {
        console.log('No product grid found');
        return;
    }
    
    const products = productGrid.querySelectorAll('.product-card');
    let visibleCount = 0;
    
    console.log('Applying filters to', products.length, 'products');
    console.log('Selected categories:', selectedCategories);
    console.log('Selected sizes:', selectedSizes);
    console.log('Selected colors:', selectedColors);
    console.log('Selected price:', selectedPrice);
    
    // Debug: Show size data for first few products
    if (products.length > 0) {
        console.log('Sample product size data:');
        for (let i = 0; i < Math.min(3, products.length); i++) {
            const product = products[i];
            const sizes = product.getAttribute('data-product-sizes');
            const selectedSizes = product.getAttribute('data-product-selected-sizes');
            console.log(`Product ${i + 1}:`, {
                name: product.getAttribute('data-product-name'),
                sizes: sizes,
                selectedSizes: selectedSizes
            });
        }
    }
    
    products.forEach(product => {
        let shouldShow = true;
        
        // Check category filter
        if (selectedCategories.length > 0) {
            const productSubcategory = product.getAttribute('data-product-subcategory');
            if (!selectedCategories.includes(productSubcategory)) {
                shouldShow = false;
            }
        }
        
        // Check size filter
        if (selectedSizes.length > 0 && shouldShow) {
            // Try both data-product-sizes and data-product-selected-sizes
            const productSizes = product.getAttribute('data-product-sizes');
            const productSelectedSizes = product.getAttribute('data-product-selected-sizes');
            
            let sizes = [];
            let hasSelectedSize = false;
            
            // Try data-product-sizes first
            if (productSizes && productSizes !== '[]' && productSizes !== 'null') {
                try {
                    sizes = JSON.parse(productSizes);
                    hasSelectedSize = selectedSizes.some(size => sizes.includes(size));
                } catch (e) {
                    // If JSON parsing fails, try comma-separated values
                    sizes = productSizes.split(',');
                    hasSelectedSize = selectedSizes.some(size => sizes.includes(size));
                }
            }
            
            // If no sizes found or no match, try data-product-selected-sizes
            if (!hasSelectedSize && productSelectedSizes && productSelectedSizes !== '[]' && productSelectedSizes !== 'null') {
                try {
                    sizes = JSON.parse(productSelectedSizes);
                    hasSelectedSize = selectedSizes.some(size => sizes.includes(size));
                } catch (e) {
                    // If JSON parsing fails, try comma-separated values
                    sizes = productSelectedSizes.split(',');
                    hasSelectedSize = selectedSizes.some(size => sizes.includes(size));
                }
            }
            
            // If no size data is available for any products, show all products
            // This handles the case where products don't have size information in the database
            if (sizes.length === 0) {
                hasSelectedSize = true; // Show all products when no size data is available
            }
            
            if (!hasSelectedSize) {
                shouldShow = false;
            }
        }
        
        // Check color filter
        if (selectedColors.length > 0 && shouldShow) {
            const productColor = product.getAttribute('data-product-color');
            if (!selectedColors.includes(productColor)) {
                shouldShow = false;
            }
        }
        
        // Check price filter
        if (selectedPrice && shouldShow) {
            const productPrice = parseFloat(product.getAttribute('data-product-price'));
            const productSalePrice = product.getAttribute('data-product-sale-price');
            const isOnSale = product.getAttribute('data-product-sale') === 'true';
            
            let priceMatch = false;
            
            if (selectedPrice === 'on-sale') {
                priceMatch = isOnSale;
            } else if (selectedPrice.includes('+')) {
                const minPrice = parseInt(selectedPrice.replace('+', ''));
                priceMatch = productPrice >= minPrice;
            } else if (selectedPrice.includes('-')) {
                const [minPrice, maxPrice] = selectedPrice.split('-').map(p => parseInt(p));
                priceMatch = productPrice >= minPrice && productPrice <= maxPrice;
            }
            
            if (!priceMatch) {
                shouldShow = false;
            }
        }
        
        // Show/hide product
        if (shouldShow) {
            product.style.display = 'block';
            visibleCount++;
        } else {
            product.style.display = 'none';
        }
    });
    
    // Update product count
    updateProductCount(visibleCount);
}


// Update Product Count Display
function updateProductCount(count) {
    const countElement = document.getElementById('style-count');
    if (countElement) {
        countElement.textContent = `${count} Kids Products`;
    }
}

// Size Helper Functions
function selectAllSizes() {
    const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
        filterBySize(checkbox.value, true);
    });
}

function clearSizeFilters() {
    const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedSizes = [];
    applyFilters();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial count
    const productGrid = document.getElementById('kids-products-grid') || 
                       document.getElementById('all-products-grid') || 
                       document.querySelector('.product-grid');
    
    if (productGrid) {
        const products = productGrid.querySelectorAll('.product-card');
        updateProductCount(products.length);
        console.log('Simple filter system initialized - No AJAX, No loading, Pure JavaScript');
        console.log('Found', products.length, 'products in grid');
    } else {
        console.log('No product grid found for filtering');
    }
});
