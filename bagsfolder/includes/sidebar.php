<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">Bags</span>
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
                    <input type="checkbox" name="category[]" value="Shoulder Bags" onchange="filterByCategory('Shoulder Bags', this.checked)">
                        <span class="checkmark"></span>
                        Shoulder Bags
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Clutches" onchange="filterByCategory('Clutches', this.checked)">
                        <span class="checkmark"></span>
                    Clutches
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Tote Bags" onchange="filterByCategory('Tote Bags', this.checked)">
                        <span class="checkmark"></span>
                        Tote Bags
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Crossbody Bags" onchange="filterByCategory('Crossbody Bags', this.checked)">
                        <span class="checkmark"></span>
                        Crossbody Bags
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Backpacks" onchange="filterByCategory('Backpacks', this.checked)">
                        <span class="checkmark"></span>
                        Backpacks
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Briefcases" onchange="filterByCategory('Briefcases', this.checked)">
                        <span class="checkmark"></span>
                        Briefcases
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Laptop Bags" onchange="filterByCategory('Laptop Bags', this.checked)">
                        <span class="checkmark"></span>
                        Laptop Bags
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Waist Bags" onchange="filterByCategory('Waist Bags', this.checked)">
                        <span class="checkmark"></span>
                        Waist Bags
                    </label>
                    <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Wallets" onchange="filterByCategory('Wallets', this.checked)">
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
                                <input type="checkbox" name="color[]" value="#f5f5dc" onchange="filterByColor('#f5f5dc', this.checked)">
                                <span class="color-swatch" style="background-color: #f5f5dc;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#000000" onchange="filterByColor('#000000', this.checked)">
                                <span class="color-swatch" style="background-color: #000000;"></span>
                            </label>
                            <label class="color-option">
                                <input type="checkbox" name="color[]" value="#0066cc" onchange="filterByColor('#0066cc', this.checked)">
                                <span class="color-swatch" style="background-color: #0066cc;"></span>
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