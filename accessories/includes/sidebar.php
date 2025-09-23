<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get current filter values from URL
$currentGender = $_GET['gender'] ?? '';
$currentCategory = $_GET['category'] ?? '';
$currentColor = $_GET['color'] ?? '';
$currentMinPrice = $_GET['min_price'] ?? '';
$currentMaxPrice = $_GET['max_price'] ?? '';

// Get all accessories to extract available options and calculate statistics
$allAccessories = $productModel->getAll(['category' => 'Accessories']);

// Calculate statistics
$stats = [
    'total_accessories' => count($allAccessories),
    'women_accessories' => 0,
    'men_accessories' => 0
];

foreach ($allAccessories as $accessory) {
    if (isset($accessory['gender'])) {
        if ($accessory['gender'] === 'women' || $accessory['gender'] === 'Women') {
            $stats['women_accessories']++;
        } elseif ($accessory['gender'] === 'men' || $accessory['gender'] === 'Men') {
            $stats['men_accessories']++;
        }
    }
}

// Get available categories
$categories = [];
foreach ($allAccessories as $accessory) {
    if (isset($accessory['subcategory']) && !in_array($accessory['subcategory'], $categories)) {
        $categories[] = $accessory['subcategory'];
    }
}
sort($categories);
?>

<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count"><?php echo $stats['total_accessories']; ?> Styles</span>
        <button type="button" class="clear-all-filters-btn" id="clear-filters" onclick="clearAllFiltersSimple()">
                    Clear All Filters
                </button>
            </div>
    <div class="side">
        
        <div class="filter-section">
            <!-- Gender Filter -->
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Gender</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="women" 
                               <?php echo $currentGender === 'women' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('women', this.checked)">
                        <span class="checkmark"></span>
                        Women (<?php echo $stats['women_accessories']; ?>)
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="men" 
                               <?php echo $currentGender === 'men' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('men', this.checked)">
                        <span class="checkmark"></span>
                        Men (<?php echo $stats['men_accessories']; ?>)
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Category Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Categories</h4>
                </div>
                <div class="filter-options">
                    <?php foreach ($categories as $category): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($category); ?>" 
                               <?php echo $currentCategory === $category ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('<?php echo htmlspecialchars($category); ?>', this.checked)">
                        <span class="checkmark"></span>
                        <?php echo htmlspecialchars($category); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Color Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Color</h4>
                </div>
                <div class="filter-options">
                    <div class="color-grid">
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="black" 
                                   <?php echo $currentColor === 'black' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('black', this.checked)">
                            <span class="color-swatch" style="background-color: #000000;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="beige" 
                                   <?php echo $currentColor === 'beige' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('beige', this.checked)">
                            <span class="color-swatch" style="background-color: #dac0b4;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="blue" 
                                   <?php echo $currentColor === 'blue' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('blue', this.checked)">
                            <span class="color-swatch" style="background-color: #0a1e3b;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="brown" 
                                   <?php echo $currentColor === 'brown' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('brown', this.checked)">
                            <span class="color-swatch" style="background-color: #966345;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="gold" 
                                   <?php echo $currentColor === 'gold' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('gold', this.checked)">
                            <span class="color-swatch" style="background-color: #f9d07f;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="green" 
                                   <?php echo $currentColor === 'green' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('green', this.checked)">
                            <span class="color-swatch" style="background-color: #04613f;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="grey" 
                                   <?php echo $currentColor === 'grey' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('grey', this.checked)">
                            <span class="color-swatch" style="background-color: #676b6e;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="orange" 
                                   <?php echo $currentColor === 'orange' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('orange', this.checked)">
                            <span class="color-swatch" style="background-color: #ffa500;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="pink" 
                                   <?php echo $currentColor === 'pink' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('pink', this.checked)">
                            <span class="color-swatch" style="background-color: #ffc0cb;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="purple" 
                                   <?php echo $currentColor === 'purple' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('purple', this.checked)">
                            <span class="color-swatch" style="background-color: #63678f;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="red" 
                                   <?php echo $currentColor === 'red' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('red', this.checked)">
                            <span class="color-swatch" style="background-color: #ff0000;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="silver" 
                                   <?php echo $currentColor === 'silver' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('silver', this.checked)">
                            <span class="color-swatch" style="background-color: #c0c0c0;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="white" 
                                   <?php echo $currentColor === 'white' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('white', this.checked)">
                            <span class="color-swatch" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Price Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Price Range</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="0-100" 
                               <?php echo ($currentMinPrice == '0' && $currentMaxPrice == '100') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(0, 100, this.checked)">
                        <span class="checkmark"></span>
                        $0 - $100
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="100-200" 
                               <?php echo ($currentMinPrice == '100' && $currentMaxPrice == '200') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(100, 200, this.checked)">
                        <span class="checkmark"></span>
                        $100 - $200
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="200-500" 
                               <?php echo ($currentMinPrice == '200' && $currentMaxPrice == '500') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(200, 500, this.checked)">
                        <span class="checkmark"></span>
                        $200 - $500
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="500+" 
                               <?php echo ($currentMinPrice == '500') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(500, '', this.checked)">
                        <span class="checkmark"></span>
                        $500+
                    </label>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
