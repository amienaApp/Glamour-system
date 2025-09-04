<?php
// Get current filter values from URL
$currentGender = $_GET['gender'] ?? '';
$currentSize = $_GET['size'] ?? '';
$currentColor = $_GET['color'] ?? '';
$currentPriceRange = $_GET['price_range'] ?? '';

// Get all shoes to extract stats
require_once __DIR__ . '/../../config1/mongodb.php';
require_once __DIR__ . '/../../models/Product.php';
$productModel = new Product();
$allShoes = $productModel->getByCategory("Shoes");

// Get stats
$stats = [
    'total_shoes' => count($allShoes),
    'women_shoes' => 0,
    'men_shoes' => 0,
    'children_shoes' => 0,
    'infant_shoes' => 0
];

foreach ($allShoes as $shoe) {
    if (isset($shoe['subcategory'])) {
        if (strpos($shoe['subcategory'], 'Women') !== false) $stats['women_shoes']++;
        if (strpos($shoe['subcategory'], 'Men') !== false) $stats['men_shoes']++;
        if (strpos($shoe['subcategory'], 'Kids') !== false) {
            // Check if it's an infant shoe
            $isInfant = false;
            
            // Check if "infant" is in the name
            if (isset($shoe['name']) && stripos($shoe['name'], 'infant') !== false) {
                $isInfant = true;
            }
            
            // Check if it has infant sizes (≤ 25)
            if (!$isInfant && isset($shoe['selected_sizes']) && is_array($shoe['selected_sizes'])) {
                foreach ($shoe['selected_sizes'] as $size) {
                    if (is_numeric($size) && (int)$size <= 25) {
                        $isInfant = true;
                        break;
                    }
                }
            }
            
            // Check color variants for infant sizes
            if (!$isInfant && isset($shoe['color_variants']) && is_array($shoe['color_variants'])) {
                foreach ($shoe['color_variants'] as $variant) {
                    if (isset($variant['selected_sizes']) && is_array($variant['selected_sizes'])) {
                        foreach ($variant['selected_sizes'] as $size) {
                            if (is_numeric($size) && (int)$size <= 25) {
                                $isInfant = true;
                                break 2;
                            }
                        }
                    }
                }
            }
            
            if ($isInfant) {
                $stats['infant_shoes']++;
            } else {
                // Count as children shoe (not infant)
                $stats['children_shoes']++;
            }
        }
    }
}
?>

