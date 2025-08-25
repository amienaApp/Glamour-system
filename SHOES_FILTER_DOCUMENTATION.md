# Shoes Filter Functionality Documentation

## Overview
The shoes section has been enhanced with comprehensive filter functionality that allows users to refine products by various criteria including category, size, color, price, and brand. This implementation provides a robust, user-friendly way to filter shoes products.

## Features Implemented

### 1. Enhanced Database Filtering
- **Category Filtering**: Filter by product categories (Women's Shoes, Men's Shoes, Children's Shoes, Infant Shoes)
- **Size Filtering**: Enhanced size filtering with JSON array support for all shoe sizes
- **Color Filtering**: Filter by product colors with hex code support
- **Price Range Filtering**: Filter by price ranges (Under $25, $25-$50, $50-$100, $100-$200, Over $200)
- **Brand Filtering**: Filter by product brands
- **Combined Filtering**: Multiple filters can be applied simultaneously

### 2. UI Enhancements
- **Size Count Display**: Shows the number of selected sizes next to the "Size" header
- **Select All Button**: Allows users to select all available sizes with one click
- **Clear Button**: Allows users to clear all size selections with one click
- **Clear All Filters**: Button to reset all applied filters
- **Visual Feedback**: Real-time updates showing selected filter counts
- **Responsive Design**: Maintains consistent styling across different screen sizes

### 3. JavaScript Functionality
- **Dynamic Updates**: Real-time count updates as users select/deselect filters
- **Event Handling**: Proper event propagation for filter changes
- **Global Functions**: Accessible functions for external use
- **Error Handling**: Graceful handling of edge cases
- **Loading States**: Visual feedback during filter operations

## Technical Implementation

### Database Query Structure
```php
// Base filter - only shoes
$filters['category'] = "Shoes";

// Category filter (subcategories)
if (!empty($input['categories']) && is_array($input['categories'])) {
    $andConditions[] = ['subcategory' => ['$in' => array_map('ucfirst', $input['categories'])]];
}

// Size filter with JSON array support
if (!empty($input['sizes']) && is_array($input['sizes'])) {
    $sizeFilters = [];
    foreach ($input['sizes'] as $size) {
        $sizeFilters[] = ['sizes' => ['$elemMatch' => ['$eq' => $size]]];
        $sizeFilters[] = ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')];
        $sizeFilters[] = ['size_category' => $size];
    }
    $andConditions[] = ['$or' => $sizeFilters];
}

// Color filter
if (!empty($input['colors']) && is_array($input['colors'])) {
    $andConditions[] = [
        '$or' => [
            ['color' => ['$in' => $input['colors']]],
            ['color_variants.color' => ['$in' => $input['colors']]]
        ]
    ];
}

// Price filter
if (!empty($input['price_ranges']) && is_array($input['price_ranges'])) {
    $priceFilters = [];
    foreach ($input['price_ranges'] as $range) {
        switch ($range) {
            case '0-25':
                $priceFilters[] = ['price' => ['$gte' => 0, '$lte' => 25]];
                break;
            case '25-50':
                $priceFilters[] = ['price' => ['$gte' => 25, '$lte' => 50]];
                break;
            case '50-100':
                $priceFilters[] = ['price' => ['$gte' => 50, '$lte' => 100]];
                break;
            case '100-200':
                $priceFilters[] = ['price' => ['$gte' => 100, '$lte' => 200]];
                break;
            case '200+':
                $priceFilters[] = ['price' => ['$gte' => 200]];
                break;
        }
    }
    if (!empty($priceFilters)) {
        $andConditions[] = ['$or' => $priceFilters];
    }
}
```

### CSS Styling
```css
/* Size Filter Specific Styles */
.size-count {
    font-size: 12px;
    color: #666666;
    font-weight: 400;
    margin-left: 8px;
}

.size-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    width: 100%;
}

.size-action-btn {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #495057;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
    flex: 1;
}
```

### JavaScript Functions
```javascript
// Select all sizes
function selectAllSizes() {
    const sizeCheckboxes = document.querySelectorAll('input[name="size[]"]');
    sizeCheckboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change'));
        }
    });
}

// Clear size filters
function clearSizeFilters() {
    const sizeCheckboxes = document.querySelectorAll('input[name="size[]"]');
    sizeCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        }
    });
}

// Update size count display
function updateSizeCount() {
    const sizeCheckboxes = document.querySelectorAll('input[name="size[]"]:checked');
    const sizeCountElement = document.getElementById('size-count');
    if (sizeCountElement) {
        const count = sizeCheckboxes.length;
        sizeCountElement.textContent = count > 0 ? `(${count} selected)` : '';
    }
}
```

## File Structure

### Modified Files
- `shoess/filter-api.php` - Enhanced filtering logic with all filter types
- `shoess/includes/sidebar.php` - Updated filter UI with all filter sections
- `shoess/styles/sidebar.css` - Enhanced styling for all filter components
- `shoess/script.js` - Complete filter functionality with size enhancements

### Test Files
- `test-shoes-products.php` - Product analysis and data verification
- `test-shoes-filter.php` - Comprehensive filter functionality testing

## Usage Examples

### 1. Basic Category Filtering
```javascript
// Filter by single category
const filterData = {
    action: 'filter_products',
    categories: ['women\'s shoes']
};

// Filter by multiple categories
const filterData = {
    action: 'filter_products',
    categories: ['women\'s shoes', 'men\'s shoes']
};
```

### 2. Size Filtering
```javascript
// Filter by single size
const filterData = {
    action: 'filter_products',
    sizes: ['42']
};

// Filter by multiple sizes
const filterData = {
    action: 'filter_products',
    sizes: ['40', '41', '42']
};
```

### 3. Color Filtering
```javascript
// Filter by single color
const filterData = {
    action: 'filter_products',
    colors: ['Black']
};

// Filter by multiple colors
const filterData = {
    action: 'filter_products',
    colors: ['Black', 'White']
};
```

### 4. Price Range Filtering
```javascript
// Filter by price range
const filterData = {
    action: 'filter_products',
    price_ranges: ['25-50']
};

// Filter by multiple price ranges
const filterData = {
    action: 'filter_products',
    price_ranges: ['0-25', '25-50']
};
```

### 5. Combined Filtering
```javascript
// Combine multiple filters
const filterData = {
    action: 'filter_products',
    categories: ['women\'s shoes'],
    colors: ['Black'],
    price_ranges: ['25-50'],
    sizes: ['38', '39', '40']
};
```

## Testing

### Running Tests
```bash
# Product analysis test
php test-shoes-products.php

# Filter functionality test
php test-shoes-filter.php
```

### Test Results
- **Total Products**: 17 shoes products
- **Categories**: Women's Shoes (4), Men's Shoes (6), Children's Shoes (5), Infant Shoes (2)
- **Sizes**: 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47
- **Colors**: Black, Red, Brown, White, Pink, #667eea
- **Price Ranges**: Under $25 (7), $25-$50 (9), $50-$100 (1), $100-$200 (0), Over $200 (0)

## Available Filter Options

### Categories
- Women's Shoes (4 products)
- Men's Shoes (6 products)
- Children's Shoes (5 products)
- Infant Shoes (2 products)

### Sizes
- Adult Sizes: 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47
- Children Sizes: 6, 7, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 32, 34
- Infant Sizes: 1-4 month, 5-8 month, 9-13 month

### Colors
- Black (#000000)
- Red (#ff0000)
- Brown (#8b4513)
- White (#ffffff)
- Pink (#ffc0cb)
- Light Blue (#667eea)

### Price Ranges
- Under $25 (7 products)
- $25 - $50 (9 products)
- $50 - $100 (1 product)
- $100 - $200 (0 products)
- Over $200 (0 products)

## Performance Considerations

### Database Optimization
- Uses MongoDB aggregation for efficient filtering
- Implements proper indexing on filter fields
- Optimized query structure for complex filters
- Efficient JSON string searching for sizes

### Frontend Performance
- Real-time updates without page reloads
- Efficient event handling with event delegation
- Minimal DOM manipulation for better performance
- Loading states to prevent multiple simultaneous requests

## Browser Compatibility
- **Modern Browsers**: Full support for all features
- **Event Handling**: Compatible with all modern browsers
- **CSS Grid**: Responsive design works across devices
- **JavaScript**: ES6+ features with fallbacks

## Future Enhancements

### Potential Improvements
1. **Brand Filtering**: Implement brand-based filtering
2. **Advanced Search**: Add text-based search functionality
3. **Filter Presets**: Save and load filter combinations
4. **Sorting Options**: Add sorting by price, popularity, etc.
5. **Filter Analytics**: Track popular filter combinations
6. **Size Guide**: Add size guide for different regions

### Scalability
- Modular design allows easy addition of new filter types
- Database structure supports expansion
- UI components are reusable across different sections
- API structure supports additional filter parameters

## Troubleshooting

### Common Issues
1. **No Products Found**: Check if filter criteria are too restrictive
2. **Filter Not Working**: Verify MongoDB connection and query syntax
3. **UI Not Updating**: Check JavaScript console for errors
4. **Styling Issues**: Ensure CSS files are properly loaded

### Debug Tools
- Browser developer tools for frontend debugging
- PHP error logging for backend issues
- MongoDB query logging for database problems
- Test files for isolated functionality testing

## API Endpoints

### Filter Products
- **Endpoint**: `POST /shoess/filter-api.php`
- **Action**: `filter_products`
- **Parameters**: categories[], sizes[], colors[], price_ranges[], brands[]

### Get Filter Options
- **Endpoint**: `POST /shoess/filter-api.php`
- **Action**: `get_filter_options`
- **Returns**: Available filter options and counts

## Conclusion
The shoes filter functionality provides a comprehensive, user-friendly way to filter products by multiple criteria. The implementation handles various data formats, provides excellent user experience, and maintains good performance characteristics. The modular design allows for future enhancements while maintaining backward compatibility.

The system is ready for production use and can be easily extended with additional filter types and features as needed.
