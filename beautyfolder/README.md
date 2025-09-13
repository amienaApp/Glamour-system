# Beauty & Cosmetics Section

This folder contains the complete beauty and cosmetics section for the Glamour Palace e-commerce system.

## Features

### 🎨 **Comprehensive Beauty Categories**
- **Makeup**: Face, Eye, Lip, Nails, Tools
- **Skincare**: Moisturizers, Cleansers, Masks, Creams
- **Hair**: Shampoo, Conditioner, Tools
- **Bath & Body**: Shower Gel, Scrubs, Soap

### 🔍 **Advanced Filtering System**
- Price range filtering
- Brand search and selection
- Color/Shade filtering with visual color picker
- Skin type filtering (for skincare products)
- Hair type filtering (for hair products)
- Real-time search with suggestions

### 🛍️ **Product Management**
- Product grid and list views
- Quick view functionality
- Add to cart with variants
- Wishlist management
- Product reviews and ratings

### 📱 **Responsive Design**
- Mobile-first approach
- Touch-friendly interface
- Optimized for all screen sizes

## File Structure

```
beautyfolder/
├── beauty.php                 # Main beauty page
├── filter-api.php            # API for filtering and product operations
├── login-handler.php         # User authentication
├── logout-handler.php        # User logout
├── register-handler.php      # User registration
├── README.md                 # This file
├── includes/
│   ├── sidebar.php           # Category sidebar with filters
│   └── main-content.php      # Main product display area
├── styles/
│   ├── main.css              # Main page styles
│   └── sidebar.css           # Sidebar and filter styles
└── js/
    ├── script.js             # Main functionality
    └── search.js             # Search and suggestions
```

## Category Structure

### Makeup
- **Face**: Foundation, Concealer, Powder, Blush, Highlighter, Bronzer & Contour, Face Primer, Setting Spray
- **Eye**: Mascara, Eyeliner, Eyeshadow, Eyebrow Pencils/Gels, False Lashes, Eye Primer
- **Lip**: Lipstick, Lip Gloss, Lip Liner, Lip Stain, Lip Balm
- **Nails**: Nail Polish, Nail Care & Treatments, Nail Tools
- **Tools**: Brushes (Face, Eye, Lip), Makeup Removers, Filters for Makeup

### Skincare
- Moisturizers
- Cleansers
- Masks
- Cream
- Filters for Skincare

### Hair
- Shampoo
- Conditioner
- Tools
- Filters for Hair

### Bath & Body
- Shower Gel
- Scrubs
- Soap
- Filters for Bath & Body

## Usage

### Accessing the Beauty Section
Navigate to `beautyfolder/beauty.php` to access the main beauty page.

### URL Parameters
- `?subcategory=makeup` - Filter by main category
- `?subcategory=makeup&type=face` - Filter by subcategory
- `?subcategory=makeup&type=face&item=foundation` - Filter by specific item

### API Endpoints
The `filter-api.php` provides the following endpoints:

- `POST action=get_products` - Get products with filters
- `POST action=search` - Search products
- `POST action=get_suggestions` - Get search suggestions
- `POST action=sort` - Sort products
- `POST action=filter` - Apply filters
- `POST action=load_more` - Load more products
- `POST action=add_to_cart` - Add product to cart
- `POST action=toggle_wishlist` - Toggle wishlist
- `POST action=get_product_details` - Get product details

## Integration

### With Main System
- Uses the same MongoDB database
- Integrates with existing cart system
- Uses shared authentication system
- Compatible with existing product models

### With Other Sections
- Shares header and navigation
- Uses consistent styling patterns
- Integrates with global search
- Compatible with quick view system

## Customization

### Adding New Categories
1. Update the `$beautyCategories` array in `includes/sidebar.php`
2. Add corresponding CSS styles in `styles/sidebar.css`
3. Update the category navigation in `beauty.php`

### Adding New Filters
1. Add filter HTML in `includes/sidebar.php`
2. Add filter logic in `filter-api.php`
3. Add filter handling in `js/script.js`

### Styling
- Main styles: `styles/main.css`
- Sidebar styles: `styles/sidebar.css`
- Responsive breakpoints: 768px, 480px

## Dependencies

- PHP 8.2+
- MongoDB
- Font Awesome 6.0+
- Modern browser with ES6+ support

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Performance

- Lazy loading of product images
- Debounced search input
- Cached search suggestions
- Optimized database queries
- Minimal JavaScript bundle

## Security

- Input validation and sanitization
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure session management

## Future Enhancements

- [ ] Product comparison feature
- [ ] Virtual try-on for makeup
- [ ] Beauty tutorials integration
- [ ] Social sharing features
- [ ] Advanced color matching
- [ ] Skin tone analysis
- [ ] Beauty routine builder
- [ ] Ingredient analysis
- [ ] Allergy warnings
- [ ] Sustainability ratings




