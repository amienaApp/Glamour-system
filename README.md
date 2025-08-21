# 🛍️ Glamour Shopping System - Complete E-commerce Platform

A modern, full-featured e-commerce system for women's clothing and fashion retail, built with PHP and file-based storage. Perfect for the Somali market with local payment integration and regional customization.

## ✨ Key Features

### 🛒 **E-commerce Core**
- **Complete Shopping Experience** - Product browsing, cart, checkout, and order management
- **User Authentication System** - Registration, login, and user profiles
- **Shopping Cart** - Persistent cart with quantity management
- **Order Processing** - Complete order lifecycle from cart to delivery
- **Payment Integration** - Somali payment methods (Waafi, EVC, etc.)
- **Product Management** - Multi-variant products with color options

### 🎨 **Visual & UX**
- **Modern Responsive Design** - Works perfectly on all devices
- **Color Circle Display** - Products show actual color circles based on hex codes
- **Image Management** - Front and back product images with preview
- **Category System** - Organized browsing with subcategories
- **Quick View** - Product preview without leaving page
- **Wishlist** - Save favorite products

### ⚙️ **Technical Excellence**
- **File-Based Storage** - No database server required, uses JSON files
- **MongoDB-like Interface** - Custom database abstraction layer
- **Admin Dashboard** - Comprehensive management interface
- **API Endpoints** - RESTful APIs for cart, orders, and payments
- **Session Management** - Secure user authentication
- **Automatic Backups** - Data integrity protection

## 🚀 Quick Start

### **System Requirements**
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Composer (for dependencies)

### **Installation**
```bash
# Clone or download the project
cd Glamour-system

# Install dependencies (if using Composer)
composer install

# Set up file permissions
chmod 755 data/ uploads/
```

### **Access Points**
- **Main Store**: `http://localhost/Glamour-system/`
- **Women's Fashion**: `http://localhost/Glamour-system/womenF/`
- **Admin Dashboard**: `http://localhost/Glamour-system/admin/`
- **Products Page**: `http://localhost/Glamour-system/products.php`

## 📁 Project Structure

```
Glamour-system/
├── 📂 admin/                    # Admin dashboard
│   ├── index.php               # Main admin dashboard
│   ├── add-product.php         # Product creation (198KB)
│   ├── edit-product.php        # Product editing (146KB)
│   ├── manage-products.php     # Product management (39KB)
│   ├── manage-orders.php       # Order management (54KB)
│   ├── manage-users.php        # User management (43KB)
│   ├── manage-categories.php   # Category management (62KB)
│   ├── manage-payments.php     # Payment management (24KB)
│   ├── order-details.php       # Order details view (34KB)
│   ├── view-product.php        # Product details (48KB)
│   ├── login.php               # Admin login
│   ├── register.php            # Admin registration
│   └── includes/               # Admin components
├── 📂 womenF/                   # Women's fashion section
│   ├── index.php               # Main women's page
│   ├── login.php               # User login (13KB)
│   ├── register.php            # User registration (20KB)
│   ├── orders.php              # User orders (16KB)
│   ├── script.js               # Main JavaScript (50KB)
│   ├── login-handler.php       # Login processing
│   ├── register-handler.php    # Registration processing
│   ├── logout-handler.php      # Logout processing
│   ├── styles/                 # CSS files
│   └── includes/               # Header, sidebar, content
├── 📂 models/                   # Data models
│   ├── Product.php             # Product management (7.2KB)
│   ├── User.php                # User management (14KB)
│   ├── Order.php               # Order processing (11KB)
│   ├── Cart.php                # Shopping cart (9.2KB)
│   ├── Payment.php             # Payment handling (13KB)
│   ├── Admin.php               # Admin management (9.8KB)
│   └── Category.php            # Category management (9.3KB)
├── 📂 config/                   # Configuration
│   └── database.php            # File-based database system (346 lines)
├── 📂 data/                     # Data storage
│   ├── collections/            # JSON data files
│   │   ├── products.json       # Product catalog
│   │   ├── users.json          # User accounts
│   │   ├── orders.json         # Order history
│   │   ├── carts.json          # Shopping carts
│   │   ├── payments.json       # Payment records
│   │   └── admins.json         # Admin accounts
│   └── categories.json         # Category structure
├── 📂 uploads/                  # File uploads
│   └── products/               # Product images (182 files)
├── 📂 img/                      # Static images
│   ├── women/                  # Women's clothing images
│   ├── men/                    # Men's clothing images
│   ├── accessories/            # Accessories images
│   └── category/               # Category images
├── 📂 includes/                 # Reusable components
│   └── product-card.php        # Product display component (48KB)
├── 📂 vendor/                   # Dependencies
│   └── mongodb/                # MongoDB library
├── index.html                   # Main landing page (48KB)
├── products.php                 # Product catalog (57KB)
├── cart.php                     # Shopping cart (17KB)
├── cart-api.php                 # Cart API (8KB)
├── orders.php                   # Order management (18KB)
├── payment.php                  # Payment processing (32KB)
├── payment-api.php              # Payment API (6.4KB)
├── place-order.php              # Order placement (49KB)
├── style.css                    # Main stylesheet (17KB)
├── composer.json                # Dependencies
└── README.md                    # This file
```

