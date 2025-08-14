// Simple Quick View Implementation
console.log('🚀 Quick View Simple loaded!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded - initializing quick view...');
    
    // Get all quick view buttons
    const quickViewButtons = document.querySelectorAll('.quick-view');
    const quickViewSidebar = document.getElementById('quick-view-sidebar');
    const quickViewOverlay = document.getElementById('quick-view-overlay');
    const closeButton = document.getElementById('close-quick-view');
    
    console.log('Found quick view buttons:', quickViewButtons.length);
    console.log('Quick view sidebar:', quickViewSidebar);
    console.log('Quick view overlay:', quickViewOverlay);
    
    // Add click handlers to all quick view buttons
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            console.log('Quick view clicked for product:', productId);
            
            if (productId) {
                openQuickView(productId);
            }
        });
    });
    
    // Close quick view handlers
    if (closeButton) {
        closeButton.addEventListener('click', closeQuickView);
    }
    
    if (quickViewOverlay) {
        quickViewOverlay.addEventListener('click', closeQuickView);
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && quickViewSidebar && quickViewSidebar.classList.contains('active')) {
            closeQuickView();
        }
    });
    
    function openQuickView(productId) {
        console.log('Opening quick view for product:', productId);
        
        // Find the product card
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) {
            console.error('Product card not found for ID:', productId);
            return;
        }
        
        // Extract product data from the card
        const productData = extractProductData(productCard, productId);
        console.log('Extracted product data:', productData);
        
        // Show the sidebar
        if (quickViewSidebar) {
            quickViewSidebar.classList.add('active');
        }
        if (quickViewOverlay) {
            quickViewOverlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
        
        // Populate the quick view
        populateQuickView(productData);
    }
    
    function extractProductData(productCard, productId) {
        // Get basic product info
        const productName = productCard.querySelector('.product-name')?.textContent || 'Product';
        const productPrice = productCard.querySelector('.product-price')?.textContent || '$0';
        const productAvailability = productCard.querySelector('.product-availability')?.textContent || '';
        
        // Get images
        const images = [];
        const imageElements = productCard.querySelectorAll('.image-slider img');
        imageElements.forEach(img => {
            const color = img.getAttribute('data-color') || '#000000';
            const type = img.alt.includes('Front') ? 'front' : 'back';
            images.push({
                src: img.src,
                color: color,
                type: type,
                alt: img.alt
            });
        });
        
        // Get colors
        const colors = [];
        const colorElements = productCard.querySelectorAll('.color-circle');
        colorElements.forEach((colorEl, index) => {
            const colorValue = colorEl.getAttribute('data-color') || '#000000';
            const colorName = colorEl.title || `Color ${index + 1}`;
            colors.push({
                name: colorName,
                value: colorValue,
                hex: colorValue
            });
        });
        
        return {
            id: productId,
            name: productName,
            price: productPrice,
            availability: productAvailability,
            images: images,
            colors: colors,
            sizes: ['XS', 'S', 'M', 'L', 'XL']
        };
    }
    
    function populateQuickView(productData) {
        console.log('Populating quick view with:', productData);
        
        // Set title and price
        const titleElement = document.getElementById('quick-view-title');
        const priceElement = document.getElementById('quick-view-price');
        
        if (titleElement) titleElement.textContent = productData.name;
        if (priceElement) priceElement.textContent = productData.price;
        
        // Set main image
        const mainImage = document.getElementById('quick-view-main-image');
        if (mainImage && productData.images.length > 0) {
            mainImage.src = productData.images[0].src;
            mainImage.alt = productData.images[0].alt || productData.name;
        }
        
        // Populate thumbnails
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        if (thumbnailsContainer) {
            thumbnailsContainer.innerHTML = '';
            
            productData.images.forEach((image, index) => {
                const thumbnail = document.createElement('div');
                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                thumbnail.innerHTML = `<img src="${image.src}" alt="${image.alt}" data-index="${index}">`;
                
                thumbnail.addEventListener('click', () => {
                    // Update main image
                    if (mainImage) {
                        mainImage.src = image.src;
                        mainImage.alt = image.alt;
                    }
                    
                    // Update active thumbnail
                    thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                    thumbnail.classList.add('active');
                });
                
                thumbnailsContainer.appendChild(thumbnail);
            });
        }
        
        // Populate colors
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (colorSelection) {
            colorSelection.innerHTML = '';
            
            productData.colors.forEach((color, index) => {
                const colorCircle = document.createElement('div');
                colorCircle.className = `quick-view-color-circle ${index === 0 ? 'active' : ''}`;
                colorCircle.style.backgroundColor = color.hex;
                colorCircle.setAttribute('data-color', color.value);
                colorCircle.title = color.name;
                
                colorCircle.addEventListener('click', () => {
                    // Update active color
                    colorSelection.querySelectorAll('.quick-view-color-circle').forEach(c => c.classList.remove('active'));
                    colorCircle.classList.add('active');
                    
                    // Filter images by color
                    const selectedColor = color.value;
                    const colorImages = productData.images.filter(img => img.color === selectedColor);
                    
                    if (colorImages.length > 0 && mainImage) {
                        // Update main image
                        mainImage.src = colorImages[0].src;
                        mainImage.alt = colorImages[0].alt;
                        
                        // Update thumbnails
                        if (thumbnailsContainer) {
                            thumbnailsContainer.innerHTML = '';
                            colorImages.forEach((image, imgIndex) => {
                                const thumbnail = document.createElement('div');
                                thumbnail.className = `thumbnail-item ${imgIndex === 0 ? 'active' : ''}`;
                                thumbnail.innerHTML = `<img src="${image.src}" alt="${image.alt}" data-index="${imgIndex}">`;
                                
                                thumbnail.addEventListener('click', () => {
                                    if (mainImage) {
                                        mainImage.src = image.src;
                                        mainImage.alt = image.alt;
                                    }
                                    thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                    thumbnail.classList.add('active');
                                });
                                
                                thumbnailsContainer.appendChild(thumbnail);
                            });
                        }
                    }
                });
                
                colorSelection.appendChild(colorCircle);
            });
        }
        
        // Populate sizes
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            sizeSelection.innerHTML = '';
            
            productData.sizes.forEach(size => {
                const sizeBtn = document.createElement('button');
                sizeBtn.className = 'quick-view-size-btn';
                sizeBtn.textContent = size;
                
                sizeBtn.addEventListener('click', () => {
                    sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                    sizeBtn.classList.add('active');
                });
                
                sizeSelection.appendChild(sizeBtn);
            });
        }
        
        // Set availability
        const availabilityElement = document.getElementById('quick-view-availability');
        if (availabilityElement) {
            if (productData.availability.includes('SOLD OUT')) {
                availabilityElement.innerHTML = '<span style="color: #f44336;">✗ Out of Stock</span>';
            } else if (productData.availability.includes('Only')) {
                availabilityElement.innerHTML = `<span style="color: #FF9800;">⚠ ${productData.availability}</span>`;
            } else {
                availabilityElement.innerHTML = '<span style="color: #4CAF50;">✓ In Stock</span>';
            }
        }
        
        // Add to bag functionality
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        if (addToBagBtn) {
            addToBagBtn.addEventListener('click', function() {
                const selectedSize = document.querySelector('.quick-view-size-btn.active');
                if (!selectedSize) {
                    alert('Please select a size');
                    return;
                }
                
                const selectedColor = document.querySelector('.quick-view-color-circle.active');
                const colorName = selectedColor ? selectedColor.title : '';
                
                console.log(`Added to cart: ${productData.name} - Size: ${selectedSize.textContent}, Color: ${colorName}`);
                alert(`Added to cart: ${productData.name}`);
            });
        }
        
        // Add to wishlist functionality
        const addToWishlistBtn = document.getElementById('add-to-wishlist-quick');
        if (addToWishlistBtn) {
            addToWishlistBtn.addEventListener('click', function() {
                console.log(`Added to wishlist: ${productData.name}`);
                alert(`Added to wishlist: ${productData.name}`);
            });
        }
    }
    
    function closeQuickView() {
        console.log('Closing quick view');
        
        if (quickViewSidebar) {
            quickViewSidebar.classList.remove('active');
        }
        if (quickViewOverlay) {
            quickViewOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }
    
    console.log('✅ Quick view initialization complete!');
});
