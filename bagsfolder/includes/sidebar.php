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

// Get all bags to extract available options
$allBags = $productModel->getAll(['category' => 'Bags']);

// Get available brands and colors
$brands = [];
$colors = [];
foreach ($allBags as $bag) {
    if (isset($bag['brand']) && !in_array($bag['brand'], $brands)) {
        $brands[] = $bag['brand'];
    }
    if (isset($bag['color']) && !in_array($bag['color'], $colors)) {
        $colors[] = $bag['color'];
    }
}
?>

<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count"><?php echo count($allBags); ?> Styles</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
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
        
        <!-- Category Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Categories</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Shoulder Bags" 
                               <?php echo $currentCategory === 'Shoulder Bags' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Shoulder Bags', this.checked)">
                        <span class="checkmark"></span>
                        Shoulder Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Clutches" 
                               <?php echo $currentCategory === 'Clutches' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Clutches', this.checked)">
                        <span class="checkmark"></span>
                        Clutches Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Tote Bags" 
                               <?php echo $currentCategory === 'Tote Bags' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Tote Bags', this.checked)">
                        <span class="checkmark"></span>
                        Tote Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Crossbody Bags" 
                               <?php echo $currentCategory === 'Crossbody Bags' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Crossbody Bags', this.checked)">
                        <span class="checkmark"></span>
                        Crossbody Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Backpacks" 
                               <?php echo $currentCategory === 'Backpacks' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Backpacks', this.checked)">
                        <span class="checkmark"></span>
                        Backpacks
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Briefcases" 
                               <?php echo $currentCategory === 'Briefcases' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Briefcases', this.checked)">
                        <span class="checkmark"></span>
                        Briefcases
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Laptop Bags" 
                               <?php echo $currentCategory === 'Laptop Bags' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Laptop Bags', this.checked)">
                        <span class="checkmark"></span>
                        Laptop Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Waist Bags" 
                               <?php echo $currentCategory === 'Waist Bags' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Waist Bags', this.checked)">
                        <span class="checkmark"></span>
                        Waist Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Wallets" 
                               <?php echo $currentCategory === 'Wallets' ? 'checked' : ''; ?>
                               onchange="updateCategoryFilter('Wallets', this.checked)">
                        <span class="checkmark"></span>
                        Wallets
                    </label>
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
                            <input type="checkbox" name="color[]" value="beige" 
                                   <?php echo $currentColor === 'beige' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('beige', this.checked)">
                            <span class="color-swatch" style="background-color: #f5f5dc;"></span>
                            Beige
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="black" 
                                   <?php echo $currentColor === 'black' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('black', this.checked)">
                            <span class="color-swatch" style="background-color: #000;"></span>
                            Black
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="blue" 
                                   <?php echo $currentColor === 'blue' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('blue', this.checked)">
                            <span class="color-swatch" style="background-color: #0066cc;"></span>
                            Blue
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="brown" 
                                   <?php echo $currentColor === 'brown' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('brown', this.checked)">
                            <span class="color-swatch" style="background-color: #8b4513;"></span>
                            Brown
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="gold" 
                                   <?php echo $currentColor === 'gold' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('gold', this.checked)">
                            <span class="color-swatch" style="background-color: #ffd700;"></span>
                            Gold
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="green" 
                                   <?php echo $currentColor === 'green' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('green', this.checked)">
                            <span class="color-swatch" style="background-color: #228b22;"></span>
                            Green
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="grey" 
                                   <?php echo $currentColor === 'grey' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('grey', this.checked)">
                            <span class="color-swatch" style="background-color: #808080;"></span>
                            Grey
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="orange" 
                                   <?php echo $currentColor === 'orange' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('orange', this.checked)">
                            <span class="color-swatch" style="background-color: #ffa500;"></span>
                            Orange
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="pink" 
                                   <?php echo $currentColor === 'pink' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('pink', this.checked)">
                            <span class="color-swatch" style="background-color: #ffc0cb;"></span>
                            Pink
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="purple" 
                                   <?php echo $currentColor === 'purple' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('purple', this.checked)">
                            <span class="color-swatch" style="background-color: #800080;"></span>
                            Purple
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="red" 
                                   <?php echo $currentColor === 'red' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('red', this.checked)">
                            <span class="color-swatch" style="background-color: #ff0000;"></span>
                            Red
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="silver" 
                                   <?php echo $currentColor === 'silver' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('silver', this.checked)">
                            <span class="color-swatch" style="background-color: #c0c0c0;"></span>
                            Silver
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="taupe" 
                                   <?php echo $currentColor === 'taupe' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('taupe', this.checked)">
                            <span class="color-swatch" style="background-color: #483c32;"></span>
                            Taupe
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="white" 
                                   <?php echo $currentColor === 'white' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('white', this.checked)">
                            <span class="color-swatch" style="background-color: #fff; border: 1px solid #ddd;"></span>
                            White
                        </label>
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
                        <input type="checkbox" name="price[]" value="200-400" 
                               <?php echo ($currentMinPrice == '200' && $currentMaxPrice == '400') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(200, 400, this.checked)">
                        <span class="checkmark"></span>
                        $200 - $400
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="400+" 
                               <?php echo ($currentMinPrice == '400') ? 'checked' : ''; ?>
                               onchange="updatePriceFilter(400, null, this.checked)">
                        <span class="checkmark"></span>
                        $400+
                    </label>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
// Store current filter state
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

// Function to clear all filters
function clearAllFilters() {
    currentFilters = {
        gender: '',
        category: '',
        color: '',
        minPrice: '',
        maxPrice: ''
    };
    
    // Uncheck all filter checkboxes specifically
    document.querySelectorAll('input[name="gender[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('input[name="category[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('input[name="color[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('input[name="price[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Redirect to base URL
    window.location.href = window.location.pathname;
}

// Function to update sort
function updateSort(sortValue) {
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sortValue);
    
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Function to update limit
function updateLimit(limitValue) {
    const params = new URLSearchParams(window.location.search);
    params.set('limit', limitValue);
    
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Function to go to page
function goToPage(pageNumber) {
    const params = new URLSearchParams(window.location.search);
    const limit = parseInt(params.get('limit')) || 60;
    const skip = (pageNumber - 1) * limit;
    params.set('skip', skip);
    
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update filter state based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    currentFilters.gender = urlParams.get('gender') || '';
    currentFilters.category = urlParams.get('category') || '';
    currentFilters.color = urlParams.get('color') || '';
    currentFilters.minPrice = urlParams.get('min_price') || '';
    currentFilters.maxPrice = urlParams.get('max_price') || '';
});
</script>