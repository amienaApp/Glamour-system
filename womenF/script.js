// Simple Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Quick View
    const quickViewButtons = document.querySelectorAll('.quick-view');
    const sidebar = document.getElementById('quick-view-sidebar');
    const overlay = document.getElementById('quick-view-overlay');
    const closeBtn = document.getElementById('close-quick-view');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            openQuickView(productId);
        });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeQuickView);
    if (overlay) overlay.addEventListener('click', closeQuickView);

    function openQuickView(productId) {
        console.log('Opening quick view for:', productId);
        
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) return;
        
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        
        if (titleEl) titleEl.textContent = name;
        if (priceEl) priceEl.textContent = price;
        
        // Handle images
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        
        if (mainImage && thumbnailsContainer) {
            // Get all images from the product card
            const images = productCard.querySelectorAll('.image-slider img');
            console.log('Found images:', images.length);
            
            if (images.length > 0) {
        // Set main image
                mainImage.src = images[0].src;
                mainImage.alt = images[0].alt || name;

                // Clear and populate thumbnails
        thumbnailsContainer.innerHTML = '';
        
                images.forEach((img, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                    thumbnail.innerHTML = `<img src="${img.src}" alt="${img.alt}" data-index="${index}">`;
            
            thumbnail.addEventListener('click', () => {
                // Update main image
                        mainImage.src = img.src;
                        mainImage.alt = img.alt;
                
                // Update active thumbnail
                            thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                            thumbnail.classList.add('active');
                        });
                        
                        thumbnailsContainer.appendChild(thumbnail);
                    });
                }
        }
        
        // Handle color selection
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (colorSelection) {
            colorSelection.innerHTML = '';
            
            // Get all images with color data
            const colorImages = productCard.querySelectorAll('.image-slider img[data-color]');
            const colors = new Set();
            
            colorImages.forEach(img => {
                const color = img.getAttribute('data-color');
                if (color) colors.add(color);
            });
            
            if (colors.size > 0) {
                colors.forEach(color => {
                    const colorBtn = document.createElement('div');
                    colorBtn.className = 'color-option';
                    colorBtn.style.cssText = `
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        background-color: ${color};
                        border: 2px solid #ddd;
                        cursor: pointer;
                        margin: 0 5px;
                        display: inline-block;
                        transition: all 0.3s ease;
                    `;
                    colorBtn.title = color;
                    
                    // Add click handler
                    colorBtn.addEventListener('click', function() {
                        // Remove active class from all color options
                        colorSelection.querySelectorAll('.color-option').forEach(opt => {
                            opt.style.border = '2px solid #ddd';
                        });
                        
                        // Add active class to clicked option
                        this.style.border = '2px solid #333';
                        
                        // Show images for this color
                        const imagesForColor = productCard.querySelectorAll(`.image-slider img[data-color="${color}"]`);
                        if (imagesForColor.length > 0) {
                            // Update main image
                            mainImage.src = imagesForColor[0].src;
                            mainImage.alt = imagesForColor[0].alt;
                            
                            // Update thumbnails
                            thumbnailsContainer.innerHTML = '';
                            imagesForColor.forEach((img, index) => {
                                const thumbnail = document.createElement('div');
                                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                                thumbnail.innerHTML = `<img src="${img.src}" alt="${img.alt}" data-index="${index}">`;
                                
                                thumbnail.addEventListener('click', () => {
                                    mainImage.src = img.src;
                                    mainImage.alt = img.alt;
                                    thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                    thumbnail.classList.add('active');
                                });
                                
                                thumbnailsContainer.appendChild(thumbnail);
                            });
                        }
                    });
                    
                    colorSelection.appendChild(colorBtn);
                });
                
                // Set first color as active
                const firstColor = colorSelection.querySelector('.color-option');
                if (firstColor) {
                    firstColor.style.border = '2px solid #333';
                }
            }
        }
        
        // Handle size selection
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            sizeSelection.innerHTML = '';
            
            // Default sizes - you can customize these based on your product data
            const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
            
            sizes.forEach(size => {
                const sizeBtn = document.createElement('div');
                sizeBtn.className = 'size-option';
                sizeBtn.textContent = size;
                sizeBtn.style.cssText = `
                    padding: 8px 12px;
                    border: 2px solid #ddd;
                    border-radius: 4px;
                    cursor: pointer;
                    margin: 0 5px 5px 0;
                    display: inline-block;
                    transition: all 0.3s ease;
                    font-size: 14px;
                    font-weight: 500;
                `;
                
                // Add click handler
                sizeBtn.addEventListener('click', function() {
                    // Remove active class from all size options
                    sizeSelection.querySelectorAll('.size-option').forEach(opt => {
                        opt.style.border = '2px solid #ddd';
                        opt.style.backgroundColor = 'transparent';
                        opt.style.color = '#333';
                    });
                    
                    // Add active class to clicked option
                    this.style.border = '2px solid #333';
                    this.style.backgroundColor = '#333';
                    this.style.color = 'white';
                });
                
                sizeSelection.appendChild(sizeBtn);
            });
        }
        
        // Handle availability and add to bag button
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        const availabilityElement = document.getElementById('quick-view-availability');
        
        if (addToBagBtn && availabilityElement) {
            const availability = productCard.querySelector('.product-availability')?.textContent || '';
            
            if (availability.includes('SOLD OUT')) {
                // Product is sold out
                addToBagBtn.disabled = true;
                addToBagBtn.textContent = 'Sold Out';
                addToBagBtn.style.opacity = '0.5';
                addToBagBtn.style.cursor = 'not-allowed';
                availabilityElement.innerHTML = '<span style="color: #f44336; font-weight: 600;">✗ Out of Stock</span>';
            } else if (availability.includes('Only')) {
                // Limited stock
                addToBagBtn.disabled = false;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Bag';
                addToBagBtn.style.opacity = '1';
                addToBagBtn.style.cursor = 'pointer';
                availabilityElement.innerHTML = `<span style="color: #FF9800; font-weight: 600;">⚠ ${availability}</span>`;
            } else {
                // In stock
                addToBagBtn.disabled = false;
                addToBagBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Bag';
                addToBagBtn.style.opacity = '1';
                addToBagBtn.style.cursor = 'pointer';
                availabilityElement.innerHTML = '<span style="color: #4CAF50; font-weight: 600;">✓ In Stock</span>';
            }
        }
        
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
