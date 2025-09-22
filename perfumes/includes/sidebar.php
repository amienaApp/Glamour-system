<?php
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';

$productModel = new Product();

// Get current filter values from URL
$currentGender = $_GET['gender'] ?? '';
$currentBrand = $_GET['brand'] ?? '';
$currentSize = $_GET['size'] ?? '';
$currentMinPrice = $_GET['min_price'] ?? '';
$currentMaxPrice = $_GET['max_price'] ?? '';

// Get all perfumes to extract brands and sizes
$allPerfumes = $productModel->getAll(['category' => 'Perfumes']);

// Get available brands and sizes
$brands = [];
$sizes = [];
$stats = [
    'total_perfumes' => 0,
    'women_perfumes' => 0,
    'men_perfumes' => 0
];

foreach ($allPerfumes as $perfume) {
    $stats['total_perfumes']++;
    
    if (isset($perfume['gender'])) {
        if ($perfume['gender'] === 'women') $stats['women_perfumes']++;
        if ($perfume['gender'] === 'men') $stats['men_perfumes']++;
    }
    
    if (isset($perfume['brand']) && !in_array($perfume['brand'], $brands)) {
        $brands[] = $perfume['brand'];
    }
    if (isset($perfume['size']) && !in_array($perfume['size'], $sizes)) {
        $sizes[] = $perfume['size'];
    }
}
?>

<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $stats['total_perfumes']; ?> Styles</span>
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
                        Women
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="men" 
                               <?php echo $currentGender === 'men' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('men', this.checked)">
                        <span class="checkmark"></span>
                        Men
                    </label>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <!-- Size Filter -->
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Size <span class="size-count" id="size-count"></span></h4>
                </div>
                <div class="filter-options">
                    <div class="size-grid">
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="30ml" 
                                   <?php echo $currentSize === '30ml' ? 'checked' : ''; ?>
                                   onchange="updateSizeFilter('30ml', this.checked)">
                            <span class="checkmark"></span>
                            30ml
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="50ml" 
                                   <?php echo $currentSize === '50ml' ? 'checked' : ''; ?>
                                   onchange="updateSizeFilter('50ml', this.checked)">
                            <span class="checkmark"></span>
                            50ml
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="100ml" 
                                   <?php echo $currentSize === '100ml' ? 'checked' : ''; ?>
                                   onchange="updateSizeFilter('100ml', this.checked)">
                            <span class="checkmark"></span>
                            100ml
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="200ml" 
                                   <?php echo $currentSize === '200ml' ? 'checked' : ''; ?>
                                   onchange="updateSizeFilter('200ml', this.checked)">
                            <span class="checkmark"></span>
                            200ml
                        </label>
                    </div>
                </div>
            </div>

            <!-- Brand Filter -->
            <div class="filter-section">
                <div class="filter-group">
                    <div class="filter-header">
                        <h4>Brands</h4>
                    </div>
                    <div class="filter-options">
                        <?php foreach ($brands as $brand): ?>
                            <label class="filter-option">
                                <input type="checkbox" name="brand[]" value="<?php echo htmlspecialchars($brand); ?>" 
                                       <?php echo $currentBrand === $brand ? 'checked' : ''; ?>
                                       onchange="updateBrandFilter('<?php echo htmlspecialchars($brand); ?>', this.checked)">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($brand); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="size-actions">
                        <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                        <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Price</h4>
            </div>
            <div class="filter-options">
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="90-110" 
                           <?php echo ($currentMinPrice == '90' && $currentMaxPrice == '110') ? 'checked' : ''; ?>
                           onchange="updatePriceFilter(90, 110, this.checked)">
                    <span class="checkmark"></span>
                    $90 - $110
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="110-150" 
                           <?php echo ($currentMinPrice == '110' && $currentMaxPrice == '150') ? 'checked' : ''; ?>
                           onchange="updatePriceFilter(110, 150, this.checked)">
                    <span class="checkmark"></span>
                    $110 - $150
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="150-200" 
                           <?php echo ($currentMinPrice == '150' && $currentMaxPrice == '200') ? 'checked' : ''; ?>
                           onchange="updatePriceFilter(150, 200, this.checked)">
                    <span class="checkmark"></span>
                    $150 - $200
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="200-250" 
                           <?php echo ($currentMinPrice == '200' && $currentMaxPrice == '250') ? 'checked' : ''; ?>
                           onchange="updatePriceFilter(200, 250, this.checked)">
                    <span class="checkmark"></span>
                    $200 - $250
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="250+" 
                           <?php echo ($currentMinPrice == '250') ? 'checked' : ''; ?>
                           onchange="updatePriceFilter(250, null, this.checked)">
                    <span class="checkmark"></span>
                    $250+
                </label>
            </div>
        </div>
    </div>


