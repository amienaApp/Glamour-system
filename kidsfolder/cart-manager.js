// Cart Manager for Quick View System
class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.updateCartDisplay();
    }

    // Load cart from localStorage
    loadCart() {
        const savedCart = localStorage.getItem('glamour-cart');
        return savedCart ? JSON.parse(savedCart) : [];
    }

    // Save cart to localStorage
    saveCart() {
        localStorage.setItem('glamour-cart', JSON.stringify(this.cart));
        this.updateCartDisplay();
    }

    // Add item to cart
    addToCart(product, selectedColor, selectedSize, quantity = 1) {
        const existingItem = this.cart.find(item => 
            item.productId === product.id && 
            item.color === selectedColor && 
            item.size === selectedSize
        );

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                productId: product.id,
                name: product.name,
                price: product.salePrice || product.price,
                color: selectedColor,
                size: selectedSize,
                quantity: quantity,
                image: product.images[0]?.src || '',
                maxStock: product.stock
            });
        }

        this.saveCart();
        this.showCartNotification('Product added to cart!');
        return true;
    }

    // Remove item from cart
    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.showCartNotification('Product removed from cart!');
    }

    // Update item quantity
    updateQuantity(index, quantity) {
        if (quantity <= 0) {
            this.removeFromCart(index);
        } else {
            this.cart[index].quantity = quantity;
            this.saveCart();
        }
    }

    // Get cart total
    getCartTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    // Get cart count
    getCartCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    }

    // Clear cart
    clearCart() {
        this.cart = [];
        this.saveCart();
    }

    // Update cart display
    updateCartDisplay() {
        const cartCount = this.getCartCount();
        const cartTotal = this.getCartTotal();
        
        // Update cart icon count
        const cartIcon = document.querySelector('.cart-icon-count');
        if (cartIcon) {
            cartIcon.textContent = cartCount;
            cartIcon.style.display = cartCount > 0 ? 'block' : 'none';
        }

        // Update cart total
        const cartTotalElement = document.querySelector('.cart-total');
        if (cartTotalElement) {
            cartTotalElement.textContent = `$${cartTotal.toFixed(2)}`;
        }
    }

    // Show cart notification
    showCartNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);

        // Hide and remove notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Render cart sidebar
    renderCartSidebar() {
        const cartSidebar = document.getElementById('cart-sidebar');
        if (!cartSidebar) return;

        if (this.cart.length === 0) {
            cartSidebar.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Add some products to get started!</p>
                </div>
            `;
            return;
        }

        cartSidebar.innerHTML = `
            <div class="cart-header">
                <h3>Shopping Cart (${this.getCartCount()} items)</h3>
                <button class="close-cart" onclick="cartManager.closeCartSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cart-items">
                ${this.cart.map((item, index) => `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="${item.image}" alt="${item.name}">
                        </div>
                        <div class="cart-item-details">
                            <h4>${item.name}</h4>
                            <p class="cart-item-variant">${item.color} • ${item.size}</p>
                            <p class="cart-item-price">$${item.price.toFixed(2)}</p>
                            <div class="cart-item-quantity">
                                <button onclick="cartManager.updateQuantity(${index}, ${item.quantity - 1})">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="cartManager.updateQuantity(${index}, ${item.quantity + 1})">+</button>
                            </div>
                        </div>
                        <button class="remove-item" onclick="cartManager.removeFromCart(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `).join('')}
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total:</span>
                    <span>$${this.getCartTotal().toFixed(2)}</span>
                </div>
                <div class="cart-actions">
                    <button class="btn-secondary" onclick="cartManager.clearCart()">Clear Cart</button>
                    <button class="btn-primary" onclick="cartManager.checkout()">Checkout</button>
                </div>
            </div>
        `;
    }

    // Open cart sidebar
    openCartSidebar() {
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartSidebar && cartOverlay) {
            this.renderCartSidebar();
            cartSidebar.classList.add('active');
            cartOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    // Close cart sidebar
    closeCartSidebar() {
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartSidebar && cartOverlay) {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Checkout function
    checkout() {
        if (this.cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }
        
        // Redirect to checkout page or show checkout form
        alert('Redirecting to checkout...');
        // window.location.href = 'checkout.php';
    }
}

// Initialize cart manager
const cartManager = new CartManager();