<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count"><?php echo count($allShoes); ?> Styles</span>
        <button id="clear-filters" class="clear-filters-btn" onclick="clearAllFilters()">Clear All Filters</button>
    </div>
    
    <!-- Gender Filter -->
    <div class="filter-section">
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
                    Women (<?php echo $stats['women_shoes']; ?>)
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="gender[]" value="men" 
                           <?php echo $currentGender === 'men' ? 'checked' : ''; ?>
                           onchange="updateGenderFilter('men', this.checked)">
                    <span class="checkmark"></span>
                    Men (<?php echo $stats['men_shoes']; ?>)
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="gender[]" value="children" id="children-checkbox" 
                           <?php echo $currentGender === 'children' ? 'checked' : ''; ?>
                           onchange="updateGenderFilter('children', this.checked)">
                    <span class="checkmark"></span>
                    Children (<?php echo $stats['children_shoes']; ?>)
                </label>
                
                <!-- Children Subcategory (Hidden by default) -->
                <div id="children-subcategory" style="display: none; margin-left: 20px; margin-top: 10px;">
                    <label class="filter-option">
                        <input type="checkbox" name="children[]" value="boys" 
                               <?php echo $currentGender === 'boys' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('boys', this.checked)">
                        <span class="checkmark"></span>
                        Boys
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="children[]" value="girls" 
                               <?php echo $currentGender === 'girls' ? 'checked' : ''; ?>
                               onchange="updateGenderFilter('girls', this.checked)">
                        <span class="checkmark"></span>
                        Girls
                    </label>
                </div>
                <label class="filter-option">
                    <input type="checkbox" name="children[]" value="infant" 
                           <?php echo $currentGender === 'infant' ? 'checked' : ''; ?>
                           onchange="updateGenderFilter('infant', this.checked)">
                    <span class="checkmark"></span>
                    Infant (<?php echo $stats['infant_shoes']; ?>)
                </label>
            </div>
        </div>
    </div>

    <!-- Size Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Size <span class="size-count" id="size-count"></span></h4>
            </div>
            <div class="filter-options">
                <!-- Adult Sizes -->
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory('adult-sizes')">
                        <span class="category-title">Adult</span>
                        
                    </div>
                    <div class="size-category-content" id="adult-sizes" style="display: none;">
                        <div class="size-grid">
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="35" 
                                       <?php echo $currentSize === '35' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('35', this.checked)">
                                <span class="checkmark"></span>
                                35
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="36" 
                                       <?php echo $currentSize === '36' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('36', this.checked)">
                                <span class="checkmark"></span>
                                36
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="37" 
                                       <?php echo $currentSize === '37' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('37', this.checked)">
                                <span class="checkmark"></span>
                                37
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="38" 
                                       <?php echo $currentSize === '38' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('38', this.checked)">
                                <span class="checkmark"></span>
                                38
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="39" 
                                       <?php echo $currentSize === '39' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('39', this.checked)">
                                <span class="checkmark"></span>
                                39
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="40" 
                                       <?php echo $currentSize === '40' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('40', this.checked)">
                                <span class="checkmark"></span>
                                40
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="41" 
                                       <?php echo $currentSize === '41' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('41', this.checked)">
                                <span class="checkmark"></span>
                                41
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="42" 
                                       <?php echo $currentSize === '42' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('42', this.checked)">
                                <span class="checkmark"></span>
                                42
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="43" 
                                       <?php echo $currentSize === '43' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('43', this.checked)">
                                <span class="checkmark"></span>
                                43
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="44" 
                                       <?php echo $currentSize === '44' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('44', this.checked)">
                                <span class="checkmark"></span>
                                44
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="45" 
                                       <?php echo $currentSize === '45' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('45', this.checked)">
                                <span class="checkmark"></span>
                                45
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="46" 
                                       <?php echo $currentSize === '46' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('46', this.checked)">
                                <span class="checkmark"></span>
                                46
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Children Sizes -->
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory('children-sizes')">
                        <span class="category-title">Children</span>
                        
                    </div>
                    <div class="size-category-content" id="children-sizes" style="display: none;">
                        <div class="size-grid">
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="6" 
                                       <?php echo $currentSize === '6' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('6', this.checked)">
                                <span class="checkmark"></span>
                                6
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="7" 
                                       <?php echo $currentSize === '7' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('7', this.checked)">
                                <span class="checkmark"></span>
                                7
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="8" 
                                       <?php echo $currentSize === '8' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('8', this.checked)">
                                <span class="checkmark"></span>
                                8
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="10" 
                                       <?php echo $currentSize === '10' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('10', this.checked)">
                                <span class="checkmark"></span>
                                10
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="12" 
                                       <?php echo $currentSize === '12' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('12', this.checked)">
                                <span class="checkmark"></span>
                                12
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="14" 
                                       <?php echo $currentSize === '14' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('14', this.checked)">
                                <span class="checkmark"></span>
                                14
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="16" 
                                       <?php echo $currentSize === '16' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('16', this.checked)">
                                <span class="checkmark"></span>
                                16
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="18" 
                                       <?php echo $currentSize === '18' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('18', this.checked)">
                                <span class="checkmark"></span>
                                18
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="20" 
                                       <?php echo $currentSize === '20' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('20', this.checked)">
                                <span class="checkmark"></span>
                                20
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="22" 
                                       <?php echo $currentSize === '22' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('22', this.checked)">
                                <span class="checkmark"></span>
                                22
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="24" 
                                       <?php echo $currentSize === '24' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('24', this.checked)">
                                <span class="checkmark"></span>
                                24
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="26" 
                                       <?php echo $currentSize === '26' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('26', this.checked)">
                                <span class="checkmark"></span>
                                26
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="28" 
                                       <?php echo $currentSize === '28' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('28', this.checked)">
                                <span class="checkmark"></span>
                                28
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="32" 
                                       <?php echo $currentSize === '32' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('32', this.checked)">
                                <span class="checkmark"></span>
                                32
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="34" 
                                       <?php echo $currentSize === '34' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('34', this.checked)">
                                <span class="checkmark"></span>
                                34
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Infant Sizes -->
                <div class="size-category">
                    <div class="size-category-header" onclick="toggleSizeCategory('infant-sizes')">
                        <span class="category-title">Infant</span>
                        
                    </div>
                    <div class="size-category-content" id="infant-sizes" style="display: none;">
                        <div class="size-grid sizes">
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="1-4-month" 
                                       <?php echo $currentSize === '1-4-month' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('1-4-month', this.checked)">
                                <span class="checkmark"></span>
                                1 to 4 m
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="5-8-month" 
                                       <?php echo $currentSize === '5-8-month' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('5-8-month', this.checked)">
                                <span class="checkmark"></span>
                                5 to 8 m
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size[]" value="9-13-month" 
                                       <?php echo $currentSize === '9-13-month' ? 'checked' : ''; ?>
                                       onchange="updateSizeFilter('9-13-month', this.checked)">
                                <span class="checkmark"></span>
                                9 to 13 m
                            </label>
                        </div>
                    </div>
                </div>
                <div class="size-actions">
                    <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                    <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shoe Type Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Shoe Type</h4>
            </div>
            <div class="filter-options">
                <div class="dropdown-filter">
                    <select id="shoe-type-dropdown" name="shoe_type" class="shoe-type-select">
                        <option value="">All Shoe Types</option>
                        <option value="boots">Boots</option>
                        <option value="sandals">Sandals</option>
                        <option value="heels">Heels</option>
                        <option value="flats">Flats</option>
                        <option value="sneakers">Sneakers</option>
                        <option value="sport-shoes">Sport Shoes</option>
                        <option value="slippers">Slippers</option>
                        <option value="formal-shoes">Formal Shoes</option>
                        <option value="casual-shoes">Casual Shoes</option>
                    </select>
                </div>
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
                        <span class="color-swatch" style="background-color: #ecbcaf;"></span>
                        Beige
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
                        <span class="color-swatch" style="background-color: #422d58;"></span>
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
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="yellow" 
                               <?php echo $currentColor === 'yellow' ? 'checked' : ''; ?>
                               onchange="updateColorFilter('yellow', this.checked)">
                        <span class="color-swatch" style="background-color: #ffff00;"></span>
                        Yellow
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
                    <input type="checkbox" name="price[]" value="on-sale" 
                           <?php echo $currentPriceRange === 'on-sale' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('on-sale', this.checked)">
                    <span class="checkmark"></span>
                    On Sale
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="0-25" 
                           <?php echo $currentPriceRange === '0-25' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('0-25', this.checked)">
                    <span class="checkmark"></span>
                    $0 - $25
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="25-50" 
                           <?php echo $currentPriceRange === '25-50' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('25-50', this.checked)">
                    <span class="checkmark"></span>
                    $25 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75" 
                           <?php echo $currentPriceRange === '50-75' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('50-75', this.checked)">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75-100" 
                           <?php echo $currentPriceRange === '75-100' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('75-100', this.checked)">
                    <span class="checkmark"></span>
                    $75 - $100
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="100+" 
                           <?php echo $currentPriceRange === '100+' ? 'checked' : ''; ?>
                           onchange="updatePriceRangeFilter('100+', this.checked)">
                    <span class="checkmark"></span>
                    $100+
                </label>
            </div>
        </div>
    </div>
