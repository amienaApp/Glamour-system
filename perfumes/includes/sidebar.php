<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Perfumes</span>
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
                    <input type="checkbox" name="category[]" value="mens-perfume" data-filter="category" onchange="updateCategoryFilter('mens-perfume', this.checked)">
                    <span class="checkmark"></span>
                    Men's Perfume
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="womens-perfume" data-filter="category" onchange="updateCategoryFilter('womens-perfume', this.checked)">
                    <span class="checkmark"></span>
                    Women's Perfume
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="unisex-perfume" data-filter="category" onchange="updateCategoryFilter('unisex-perfume', this.checked)">
                    <span class="checkmark"></span>
                    Unisex Perfume
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="cologne" data-filter="category" onchange="updateCategoryFilter('cologne', this.checked)">
                    <span class="checkmark"></span>
                    Cologne
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="eau-de-toilette" data-filter="category" onchange="updateCategoryFilter('eau-de-toilette', this.checked)">
                    <span class="checkmark"></span>
                    Eau de Toilette
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="eau-de-parfum" data-filter="category" onchange="updateCategoryFilter('eau-de-parfum', this.checked)">
                    <span class="checkmark"></span>
                    Eau de Parfum
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="perfume-sets" data-filter="category" onchange="updateCategoryFilter('perfume-sets', this.checked)">
                    <span class="checkmark"></span>
                    Perfume Sets
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="mini-perfumes" data-filter="category" onchange="updateCategoryFilter('mini-perfumes', this.checked)">
                    <span class="checkmark"></span>
                    Mini Perfumes
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
                        <input type="checkbox" name="size[]" value="X" data-filter="size">
                        <span class="checkmark"></span>
                        X
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
