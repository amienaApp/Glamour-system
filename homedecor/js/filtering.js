/**
 * Home Decor Filtering System
 * Handles all sidebar filtering functionality
 */

class HomeDecorFilter {
    constructor() {
        this.filters = {
            category: [],
            price: [],
            color: [],
            material: [],
            availability: []
        };
        
        this.allProducts = [];
        this.filteredProducts = [];
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadAllProducts();
        this.loadColorsFromDatabase();
        this.loadMaterialsFromDatabase();
        this.updateStyleCount();
        this.initializeDefaultFilters();
    }
    
    setupEventListeners() {
        console.log('HomeDecorFilter: Setting up event listeners...');
        
        // Use a single event listener to avoid conflicts with script.js
        document.addEventListener('change', (e) => {
            console.log('HomeDecorFilter: Change event detected', e.target);
            
            // Check if this is a filter input and is within the sidebar
            if (e.target.hasAttribute('data-filter')) {
                const sidebar = e.target.closest('.sidebar');
                if (sidebar) {
                    const filterType = e.target.getAttribute('data-filter');
                    console.log('HomeDecorFilter: Processing filter change', filterType, e.target.value, 'checked:', e.target.checked);
                    this.handleFilterChange(filterType, e.target);
                } else {
                    console.log('HomeDecorFilter: Filter input not in sidebar, ignoring');
                }
            } else {
                console.log('HomeDecorFilter: Not a filter input, ignoring');
            }
        });
        
        // Clear all filters button
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            console.log('HomeDecorFilter: Clear filters button found');
            clearFiltersBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.clearAllFilters();
            });
        } else {
            console.log('HomeDecorFilter: Clear filters button not found');
        }
        
        console.log('HomeDecorFilter: Event listeners set up complete');
    }
    
    loadColorsFromDatabase() {
        console.log('HomeDecorFilter: Loading colors from database...');
        
        fetch('get-colors-api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('HomeDecorFilter: Colors loaded successfully:', data.data.colors);
                    this.populateColorFilter(data.data.colors);
                } else {
                    console.error('HomeDecorFilter: Error loading colors:', data.message);
                    this.showColorLoadError();
                }
            })
            .catch(error => {
                console.error('HomeDecorFilter: Error fetching colors:', error);
                this.showColorLoadError();
            });
    }
    
    populateColorFilter(colors) {
        const colorGrid = document.getElementById('color-filter-grid');
        if (!colorGrid) {
            console.error('HomeDecorFilter: Color grid not found');
            return;
        }
        
        // Clear loading message
        colorGrid.innerHTML = '';
        
        // Color mapping for better display
        const colorMap = {
            'beige': '#f5f5dc',
            'black': '#000000',
            'blue': '#0066cc',
            'brown': '#8b4513',
            'gold': '#ffd700',
            'green': '#228b22',
            'grey': '#808080',
            'gray': '#808080',
            'orange': '#ffa500',
            'pink': '#ffc0cb',
            'purple': '#800080',
            'red': '#ff0000',
            'silver': '#c0c0c0',
            'taupe': '#483c32',
            'white': '#ffffff',
            'yellow': '#ffff00',
            'navy': '#000080',
            'maroon': '#800000',
            'teal': '#008080',
            'olive': '#808000',
            'lime': '#00ff00',
            'aqua': '#00ffff',
            'fuchsia': '#ff00ff',
            'coral': '#ff7f50',
            'salmon': '#fa8072',
            'tan': '#d2b48c',
            'ivory': '#fffff0',
            'cream': '#fff8dc',
            'mint': '#f5fffa',
            'lavender': '#e6e6fa',
            'peach': '#ffdab9',
            'rose': '#ff69b4',
            'turquoise': '#40e0d0',
            'indigo': '#4b0082',
            'violet': '#8a2be2',
            'magenta': '#ff00ff',
            'cyan': '#00ffff',
            'lime': '#00ff00',
            'amber': '#ffbf00',
            'bronze': '#cd7f32',
            'copper': '#b87333',
            'charcoal': '#36454f',
            'slate': '#708090',
            'steel': '#4682b4',
            'denim': '#1560bd',
            'khaki': '#f0e68c',
            'burgundy': '#800020',
            'wine': '#722f37',
            'plum': '#8e4585',
            'mauve': '#e0b0ff',
            'sage': '#9caf88',
            'forest': '#228b22',
            'emerald': '#50c878',
            'jade': '#00a86b',
            'mint': '#3eb489',
            'seafoam': '#9fe2bf',
            'aqua': '#00ffff',
            'sky': '#87ceeb',
            'royal': '#4169e1',
            'midnight': '#191970',
            'navy': '#000080',
            'cobalt': '#0047ab',
            'sapphire': '#0f52ba',
            'azure': '#007fff',
            'cerulean': '#007ba7',
            'teal': '#008080',
            'turquoise': '#40e0d0',
            'aquamarine': '#7fffd4',
            'mint': '#f5fffa',
            'lime': '#00ff00',
            'olive': '#808000',
            'yellow': '#ffff00',
            'gold': '#ffd700',
            'amber': '#ffbf00',
            'orange': '#ffa500',
            'peach': '#ffdab9',
            'coral': '#ff7f50',
            'salmon': '#fa8072',
            'pink': '#ffc0cb',
            'rose': '#ff69b4',
            'magenta': '#ff00ff',
            'fuchsia': '#ff00ff',
            'purple': '#800080',
            'violet': '#8a2be2',
            'lavender': '#e6e6fa',
            'plum': '#8e4585',
            'mauve': '#e0b0ff',
            'indigo': '#4b0082',
            'red': '#ff0000',
            'crimson': '#dc143c',
            'maroon': '#800000',
            'burgundy': '#800020',
            'wine': '#722f37',
            'brown': '#8b4513',
            'tan': '#d2b48c',
            'beige': '#f5f5dc',
            'cream': '#fff8dc',
            'ivory': '#fffff0',
            'white': '#ffffff',
            'gray': '#808080',
            'grey': '#808080',
            'silver': '#c0c0c0',
            'charcoal': '#36454f',
            'slate': '#708090',
            'steel': '#4682b4',
            'black': '#000000'
        };
        
        colors.forEach(colorData => {
            const colorName = colorData.name;
            const colorCount = colorData.count;
            const displayName = colorData.display_name;
            const allColors = colorData.all_colors || [colorName]; // All colors in this group
            
            // Check if it's already a hex color
            let colorHex = colorName;
            if (!colorName.startsWith('#')) {
                // Get color hex value from mapping or generate a default
                colorHex = colorMap[colorName.toLowerCase()];
                if (!colorHex) {
                    // Generate a simple color based on the name
                    colorHex = this.generateColorFromName(colorName);
                }
            }
            
            // Create a more readable display name for hex colors
            let readableName = displayName;
            if (colorName.startsWith('#')) {
                // Convert hex to a more readable name
                readableName = this.hexToReadableName(colorName);
            }
            
            const colorOption = document.createElement('label');
            colorOption.className = 'color-option';
            colorOption.innerHTML = `
                <input type="checkbox" name="color[]" value="${colorName}" data-filter="color" data-all-colors="${allColors.join(',')}">
                <span class="color-swatch" style="background-color: ${colorHex};" title="${readableName} (${colorCount} items)"></span>
                ${readableName}
            `;
            
            colorGrid.appendChild(colorOption);
        });
        
        console.log(`HomeDecorFilter: Populated ${colors.length} colors in filter`);
    }
    
    generateColorFromName(name) {
        // Simple hash function to generate consistent colors
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        
        // Convert to hex color
        const hue = Math.abs(hash) % 360;
        return `hsl(${hue}, 70%, 50%)`;
    }
    
    hexToReadableName(hex) {
        // Convert hex to RGB
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        
        // Convert RGB to HSL
        const hsl = this.rgbToHsl(r, g, b);
        const hue = hsl[0];
        const saturation = hsl[1];
        const lightness = hsl[2];
        
        // Determine color name based on hue and saturation
        if (saturation < 0.1) {
            if (lightness > 0.9) return 'White';
            if (lightness < 0.1) return 'Black';
            return 'Gray';
        }
        
        if (hue < 15) return 'Red';
        if (hue < 45) return 'Orange';
        if (hue < 75) return 'Yellow';
        if (hue < 150) return 'Green';
        if (hue < 195) return 'Cyan';
        if (hue < 255) return 'Blue';
        if (hue < 285) return 'Purple';
        if (hue < 315) return 'Magenta';
        return 'Red';
    }
    
    rgbToHsl(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;
        
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        
        if (max === min) {
            h = s = 0; // achromatic
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        
        return [h * 360, s, l];
    }
    
    showColorLoadError() {
        const colorGrid = document.getElementById('color-filter-grid');
        if (colorGrid) {
            colorGrid.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #e53e3e;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <br>Error loading colors
                    <br><small>Please refresh the page</small>
                </div>
            `;
        }
    }
    
    loadMaterialsFromDatabase() {
        console.log('HomeDecorFilter: Loading materials from database...');
        
        fetch('get-materials-api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('HomeDecorFilter: Materials loaded successfully:', data.materials);
                    this.populateMaterialFilter(data.materials);
                } else {
                    console.error('HomeDecorFilter: Error loading materials:', data.error);
                    this.showMaterialLoadError();
                }
            })
            .catch(error => {
                console.error('HomeDecorFilter: Error fetching materials:', error);
                this.showMaterialLoadError();
            });
    }
    
    populateMaterialFilter(materials) {
        const materialContainer = document.getElementById('material-filter-options');
        if (!materialContainer) {
            console.error('HomeDecorFilter: Material container not found');
            return;
        }
        
        // Clear loading message
        materialContainer.innerHTML = '';
        
        // Add each material as a radio button option (all active)
        materials.forEach(material => {
            const label = document.createElement('label');
            label.className = 'filter-option';
            
            label.innerHTML = `
                <input type="radio" name="material" value="${material.name}" data-filter="material">
                <span class="checkmark"></span>
                ${material.name} (${material.count})
            `;
            
            materialContainer.appendChild(label);
        });
        
        console.log('HomeDecorFilter: Material filter populated with', materials.length, 'materials');
    }
    
    showMaterialLoadError() {
        const materialContainer = document.getElementById('material-filter-options');
        if (materialContainer) {
            materialContainer.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #e53e3e;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <br>Error loading materials
                    <br><small>Please refresh the page</small>
                </div>
            `;
        }
    }
    
    loadAllProducts() {
        console.log('HomeDecorFilter: Loading all products...');
        
        // Get all product cards from both grids
        const filteredGrid = document.getElementById('filtered-products-grid');
        const allProductsGrid = document.getElementById('all-home-decor-grid');
        
        console.log('HomeDecorFilter: Looking for product grids...');
        console.log('HomeDecorFilter: filteredGrid found:', !!filteredGrid);
        console.log('HomeDecorFilter: allProductsGrid found:', !!allProductsGrid);
        
        if (filteredGrid) {
            this.allProducts = Array.from(filteredGrid.querySelectorAll('.product-card'));
            console.log('HomeDecorFilter: Found products in filtered grid:', this.allProducts.length);
        } else if (allProductsGrid) {
            this.allProducts = Array.from(allProductsGrid.querySelectorAll('.product-card'));
            console.log('HomeDecorFilter: Found products in all products grid:', this.allProducts.length);
        } else {
            console.log('HomeDecorFilter: No product grids found, trying to find any product grid...');
            // Try to find any product grid as fallback
            const anyGrid = document.querySelector('.product-grid');
            if (anyGrid) {
                this.allProducts = Array.from(anyGrid.querySelectorAll('.product-card'));
                console.log('HomeDecorFilter: Found products in fallback grid:', this.allProducts.length);
            } else {
                console.log('HomeDecorFilter: No product grids found at all');
            }
        }
        
        this.filteredProducts = [...this.allProducts];
        
        // Debug: Log product data
        if (this.allProducts.length > 0) {
            // console.log('Sample product data:', {
            //     id: this.allProducts[0].dataset.productId,
            //     category: this.allProducts[0].dataset.category,
            //     price: this.allProducts[0].dataset.price,
            //     color: this.allProducts[0].dataset.color,
            //     material: this.allProducts[0].dataset.material,
            //     availability: this.allProducts[0].dataset.availability
            // });
        }
    }
    
    handleFilterChange(filterType, checkbox) {
        const value = checkbox.value;
        const isChecked = checkbox.checked;
        
        console.log(`HomeDecorFilter: ${filterType} = ${value}, checked: ${isChecked}`);
        
        // Handle different filter types
        if (filterType === 'material' || filterType === 'availability') {
            // Radio buttons - single selection
            if (isChecked) {
                if (value === '') {
                    // If "All" option is selected, clear this filter
                    this.filters[filterType] = [];
                } else {
                    this.filters[filterType] = [value];
                }
            }
        } else {
            // Checkboxes - multiple selection
            if (isChecked) {
                if (value === '') {
                    // If "All" option is selected, clear this filter
                    this.filters[filterType] = [];
                } else {
                    // Add to the array if not already present
                    if (!this.filters[filterType].includes(value)) {
                        this.filters[filterType].push(value);
                    }
                }
            } else {
                // Remove from the array
                const index = this.filters[filterType].indexOf(value);
                if (index > -1) {
                    this.filters[filterType].splice(index, 1);
                }
            }
        }
        
        console.log(`HomeDecorFilter: Current filters:`, this.filters);
        this.applyFilters();
    }
    
    applyFilters() {
        console.log(`HomeDecorFilter: Applying filters to ${this.allProducts.length} products`);
        console.log(`HomeDecorFilter: Current filter state:`, this.filters);
        
        this.filteredProducts = this.allProducts.filter(product => {
            const matches = this.productMatchesFilters(product);
            if (!matches) {
                console.log(`HomeDecorFilter: Product ${product.dataset.productId} (${product.dataset.category}) filtered out`);
            } else {
                console.log(`HomeDecorFilter: Product ${product.dataset.productId} (${product.dataset.category}) matches filters`);
            }
            return matches;
        });
        
        console.log(`HomeDecorFilter: ${this.filteredProducts.length} products match filters`);
        
        this.updateProductDisplay();
        this.updateStyleCount();
    }
    
    productMatchesFilters(product) {
        // Category filter
        if (this.filters.category.length > 0) {
            const productCategory = product.dataset.category;
            console.log(`HomeDecorFilter: Checking category - Product: "${productCategory}", Filter: [${this.filters.category.join(', ')}]`);
            if (!this.filters.category.includes(productCategory)) {
                return false;
            }
        }
        
        // Price filter
        if (this.filters.price.length > 0) {
            const productPrice = parseFloat(product.dataset.price) || 0;
            let priceMatch = false;
            
            console.log(`HomeDecorFilter: Checking price - Product: $${productPrice}, Filter: [${this.filters.price.join(', ')}]`);
            
            for (const priceRange of this.filters.price) {
                if (this.priceInRange(productPrice, priceRange)) {
                    priceMatch = true;
                    console.log(`HomeDecorFilter: Price match found for range: ${priceRange}`);
                    break;
                }
            }
            
            if (!priceMatch) {
                console.log(`HomeDecorFilter: No price match found for $${productPrice}`);
                return false;
            }
        }
        
        // Color filter
        if (this.filters.color.length > 0) {
            const productColor = product.dataset.color;
            console.log(`HomeDecorFilter: Checking color - Product: "${productColor}", Filter: [${this.filters.color.join(', ')}]`);
            
            // Check main color against all selected color groups
            let colorMatch = this.filters.color.some(filterColor => {
                // Get the checkbox element to check for grouped colors
                const checkbox = document.querySelector(`input[value="${filterColor}"][data-filter="color"]`);
                if (checkbox && checkbox.dataset.allColors) {
                    // This is a grouped color, check against all colors in the group
                    const allColors = checkbox.dataset.allColors.split(',');
                    return allColors.some(groupColor => 
                        groupColor.toLowerCase() === productColor.toLowerCase()
                    );
                } else {
                    // Direct color match
                    return filterColor.toLowerCase() === productColor.toLowerCase();
                }
            });
            
            // If no match on main color, check color variants
            if (!colorMatch) {
                try {
                    const colorVariants = JSON.parse(product.dataset.productVariants || '[]');
                    for (const variant of colorVariants) {
                        if (variant.color && this.filters.color.some(filterColor => {
                            const checkbox = document.querySelector(`input[value="${filterColor}"][data-filter="color"]`);
                            if (checkbox && checkbox.dataset.allColors) {
                                // This is a grouped color, check against all colors in the group
                                const allColors = checkbox.dataset.allColors.split(',');
                                return allColors.some(groupColor => 
                                    groupColor.toLowerCase() === variant.color.toLowerCase()
                                );
                            } else {
                                // Direct color match
                                return filterColor.toLowerCase() === variant.color.toLowerCase();
                            }
                        })) {
                            colorMatch = true;
                            break;
                        }
                    }
                } catch (e) {
                    console.log('HomeDecorFilter: Error parsing color variants:', e);
                }
            }
            
            if (!colorMatch) {
                console.log(`HomeDecorFilter: No color match found for "${productColor}"`);
                return false;
            }
        }
        
        // Material filter
        if (this.filters.material.length > 0) {
            const productMaterial = product.dataset.material;
            console.log(`HomeDecorFilter: Checking material - Product: "${productMaterial}", Filter: [${this.filters.material.join(', ')}]`);
            
            // Case-insensitive material matching
            const productMaterialLower = productMaterial.toLowerCase();
            const materialMatch = this.filters.material.some(filterMaterial => 
                filterMaterial.toLowerCase() === productMaterialLower
            );
            
            if (!materialMatch) {
                return false;
            }
        }
        
        // Availability filter
        if (this.filters.availability.length > 0) {
            const productAvailability = product.dataset.availability;
            const productSale = product.dataset.sale;
            const productNew = product.dataset.new;
            
            let availabilityMatch = false;
            
            for (const availability of this.filters.availability) {
                if (availability === 'in-stock' && productAvailability === 'in-stock') {
                    availabilityMatch = true;
                    break;
                } else if (availability === 'on-sale' && productSale === 'on-sale') {
                    availabilityMatch = true;
                    break;
                } else if (availability === 'new-arrival' && productNew === 'new-arrival') {
                    availabilityMatch = true;
                    break;
                }
            }
            
            if (!availabilityMatch) {
                return false;
            }
        }
        
        return true;
    }
    
    priceInRange(price, range) {
        console.log(`HomeDecorFilter: Checking if $${price} is in range: ${range}`);
        
        if (range === '900+') {
            return price >= 900;
        }
        
        const [min, max] = range.split('-').map(Number);
        const result = price >= min && price <= max;
        console.log(`HomeDecorFilter: Price $${price} in range ${range}: ${result} (min: ${min}, max: ${max})`);
        return result;
    }
    
    updateProductDisplay() {
        console.log(`HomeDecorFilter: Updating product display - hiding ${this.allProducts.length} products, showing ${this.filteredProducts.length} products`);
        
        // Hide all products first
        this.allProducts.forEach(product => {
            product.style.display = 'none';
        });
        
        // Show filtered products
        this.filteredProducts.forEach(product => {
            product.style.display = 'block';
        });
        
        // Show "no products" message if no results
        this.showNoProductsMessage();
        
        console.log('HomeDecorFilter: Product display updated');
    }
    
    showNoProductsMessage() {
        // Remove existing no products message
        const existingMessage = document.querySelector('.no-filtered-products');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        if (this.filteredProducts.length === 0) {
            const message = document.createElement('div');
            message.className = 'no-filtered-products';
            message.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3>No products match your filters</h3>
                    <p>Try adjusting your filter criteria or <button onclick="homeDecorFilter.clearAllFilters()" style="background: none; border: none; color: #007bff; text-decoration: underline; cursor: pointer;">clear all filters</button></p>
                </div>
            `;
            
            // Insert message into the appropriate grid
            const filteredGrid = document.getElementById('filtered-products-grid');
            const allProductsGrid = document.getElementById('all-home-decor-grid');
            
            if (filteredGrid) {
                filteredGrid.appendChild(message);
            } else if (allProductsGrid) {
                allProductsGrid.appendChild(message);
            }
        }
    }
    
    updateStyleCount() {
        const styleCountElement = document.getElementById('style-count');
        if (styleCountElement) {
            const count = this.filteredProducts.length;
            styleCountElement.textContent = `${count} Style${count !== 1 ? 's' : ''}`;
        }
    }
    
    clearAllFilters() {
        console.log('HomeDecorFilter: Clearing all filters...');
        
        // Uncheck all filter radio buttons and checkboxes
        document.querySelectorAll('input[data-filter]').forEach(input => {
            input.checked = false;
        });
        
        // Check the "All" options (empty values) by default for radio buttons
        document.querySelectorAll('input[data-filter][value=""][type="radio"]').forEach(input => {
            input.checked = true;
        });
        
        // Reset filters object
        this.filters = {
            category: [],
            price: [],
            color: [],
            material: [],
            availability: []
        };
        
        // Reload all products from DOM to ensure we have the latest data
        this.loadAllProducts();
        
        // Show all products
        this.filteredProducts = [...this.allProducts];
        this.updateProductDisplay();
        this.updateStyleCount();
        
        // Remove no products message
        const existingMessage = document.querySelector('.no-filtered-products');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        console.log('HomeDecorFilter: All filters cleared');
    }
    
    // Public method to get current filters
    getCurrentFilters() {
        return this.filters;
    }
    
    // Public method to get filtered products count
    getFilteredCount() {
        return this.filteredProducts.length;
    }
    
    initializeDefaultFilters() {
        // Check the "All" options (empty values) by default for radio buttons only
        document.querySelectorAll('input[data-filter][value=""][type="radio"]').forEach(input => {
            input.checked = true;
        });
        
        console.log('HomeDecorFilter: Default filters initialized');
    }
}

// Initialize the filter system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('HomeDecorFilter: Initializing filter system...');
    window.homeDecorFilter = new HomeDecorFilter();
    console.log('HomeDecorFilter: Filter system initialized successfully');
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomeDecorFilter;
}