## 🛍️ E-commerce Features

### **Product Management**
- **Multi-variant Products** - Color variants with different images
- **Category System** - Main categories and subcategories
- **Pricing** - Regular and sale pricing with discounts
- **Stock Management** - Inventory tracking and availability
- **Featured Products** - Highlight special items
- **Image Management** - Front and back product images

### **Shopping Experience**
- **Advanced Filtering** - By category, color, price, availability
- **Search Functionality** - Product search with regex support
- **Quick View** - Product preview without leaving page
- **Wishlist** - Save favorite products
- **Responsive Grid** - Product cards adapt to screen size
- **Pagination** - Efficient product browsing

### **Cart & Checkout**
- **Persistent Cart** - Items saved across sessions
- **Quantity Management** - Update quantities easily
- **Multiple Payment Methods** - Waafi, EVC, and other local options
- **Order Tracking** - Complete order history and status
- **Email Notifications** - Order confirmations and updates
- **Shipping Address** - Regional shipping support

## 👤 User System

### **Authentication**
- **User Registration** - Complete signup process with validation
- **Login System** - Secure authentication with session management
- **Profile Management** - User information and preferences
- **Session Security** - Secure session handling and logout

### **User Features**
- **Order History** - View past orders and track current ones
- **Dashboard** - User account overview and statistics
- **Regional Settings** - Somalia-specific regions and cities
- **Payment Methods** - Local payment integration
- **Account Settings** - Profile management and preferences

### **User Dropdown Menu**
- **Click to Toggle** - User icon toggles dropdown
- **Session Aware** - Shows different content based on login status
- **Quick Actions** - Dashboard, orders, and logout
- **Responsive Design** - Works on all screen sizes

## 🎨 Design System

