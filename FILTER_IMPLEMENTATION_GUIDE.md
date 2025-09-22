# Sidebar Filter Implementation Guide

This guide explains how to implement the working sidebar "Refine By" filters on all category pages.

## What's Been Implemented

### 1. Bags Page (Complete Implementation)
- ✅ Working AJAX-based filters
- ✅ Gender, Category, Color, and Price filters
- ✅ Real-time product grid updates
- ✅ Loading states and error handling
- ✅ Clear all filters functionality

### 2. Universal Filter System
- ✅ `scripts/universal-filter.js` - JavaScript class for consistent filtering
- ✅ `scripts/universal-filter-api.php` - Universal API for all categories
- ✅ `styles/filter-styles.css` - Consistent styling for all pages

## How to Implement on Other Pages

### Step 1: Include Required Files

Add these to your category page's `<head>` section:

```html
<link rel="stylesheet" href="styles/filter-styles.css?v=<?php echo time(); ?>">
<script src="../scripts/universal-filter.js?v=<?php echo time(); ?>"></script>
```

### Step 2: Update Your Sidebar

Replace your existing sidebar with the working filter structure. Use the bags page sidebar as a template:

```php
<!-- Sidebar Filters -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Refine By</h3>
        <span class="style-count" id="style-count"><?php echo count($allProducts); ?> Styles</span>
        <button type="button" class="clear-all-filters-btn" id="clear-filters" onclick="clearAllFilters()">
            <i class="fas fa-times"></i>
            Clear All Filters
        </button>
    </div>
    <div class="side">
        <!-- Your filter sections here -->
    </div>
</aside>
```

### Step 3: Update Your Filter API

Copy the `bagsfolder/filter-api.php` and modify it for your category:

1. Change the base category filter:
```php
$filters['category'] = "Your Category Name"; // e.g., "Beauty & Cosmetics", "Shoes", etc.
```

2. Update the color groups if needed for your category
3. Adjust subcategory options to match your products

### Step 4: Update Product Grid IDs

Make sure your product grid has one of these IDs:
- `all-products-grid`
- `filtered-products-grid`
- `all-bags-grid`
- `all-beauty-grid`
- `all-shoes-grid`
- `all-men-grid`
- `all-kids-grid`

Or use the class `.product-grid`

## Quick Implementation for Each Category

### Beauty & Cosmetics
1. Copy `bagsfolder/includes/sidebar.php` to `beautyfolder/includes/sidebar.php`
2. Update the category filter in the sidebar to use "Beauty & Cosmetics" subcategories
3. Copy `bagsfolder/filter-api.php` to `beautyfolder/filter-api.php`
4. Change the base category to "Beauty & Cosmetics"

### Shoes
1. Copy the sidebar and filter API files
2. Update subcategories to match shoe types (Sneakers, Boots, Heels, etc.)
3. Update the base category to "Shoes"

### Men's Clothing
1. Copy the sidebar and filter API files
2. Update subcategories to match men's clothing (Shirts, Pants, Jackets, etc.)
3. Update the base category to "Men"

### Kids
1. Copy the sidebar and filter API files
2. Update subcategories to match kids' clothing
3. Update the base category to "Kids"

## Filter Options by Category

### Bags
- Gender: Women, Men
- Categories: Shoulder Bags, Clutches, Tote Bags, Crossbody Bags, Backpacks, Briefcases, Laptop Bags, Waist Bags, Wallets
- Colors: All standard colors
- Price: $0-100, $100-200, $200-400, $400+

### Beauty & Cosmetics
- Gender: Women, Men
- Categories: Makeup, Skincare, Hair Care, Bath & Body, Beauty Tools
- Colors: All standard colors
- Price: $0-100, $100-200, $200-400, $400+

### Shoes
- Gender: Women, Men, Kids
- Categories: Sneakers, Boots, Heels, Flats, Sandals, Athletic
- Colors: All standard colors
- Price: $0-100, $100-200, $200-400, $400+

## Testing the Implementation

1. **Check Console**: Open browser dev tools and check for JavaScript errors
2. **Test Filters**: Try each filter option and verify products update
3. **Test Clear**: Click "Clear All Filters" and verify all products show
4. **Test Loading**: Verify loading indicators appear during filtering
5. **Test Errors**: Check error handling if API fails

## Troubleshooting

### Common Issues

1. **Filters not working**: Check browser console for JavaScript errors
2. **No products showing**: Verify the filter API is returning data
3. **Wrong products**: Check the category filter in the API
4. **Styling issues**: Ensure `filter-styles.css` is included

### Debug Steps

1. Check browser console for errors
2. Verify API responses in Network tab
3. Check that product grid element exists
4. Verify filter data is being sent correctly

## Files to Copy/Modify

### Required Files (Copy from bagsfolder)
- `includes/sidebar.php` - Main sidebar with filters
- `filter-api.php` - API for handling filter requests
- `styles/filter-styles.css` - Styling for filters

### Files to Include
- `../scripts/universal-filter.js` - Universal filter system
- `styles/filter-styles.css` - Filter styling

## Example Implementation

Here's a minimal example for a new category page:

```php
<!-- In your category page -->
<link rel="stylesheet" href="styles/filter-styles.css?v=<?php echo time(); ?>">
<script src="../scripts/universal-filter.js?v=<?php echo time(); ?>"></script>

<!-- In your page layout -->
<div class="page-layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content">
        <div class="product-grid" id="all-products-grid">
            <!-- Your products will be dynamically loaded here -->
        </div>
    </main>
</div>
```

The universal filter system will automatically detect your category and initialize the appropriate filters.

