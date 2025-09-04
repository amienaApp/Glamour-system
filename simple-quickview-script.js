/**
 * Simple Quickview Script
 * Works with existing HTML structure
 */

class SimpleQuickview {
    constructor() {
        this.currentProduct = null;
        this.selectedColor = null;
        this.selectedSize = null;
        this.init();
    }

    init() {
        console.log('SimpleQuickview: Initialized');
        this.bindEvents();
    }

    bindEvents() {
        // Close quickview when clicking overlay
        const overlay = document.getElementById('quick-view-overlay');
        if (overlay) {
            overlay.addEventListener('click', () => this.closeQuickview());
        }

        // Close button
        const closeBtn = document.getElementById('close-quick-view');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeQuickview());
        }
    }

    async openQuickview(productId) {
        console.log('SimpleQuickview: Opening quickview for product:', productId);
        
        // Show quickview
        this.showQuickview();
        
        // Load product data
        await this.loadProductData(productId);
    }

    showQuickview() {
        const sidebar = document.getElementById('quick-view-sidebar');
        const overlay = document.getElementById('quick-view-overlay');
        
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        
        document.body.style.overflow = 'hidden';
    }

    closeQuickview() {
        const sidebar = document.getElementById('quick-view-sidebar');
        const overlay = document.getElementById('quick-view-overlay');
        
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        
        document.body.style.overflow = '';
    }

    async loadProductData(productId) {
        try {
            console.log('SimpleQuickview: Loading product data...');
            
            // Show loading state
            this.showLoadingState();
            
            // Fetch product data
            const response = await fetch(`get-product-details.php?product_id=${productId}`);
            const data = await response.json();
            
            console.log('SimpleQuickview: Product data received:', data);
            
            if (data.success && data.product) {
                this.currentProduct = data.product;
                this.populateQuickview(data.product);
            } else {
                this.showError('Failed to load product data');
            }
            
        } catch (error) {
            console.error('SimpleQuickview: Error loading product:', error);
            this.showError('Network error - please try again');
        }
    }

    showLoadingState() {
        const title = document.getElementById('quick-view-title');
        const price = document.getElementById('quick-view-price');
        const description = document.getElementById('quick-view-description');
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnails = document.getElementById('quick-view-thumbnails');
        const colorSelection = document.getElementById('quick-view-color-selection');
        const sizeSelection = document.getElementById('quick-view-size-selection');
        
        if (title) title.textContent = 'Loading...';
        if (price) price.textContent = '';
        if (description) description.textContent = 'Loading product details...';
        if (mainImage) mainImage.style.display = 'none';
        if (thumbnails) thumbnails.innerHTML = '<div class="loading">Loading images...</div>';
        if (colorSelection) colorSelection.innerHTML = '<div class="loading">Loading colors...</div>';
        if (sizeSelection) sizeSelection.innerHTML = '<div class="loading">Loading sizes...</div>';
    }

    showError(message) {
        const title = document.getElementById('quick-view-title');
        const description = document.getElementById('quick-view-description');
        
        if (title) title.textContent = 'Error';
        if (description) description.textContent = message;
    }

    populateQuickview(product) {
        console.log('SimpleQuickview: Populating quickview with product:', product);
        
        // Set basic product info
        this.setElementText('quick-view-title', product.name);
        this.setElementText('quick-view-price', this.formatPrice(product.price, product.salePrice));
        this.setElementText('quick-view-description', product.description);
        
        // Set product images
        this.setProductImages(product);
        
        // Set colors
        this.setProductColors(product);
        
        // Set sizes
        this.setProductSizes(product);
        
        // Set add to cart button
        this.setAddToCartButton(product);
        
        // Set add to wishlist button
        this.setAddToWishlistButton(product);
        
        // Set default selections
        this.setDefaultSelections(product);
    }

    setElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element && text) {
            element.textContent = text;
        }
    }

    formatPrice(price, salePrice) {
        if (salePrice && salePrice < price) {
            return `<span style="text-decoration: line-through; color: #999;">$${price}</span> <span style="color: #e74c3c; font-weight: bold;">$${salePrice}</span>`;
        }
        return `$${price}`;
    }

    setProductImages(product) {
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnails = document.getElementById('quick-view-thumbnails');
        
        if (!mainImage || !thumbnails) return;
        
        // Get all images from the product
        let allImages = [];
        
        // Add main product images
        if (product.images && product.images.length > 0) {
            allImages = product.images;
        }
        
        // Add color variant images
        if (product.colors && product.colors.length > 0) {
            product.colors.forEach(color => {
                if (color.images && color.images.length > 0) {
                    allImages = allImages.concat(color.images);
                }
            });
        }
        
        // Remove duplicates
        allImages = allImages.filter((image, index, self) => 
            index === self.findIndex(img => img.src === image.src)
        );
        
        if (allImages.length > 0) {
            // Set main image
            mainImage.src = allImages[0].src;
            mainImage.alt = allImages[0].alt || product.name;
            mainImage.style.display = 'block';
            
            // Set thumbnails
            thumbnails.innerHTML = '';
            allImages.forEach((image, index) => {
                const thumbnail = document.createElement('div');
                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                thumbnail.innerHTML = `<img src="${image.src}" alt="${image.alt}" data-index="${index}">`;
                
                thumbnail.addEventListener('click', () => {
                    // Update main image
                    mainImage.src = image.src;
                    mainImage.alt = image.alt;
                    
                    // Update active thumbnail
                    thumbnails.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                    thumbnail.classList.add('active');
                });
                
                thumbnails.appendChild(thumbnail);
            });
        }
    }

    setProductColors(product) {
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (!colorSelection) return;
        
        if (product.colors && product.colors.length > 0) {
            colorSelection.innerHTML = '';
            product.colors.forEach((color, index) => {
                const colorCircle = document.createElement('div');
                colorCircle.className = `quick-view-color-circle ${index === 0 ? 'active' : ''}`;
                colorCircle.style.backgroundColor = color.hex || '#ccc';
                colorCircle.title = color.name;
                colorCircle.setAttribute('data-color', color.value);
                
                colorCircle.addEventListener('click', () => {
                    // Update active color
                    colorSelection.querySelectorAll('.quick-view-color-circle').forEach(c => c.classList.remove('active'));
                    colorCircle.classList.add('active');
                    
                    // Track selected color
                    this.selectedColor = color;
                    console.log('SimpleQuickview: Color selected:', color.name, color.value);
                    
                    // Update images for this color
                    this.updateImagesForColor(color);
                });
                
                colorSelection.appendChild(colorCircle);
            });
        } else {
            colorSelection.innerHTML = '<span>No colors available</span>';
        }
    }

    setProductSizes(product) {
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (!sizeSelection) return;
        
        if (product.sizes && product.sizes.length > 0) {
            sizeSelection.innerHTML = '';
            product.sizes.forEach((size, index) => {
                const sizeBtn = document.createElement('button');
                sizeBtn.className = `quick-view-size-btn ${index === 0 ? 'active' : ''}`;
                sizeBtn.textContent = size.name;
                sizeBtn.setAttribute('data-size', size.name);
                
                if (!size.available || size.stock <= 0) {
                    sizeBtn.classList.add('sold-out');
                    sizeBtn.disabled = true;
                }
                
                sizeBtn.addEventListener('click', () => {
                    if (!sizeBtn.disabled) {
                        // Update active size
                        sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                        sizeBtn.classList.add('active');
                        
                        // Track selected size
                        this.selectedSize = size;
                        console.log('SimpleQuickview: Size selected:', size.name);
                    }
                });
                
                sizeSelection.appendChild(sizeBtn);
            });
        } else {
            sizeSelection.innerHTML = '<span>No sizes available</span>';
        }
    }

    updateImagesForColor(color) {
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnails = document.getElementById('quick-view-thumbnails');
        
        if (!mainImage || !thumbnails || !color.images) return;
        
        if (color.images.length > 0) {
            // Update main image
            mainImage.src = color.images[0].src;
            mainImage.alt = color.images[0].alt;
            
            // Update thumbnails
            thumbnails.innerHTML = '';
            color.images.forEach((image, index) => {
                const thumbnail = document.createElement('div');
                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                thumbnail.innerHTML = `<img src="${image.src}" alt="${image.alt}" data-index="${index}">`;
                
                thumbnail.addEventListener('click', () => {
                    mainImage.src = image.src;
                    mainImage.alt = image.alt;
                    
                    thumbnails.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                    thumbnail.classList.add('active');
                });
                
                thumbnails.appendChild(thumbnail);
            });
        }
    }

    setAddToCartButton(product) {
        const addToCartBtn = document.querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.onclick = () => {
                // Use the tracked selected options
                const color = this.selectedColor ? this.selectedColor.name : 'Default';
                const size = this.selectedSize ? this.selectedSize.name : 'Default';
                
                console.log('SimpleQuickview: Add to cart clicked', {
                    productId: product.id,
                    productName: product.name,
                    selectedColor: color,
                    selectedSize: size,
                    colorValue: this.selectedColor ? this.selectedColor.value : 'default',
                    sizeData: this.selectedSize
                });
                
                // Validate selection
                if (!this.selectedColor || !this.selectedSize) {
                    alert('Please select both a color and size before adding to cart.');
                    return;
                }
                
                // Here you can add your cart logic
                alert(`Added to cart: ${product.name}\nColor: ${color}\nSize: ${size}\nPrice: $${product.price}`);
                
                // You can also call your cart API here:
                // this.addToCart(product.id, this.selectedColor.value, this.selectedSize.name);
            };
        }
    }
    
    setDefaultSelections(product) {
        // Set default color (first available)
        if (product.colors && product.colors.length > 0) {
            this.selectedColor = product.colors[0];
            console.log('SimpleQuickview: Default color set:', this.selectedColor.name);
        }
        
        // Set default size (first available)
        if (product.sizes && product.sizes.length > 0) {
            this.selectedSize = product.sizes[0];
            console.log('SimpleQuickview: Default size set:', this.selectedSize.name);
        }
    }
    
    setAddToWishlistButton(product) {
        const addToWishlistBtn = document.querySelector('.add-to-wishlist-btn');
        if (addToWishlistBtn) {
            addToWishlistBtn.onclick = () => {
                // Use the tracked selected options
                const color = this.selectedColor ? this.selectedColor.name : 'Default';
                const size = this.selectedSize ? this.selectedSize.name : 'Default';
                
                console.log('SimpleQuickview: Add to wishlist clicked', {
                    productId: product.id,
                    productName: product.name,
                    selectedColor: color,
                    selectedSize: size,
                    colorValue: this.selectedColor ? this.selectedColor.value : 'default',
                    sizeData: this.selectedSize
                });
                
                // Validate selection
                if (!this.selectedColor || !this.selectedSize) {
                    alert('Please select both a color and size before adding to wishlist.');
                    return;
                }
                
                // Here you can add your wishlist logic
                alert(`Added to wishlist: ${product.name}\nColor: ${color}\nSize: ${size}\nPrice: $${product.price}`);
                
                // You can also call your wishlist API here:
                // this.addToWishlist(product.id, this.selectedColor.value, this.selectedSize.name);
            };
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.simpleQuickview = new SimpleQuickview();
    console.log('SimpleQuickview: Ready to use');
});

// Global function to open quickview (can be called from HTML)
function openQuickview(productId) {
    if (window.simpleQuickview) {
        window.simpleQuickview.openQuickview(productId);
    } else {
        console.error('SimpleQuickview not initialized');
    }
}