</aside>

<script>
// Store current filter state
let currentFilters = {
    gender: '<?php echo $currentGender; ?>',
    size: '<?php echo $currentSize; ?>',
    color: '<?php echo $currentColor; ?>',
    priceRange: '<?php echo $currentPriceRange; ?>'
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

// Function to update size filter
function updateSizeFilter(size, checked) {
    if (checked) {
        currentFilters.size = size;
    } else {
        currentFilters.size = '';
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

// Function to update price range filter
function updatePriceRangeFilter(priceRange, checked) {
    if (checked) {
        currentFilters.priceRange = priceRange;
    } else {
        currentFilters.priceRange = '';
    }
    applyFilters();
}

// Function to apply all filters
function applyFilters() {
    const params = new URLSearchParams();
    
    // Get current subcategory from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSubcategory = urlParams.get('subcategory') || '';
    if (currentSubcategory) {
        params.append('subcategory', currentSubcategory);
    }
    
    if (currentFilters.gender) params.append('gender', currentFilters.gender);
    if (currentFilters.size) params.append('size', currentFilters.size);
    if (currentFilters.color) params.append('color', currentFilters.color);
    if (currentFilters.priceRange) params.append('price_range', currentFilters.priceRange);
    
    // Update URL and reload page
    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
    window.location.reload();
}

// Function to clear all filters
function clearAllFilters() {
    currentFilters = {
        gender: '',
        size: '',
        color: '',
        priceRange: ''
    };
    
    // Uncheck all checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Redirect to base URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSubcategory = urlParams.get('subcategory') || '';
    if (currentSubcategory) {
        window.location.href = window.location.pathname + '?subcategory=' + currentSubcategory;
    } else {
        window.location.href = window.location.pathname;
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

// JavaScript to show/hide children subcategory
document.addEventListener('DOMContentLoaded', function() {
    const childrenCheckbox = document.getElementById('children-checkbox');
    const childrenSubcategory = document.getElementById('children-subcategory');
    
    if (childrenCheckbox && childrenSubcategory) {
        childrenCheckbox.addEventListener('change', function() {
            if (this.checked) {
                childrenSubcategory.style.display = 'block';
                // Add smooth animation
                childrenSubcategory.style.opacity = '0';
                childrenSubcategory.style.transform = 'translateY(-5px)';
                setTimeout(() => {
                    childrenSubcategory.style.opacity = '1';
                    childrenSubcategory.style.transform = 'translateY(0)';
                }, 10);
            } else {
                childrenSubcategory.style.display = 'none';
                // Uncheck all children subcategory checkboxes when hiding
                const childrenCheckboxes = childrenSubcategory.querySelectorAll('input[type="checkbox"]');
                childrenCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        });
    }
});

// JavaScript function to toggle size categories
function toggleSizeCategory(categoryId) {
    const content = document.getElementById(categoryId);
    const toggleIcon = document.getElementById(categoryId.replace('-sizes', '-toggle'));
    
    if (content.style.display === 'none' || content.style.display === '') {
        // Show the category
        content.style.display = 'block';
        if (toggleIcon) toggleIcon.textContent = '▲';
        // Add smooth animation
        content.style.opacity = '0';
        content.style.transform = 'translateY(-5px)';
        setTimeout(() => {
            content.style.opacity = '1';
            content.style.transform = 'translateY(0)';
        }, 10);
    } else {
        // Hide the category
        content.style.display = 'none';
        if (toggleIcon) toggleIcon.textContent = '▼';
        // Uncheck all checkboxes when hiding
        const checkboxes = content.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update filter state based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    currentFilters.gender = urlParams.get('gender') || '';
    currentFilters.size = urlParams.get('size') || '';
    currentFilters.color = urlParams.get('color') || '';
    currentFilters.priceRange = urlParams.get('price_range') || '';
});
</script> 