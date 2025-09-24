# Glamour Palace E-commerce System
## Comprehensive System Documentation for Thesis Defense

---

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [System Architecture](#system-architecture)
4. [Database Design](#database-design)
5. [API Documentation](#api-documentation)
6. [Core Features](#core-features)
7. [File Structure](#file-structure)
8. [Security Implementation](#security-implementation)
9. [Performance Optimizations](#performance-optimizations)
10. [Deployment & Configuration](#deployment--configuration)
11. [Future Enhancements](#future-enhancements)

---

## System Overview

**Glamour Palace** is a comprehensive, full-stack e-commerce platform designed for fashion and lifestyle products. The system provides a complete online shopping experience with advanced features including dynamic product management, multi-category filtering, real-time cart functionality, and integrated payment processing.

### Key Statistics
- **Total Files**: 1,500+ files including images, stylesheets, and scripts
- **Product Categories**: 10+ main categories with 50+ subcategories
- **Database Collections**: 7 main collections (products, users, orders, carts, payments, categories, admins)
- **API Endpoints**: 25+ RESTful endpoints
- **Supported Payment Methods**: 3 (Waafi Mobile Money, Credit/Debit Cards, Bank Transfer)

---

## Technology Stack

### Backend Technologies
- **PHP 7.4+**: Server-side scripting language
- **MongoDB 4.4+**: NoSQL database for flexible data storage
- **Composer**: Dependency management
- **MongoDB PHP Library**: Official MongoDB driver for PHP

### Frontend Technologies
- **HTML5**: Semantic markup structure 
- **CSS3**: Advanced styling with Flexbox and Grid
- **JavaScript (ES6+)**: Client-side interactivity
- **Bootstrap 5.3.0**: Responsive framework
- **Swiper.js 11**: Touch slider functionality
- **Font Awesome 6.0.0**: Icon library
- **Animate.css 4.1.1**: CSS animations

### Development Tools
- **XAMPP**: Local development environment
- **Git**: Version control
- **Composer**: PHP package management

### External Services
- **MongoDB Atlas**: Cloud database hosting
- **CDN Services**: Bootstrap, Font Awesome, Swiper.js

---

## System Architecture

### 1. MVC Pattern Implementation
```
├── Models/           # Data layer (Product, User, Cart, Order, etc.)
├── Views/            # Presentation layer (PHP templates)
├── Controllers/      # Business logic (API endpoints)
└── Config/          # Configuration files
```

### 2. Database Architecture
- **Database**: `glamour_system`
- **Collections**: 
  - `products` - Product catalog
  - `users` - Customer accounts
  - `admins` - Administrator accounts
  - `orders` - Order management
  - `carts` - Shopping cart data
  - `payments` - Payment transactions
  - `categories` - Product categorization

### 3. API Architecture
- **RESTful Design**: Standard HTTP methods (GET, POST, PUT, DELETE)
- **JSON Responses**: Consistent data format
- **Error Handling**: Comprehensive error management
- **Authentication**: Session-based user management

---

## Database Design

### Collections Schema

#### Products Collection
```javascript
{
  _id: ObjectId,
  name: String,
  price: Number,
  category: String,
  subcategory: String,
  color_variants: Array,
  images: Object,
  stock: Number,
  available: Boolean,
  featured: Boolean,
  sale: Boolean,
  salePrice: Number,
  sizes: Array,
  description: String,
  createdAt: Date,
  updatedAt: Date
}
```

#### Users Collection
```javascript
{
  _id: ObjectId,
  username: String,
  email: String,
  password: String (hashed),
  contact_number: String,
  gender: String,
  region: String,
  city: String,
  status: String,
  role: String,
  created_at: Date,
  updated_at: Date
}
```

#### Orders Collection
```javascript
{
  _id: ObjectId,
  user_id: ObjectId,
  items: Array,
  total_amount: Number,
  status: String,
  order_number: String,
  shipping_address: String,
  payment_method: String,
  created_at: Date,
  expires_at: Date
}
```

#### Carts Collection
```javascript
{
  _id: ObjectId,
  user_id: String,
  items: Array,
  total: Number,
  item_count: Number,
  created_at: Date,
  updated_at: Date
}
```

---

## API Documentation

### Core APIs

#### 1. Cart Management API (`cart-api.php`)
- **POST** `add_to_cart` - Add product to cart
- **POST** `get_cart` - Retrieve user's cart
- **POST** `update_quantity` - Update item quantity
- **POST** `remove_item` - Remove item from cart
- **POST** `clear_cart` - Clear entire cart
- **POST** `place_order` - Create order from cart
- **POST** `get_cart_count` - Get cart item count

#### 2. Product Management API (`get-product-details.php`)
- **GET** `?product_id={id}` - Get detailed product information

#### 3. Payment Processing API (`payment-api.php`)
- **POST** `create_payment` - Initialize payment
- **POST** `process_payment` - Process payment transaction
- **POST** `get_payment_status` - Check payment status
- **POST** `get_payment_methods` - Get available payment methods
- **POST** `validate_phone` - Validate Somali phone numbers

#### 4. Perfumes API (`perfumes-api.php`)
- **GET** `get_perfumes` - Get filtered perfumes
- **GET** `get_brands` - Get available brands
- **GET** `get_sizes` - Get available sizes
- **GET** `get_statistics` - Get perfume statistics

#### 5. Authentication API (`auth-check.php`)
- **GET** - Check user authentication status

### Filter APIs (Category-specific)
Each category folder contains its own `filter-api.php`:
- **POST** `get_products` - Get filtered products
- **POST** `search` - Search products
- **POST** `get_suggestions` - Get search suggestions
- **POST** `sort` - Sort products
- **POST** `filter` - Apply filters
- **POST** `load_more` - Pagination

---

## Core Features

### 1. Product Management
- **Dynamic Product Loading**: All products loaded from MongoDB
- **Multi-level Categorization**: Categories → Subcategories → Sub-subcategories
- **Advanced Filtering**: Price, color, size, brand, availability
- **Search Functionality**: Real-time search with suggestions
- **Product Variants**: Color and size variations
- **Stock Management**: Real-time inventory tracking

### 2. Shopping Cart System
- **Session-based Cart**: Works for both authenticated and guest users
- **Real-time Updates**: Instant cart count and total updates
- **Stock Validation**: Prevents overselling
- **Persistent Cart**: Cart persists across sessions for logged-in users
- **Quick Actions**: Add, remove, update quantities

### 3. Order Management
- **Order Creation**: Convert cart to order
- **Order Tracking**: Status updates (pending, confirmed, completed, cancelled)
- **Order History**: User can view past orders
- **Order Expiry**: Automatic order expiration (2 hours)
- **Stock Integration**: Automatic stock reduction

### 4. Payment Processing
- **Multiple Payment Methods**:
  - Waafi Mobile Money (SAHAL, SAAD, EVC, EDAHAB)
  - Credit/Debit Cards (Visa, Mastercard, Amex)
  - Bank Transfer
- **Somali Phone Validation**: Specialized for Somali mobile numbers
- **Transaction Tracking**: Complete payment history
- **Order Confirmation**: Automatic order confirmation on successful payment

### 5. User Management
- **User Registration**: Complete user profile creation
- **Authentication**: Secure login/logout system
- **Profile Management**: Update user information
- **Password Security**: Bcrypt hashing
- **Admin Panel**: Separate admin authentication

### 6. Admin Dashboard
- **Product Management**: Add, edit, delete products
- **Order Management**: View and update orders
- **User Management**: Manage customer accounts
- **Category Management**: Organize product categories
- **Payment Management**: Track payment transactions
- **Statistics**: System analytics and reports

### 7. Responsive Design
- **Mobile-first Approach**: Optimized for mobile devices
- **Cross-browser Compatibility**: Works on all modern browsers
- **Touch-friendly Interface**: Optimized for touch devices
- **Progressive Enhancement**: Works without JavaScript

### 8. Performance Features
- **Image Optimization**: WebP and AVIF format support
- **Lazy Loading**: Images load as needed
- **Caching**: Browser and server-side caching
- **CDN Integration**: External resources from CDN
- **Database Indexing**: Optimized MongoDB queries

---

## File Structure

```
Glamour-system/
├── admin/                    # Admin panel
│   ├── manage-products.php
│   ├── manage-orders.php
│   ├── manage-users.php
│   └── includes/
├── models/                   # Data models
│   ├── Product.php
│   ├── User.php
│   ├── Cart.php
│   ├── Order.php
│   ├── Payment.php
│   ├── Category.php
│   └── Admin.php
├── config1/                  # Configuration
│   └── mongodb.php
├── includes/                 # Shared components
│   ├── product-card.php
│   ├── product-functions.php
│   └── cart-notification-include.php
├── [category-folders]/       # Product categories
│   ├── womenF/
│   ├── menfolder/
│   ├── kidsfolder/
│   ├── beautyfolder/
│   ├── bagsfolder/
│   ├── shoess/
│   ├── accessories/
│   ├── perfumes/
│   └── homedecor/
├── scripts/                  # JavaScript files
├── styles/                   # CSS files
├── img/                      # Product images
├── uploads/                  # User uploads
├── vendor/                   # Composer dependencies
├── cart-api.php             # Cart management API
├── payment-api.php          # Payment processing API
├── perfumes-api.php         # Perfumes API
├── get-product-details.php  # Product details API
├── auth-check.php           # Authentication API
└── index.php                # Homepage
```

---

## Security Implementation

### 1. Authentication & Authorization
- **Password Hashing**: Bcrypt with salt
- **Session Management**: Secure session handling
- **Role-based Access**: User and admin roles
- **Input Validation**: Server-side validation

### 2. Data Protection
- **SQL Injection Prevention**: Parameterized queries
- **XSS Protection**: Input sanitization
- **CSRF Protection**: Token-based protection
- **Data Encryption**: Sensitive data encryption

### 3. API Security
- **Request Validation**: Input validation on all endpoints
- **Rate Limiting**: Prevent API abuse
- **CORS Configuration**: Proper cross-origin setup
- **Error Handling**: Secure error messages

---

## Performance Optimizations

### 1. Database Optimizations
- **Indexing**: Strategic database indexes
- **Query Optimization**: Efficient MongoDB queries
- **Connection Pooling**: Reuse database connections
- **Caching**: Query result caching

### 2. Frontend Optimizations
- **Image Compression**: Optimized image formats
- **Minification**: Minified CSS and JavaScript
- **CDN Usage**: External resources from CDN
- **Lazy Loading**: Deferred image loading

### 3. Server Optimizations
- **Output Buffering**: Clean JSON responses
- **Error Suppression**: Production error handling
- **Memory Management**: Efficient memory usage
- **Response Compression**: Gzip compression

---

## Deployment & Configuration

### 1. System Requirements
- **PHP**: 7.4 or higher
- **MongoDB**: 4.4 or higher
- **Web Server**: Apache/Nginx
- **Composer**: For dependency management

### 2. Installation Steps
1. Clone the repository
2. Install dependencies: `composer install`
3. Configure MongoDB connection in `config1/mongodb.php`
4. Set up web server virtual host
5. Initialize database collections
6. Upload product images

### 3. Configuration Files
- **MongoDB Connection**: `config1/mongodb.php`
- **Cart Configuration**: `cart-config.php`
- **Composer Dependencies**: `composer.json`

---

## Future Enhancements

### 1. Planned Features
- **Email Notifications**: Order confirmations and updates
- **Advanced Analytics**: Detailed sales reports
- **Multi-language Support**: Internationalization
- **Mobile App**: Native mobile application
- **AI Recommendations**: Product recommendation engine

### 2. Technical Improvements
- **Microservices Architecture**: Service-oriented design
- **API Versioning**: Backward compatibility
- **Real-time Updates**: WebSocket integration
- **Advanced Caching**: Redis implementation

### 3. Business Features
- **Loyalty Program**: Customer rewards system
- **Inventory Management**: Advanced stock tracking
- **Marketing Tools**: Promotional campaigns
- **Customer Support**: Live chat integration

---

## Conclusion

The Glamour Palace e-commerce system represents a comprehensive, modern web application built with industry-standard technologies and best practices. The system demonstrates proficiency in:

- **Full-stack Development**: Complete frontend and backend implementation
- **Database Design**: Efficient NoSQL database architecture
- **API Development**: RESTful API design and implementation
- **Security Implementation**: Comprehensive security measures
- **Performance Optimization**: Multiple optimization strategies
- **User Experience**: Intuitive and responsive design

This system serves as a solid foundation for a production e-commerce platform and demonstrates the ability to build scalable, maintainable web applications using modern technologies.

---

**Total Development Time**: Estimated 6-8 months
**Lines of Code**: 15,000+ lines
**Database Records**: 1,000+ sample products
**API Endpoints**: 25+ endpoints
**Supported Devices**: Desktop, Tablet, Mobile
**Browser Support**: Chrome, Firefox, Safari, Edge

---

*This documentation serves as a comprehensive guide for thesis defense and system understanding.*
