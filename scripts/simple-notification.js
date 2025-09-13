/**
 * Simple Notification System
 * Lightweight notifications for cart and wishlist
 */
(function() {
    'use strict';
    
    // Simple notification function
    function showNotification(message, type = 'success') {
        // Remove any existing notification
        const existing = document.querySelector('.simple-notification');
        if (existing) {
            existing.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'simple-notification';
        notification.textContent = message;
        
        // Simple styling
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 12px 16px;
            border-radius: 4px;
            z-index: 10000;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Show
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Hide after 2 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 2000);
    }
    
    // Global functions
    window.showNotification = showNotification;
    window.showSuccessNotification = (msg) => showNotification(msg, 'success');
    window.showErrorNotification = (msg) => showNotification(msg, 'error');
    window.showInfoNotification = (msg) => showNotification(msg, 'info');
    
    // Simple notification manager for compatibility
    window.notificationManager = {
        show: showNotification,
        success: (msg) => showNotification(msg, 'success'),
        error: (msg) => showNotification(msg, 'error'),
        info: (msg) => showNotification(msg, 'info'),
        warning: (msg) => showNotification(msg, 'warning'),
        hideAll: () => {
            const existing = document.querySelector('.simple-notification');
            if (existing) existing.remove();
        }
    };
    
})();

