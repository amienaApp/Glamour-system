// Unified Accessories Script

// Quick View button click handler - use universal quickview manager
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
            e.preventDefault();
            const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
            const productId = button.getAttribute('data-product-id');
        if (productId && window.quickviewManager) {
            window.quickviewManager.openQuickview(productId);
        } else if (productId) {
            // Fallback to global function
            window.openQuickView(productId);
            }
        }
    });
    
document.addEventListener('DOMContentLoaded', function() {
    console.log('Accessories script loaded successfully');
    
    // Initialize all functionality
    initializeCategoryModals();
    initializeHeaderModals();
    initializeFilters();
    initializeColorSwitching();
    initializeCartFunctionality();
    
    // Category Modal Functionality
    function initializeCategoryModals() {
        const categoryModals = document.querySelectorAll('.category-modal');
        const modalTriggers = document.querySelectorAll('.modal-trigger');
        
        // Handle subcategory item clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.subcategory-item')) {
                const subcategoryItem = e.target.closest('.subcategory-item');
                const category = subcategoryItem.getAttribute('data-category');
                const subcategory = subcategoryItem.getAttribute('data-subcategory');
                
                if (category && subcategory) {
                    // Redirect to the appropriate page
                    window.location.href = `../${category}/${subcategory}.php`;
                }
            }
        });
        
        // Handle modal triggers
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetModal = this.getAttribute('data-modal');
                const modal = document.getElementById(targetModal);
                
                if (modal) {
                    // Close all other modals first
                    categoryModals.forEach(m => {
                            m.style.opacity = '0';
                            m.style.visibility = 'hidden';
                    });
                    
                    // Show target modal
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                }
            });
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-modal')) {
                categoryModals.forEach(modal => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                });
            }
        });
    }
    
    // Header Modals Functionality
    function initializeHeaderModals() {
        // Get modal elements
        const userModal = document.getElementById('user-modal');
        const userIcon = document.querySelector('.user-icon');
        const flagContainer = document.querySelector('.flag-container');
        const closeLoginModal = document.getElementById('close-login-modal');
        const closeRegisterModal = document.getElementById('close-register-modal');
        
        // User Icon Click - Open User Modal
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(userModal);
            });
        }
        
        // Flag Container Click - Disabled (Coming Soon)
        if (flagContainer) {
            flagContainer.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Region settings coming soon!');
            });
        }
        
        // Close Login Modal
        if (closeLoginModal) {
            closeLoginModal.addEventListener('click', function() {
                closeModal(userModal);
                // Reset to login form when closing
                switchToLoginForm();
            });
        }
        
        // Close Register Modal
        if (closeRegisterModal) {
            closeRegisterModal.addEventListener('click', function() {
                closeModal(userModal);
                // Reset to login form when closing
                switchToLoginForm();
            });
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target);
                // Reset to login form when closing
                switchToLoginForm();
            }
        });
        
        // Show/hide password functionality
        const showPasswordBtns = document.querySelectorAll('.show-password');
        
        showPasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }
    
    // Filter Functionality
    function initializeFilters() {
        // Add any filter-specific initialization here
        console.log('Filters initialized');
    }
    
    // Color Switching Functionality
    function initializeColorSwitching() {
        console.log('Initializing color switching...');
        
        // Color circle functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('color-circle')) {
                e.preventDefault();
                e.stopPropagation();
                
                const selectedColor = e.target.getAttribute('data-color');
                const productCard = e.target.closest('.product-card');
                
                if (productCard) {
                    const imageSlider = productCard.querySelector('.image-slider');
                    const colorCircles = productCard.querySelectorAll('.color-circle');
                    
                    // Remove active class from all color circles
                    colorCircles.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked circle
                    e.target.classList.add('active');
                    
                    // Show media for this color
                    if (imageSlider) {
                        const mediaForColor = imageSlider.querySelectorAll(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                        
                        if (mediaForColor.length > 0) {
                            // Hide all media first
                            imageSlider.querySelectorAll('img, video').forEach(media => {
                                media.classList.remove('active');
                                media.style.opacity = '0';
                            });
                            
                            // Show first media for this color (front view)
                            const firstMedia = mediaForColor[0];
                            firstMedia.classList.add('active');
                            firstMedia.style.opacity = '1';
                            
                            // Auto-play video if it's a video
                            if (firstMedia.tagName.toLowerCase() === 'video') {
                                firstMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                            }
                        }
                    }
                }
            }
        });
        
        // Media click to switch between front/back
        document.addEventListener('click', function(e) {
            if (e.target.closest('.image-slider') && (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO')) {
                const clickedMedia = e.target;
                const imageSlider = clickedMedia.closest('.image-slider');
                const productCard = imageSlider.closest('.product-card');
                
                if (imageSlider && productCard) {
                    const activeColorCircle = productCard.querySelector('.color-circle.active');
                    if (!activeColorCircle) return;
                    
                    const activeColor = activeColorCircle.getAttribute('data-color');
                    const allMedia = imageSlider.querySelectorAll('img, video');
                    const mediaForColor = Array.from(allMedia).filter(media => {
                        return media.getAttribute('data-color') === activeColor;
                    });
                    
                    if (mediaForColor.length > 1) {
                        const currentIndex = mediaForColor.indexOf(clickedMedia);
                        const nextIndex = (currentIndex + 1) % mediaForColor.length;
                        const nextMedia = mediaForColor[nextIndex];
                        
                        // Hide all media
                        allMedia.forEach(media => {
                            media.classList.remove('active');
                            media.style.opacity = '0';
                        });
                        
                        // Show the next media
                        nextMedia.classList.add('active');
                        nextMedia.style.opacity = '1';
                        
                        // Auto-play video if it's a video
                        if (nextMedia.tagName.toLowerCase() === 'video') {
                            nextMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                        }
                    }
                }
            }
        });
    }
    
    // Cart Functionality
    function initializeCartFunctionality() {
        console.log('Initializing cart functionality...');
        
        // Add to cart functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-bag') || e.target.closest('.add-to-bag')) {
                e.preventDefault();
                const button = e.target.classList.contains('add-to-bag') ? e.target : e.target.closest('.add-to-bag');
                
                if (button.disabled) return;
                
                if (button) {
                    addToCartFromCard(button);
                }
            }
            
            // Quick view add to bag functionality
            if (e.target.id === 'add-to-bag-quick' || e.target.id === 'add-to-bag-quick-alt' || 
                e.target.classList.contains('add-to-bag-quick') || e.target.closest('.add-to-bag-quick')) {
                e.preventDefault();
                const button = e.target.id === 'add-to-bag-quick' || e.target.id === 'add-to-bag-quick-alt' ? 
                              e.target : (e.target.classList.contains('add-to-bag-quick') ? e.target : e.target.closest('.add-to-bag-quick'));
                
                if (button.disabled) return;
                
                const productId = button.getAttribute('data-product-id');
                const productName = document.getElementById('quick-view-title')?.textContent || 'Product';
                
                if (productId) {
                    // Get selected color and size from quick view
                    const selectedQuickViewColor = document.querySelector('#quick-view-color-selection .quick-view-color-circle.active')?.getAttribute('data-color') || '';
                    const selectedQuickViewSize = document.querySelector('#quick-view-size-selection .size-option.active')?.textContent || '';
                    
                    // If no size selected, use fallback
                    if (!selectedQuickViewSize) {
                        selectedQuickViewSize = 'M';
                    }
                    
                    addToCartFromQuickView(productId, productName, selectedQuickViewColor, selectedQuickViewSize);
                }
            }
        });
    }
    
    // Add to cart functions (copied from women's page)
    function addToCart(productId, productName) {
        // Get the product card to find selected color and size
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        let selectedColor = '';
        let selectedSize = '';
        let selectedVariant = null;
        let selectedVariantImage = '';
        const mainPrice = productCard ? parseFloat(productCard.getAttribute('data-price')) || 0 : 0;
        
        if (productCard) {
        // Get selected color from active color circle
        const activeColorCircle = productCard.querySelector('.color-circle.active');
        if (activeColorCircle) {
            selectedColor = activeColorCircle.getAttribute('data-color') || '';
            
            // Get the currently active/visible image from the image slider
            const imageSlider = productCard.querySelector('.image-slider');
            if (imageSlider) {
                // Method 1: Try to find active image with matching color
                let activeImage = imageSlider.querySelector('img.active, video.active');
                
                if (activeImage && activeImage.getAttribute('data-color') === selectedColor) {
                    selectedVariantImage = activeImage.src;
                } else {
                    // Method 2: Find any image with the selected color
                    const colorImage = imageSlider.querySelector(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                    if (colorImage) {
                        selectedVariantImage = colorImage.src;
                    } else {
                        // Method 3: Find any visible image (opacity > 0 or display != none)
                        const allImages = imageSlider.querySelectorAll('img, video');
                        for (let img of allImages) {
                            const style = window.getComputedStyle(img);
                            if (style.opacity !== '0' && style.display !== 'none') {
                                selectedVariantImage = img.src;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Find the specific color variant data
            const variantsData = productCard.getAttribute('data-product-variants');
            if (variantsData && variantsData !== '[]' && variantsData !== 'null') {
                try {
                    const variants = JSON.parse(variantsData);
                    if (Array.isArray(variants) && variants.length > 0) {
                        selectedVariant = variants.find(variant => variant.color === selectedColor);
                    }
                } catch (e) {
                    console.log('Could not parse variants data:', e);
                }
            }
        }
            
            // Get selected size if available (for products with size selection)
            const activeSizeOption = productCard.querySelector('.size-option.selected');
            if (activeSizeOption) {
                selectedSize = activeSizeOption.getAttribute('data-size') || '';
            }
        }
        
        // Determine the price to use (variant price or main price)
        let finalPrice = mainPrice;
        if (selectedVariant && selectedVariant.price && selectedVariant.price > 0) {
            finalPrice = selectedVariant.price;
        }
        
        // Show loading state
        const button = document.querySelector(`[data-product-id="${productId}"] .add-to-bag`);
        let originalText = '';
        if (button) {
            originalText = button.textContent;
            button.textContent = 'Adding...';
            button.disabled = true;
        }
        
        // Prepare cart data
        let cartData = `action=add_to_cart&product_id=${productId}&quantity=1&color=${encodeURIComponent(selectedColor)}&size=${encodeURIComponent(selectedSize)}&price=${finalPrice}&return_url=${encodeURIComponent(window.location.href)}`;
        
        // Add variant-specific data if available
        if (selectedVariantImage) {
            cartData += `&variant_image=${encodeURIComponent(selectedVariantImage)}`;
        } else {
            // Fallback: Try to find variant image directly from product card
            const allImages = productCard.querySelectorAll('img[data-color]');
            for (let img of allImages) {
                if (img.getAttribute('data-color') === selectedColor) {
                    selectedVariantImage = img.src;
                    cartData += `&variant_image=${encodeURIComponent(selectedVariantImage)}`;
                    break;
                }
            }
        }
        
        if (selectedVariant) {
            if (selectedVariant.name) {
                cartData += `&variant_name=${encodeURIComponent(selectedVariant.name)}`;
            }
            if (selectedVariant.stock !== undefined) {
                cartData += `&variant_stock=${selectedVariant.stock}`;
            }
            if (!selectedVariantImage && selectedVariant.front_image) {
                cartData += `&variant_image=${encodeURIComponent(selectedVariant.front_image)}`;
                console.log('✅ Sending variant front_image to cart:', selectedVariant.front_image);
            }
        }
        
        // Make API call to add to cart with selected color and size
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: cartData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count using unified system
                if (window.cartNotificationManager) {
                    window.cartNotificationManager.handleCartUpdate(data);
                } else if (typeof addToCartCount === 'function') {
                    addToCartCount();
                }
                
                // Show brief success notification
                showNotification(`✓ ${productName} added to cart!`, 'success');
            } else {
                // Show error notification
                showNotification(`✗ Error: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('✗ Error adding product to cart', 'error');
        })
        .finally(() => {
            // Reset button state
            if (button && originalText) {
                button.textContent = originalText;
                button.disabled = false;
            }
        });
    }

    function addToCartFromCard(button) {
        // Get the product card from the button
        const productCard = button.closest('.product-card');
        if (!productCard) {
            console.error('Product card not found');
            return;
        }
        
        // Get product information
        const productId = productCard.getAttribute('data-product-id');
        const productName = productCard.querySelector('.product-name')?.textContent || 'Product';
        const mainPrice = parseFloat(productCard.getAttribute('data-price')) || 0;
        
        if (!productId) {
            console.error('Product ID not found');
            return;
        }
        
        // Get selected color from active color circle
        let selectedColor = '';
        let selectedVariant = null;
        let selectedVariantImage = '';
        const activeColorCircle = productCard.querySelector('.color-circle.active');
        if (activeColorCircle) {
            selectedColor = activeColorCircle.getAttribute('data-color') || '';
            
            // Get the currently active/visible image from the image slider
            const imageSlider = productCard.querySelector('.image-slider');
            if (imageSlider) {
                // Method 1: Try to find active image with matching color
                let activeImage = imageSlider.querySelector('img.active, video.active');
                
                if (activeImage && activeImage.getAttribute('data-color') === selectedColor) {
                    selectedVariantImage = activeImage.src;
                } else {
                    // Method 2: Find any image with the selected color
                    const colorImage = imageSlider.querySelector(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                    if (colorImage) {
                        selectedVariantImage = colorImage.src;
                    } else {
                        // Method 3: Find any visible image (opacity > 0 or display != none)
                        const allImages = imageSlider.querySelectorAll('img, video');
                        for (let img of allImages) {
                            const style = window.getComputedStyle(img);
                            if (style.opacity !== '0' && style.display !== 'none') {
                                selectedVariantImage = img.src;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Find the specific color variant data
            const variantsData = productCard.getAttribute('data-product-variants');
            if (variantsData && variantsData !== '[]' && variantsData !== 'null') {
                try {
                    const variants = JSON.parse(variantsData);
                    if (Array.isArray(variants) && variants.length > 0) {
                        selectedVariant = variants.find(variant => variant.color === selectedColor);
                    }
                } catch (e) {
                    console.log('Could not parse variants data:', e);
                }
            }
        }
        
        // Get selected size if available
        let selectedSize = '';
        const activeSizeOption = productCard.querySelector('.size-option.selected');
        if (activeSizeOption) {
            selectedSize = activeSizeOption.getAttribute('data-size') || '';
        }
        
        // Determine the price to use (variant price or main price)
        let finalPrice = mainPrice;
        if (selectedVariant && selectedVariant.price && selectedVariant.price > 0) {
            finalPrice = selectedVariant.price;
        }
        
        // Show loading state
        let originalText = button.textContent;
        button.textContent = 'Adding...';
        button.disabled = true;
        
        // Prepare cart data
        let cartData = `action=add_to_cart&product_id=${productId}&quantity=1&color=${encodeURIComponent(selectedColor)}&size=${encodeURIComponent(selectedSize)}&price=${finalPrice}&return_url=${encodeURIComponent(window.location.href)}`;
        
        // Add variant-specific data if available
        if (selectedVariantImage) {
            cartData += `&variant_image=${encodeURIComponent(selectedVariantImage)}`;
        } else {
            // Fallback: Try to find variant image directly from product card
            const allImages = productCard.querySelectorAll('img[data-color]');
            for (let img of allImages) {
                if (img.getAttribute('data-color') === selectedColor) {
                    selectedVariantImage = img.src;
                    cartData += `&variant_image=${encodeURIComponent(selectedVariantImage)}`;
                    break;
                }
            }
        }
        
        if (selectedVariant) {
            if (selectedVariant.name) {
                cartData += `&variant_name=${encodeURIComponent(selectedVariant.name)}`;
            }
            if (selectedVariant.stock !== undefined) {
                cartData += `&variant_stock=${selectedVariant.stock}`;
            }
            if (!selectedVariantImage && selectedVariant.front_image) {
                cartData += `&variant_image=${encodeURIComponent(selectedVariant.front_image)}`;
                console.log('✅ Sending variant front_image to cart:', selectedVariant.front_image);
            }
        }
        
        // Make API call to add to cart with selected variants
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: cartData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count using unified system
                if (window.cartNotificationManager) {
                    window.cartNotificationManager.handleCartUpdate(data);
                } else if (typeof addToCartCount === 'function') {
                    addToCartCount();
                }
                
                // Show brief success notification
                showNotification(`✓ ${productName} added to cart!`, 'success');
            } else {
                // Show error notification
                showNotification(`✗ Error: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('✗ Error adding product to cart', 'error');
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    function addToCartFromQuickView(productId, productName, selectedColor, selectedSize) {
        // Get the product card to find variant data
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        let selectedVariant = null;
        let selectedVariantImage = '';
        const mainPrice = productCard ? parseFloat(productCard.getAttribute('data-price')) || 0 : 0;
        
        if (productCard && selectedColor) {
            // Find the specific color variant data
            const variantsData = productCard.getAttribute('data-product-variants');
            if (variantsData && variantsData !== '[]' && variantsData !== 'null') {
                try {
                    const variants = JSON.parse(variantsData);
                    if (Array.isArray(variants) && variants.length > 0) {
                        selectedVariant = variants.find(variant => variant.color === selectedColor);
                    }
                } catch (e) {
                    console.log('Could not parse variants data:', e);
                }
            }
            
            // Get variant image for the selected color
            const imageSlider = productCard.querySelector('.image-slider');
            if (imageSlider) {
                const colorImage = imageSlider.querySelector(`img[data-color="${selectedColor}"], video[data-color="${selectedColor}"]`);
                if (colorImage) {
                    selectedVariantImage = colorImage.src;
                }
            }
        }
        
        // Determine the price to use (variant price or main price)
        let finalPrice = mainPrice;
        if (selectedVariant && selectedVariant.price && selectedVariant.price > 0) {
            finalPrice = selectedVariant.price;
        }
        
        // Show loading state
        const button = document.getElementById('add-to-bag-quick') || document.getElementById('add-to-bag-quick-alt');
        let originalText = '';
        if (button) {
            originalText = button.textContent;
            button.textContent = 'Adding...';
            button.disabled = true;
        }
        
        // Prepare cart data
        let cartData = `action=add_to_cart&product_id=${productId}&quantity=1&color=${encodeURIComponent(selectedColor)}&size=${encodeURIComponent(selectedSize)}&price=${finalPrice}&return_url=${encodeURIComponent(window.location.href)}`;
        
        // Add variant-specific data if available
        if (selectedVariantImage) {
            cartData += `&variant_image=${encodeURIComponent(selectedVariantImage)}`;
        }
        
        if (selectedVariant) {
            if (selectedVariant.name) {
                cartData += `&variant_name=${encodeURIComponent(selectedVariant.name)}`;
            }
            if (selectedVariant.stock !== undefined) {
                cartData += `&variant_stock=${selectedVariant.stock}`;
            }
            if (!selectedVariantImage && selectedVariant.front_image) {
                cartData += `&variant_image=${encodeURIComponent(selectedVariant.front_image)}`;
                console.log('✅ Sending variant front_image to cart:', selectedVariant.front_image);
            }
        }
        
        // Make API call to add to cart with selected variants
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: cartData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count using unified system
                if (window.cartNotificationManager) {
                    window.cartNotificationManager.handleCartUpdate(data);
                } else if (typeof addToCartCount === 'function') {
                    addToCartCount();
                }
                
                // Show brief success notification
                showNotification(`✓ ${productName} added to cart!`, 'success');
            } else {
                // Show error notification
                showNotification(`✗ Error: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('✗ Error adding product to cart', 'error');
        })
        .finally(() => {
            // Reset button state
            if (button && originalText) {
                button.textContent = originalText;
                button.disabled = false;
            }
        });
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.cart-notification');
        existingNotifications.forEach(notification => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        });
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        
        // Set colors based on type
        let backgroundColor, icon;
        switch (type) {
            case 'success':
                backgroundColor = '#28a745';
                icon = '✓';
                break;
            case 'error':
                backgroundColor = '#dc3545';
                icon = '✗';
                break;
            case 'info':
            default:
                backgroundColor = '#17a2b8';
                icon = 'ℹ';
                break;
        }
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${backgroundColor};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-weight: 500;
            font-size: 14px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            word-wrap: break-word;
        `;
        
        notification.textContent = `${icon} ${message}`;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Make functions globally accessible
    window.addToCart = addToCart;
    window.addToCartFromCard = addToCartFromCard;
    window.addToCartFromQuickView = addToCartFromQuickView;
});


