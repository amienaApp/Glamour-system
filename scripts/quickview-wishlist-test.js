/**
 * Quickview Wishlist Test Script
 * This script tests the quickview wishlist functionality
 */

// Test function to verify quickview wishlist integration
function testQuickviewWishlist() {
    console.log('Testing Quickview Wishlist Integration...');
    
    // Check if managers are loaded
    if (!window.quickviewManager) {
        console.error('❌ QuickviewManager not found');
        return false;
    }
    
    if (!window.wishlistManager) {
        console.error('❌ WishlistManager not found');
        return false;
    }
    
    console.log('✅ Both managers are loaded');
    
    // Check if quickview elements exist
    const quickviewSidebar = document.getElementById('quick-view-sidebar');
    const wishlistButton = document.getElementById('add-to-wishlist-quick');
    
    if (!quickviewSidebar) {
        console.error('❌ Quickview sidebar not found');
        return false;
    }
    
    if (!wishlistButton) {
        console.error('❌ Wishlist button not found');
        return false;
    }
    
    console.log('✅ Quickview elements found');
    
    // Test product data storage
    const testProduct = {
        id: 'test-product-123',
        name: 'Test Product',
        price: 29.99,
        front_image: 'test-image.jpg',
        category: 'Test Category',
        images: [{ src: 'test-image.jpg', alt: 'Test' }],
        colors: [{ value: 'red', name: 'Red', hex: '#ff0000' }],
        sizes: [{ name: 'M', available: true, stock: 10 }]
    };
    
    // Simulate opening quickview with test product
    console.log('🧪 Testing with mock product data...');
    
    // Store test data in quickview container
    quickviewSidebar.setAttribute('data-product-id', testProduct.id);
    quickviewSidebar.setAttribute('data-product-name', testProduct.name);
    quickviewSidebar.setAttribute('data-product-price', testProduct.price);
    quickviewSidebar.setAttribute('data-product-image', testProduct.front_image);
    quickviewSidebar.setAttribute('data-product-category', testProduct.category);
    
    // Test product ID retrieval
    const retrievedId = window.wishlistManager.getQuickViewProductId();
    if (retrievedId !== testProduct.id) {
        console.error('❌ Product ID retrieval failed');
        return false;
    }
    
    console.log('✅ Product ID retrieval works');
    
    // Test product details retrieval
    const productDetails = window.wishlistManager.getQuickViewProductDetails(testProduct.id);
    if (!productDetails || productDetails.id !== testProduct.id) {
        console.error('❌ Product details retrieval failed');
        return false;
    }
    
    console.log('✅ Product details retrieval works');
    console.log('📋 Retrieved details:', productDetails);
    
    // Test wishlist toggle
    console.log('🧪 Testing wishlist toggle...');
    const initialWishlist = window.wishlistManager.getWishlist();
    console.log('📋 Initial wishlist length:', initialWishlist.length);
    
    // Add to wishlist
    window.wishlistManager.toggleQuickViewWishlist(testProduct.id, wishlistButton);
    
    const afterAddWishlist = window.wishlistManager.getWishlist();
    console.log('📋 After add wishlist length:', afterAddWishlist.length);
    
    if (afterAddWishlist.length <= initialWishlist.length) {
        console.error('❌ Adding to wishlist failed');
        return false;
    }
    
    console.log('✅ Adding to wishlist works');
    
    // Remove from wishlist
    window.wishlistManager.toggleQuickViewWishlist(testProduct.id, wishlistButton);
    
    const afterRemoveWishlist = window.wishlistManager.getWishlist();
    console.log('📋 After remove wishlist length:', afterRemoveWishlist.length);
    
    if (afterRemoveWishlist.length !== initialWishlist.length) {
        console.error('❌ Removing from wishlist failed');
        return false;
    }
    
    console.log('✅ Removing from wishlist works');
    
    // Clean up test data
    quickviewSidebar.removeAttribute('data-product-id');
    quickviewSidebar.removeAttribute('data-product-name');
    quickviewSidebar.removeAttribute('data-product-price');
    quickviewSidebar.removeAttribute('data-product-image');
    quickviewSidebar.removeAttribute('data-product-category');
    
    console.log('🎉 All tests passed! Quickview wishlist integration is working correctly.');
    return true;
}

// Auto-run test when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for all scripts to load
    setTimeout(() => {
        if (window.location.search.includes('test-wishlist=true')) {
            testQuickviewWishlist();
        }
    }, 1000);
});

// Make test function available globally
window.testQuickviewWishlist = testQuickviewWishlist;
