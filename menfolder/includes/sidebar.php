<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count">12 Styles</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
    </div>
    <div class="side">

        
        <!-- Category Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Category</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="shirts" data-filter="category">
                        <span class="checkmark"></span>
                        Shirts
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="T-Shirts" data-filter="category">
                        <span class="checkmark"></span>
                        T-Shirts
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="suits" data-filter="category">
                        <span class="checkmark"></span>
                        Suits
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="pants" data-filter="category">
                        <span class="checkmark"></span>
                        Pants
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="shorts" data-filter="category">
                        <span class="checkmark"></span>
                        Shorts & Underwear
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="hoodies" data-filter="category">
                        <span class="checkmark"></span>
                        Hoodies & Sweatshirts
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
                    <div class="size-grid" id="size-filter">
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="XS" data-filter="size">
                            <span class="checkmark"></span>
                            XS
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="S" data-filter="size">
                            <span class="checkmark"></span>
                            S
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="M" data-filter="size">
                            <span class="checkmark"></span>
                            M
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="L" data-filter="size">
                            <span class="checkmark"></span>
                            L
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="XL" data-filter="size">
                            <span class="checkmark"></span>
                            XL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="XXL" data-filter="size">
                            <span class="checkmark"></span>
                            XXL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="2XL" data-filter="size">
                            <span class="checkmark"></span>
                            2XL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="3XL" data-filter="size">
                            <span class="checkmark"></span>
                            3XL
                        </label>
                    </div>
                    <div class="size-actions">
                        <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                        <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
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
                    <div class="color-grid" id="color-filter-options">
                        <!-- Colors will be loaded dynamically from database -->
                        <div class="loading-colors" style="text-align: center; padding: 20px; color: #666;">
                            <i class="fas fa-spinner fa-spin"></i> Loading colors...
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
                        <input type="checkbox" name="price[]" value="0-25" data-filter="price_range">
                        <span class="checkmark"></span>
                        Under $25
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="25-50" data-filter="price_range">
                        <span class="checkmark"></span>
                        $25 - $50
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="50-100" data-filter="price_range">
                        <span class="checkmark"></span>
                        $50 - $100
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="100-200" data-filter="price_range">
                        <span class="checkmark"></span>
                        $100 - $200
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="200+" data-filter="price_range">
                        <span class="checkmark"></span>
                        Over $200
                    </label>
                </div>
            </div>
        </div>


        <!-- Availability Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Availability</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="in-stock">
                        <span class="checkmark"></span>
                        In Stock
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="on-sale">
                        <span class="checkmark"></span>
                        On Sale
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="new-arrival">
                        <span class="checkmark"></span>
                        New Arrival
                    </label>
                </div>
            </div>
        </div>
    </div>
</aside> 