<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">2500+ Beauty Products</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">
        <!-- Beauty Category Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Beauty Category</h4>
            </div>
            <div class="filter-options">
                <div class="category-grid" id="beauty-category-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_categories[]" value="makeup" data-filter="beauty_categories">
                        <span class="checkmark"></span>
                        Makeup
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_categories[]" value="skincare" data-filter="beauty_categories">
                        <span class="checkmark"></span>
                        Skincare
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_categories[]" value="hair" data-filter="beauty_categories">
                        <span class="checkmark"></span>
                        Hair Care
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_categories[]" value="bath-body" data-filter="beauty_categories">
                        <span class="checkmark"></span>
                        Bath & Body
                    </label>
                    
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_categories[]" value="tools" data-filter="beauty_categories">
                        <span class="checkmark"></span>
                        Beauty Tools
                    </label>
                </div>
            </div>
        </div>

        <!-- Makeup Type Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Makeup Type</h4>
            </div>
            <div class="filter-options">
                <div class="makeup-type-grid" id="makeup-type-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_types[]" value="face" data-filter="makeup_types">
                        <span class="checkmark"></span>
                        Face
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_types[]" value="eye" data-filter="makeup_types">
                        <span class="checkmark"></span>
                        Eye
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_types[]" value="lip" data-filter="makeup_types">
                        <span class="checkmark"></span>
                        Lip
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_types[]" value="nails" data-filter="makeup_types">
                        <span class="checkmark"></span>
                        Nails
                    </label>
                </div>
            </div>
        </div>

        <!-- Sub-Subcategory Filter -->
        <div class="filter-group" id="sub-subcategory-filter-group">
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

        <!-- Color Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Color/Shade</h4>
            </div>
            <div class="filter-options">
                <div class="color-grid" id="color-filter">
                    <div class="loading-colors">Loading colors...</div>
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
                    <input type="checkbox" name="price[]" value="0-15" data-filter="price_range">
                    <span class="checkmark"></span>
                    $0 - $15
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="15-30" data-filter="price_range">
                    <span class="checkmark"></span>
                    $15 - $30
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="30-50" data-filter="price_range">
                    <span class="checkmark"></span>
                    $30 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75" data-filter="price_range">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75+" data-filter="price_range">
                    <span class="checkmark"></span>
                    $75+
                </label>
            </div>
        </div>
    </div>

    </div>
</aside>
