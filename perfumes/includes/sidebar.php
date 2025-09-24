<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Perfumes</span>
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
                        <input type="checkbox" name="category[]" value="Men's Perfumes" onchange="filterByCategory('Men's Perfumes', this.checked)">
                        <span class="checkmark"></span>
                        Men's Perfumes
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Women's Perfumes" onchange="filterByCategory('Women's Perfumes', this.checked)">
                        <span class="checkmark"></span>
                        Women's Perfumes
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Unisex Perfumes" onchange="filterByCategory('Unisex Perfumes', this.checked)">
                        <span class="checkmark"></span>
                        Unisex Perfumes
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="Luxury Perfumes" onchange="filterByCategory('Luxury Perfumes', this.checked)">
                        <span class="checkmark"></span>
                        Luxury Perfumes
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
                        <input type="checkbox" name="size[]" value="30ml" onchange="filterBySize('30ml', this.checked)">
                        <span class="checkmark"></span>
                        30ml
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="50ml" onchange="filterBySize('50ml', this.checked)">
                        <span class="checkmark"></span>
                        50ml
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="75ml" onchange="filterBySize('75ml', this.checked)">
                        <span class="checkmark"></span>
                        75ml
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="100ml" onchange="filterBySize('100ml', this.checked)">
                        <span class="checkmark"></span>
                        100ml
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="125ml" onchange="filterBySize('125ml', this.checked)">
                        <span class="checkmark"></span>
                        125ml
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="size[]" value="150ml" onchange="filterBySize('150ml', this.checked)">
                        <span class="checkmark"></span>
                        150ml
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
                                <input type="checkbox" name="color[]" value="#ffd700" onchange="filterByColor('#ffd700', this.checked)">
                                <span class="color-swatch" style="background-color: #ffd700;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#c0c0c0" onchange="filterByColor('#c0c0c0', this.checked)">
                                <span class="color-swatch" style="background-color: #c0c0c0;"></span>
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