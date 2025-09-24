<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Filters</h3>
        <span class="style-count" id="style-count"><?php echo $filterData['productCount']; ?> Women's Products</span>
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
                    <input type="checkbox" name="category[]" value="Dresses" onchange="filterByCategory('Dresses', this.checked)">
                    <span class="checkmark"></span>
                    Dresses
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Tops" onchange="filterByCategory('Tops', this.checked)">
                    <span class="checkmark"></span>
                    Tops
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Bottoms" onchange="filterByCategory('Bottoms', this.checked)">
                    <span class="checkmark"></span>
                    Bottoms
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Outerwear" onchange="filterByCategory('Outerwear', this.checked)">
                    <span class="checkmark"></span>
                    Outerwear
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Activewear" onchange="filterByCategory('Activewear', this.checked)">
                    <span class="checkmark"></span>
                    Activewear
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Wedding Dress" onchange="filterByCategory('Wedding Dress', this.checked)">
                    <span class="checkmark"></span>
                    Wedding Dress
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Bridesmaid Wear" onchange="filterByCategory('Bridesmaid Wear', this.checked)">
                    <span class="checkmark"></span>
                    Bridesmaid Wear
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
                                <input type="checkbox" name="color[]" value="#050505" onchange="filterByColor('#050505', this.checked)">
                                <span class="color-swatch" style="background-color: #050505;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#1b6435" onchange="filterByColor('#1b6435', this.checked)">
                                <span class="color-swatch" style="background-color: #1b6435;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#dc3545" onchange="filterByColor('#dc3545', this.checked)">
                                <span class="color-swatch" style="background-color: #dc3545;"></span>
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
