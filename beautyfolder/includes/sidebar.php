<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">2500+ Beauty Products</span>
        <button id="clear-filters" class="clear-filters-btn" onclick="clearAllFiltersSimple()">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">
        <!-- Beauty Category Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Category</h4>
            </div>
            <div class="filter-options" id="category-filter">
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="makeup" data-filter="category" onchange="updateCategoryFilter('makeup', this.checked)">
                    <span class="checkmark"></span>
                    Makeup
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="skincare" data-filter="category" onchange="updateCategoryFilter('skincare', this.checked)">
                    <span class="checkmark"></span>
                    Skincare
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="hair-care" data-filter="category" onchange="updateCategoryFilter('hair-care', this.checked)">
                    <span class="checkmark"></span>
                    Hair Care
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="bath-body" data-filter="category" onchange="updateCategoryFilter('bath-body', this.checked)">
                    <span class="checkmark"></span>
                    Bath & Body
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="tools" data-filter="category" onchange="updateCategoryFilter('tools', this.checked)">
                    <span class="checkmark"></span>
                    Beauty Tools
                </label>
            </div>
        </div>

        <!-- Beauty Category Type Filter -->
        <div class="filter-group" id="beauty-type-group" style="display: none;">
            <div class="filter-header">
                <h4 id="beauty-type-title">Category Type</h4>
            </div>
            <div class="filter-options">
                <!-- Makeup Types -->
                <div class="beauty-type-grid" id="makeup-types" style="display: none;">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="face" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Face
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="eye" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Eye
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="lip" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Lip
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="nail" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Nail
                    </label>
                </div>
                
                <!-- Skincare Types -->
                <div class="beauty-type-grid" id="skincare-types" style="display: none;">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="moisturizers" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Moisturizers
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="cleansers" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Cleansers
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="masks" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Masks
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="sun-care" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Sun Care
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="cream" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Cream
                    </label>
                </div>
                
                <!-- Hair Care Types -->
                <div class="beauty-type-grid" id="hair-care-types" style="display: none;">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="shampoo" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Shampoo
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="conditioner" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Conditioner
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="tools" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Tools
                    </label>
                </div>
                
                <!-- Bath & Body Types -->
                <div class="beauty-type-grid" id="bath-body-types" style="display: none;">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="shower-gel" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Shower Gel
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="scrubs" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Scrubs
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="soap" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Soap
                    </label>
                </div>
                
                <!-- Beauty Tools Types -->
                <div class="beauty-type-grid" id="beauty-tools-types" style="display: none;">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="makeup-brushes" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Makeup Brushes
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="beauty-sponges" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Beauty Sponges
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="eyelash-curlers" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Eyelash Curlers
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="tweezers" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Tweezers
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="mirrors" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Mirrors
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_types[]" value="storage-organization" data-filter="beauty_types">
                        <span class="checkmark"></span>
                        Storage & Organization
                    </label>
                </div>
            </div>
        </div>

        <!-- Sub-Subcategory Filter -->
        <div class="filter-group" id="sub-subcategory-filter-group" style="display: none;">
            <div class="filter-header">
                <h4>Product Type</h4>
            </div>
            <div class="filter-options">
                <div class="sub-subcategory-grid" id="sub-subcategory-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="foundation" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Foundation
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="concealer" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Concealer
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="powder" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Powder
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="blush" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Blush
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="bronzer" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Bronzer
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="highlighter" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Highlighter
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="eyeshadow" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Eyeshadow
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="eyeliner" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Eyeliner
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="mascara" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Mascara
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="lipstick" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Lipstick
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="lipgloss" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Lip Gloss
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="sub_subcategories[]" value="nail-polish" data-filter="sub_subcategories">
                        <span class="checkmark"></span>
                        Nail Polish
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
            <div class="filter-options" id="price-filter">
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="on-sale" data-filter="price_range">
                    <span class="checkmark"></span>
                    On Sale
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="0-25" data-filter="price_range">
                    <span class="checkmark"></span>
                    $0 - $25
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="25-50" data-filter="price_range">
                    <span class="checkmark"></span>
                    $25 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75" data-filter="price_range">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75-100" data-filter="price_range">
                    <span class="checkmark"></span>
                    $75 - $100
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="100+" data-filter="price_range">
                    <span class="checkmark"></span>
                    $100+
                </label>
            </div>
        </div>
    </div>

    </div>
</aside>

<script src="../../instant-filters.js"></script>