// Current filter state
let currentFilters = {
    gender: '<?php echo $currentGender; ?>',
    category: '<?php echo $currentCategory; ?>',
    color: '<?php echo $currentColor; ?>',
    minPrice: '<?php echo $currentMinPrice; ?>',
    maxPrice: '<?php echo $currentMaxPrice; ?>'
};

// Function to update gender filter
function updateGenderFilter(gender, checked) {
    if (checked) {
        currentFilters.gender = gender;
        // Uncheck other gender options
        document.querySelectorAll('input[name="gender[]"]').forEach(checkbox => {
            if (checkbox.value !== gender) {
                checkbox.checked = false;
            }
        });
    } else {
        currentFilters.gender = '';
    }
    applyFilters();
}

// Function to update category filter
function updateCategoryFilter(category, checked) {
    if (checked) {
        currentFilters.category = category;
    } else {
        currentFilters.category = '';
    }
    applyFilters();
}

// Function to update color filter
function updateColorFilter(color, checked) {
    if (checked) {
        currentFilters.color = color;
    } else {
        currentFilters.color = '';
    }
    applyFilters();
}

// Function to update price filter
function updatePriceFilter(minPrice, maxPrice, checked) {
    if (checked) {
        currentFilters.minPrice = minPrice;
        currentFilters.maxPrice = maxPrice;
    } else {
        currentFilters.minPrice = '';
        currentFilters.maxPrice = '';
    }
    applyFilters();
}

// Function to apply all filters
function applyFilters() {
    const params = new URLSearchParams();
    
    if (currentFilters.gender) params.append('gender', currentFilters.gender);
    if (currentFilters.category) params.append('category', currentFilters.category);
    if (currentFilters.color) params.append('color', currentFilters.color);
    if (currentFilters.minPrice) params.append('min_price', currentFilters.minPrice);
    if (currentFilters.maxPrice) params.append('max_price', currentFilters.maxPrice);
    
    // Update URL and reload page
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Clear All Filters function - New comprehensive implementation
function clearAllFilters() {
    console.log('Clearing all filters...');
    
    // Reset all filter checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Clear URL parameters
    const baseUrl = window.location.pathname;
    window.history.replaceState({}, document.title, baseUrl);
    
    // Show loading state
    showFilterLoading();
    
    // Make AJAX request to get all accessories
    fetch('filter-api.php', {
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
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Clear filters response:', data);
        
        if (data.success) {
            console.log('Successfully cleared filters, products count:', data.data.products.length);
            updateProductGrid(data.data.products);
            updateStyleCount(data.data.total_count);
            hideFilterLoading();
        } else {
            console.error('Clear filters error:', data.message);
            hideFilterLoading();
            showFilterError(data.message);
        }
    })
    .catch(error => {
        console.error('Clear filters request error:', error);
        hideFilterLoading();
        showFilterError('Network error occurred: ' + error.message);
        
        // Fallback: reload the page to show all products
        console.log('Falling back to page reload...');
        setTimeout(() => {
    window.location.reload();
        }, 1000);
    });
}

// Helper functions for the clear filters functionality
function showFilterLoading() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.add('filter-loading');
    }
}

function hideFilterLoading() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.remove('filter-loading');
    }
}

function showFilterError(message) {
    console.error('Filter error:', message);
    // You can add a visual error message here if needed
}

function updateProductGrid(products) {
    console.log(`Updating product grid with ${products.length} products`);
    
    // Find the product grid - try multiple possible selectors
    let productGrid = document.getElementById('all-accessories-grid') || 
                     document.getElementById('filtered-products-grid') ||
                     document.querySelector('#all-accessories-grid') ||
                     document.querySelector('#filtered-products-grid') ||
                     document.querySelector('.product-grid');
    
    if (!productGrid) {
        console.error('Product grid not found. Available elements:', {
            allAccessoriesGrid: !!document.getElementById('all-accessories-grid'),
            filteredProductsGrid: !!document.getElementById('filtered-products-grid'),
            productGrids: document.querySelectorAll('.product-grid').length
        });
        return;
    }
    
    console.log('Found product grid:', productGrid);
    
    // Clear existing products
    productGrid.innerHTML = '';
    
    if (products.length === 0) {
        productGrid.innerHTML = '<div class="no-products"><p>No accessories found.</p></div>';
        return;
    }
    
    // Generate product cards
    products.forEach(product => {
        const productCard = createProductCard(product);
        productGrid.appendChild(productCard);
    });
    
    console.log(`Successfully added ${products.length} products to grid`);
}

