# Glamour Shopping - Modern E-commerce System

A beautiful, modern e-commerce system for women's clothing built with PHP and file-based storage.

## ✨ Features

- **Dynamic Product Management**: Add products with images, colors, categories, and pricing
- **Color Circle Display**: Products show actual color circles based on hex color codes
- **Category & Subcategory System**: Organized product browsing
- **Admin Dashboard**: Easy product management interface
- **Responsive Design**: Works on all devices
- **File-Based Storage**: No database server required - uses JSON files
- **Image Upload**: Front and back product images
- **Filtering & Sorting**: By color, price, category, and more
- **Pagination**: Efficient product browsing

## 🚀 Quick Start

### 1. Setup
```bash
# Run the database setup script
php setup-database.php
```

### 2. Access Admin Dashboard
```
http://localhost/Glamour-system/admin/
```

### 3. Add Products
- Go to "Add New Product" in admin dashboard
- Upload front and back images
- Choose color using color picker
- Set price and category
- Submit to create product card

### 4. View Products
```
http://localhost/Glamour-system/category.php?category=Women's%20Clothing
```

## 📁 Project Structure

```
Glamour-system/
├── admin/                    # Admin dashboard
│   ├── index.php            # Main admin dashboard
│   ├── add-product.php      # Add new products
│   └── get-subcategories.php # AJAX endpoint
├── config/
│   └── database.php         # File-based database system
├── models/
│   ├── Product.php          # Product management
│   └── Category.php         # Category management
├── includes/
│   └── product-card.php     # Reusable product card component
├── data/                    # JSON data files
│   ├── products.json        # Product data
│   └── categories.json      # Category data
├── uploads/
│   └── products/            # Product images
├── category.php             # Category browsing page
├── setup-database.php       # Database initialization
└── README.md               # This file
```

## 🎨 Product Features

### Color System
- **Color Picker**: Choose exact colors for products
- **Color Circles**: Display actual color in product cards
- **Color Filtering**: Filter products by color

### Image Management
- **Front & Back Images**: Upload both views
- **Image Preview**: See images before saving
- **Automatic Resizing**: Optimized for web display

### Category System
- **Main Categories**: Women's Clothing, Men's Clothing, etc.
- **Subcategories**: Dresses, Tops, Bottoms, etc.
- **Dynamic Loading**: Subcategories load via AJAX

## 🔧 Technical Details

### File-Based Storage
- **No Database Required**: Uses JSON files for data storage
- **Fast & Reliable**: No server setup needed
- **Easy Backup**: Just copy the `data/` folder
- **Portable**: Works on any PHP server

### Admin Features
- **Product Management**: Add, edit, delete products
- **Category Management**: Organize products
- **Statistics Dashboard**: View store metrics
- **Image Upload**: Handle product images

### Frontend Features
- **Responsive Grid**: Product cards adapt to screen size
- **Filtering**: By color, price, category
- **Sorting**: By price, name, date
- **Pagination**: Efficient browsing

## 🎯 Usage Examples

### Adding a Product
1. Go to admin dashboard
2. Click "Add New Product"
3. Fill in product details:
   - Name: "Elegant Blue Dress"
   - Price: $89.99
   - Color: Choose blue (#0066CC)
   - Category: Women's Clothing
   - Subcategory: Dresses
   - Upload front and back images
4. Submit - product appears on category page

### Viewing Products
1. Visit category page
2. Use filters to find specific products
3. Click on product cards for details
4. Browse through pages of products

## 🛠️ Customization

### Adding Categories
Edit `models/Category.php` and add to `initializeDefaultCategories()` method.

### Styling
- Main styles: `style.css`
- Product cards: `includes/product-card.php`
- Admin styles: Inline CSS in admin files

### Features
- Add new features by extending the models
- Create new admin pages in `admin/` directory
- Add new frontend pages as needed

## 📊 Data Storage

### Products JSON Structure
```json
{
  "name": "Product Name",
  "price": 89.99,
  "color": "#0066CC",
  "category": "Women's Clothing",
  "subcategory": "Dresses",
  "images": {
    "front": "uploads/products/front.jpg",
    "back": "uploads/products/back.jpg"
  },
  "description": "Product description",
  "featured": true,
  "sale": false,
  "salePrice": null
}
```

### Categories JSON Structure
```json
{
  "name": "Women's Clothing",
  "subcategories": ["Dresses", "Tops", "Bottoms"],
  "createdAt": "2025-08-08 16:16:17",
  "updatedAt": "2025-08-08 16:16:17"
}
```

## 🎉 Benefits

- ✅ **No Database Setup**: Works immediately
- ✅ **Fast Performance**: File-based storage is quick
- ✅ **Easy Backup**: Copy data files
- ✅ **Portable**: Move to any server
- ✅ **Modern UI**: Beautiful, responsive design
- ✅ **Full Featured**: All e-commerce functionality
- ✅ **Easy to Extend**: Modular architecture

## 🚀 Ready to Use!

Your Glamour Shopping system is now ready! Start adding products and building your online store.

**Admin Dashboard**: `http://localhost/Glamour-system/admin/`
**Store Front**: `http://localhost/Glamour-system/category.php?category=Women's%20Clothing`
