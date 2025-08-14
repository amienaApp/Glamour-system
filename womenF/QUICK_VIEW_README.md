# Quick View Functionality

## Overview
The quick view feature allows customers to view product details without leaving the current page. It displays product information in a sliding sidebar that appears from the right side of the screen.

## Features

### ✅ **Dynamic Product Loading**
- Fetches real product data from the database via API
- Caches product data for better performance
- Handles loading states and error scenarios

### ✅ **Image Gallery**
- Main product image display
- Thumbnail navigation
- Support for multiple product images
- Color-specific image filtering

### ✅ **Color Selection**
- Visual color circles
- Real-time image switching based on color selection
- Support for color variants

### ✅ **Size Selection**
- Dynamic size options based on product data
- Sold-out size indicators
- Size availability status

### ✅ **Shopping Features**
- Add to cart functionality with localStorage
- Add to wishlist functionality
- Success/error feedback
- Cart count updates

### ✅ **User Experience**
- Smooth animations and transitions
- Loading states with spinners
- Error handling with user-friendly messages
- Responsive design for mobile devices
- Keyboard navigation (Escape to close)

## Files Structure

```
womenF/
├── get-product-data.php          # API endpoint for product data
├── script.js                     # Main JavaScript functionality
├── styles/main.css               # Quick view styling
├── test-quick-view.php           # Test page for functionality
└── QUICK_VIEW_README.md          # This documentation
```

## API Endpoint

### `get-product-data.php`
**URL:** `womenF/get-product-data.php?id={product_id}`

**Method:** GET

**Parameters:**
- `id` (required): Product ID from the database

**Response Format:**
```json
{
  "id": "product_id",
  "name": "Product Name",
  "price": "$89.99",
  "description": "Product description",
  "available": true,
  "stock": 10,
  "images": [
    {
      "src": "../uploads/products/image.jpg",
      "color": "#FF6B6B",
      "type": "front"
    }
  ],
  "colors": [
    {
      "name": "Red",
      "value": "#FF6B6B",
      "hex": "#FF6B6B"
    }
  ],
  "sizes": ["XS", "S", "M", "L", "XL"],
  "variant_sizes": {
    "#FF6B6B": ["S", "M", "L"]
  }
}
```

## Usage

### 1. **Product Cards**
Each product card should have a "Quick View" button with the product ID:
```html
<button class="quick-view" data-product-id="product_id">Quick View</button>
```

### 2. **Quick View Sidebar**
The sidebar HTML structure is already included in `main-content.php`:
```html
<div class="quick-view-sidebar" id="quick-view-sidebar">
    <!-- Quick view content -->
</div>
<div class="quick-view-overlay" id="quick-view-overlay"></div>
```

### 3. **JavaScript Integration**
The functionality is automatically initialized when the page loads. No additional setup required.

## Customization

### **Styling**
Modify the CSS in `styles/main.css`:
- `.quick-view-sidebar` - Main sidebar styling
- `.quick-view-overlay` - Background overlay
- `.quick-view-content` - Content area styling

### **Functionality**
Modify the JavaScript in `script.js`:
- `openQuickView()` - Main function for opening quick view
- `closeQuickViewSidebar()` - Function for closing quick view
- Cart/wishlist functionality can be customized

### **API Response**
Modify `get-product-data.php` to include additional product data as needed.

## Testing

### **Test Page**
Visit `womenF/test-quick-view.php` to test the API endpoint and functionality.

### **Manual Testing**
1. Go to the women's fashion page
2. Click "Quick View" on any product card
3. Verify the sidebar opens with product details
4. Test color and size selection
5. Test add to cart and wishlist functionality

## Browser Support
- Modern browsers with ES6+ support
- Mobile browsers (responsive design)
- Requires JavaScript enabled

## Performance
- Product data is cached after first load
- Images are loaded on demand
- Minimal impact on page performance

## Troubleshooting

### **Common Issues**

1. **Quick view not opening**
   - Check if product ID is correctly set in data attribute
   - Verify JavaScript console for errors
   - Ensure API endpoint is accessible

2. **Images not loading**
   - Check image paths in database
   - Verify file permissions
   - Check browser network tab

3. **API errors**
   - Check PHP error logs
   - Verify database connection
   - Test API endpoint directly

### **Debug Mode**
Enable console logging by checking the browser's developer tools for detailed error messages.

## Future Enhancements
- [ ] Product recommendations in quick view
- [ ] Social sharing functionality
- [ ] Product reviews integration
- [ ] Advanced filtering options
- [ ] Video support for products
- [ ] AR/VR product preview

