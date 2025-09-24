/**
 * Dashboard Widgets
 * Real-time updates and interactive features for the dashboard
 */

class DashboardWidgets {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.isInitialized = true;
        this.setupEventListeners();
        this.startPeriodicUpdates();
        this.setupOrderActions();
    }

    setupEventListeners() {
        // Listen for cart updates
        document.addEventListener('cartUpdated', (event) => {
            this.updateCartCount();
        });

        // Listen for wishlist updates
        document.addEventListener('wishlistUpdated', (event) => {
            this.updateWishlistCount();
        });

        // Listen for order updates
        document.addEventListener('orderUpdated', (event) => {
            this.refreshOrderData();
        });
    }

    startPeriodicUpdates() {
        // Update counts every 30 seconds
        setInterval(() => {
            this.updateCartCount();
            this.updateWishlistCount();
        }, this.updateInterval);
    }

    updateCartCount() {
        fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_stats'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.data.cart_count;
                    this.animateCounter(cartCountElement);
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
        });
    }

    updateWishlistCount() {
        try {
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            const wishlistCountElement = document.getElementById('wishlist-count');
            if (wishlistCountElement) {
                wishlistCountElement.textContent = wishlist.length;
                this.animateCounter(wishlistCountElement);
            }
        } catch (error) {
            console.error('Error updating wishlist count:', error);
        }
    }

    refreshOrderData() {
        // Refresh recent orders
        fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_recent_orders&limit=5'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateOrderDisplay(data.data);
            }
        })
        .catch(error => {
            console.error('Error refreshing order data:', error);
        });
    }

    updateOrderDisplay(orders) {
        const ordersContainer = document.querySelector('.section-card .row');
        if (!ordersContainer) return;

        // Clear existing orders
        const existingOrders = ordersContainer.querySelectorAll('.order-item');
        existingOrders.forEach(order => order.remove());

        // Add new orders
        orders.forEach(order => {
            const orderElement = this.createOrderElement(order);
            ordersContainer.appendChild(orderElement);
        });
    }

    createOrderElement(order) {
        const orderDiv = document.createElement('div');
        orderDiv.className = 'order-item';
        orderDiv.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded p-2">
                            <i class="fas fa-receipt fa-2x text-muted"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Order #${order.order_number}</h6>
                            <small class="text-muted">
                                ${new Date(order.created_at).toLocaleDateString('en-US', { 
                                    month: 'short', 
                                    day: 'numeric', 
                                    year: 'numeric' 
                                })}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fw-bold">$${parseFloat(order.total_amount).toFixed(2)}</div>
                        <small class="text-muted">${order.item_count} items</small>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <span class="order-status status-${order.status}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                    ${this.getOrderActions(order)}
                </div>
            </div>
        `;
        return orderDiv;
    }

    getOrderActions(order) {
        if (order.status === 'pending' || order.status === 'confirmed') {
            return `
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-danger" onclick="cancelOrder('${order._id}')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            `;
        }
        return '';
    }

    setupOrderActions() {
        // Cancel order function
        window.cancelOrder = (orderId) => {
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch('dashboard-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=cancel_order&order_id=${orderId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Order cancelled successfully', 'success');
                        this.refreshOrderData();
                    } else {
                        this.showNotification(data.message || 'Failed to cancel order', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error cancelling order:', error);
                    this.showNotification('An error occurred while cancelling order', 'error');
                });
            }
        };
    }

    animateCounter(element) {
        element.style.transform = 'scale(1.1)';
        element.style.transition = 'transform 0.2s ease';
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 200);
    }

    showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Public method to refresh all data
    refreshAll() {
        this.updateCartCount();
        this.updateWishlistCount();
        this.refreshOrderData();
    }

    // Public method to get dashboard statistics
    getStats() {
        return fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_stats'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            }
            throw new Error(data.message || 'Failed to get stats');
        });
    }
}

// Initialize dashboard widgets when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardWidgets = new DashboardWidgets();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardWidgets;
}
