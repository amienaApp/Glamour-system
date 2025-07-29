# Glamour Shopping - Modular E-commerce System

A modern, responsive e-commerce website built with PHP, HTML, CSS, and JavaScript, featuring reusable components similar to the Lulus fashion website.

## 🚀 Features

- **Modular Architecture**: Reusable header, sidebar, and footer components
- **Responsive Design**: Mobile-first approach with modern CSS Grid and Flexbox
- **Advanced Filtering**: Category, size, color, and price filters with real-time updates
- **Product Management**: Dynamic product loading and wishlist functionality
- **Search Functionality**: Integrated search with AJAX support
- **Shopping Cart**: Add to cart with real-time count updates
- **Newsletter Integration**: Email signup with modal popup
- **Chat Widget**: Customer support chat interface
- **Pagination**: Dynamic page loading with URL state management

## 📁 Project Structure

```
Glamour-system/
├── includes/                 # Reusable PHP components
│   ├── header.php           # Main header with navigation
│   ├── sidebar.php          # Filter sidebar component
│   └── footer.php           # Footer with links and modals
├── assets/
│   ├── css/                 # Stylesheets
│   │   ├── main.css         # Global styles and layout
│   │   ├── header.css       # Header-specific styles
│   │   ├── sidebar.css      # Sidebar and filter styles
│   │   ├── sale.css         # Sale page styles
│   │   └── home.css         # Homepage styles
│   └── js/                  # JavaScript files
│       ├── main.js          # Core functionality
│       ├── filters.js       # Filter and product management
│       ├── sale.js          # Sale page functionality
│       └── home.js          # Homepage functionality
├── img/                     # Product and category images
├── index.php               # Homepage
├── sale.php                # Sale page with filters
└── README.md               # This file
```

## 🛠️ How to Use the Modular System

### 1. Including Components

To use the header, sidebar, or footer in any page:

```php
<?php
// Page configuration
$page_title = 'Your Page Title';
$show_sale_banner = true; // Optional: show sale banner
$total_styles = 1000; // For sidebar product count

// Include header
include 'includes/header.php';
?>

<!-- Your page content here -->

<?php include 'includes/footer.php'; ?>
```

### 2. Header Component

The header includes:
- Logo and navigation
- Search functionality
- User actions (wishlist, cart, account)
- Optional sale banner
- Responsive mobile menu

**Customization:**
```php
// In your page before including header.php
$page_title = 'Custom Page Title';
$show_sale_banner = false; // Hide sale banner
$additional_css = '<link rel="stylesheet" href="custom.css">';
```

### 3. Sidebar Component

The sidebar includes:
- Category filters
- Size filters
- Color filters with swatches
- Price range filters
- Clear all filters button
- Dynamic product count

**Features:**
- Collapsible filter sections
- Real-time filtering
- URL state management
- Responsive design

### 4. Footer Component

The footer includes:
- Quick links
- Customer care links
- Services information
- Newsletter signup modal
- Chat widget
- Copyright information

## 🎨 Styling System

### CSS Architecture

1. **main.css**: Global styles, layout, and product grid
2. **header.css**: Header-specific styles and navigation
3. **sidebar.css**: Sidebar filters and interactions
4. **sale.css**: Sale page specific styles
5. **home.css**: Homepage specific styles

### Key Features:
- CSS Grid for responsive layouts
- Flexbox for component alignment
- CSS Custom Properties for theming
- Mobile-first responsive design
- Smooth animations and transitions

## ⚡ JavaScript Functionality

### Core Features:

1. **Product Filters** (`filters.js`):
   - Real-time filtering
   - URL state management
   - Product count updates
   - Wishlist functionality

2. **Header Functions** (`main.js`):
   - Search functionality
   - Wishlist management
   - Cart updates
   - Newsletter modal

3. **Sale Page** (`sale.js`):
   - View toggle (grid/list)
   - Items per page
   - Pagination
   - Product interactions

### Usage Examples:

```javascript
// Add to cart
window.SalePage.addToCart(productId);

// Show notification
window.SalePage.showNotification('Product added!', 'success');

// Update filters
window.productFilters.updateFilter('category', 'dresses', true);
```

## 🔧 Customization

### Adding New Pages

1. Create a new PHP file
2. Set page configuration variables
3. Include header and footer
4. Add your content
5. Create page-specific CSS/JS if needed

Example:
```php
<?php
$page_title = 'New Page';
$additional_css = '<link rel="stylesheet" href="assets/css/newpage.css">';
$additional_js = '<script src="assets/js/newpage.js"></script>';
?>

<?php include 'includes/header.php'; ?>

<!-- Your content here -->

<?php include 'includes/footer.php'; ?>
```

### Adding New Filters

1. Add filter HTML to `includes/sidebar.php`
2. Update JavaScript in `assets/js/filters.js`
3. Add corresponding CSS styles

### Styling Customization

The system uses CSS custom properties for easy theming:

```css
:root {
    --primary-color: #007bff;
    --secondary-color: #ff6b6b;
    --text-color: #333;
    --background-color: #fff;
}
```

## 📱 Responsive Design

The system is fully responsive with breakpoints:
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: 320px - 767px

## 🚀 Getting Started

1. **Setup**: Upload files to your web server
2. **Configure**: Update database connections if needed
3. **Customize**: Modify colors, fonts, and content
4. **Test**: Test on different devices and browsers

## 🔗 Dependencies

- **Font Awesome**: Icons (CDN)
- **Google Fonts**: Typography (optional)
- **PHP**: Server-side processing
- **Modern Browser**: ES6+ JavaScript support

## 📄 License

This project is open source and available under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For questions or support, please open an issue in the repository.

---

**Built with ❤️ for modern e-commerce**
