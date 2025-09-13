<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">12 Styles</span>
        <button id="clear-filters" class="clear-filters-btn">Clear All Filters</button>
    </div>
    <div class="side">

        
        <!-- Category Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Category</h4>
                </div>
                <div class="filter-options" id="category-filter-options">
                    <!-- Categories will be loaded dynamically -->
                    <div class="loading-categories" style="text-align: center; padding: 10px; color: #666;">
                        <div style="width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 5px;"></div>
                        Loading categories...
                    </div>
                </div>
            </div>
        </div>

        <!-- Size Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Size <span class="size-count" id="size-count"></span></h4>
                </div>
                <div class="filter-options">
                    <div class="size-grid" id="size-filter">
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="XS" data-filter="size">
                            <span class="checkmark"></span>
                            XS
                        </label>
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
                            <input type="checkbox" name="size[]" value="XL" data-filter="size">
                            <span class="checkmark"></span>
                            XL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="XXL" data-filter="size">
                            <span class="checkmark"></span>
                            XXL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="2XL" data-filter="size">
                            <span class="checkmark"></span>
                            2XL
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="size[]" value="3XL" data-filter="size">
                            <span class="checkmark"></span>
                            3XL
                        </label>
                    </div>
                    <div class="size-actions">
                        <button type="button" class="size-action-btn" onclick="selectAllSizes()">Select All</button>
                        <button type="button" class="size-action-btn" onclick="clearSizeFilters()">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Color</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#000000" data-filter="color">
                        <span class="checkmark"></span>
                        Black
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#ffffff" data-filter="color">
                        <span class="checkmark"></span>
                        White
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#0066cc" data-filter="color">
                        <span class="checkmark"></span>
                        Blue
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#812d2d" data-filter="color">
                        <span class="checkmark"></span>
                        Maroon
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#808080" data-filter="color">
                        <span class="checkmark"></span>
                        Gray
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#333333" data-filter="color">
                        <span class="checkmark"></span>
                        Dark Gray
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="#667eea" data-filter="color">
                        <span class="checkmark"></span>
                        Light Blue
                    </label>
                </div>
            </div>
        </div>

        <!-- Price Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Price</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="0-25" data-filter="price_range">
                        <span class="checkmark"></span>
                        Under $25
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="25-50" data-filter="price_range">
                        <span class="checkmark"></span>
                        $25 - $50
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="50-100" data-filter="price_range">
                        <span class="checkmark"></span>
                        $50 - $100
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="100-200" data-filter="price_range">
                        <span class="checkmark"></span>
                        $100 - $200
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="200+" data-filter="price_range">
                        <span class="checkmark"></span>
                        Over $200
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
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="nike" data-filter="brand">
                        <span class="checkmark"></span>
                        Nike
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="adidas" data-filter="brand">
                        <span class="checkmark"></span>
                        Adidas
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="puma" data-filter="brand">
                        <span class="checkmark"></span>
                        Puma
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="under-armour" data-filter="brand">
                        <span class="checkmark"></span>
                        Under Armour
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="levis" data-filter="brand">
                        <span class="checkmark"></span>
                        Levi's
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="brand[]" value="calvin-klein" data-filter="brand">
                        <span class="checkmark"></span>
                        Calvin Klein
                    </label>
                </div>
            </div>
        </div>

        <!-- Style Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Style</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="style[]" value="casual">
                        <span class="checkmark"></span>
                        Casual
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="style[]" value="formal">
                        <span class="checkmark"></span>
                        Formal
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="style[]" value="sporty">
                        <span class="checkmark"></span>
                        Sporty
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="style[]" value="streetwear">
                        <span class="checkmark"></span>
                        Streetwear
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="style[]" value="business">
                        <span class="checkmark"></span>
                        Business
                    </label>
                </div>
            </div>
        </div>

        <!-- Material Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Material</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="cotton">
                        <span class="checkmark"></span>
                        Cotton
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="polyester">
                        <span class="checkmark"></span>
                        Polyester
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="wool">
                        <span class="checkmark"></span>
                        Wool
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="linen">
                        <span class="checkmark"></span>
                        Linen
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="denim">
                        <span class="checkmark"></span>
                        Denim
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="material[]" value="silk">
                        <span class="checkmark"></span>
                        Silk
                    </label>
                </div>
            </div>
        </div>

        <!-- Fit Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Fit</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="fit[]" value="slim">
                        <span class="checkmark"></span>
                        Slim
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="fit[]" value="regular">
                        <span class="checkmark"></span>
                        Regular
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="fit[]" value="loose">
                        <span class="checkmark"></span>
                        Loose
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="fit[]" value="relaxed">
                        <span class="checkmark"></span>
                        Relaxed
                    </label>
                </div>
            </div>
        </div>

        <!-- Availability Filter -->
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Availability</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="in-stock">
                        <span class="checkmark"></span>
                        In Stock
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="on-sale">
                        <span class="checkmark"></span>
                        On Sale
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="availability[]" value="new-arrival">
                        <span class="checkmark"></span>
                        New Arrival
                    </label>
                </div>
            </div>
        </div>
    </div>
</aside> 