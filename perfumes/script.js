// E-commerce Page JavaScript
console.log('🚀 Script loaded successfully!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded successfully!');

    // Scroll functionality for logo and nav-right fade
    let lastScrollTop = 0;
    const logoContainer = document.querySelector('.logo-container');
    const navRightContainer = document.querySelector('.nav-right-container');
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Fade out when scrolling down, fade in when scrolling up or at top
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down and not at top
            logoContainer.style.opacity = '0';
            logoContainer.style.transform = 'translateX(-20px)';
            navRightContainer.style.opacity = '0';
            navRightContainer.style.transform = 'translateX(20px)';
        } else {
            // Scrolling up or at top
            logoContainer.style.opacity = '1';
            logoContainer.style.transform = 'translateX(0)';
            navRightContainer.style.opacity = '1';
            navRightContainer.style.transform = 'translateX(0)';
        }
        
        lastScrollTop = scrollTop;
    });

    // Product Image Slider and Color Switching Functionality
    const productCards = document.querySelectorAll('.product-card');
    console.log('Found product cards:', productCards.length);
    
    productCards.forEach((card, index) => {
        const imageSlider = card.querySelector('.image-slider');
        const images = imageSlider ? imageSlider.querySelectorAll('img') : [];
        const colorCircles = card.querySelectorAll('.color-circle');
        let currentColor = 'black'; // Default color
        let autoSlideInterval;
        let currentImageIndex = 0; // Track current image index for each color
        
        console.log(`Product ${index + 1}: Found ${images.length} images and ${colorCircles.length} color circles`);
        
        // Only initialize if we have images
        if (images.length > 0) {
            console.log(`Product ${index + 1}: Initializing slider`);
            
            // Initialize auto-sliding for the first color
            startAutoSlide();
            
            // Manual swipe functionality - click on image to switch
            imageSlider.addEventListener('click', function(e) {
                // Don't trigger if clicking on buttons
                if (e.target.closest('.heart-button') || e.target.closest('.product-actions')) {
                    return;
                }
                
                manualNextImage();
            });
            
            // Manual swipe with arrow keys
            card.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    manualPrevImage();
                } else if (e.key === 'ArrowRight') {
                    manualNextImage();
                }
            });
            
            // Touch/swipe support for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            imageSlider.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });
            
            imageSlider.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
            
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        // Swiped left - next image
                        manualNextImage();
                    } else {
                        // Swiped right - previous image
                        manualPrevImage();
                    }
                }
            }
            
            // Manual navigation functions
            function manualNextImage() {
                const colorImages = Array.from(images).filter(img => img.getAttribute('data-color') === currentColor);
                if (colorImages.length > 1) {
                    // Hide current image
                    colorImages[currentImageIndex].classList.remove('active');
                    
                    // Move to next image
                    currentImageIndex = (currentImageIndex + 1) % colorImages.length;
                    
                    // Show next image
                    colorImages[currentImageIndex].classList.add('active');
                    
                    console.log(`Product ${index + 1}: Manual switch to image ${currentImageIndex} for color ${currentColor}`);
                    
                    // Restart auto-sliding timer
                    restartAutoSlide();
                }
            }
            
            function manualPrevImage() {
                const colorImages = Array.from(images).filter(img => img.getAttribute('data-color') === currentColor);
                if (colorImages.length > 1) {
                    // Hide current image
                    colorImages[currentImageIndex].classList.remove('active');
                    
                    // Move to previous image
                    currentImageIndex = (currentImageIndex - 1 + colorImages.length) % colorImages.length;
                    
                    // Show previous image
                    colorImages[currentImageIndex].classList.add('active');
                    
                    console.log(`Product ${index + 1}: Manual switch to image ${currentImageIndex} for color ${currentColor}`);
                    
                    // Restart auto-sliding timer
                    restartAutoSlide();
                }
            }
            
            // Color circle click functionality
            colorCircles.forEach(circle => {
                circle.addEventListener('click', function() {
                    const selectedColor = this.getAttribute('data-color');
                    console.log(`Product ${index + 1}: Switching to color ${selectedColor}`);
                    
                    // Update active color circle
                    colorCircles.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Switch to the selected color
                    switchToColor(selectedColor);
                });
            });
        }
        
        // Function to switch to a specific color
        function switchToColor(color) {
            currentColor = color;
            currentImageIndex = 0; // Reset to first image of new color
            
            // Hide all images
            images.forEach(img => {
                img.classList.remove('active');
            });
            
            // Show the first image of the selected color (front view)
            const colorImages = Array.from(images).filter(img => img.getAttribute('data-color') === color);
            console.log(`Product ${index + 1}: Found ${colorImages.length} images for color ${color}`);
            
            if (colorImages.length > 0) {
                colorImages[0].classList.add('active');
            }
            
            // Restart auto-sliding for the new color
            startAutoSlide();
        }
        
        // Function to restart auto-sliding timer
        function restartAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
            startAutoSlide();
        }
        
        // Function to start auto-sliding
        function startAutoSlide() {
            // Clear existing interval
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
            
            // Start new interval
            autoSlideInterval = setInterval(() => {
                const colorImages = Array.from(images).filter(img => img.getAttribute('data-color') === currentColor);
                
                if (colorImages.length > 1) {
                    // Hide current image
                    colorImages[currentImageIndex].classList.remove('active');
                    
                    // Move to next image
                    currentImageIndex = (currentImageIndex + 1) % colorImages.length;
                    
                    // Show next image
                    colorImages[currentImageIndex].classList.add('active');
                    
                    console.log(`Product ${index + 1}: Auto-switching to image ${currentImageIndex} for color ${currentColor}`);
                }
            }, 3000); // Switch every 3 seconds
        }
        
        // Pause auto-sliding on hover
        card.addEventListener('mouseenter', () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                console.log(`Product ${index + 1}: Paused auto-sliding`);
            }
        });
        
        // Resume auto-sliding when mouse leaves
        card.addEventListener('mouseleave', () => {
            startAutoSlide();
            console.log(`Product ${index + 1}: Resumed auto-sliding`);
        });
    });

    // Quick View Sidebar Functionality
    function setupQuickView() {
        const quickViewSidebar = document.getElementById('quick-view-sidebar');
        const quickViewOverlay = document.getElementById('quick-view-overlay');
        const closeQuickView = document.getElementById('close-quick-view');
        const quickViewButtons = document.querySelectorAll('.quick-view');
        
        console.log('Quick view elements found:');
        console.log('- Sidebar:', quickViewSidebar);
        console.log('- Overlay:', quickViewOverlay);
        console.log('- Close button:', closeQuickView);
        console.log('- Quick view buttons:', quickViewButtons.length);
    
    // Test if we can find the elements
    if (!quickViewSidebar) {
        console.error('❌ Quick view sidebar not found!');
    } else {
        console.log('✅ Quick view sidebar found');
    }
    
    if (!quickViewOverlay) {
        console.error('❌ Quick view overlay not found!');
    } else {
        console.log('✅ Quick view overlay found');
    }
    
    if (quickViewButtons.length === 0) {
        console.error('❌ No quick view buttons found!');
    } else {
        console.log('✅ Found', quickViewButtons.length, 'quick view buttons');
    }
    
    // Product data is now extracted dynamically from the DOM

    // Open Quick View
    console.log('Setting up quick view buttons. Found:', quickViewButtons.length, 'buttons');
    
    // Debug: Log each button
    quickViewButtons.forEach((button, index) => {
        console.log(`Button ${index}:`, button);
        console.log(`Button ${index} data-product-id:`, button.getAttribute('data-product-id'));
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.getAttribute('data-product-id');
            console.log('Quick view clicked for product ID:', productId);
            openQuickView(productId);
        });
    });
    


    // Close Quick View
    if (closeQuickView) {
        closeQuickView.addEventListener('click', closeQuickViewSidebar);
    }

    if (quickViewOverlay) {
        quickViewOverlay.addEventListener('click', closeQuickViewSidebar);
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && quickViewSidebar.classList.contains('active')) {
            closeQuickViewSidebar();
        }
    });

    function openQuickView(productId) {
        console.log('openQuickView called with productId:', productId);
        
        // Find the product card with this ID
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        console.log('Product card found:', productCard);
        
        if (!productCard) {
            console.log('No product card found for ID:', productId);
            console.log('All product cards:', document.querySelectorAll('[data-product-id]'));
            return;
        }
        
        // Extract product data from the DOM
        const product = {
            name: productCard.querySelector('.product-name').textContent,
            price: productCard.querySelector('.product-price').textContent,
            brand: productCard.querySelector('.product-brand').textContent,
            size: productCard.querySelector('.product-size').textContent,
            images: []
        };
        
        // Get images from the product card
        const images = productCard.querySelectorAll('.image-slider img');
        images.forEach(img => {
            product.images.push({
                src: img.src,
                color: img.getAttribute('data-color') || 'default'
            });
        });
        
        // Get colors from the product card
        const colorCircles = productCard.querySelectorAll('.color-circle');
        product.colors = [];
        colorCircles.forEach((circle, index) => {
            product.colors.push({
                name: circle.title || circle.getAttribute('data-color'),
                value: circle.getAttribute('data-color'),
                hex: circle.style.backgroundColor || '#000'
            });
        });
        
        // Get sizes (for perfumes, usually just one size)
        product.sizes = [product.size || '100ml'];
        
        console.log('Extracted product data:', product);

        // Populate product data
        document.getElementById('quick-view-title').textContent = product.name;
        document.getElementById('quick-view-price').textContent = product.price;
        
        // Add brand information if available
        const brandElement = document.getElementById('quick-view-brand');
        if (brandElement && product.brand) {
            brandElement.textContent = product.brand;
        }
        
        // Set main image
        const mainImage = document.getElementById('quick-view-main-image');
        mainImage.src = product.images[0].src;
        mainImage.alt = product.name;

        // Populate thumbnails
        const thumbnailsContainer = document.getElementById('quick-view-thumbnails');
        thumbnailsContainer.innerHTML = '';
        
        product.images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
            thumbnail.innerHTML = `<img src="${image.src}" alt="${product.name} - ${image.color}" data-index="${index}">`;
            
            thumbnail.addEventListener('click', () => {
                // Update main image
                mainImage.src = image.src;
                
                // Update active thumbnail
                thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            });
            
            thumbnailsContainer.appendChild(thumbnail);
        });

        // Populate colors
        const colorSelection = document.getElementById('quick-view-color-selection');
        colorSelection.innerHTML = '';
        
        product.colors.forEach((color, index) => {
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
                const colorImages = product.images.filter(img => img.color === selectedColor);
                
                if (colorImages.length > 0) {
                    // Update main image and thumbnails
                    mainImage.src = colorImages[0].src;
                    
                    // Update thumbnails
                    thumbnailsContainer.innerHTML = '';
                    colorImages.forEach((image, imgIndex) => {
                        const thumbnail = document.createElement('div');
                        thumbnail.className = `thumbnail-item ${imgIndex === 0 ? 'active' : ''}`;
                        thumbnail.innerHTML = `<img src="${image.src}" alt="${product.name} - ${image.color}" data-index="${imgIndex}">`;
                        
                        thumbnail.addEventListener('click', () => {
                            mainImage.src = image.src;
                            thumbnailsContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                            thumbnail.classList.add('active');
                        });
                        
                        thumbnailsContainer.appendChild(thumbnail);
                    });
                }
            });
            
            colorSelection.appendChild(colorCircle);
        });

        // Populate sizes
        const sizeSelection = document.getElementById('quick-view-size-selection');
        sizeSelection.innerHTML = '';
        
        product.sizes.forEach(size => {
            const sizeBtn = document.createElement('button');
            sizeBtn.className = 'quick-view-size-btn';
            sizeBtn.textContent = size;
            
            // For perfumes, we don't have soldOutSizes, so just add click event
            sizeBtn.addEventListener('click', () => {
                sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                sizeBtn.classList.add('active');
            });
            
            sizeSelection.appendChild(sizeBtn);
        });
        
        // Add perfume-specific information
        const descriptionElement = document.querySelector('.quick-view-description p');
        if (descriptionElement && product.concentration) {
            let description = `A luxurious ${product.concentration} fragrance. `;
            if (product.notes) {
                description += `Features notes of ${product.notes}. `;
            }
            if (product.longevity) {
                description += `Longevity: ${product.longevity}. `;
            }
            if (product.sillage) {
                description += `Sillage: ${product.sillage}. `;
            }
            if (product.season) {
                description += `Perfect for ${product.season}.`;
            }
            descriptionElement.textContent = description;
        }

        // Show sidebar
        console.log('Showing quick view sidebar...');
        console.log('Sidebar element:', quickViewSidebar);
        console.log('Overlay element:', quickViewOverlay);
        
        if (quickViewSidebar) {
            quickViewSidebar.classList.add('active');
            console.log('Added active class to sidebar');
        } else {
            console.error('Quick view sidebar not found!');
        }
        
        if (quickViewOverlay) {
            quickViewOverlay.classList.add('active');
            console.log('Added active class to overlay');
        } else {
            console.error('Quick view overlay not found!');
        }
        
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        console.log('Sidebar classes:', quickViewSidebar ? quickViewSidebar.className : 'sidebar not found');
        console.log('Overlay classes:', quickViewOverlay ? quickViewOverlay.className : 'overlay not found');
    }

    function closeQuickViewSidebar() {
        quickViewSidebar.classList.remove('active');
        quickViewOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Add to bag functionality for quick view
    const addToBagQuick = document.getElementById('add-to-bag-quick');
    if (addToBagQuick) {
        addToBagQuick.addEventListener('click', function() {
            const selectedSize = document.querySelector('.quick-view-size-btn.active');
            if (!selectedSize) {
                showNotification('Please select a size', 'error');
                return;
            }
            
            // Get the product ID from the currently open quick view
            const productName = document.getElementById('quick-view-title').textContent;
            const productCard = document.querySelector(`[data-product-id] .product-name`);
            let productId = null;
            
            // Find the product card that matches the current product name
            document.querySelectorAll('.product-card').forEach(card => {
                if (card.querySelector('.product-name').textContent === productName) {
                    productId = card.getAttribute('data-product-id');
                }
            });
            
            if (!productId) {
                showNotification('Product not found', 'error');
                return;
            }
            
            const selectedColor = document.querySelector('.quick-view-color-circle.active');
            const colorName = selectedColor ? selectedColor.title : '';
            
            console.log(`Adding to cart: ${productName} - Size: ${selectedSize.textContent}, Color: ${colorName}`);
            addToCart(productId, productName);
        });
    }

    // Add to wishlist functionality for quick view
    const addToWishlistQuick = document.getElementById('add-to-wishlist-quick');
    if (addToWishlistQuick) {
        addToWishlistQuick.addEventListener('click', function() {
            const productName = document.getElementById('quick-view-title').textContent;
            console.log(`Added to wishlist: ${productName}`);
            showNotification(`✓ ${productName} added to wishlist!`, 'success');
        });
    }
    
    } // End of setupQuickView function

    // Modal Functionality
    const somaliaFlag = document.getElementById('somalia-flag');
    const userIcon = document.getElementById('user-icon');
    const regionModal = document.getElementById('region-modal');
    const userModal = document.getElementById('user-modal');
    const closeRegionModal = document.getElementById('close-region-modal');
    const closeUserModal = document.getElementById('close-user-modal');

    // Open Region Modal
    if (somaliaFlag) {
        somaliaFlag.addEventListener('click', function() {
            regionModal.classList.add('active');
        });
    }

    // Open User Modal
    if (userIcon) {
        userIcon.addEventListener('click', function() {
            userModal.classList.add('active');
        });
    }

    // Close Modals
    if (closeRegionModal) {
        closeRegionModal.addEventListener('click', function() {
            regionModal.classList.remove('active');
        });
    }

    if (closeUserModal) {
        closeUserModal.addEventListener('click', function() {
            userModal.classList.remove('active');
        });
    }

    // Close modal when clicking outside content
    if (regionModal) {
        regionModal.addEventListener('click', function(event) {
            if (event.target === regionModal) {
                regionModal.classList.remove('active');
            }
        });
    }

    if (userModal) {
        userModal.addEventListener('click', function(event) {
            if (event.target === userModal) {
                userModal.classList.remove('active');
            }
        });
    }

    // Tab functionality for user modal
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            button.classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
        });
    });

    // Show/Hide Password functionality
    const passwordContainers = document.querySelectorAll('.password-container');
    
    passwordContainers.forEach(container => {
        const input = container.querySelector('input');
        const showPassword = container.querySelector('.show-password');
        
        if (showPassword && input) {
            showPassword.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    this.textContent = 'Show';
                }
            });
        }
    });

    // Heart button functionality
    const heartButtons = document.querySelectorAll('.heart-button');
    
    heartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('fas')) {
                icon.classList.remove('fas');
                icon.classList.add('far');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
        });
    });

    // Color circle selection functionality
    const colorCircles = document.querySelectorAll('.color-circle');
    
    colorCircles.forEach(circle => {
        circle.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const circles = productCard.querySelectorAll('.color-circle');
            
            // Remove active class from all circles in this product
            circles.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked circle
            this.classList.add('active');
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-bag');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productId = productCard.getAttribute('data-product-id');
            const productName = productCard.querySelector('.product-name').textContent;
            
            console.log('Adding to cart:', productName);
            addToCart(productId, productName);
        });
    });

    // Quick view functionality is now handled by the main event listeners above

    // Sorting Functionality
    const sortSelect = document.getElementById('sort-select');
    const productGrid = document.querySelector('.product-grid');
    
    if (sortSelect && productGrid) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const products = Array.from(productGrid.querySelectorAll('.product-card'));
            
            console.log('Sorting products by:', sortValue);
            
            // Sort products based on selected option
            products.sort((a, b) => {
                switch(sortValue) {
                    case 'price-low':
                        const priceA = parseInt(a.getAttribute('data-price'));
                        const priceB = parseInt(b.getAttribute('data-price'));
                        return priceA - priceB;
                        
                    case 'price-high':
                        const priceAHigh = parseInt(a.getAttribute('data-price'));
                        const priceBHigh = parseInt(b.getAttribute('data-price'));
                        return priceBHigh - priceAHigh;
                        
                    case 'newest':
                        const idA = parseInt(a.getAttribute('data-product-id'));
                        const idB = parseInt(b.getAttribute('data-product-id'));
                        return idB - idA; // Higher ID = newer
                        
                    case 'popular':
                        // For now, sort by price as a proxy for popularity
                        const priceAPop = parseInt(a.getAttribute('data-price'));
                        const priceBPop = parseInt(b.getAttribute('data-price'));
                        return priceBPop - priceAPop; // Higher price = more popular
                        
                    case 'featured':
                    default:
                        // Default order (as they appear in HTML)
                        const idAFeat = parseInt(a.getAttribute('data-product-id'));
                        const idBFeat = parseInt(b.getAttribute('data-product-id'));
                        return idAFeat - idBFeat;
                }
            });
            
            // Clear the grid and re-append sorted products
            productGrid.innerHTML = '';
            products.forEach(product => {
                productGrid.appendChild(product);
            });
            
            console.log('Products sorted successfully!');
        });
    }

    // Filtering Functionality
    const filterCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            applyFilters();
        });
    });
    
    function applyFilters() {
        const products = Array.from(productGrid.querySelectorAll('.product-card'));
        const selectedGenders = getSelectedValues('gender');
        const selectedSizes = getSelectedValues('size');
        const selectedBrands = getSelectedValues('category'); // category is used for brands
        const selectedPrices = getSelectedValues('price');
        
        console.log('Applying filters:');
        console.log('- Selected genders:', selectedGenders);
        console.log('- Selected sizes:', selectedSizes);
        console.log('- Selected brands:', selectedBrands);
        console.log('- Selected prices:', selectedPrices);
        
        products.forEach(product => {
            let shouldShow = true;
            
            // Check gender filter
            if (selectedGenders.length > 0) {
                const productGender = product.getAttribute('data-gender');
                if (!selectedGenders.includes(productGender)) {
                    shouldShow = false;
                }
            }
            
            // Check size filter
            if (selectedSizes.length > 0 && shouldShow) {
                const productSize = product.getAttribute('data-size');
                if (!selectedSizes.includes(productSize)) {
                    shouldShow = false;
                }
            }
            
            // Check brand filter
            if (selectedBrands.length > 0 && shouldShow) {
                const productBrand = product.getAttribute('data-brand');
                if (!selectedBrands.includes(productBrand)) {
                    shouldShow = false;
                }
            }
            
            // Check price filter
            if (selectedPrices.length > 0 && shouldShow) {
                const productPrice = parseInt(product.getAttribute('data-price'));
                let priceInRange = false;
                console.log(`Checking price filter for product ${product.getAttribute('data-product-id')}: $${productPrice}`);
                
                selectedPrices.forEach(priceRange => {
                    switch(priceRange) {
                        case '90-110':
                            if (productPrice >= 90 && productPrice <= 110) priceInRange = true;
                            break;
                        case '110-150':
                            if (productPrice >= 110 && productPrice <= 150) priceInRange = true;
                            break;
                        case '150-200':
                            if (productPrice >= 150 && productPrice <= 200) priceInRange = true;
                            break;
                        case '200-250':
                            if (productPrice >= 200 && productPrice <= 250) priceInRange = true;
                            break;
                        case '250+':
                            if (productPrice >= 250) priceInRange = true;
                            break;
                    }
                });
                
                if (!priceInRange) {
                    shouldShow = false;
                }
            }
            
            // Show/hide product
            if (shouldShow) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
        
        console.log('Filters applied successfully!');
        
        // Update the style count
        updateStyleCount();
        
        // Add visual feedback for active filters
        updateFilterIndicators();
    }
    
    function getSelectedValues(name) {
        const checkboxes = document.querySelectorAll(`input[name="${name}[]"]:checked`);
        return Array.from(checkboxes).map(cb => cb.value);
    }
    
    // Add "Clear All" functionality
    function addClearAllButton() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && !document.querySelector('.clear-all-btn')) {
            const clearAllBtn = document.createElement('button');
            clearAllBtn.className = 'clear-all-btn';
            clearAllBtn.textContent = 'Clear All';
            
            clearAllBtn.addEventListener('click', function() {
                // Uncheck all checkboxes
                filterCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                // Show all products
                showAllProducts();
                // Update filter indicators
                updateFilterIndicators();
            });
            
            // Insert after the first filter section
            const firstFilterSection = sidebar.querySelector('.filter-section');
            if (firstFilterSection) {
                firstFilterSection.parentNode.insertBefore(clearAllBtn, firstFilterSection);
            }
        }
    }
    
    function showAllProducts() {
        const products = document.querySelectorAll('.product-card');
        products.forEach(product => {
            product.style.display = 'block';
        });
        console.log('All products shown');
        updateStyleCount();
    }
    
    function updateStyleCount() {
        const visibleProducts = document.querySelectorAll('.product-card[style*="block"], .product-card:not([style*="none"])');
        const styleCountElement = document.querySelector('.style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${visibleProducts.length} Styles`;
        }
    }
    
    function updateFilterIndicators() {
        const selectedGenders = getSelectedValues('gender');
        const selectedSizes = getSelectedValues('size');
        const selectedBrands = getSelectedValues('category');
        const selectedPrices = getSelectedValues('price');
        
        // Update sidebar header to show active filters
        const sidebarHeader = document.querySelector('.sidebar-header h3');
        if (sidebarHeader) {
            const activeFilters = [];
            if (selectedGenders.length > 0) {
                activeFilters.push(`${selectedGenders.length} gender`);
            }
            if (selectedSizes.length > 0) {
                activeFilters.push(`${selectedSizes.length} size`);
            }
            if (selectedBrands.length > 0) {
                activeFilters.push(`${selectedBrands.length} brand`);
            }
            if (selectedPrices.length > 0) {
                activeFilters.push(`${selectedPrices.length} price`);
            }
            
            if (activeFilters.length > 0) {
                sidebarHeader.textContent = `Refine By (${activeFilters.join(', ')} active)`;
            } else {
                sidebarHeader.textContent = 'Refine By';
            }
        }
    }
    
    // Initialize the clear all button
    addClearAllButton();
    
    // Initialize filters on page load
    applyFilters();
    
    // Cart functionality
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
    
    // Update cart count function
    function updateCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }
    
    // Call setupQuickView when DOM is ready
    setupQuickView();
}); 