</aside>

<script>
// Store current filter state
let currentFilters = {
    gender: '<?php echo $currentGender; ?>',
    brand: '<?php echo $currentBrand; ?>',
    size: '<?php echo $currentSize; ?>',
    minPrice: '<?php echo $currentMinPrice; ?>',
    maxPrice: '<?php echo $currentMaxPrice; ?>'
};

// Function to update gender filter
function updateGenderFilter(gender, checked) {
    if (checked) {
        currentFilters.gender = gender;
    } else {
        currentFilters.gender = '';
    }
    applyFilters();
}

// Function to update brand filter
function updateBrandFilter(brand, checked) {
    if (checked) {
        currentFilters.brand = brand;
    } else {
        currentFilters.brand = '';
    }
    applyFilters();
}

// Function to update size filter
function updateSizeFilter(size, checked) {
    if (checked) {
        currentFilters.size = size;
    } else {
        currentFilters.size = '';
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
    if (currentFilters.brand) params.append('brand', currentFilters.brand);
    if (currentFilters.size) params.append('size', currentFilters.size);
    if (currentFilters.minPrice) params.append('min_price', currentFilters.minPrice);
    if (currentFilters.maxPrice) params.append('max_price', currentFilters.maxPrice);
    
    // Update URL and reload page
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Function to clear all filters
function clearAllFilters() {
    console.log('Clearing all filters...');
    
    // Uncheck all filter checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset filter state
    currentFilters = {
        gender: '',
        brand: '',
        size: '',
        minPrice: '',
        maxPrice: ''
    };
    
    // Show loading state
    showFilterLoading();
    
    // Make AJAX request to get all perfumes
    fetch('filter-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'filter_products',
            gender: '',
            brand: '',
            size: '',
            min_price: '',
            max_price: ''
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
    
    // Find the product grid
    let productGrid = document.getElementById('all-perfumes-grid') || 
                     document.getElementById('filtered-products-grid') ||
                     document.querySelector('#all-perfumes-grid') ||
                     document.querySelector('#filtered-products-grid') ||
                     document.querySelector('.product-grid');
    
    if (!productGrid) {
        console.error('Product grid not found. Available elements:', {
            allPerfumesGrid: !!document.getElementById('all-perfumes-grid'),
            filteredProductsGrid: !!document.getElementById('filtered-products-grid'),
            productGrids: document.querySelectorAll('.product-grid').length
        });
        return;
    }
    
    console.log('Found product grid:', productGrid);
    
    // Clear existing products
    productGrid.innerHTML = '';
    
    if (products.length === 0) {
        productGrid.innerHTML = '<div class="no-products"><p>No perfumes found.</p></div>';
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
    card.className = 'product-card';
    card.setAttribute('data-product-id', product.id);
    
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

function updateStyleCount(count) {
    const styleCountElement = document.querySelector('.style-count');
    if (styleCountElement) {
        styleCountElement.textContent = `${count} Style${count !== 1 ? 's' : ''}`;
    }
}

// Function to select all sizes
function selectAllSizes() {
    const sizeCheckboxes = document.querySelectorAll('input[name="size[]"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
        updateSizeFilter(checkbox.value, true);
    });
}

// Function to clear size filters
function clearSizeFilters() {
    const sizeCheckboxes = document.querySelectorAll('input[name="size[]"]');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    currentFilters.size = '';
    applyFilters();
}

// Make clearAllFilters globally accessible
window.clearAllFilters = clearAllFilters;

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update filter state based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    currentFilters.gender = urlParams.get('gender') || '';
    currentFilters.brand = urlParams.get('brand') || '';
    currentFilters.size = urlParams.get('size') || '';
    currentFilters.minPrice = urlParams.get('min_price') || '';
    currentFilters.maxPrice = urlParams.get('max_price') || '';
});
</script>

<script src="../../instant-filters.js"></script> 