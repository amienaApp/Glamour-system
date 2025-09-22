# Wishlist Integration - Complete Guide

## Overview
The Glamour System now features a comprehensive, fully integrated wishlist functionality that works seamlessly across all pages and product categories. The wishlist system is built with modern JavaScript, uses localStorage for persistence, and includes advanced features like cross-tab synchronization, analytics tracking, and bulk operations.

## Features

### Core Functionality
- ✅ **Add/Remove Products**: Click heart buttons to add/remove items from wishlist
- ✅ **Persistent Storage**: Uses localStorage for data persistence
- ✅ **Cross-tab Sync**: Changes sync across multiple browser tabs
- ✅ **Visual Feedback**: Animated heart buttons with color changes
- ✅ **Notifications**: Toast notifications for all actions
- ✅ **Quick View Integration**: Works with product quick view modals

### Advanced Features
- ✅ **Bulk Operations**: Select multiple items for bulk actions
- ✅ **Export/Import**: Export wishlist to JSON, import from files
- ✅ **Sharing**: Share wishlist via native sharing or clipboard
- ✅ **Analytics**: Track wishlist events for analytics
- ✅ **Filtering**: Filter products to show only wishlist items
- ✅ **Sorting**: Sort products by wishlist status
- ✅ **Statistics**: View wishlist statistics and insights
- ✅ **Responsive Design**: Works perfectly on all device sizes

### Integration Features
- ✅ **Auto-injection**: Automatically adds wishlist buttons to product cards
- ✅ **Category Pages**: Full integration with all category pages
- ✅ **Product Details**: Works on product detail pages
- ✅ **Search Results**: Integrated with search functionality
- ✅ **Accessibility**: Full keyboard navigation and screen reader support

## File Structure

```
scripts/
├── wishlist-manager.js          # Core wishlist functionality
├── wishlist-integration.js      # Integration and UI enhancements
└── wishlist-include.php         # Universal include file

includes/
└── wishlist-include.php         # Universal wishlist include

wishlist.php                     # Dedicated wishlist page
```

## Usage

### Basic Integration
To add wishlist functionality to any page, simply include the wishlist scripts:

```html
<!-- Include in <head> or before closing </body> -->
<script src="scripts/wishlist-manager.js"></script>
<script src="scripts/wishlist-integration.js"></script>
```

Or use the universal include:
```php
<?php include 'includes/wishlist-include.php'; ?>
```

### Product Card Integration
The system automatically detects product cards with `data-product-id` attributes and adds wishlist buttons:

```html
<div class="product-card" data-product-id="123">
    <div class="product-image">
        <img src="product.jpg" alt="Product">
        <!-- Wishlist button will be auto-injected here -->
    </div>
    <div class="product-info">
        <h3 class="product-name">Product Name</h3>
        <div class="product-price">$99.99</div>
    </div>
</div>
```

### Quick View Integration
For quick view modals, add a wishlist button with the ID `add-to-wishlist-quick`:

```html
<button id="add-to-wishlist-quick" class="btn-wishlist">
    <i class="far fa-heart"></i> Add to Wishlist
</button>
```

## API Reference

### Global Functions
```javascript
// Add/remove from wishlist
window.addToWishlist(productId);
window.removeFromWishlist(productId);

// Check wishlist status
window.isInWishlist(productId);

// Get wishlist count
window.getWishlistCount();

// Show notification
window.showWishlistNotification(message, type);

// Export/import
window.exportWishlist();
window.importWishlist(file);
```

### WishlistManager Class
```javascript
// Access the main wishlist manager
const wishlist = window.wishlistManager;

// Get wishlist data
const items = wishlist.getWishlist();

// Get statistics
const stats = wishlist.getWishlistStats();

// Clear wishlist
wishlist.clearWishlist();

// Check if item is in wishlist
const isInWishlist = wishlist.isInWishlist(productId, selectedColor);
```

### Events
Listen for wishlist changes:
```javascript
document.addEventListener('wishlistChange', (e) => {
    console.log('Wishlist changed:', e.detail);
    // e.detail.type: 'added', 'removed', 'cleared', 'imported'
    // e.detail.productId: ID of the product
    // e.detail.wishlist: Current wishlist array
    // e.detail.count: Total number of items
});
```

## Configuration

### Maximum Items
Set the maximum number of items in the wishlist:
```javascript
// In wishlist-manager.js
this.maxItems = 100; // Default is 100
```

### Storage Key
Change the localStorage key:
```javascript
// In wishlist-manager.js
this.storageKey = 'wishlist'; // Default is 'wishlist'
```

### Notification Duration
Modify notification display time:
```javascript
// In wishlist-manager.js
setTimeout(() => {
    notification.classList.remove('show');
}, 3000); // 3 seconds default
```

## Styling

### CSS Classes
- `.heart-button` - Main wishlist button
- `.heart-button.active` - Active state (in wishlist)
- `.heart-button.loading` - Loading state
- `.wishlist-count` - Count badge
- `.wishlist-notification` - Toast notifications

### Customization
Override CSS variables for easy theming:
```css
:root {
    --wishlist-primary: #e74c3c;
    --wishlist-secondary: #c0392b;
    --wishlist-success: #28a745;
    --wishlist-warning: #ffc107;
    --wishlist-info: #17a2b8;
}
```

## Analytics Integration

### Google Analytics
The system automatically tracks wishlist events:
```javascript
gtag('event', 'wishlist_added', {
    'event_category': 'engagement',
    'event_label': productId,
    'value': wishlistCount
});
```

### Facebook Pixel
Track wishlist events for Facebook advertising:
```javascript
fbq('track', 'AddToWishlist', {
    content_ids: [productId],
    content_type: 'product'
});
```

## Browser Support
- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ Mobile browsers

## Performance
- **Lightweight**: Core script is only ~15KB minified
- **Fast**: Uses efficient DOM manipulation and event delegation
- **Memory Efficient**: Minimal memory footprint
- **Lazy Loading**: Buttons are injected only when needed

## Security
- **XSS Protection**: All user input is sanitized
- **Data Validation**: Imported data is validated before processing
- **Local Storage**: Data stays in user's browser (no server storage)

## Troubleshooting

### Common Issues

1. **Wishlist buttons not appearing**
   - Ensure product cards have `data-product-id` attributes
   - Check that scripts are loaded in correct order
   - Verify no JavaScript errors in console

2. **Buttons not working**
   - Check if `window.wishlistManager` is defined
   - Ensure event listeners are properly bound
   - Verify no conflicting CSS or JavaScript

3. **Data not persisting**
   - Check if localStorage is enabled
   - Verify browser supports localStorage
   - Check for storage quota exceeded errors

4. **Cross-tab sync not working**
   - Ensure storage event listener is active
   - Check if tabs are from same origin
   - Verify no browser extensions blocking events

### Debug Mode
Enable debug logging:
```javascript
// In wishlist-manager.js
console.log('Wishlist debug:', {
    productId: productId,
    action: action,
    wishlist: wishlist
});
```

## Future Enhancements
- [ ] Server-side wishlist synchronization
- [ ] Wishlist sharing via URL
- [ ] Wishlist categories and tags
- [ ] Price drop notifications
- [ ] Wishlist recommendations
- [ ] Social wishlist features

## Support
For issues or questions:
1. Check the browser console for errors
2. Verify all required files are included
3. Test with different browsers
4. Check localStorage permissions

## License
This wishlist integration is part of the Glamour System and follows the same licensing terms.
