// Simple Quick View
document.addEventListener('DOMContentLoaded', function() {
    console.log('Quick view loaded');
    
    // Get elements
    const quickViewButtons = document.querySelectorAll('.quick-view');
    const sidebar = document.getElementById('quick-view-sidebar');
    const overlay = document.getElementById('quick-view-overlay');
    const closeBtn = document.getElementById('close-quick-view');
    
    // Add click handlers
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            openQuickView(productId);
        });
    });
    
    // Close handlers
    if (closeBtn) closeBtn.addEventListener('click', closeQuickView);
    if (overlay) overlay.addEventListener('click', closeQuickView);
    
    function openQuickView(productId) {
        console.log('Opening quick view for:', productId);
        
        // Find product card
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) return;
        
        // Extract data
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        // Populate quick view
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        
        if (titleEl) titleEl.textContent = name;
        if (priceEl) priceEl.textContent = price;
        
        // Show sidebar
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeQuickView() {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
});
