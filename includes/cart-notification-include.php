<?php
// Cart Notification System Include
// This file provides cart notification functionality for the home page

// No PHP code needed here - this is just a placeholder
// The actual cart notification functionality is handled by JavaScript
?>

<script>
// Cart Notification Manager
window.cartNotificationManager = {
    loadCartCount: function() {
        // This function will be called to load cart count
        // The actual implementation is in the home header
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
    },
    
    showNotification: function(message, type = 'success') {
        // Show notification using the existing notification system
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            // Fallback notification
            alert(message);
        }
    }
};
</script>