### **Color Scheme**
- **Primary**: Ocean Blue (#0066CC)
- **Secondary**: Light Blue (#E6F3FF)
- **Accent**: Orange (#EE664E)
- **Neutral**: Gray tones for text and backgrounds
- **Success**: Green (#28A745)
- **Error**: Red (#DC3545)

### **Typography**
- **Primary Font**: Poppins (modern, clean)
- **Display Font**: Playfair Display (elegant headings)
- **Monospace**: Courier New (debug information)

### **Components**
- **Product Cards** - Hover effects, color circles, action buttons
- **Navigation** - Sticky header with dropdown menus
- **Modals** - Authentication and product quick view
- **Notifications** - Success, error, and info messages
- **Forms** - Modern form design with validation

## 🔧 Technical Architecture

### **Database System**
- **File-Based Storage** - JSON files for data persistence
- **MongoDB-like Interface** - Familiar query methods (find, insertOne, updateOne)
- **Automatic Backups** - Timestamped backup files for data safety
- **Data Integrity** - Validation and error handling
- **Collections** - Organized data storage (products, users, orders, carts)

### **API Endpoints**
- **Cart API** (`cart-api.php`) - Add, remove, update cart items
- **Payment API** (`payment-api.php`) - Process payments and transactions
- **Order API** - Create and manage orders
- **User API** - Authentication and profile management

### **Security Features**
- **Password Hashing** - Bcrypt encryption for user passwords
- **Session Security** - Secure session management with timeouts
- **Input Validation** - Data sanitization and validation
- **CSRF Protection** - Cross-site request forgery prevention
- **File Upload Security** - Secure image upload handling

## 🌍 Localization

### **Somali Market Focus**
- **Regional Data** - Somalia's 18 administrative regions
- **Local Payment Methods** - Waafi, EVC, and other local options
- **Currency Support** - USD and Somali Shilling
- **Contact Formats** - Somali phone number formats (+252)
- **Regional Cities** - Major cities for each region

### **User Experience**
- **Mobile-First** - Optimized for mobile devices
- **Fast Loading** - Optimized images and code
- **Accessibility** - Screen reader friendly
- **Cross-Browser** - Works on all modern browsers
- **Local Language** - Somali language considerations

## 📊 Data Structure

### **Product Schema**
```json
{
  "_id": "unique_id",
  "name": "Product Name",
  "price": 89.99,
  "color": "#0066CC",
  "category": "Women's Clothing",
  "subcategory": "Dresses",
  "images": {
    "front": "uploads/products/front.jpg",
    "back": "uploads/products/back.jpg"
  },
  "color_variants": [
    {
      "name": "Blue",
      "color": "#0066CC",
      "front_image": "path/to/image.jpg",
      "back_image": "path/to/image.jpg"
    }
  ],
  "description": "Product description",
  "featured": true,
  "sale": false,
  "salePrice": null,
  "stock": 10,
  "size_category": "",
  "selected_sizes": "",
  "createdAt": "2025-08-15 10:30:00",
  "updatedAt": "2025-08-15 10:30:00"
}
```

### **User Schema**
```json
{
  "_id": "unique_id",
  "username": "username",
  "email": "user@example.com",
  "contact_number": "+252907166125",
  "gender": "female",
  "region": "mudug",
  "city": "galkayo",
  "password": "hashed_password",
  "status": "active",
  "role": "user",
  "created_at": "2025-08-15 17:02:05",
  "updated_at": "2025-08-15 17:02:05"
}
```

### **Order Schema**
```json
{
  "_id": "unique_id",
  "user_id": "user_id",
  "items": [
    {
      "product_id": "product_id",
      "quantity": 1,
      "price": 89.99,
      "subtotal": 89.99,
      "product": {
        "name": "Product Name",
        "price": 89.99
      }
    }
  ],
  "total_amount": 89.99,
  "item_count": 1,
  "status": "pending",
  "order_number": "ORD20250820123456789",
  "shipping_address": "Address",
  "billing_address": "Address",
  "payment_method": "waafi",
  "notes": "",
  "created_at": "2025-08-20 12:34:56",
  "updated_at": "2025-08-20 12:34:56"
}
```

## 🎯 Usage Examples

### **Adding Products**
1. Access admin dashboard at `/admin/`
2. Navigate to "Add New Product"
3. Fill product details and upload images
4. Set pricing, category, and variants
5. Submit to create product

### **User Registration**
1. Click "Sign Up" in header dropdown
2. Fill registration form with details
3. Select region and city from dropdowns
4. Create account and login automatically

### **Making a Purchase**
1. Browse products and add to cart
2. Review cart and proceed to checkout
3. Fill shipping and billing information
4. Select payment method (Waafi, EVC, etc.)
5. Complete order and receive confirmation

### **Managing Orders**
1. Access "My Orders" from user dropdown
2. View order history and current orders
3. Track order status and details
4. Contact support if needed

## 🛠️ Customization

### **Adding Features**
- **New Models** - Extend existing model classes
- **Admin Pages** - Create new management interfaces
- **API Endpoints** - Add new functionality
- **Frontend Pages** - Create new user-facing pages

### **Styling**
- **CSS Variables** - Easy color scheme changes
- **Component Styles** - Modular CSS architecture
- **Responsive Design** - Mobile-first approach
- **Theme Support** - Easy theme switching

### **Configuration**
- **Database Settings** - Modify storage paths
- **Payment Methods** - Add new payment options
- **Email Templates** - Customize notifications
- **Regional Settings** - Add new regions/cities

## 🚀 Deployment

### **Local Development**
```bash
# Start local server
php -S localhost:8000

# Access application
http://localhost:8000
```

### **Production Deployment**
1. Upload files to web server
2. Set proper file permissions (755 for directories, 644 for files)
3. Configure web server (Apache/Nginx)
4. Set up SSL certificate for security
5. Configure domain and DNS settings

### **Backup Strategy**
- **Automatic Backups** - System creates timestamped backups
- **Manual Backups** - Copy `data/` folder for complete backup
- **Image Backups** - Backup `uploads/` folder for product images
- **Configuration** - Backup configuration files

## 📈 Performance

### **Optimizations**
- **Image Optimization** - Compressed product images
- **Code Minification** - Minified CSS and JavaScript
- **Caching** - Browser and server-side caching
- **Database Indexing** - Efficient data queries
- **Lazy Loading** - Images load as needed

### **Monitoring**
- **Error Logging** - Comprehensive error tracking
- **Performance Metrics** - Load time monitoring
- **User Analytics** - Usage statistics
- **Backup Monitoring** - Data integrity checks

## 🔒 Security

### **Authentication Security**
- **Password Hashing** - Bcrypt with salt
- **Session Management** - Secure session handling
- **CSRF Protection** - Cross-site request forgery prevention
- **Input Validation** - Data sanitization

### **File Security**
- **Upload Validation** - Secure file upload handling
- **Path Protection** - Prevent directory traversal
- **File Type Validation** - Only allow safe file types
- **Size Limits** - Prevent large file uploads

## 🌟 Benefits

- ✅ **No Database Setup** - Works immediately without configuration
- ✅ **Fast Performance** - File-based storage is lightning fast
- ✅ **Easy Backup** - Simple file copying for backups
- ✅ **Portable** - Move to any server easily
- ✅ **Modern UI** - Beautiful, professional design
- ✅ **Full Featured** - Complete e-commerce functionality
- ✅ **Local Market Focus** - Tailored for Somali market
- ✅ **Mobile Optimized** - Perfect mobile experience
- ✅ **Secure** - Enterprise-level security features
- ✅ **Scalable** - Modular architecture for growth
- ✅ **User Friendly** - Intuitive interface and navigation
- ✅ **Admin Dashboard** - Comprehensive management tools

## 🔗 Quick Links

- **Main Store**: `http://localhost/Glamour-system/`
- **Women's Fashion**: `http://localhost/Glamour-system/womenF/`
- **Admin Dashboard**: `http://localhost/Glamour-system/admin/`
- **Product Catalog**: `http://localhost/Glamour-system/products.php`
- **Shopping Cart**: `http://localhost/Glamour-system/cart.php`
- **Orders**: `http://localhost/Glamour-system/orders.php`
- **Payment**: `http://localhost/Glamour-system/payment.php`

## 📞 Support

For support and questions:
- Check the admin dashboard for system status
- Review error logs in the server
- Contact the development team

---

**Glamour Shopping System** - Powering modern e-commerce for the Somali market! 🛍️✨

*Built with ❤️ for fashion retail excellence*
