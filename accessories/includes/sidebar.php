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
    </div>
    <div class="side">
        <!-- Clear All Filters Button -->
        <div class="filter-section">
            <div class="filter-group">
                <button type="button" class="clear-all-filters-btn" onclick="clearAllFilters()">
                    <i class="fas fa-times"></i>
                    Clear All Filters
                </button>
            </div>
        </div>
        
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
                            Black
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="beige" 
                                   <?php echo $currentColor === 'beige' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('beige', this.checked)">
                            <span class="color-swatch" style="background-color: #dac0b4;"></span>
                            Beige
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="blue" 
                                   <?php echo $currentColor === 'blue' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('blue', this.checked)">
                            <span class="color-swatch" style="background-color: #0a1e3b;"></span>
                            Blue
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="brown" 
                                   <?php echo $currentColor === 'brown' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('brown', this.checked)">
                            <span class="color-swatch" style="background-color: #966345;"></span>
                            Brown
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="gold" 
                                   <?php echo $currentColor === 'gold' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('gold', this.checked)">
                            <span class="color-swatch" style="background-color: #f9d07f;"></span>
                            Gold
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="green" 
                                   <?php echo $currentColor === 'green' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('green', this.checked)">
                            <span class="color-swatch" style="background-color: #04613f;"></span>
                            Green
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="grey" 
                                   <?php echo $currentColor === 'grey' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('grey', this.checked)">
                            <span class="color-swatch" style="background-color: #676b6e;"></span>
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
                            <span class="color-swatch" style="background-color: #63678f;"></span>
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
                            <input type="checkbox" name="color[]" value="white" 
                                   <?php echo $currentColor === 'white' ? 'checked' : ''; ?>
                                   onchange="updateColorFilter('white', this.checked)">
                            <span class="color-swatch" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
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

// Function to clear all filters
function clearAllFilters() {
    // Reset current filters state
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
    
    // Clear all URL parameters and reload the page to show all accessories products
    const baseUrl = window.location.pathname;
    
    // Use replaceState to clear URL parameters
    window.history.replaceState({}, document.title, baseUrl);
    
    // Force reload to show all accessories products
    window.location.reload();
}

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