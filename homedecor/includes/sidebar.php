<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count">0 Styles</span>
        <button type="button" class="clear-all-filters-btn" id="clear-filters" onclick="clearAllFilters()">
            <i class="fas fa-times"></i>
            Clear All Filters
        </button>
    </div>

    <!-- Category Filter -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="filter-header">
                <h4>Categories</h4>
            </div>
            <div class="filter-options">
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Bedding" data-filter="category">
                    <span class="checkmark"></span>
                    Bedding
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="living room" data-filter="category">
                    <span class="checkmark"></span>
                    Living Room
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="Kitchen" data-filter="category">
                    <span class="checkmark"></span>
                    Kitchen
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="artwork" data-filter="category">
                    <span class="checkmark"></span>
                    Artwork
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="dinning room" data-filter="category">
                    <span class="checkmark"></span>
                    Dining Room
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="category[]" value="lightinning" data-filter="category">
                    <span class="checkmark"></span>
                    Lighting
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
                    <input type="checkbox" name="price[]" value="0-300" data-filter="price">
                    <span class="checkmark"></span>
                    $0 - $300
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="300-600" data-filter="price">
                    <span class="checkmark"></span>
                    $300 - $600
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="600-900" data-filter="price">
                    <span class="checkmark"></span>
                    $600 - $900
                </label>
                <label class="filter-option">
                    <input type="checkbox" name="price[]" value="900+" data-filter="price">
                    <span class="checkmark"></span>
                    $900+
                </label>
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
                <div class="color-grid" id="color-filter-grid">
                    <!-- Colors will be loaded dynamically from database -->
                    <div class="loading-colors" style="text-align: center; padding: 20px; color: #666;">
                        <i class="fas fa-spinner fa-spin"></i> Loading colors...
                    </div>
                </div>
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
                    <input type="radio" name="material" value="" data-filter="material" checked>
                    <span class="checkmark"></span>
                    All Materials
                </label>
                <div id="material-filter-options">
                    <!-- Materials will be loaded dynamically from database -->
                    <div class="loading-materials" style="text-align: center; padding: 20px; color: #666;">
                        <i class="fas fa-spinner fa-spin"></i> Loading materials...
                    </div>
                </div>
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
                    <input type="radio" name="availability" value="" data-filter="availability">
                    <span class="checkmark"></span>
                    All Availability
                </label>
                <label class="filter-option">
                    <input type="radio" name="availability" value="in-stock" data-filter="availability">
                    <span class="checkmark"></span>
                    In Stock
                </label>
                <label class="filter-option">
                    <input type="radio" name="availability" value="on-sale" data-filter="availability">
                    <span class="checkmark"></span>
                    On Sale
                </label>
                <label class="filter-option">
                    <input type="radio" name="availability" value="new-arrival" data-filter="availability">
                    <span class="checkmark"></span>
                    New Arrival
                </label>
            </div>
        </div>
    </div>
</aside>