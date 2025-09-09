<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">2500+ Beauty Products</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
    </div>
     <div class="side">
    <div class="filter-section">
        <!-- Beauty Category Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Beauty Category</h4>
            </div>
            <div class="filter-options">
                <div class="category-grid" id="beauty-category-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="makeup" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Makeup
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="skincare" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Skincare
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="hair" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Hair Care
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="bath-body" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Bath & Body
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="perfumes" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Perfumes
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="beauty_category[]" value="tools" data-filter="beauty_category">
                        <span class="checkmark"></span>
                        Beauty Tools
                    </label>
                </div>
            </div>
        </div>

        <!-- Makeup Type Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Makeup Type</h4>
            </div>
            <div class="filter-options">
                <div class="makeup-type-grid" id="makeup-type-filter">
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="face" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Face
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="eye" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Eye
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="lip" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Lip
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="makeup_type[]" value="nails" data-filter="makeup_type">
                        <span class="checkmark"></span>
                        Nails
                    </label>
                </div>
            </div>
        </div>

        <!-- Sub-Subcategory Filter -->
        <div class="filter-group" id="sub-subcategory-filter-group" style="display: none;">
            <div class="filter-header">
                <h4>Product Type</h4>
            </div>
            <div class="filter-options">
                <div class="sub-subcategory-grid" id="sub-subcategory-filter">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-group">
            <div class="filter-header">
                <h4>Color/Shade</h4>
            </div>
            <div class="filter-options">
                <div class="color-grid" id="color-filter">
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ff0000" data-filter="color">
                        <span class="color-swatch" style="background-color: #ff0000;"></span>
                        Red
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ff69b4" data-filter="color">
                        <span class="color-swatch" style="background-color: #ff69b4;"></span>
                        Pink
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#800080" data-filter="color">
                        <span class="color-swatch" style="background-color: #800080;"></span>
                        Purple
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#0000ff" data-filter="color">
                        <span class="color-swatch" style="background-color: #0000ff;"></span>
                        Blue
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#008000" data-filter="color">
                        <span class="color-swatch" style="background-color: #008000;"></span>
                        Green
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ffff00" data-filter="color">
                        <span class="color-swatch" style="background-color: #ffff00;"></span>
                        Yellow
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ffa500" data-filter="color">
                        <span class="color-swatch" style="background-color: #ffa500;"></span>
                        Orange
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#8b4513" data-filter="color">
                        <span class="color-swatch" style="background-color: #8b4513;"></span>
                        Brown
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#000000" data-filter="color">
                        <span class="color-swatch" style="background-color: #000000;"></span>
                        Black
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ffffff" data-filter="color">
                        <span class="color-swatch" style="background-color: #ffffff; border: 1px solid #ccc;"></span>
                        White
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#f5deb3" data-filter="color">
                        <span class="color-swatch" style="background-color: #f5deb3;"></span>
                        Nude
                    </label>
                    <label class="color-option">
                        <input type="checkbox" name="color[]" value="#ffd700" data-filter="color">
                        <span class="color-swatch" style="background-color: #ffd700;"></span>
                        Gold
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
                    <input type="checkbox" name="price[]" value="0-15" data-filter="price_range">
                    <span class="checkmark"></span>
                    $0 - $15
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="15-30" data-filter="price_range">
                    <span class="checkmark"></span>
                    $15 - $30
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="30-50" data-filter="price_range">
                    <span class="checkmark"></span>
                    $30 - $50
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="50-75" data-filter="price_range">
                    <span class="checkmark"></span>
                    $50 - $75
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="75+" data-filter="price_range">
                    <span class="checkmark"></span>
                    $75+
                </label>
            </div>
        </div>
    </div>

    <!-- Brand Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Brand</h4>
            </div>
            <div class="filter-options" id="brand-filter">
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="MAC" data-filter="brand">
                    <span class="checkmark"></span>
                    MAC
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="Maybelline" data-filter="brand">
                    <span class="checkmark"></span>
                    Maybelline
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="L'Oreal" data-filter="brand">
                    <span class="checkmark"></span>
                    L'Oreal
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="Revlon" data-filter="brand">
                    <span class="checkmark"></span>
                    Revlon
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="CoverGirl" data-filter="brand">
                    <span class="checkmark"></span>
                    CoverGirl
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="NYX" data-filter="brand">
                    <span class="checkmark"></span>
                    NYX
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="Urban Decay" data-filter="brand">
                    <span class="checkmark"></span>
                    Urban Decay
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="brand[]" value="Too Faced" data-filter="brand">
                    <span class="checkmark"></span>
                    Too Faced
                </label>
            </div>
        </div>
    </div>

    <!-- Skin Type Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Skin Type</h4>
            </div>
            <div class="filter-options" id="skin-type-filter">
                <label class="filter-option">
                    <input type="checkbox" name="skin_type[]" value="oily" data-filter="skin_type">
                    <span class="checkmark"></span>
                    Oily
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="skin_type[]" value="dry" data-filter="skin_type">
                    <span class="checkmark"></span>
                    Dry
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="skin_type[]" value="combination" data-filter="skin_type">
                    <span class="checkmark"></span>
                    Combination
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="skin_type[]" value="sensitive" data-filter="skin_type">
                    <span class="checkmark"></span>
                    Sensitive
                </label>
            </div>
        </div>
    </div>

    <!-- Hair Type Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Hair Type</h4>
            </div>
            <div class="filter-options" id="hair-type-filter">
                <label class="filter-option">
                    <input type="checkbox" name="hair_type[]" value="straight" data-filter="hair_type">
                    <span class="checkmark"></span>
                    Straight
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="hair_type[]" value="wavy" data-filter="hair_type">
                    <span class="checkmark"></span>
                    Wavy
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="hair_type[]" value="curly" data-filter="hair_type">
                    <span class="checkmark"></span>
                    Curly
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="hair_type[]" value="coily" data-filter="hair_type">
                    <span class="checkmark"></span>
                    Coily
                </label>
            </div>
        </div>
    </div>
    </div>
</aside>
