<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">Bags</span>
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
                        <input type="checkbox" name="category[]" value="shoulder-bags" data-filter="category">
                        <span class="checkmark"></span>
                        Shoulder Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="clutches" data-filter="category">
                        <span class="checkmark"></span>
                        Clutches
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="tote-bags" data-filter="category">
                        <span class="checkmark"></span>
                        Tote Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="crossbody-bags" data-filter="category">
                        <span class="checkmark"></span>
                        Crossbody Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="backpacks" data-filter="category">
                        <span class="checkmark"></span>
                        Backpacks
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="briefcases" data-filter="category">
                        <span class="checkmark"></span>
                        Briefcases
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="laptop-bags" data-filter="category">
                        <span class="checkmark"></span>
                        Laptop Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="waist-bags" data-filter="category">
                        <span class="checkmark"></span>
                        Waist Bags
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="wallets" data-filter="category">
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
                    <div class="color-grid" id="color-filter">
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#f5f5dc" data-filter="color">
                            <span class="color-swatch" style="background-color: #f5f5dc;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#000000" data-filter="color">
                            <span class="color-swatch" style="background-color: #000000;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#0066cc" data-filter="color">
                            <span class="color-swatch" style="background-color: #0066cc;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#8b4513" data-filter="color">
                            <span class="color-swatch" style="background-color: #8b4513;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ffd700" data-filter="color">
                            <span class="color-swatch" style="background-color: #ffd700;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#228b22" data-filter="color">
                            <span class="color-swatch" style="background-color: #228b22;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#808080" data-filter="color">
                            <span class="color-swatch" style="background-color: #808080;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ffa500" data-filter="color">
                            <span class="color-swatch" style="background-color: #ffa500;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ffc0cb" data-filter="color">
                            <span class="color-swatch" style="background-color: #ffc0cb;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#800080" data-filter="color">
                            <span class="color-swatch" style="background-color: #800080;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ff0000" data-filter="color">
                            <span class="color-swatch" style="background-color: #ff0000;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#c0c0c0" data-filter="color">
                            <span class="color-swatch" style="background-color: #c0c0c0;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#483c32" data-filter="color">
                            <span class="color-swatch" style="background-color: #483c32;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ffffff" data-filter="color">
                            <span class="color-swatch" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
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
                        <input type="checkbox" name="price[]" value="0-100" data-filter="price_range">
                        <span class="checkmark"></span>
                        $0 - $100
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="100-200" data-filter="price_range">
                        <span class="checkmark"></span>
                        $100 - $200
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="200-400" data-filter="price_range">
                        <span class="checkmark"></span>
                        $200 - $400
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="400+" data-filter="price_range">
                        <span class="checkmark"></span>
                        $400+
                    </label>
                </div>
            </div>
        </div>
    </div>
</aside>