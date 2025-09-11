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
                        <input type="checkbox" name="beauty_category[]" value="makeup" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Makeup
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="skincare" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Skincare
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="hair" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Hair Care
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="bath-body" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Bath & Body
                    </label>
                    
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="tools" data-filter="beauty_category">
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
                        <input type="checkbox" name="makeup_type[]" value="face" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Face
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="eye" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Eye
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="lip" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Lip
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="nails" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Nails
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
                    <!-- Dynamic content will be loaded here -->
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
