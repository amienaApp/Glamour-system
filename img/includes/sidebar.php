<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count"><?php echo isset($total_styles) ? $total_styles : '0'; ?> Styles</span>
    </div>

    <div class="filter-section">
        <!-- Category Filter -->
        <div class="filter-group">
            <div class="filter-header" data-toggle="category">
                <h4>Category</h4>
                <i class="fas fa-minus toggle-icon"></i>
            </div>
            <div class="filter-options" id="category-options">
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="accessories">
                    <span class="checkmark"></span>
                    Accessories
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="beauty">
                    <span class="checkmark"></span>
                    Beauty
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="bottoms">
                    <span class="checkmark"></span>
                    Bottoms
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="dresses">
                    <span class="checkmark"></span>
                    Dresses
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="jewelry">
                    <span class="checkmark"></span>
                    Jewelry
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="lingerie">
                    <span class="checkmark"></span>
                    Lingerie
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="outerwear">
                    <span class="checkmark"></span>
                    Outerwear
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="rompers">
                    <span class="checkmark"></span>
                    Rompers/Jumpsuits
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="shoes">
                    <span class="checkmark"></span>
                    Shoes
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="swim">
                    <span class="checkmark"></span>
                    Swim
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="tops">
                    <span class="checkmark"></span>
                    Tops
                </label>
            </div>
        </div>

        <!-- Size Filter -->
        <div class="filter-group">
            <div class="filter-header" data-toggle="size">
                <h4>Size</h4>
                <i class="fas fa-minus toggle-icon"></i>
            </div>
            <div class="filter-options" id="size-options">
                <div class="size-section">
                    <h5>Apparel</h5>
                    <div class="size-grid">
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="xxs">
                            <span class="checkmark"></span>
                            XXS
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="xs">
                            <span class="checkmark"></span>
                            XS
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="s">
                            <span class="checkmark"></span>
                            S
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="m">
                            <span class="checkmark"></span>
                            M
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="l">
                            <span class="checkmark"></span>
                            L
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="xl">
                            <span class="checkmark"></span>
                            XL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="xxl">
                            <span class="checkmark"></span>
                            XXL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="1x">
                            <span class="checkmark"></span>
                            1X
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="2x">
                            <span class="checkmark"></span>
                            2X
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="3x">
                            <span class="checkmark"></span>
                            3X
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-group">
            <div class="filter-header" data-toggle="color">
                <h4>Color</h4>
                <i class="fas fa-minus toggle-icon"></i>
            </div>
            <div class="filter-options" id="color-options">
                <div class="color-grid">
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="beige">
                        <span class="color-swatch" style="background-color: #f5f5dc;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="black">
                        <span class="color-swatch" style="background-color: #000;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="blue">
                        <span class="color-swatch" style="background-color: #0066cc;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="brown">
                        <span class="color-swatch" style="background-color: #8b4513;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="gold">
                        <span class="color-swatch" style="background-color: #ffd700;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="green">
                        <span class="color-swatch" style="background-color: #228b22;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="grey">
                        <span class="color-swatch" style="background-color: #808080;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="pink">
                        <span class="color-swatch" style="background-color: #ffc0cb;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="purple">
                        <span class="color-swatch" style="background-color: #800080;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="red">
                        <span class="color-swatch" style="background-color: #ff0000;"></span>
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="white">
                        <span class="color-swatch" style="background-color: #fff; border: 1px solid #ddd;"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Price Filter -->
        <div class="filter-group">
            <div class="filter-header" data-toggle="price">
                <h4>Price</h4>
                <i class="fas fa-minus toggle-icon"></i>
            </div>
            <div class="filter-options" id="price-options">
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="0-25">
                    <span class="checkmark"></span>
                    $0 - $25
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="25-50">
                    <span class="checkmark"></span>
                    $25 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75-100">
                    <span class="checkmark"></span>
                    $75 - $100
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="100+">
                    <span class="checkmark"></span>
                    $100+
                </label>
            </div>
        </div>
    </div>

    <!-- Clear Filters Button -->
    <div class="filter-actions">
        <button class="clear-filters-btn" onclick="clearAllFilters()">Clear All Filters</button>
    </div>
</aside> 
