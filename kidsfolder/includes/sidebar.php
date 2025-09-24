<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count">4146 Styles</span>
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
                        <input type="checkbox" name="category[]" value="Boys" onchange="filterByCategory('Boys', this.checked)">
                        <span class="checkmark"></span>
                        Boys
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Girls" onchange="filterByCategory('Girls', this.checked)">
                        <span class="checkmark"></span>
                        Girls
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Baby" onchange="filterByCategory('Baby', this.checked)">
                        <span class="checkmark"></span>
                        Baby
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Toddler" onchange="filterByCategory('Toddler', this.checked)">
                        <span class="checkmark"></span>
                        Toddler
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
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="S" onchange="filterBySize('S', this.checked)">
                        <span class="checkmark"></span>
                        S
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="M" onchange="filterBySize('M', this.checked)">
                        <span class="checkmark"></span>
                        M
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="L" onchange="filterBySize('L', this.checked)">
                        <span class="checkmark"></span>
                        L
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="X" onchange="filterBySize('X', this.checked)">
                        <span class="checkmark"></span>
                        X
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="XL" onchange="filterBySize('XL', this.checked)">
                        <span class="checkmark"></span>
                        XL
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="XXL" onchange="filterBySize('XXL', this.checked)">
                        <span class="checkmark"></span>
                        XXL
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
                                <input type="checkbox" name="color[]" value="#87ceeb" onchange="filterByColor('#87ceeb', this.checked)">
                                <span class="color-swatch" style="background-color: #87ceeb;"></span>
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