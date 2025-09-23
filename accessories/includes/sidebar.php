<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Accessories</span>
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
                    <input type="checkbox" name="category[]" value="watches" data-filter="category" onchange="updateCategoryFilter('watches', this.checked)">
                    <span class="checkmark"></span>
                    Watches
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="sunglasses" data-filter="category" onchange="updateCategoryFilter('sunglasses', this.checked)">
                    <span class="checkmark"></span>
                    Sunglasses
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="jewelry" data-filter="category" onchange="updateCategoryFilter('jewelry', this.checked)">
                    <span class="checkmark"></span>
                    Jewelry
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="belts" data-filter="category" onchange="updateCategoryFilter('belts', this.checked)">
                    <span class="checkmark"></span>
                    Belts
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="scarves" data-filter="category" onchange="updateCategoryFilter('scarves', this.checked)">
                    <span class="checkmark"></span>
                    Scarves
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="hats" data-filter="category" onchange="updateCategoryFilter('hats', this.checked)">
                    <span class="checkmark"></span>
                    Hats
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="gloves" data-filter="category" onchange="updateCategoryFilter('gloves', this.checked)">
                    <span class="checkmark"></span>
                    Gloves
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="bags" data-filter="category" onchange="updateCategoryFilter('bags', this.checked)">
                    <span class="checkmark"></span>
                    Bags
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
                        <input type="checkbox" name="size[]" value="S" data-filter="size" onchange="updateSizeFilter('S', this.checked)">
                        <span class="checkmark"></span>
                        S
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="M" data-filter="size" onchange="updateSizeFilter('M', this.checked)">
                        <span class="checkmark"></span>
                        M
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="L" data-filter="size" onchange="updateSizeFilter('L', this.checked)">
                        <span class="checkmark"></span>
                        L
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="X" data-filter="size" onchange="updateSizeFilter('X', this.checked)">
                        <span class="checkmark"></span>
                        X
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="XL" data-filter="size" onchange="updateSizeFilter('XL', this.checked)">
                        <span class="checkmark"></span>
                        XL
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="XXL" data-filter="size" onchange="updateSizeFilter('XXL', this.checked)">
                        <span class="checkmark"></span>
                        XXL
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
                        <?php if (!empty($filterData['colors'])): ?>
                            <?php foreach ($filterData['colors'] as $color): ?>
                                <label class="color-option">
                                    <input type="checkbox" name="color[]" value="<?php echo htmlspecialchars($color); ?>" data-filter="color" onchange="updateColorFilter('<?php echo htmlspecialchars($color); ?>', this.checked)">
                                    <span class="color-swatch" style="background-color: <?php echo htmlspecialchars($color); ?>;"></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-colors">No colors available</p>
                        <?php endif; ?>
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
                <?php if ($filterData['hasOnSale']): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="on-sale" data-filter="price_range" onchange="updatePriceFilter('on-sale', null, this.checked)">
                        <span class="checkmark"></span>
                        On Sale
                    </label>
                <?php endif; ?>
                
                <?php if (!empty($filterData['priceRanges'])): ?>
                    <?php foreach ($filterData['priceRanges'] as $range): ?>
                        <label class="filter-option">
                            <input type="checkbox" name="price[]" value="<?php echo $range['min'] . '-' . ($range['max'] ?? 'max'); ?>" data-filter="price_range" onchange="updatePriceFilter(<?php echo $range['min']; ?>, <?php echo $range['max'] ?? 'null'; ?>, this.checked)">
                            <span class="checkmark"></span>
                            <?php echo htmlspecialchars($range['label']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-prices">No price ranges available</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    

    
    </div>
</aside>
