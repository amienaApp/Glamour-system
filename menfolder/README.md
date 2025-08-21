# E-commerce Clone - Lulus Style

This is a complete e-commerce website clone that replicates the exact design and functionality of the Lulus dresses page from the provided image.

## Features

### 🎯 Exact Design Match
- **Top Navigation Bar**: Horizontal navigation with categories like "New", "Bestsellers", "Dresses", etc.
- **Sidebar Filters**: "Refine By" section with size and color filters
- **Product Grid**: Responsive product cards with hover effects
- **Promotional Elements**: "Give $20, Get $20" banner and chat button

### 🛍️ Product Cards Include
- Product images with hover zoom effect
- Heart icon for wishlist functionality
- Product names and prices
- Color swatches for available options
- Promotional banners (e.g., "In Demand!", "Best seller")
- Hover-activated "Add to Cart" and "View More" buttons

### 🎨 Interactive Elements
- **Heart Buttons**: Click to add/remove from wishlist
- **Color Selection**: Click color circles to select options
- **Filter Checkboxes**: Size and color filtering
- **Sort Options**: Dropdown for sorting products
- **View Options**: Toggle between 60/120 items per page
- **Promotional Banner**: Closeable banner
- **Chat Support**: Fixed chat button

## File Structure

```
testing/
├── index.php              # Main page that includes all components
├── includes/              # PHP component files
│   ├── header.php         # Top navigation and promotional elements
│   ├── sidebar.php        # Filter sidebar with size and color options
│   └── main-content.php   # Product grid and content area
├── styles/                # CSS stylesheet files
│   ├── header.css         # Styles for navigation and promotional elements
│   ├── sidebar.css        # Styles for filter sidebar
│   └── main.css          # Styles for main content and product cards
├── script.js             # JavaScript for all interactive functionality
└── README.md             # This file
```

## How to Use

1. **Setup**: Place all files in your web server directory
2. **Images**: Update image paths in `includes/main-content.php` to point to your actual product images
3. **Customization**: Modify colors, fonts, and styling in the `styles/` directory CSS files
4. **Functionality**: Add backend logic for cart, wishlist, and filtering in the JavaScript file

## Key Features Explained

### Product Cards
- **Hover Effects**: Cards lift up and show action buttons on hover
- **Image Zoom**: Product images scale slightly on hover
- **Color Swatches**: Circular color indicators with tooltips
- **Promotional Banners**: Overlay banners for special offers

### Sidebar Filters
- **Size Grid**: Two-column layout with scrollable size options
- **Color Grid**: Circular color swatches with checkboxes
- **Responsive**: Adapts to mobile devices

### Navigation
- **Sticky Header**: Navigation stays at top when scrolling
- **Active States**: Current page highlighted
- **Responsive**: Horizontal scroll on mobile

## Browser Compatibility
- Chrome, Firefox, Safari, Edge (modern versions)
- Mobile responsive design
- Touch-friendly interactions

## Customization

### Colors
Update the CSS variables in the stylesheets to match your brand:
```css
:root {
    --primary-color: #000;
    --secondary-color: #666;
    --accent-color: #ff4757;
    --background-color: #fff;
}
```

### Images
Replace the image paths in `includes/main-content.php`:
```php
<img src="path/to/your/product-image.jpg" alt="Product Name">
```

### Products
Add more products by duplicating the product card structure in `includes/main-content.php`.

## JavaScript Features

- Heart button toggle functionality
- Color circle selection
- Filter checkbox interactions
- Sort and view option changes
- Add to cart notifications
- Promotional banner close
- Chat button functionality
- Tooltips for color swatches

## Responsive Design

The layout adapts to different screen sizes:
- **Desktop**: Full sidebar + main content layout
- **Tablet**: Adjusted spacing and grid columns
- **Mobile**: Stacked layout with full-width elements

## Performance

- Optimized CSS with efficient selectors
- Minimal JavaScript for smooth interactions
- Responsive images with proper sizing
- Smooth animations and transitions

---

**Note**: This is a frontend clone. For a production e-commerce site, you'll need to add:
- Backend database integration
- User authentication
- Shopping cart functionality
- Payment processing
- Inventory management
- SEO optimization 