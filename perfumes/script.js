// Simple Script - Clean Version (Quickview handled by centralized script)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Initialize category modal functionality
    initializeCategoryModals();
    
    // Initialize header modals functionality
    initializeHeaderModals();
    
    // Initialize filter functionality
    initializeFilters();
    
    // Quick View functionality is now handled by centralized script
    
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
                        
                        // Now you can click on the media to see back view
                        // The click handler will cycle through all media for this color
                        console.log(`Showing ${mediaForColor.length} media items for color: ${selectedColor}`);
                        console.log('Click on the media to see back view');
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
                    
                    console.log(`Switching from media ${currentIndex + 1} to ${nextIndex + 1} of ${mediaForColor.length}`);
                    
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
                } else {
                    console.log('Only one media item for this color, no switching possible');
                }
            }
        }
    });
    
    // Initialize product cards
    function initializeProductCard(card) {
        const colorCircles = card.querySelectorAll('.color-circle');
        const imageSlider = card.querySelector('.image-slider');
        
        // Ensure first media is visible
        if (imageSlider) {
            const firstMedia = imageSlider.querySelector('img, video');
            if (firstMedia) {
                firstMedia.classList.add('active');
                firstMedia.style.opacity = '1';
                
                // Auto-play video if it's a video
                if (firstMedia.tagName.toLowerCase() === 'video') {
                    firstMedia.play().catch(e => console.log('Video autoplay prevented:', e));
                }
            }
        }
        
        // Ensure first color circle is active
        if (colorCircles.length > 0) {
            const firstColorCircle = colorCircles[0];
            if (!firstColorCircle.classList.contains('active')) {
                firstColorCircle.classList.add('active');
            }
        }
    }
    
    // Initialize existing product cards
    const existingProductCards = document.querySelectorAll('.product-card');
    existingProductCards.forEach(card => {
        initializeProductCard(card);
    });
    
    // Add to cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-bag') || e.target.closest('.add-to-bag')) {
            e.preventDefault();
            const button = e.target.classList.contains('add-to-bag') ? e.target : e.target.closest('.add-to-bag');
            
            if (button.disabled) return;
            
            const productCard = button.closest('.product-card');
            const productId = productCard ? productCard.getAttribute('data-product-id') : null;
            const productName = productCard ? productCard.querySelector('.product-name')?.textContent : 'Product';
            
            if (productId) {
                addToCart(productId, productName);
            }
        }
    });
    
    function addToCart(productId, productName) {
        console.log('Adding to cart:', productId, productName);
        
        // Show loading state
        const button = document.querySelector(`[data-product-id="${productId}"] .add-to-bag`);
        let originalText = '';
        if (button) {
            originalText = button.textContent;
            button.textContent = 'Adding...';
            button.disabled = true;
        }
        
        // Show immediate feedback notification
        showNotification(`Adding ${productName} to cart...`, 'info');
        
        // Make API call to add to cart
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add_to_cart&product_id=${productId}&quantity=1&return_url=${encodeURIComponent(window.location.href)}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Cart API response:', data);
            
            if (data.success) {
                // Update cart count in header
                if (typeof updateCartCount === 'function') {
                    updateCartCount(data.cart_count);
                }
                
                // Show success notification
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
    
    // Improved notification function
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
                const href = subcategoryItem.getAttribute('href');
                
                console.log('Subcategory clicked:', category, subcategory);
                
                // Navigate to the section if href is provided
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetSection = document.querySelector(href);
                    if (targetSection) {
                        // Close all modals
                        categoryModals.forEach(modal => {
                            modal.style.opacity = '0';
                            modal.style.visibility = 'hidden';
                        });
                        
                        // Scroll to target section
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        });
        
        // Handle modal triggers
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetModal = document.querySelector(this.getAttribute('data-modal'));
                if (targetModal) {
                    // Close all other modals
                    categoryModals.forEach(modal => {
                        if (modal !== targetModal) {
                            modal.style.opacity = '0';
                            modal.style.visibility = 'hidden';
                        }
                    });
                    
                    // Toggle target modal
                    if (targetModal.style.opacity === '1') {
                        targetModal.style.opacity = '0';
                        targetModal.style.visibility = 'hidden';
                    } else {
                        targetModal.style.opacity = '1';
                        targetModal.style.visibility = 'visible';
                    }
                }
            });
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.modal-trigger') && !e.target.closest('.category-modal')) {
                categoryModals.forEach(modal => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                });
            }
        });
    }
    
    // Header Modal Functionality
    function initializeHeaderModals() {
        const headerModals = document.querySelectorAll('.header-modal');
        const headerTriggers = document.querySelectorAll('.header-trigger');
        
        headerTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetModal = document.querySelector(this.getAttribute('data-modal'));
                if (targetModal) {
                    // Close all other header modals
                    headerModals.forEach(modal => {
                        if (modal !== targetModal) {
                            modal.style.display = 'none';
                        }
                    });
                    
                    // Toggle target modal
                    if (targetModal.style.display === 'block') {
                        targetModal.style.display = 'none';
                    } else {
                        targetModal.style.display = 'block';
                    }
                }
            });
        });
        
        // Close header modals when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-trigger') && !e.target.closest('.header-modal')) {
                headerModals.forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    }
    
    // Filter Functionality
    function initializeFilters() {
        // Size filter functionality
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sizeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSizeCount();
                applyFilters();
            });
        });
        
        // Color filter functionality
        const colorCheckboxes = document.querySelectorAll('#color-filter input[type="checkbox"]');
        colorCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateColorCount();
                applyFilters();
            });
        });
        
        // Price filter functionality
        const priceInputs = document.querySelectorAll('#price-filter input[type="range"]');
        priceInputs.forEach(input => {
            input.addEventListener('input', function() {
                updatePriceDisplay();
                applyFilters();
            });
        });
        
        // Category filter functionality
        const categoryCheckboxes = document.querySelectorAll('#category-filter input[type="checkbox"]');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCategoryCount();
                applyFilters();
            });
        });
    }
    
    // Filter helper functions
    function updateSizeCount() {
        const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]:checked');
        const sidebarSizeCountElement = document.getElementById('size-count');
        if (sidebarSizeCountElement) {
            const count = sidebarSizeCheckboxes.length;
            sidebarSizeCountElement.textContent = count > 0 ? `(${count} selected)` : '';
        }
    }
    
    function updateColorCount() {
        const sidebarColorCheckboxes = document.querySelectorAll('#color-filter input[type="checkbox"]:checked');
        const sidebarColorCountElement = document.getElementById('color-count');
        if (sidebarColorCountElement) {
            const count = sidebarColorCheckboxes.length;
            sidebarColorCountElement.textContent = count > 0 ? `(${count} selected)` : '';
        }
    }
    
    function updatePriceDisplay() {
        const minPriceInput = document.getElementById('min-price');
        const maxPriceInput = document.getElementById('max-price');
        const priceDisplay = document.getElementById('price-display');
        
        if (minPriceInput && maxPriceInput && priceDisplay) {
            const minPrice = minPriceInput.value;
            const maxPrice = maxPriceInput.value;
            priceDisplay.textContent = `$${minPrice} - $${maxPrice}`;
        }
    }
    
    function updateCategoryCount() {
        const sidebarCategoryCheckboxes = document.querySelectorAll('#category-filter input[type="checkbox"]:checked');
        const sidebarCategoryCountElement = document.getElementById('category-count');
        if (sidebarCategoryCountElement) {
            const count = sidebarCategoryCheckboxes.length;
            sidebarCategoryCountElement.textContent = count > 0 ? `(${count} selected)` : '';
        }
    }
    
    // Apply filters function
    function applyFilters() {
        const productCards = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        productCards.forEach(card => {
            let shouldShow = true;
            
            // Size filter
            const selectedSizes = Array.from(document.querySelectorAll('#size-filter input[type="checkbox"]:checked'))
                .map(cb => cb.value);
            if (selectedSizes.length > 0) {
                const productSizes = card.dataset.productSizes;
                if (productSizes) {
                    try {
                        const sizes = JSON.parse(productSizes);
                        const hasMatchingSize = selectedSizes.some(size => sizes.includes(size));
                        if (!hasMatchingSize) shouldShow = false;
                    } catch (e) {
                        console.log('Error parsing product sizes:', e);
                    }
                }
            }
            
            // Color filter
            const selectedColors = Array.from(document.querySelectorAll('#color-filter input[type="checkbox"]:checked'))
                .map(cb => cb.value);
            if (selectedColors.length > 0) {
                const productColor = card.dataset.productColor;
                if (productColor && !selectedColors.includes(productColor)) {
                    shouldShow = false;
                }
            }
            
            // Price filter
            const minPrice = parseFloat(document.getElementById('min-price')?.value || 0);
            const maxPrice = parseFloat(document.getElementById('max-price')?.value || 1000);
            const productPrice = parseFloat(card.dataset.productPrice || 0);
            if (productPrice < minPrice || productPrice > maxPrice) {
                shouldShow = false;
            }
            
            // Category filter
            const selectedCategories = Array.from(document.querySelectorAll('#category-filter input[type="checkbox"]:checked'))
                .map(cb => cb.value);
            if (selectedCategories.length > 0) {
                const productCategory = card.dataset.productCategory;
                if (productCategory && !selectedCategories.includes(productCategory)) {
                    shouldShow = false;
                }
            }
            
            // Show/hide card
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update results count
        const resultsCount = document.getElementById('results-count');
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} products found`;
        }
        
        console.log(`Filter applied: ${visibleCount} products visible`);
    }
    
    // Global functions
    window.selectAllSizes = function() {
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sizeCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSizeCount();
        applyFilters();
    };
    
    window.clearSizeFilters = function() {
        const sizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sizeCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSizeCount();
        applyFilters();
    };
    
    window.selectAllColors = function() {
        const colorCheckboxes = document.querySelectorAll('#color-filter input[type="checkbox"]');
        colorCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateColorCount();
        applyFilters();
    };
    
    window.clearColorFilters = function() {
        const colorCheckboxes = document.querySelectorAll('#color-filter input[type="checkbox"]');
        colorCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateColorCount();
        applyFilters();
    };
    
    window.selectAllCategories = function() {
        const categoryCheckboxes = document.querySelectorAll('#category-filter input[type="checkbox"]');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateCategoryCount();
        applyFilters();
    };
    
    window.clearCategoryFilters = function() {
        const categoryCheckboxes = document.querySelectorAll('#category-filter input[type="checkbox"]');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateCategoryCount();
        applyFilters();
    };
    
    window.clearAllFilters = function() {
        clearSizeFilters();
        clearColorFilters();
        clearCategoryFilters();
        
        // Reset price range
        const minPriceInput = document.getElementById('min-price');
        const maxPriceInput = document.getElementById('max-price');
        if (minPriceInput) minPriceInput.value = 0;
        if (maxPriceInput) maxPriceInput.value = 1000;
        updatePriceDisplay();
        
        applyFilters();
    };
    
    // Make functions globally accessible
    window.updateSizeCount = updateSizeCount;
    window.updateColorCount = updateColorCount;
    window.updatePriceDisplay = updatePriceDisplay;
    window.updateCategoryCount = updateCategoryCount;
    window.applyFilters = applyFilters;
    window.showNotification = showNotification;
});
