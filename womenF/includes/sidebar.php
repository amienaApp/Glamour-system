<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">4146 Styles</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">
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
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Color</h4>
            </div>
            <div class="filter-options">
                <div class="color-grid" id="color-filter">
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#050505" data-filter="color">
                        <span class="color-swatch" style="background-color: #050505;"></span>
                        Black
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#0d0d0d" data-filter="color">
                        <span class="color-swatch" style="background-color: #0d0d0d;"></span>
                        Dark Gray
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#121212" data-filter="color">
                        <span class="color-swatch" style="background-color: #121212;"></span>
                        Charcoal
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#1b6435" data-filter="color">
                        <span class="color-swatch" style="background-color: #1b6435;"></span>
                        Forest Green
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#1f1e21" data-filter="color">
                        <span class="color-swatch" style="background-color: #1f1e21;"></span>
                        Dark Charcoal
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#473c3f" data-filter="color">
                        <span class="color-swatch" style="background-color: #473c3f;"></span>
                        Taupe
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#4b1924" data-filter="color">
                        <span class="color-swatch" style="background-color: #4b1924;"></span>
                        Burgundy
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#5e331c" data-filter="color">
                        <span class="color-swatch" style="background-color: #5e331c;"></span>
                        Brown
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#6d79ab" data-filter="color">
                        <span class="color-swatch" style="background-color: #6d79ab;"></span>
                        Blue
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#754640" data-filter="color">
                        <span class="color-swatch" style="background-color: #754640;"></span>
                        Rust
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#782121" data-filter="color">
                        <span class="color-swatch" style="background-color: #782121;"></span>
                        Dark Red
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#917e7e" data-filter="color">
                        <span class="color-swatch" style="background-color: #917e7e;"></span>
                        Gray
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#b4e5fe" data-filter="color">
                        <span class="color-swatch" style="background-color: #b4e5fe;"></span>
                        Light Blue
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#b9ba95" data-filter="color">
                        <span class="color-swatch" style="background-color: #b9ba95;"></span>
                        Sage
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#d1f5ef" data-filter="color">
                        <span class="color-swatch" style="background-color: #d1f5ef;"></span>
                        Mint
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#d6efff" data-filter="color">
                        <span class="color-swatch" style="background-color: #d6efff;"></span>
                        Sky Blue
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#e9e0de" data-filter="color">
                        <span class="color-swatch" style="background-color: #e9e0de;"></span>
                        Blush
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ea6695" data-filter="color">
                        <span class="color-swatch" style="background-color: #ea6695;"></span>
                        Pink
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#eed3d3" data-filter="color">
                        <span class="color-swatch" style="background-color: #eed3d3;"></span>
                        Light Pink
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#f67fbc" data-filter="color">
                        <span class="color-swatch" style="background-color: #f67fbc;"></span>
                        Hot Pink
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#faf3f8" data-filter="color">
                        <span class="color-swatch" style="background-color: #faf3f8;"></span>
                        White
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#fe6ca6" data-filter="color">
                        <span class="color-swatch" style="background-color: #fe6ca6;"></span>
                        Rose Pink
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ffccea" data-filter="color">
                        <span class="color-swatch" style="background-color: #ffccea;"></span>
                        Pale Pink
                    </label>
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

    <!-- Category Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Category</h4>
            </div>
            <div class="filter-options" id="category-filter">
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="dresses" data-filter="category">
                    <span class="checkmark"></span>
                    Dresses
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="tops" data-filter="category">
                    <span class="checkmark"></span>
                    Tops
                </label>
            </div>
        </div>
    </div>

    <!-- Dress Length Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Dress Length</h4>
            </div>
            <div class="filter-options" id="length-filter">
                <label class="filter-option">
                    <input type="checkbox" name="length[]" value="mini" data-filter="length">
                    <span class="checkmark"></span>
                    Mini
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="length[]" value="midi" data-filter="length">
                    <span class="checkmark"></span>
                    Midi
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="length[]" value="maxi" data-filter="length">
                    <span class="checkmark"></span>
                    Maxi
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="length[]" value="high-low" data-filter="length">
                    <span class="checkmark"></span>
                    High Low
                </label>
            </div>
        </div>
    </div>
    </div>
</aside> 