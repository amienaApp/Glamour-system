# 🏠 Home Decor System

This directory contains the home decor page for the Glamour Palace e-commerce system.

## 🚀 Setup Instructions

### 1. Run the Category Setup Script
First, run the setup script to create the necessary categories in the database:

```bash
php setup-categories.php
```

This will create:
- **Home and Living** (main category)
- **Home Decor** (alternative category)
- **Home & Living** (alternative category)
- **Home** (alternative category)

With subcategories:
- Living Room
- Kitchen
- Bedroom
- Dining
- Lighting
- Artwork

### 2. Add Products from Admin Panel
1. Go to `admin/add-product.php`
2. Select category: **"Home and Living"**
3. Choose appropriate subcategory (e.g., "Living Room", "Kitchen", etc.)
4. Fill in product details
5. Upload product images
6. Save the product

### 3. View Products on Home Decor Page
Products added with category "Home and Living" will automatically appear on:
- `homedecor/homedecor.php` (main page)
- Filtered by subcategory when using navigation links

## 📁 File Structure

```
homedecor/
├── homedecor.php              # Main home decor page
├── setup-categories.php        # Database setup script
├── includes/
│   ├── main-content.php       # Product display logic
│   └── sidebar.php            # Filter sidebar
├── styles/
│   ├── main.css               # Main page styles
│   └── sidebar.css            # Sidebar styles
└── script.js                  # JavaScript functionality
```

## 🎯 Features

### Product Display
- **Dynamic Filtering**: Products automatically filtered by category
- **Subcategory Navigation**: Easy navigation between home decor categories
- **Image Gallery**: Support for both images and videos
- **Color Variants**: Display multiple color options
- **Quick View**: Sidebar product preview

### Filtering System
- **Category Filters**: Filter by room type (Living Room, Kitchen, etc.)
- **Size Filters**: Available sizes for applicable products
- **Price Sorting**: Multiple sorting options
- **View Options**: Grid layout options

### Responsive Design
- **Mobile Friendly**: Works on all device sizes
- **Modern UI**: Professional styling and animations
- **Accessibility**: Proper ARIA labels and semantic HTML

## 🔧 How It Works

### 1. Product Retrieval
The system automatically fetches products with category "Home and Living" from the database.

### 2. Category Fallback
If "Home and Living" is not found, it tries alternative categories:
- Home Decor
- Home & Living
- Home

### 3. Subcategory Filtering
Products can be filtered by subcategory using URL parameters:
- `homedecor.php?subcategory=living-room`
- `homedecor.php?subcategory=kitchen`
- `homedecor.php?subcategory=bedroom`

### 4. Dynamic Content
- Product grids update automatically
- Filter options adapt to available products
- Navigation reflects current category selection

## 🎨 Customization

### Adding New Categories
1. Edit `setup-categories.php`
2. Add new category names to the arrays
3. Run the setup script again

### Styling Changes
- Modify `styles/main.css` for main page styles
- Modify `styles/sidebar.css` for sidebar styles
- Update color schemes and layouts as needed

### JavaScript Functionality
- Edit `script.js` for interactive features
- Add new filter options
- Enhance product display functionality

## 🐛 Troubleshooting

### Products Not Showing
1. Check if category "Home and Living" exists in database
2. Verify products have correct category assignment
3. Check database connection and permissions

### Images Not Loading
1. Verify image paths are correct
2. Check file permissions in uploads directory
3. Ensure image files exist and are accessible

### Filtering Not Working
1. Check JavaScript console for errors
2. Verify filter event listeners are properly attached
3. Check if product data structure matches expected format

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🔒 Security Notes

- All user inputs are properly sanitized
- File uploads are validated for type and size
- Database queries use parameterized inputs
- XSS protection implemented throughout

## 📞 Support

For technical support or questions about the home decor system, please contact the development team.

---

**Note**: This system is designed to work with the main Glamour Palace e-commerce platform. Ensure all dependencies and database connections are properly configured before use.
