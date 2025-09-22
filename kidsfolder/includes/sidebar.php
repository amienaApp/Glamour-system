<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count">1000+ Kids Styles</span>
        <button id="clear-filters" class="clear-filters-btn" onclick="clearAllFiltersSimple()">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">
        <!-- Kids Category Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Kids Category</h4>
            </div>
            <div class="filter-options">
                <div class="category-grid" id="kids-category-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="shirts" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Shirts
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="dresses" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Dresses
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="pants" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Pants
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="shorts" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Shorts
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="skirts" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Skirts
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="tops" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Tops
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="kids_category[]" value="accessories" data-filter="kids_category">
                        <span class="checkmark"></span>
                        Accessories
                    </label>
                </div>
            </div>
        </div>

        <!-- Gender Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Gender</h4>
            </div>
            <div class="filter-options">
                <div class="gender-grid" id="gender-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="boys" data-filter="gender">
                        <span class="checkmark"></span>
                        Boys
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="girls" data-filter="gender">
                        <span class="checkmark"></span>
                        Girls
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="unisex" data-filter="gender">
                        <span class="checkmark"></span>
                        Unisex
                    </label>
                </div>
            </div>
        </div>

        <!-- Age Group Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Age Group</h4>
            </div>
            <div class="filter-options">
                <div class="age-grid" id="age-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="2-4" data-filter="age_group">
                        <span class="checkmark"></span>
                        2-4 Years
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="4-6" data-filter="age_group">
                        <span class="checkmark"></span>
                        4-6 Years
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="6-8" data-filter="age_group">
                        <span class="checkmark"></span>
                        6-8 Years
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="8-10" data-filter="age_group">
                        <span class="checkmark"></span>
                        8-10 Years
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="10-12" data-filter="age_group">
                        <span class="checkmark"></span>
                        10-12 Years
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="age[]" value="12-14" data-filter="age_group">
                        <span class="checkmark"></span>
                        12-14 Years
                    </label>
                </div>
            </div>
        </div>

        <!-- Size Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Size</h4>
            </div>
            <div class="filter-options">
                <div class="size-grid" id="size-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="2T" data-filter="size">
                        <span class="checkmark"></span>
                        2T
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="3T" data-filter="size">
                        <span class="checkmark"></span>
                        3T
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="4T" data-filter="size">
                        <span class="checkmark"></span>
                        4T
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="5" data-filter="size">
                        <span class="checkmark"></span>
                        5
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="6" data-filter="size">
                        <span class="checkmark"></span>
                        6
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="7" data-filter="size">
                        <span class="checkmark"></span>
                        7
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="8" data-filter="size">
                        <span class="checkmark"></span>
                        8
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="10" data-filter="size">
                        <span class="checkmark"></span>
                        10
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="12" data-filter="size">
                        <span class="checkmark"></span>
                        12
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="14" data-filter="size">
                        <span class="checkmark"></span>
                        14
                    </label>
                </div>
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Color</h4>
            </div>
            <div class="filter-options">
                <div class="color-grid" id="color-filter">
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="blue" data-filter="color">
                        <span class="color-swatch" style="background-color: #4A90E2;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="red" data-filter="color">
                        <span class="color-swatch" style="background-color: #E74C3C;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="green" data-filter="color">
                        <span class="color-swatch" style="background-color: #2ECC71;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="yellow" data-filter="color">
                        <span class="color-swatch" style="background-color: #F1C40F;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="pink" data-filter="color">
                        <span class="color-swatch" style="background-color: #E91E63;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="purple" data-filter="color">
                        <span class="color-swatch" style="background-color: #9B59B6;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="orange" data-filter="color">
                        <span class="color-swatch" style="background-color: #FF9800;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="black" data-filter="color">
                        <span class="color-swatch" style="background-color: #2C3E50;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="white" data-filter="color">
                        <span class="color-swatch" style="background-color: #ECF0F1; border: 1px solid #BDC3C7;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="gray" data-filter="color">
                        <span class="color-swatch" style="background-color: #95A5A6;"></span>
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
            <div class="filter-options" id="price-filter">
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="on-sale" data-filter="price_range">
                    <span class="checkmark"></span>
                    On Sale
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="0-10" data-filter="price_range">
                    <span class="checkmark"></span>
                    $0 - $10
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="10-20" data-filter="price_range">
                    <span class="checkmark"></span>
                    $10 - $20
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="20-30" data-filter="price_range">
                    <span class="checkmark"></span>
                    $20 - $30
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="30-40" data-filter="price_range">
                    <span class="checkmark"></span>
                    $30 - $40
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="40+" data-filter="price_range">
                    <span class="checkmark"></span>
                    $40+
                </label>
            </div>
        </div>
    </div>

    <!-- Brand Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Brand</h4>
            </div>
            <div class="filter-options" id="brand-filter">
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="carters" data-filter="brand">
                    <span class="checkmark"></span>
                    Carter's
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="gap-kids" data-filter="brand">
                    <span class="checkmark"></span>
                    Gap Kids
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="old-navy" data-filter="brand">
                    <span class="checkmark"></span>
                    Old Navy
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="hm-kids" data-filter="brand">
                    <span class="checkmark"></span>
                    H&M Kids
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="zara-kids" data-filter="brand">
                    <span class="checkmark"></span>
                    Zara Kids
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="uniqlo-kids" data-filter="brand">
                    <span class="checkmark"></span>
                    Uniqlo Kids
                </label>
            </div>
        </div>
    </div>

    </div>
</aside>

<script src="../../instant-filters.js"></script>