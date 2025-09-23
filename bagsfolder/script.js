// Unified Bags Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bags script loaded successfully');
    
    // Initialize all functionality
    initializeCategoryModals();
    initializeHeaderModals();
    initializeFilters();
    initializeQuickView();
    
    // Global variables to track selected variants in quick view
    let selectedQuickViewColor = '';
    let selectedQuickViewSize = '';

    function initializeQuickView() {
        const sidebar = document.getElementById('quick-view-sidebar');
        const overlay = document.getElementById('quick-view-overlay');
        const closeBtn = document.getElementById('close-quick-view');
        
        console.log('Quick view elements found:', {
            sidebar: !!sidebar,
            overlay: !!overlay,
            closeBtn: !!closeBtn
        }); 
    
    // Quick View button click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
            e.preventDefault();
            // console.log('Quick view button clicked!');
            const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
            const productId = button.getAttribute('data-product-id');
            // console.log('Product ID:', productId);
            if (productId) {
                openQuickView(productId);
            } else {
                console.error('No product ID found');
            }
        }
    });
    
    // Close quick view
    if (closeBtn) closeBtn.addEventListener('click', closeQuickView);
    if (overlay) overlay.addEventListener('click', closeQuickView);
    
    // Global variables to track selected variants in quick view
    let selectedQuickViewColor = '';
    let selectedQuickViewSize = '';

    function openQuickView(productId) {
        // console.log('Opening quick view for:', productId);
        
        try {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (!productCard) {
                console.error('Product card not found for ID:', productId);
                return;
            }
            
            // console.log('Product card found:', productCard);
        
        const name = productCard.querySelector('.product-name')?.textContent || 'Product';
        const price = productCard.querySelector('.product-price')?.textContent || '$0';
        
        // Update quick view content
        const titleEl = document.getElementById('quick-view-title');
        const priceEl = document.getElementById('quick-view-price');
        const addToBagBtn = document.getElementById('add-to-bag-quick');
        
        if (titleEl) titleEl.textContent = name;
        if (priceEl) priceEl.textContent = price;
        if (addToBagBtn) addToBagBtn.setAttribute('data-product-id', productId);
        
        // Handle images
        const mainImage = document.getElementById('quick-view-main-image');
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        
        if (mainImage && thumbnailsContainer) {
            // Get all media from the product card (both images and videos)
            const mediaElements = productCard.querySelectorAll('.image-slider img, .image-slider video');
            
            if (mediaElements.length > 0) {
                const firstMedia = mediaElements[0];
                const isVideo = firstMedia.tagName.toLowerCase() === 'video';
                
                // Set main media
                if (isVideo) {
                    mainImage.style.display = 'none';
                    const mainVideo = document.getElementById('quick-view-main-video');
                    if (mainVideo) {
                        mainVideo.src = firstMedia.src;
                        mainVideo.style.display = 'block';
                        mainVideo.muted = true;
                        mainVideo.loop = true;
                        mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                    }
                } else {
                    mainImage.src = firstMedia.src;
                    mainImage.alt = firstMedia.alt || name;
                    mainImage.style.display = 'block';
                    const mainVideo = document.getElementById('quick-view-main-video');
                    if (mainVideo) mainVideo.style.display = 'none';
                }
                
                // Clear and populate thumbnails
                thumbnailsContainer.innerHTML = '';
                
                mediaElements.forEach((media, index) => {
                    const thumbnail = document.createElement('div');
                    thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                    const isThumbnailVideo = media.tagName.toLowerCase() === 'video';
                    
                    if (isThumbnailVideo) {
                        const thumbnailVideo = document.createElement('video');
                        thumbnailVideo.src = media.src;
                        thumbnailVideo.muted = true;
                        thumbnailVideo.setAttribute('data-index', index);
                        thumbnail.appendChild(thumbnailVideo);
                    } else {
                        const thumbnailImg = document.createElement('img');
                        thumbnailImg.src = media.src;
                        thumbnailImg.alt = media.alt;
                        thumbnailImg.setAttribute('data-index', index);
                        thumbnail.appendChild(thumbnailImg);
                    }
                    
                    thumbnail.addEventListener('click', () => {
                        if (isThumbnailVideo) {
                            // Show video
                            mainImage.style.display = 'none';
                            const mainVideo = document.getElementById('quick-view-main-video');
                            if (mainVideo) {
                                mainVideo.src = media.src;
                                mainVideo.style.display = 'block';
                                mainVideo.muted = true;
                                mainVideo.loop = true;
                                mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                            }
                        } else {
                            // Show image
                            mainImage.src = media.src;
                            mainImage.alt = media.alt;
                            mainImage.style.display = 'block';
                            const mainVideo = document.getElementById('quick-view-main-video');
                            if (mainVideo) mainVideo.style.display = 'none';
                        }
                        
                        thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    });
                    
                    thumbnailsContainer.appendChild(thumbnail);
                });
            }
        }
        
        // Handle color circles in quick view
        const colorSelection = document.getElementById('quick-view-color-selection');
        if (colorSelection) {
            colorSelection.innerHTML = '';
            
            // Get all color circles from the product card
            const colorCircles = productCard.querySelectorAll('.color-circle');
            
            colorCircles.forEach(circle => {
                const color = circle.getAttribute('data-color');
                const title = circle.getAttribute('title');
                const isActive = circle.classList.contains('active');
                
                if (color) {
                    const colorBtn = document.createElement('div');
                    colorBtn.className = 'quick-view-color-circle';
                    colorBtn.style.cssText = `
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        background-color: ${color};
                        border: 2px solid ${isActive ? '#000' : '#ddd'};
                        cursor: pointer;
                        margin: 0 5px;
                        display: inline-block;
                        transition: all 0.3s ease;
                        position: relative;
                        z-index: 10;
                    `;
                    colorBtn.title = title || color;
                    
                    if (isActive) {
                        colorBtn.classList.add('active');
                        selectedQuickViewColor = color; // Set initial selected color
                    }
                    
                    // Add click handler for quick view color circles
                    colorBtn.addEventListener('click', function() {
                        // Remove active class from all color options
                        colorSelection.querySelectorAll('.quick-view-color-circle').forEach(opt => {
                            opt.classList.remove('active');
                            opt.style.border = '2px solid #ddd';
                        });
                        
                        // Add active class to clicked option
                        this.classList.add('active');
                        this.style.border = '2px solid #000';
                        
                        // Store selected color
                        selectedQuickViewColor = color;
                        
                        // Update media for this color
                        const mediaForColor = productCard.querySelectorAll(`.image-slider img[data-color="${color}"], .image-slider video[data-color="${color}"]`);
                        
                        if (mediaForColor.length > 0 && mainImage && thumbnailsContainer) {
                            const firstMedia = mediaForColor[0];
                            const isVideo = firstMedia.tagName.toLowerCase() === 'video';
                            
                            // Update main media
                            if (isVideo) {
                                mainImage.style.display = 'none';
                                const mainVideo = document.getElementById('quick-view-main-video');
                                if (mainVideo) {
                                    mainVideo.src = firstMedia.src;
                                    mainVideo.style.display = 'block';
                                    mainVideo.muted = true;
                                    mainVideo.loop = true;
                                    mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                                }
                            } else {
                                mainImage.src = firstMedia.src;
                                mainImage.alt = firstMedia.alt || name;
                                mainImage.style.display = 'block';
                                const mainVideo = document.getElementById('quick-view-main-video');
                                if (mainVideo) mainVideo.style.display = 'none';
                            }
                            
                            // Update thumbnails
                            thumbnailsContainer.innerHTML = '';
                            mediaForColor.forEach((media, index) => {
                                const thumbnail = document.createElement('div');
                                thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                                const isThumbnailVideo = media.tagName.toLowerCase() === 'video';
                                
                                if (isThumbnailVideo) {
                                    const thumbnailVideo = document.createElement('video');
                                    thumbnailVideo.src = media.src;
                                    thumbnailVideo.muted = true;
                                    thumbnailVideo.setAttribute('data-index', index);
                                    thumbnail.appendChild(thumbnailVideo);
                                } else {
                                    const thumbnailImg = document.createElement('img');
                                    thumbnailImg.src = media.src;
                                    thumbnailImg.alt = media.alt;
                                    thumbnailImg.setAttribute('data-index', index);
                                    thumbnail.appendChild(thumbnailImg);
                                }
                                
                                thumbnail.addEventListener('click', () => {
                                    if (isThumbnailVideo) {
                                        // Show video
                                        mainImage.style.display = 'none';
                                        const mainVideo = document.getElementById('quick-view-main-video');
                                        if (mainVideo) {
                                            mainVideo.src = media.src;
                                            mainVideo.style.display = 'block';
                                            mainVideo.muted = true;
                                            mainVideo.loop = true;
                                            mainVideo.play().catch(e => console.log('Video autoplay prevented:', e));
                                        }
                                    } else {
                                        // Show image
                                        mainImage.src = media.src;
                                        mainImage.alt = media.alt;
                                        mainImage.style.display = 'block';
                                        const mainVideo = document.getElementById('quick-view-main-video');
                                        if (mainVideo) mainVideo.style.display = 'none';
                                    }
                                    
                                    thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                    thumbnail.classList.add('active');
                                });
                                
                                thumbnailsContainer.appendChild(thumbnail);
                            });
                        }
                    });
                    
                    colorSelection.appendChild(colorBtn);
                }
            });
        }
        
        // Handle size selection in quick view - Enhanced for Multiple Forms
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
            // console.log('Size selection element found, processing sizes...');
            
            try {
            sizeSelection.innerHTML = '';
            
                // Helper function to clean JSON strings
                function cleanJsonString(jsonString) {
                    if (!jsonString || jsonString === '[]' || jsonString === 'null') {
                        return null;
                    }
                    
                    let cleaned = jsonString;
                    // Remove outer quotes if present
                    if (cleaned.startsWith('"') && cleaned.endsWith('"')) {
                        cleaned = cleaned.slice(1, -1);
                    }
                    // Unescape quotes
                    cleaned = cleaned.replace(/\\"/g, '"');
                    return cleaned;
                }
                
                // Get actual available sizes from product data
                let sizes = [];
                
                // Try to get sizes from product data - check all possible sources
                const productSizes = productCard.dataset.productSizes;
                const productSelectedSizes = productCard.dataset.productSelectedSizes;
                const productVariants = productCard.dataset.productVariants;
                const productProductVariants = productCard.dataset.productProductVariants;
                const productOptions = productCard.dataset.productOptions;
                const productProductOptions = productCard.dataset.productProductOptions;
            
            /* console.log('Raw product size data:', {
                productSizes: productSizes,
                productSelectedSizes: productSelectedSizes,
                productVariants: productVariants,
                productProductVariants: productProductVariants,
                productOptions: productOptions,
                productProductOptions: productProductOptions
            }); */
            
            // Check multiple possible size data sources in order of preference
            if (productSizes && productSizes !== '[]' && productSizes !== 'null') {
                try {
                    const cleanSizes = cleanJsonString(productSizes);
                    if (cleanSizes) {
                        // console.log('Cleaned productSizes:', cleanSizes);
                        const parsedSizes = JSON.parse(cleanSizes);
                        if (Array.isArray(parsedSizes) && parsedSizes.length > 0) {
                            sizes = parsedSizes.filter(size => size && size.trim() !== '');
                            // console.log('Found sizes in productSizes:', sizes);
                        }
                    }
                } catch (e) {
                    // console.log('Could not parse productSizes:', e);
                    // console.log('Raw productSizes:', productSizes);
                }
            }
            
            // If no sizes from productSizes, try selectedSizes
            if (sizes.length === 0 && productSelectedSizes && productSelectedSizes !== '[]' && productSelectedSizes !== 'null') {
                try {
                    const cleanSelectedSizes = cleanJsonString(productSelectedSizes);
                    if (cleanSelectedSizes) {
                        // console.log('Cleaned productSelectedSizes:', cleanSelectedSizes);
                        const parsedSelectedSizes = JSON.parse(cleanSelectedSizes);
                        if (Array.isArray(parsedSelectedSizes) && parsedSelectedSizes.length > 0) {
                            sizes = parsedSelectedSizes.filter(size => size && size.trim() !== '');
                            // console.log('Found sizes in productSelectedSizes:', sizes);
                        }
                    }
                } catch (e) {
                    // console.log('Could not parse productSelectedSizes:', e);
                    // console.log('Raw productSelectedSizes:', productSelectedSizes);
                }
            }
            
            // If still no sizes, try to extract from variants
            if (sizes.length === 0 && (productVariants || productProductVariants)) {
                try {
                    const variantsData = productVariants || productProductVariants;
                    if (variantsData && variantsData !== '[]' && variantsData !== 'null') {
                        const parsedVariants = JSON.parse(variantsData);
                        if (Array.isArray(parsedVariants)) {
                            const variantSizes = [];
                            parsedVariants.forEach(variant => {
                                if (variant && variant.size && variant.size.trim() !== '' && !variantSizes.includes(variant.size)) {
                                    variantSizes.push(variant.size);
                                }
                            });
                            if (variantSizes.length > 0) {
                                sizes = variantSizes;
                                // console.log('Found sizes in variants:', sizes);
                            }
                        }
                    }
                } catch (e) {
                    // console.log('Could not parse variants for sizes:', e);
                }
            }
            
            // If still no sizes, try to extract from options
            if (sizes.length === 0 && (productOptions || productProductOptions)) {
                try {
                    const optionsData = productOptions || productProductOptions;
                    if (optionsData && optionsData !== '[]' && optionsData !== 'null') {
                        const parsedOptions = JSON.parse(optionsData);
                        if (Array.isArray(parsedOptions)) {
                            const optionSizes = [];
                            parsedOptions.forEach(option => {
                                if (option && option.size && option.size.trim() !== '' && !optionSizes.includes(option.size)) {
                                    optionSizes.push(option.size);
                                }
                            });
                            if (optionSizes.length > 0) {
                                sizes = optionSizes;
                                // console.log('Found sizes in options:', sizes);
                            }
                        }
                    }
                } catch (e) {
                    // console.log('Could not parse options for sizes:', e);
                }
            }
            
            // If still no sizes, hide the size section
            if (sizes.length === 0) {
                console.log('No sizes found for this product - hiding size section');
                const sizeSection = document.querySelector('.size-section');
                if (sizeSection) {
                    sizeSection.style.display = 'none';
                }
                return; // Exit early, don't show size selection
            }
            
            // Check if this is a one-size item
            if (sizes.length === 1 && (sizes[0] === 'One Size' || sizes[0] === 'One-Size')) {
                // Hide the entire size section for one-size items
                const sizeSection = document.querySelector('.size-section');
                if (sizeSection) {
                    sizeSection.style.display = 'none';
                }
                return;
            }
            
            // Show size section for items with multiple sizes
            const sizeSection = document.querySelector('.size-section');
            if (sizeSection) {
                sizeSection.style.display = 'block';
            }
            
            // console.log('Available sizes for product:', sizes);
            /* console.log('Product size data sources:', {
                productSizes: productSizes,
                productSelectedSizes: productSelectedSizes,
                productVariants: productVariants,
                productProductVariants: productProductVariants
            }); */
            
            // Function to convert size codes to full names
            function getSizeName(sizeCode) {
                const sizeMap = {
                    'XXS': 'Extra Extra Small',
                    'XS': 'Extra Small', 
                    'S': 'Small',
                    'M': 'Medium',
                    'L': 'Large',
                    'XL': 'Extra Large',
                    'XXL': 'Extra Extra Large',
                    'XXXL': 'Extra Extra Extra Large',
                    '0': 'Size 0',
                    '2': 'Size 2',
                    '4': 'Size 4',
                    '6': 'Size 6',
                    '8': 'Size 8',
                    '10': 'Size 10',
                    '12': 'Size 12',
                    '14': 'Size 14',
                    '16': 'Size 16',
                    '18': 'Size 18',
                    '20': 'Size 20',
                    '22': 'Size 22',
                    '24': 'Size 24',
                    '26': 'Size 26',
                    '28': 'Size 28',
                    '30': 'Size 30',
                    '32': 'Size 32',
                    '34': 'Size 34',
                    '36': 'Size 36',
                    '38': 'Size 38',
                    '40': 'Size 40',
                    '42': 'Size 42',
                    '44': 'Size 44',
                    '46': 'Size 46',
                    '48': 'Size 48',
                    '50': 'Size 50',
                    '52': 'Size 52',
                    '54': 'Size 54',
                    '56': 'Size 56',
                    '58': 'Size 58',
                    '60': 'Size 60',
                    '62': 'Size 62',
                    '64': 'Size 64',
                    '66': 'Size 66',
                    '68': 'Size 68',
                    '70': 'Size 70',
                    '72': 'Size 72',
                    '74': 'Size 74',
                    '76': 'Size 76',
                    '78': 'Size 78',
                    '80': 'Size 80',
                    '82': 'Size 82',
                    '84': 'Size 84',
                    '86': 'Size 86',
                    '88': 'Size 88',
                    '90': 'Size 90',
                    '92': 'Size 92',
                    '94': 'Size 94',
                    '96': 'Size 96',
                    '98': 'Size 98',
                    '100': 'Size 100'
                };
                
                // Return full name if found, otherwise return the original code
                return sizeMap[sizeCode] || sizeCode;
            }
            
            // If no sizes available, show appropriate message but still allow selection
            if (sizes.length === 0) {
                // This should not happen anymore since we added fallback above, but keep as safety
                sizes = ['S', 'M', 'L', 'XL'];
                selectedQuickViewSize = 'M';
            }
            
            // Create size selection header with count
            const sizeHeader = document.createElement('div');
            sizeHeader.className = 'size-selection-header';
            sizeHeader.style.cssText = `
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            `;
            
            const sizeTitle = document.createElement('h4');
            sizeTitle.textContent = 'Size';
            sizeTitle.style.cssText = 'margin: 0; font-size: 14px; font-weight: 600;';
            
            const sizeCount = document.createElement('span');
            sizeCount.id = 'quick-view-size-count';
            sizeCount.style.cssText = 'font-size: 12px; color: #666; font-weight: 400;';
            
            sizeHeader.appendChild(sizeTitle);
            sizeHeader.appendChild(sizeCount);
            sizeSelection.appendChild(sizeHeader);
            
            // Create size action buttons
            const sizeActions = document.createElement('div');
            sizeActions.className = 'size-actions';
            sizeActions.style.cssText = `
                display: flex;
                gap: 8px;
                margin-bottom: 10px;
                width: 100%;
            `;
            
            const selectAllBtn = document.createElement('button');
            selectAllBtn.textContent = 'Select All';
            selectAllBtn.className = 'size-action-btn';
            selectAllBtn.style.cssText = `
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                color: #495057;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 11px;
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 500;
                flex: 1;
            `;
            
            const clearAllBtn = document.createElement('button');
            clearAllBtn.textContent = 'Clear';
            clearAllBtn.className = 'size-action-btn';
            clearAllBtn.style.cssText = `
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                color: #495057;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 11px;
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 500;
                flex: 1;
            `;
            
            // Add event listeners for action buttons
            selectAllBtn.addEventListener('click', function() {
                sizeSelection.querySelectorAll('.size-option').forEach(opt => {
                    opt.classList.add('selected');
                    opt.style.border = '2px solid #333';
                    opt.style.backgroundColor = '#333';
                    opt.style.color = 'white';
                });
                updateSizeCount();
            });
            
            clearAllBtn.addEventListener('click', function() {
                sizeSelection.querySelectorAll('.size-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.style.border = '2px solid #ddd';
                    opt.style.backgroundColor = 'transparent';
                    opt.style.color = '#333';
                });
                selectedQuickViewSize = '';
                updateSizeCount();
            });
            
            sizeActions.appendChild(selectAllBtn);
            sizeActions.appendChild(clearAllBtn);
            sizeSelection.appendChild(sizeActions);
            
            // Create size options container
            const sizeOptionsContainer = document.createElement('div');
            sizeOptionsContainer.className = 'size-options-container';
            sizeOptionsContainer.style.cssText = `
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
            `;
            
            sizes.forEach(size => {
                const sizeBtn = document.createElement('div');
                sizeBtn.className = 'size-option';
                
                // Use the original size code from database (S, M, L, etc.)
                sizeBtn.textContent = size;
                sizeBtn.setAttribute('data-size', size);
                sizeBtn.setAttribute('title', `Size: ${size}`);
                sizeBtn.style.cssText = `
                    display: inline-block;
                    padding: 8px 12px;
                    margin: 2px;
                    border: 2px solid #ddd;
                    border-radius: 4px;
                    cursor: pointer;
                    background-color: transparent;
                    color: #333;
                    transition: all 0.3s ease;
                    font-size: 14px;
                    font-weight: 500;
                    min-width: 40px;
                    text-align: center;
                `;
                
                // Set default selected size (first available size)
                if (sizes.indexOf(size) === 0) {
                    sizeBtn.classList.add('selected');
                    sizeBtn.style.border = '2px solid #333';
                    sizeBtn.style.backgroundColor = '#333';
                    sizeBtn.style.color = 'white';
                    selectedQuickViewSize = size;
                }
                
                // Add click handler with enhanced functionality
                sizeBtn.addEventListener('click', function() {
                    // Toggle selection instead of single selection
                    if (this.classList.contains('selected')) {
                        // Deselect
                        this.classList.remove('selected');
                        this.style.border = '2px solid #ddd';
                        this.style.backgroundColor = 'transparent';
                        this.style.color = '#333';
                        
                        // If this was the selected size, clear it
                        if (selectedQuickViewSize === size) {
                            selectedQuickViewSize = '';
                        }
                    } else {
                        // Select
                        this.classList.add('selected');
                    this.style.border = '2px solid #333';
                    this.style.backgroundColor = '#333';
                    this.style.color = 'white';
                    
                        // Set as selected size if none selected
                        if (!selectedQuickViewSize) {
                    selectedQuickViewSize = size;
                        }
                    }
                    
                    // Update count display
                    updateSizeCount();
                });
                
                sizeOptionsContainer.appendChild(sizeBtn);
            });
            
            sizeSelection.appendChild(sizeOptionsContainer);
            
            // Initialize size count
            updateSizeCount();
            
            } catch (error) {
                console.error('Error in size selection:', error);
                // Show a simple fallback message
                sizeSelection.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Size selection temporarily unavailable</div>';
            }
        }
        
        // Show quick view
        // console.log('Showing quick view...');
        if (sidebar) {
            sidebar.classList.add('active');
            // console.log('Sidebar active class added');
        } else {
            console.error('Sidebar not found');
        }
        if (overlay) {
            overlay.classList.add('active');
            // console.log('Overlay active class added');
        } else {
            console.error('Overlay not found');
        }
        document.body.style.overflow = 'hidden';
        // console.log('Quick view should be visible now');
        
        } catch (error) {
            console.error('Error in openQuickView:', error);
            // Still try to show the quickview even if there was an error
            if (sidebar) sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeQuickView() {
        // console.log('Closing quick view...');
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
        // Make functions globally accessible
        window.closeQuickView = closeQuickView;
        window.openQuickView = openQuickView;
    }
    
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
                        // console.log(`Showing ${mediaForColor.length} media items for color: ${selectedColor}`);
                        // console.log('Click on the media to see back view');
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
                    
                    // console.log(`Switching from media ${currentIndex + 1} to ${nextIndex + 1} of ${mediaForColor.length}`);
                    
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
                    // console.log('Only one media item for this color, no switching possible');
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
        
        // Quick view add to bag functionality
        if (e.target.classList.contains('add-to-bag-quick') || e.target.closest('.add-to-bag-quick')) {
            e.preventDefault();
            const button = e.target.classList.contains('add-to-bag-quick') ? e.target : e.target.closest('.add-to-bag-quick');
            
            if (button.disabled) return;
            
            const productId = button.getAttribute('data-product-id');
            const productName = document.getElementById('quick-view-title')?.textContent || 'Product';
            
            if (productId) {
                // Check if variants are selected
                if (!selectedQuickViewColor) {
                    showNotification('Please select a color first', 'error');
                    return;
                }
                if (!selectedQuickViewSize) {
                    // For products without sizes, use fallback
                    selectedQuickViewSize = 'M';
                }
                
                addToCartFromQuickView(productId, productName, selectedQuickViewColor, selectedQuickViewSize);
            }
        }
    });
    
    function addToCart(productId, productName) {
        // console.log('Adding to cart:', productId, productName);
        
        // Show loading state
        const button = document.querySelector(`[data-product-id="${productId}"] .add-to-bag`);
        let originalText = '';
        if (button) {
            originalText = button.textContent;
            button.textContent = 'Adding...';
            button.disabled = true;
        }
        
        // No loading notification needed
        
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
            // console.log('Cart API response:', data);
            
            if (data.success) {
                // Update cart count in header
                if (typeof addToCartCount === 'function') {
                    addToCartCount();
                }
                
                // Show brief success notification
                showNotification(`? ${productName} added to cart!`, 'success');
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

    function addToCartFromQuickView(productId, productName, selectedColor, selectedSize) {
        // console.log('Adding to cart from quick view:', productId, productName, 'Color:', selectedColor, 'Size:', selectedSize);
        
        // Show loading state
        const button = document.querySelector('#add-to-bag-quick');
        let originalText = '';
        if (button) {
            originalText = button.textContent;
            button.textContent = 'Adding...';
            button.disabled = true;
        }
        
        // Show immediate feedback notification
        showNotification(`Adding ${productName} (${selectedColor}, ${selectedSize}) to cart...`, 'info');
        
        // Make API call to add to cart with selected variants
        fetch('../cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add_to_cart&product_id=${productId}&quantity=1&color=${encodeURIComponent(selectedColor)}&size=${encodeURIComponent(selectedSize)}&return_url=${encodeURIComponent(window.location.href)}`
        })
        .then(response => response.json())
        .then(data => {
            // console.log('Cart API response:', data);
            
            if (data.success) {
                // Update cart count in header
                if (typeof addToCartCount === 'function') {
                    addToCartCount();
                }
                
                // Show success notification
                showNotification(`✓ ${productName} (${selectedColor}, ${selectedSize}) added to cart!`, 'success');
                
                // Close quick view after successful addition
                setTimeout(() => {
                    closeQuickView();
                }, 1500);
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
        
        // Remove after 1 second for success messages, longer for others
        const duration = type === 'success' ? 1000 : 3000; // 1 second for success, 3 seconds for others
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, duration);
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
                
                // console.log('Subcategory clicked:', category, subcategory);
                
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
                        
                        // Smooth scroll to the section
                        targetSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // console.log(`Navigating to section: ${href}`);
                    }
                } else if (href && !href.startsWith('#')) {
                    // Allow navigation to full URLs (don't prevent default)
                    // console.log(`Navigating to URL: ${href}`);
                    // Close modals before navigation
                    categoryModals.forEach(modal => {
                        modal.style.opacity = '0';
                        modal.style.visibility = 'hidden';
                    });
                } else if (subcategory) {
                    // console.log(`Navigating to ${category} > ${subcategory}`);
                    // Fallback for other subcategories that don't have sections yet
                }
            }
        });
        
        // Add hover delay for better UX
        let hoverTimeout;
        
        modalTriggers.forEach(trigger => {
            const modal = trigger.nextElementSibling;
            
            trigger.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    // Close all other modals
                    categoryModals.forEach(m => {
                        if (m !== modal) {
                            m.style.opacity = '0';
                            m.style.visibility = 'hidden';
                        }
                    });
                    
                    // Show current modal
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                }, 150); // Small delay for better UX
            });
            
            trigger.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                }, 200); // Slightly longer delay to prevent flickering
            });
            
            // Keep modal open when hovering over it
            modal.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
            });
            
            modal.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                }, 100);
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
        const userIcon = document.getElementById('user-icon');
        const flagContainer = document.querySelector('.flag-container');
        
        // Get close buttons
        const closeLoginModal = document.getElementById('close-login-modal');
        const closeRegisterModal = document.getElementById('close-register-modal');
        
        // User Icon Click - Open User Modal
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(userModal);
            });
        }
        
        // Flag Container Click - Disabled (Decorative Only)
        if (flagContainer) {
            flagContainer.addEventListener('click', function(e) {
                e.preventDefault();
                // Flag is decorative only - no action needed
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
        
        // Show/Hide Password functionality
        const showPasswordBtns = document.querySelectorAll('.show-password');
        
        showPasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });
        
        // Contact Number Validation and Formatting
        const contactInput = document.getElementById('contact-number');
        if (contactInput) {
            contactInput.addEventListener('input', function(e) {
                // Remove any non-numeric characters
                let value = this.value.replace(/\D/g, '');
                
                // Limit to 9 digits
                if (value.length > 9) {
                    value = value.substring(0, 9);
                }
                
                // Update the input value
                this.value = value;
            });
            
            // Prevent non-numeric input
            contactInput.addEventListener('keypress', function(e) {
                const charCode = e.which ? e.which : e.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    e.preventDefault();
                }
            });
        }

        // Region-City Dynamic Dropdown
        const regionSelect = document.getElementById('region');
        const citySelect = document.getElementById('city');
        
        if (regionSelect && citySelect) {
            regionSelect.addEventListener('change', function() {
                const selectedRegion = this.value;
                citySelect.innerHTML = '<option value="">Select City</option>';
                citySelect.disabled = true;
                
                if (selectedRegion) {
                    const cities = getCitiesForRegion(selectedRegion);
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.toLowerCase().replace(/\s+/g, '-');
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                }
            });
        }
        
        // Validation Modal Event Listeners
        const validationBtn = document.getElementById('validation-btn');
        if (validationBtn) {
            validationBtn.addEventListener('click', hideValidationModal);
        }

        // Close validation modal when clicking outside
        const validationModal = document.getElementById('validation-modal');
        if (validationModal) {
            validationModal.addEventListener('click', function(e) {
                if (e.target === validationModal) {
                    hideValidationModal();
                }
            });
        }

        // Authentication Form Switching
        const switchToRegister = document.getElementById('switch-to-register');
        const switchToLogin = document.getElementById('switch-to-login');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        
        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                loginForm.style.display = 'none';
                registerForm.style.display = 'flex';
            });
        }
        
        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                registerForm.style.display = 'none';
                loginForm.style.display = 'flex';
            });
        }

        // Login Form Submission
        const loginFormElement = document.querySelector('.login-form');
        
        if (loginFormElement) {
            loginFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = {
                    username: document.getElementById('login-username').value,
                    password: document.getElementById('login-password').value
                };
                
                // Show loading state
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                submitBtn.disabled = true;
                
                // Send login request
                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showValidationModal('success', 'Login Successful', data.message);
                        setTimeout(() => {
                            closeModal(userModal);
                            loginFormElement.reset();
                            // Update UI to show logged in state
                            updateUserInterface(data.user);
                        }, 1500);
                    } else {
                        showValidationModal('error', 'Login Failed', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showValidationModal('error', 'Connection Error', 'Login failed. Please check your connection and try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }

        // User Registration Form Submission
        const registrationForm = document.querySelector('.user-registration-form');
        
        if (registrationForm) {
            registrationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const contactNumber = document.getElementById('contact-number').value;
                const formData = {
                    username: document.getElementById('username').value,
                    email: document.getElementById('email').value,
                    contact_number: '+252' + contactNumber,
                    gender: document.querySelector('input[name="gender"]:checked')?.value || '',
                    region: document.getElementById('region').value,
                    city: document.getElementById('city').value,
                    password: document.getElementById('password').value,
                    confirm_password: document.getElementById('confirm-password').value
                };
                
                // Validation
                if (formData.password !== formData.confirm_password) {
                    showValidationModal('error', 'Password Mismatch', 'Passwords do not match! Please make sure both passwords are identical.');
                    return;
                }
                
                if (!formData.gender) {
                    showValidationModal('warning', 'Gender Required', 'Please select your gender to continue.');
                    return;
                }
                
                if (formData.password.length < 1) {
                    showValidationModal('error', 'Password Required', 'Please enter a password.');
                    return;
                }
                
                // Validate contact number (must be exactly 9 digits)
                if (contactNumber.length !== 9) {
                    showValidationModal('error', 'Invalid Contact Number', 'Contact number must be exactly 9 digits (e.g., 123456789).');
                    return;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                submitBtn.disabled = true;
                
                // Send registration request
                fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        showSuccessNotification();
                        
                        // Reset form
                        registrationForm.reset();
                        citySelect.disabled = true;
                        citySelect.innerHTML = '<option value="">Select Region First</option>';
                        
                        // Close modal and redirect to login after 1.5 seconds
                        setTimeout(() => {
                            closeModal(userModal);
                            // Switch to login form
                            switchToLoginForm();
                        }, 1500);
                    } else {
                        showValidationModal('error', 'Registration Failed', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showValidationModal('error', 'Connection Error', 'Registration failed. Please check your connection and try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
        
        // Helper functions
        function openModal(modal) {
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }
        
        function closeModal(modal) {
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
            }
        }
        
        function getCitiesForRegion(region) {
            const cityMap = {
                'banadir': ['Mogadishu', 'Afgooye', 'Marka'],
                'bari': ['Bosaso', 'Qardho', 'Caluula', 'Bandarbeyla'],
                'bay': ['Baidoa', 'Buurhakaba', 'Dinsor'],
                'galguduud': ['Dhusamareb', 'Guriceel', 'Abudwaq', 'Adado'],
                'gedo': ['Garbahaarrey', 'Bardheere', 'Luuq', 'El Wak'],
                'hiran': ['Beledweyne', 'Buloburde', 'Jalalaqsi'],
                'jubbada-dhexe': ['Bu\'aale', 'Jilib', 'Sakow'],
                'jubbada-hoose': ['Kismayo', 'Jamame', 'Badhaadhe'],
                'mudug': ['Galkayo', 'Hobyo', 'Jariban', 'Garacad', 'Goldogob', 'Bacaadwayn'],
                'nugaal': ['Garowe', 'Eyl', 'Burtinle'],
                'sanaag': ['Erigavo', 'Badhan', 'Las Khorey'],
                'shabeellaha-dhexe': ['Jowhar', 'Balcad', 'Adale', 'Warsheikh'],
                'shabeellaha-hoose': ['Marka', 'Wanlaweyn', 'Qoryooley', 'Baraawe'],
                'sool': ['Las Anod', 'Taleex', 'Xingalool'],
                'togdheer': ['Burao', 'Oodweyne', 'Sheikh'],
                'woqooyi-galbeed': ['Hargeisa', 'Berbera', 'Borama', 'Gabiley']
            };
            
            return cityMap[region] || [];
        }

        // Update user interface after login/registration
        function updateUserInterface(user) {
            const userIcon = document.querySelector('.user-icon');
            if (userIcon && user) {
                // Change user icon to show logged in state
                userIcon.innerHTML = `<i class="fas fa-user"></i>`;
                userIcon.title = `Welcome, ${user.username}!`;
                
                // Add logout functionality
                userIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Do you want to logout?')) {
                        logout();
                    }
                });
            }
        }

        // Show validation modal
        function showValidationModal(type, title, message) {
            const modal = document.getElementById('validation-modal');
            const icon = document.getElementById('validation-icon');
            const titleEl = document.getElementById('validation-title');
            const messageEl = document.getElementById('validation-message');
            
            if (modal && icon && titleEl && messageEl) {
                // Set icon based on type
                icon.className = 'validation-icon';
                if (type === 'error') {
                    icon.classList.add('error');
                    icon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
                } else if (type === 'success') {
                    icon.classList.add('success');
                    icon.innerHTML = '<i class="fas fa-check-circle"></i>';
                } else if (type === 'warning') {
                    icon.classList.add('warning');
                    icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                }
                
                // Set title and message
                titleEl.textContent = title;
                messageEl.textContent = message;
                
                // Show modal
                modal.classList.add('show');
            }
        }

        // Hide validation modal
        function hideValidationModal() {
            const modal = document.getElementById('validation-modal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Show success notification
        function showSuccessNotification() {
            const notification = document.getElementById('success-notification');
            if (notification) {
                notification.classList.add('show');
                
                // Hide notification after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            }
        }

        // Switch to login form
        function switchToLoginForm() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (loginForm && registerForm) {
                registerForm.style.display = 'none';
                loginForm.style.display = 'flex';
            }
        }

        // Logout function
        function logout() {
            fetch('logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showValidationModal('success', 'Logged Out', data.message);
                    setTimeout(() => {
                        // Reset user interface
                        const userIcon = document.querySelector('.user-icon');
                        if (userIcon) {
                            userIcon.innerHTML = `<i class="fas fa-user"></i>`;
                            userIcon.title = 'Sign In / Register';
                            
                            // Remove logout event listener and restore original functionality
                            userIcon.removeEventListener('click', logout);
                            userIcon.addEventListener('click', function() {
                                openModal(userModal);
                            });
                        }
                    }, 1500);
                } else {
                    showValidationModal('error', 'Logout Failed', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showValidationModal('error', 'Connection Error', 'Logout failed. Please try again.');
            });
        }
    }
    
    // Filter Functionality
    function initializeFilters() {
        // console.log('Initializing filters...');
        
        // Get current subcategory from URL
        const urlParams = new URLSearchParams(window.location.search);
        const currentSubcategory = urlParams.get('subcategory') || '';
        
        // Filter state
        let filterState = {
            sizes: [],
            colors: [],
            price_ranges: [],
            categories: [],
            lengths: []
        };
        
        // Add event listeners to all filter checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.hasAttribute('data-filter')) {
                const filterType = e.target.getAttribute('data-filter');
                const filterValue = e.target.value;
                const isChecked = e.target.checked;
                
                // console.log(`Filter changed: ${filterType} = ${filterValue}, checked: ${isChecked}`);
                
                // Update filter state
                if (isChecked) {
                    if (!filterState[filterType + 's'].includes(filterValue)) {
                        filterState[filterType + 's'].push(filterValue);
                    }
                } else {
                    const index = filterState[filterType + 's'].indexOf(filterValue);
                    if (index > -1) {
                        filterState[filterType + 's'].splice(index, 1);
                    }
                }
                
                // console.log('Current filter state:', filterState);
                
                // Apply filters
                applyFilters();
            }
        });
        
        // Clear filters button
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                clearAllFilters();
            });
        }
        
        function applyFilters() {
            // console.log('Applying filters...');
            
            // Show loading state
            showFilterLoading();
            
            // Prepare filter data
            const filterData = {
                action: 'filter_products',
                subcategory: currentSubcategory,
                sizes: filterState.sizes,
                colors: filterState.colors,
                price_ranges: filterState.price_ranges,
                categories: filterState.categories,
                lengths: filterState.lengths
            };
            
            // console.log('Sending filter data:', filterData);
            
            // Send filter request
            fetch('filter-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filterData)
            })
            .then(response => response.json())
            .then(data => {
                // console.log('Filter response:', data);
                
                if (data.success) {
                    updateProductGrid(data.data.products);
                    updateStyleCount(data.data.total_count);
                    hideFilterLoading();
                } else {
                    console.error('Filter error:', data.message);
                    hideFilterLoading();
                    showFilterError(data.message);
                }
            })
            .catch(error => {
                console.error('Filter request error:', error);
                hideFilterLoading();
                showFilterError('Network error occurred');
            });
        }
        
        function clearAllFilters() {
            // console.log('Clearing all filters...');
            
            // Uncheck all filter checkboxes
            document.querySelectorAll('input[data-filter]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset filter state
            filterState = {
                sizes: [],
                colors: [],
                price_ranges: [],
                categories: [],
                lengths: []
            };
            
            // Apply filters (will show all products)
            applyFilters();
        }
        
        // Make clearAllFilters globally accessible
        window.clearAllFilters = clearAllFilters;
        
        function updateProductGrid(products) {
            // console.log(`Updating product grid with ${products.length} products`);
            
            // Get the appropriate product grid based on current subcategory
            let productGrid;
            if (currentSubcategory) {
                productGrid = document.getElementById('filtered-products-grid');
            } else {
                // Try different grid IDs for main page
                productGrid = document.getElementById('dresses-grid') || 
                             document.getElementById('filtered-products-grid') ||
                             document.querySelector('.product-grid');
            }
            
            if (!productGrid) {
                console.error('Product grid not found');
                return;
            }
            
            // Clear existing products
            productGrid.innerHTML = '';
            
            if (products.length === 0) {
                productGrid.innerHTML = `
                    <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p>No products found matching your filters.</p>
                        <button onclick="clearAllFilters()" class="clear-filters-btn">Clear Filters</button>
                    </div>
                `;
                return;
            }
            
            // Products will be loaded from the main page, no need to create ugly product cards
        }
        
        
        
        function updateStyleCount(count) {
            const styleCountElement = document.getElementById('style-count');
            if (styleCountElement) {
                styleCountElement.textContent = `${count} Styles`;
            }
        }
        
        function showFilterLoading() {
            // Add loading overlay to product grid
            const productGrid = document.getElementById('filtered-products-grid') || 
                               document.getElementById('dresses-grid') ||
                               document.querySelector('.product-grid');
            if (productGrid) {
                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'filter-loading';
                loadingOverlay.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                `;
                loadingOverlay.innerHTML = `
                    <div style="text-align: center;">
                        <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
                        <p>Filtering products...</p>
                    </div>
                `;
                productGrid.style.position = 'relative';
                productGrid.appendChild(loadingOverlay);
                
                // Also add loading state to sidebar
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.add('filter-loading');
                }
            }
        }
        
        function hideFilterLoading() {
            const loadingOverlay = document.getElementById('filter-loading');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
            
            // Remove loading state from sidebar
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.remove('filter-loading');
            }
        }
        
        function showFilterError(message) {
            // Show error notification
            showNotification(`Filter error: ${message}`, 'error');
        }
        
        // Add CSS for loading animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* Enhanced Size Selection Styles */
            .size-selection-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }
            
            .size-actions {
                display: flex;
                gap: 8px;
                margin-bottom: 10px;
                width: 100%;
            }
            
            .size-action-btn {
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                color: #495057;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 11px;
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 500;
                flex: 1;
            }
            
            .size-action-btn:hover {
                background: #e9ecef;
                border-color: #dee2e6;
            }
            
            .size-options-container {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                margin-top: 5px;
            }
            
            .size-option {
                display: inline-block;
                padding: 8px 12px;
                margin: 2px;
                border: 2px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
                background-color: transparent;
                color: #333;
                transition: all 0.3s ease;
                font-size: 14px;
                font-weight: 500;
                min-width: 40px;
                text-align: center;
                user-select: none;
                white-space: nowrap;
            }
            
            .size-option:hover {
                border-color: #999;
                background-color: #f8f9fa;
            }
            
            .size-option.selected {
                border: 2px solid #333;
                background-color: #333;
                color: white;
            }
            
            .size-option.selected:hover {
                background-color: #555;
            }
            
            /* Quick View Size Count */
            #quick-view-size-count {
                font-size: 12px;
                color: #666;
                font-weight: 400;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .size-option {
                    min-width: 35px;
                    padding: 6px 10px;
                    font-size: 13px;
                }
                
                .size-action-btn {
                    padding: 5px 10px;
                    font-size: 10px;
                }
            }
            
            @media (max-width: 480px) {
                .size-option {
                    min-width: 32px;
                    padding: 5px 8px;
                    font-size: 12px;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Enhanced Size Filter Functions - Works with Multiple Forms
    function selectAllSizes() {
        // console.log('Selecting all sizes...');
        
        // Handle sidebar filter sizes
        const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        let hasChanges = false;
        
        sidebarSizeCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.checked = true;
                hasChanges = true;
                // Trigger change event to update filters
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            }
        });
        
        // Handle quickview size selections for all open quickviews
        const quickviewSizeSelections = document.querySelectorAll('#quick-view-size-selection .size-option');
        quickviewSizeSelections.forEach(sizeOption => {
            if (!sizeOption.classList.contains('selected')) {
                sizeOption.classList.add('selected');
                sizeOption.style.border = '2px solid #333';
                sizeOption.style.backgroundColor = '#333';
                sizeOption.style.color = 'white';
            }
        });
        
        // Update count display
        updateSizeCount();
        
        // If changes were made, trigger filter update
        if (hasChanges) {
            // console.log('Size selection changed, updating filters...');
            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        }
    }
    
    function clearSizeFilters() {
        // console.log('Clearing size filters...');
        
        // Handle sidebar filter sizes
        const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        let hasChanges = false;
        
        sidebarSizeCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                hasChanges = true;
                // Trigger change event to update filters
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            }
        });
        
        // Handle quickview size selections for all open quickviews
        const quickviewSizeSelections = document.querySelectorAll('#quick-view-size-selection .size-option');
        quickviewSizeSelections.forEach(sizeOption => {
            if (sizeOption.classList.contains('selected')) {
                sizeOption.classList.remove('selected');
                sizeOption.style.border = '2px solid #ddd';
                sizeOption.style.backgroundColor = 'transparent';
                sizeOption.style.color = '#333';
            }
        });
        
        // Update count display
        updateSizeCount();
        
        // If changes were made, trigger filter update
        if (hasChanges) {
            // console.log('Size selection cleared, updating filters...');
            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        }
    }
    
    function updateSizeCount() {
        // Update sidebar size count
        const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]:checked');
        const sidebarSizeCountElement = document.getElementById('size-count');
        if (sidebarSizeCountElement) {
            const count = sidebarSizeCheckboxes.length;
            sidebarSizeCountElement.textContent = count > 0 ? `(${count} selected)` : '';
        }
        
        // Update quickview size count if available
        const quickviewSizeCountElement = document.getElementById('quick-view-size-count');
        if (quickviewSizeCountElement) {
            const quickviewSelectedSizes = document.querySelectorAll('#quick-view-size-selection .size-option.selected');
            const quickviewCount = quickviewSelectedSizes.length;
            quickviewSizeCountElement.textContent = quickviewCount > 0 ? `(${quickviewCount} selected)` : '';
        }
    }
    
    // Enhanced global functions for multiple form handling
    window.selectAllSizes = selectAllSizes;
    window.clearSizeFilters = clearSizeFilters;
    window.updateSizeCount = updateSizeCount;
    
    // Global function to handle size selection across all forms
    window.handleSizeSelection = function(sizeElement, isSelected) {
        if (isSelected) {
            sizeElement.classList.add('selected');
            sizeElement.style.border = '2px solid #333';
            sizeElement.style.backgroundColor = '#333';
            sizeElement.style.color = 'white';
        } else {
            sizeElement.classList.remove('selected');
            sizeElement.style.border = '2px solid #ddd';
            sizeElement.style.backgroundColor = 'transparent';
            sizeElement.style.color = '#333';
        }
        updateSizeCount();
    };
    
    // Global function to get all selected sizes from all forms
    window.getAllSelectedSizes = function() {
        const selectedSizes = [];
        
        // Get from sidebar filters
        const sidebarSelected = document.querySelectorAll('#size-filter input[type="checkbox"]:checked');
        sidebarSelected.forEach(checkbox => {
            selectedSizes.push(checkbox.value);
        });
        
        // Get from quickview
        const quickviewSelected = document.querySelectorAll('#quick-view-size-selection .size-option.selected');
        quickviewSelected.forEach(sizeOption => {
            selectedSizes.push(sizeOption.getAttribute('data-size'));
        });
        
        return [...new Set(selectedSizes)]; // Remove duplicates
    };
    
    // Global function to reset all size selections
    window.resetAllSizeSelections = function() {
        // Reset sidebar filters
        const sidebarCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
        sidebarCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        });
        
        // Reset quickview selections
        const quickviewSizes = document.querySelectorAll('#quick-view-size-selection .size-option');
        quickviewSizes.forEach(sizeOption => {
            sizeOption.classList.remove('selected');
            sizeOption.style.border = '2px solid #ddd';
            sizeOption.style.backgroundColor = 'transparent';
            sizeOption.style.color = '#333';
        });
        
        updateSizeCount();
    };
    
    // Global function to convert size codes to full names
    window.getSizeName = function(sizeCode) {
        const sizeMap = {
            'XXS': 'Extra Extra Small',
            'XS': 'Extra Small', 
            'S': 'Small',
            'M': 'Medium',
            'L': 'Large',
            'XL': 'Extra Large',
            'XXL': 'Extra Extra Large',
            'XXXL': 'Extra Extra Extra Large',
            '0': 'Size 0',
            '2': 'Size 2',
            '4': 'Size 4',
            '6': 'Size 6',
            '8': 'Size 8',
            '10': 'Size 10',
            '12': 'Size 12',
            '14': 'Size 14',
            '16': 'Size 16',
            '18': 'Size 18',
            '20': 'Size 20',
            '22': 'Size 22',
            '24': 'Size 24',
            '26': 'Size 26',
            '28': 'Size 28',
            '30': 'Size 30',
            '32': 'Size 32',
            '34': 'Size 34',
            '36': 'Size 36',
            '38': 'Size 38',
            '40': 'Size 40',
            '42': 'Size 42',
            '44': 'Size 44',
            '46': 'Size 46',
            '48': 'Size 48',
            '50': 'Size 50',
            '52': 'Size 52',
            '54': 'Size 54',
            '56': 'Size 56',
            '58': 'Size 58',
            '60': 'Size 60',
            '62': 'Size 62',
            '64': 'Size 64',
            '66': 'Size 66',
            '68': 'Size 68',
            '70': 'Size 70',
            '72': 'Size 72',
            '74': 'Size 74',
            '76': 'Size 76',
            '78': 'Size 78',
            '80': 'Size 80',
            '82': 'Size 82',
            '84': 'Size 84',
            '86': 'Size 86',
            '88': 'Size 88',
            '90': 'Size 90',
            '92': 'Size 92',
            '94': 'Size 94',
            '96': 'Size 96',
            '98': 'Size 98',
            '100': 'Size 100'
        };
        
        // Return full name if found, otherwise return the original code
        return sizeMap[sizeCode] || sizeCode;
    };
    
    // Add event listener for size count updates
    document.addEventListener('change', function(e) {
        if (e.target.closest('#size-filter')) {
            updateSizeCount();
        }
    });
    
    // Initialize size count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSizeCount();
    });
    
    // Enhanced fallback functions for multiple forms
    if (!window.selectAllSizes) {
        window.selectAllSizes = function() {
            // console.log('Fallback selectAllSizes called...');
            
            // Handle sidebar filter sizes
            const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
            sidebarSizeCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            });
            
            // Handle quickview size selections
            const quickviewSizeSelections = document.querySelectorAll('#quick-view-size-selection .size-option');
            quickviewSizeSelections.forEach(sizeOption => {
                sizeOption.classList.add('selected');
                sizeOption.style.border = '2px solid #333';
                sizeOption.style.backgroundColor = '#333';
                sizeOption.style.color = 'white';
            });
            
            updateSizeCount();
        };
    }
    
    if (!window.clearSizeFilters) {
        window.clearSizeFilters = function() {
            // console.log('Fallback clearSizeFilters called...');
            
            // Handle sidebar filter sizes
            const sidebarSizeCheckboxes = document.querySelectorAll('#size-filter input[type="checkbox"]');
            sidebarSizeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            });
            
            // Handle quickview size selections
            const quickviewSizeSelections = document.querySelectorAll('#quick-view-size-selection .size-option');
            quickviewSizeSelections.forEach(sizeOption => {
                sizeOption.classList.remove('selected');
                sizeOption.style.border = '2px solid #ddd';
                sizeOption.style.backgroundColor = 'transparent';
                sizeOption.style.color = '#333';
            });
            
            updateSizeCount();
        };
    }
});

