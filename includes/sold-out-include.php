<?php
/**
 * Universal Sold Out Include
 * This file provides sold out functionality across all pages
 */
?>
<script src="scripts/sold-out-manager.js?v=<?php echo time(); ?>"></script>
<script>
// Initialize sold out functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for sold out manager to be available
    if (window.soldOutManager) {
        console.log('SoldOutManager: Available and ready');
    } else {
        console.warn('SoldOutManager: Not available, retrying...');
        // Retry after a short delay
        setTimeout(() => {
            if (window.soldOutManager) {
                console.log('SoldOutManager: Available after retry');
            } else {
                console.error('SoldOutManager: Failed to load');
            }
        }, 1000);
    }
});
</script>

