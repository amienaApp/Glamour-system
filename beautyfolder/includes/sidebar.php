<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Beauty Products</span>
        <button id="clear-filters" class="clear-filters-btn" onclick="clearAllFilters()">Clear All Filters</button>
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
                <?php if (!empty($subcategories)): ?>
                    <?php foreach ($subcategories as $subcategory): ?>
                        <label class="filter-option">
                            <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($subcategory); ?>" onchange="filterByCategory('<?php echo htmlspecialchars($subcategory); ?>', this.checked)">
                            <span class="checkmark"></span>
                            <?php echo htmlspecialchars($subcategory); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback categories if no subcategories found -->
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Makeup" onchange="filterByCategory('Makeup', this.checked)">
                        <span class="checkmark"></span>
                        Makeup
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Skincare" onchange="filterByCategory('Skincare', this.checked)">
                        <span class="checkmark"></span>
                        Skincare
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Hair Care" onchange="filterByCategory('Hair Care', this.checked)">
                        <span class="checkmark"></span>
                        Hair Care
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Fragrance" onchange="filterByCategory('Fragrance', this.checked)">
                        <span class="checkmark"></span>
                        Fragrance
                    </label>
                <?php endif; ?>
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
                    <!-- Simple, clean beauty sizes -->
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Sample" onchange="filterBySize('Sample', this.checked)">
                        <span class="checkmark"></span>
                        Sample
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Travel" onchange="filterBySize('Travel', this.checked)">
                        <span class="checkmark"></span>
                        Travel
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Regular" onchange="filterBySize('Regular', this.checked)">
                        <span class="checkmark"></span>
                        Regular
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Large" onchange="filterBySize('Large', this.checked)">
                        <span class="checkmark"></span>
                        Large
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Jumbo" onchange="filterBySize('Jumbo', this.checked)">
                        <span class="checkmark"></span>
                        Jumbo
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Mini" onchange="filterBySize('Mini', this.checked)">
                        <span class="checkmark"></span>
                        Mini
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Small" onchange="filterBySize('Small', this.checked)">
                        <span class="checkmark"></span>
                        Small
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Medium" onchange="filterBySize('Medium', this.checked)">
                        <span class="checkmark"></span>
                        Medium
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="Family" onchange="filterBySize('Family', this.checked)">
                        <span class="checkmark"></span>
                        Family
                    </label>
                </div>
                <div class="size-actions">
                    <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                    <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
                </div>
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Color</h4>
                    <small style="color: #666; font-size: 10px;">
                        <?php echo count($allColors); ?> colors available
                    </small>
                </div>
                <div class="filter-options">
                    <div class="color-grid" id="color-filter">
                        <?php if (!empty($allColors)): ?>
                            <?php foreach ($allColors as $color): ?>
                                <label class="color-option">
                                    <input type="checkbox" name="color[]" value="<?php echo htmlspecialchars($color); ?>" onchange="filterByColor('<?php echo htmlspecialchars($color); ?>', this.checked)">
                                    <span class="color-swatch" style="background-color: <?php echo htmlspecialchars($color); ?>;"></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback colors if no colors found in database -->
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#ff69b4" onchange="filterByColor('#ff69b4', this.checked)">
                                <span class="color-swatch" style="background-color: #ff69b4;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#ffc0cb" onchange="filterByColor('#ffc0cb', this.checked)">
                                <span class="color-swatch" style="background-color: #ffc0cb;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#ffd700" onchange="filterByColor('#ffd700', this.checked)">
                                <span class="color-swatch" style="background-color: #ffd700;"></span>
                            </label>
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
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="on-sale" onchange="filterByPrice('on-sale', this.checked)">
                    <span class="checkmark"></span>
                    On Sale
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="0-25" onchange="filterByPrice('0-25', this.checked)">
                    <span class="checkmark"></span>
                    $0 - $25
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="25-50" onchange="filterByPrice('25-50', this.checked)">
                    <span class="checkmark"></span>
                    $25 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75" onchange="filterByPrice('50-75', this.checked)">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75-100" onchange="filterByPrice('75-100', this.checked)">
                    <span class="checkmark"></span>
                    $75 - $100
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="100+" onchange="filterByPrice('100+', this.checked)">
                    <span class="checkmark"></span>
                    $100+
                </label>
            </div>
        </div>
    </div>

    

    
    </div>
</aside>