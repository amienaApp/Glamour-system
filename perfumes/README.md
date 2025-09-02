# Perfumes Section - Dynamic E-commerce Module

## Overview
The perfumes section has been completely converted from static HTML to a dynamic, database-driven e-commerce module. All products are now loaded from the database with full filtering, sorting, and pagination capabilities.

## Features

### 🛍️ **Dynamic Product Loading**
- All perfume products are loaded from the database
- Real-time product information (prices, availability, stock levels)
- Automatic image handling for front/back views and color variants

### 🔍 **Advanced Filtering System**
- **Gender Filter**: Men's, Women's, Unisex perfumes
- **Brand Filter**: Dynamic list of available brands (Dior, Chanel, Gucci, etc.)
- **Size Filter**: Available sizes (30ml, 50ml, 100ml, 200ml)
- **Price Range Filter**: Multiple price brackets ($90-$110, $110-$150, etc.)
- **Clear All Filters**: One-click filter reset

### 📊 **Smart Sorting Options**
- Featured (default)
- Newest first
- Price: Low to High
- Price: High to Low
- Most Popular

### 📄 **Pagination**
- Configurable items per page (60, 120)
- Page navigation with current page highlighting
- URL-based pagination state

### 🛒 **Shopping Cart Integration**
- Add to cart functionality
- Real-time cart count updates
- Stock availability checking
- "Sold Out" status for unavailable items

### 🎨 **Interactive Features**
- Quick view modal with product details
- Color variant selection
- Image gallery with front/back views
- Wishlist functionality (UI ready)

## Database Structure

### Perfume Products
Each perfume product includes:
- **Basic Info**: Name, brand, gender, size, price
- **Images**: Front image, back image, color variants
- **Status**: Featured, sale, available, stock count
- **Metadata**: Creation date, last updated, unique ID

### Sample Data
The system includes 12 sample perfumes:
- **Men's Fragrances**: 5 products (Dior Sauvage, Gucci Guilty, etc.)
- **Women's Fragrances**: 7 products (Miss Dior, Chanel Coco, etc.)
- **Brands**: Dior, Chanel, Gucci, Valentino, Lattafa, Other
- **Sizes**: 30ml, 50ml, 100ml

## API Endpoints

### `perfumes-api.php`
- `GET /perfumes-api.php?action=get_perfumes` - Get filtered perfumes
- `GET /perfumes-api.php?action=get_brands` - Get available brands
- `GET /perfumes-api.php?action=get_sizes` - Get available sizes
- `GET /perfumes-api.php?action=get_statistics` - Get perfume statistics
- `GET /perfumes-api.php?action=initialize_perfumes` - Add sample data

### Query Parameters
- `gender`: Filter by gender (men/women)
- `brand`: Filter by brand name
- `size`: Filter by size
- `min_price`/`max_price`: Price range filter
- `sort`: Sorting option
- `limit`: Items per page
- `skip`: Pagination offset

## Setup Instructions

### 1. Initialize Sample Data
Run the initialization script to add sample perfumes:
```bash
php initialize-perfumes.php
```

### 2. Access the Perfumes Page
Navigate to: `http://your-domain/perfumes/`

### 3. Test Functionality
- Try different filters in the sidebar
- Test sorting options
- Add products to cart
- Use quick view feature

## File Structure

```
perfumes/
├── index.php              # Main entry point
├── includes/
│   ├── header.php         # Navigation and modals
│   ├── sidebar.php        # Dynamic filter sidebar
│   └── perfumes.php       # Dynamic product grid
├── styles/
│   ├── header.css         # Navigation styles
│   ├── sidebar.css        # Filter styles
│   └── main.css          # Product grid + new styles
├── script.js             # Interactive functionality
└── README.md            # This file

models/
└── Perfume.php          # Perfume-specific model

perfumes-api.php         # API endpoint
initialize-perfumes.php  # Data initialization script
```

## Technical Implementation

### Backend (PHP)
- **Perfume Model**: Extends Product model with perfume-specific methods
- **Database Integration**: Uses file-based JSON storage (MongoDB-like)
- **API Layer**: RESTful endpoints for dynamic data loading
- **Filtering Logic**: Advanced query building with multiple criteria

### Frontend (JavaScript)
- **Dynamic Loading**: AJAX-based product loading
- **Filter Management**: URL-based state management
- **Cart Integration**: Real-time cart updates
- **Interactive UI**: Modal dialogs, image galleries

### Styling (CSS)
- **Responsive Design**: Mobile-first approach
- **Modern UI**: Clean, professional e-commerce styling
- **Interactive Elements**: Hover effects, transitions
- **Accessibility**: Proper contrast, focus states

## Future Enhancements

### Planned Features
- [ ] Advanced search with autocomplete
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Product comparison
- [ ] Email notifications for restocks
- [ ] Related products suggestions

### Technical Improvements
- [ ] Caching layer for better performance
- [ ] Image optimization and lazy loading
- [ ] Advanced analytics tracking
- [ ] SEO optimization
- [ ] Mobile app integration

## Troubleshooting

### Common Issues

**No products showing:**
1. Run `php initialize-perfumes.php` to add sample data
2. Check database connection in `config1/database.php`
3. Verify file permissions for data directory

**Filters not working:**
1. Check JavaScript console for errors
2. Verify URL parameters are being passed correctly
3. Ensure database queries are working

**Images not loading:**
1. Check image paths in product data
2. Verify image files exist in `img/perfumes/` directory
3. Check file permissions

### Debug Mode
Enable debug mode by adding to `config1/database.php`:
```php
define('DEBUG', true);
```

## Support
For technical support or feature requests, please refer to the main project documentation or contact the development team. 