<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count">4146 Styles</span>
        <button id="clear-filters" class="clear-filters-btn" onclick="clearAllFiltersSimple()">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">

    <!-- Category Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Category</h4>
            </div>
            <div class="filter-options" id="category-filter">
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="boys" data-filter="category">
                    <span class="checkmark"></span>
                    Boys
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="girls" data-filter="category">
                    <span class="checkmark"></span>
                    Girls
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="baby" data-filter="category">
                    <span class="checkmark"></span>
                    Baby
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="toddler" data-filter="category">
                    <span class="checkmark"></span>
                    Toddler
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="accessories" data-filter="category">
                    <span class="checkmark"></span>
                    Accessories
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="shoes" data-filter="category">
                    <span class="checkmark"></span>
                    Shoes
                </label>
            </div>
        </div>
    </div>
        <!-- Size Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Size</h4>
                <span class="size-count" id="size-count"></span>
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
                        <input type="checkbox" name="size[]" value="5T" data-filter="size">
                        <span class="checkmark"></span>
                        5T
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
                <div class="size-actions">
                    <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                    <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
                </div>
                <script>
                    // Ensure size filter functions are available
                    if (typeof selectAllSizes === 'undefined') {
                        window.selectAllSizes = function() {
                            const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
                            sizeCheckboxes.forEach(checkbox => {
                                checkbox.checked = true;
                                const changeEvent = new Event('change', { bubbles: true });
                                checkbox.dispatchEvent(changeEvent);
                            });
                            // Update count if function exists
                            if (typeof updateSizeCount === 'function') {
                                updateSizeCount();
                            }
                        };
                    }
                    
                    if (typeof clearSizeFilters === 'undefined') {
                        window.clearSizeFilters = function() {
                            const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
                            sizeCheckboxes.forEach(checkbox => {
                                checkbox.checked = false;
                                const changeEvent = new Event('change', { bubbles: true });
                                checkbox.dispatchEvent(changeEvent);
                            });
                            // Update count if function exists
                            if (typeof updateSizeCount === 'function') {
                                updateSizeCount();
                            }
                        };
                    }
                </script>
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
                            <input type="checkbox" name="color[]" value="#050505" data-filter="color">
                            <span class="color-swatch" style="background-color: #050505;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#0d0d0d" data-filter="color">
                            <span class="color-swatch" style="background-color: #0d0d0d;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#121212" data-filter="color">
                            <span class="color-swatch" style="background-color: #121212;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#1b6435" data-filter="color">
                            <span class="color-swatch" style="background-color: #1b6435;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#1f1e21" data-filter="color">
                            <span class="color-swatch" style="background-color: #1f1e21;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#473c3f" data-filter="color">
                            <span class="color-swatch" style="background-color: #473c3f;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#4b1924" data-filter="color">
                            <span class="color-swatch" style="background-color: #4b1924;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#5e331c" data-filter="color">
                            <span class="color-swatch" style="background-color: #5e331c;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#6d79ab" data-filter="color">
                            <span class="color-swatch" style="background-color: #6d79ab;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#754640" data-filter="color">
                            <span class="color-swatch" style="background-color: #754640;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#782121" data-filter="color">
                            <span class="color-swatch" style="background-color: #782121;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#917e7e" data-filter="color">
                            <span class="color-swatch" style="background-color: #917e7e;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#b4e5fe" data-filter="color">
                            <span class="color-swatch" style="background-color: #b4e5fe;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#b9ba95" data-filter="color">
                            <span class="color-swatch" style="background-color: #b9ba95;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#d1f5ef" data-filter="color">
                            <span class="color-swatch" style="background-color: #d1f5ef;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#d6efff" data-filter="color">
                            <span class="color-swatch" style="background-color: #d6efff;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#e9e0de" data-filter="color">
                            <span class="color-swatch" style="background-color: #e9e0de;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ea6695" data-filter="color">
                            <span class="color-swatch" style="background-color: #ea6695;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#eed3d3" data-filter="color">
                            <span class="color-swatch" style="background-color: #eed3d3;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#f67fbc" data-filter="color">
                            <span class="color-swatch" style="background-color: #f67fbc;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#faf3f8" data-filter="color">
                            <span class="color-swatch" style="background-color: #faf3f8;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#fe6ca6" data-filter="color">
                            <span class="color-swatch" style="background-color: #fe6ca6;"></span>
                        </label>
                        <label class="color-option">
                            <input type="checkbox" name="color[]" value="#ffccea" data-filter="color">
                            <span class="color-swatch" style="background-color: #ffccea;"></span>
                        </label>
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
