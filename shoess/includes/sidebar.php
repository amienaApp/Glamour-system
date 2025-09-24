<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Shoes</span>
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
                    <input type="checkbox" name="category[]" value="Women's Shoes" onchange="filterByCategory('Women's Shoes', this.checked)">
                    <span class="checkmark"></span>
                    Women's Shoes
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Men's Shoes" onchange="filterByCategory('Men's Shoes', this.checked)">
                    <span class="checkmark"></span>
                    Men's Shoes
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Kids' Shoes" onchange="filterByCategory('Kids' Shoes', this.checked)">
                    <span class="checkmark"></span>
                    Kids' Shoes
                </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Athletic Shoes" onchange="filterByCategory('Athletic Shoes', this.checked)">
                        <span class="checkmark"></span>
                    Athletic Shoes
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Boots" onchange="filterByCategory('Boots', this.checked)">
                        <span class="checkmark"></span>
                    Boots
                    </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Sandals" onchange="filterByCategory('Sandals', this.checked)">
                    <span class="checkmark"></span>
                    Sandals
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
                        <input type="checkbox" name="size[]" value="5" onchange="filterBySize('5', this.checked)">
                                <span class="checkmark"></span>
                        5
                            </label>
                            <label class="filter-option">
                        <input type="checkbox" name="size[]" value="6" onchange="filterBySize('6', this.checked)">
                                <span class="checkmark"></span>
                                6
                            </label>
                            <label class="filter-option">
                        <input type="checkbox" name="size[]" value="7" onchange="filterBySize('7', this.checked)">
                                <span class="checkmark"></span>
                                7
                            </label>
                            <label class="filter-option">
                        <input type="checkbox" name="size[]" value="8" onchange="filterBySize('8', this.checked)">
                                <span class="checkmark"></span>
                                8
                            </label>
                            <label class="filter-option">
                        <input type="checkbox" name="size[]" value="9" onchange="filterBySize('9', this.checked)">
                        <span class="checkmark"></span>
                        9
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="10" onchange="filterBySize('10', this.checked)">
                                <span class="checkmark"></span>
                                10
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
                                <input type="checkbox" name="color[]" value="#000000" onchange="filterByColor('#000000', this.checked)">
                                <span class="color-swatch" style="background-color: #000000;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#ffffff" onchange="filterByColor('#ffffff', this.checked)">
                                <span class="color-swatch" style="background-color: #ffffff;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#8b4513" onchange="filterByColor('#8b4513', this.checked)">
                                <span class="color-swatch" style="background-color: #8b4513;"></span>
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