function createProductCard(product) {
    const card = document.createElement('div');
    
    // Determine if product is sold out - same logic as womenF
    const stock = parseInt(product.stock) || 0;
    const available = product.available;
    const isSoldOut = stock <= 0 || available === false;
    const isLowStock = stock > 0 && stock <= 5 && available !== false;
    
    card.className = 'product-card';
    card.setAttribute('data-product-id', product.id);
    card.setAttribute('data-product-name', product.name || '');
    card.setAttribute('data-product-price', product.price || 0);
    card.setAttribute('data-product-category', product.category || '');
    card.setAttribute('data-product-subcategory', product.subcategory || '');
    card.setAttribute('data-product-stock', stock);
    card.setAttribute('data-product-available', available ? 'true' : 'false');
    card.setAttribute('data-product-color', product.color || '');
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
                ${isSoldOut ? 
                    '<button class="add-to-bag" disabled style="opacity: 0.5; cursor: not-allowed;">Sold Out</button>' :
                    '<button class="add-to-bag" data-product-id="' + product.id + '" data-product-name="' + product.name + '" data-product-price="' + product.price + '" data-product-color="' + (product.color || '') + '" data-product-stock="' + stock + '">Add To Bag</button>'
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
            ${isSoldOut ? 
                '<div class="product-availability" style="color: #e53e3e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">SOLD OUT</div>' :
                (isLowStock ? 
                    `<div class="product-availability" style="color: #d69e2e; font-size: 0.9rem; font-weight: 600; margin-top: 5px;">Only ${stock} left</div>` : '')
            }
        </div>
    `;
    
    return card;
}

function updateStyleCount(count) {
    const styleCountElement = document.querySelector('.style-count');
    if (styleCountElement) {
        styleCountElement.textContent = `${count} Styles`;
    }
}

// Simple Clear All Filters function that reloads the page
function clearAllFiltersSimple() {
    console.log('Clearing all filters - simple method');
    
    // Clear URL parameters and reload
    const baseUrl = window.location.pathname;
    window.history.replaceState({}, document.title, baseUrl);
    window.location.reload();
}

// Make the functions globally accessible
window.clearAllFilters = clearAllFilters;
window.clearAllFiltersSimple = clearAllFiltersSimple;

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set current filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentFilters.gender = urlParams.get('gender') || '';
    currentFilters.category = urlParams.get('category') || '';
    currentFilters.color = urlParams.get('color') || '';
    currentFilters.minPrice = urlParams.get('min_price') || '';
    currentFilters.maxPrice = urlParams.get('max_price') || '';
    
    // Ensure checkboxes are properly synchronized with URL parameters
    // Uncheck all checkboxes first
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Then check the ones that match current URL parameters
    if (currentFilters.gender) {
        const genderCheckbox = document.querySelector(`input[name="gender[]"][value="${currentFilters.gender}"]`);
        if (genderCheckbox) genderCheckbox.checked = true;
    }
    
    if (currentFilters.category) {
        const categoryCheckbox = document.querySelector(`input[name="category[]"][value="${currentFilters.category}"]`);
        if (categoryCheckbox) categoryCheckbox.checked = true;
    }
    
    if (currentFilters.color) {
        const colorCheckbox = document.querySelector(`input[name="color[]"][value="${currentFilters.color}"]`);
        if (colorCheckbox) colorCheckbox.checked = true;
    }
    
    if (currentFilters.minPrice && currentFilters.maxPrice) {
        const priceCheckbox = document.querySelector(`input[name="price[]"][value="${currentFilters.minPrice}-${currentFilters.maxPrice}"]`);
        if (priceCheckbox) priceCheckbox.checked = true;
    } else if (currentFilters.minPrice && !currentFilters.maxPrice) {
        const priceCheckbox = document.querySelector(`input[name="price[]"][value="${currentFilters.minPrice}+"]`);
        if (priceCheckbox) priceCheckbox.checked = true;
    }
    
    // If no URL parameters exist, ensure all checkboxes are unchecked
    if (window.location.search === '' || window.location.search === '?') {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
});
</script>