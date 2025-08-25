<?php
require_once __DIR__ . '/../../config/mongodb.php';
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
        <h3>Refine By</h3>
        <span class="style-count" id="style-count"><?php echo $stats['total_perfumes']; ?> Styles</span>
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
                        Women (<?php echo $stats['women_perfumes']; ?>)
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="men" 
                               <?php echo $currentGender === 'men' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('men', this.checked)">
                        <span class="checkmark"></span>
                        Men (<?php echo $stats['men_perfumes']; ?>)
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
                        <?php foreach ($sizes as $size): ?>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="<?php echo htmlspecialchars($size); ?>" 
                                       <?php echo $currentSize === $size ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('<?php echo htmlspecialchars($size); ?>', this.checked)">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($size); ?>
                            </label>
                        <?php endforeach; ?>
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
    currentFilters = {
        gender: '',
        brand: '',
        size: '',
        minPrice: '',
        maxPrice: ''
    };
    
    // Uncheck all checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Redirect to base URL
    window.location.href = window.location.pathname;
}

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