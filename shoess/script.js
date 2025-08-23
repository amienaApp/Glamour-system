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
    
    // Product data for quick view
    const productData = {
        1: {
            name: "Elegant Heels",
            price: "$89",
            category: "Women's Heels",
            gender: "women",
            images: [
                { src: "../img/shoes/womenshoes/1.avif", color: "black" },
                { src: "../img/shoes/womenshoes/1.0.avif", color: "black" },
                { src: "../img/shoes/womenshoes/1.1.avif", color: "gold" },
                { src: "../img/shoes/womenshoes/1.1.0.avif", color: "gold" },
                { src: "../img/shoes/womenshoes/1.2.avif", color: "white" },
                { src: "../img/shoes/womenshoes/1.2.0.avif", color: "white" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Gold", value: "gold", hex: "#FFD700" },
                { name: "White", value: "white", hex: "#f8f8f8ff" }
            ],
            
                         soldOutSizes: ["35"],
            material: "Leather",
            heelHeight: "3 inches",
            style: "Classic",
            season: "All Year"
        },
        2: {
            name: "Classic Flats",
            price: "$45",
            category: "Boys' Flats",
            gender: "boys",
            images: [
                { src: "../img/shoes/boy/4.jpg", color: "black" },
                { src: "../img/shoes/boy/4.0.png", color: "black" },
                { src: "../img/shoes/boy/4.2.avif", color: "blue" },
                { src: "../img/shoes/boy/4.2.1.jpeg", color: "blue" },
                { src: "../img/shoes/boy/4.1.avif", color: "gold" },
                { src: "../img/shoes/boy/4.1.0.jpeg", color: "gold" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#070606ff" },
                { name: "Blue", value: "blue", hex: "#5a46efff" },
                { name: "Gold", value: "gold", hex: "#FFD700" }
            ],
            soldOutSizes: [],
            material: "Canvas",
            style: "Casual",
            season: "All Year"
        },
        3: {
            name: "Comfortable Flats",
            price: "$45",
            category: "Women's Flats",
            gender: "women",
            images: [
                { src: "../img/shoes/womenshoes/5.jpg", color: "black" },
                { src: "../img/shoes/womenshoes/5.1.avif", color: "white" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "White", value: "white", hex: "#fbfbfbff" }
            ],
                         soldOutSizes: ["36"],
            material: "Leather",
            style: "Comfortable",
            season: "All Year"
        },
        4: {
            name: "Leather Flat",
            price: "$120",
            category: "Men's Flats",
            gender: "men",
            images: [
                { src: "../img/shoes/menshoes/2.jpg", color: "black" },
                { src: "../img/shoes/menshoes/2.0.jpg", color: "black" },
                { src: "../img/shoes/menshoes/2.1.jpg", color: "brown" },
                { src: "../img/shoes/menshoes/2.1.0.jpg", color: "brown" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Brown", value: "brown", hex: "#964B00" }
            ],
                         soldOutSizes: ["39"],
            material: "Genuine Leather",
            style: "Formal",
            season: "All Year"
        },
        5: {
            name: "Elegant Boots",
            price: "$55",
            category: "Women's Boots",
            gender: "women",
            images: [
                { src: "../img/shoes/womenshoes/8.jpg", color: "black" },
                { src: "../img/shoes/womenshoes/8.0.png", color: "black" },
                { src: "../img/shoes/womenshoes/8.1.png", color: "beige" },
                { src: "../img/shoes/womenshoes/8.1.0.png", color: "beige" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "Beige", value: "beige", hex: "#eee8c4ff" }
            ],
            soldOutSizes: [],
            material: "Synthetic Leather",
            style: "Fashion",
            season: "Fall/Winter"
        },
        6: {
            name: "Flat Shoes",
            price: "$85",
            category: "Men's Flats",
            gender: "men",
            images: [
                { src: "../img/shoes/menshoes/3.2.jpg", color: "black" },
                { src: "../img/shoes/menshoes/3.2.0.jpg", color: "black" },
                { src: "../img/shoes/menshoes/3.jpg", color: "brown" },
                { src: "../img/shoes/menshoes/3.0.jpg", color: "brown" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Brown", value: "brown", hex: "#964B00" }
            ],
                         soldOutSizes: ["40"],
            material: "Leather",
            style: "Casual",
            season: "All Year"
        },
        7: {
            name: "Classic Heel Shoes",
            price: "$25",
            category: "Girls' Heels",
            gender: "girls",
            images: [
                { src: "../img/shoes/girls/1.jpg", color: "black" },
                { src: "../img/shoes/girls/1.0.webp", color: "black" },
                { src: "../img/shoes/girls/1.1.jpg", color: "pink" },
                { src: "../img/shoes/girls/1.2.jpg", color: "white" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "Pink", value: "pink", hex: "#ffc0cb" },
                { name: "White", value: "white", hex: "#fafafaff" }
            ],
                         soldOutSizes: ["6"],
            material: "Synthetic",
            style: "Party",
            season: "All Year"
        },
        8: {
            name: "Boys Sneakers",
            price: "$30",
            category: "Boys' Sneakers",
            gender: "boys",
            images: [
                { src: "../img/shoes/boy/1.avif", color: "red" },
                { src: "../img/shoes/boy/1.0.avif", color: "red" },
                { src: "../img/shoes/boy/1.1.avif", color: "orange" },
                { src: "../img/shoes/boy/1.1.0.avif", color: "orange" },
                { src: "../img/shoes/boy/1.2.avif", color: "green" },
                { src: "../img/shoes/boy/1.2.0.avif", color: "green" }
            ],
            colors: [
                { name: "Red", value: "red", hex: "#f70000ff" },
                { name: "Orange", value: "orange", hex: "#fb683bff" },
                { name: "Green", value: "green", hex: "#58e368ff" }
            ],
            soldOutSizes: [],
            material: "Canvas",
            style: "Athletic",
            season: "All Year"
        },
        9: {
            name: "Infant Shoes",
            price: "$18",
            category: "Infant Sneakers",
            gender: "infant",
            images: [
                { src: "../img/shoes/infant/8.jpg", color: "brown" },
                { src: "../img/shoes/infant/8.0.avif", color: "brown" },
                { src: "../img/shoes/infant/8.1.avif", color: "pink" },
                { src: "../img/shoes/infant/8.1.0.avif", color: "pink" }
            ],
            colors: [
                { name: "Brown", value: "brown", hex: "#7c5c37ff" },
                { name: "Pink", value: "pink", hex: "#f986baff" }
            ],
                         soldOutSizes: ["1-4M"],
            material: "Soft Fabric",
            style: "Comfortable",
            season: "All Year"
        },
        10: {
            name: "Girl Boots",
            price: "$32",
            category: "Girls' Boots",
            gender: "girls",
            images: [
                { src: "../img/shoes/girls/5.jpg", color: "white" },
                { src: "../img/shoes/girls/5.1.jpg", color: "pink" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Pink", value: "pink", hex: "#FFC0CB" }
            ],
            
            soldOutSizes: [],
            material: "Synthetic",
            style: "Fashion",
            season: "Fall/Winter"
        },
        11: {
            name: "Baby Shoes",
            price: "$25",
            category: "Infant Sandals",
            gender: "infant",
            images: [
                { src: "../img/shoes/infant/7.jpg", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#f587dcff" }
            ],
            
            soldOutSizes: [],
            material: "Soft Fabric",
            style: "Comfortable",
            season: "Spring/Summer"
        },
        12: {
            name: "Men Sandals",
            price: "$28",
            category: "Men's Sandals",
            gender: "men",
            images: [
                { src: "../img/shoes/menshoes/5.1.webp", color: "white" },
                { src: "../img/shoes/menshoes/5.1.0.webp", color: "white" },
                { src: "../img/shoes/menshoes/5.webp", color: "black" },
                { src: "../img/shoes/menshoes/5.0.webp", color: "black" },
                { src: "../img/shoes/menshoes/5.2.webp", color: "brown" },
                { src: "../img/shoes/menshoes/5.2.0.webp", color: "brown" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "Brown", value: "brown", hex: "#913224ff" }
            ],
            
                         soldOutSizes: ["38"],
            material: "Leather",
            style: "Casual",
            season: "Spring/Summer"
        }
    };

    // Open Quick View
    console.log('Setting up quick view buttons. Found:', quickViewButtons.length, 'buttons');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
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
        const product = productData[productId];
        console.log('Found product:', product);
        if (!product) {
            console.log('No product found for ID:', productId);
            return;
        }

        // Populate product data
        document.getElementById('quick-view-title').textContent = product.name;
        document.getElementById('quick-view-price').textContent = product.price;
        
        // Add category information if available
        const categoryElement = document.getElementById('quick-view-category');
        if (categoryElement && product.category) {
            categoryElement.textContent = product.category;
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

        // Populate sizes based on gender
        const sizeSelection = document.getElementById('quick-view-size-selection');
        sizeSelection.innerHTML = '';
        
        // Define size ranges based on gender
        let availableSizes = [];
        if (product.gender === 'women') {
            availableSizes = ["35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46"];
        } else if (product.gender === 'men') {
            availableSizes = ["35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46"];
        } else if (product.gender === 'boys' || product.gender === 'girls') {
            availableSizes = ["6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34"];
        } else if (product.gender === 'infant') {
            availableSizes = ["1-4M", "5-8M", "9-13M"];
        }
        
        availableSizes.forEach(size => {
            const sizeBtn = document.createElement('button');
            sizeBtn.className = 'quick-view-size-btn';
            sizeBtn.textContent = size;
            
            if (product.soldOutSizes.includes(size)) {
                sizeBtn.classList.add('sold-out');
            } else {
                sizeBtn.addEventListener('click', () => {
                    sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                    sizeBtn.classList.add('active');
                });
            }
            
            sizeSelection.appendChild(sizeBtn);
        });

        // Add shoe-specific information
        const descriptionElement = document.querySelector('.quick-view-description p');
        if (descriptionElement && product.material) {
            let description = `A stylish ${product.category.toLowerCase()} made of ${product.material}. `;
            if (product.style) {
                description += `Features a ${product.style.toLowerCase()} design. `;
            }
            if (product.season) {
                description += `Perfect for ${product.season.toLowerCase()}. `;
            }
            if (product.heelHeight) {
                description += `Heel height: ${product.heelHeight}. `;
            }
            description += `Comfortable and durable for everyday wear.`;
            descriptionElement.textContent = description;
        }

        // Show sidebar
        console.log('Showing quick view sidebar...');
        quickViewSidebar.classList.add('active');
        quickViewOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        console.log('Sidebar classes:', quickViewSidebar.className);
        console.log('Overlay classes:', quickViewOverlay.className);
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
            
            const productId = this.getAttribute('data-product-id');
            const productName = document.getElementById('quick-view-title').textContent;
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
            const imageSlider = productCard.querySelector('.image-slider');
            const images = imageSlider.querySelectorAll('img');
            const selectedColor = this.getAttribute('data-color');
            
            // Remove active class from all circles in this product
            circles.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked circle
            this.classList.add('active');
            
            // Show only images for the selected color
            images.forEach(img => {
                const imageColor = img.getAttribute('data-color');
                if (imageColor === selectedColor) {
                    img.style.display = 'block';
                    img.classList.add('active');
                } else {
                    img.style.display = 'none';
                    img.classList.remove('active');
                }
            });
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-bag');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add your cart functionality here
            console.log('Added to cart:', this.closest('.product-card').querySelector('.product-name').textContent);
        });
    });

         // Quick view functionality (old version - keeping for compatibility)
     const quickViewButtonsOld = document.querySelectorAll('.quick-view');
     
     quickViewButtonsOld.forEach(button => {
         button.addEventListener('click', function() {
             // Add your quick view functionality here
             console.log('Quick view:', this.closest('.product-card').querySelector('.product-name').textContent);
         });
     });

     // Sidebar Filtering Functionality
     console.log('Setting up sidebar filters...');
     
     // Get all filter checkboxes
     const filterCheckboxes = document.querySelectorAll('.sidebar input[type="checkbox"]');
     const productGrid = document.querySelector('.product-grid');
     
     // Add event listeners to all filter checkboxes
     filterCheckboxes.forEach(checkbox => {
         checkbox.addEventListener('change', applyFilters);
     });
     
     function applyFilters() {
         console.log('Applying filters...');
         
         // Get all selected filter values
         const selectedGenders = getSelectedValues('gender[]');
         const selectedChildren = getSelectedValues('children[]');
         const selectedSizes = getSelectedValues('size[]');
         const selectedCategories = getSelectedValues('category[]');
         const selectedColors = getSelectedValues('color[]');
         const selectedPrices = getSelectedValues('price[]');
         
         console.log('Selected filters:', {
             genders: selectedGenders,
             children: selectedChildren,
             sizes: selectedSizes,
             categories: selectedCategories,
             colors: selectedColors,
             prices: selectedPrices
         });
         
         // Filter products
         document.querySelectorAll('.product-card').forEach(card => {
             const productId = card.getAttribute('data-product-id');
             const product = productData[productId];
             
             if (!product) {
                 card.style.display = 'none';
                 return;
             }
             
             let shouldShow = true;
             
             // Gender filter
             if (selectedGenders.length > 0 || selectedChildren.length > 0) {
                 const allSelectedGenders = [...selectedGenders, ...selectedChildren];
                 if (!allSelectedGenders.includes(product.gender)) {
                     shouldShow = false;
                 }
             }
             
             // Category filter
             if (selectedCategories.length > 0 && shouldShow) {
                 const productCategory = product.category.toLowerCase();
                 const matchesCategory = selectedCategories.some(category => {
                     switch(category) {
                         case 'heels':
                             return productCategory.includes('heel');
                         case 'boots':
                             return productCategory.includes('boot');
                         case 'sandals':
                             return productCategory.includes('sandal');
                         case 'flats':
                             return productCategory.includes('flat');
                         case 'sneakers':
                             return productCategory.includes('sneaker');
                         case 'sports-shoes':
                             return productCategory.includes('sport');
                         case 'slippers':
                             return productCategory.includes('slipper');
                         default:
                             return false;
                     }
                 });
                 
                 if (!matchesCategory) {
                     shouldShow = false;
                 }
             }
             
             // Color filter
             if (selectedColors.length > 0 && shouldShow) {
                 const productColors = product.colors.map(color => color.value);
                 const matchesColor = selectedColors.some(color => productColors.includes(color));
                 
                 if (!matchesColor) {
                     shouldShow = false;
                 } else {
                     // Filter images to show only selected colors
                     const imageSlider = card.querySelector('.image-slider');
                     const allImages = imageSlider.querySelectorAll('img');
                     const colorCircles = card.querySelectorAll('.color-circle');
                     
                     // Hide all images first
                     allImages.forEach(img => {
                         img.style.display = 'none';
                         img.classList.remove('active');
                     });
                     
                     // Show only images that match the selected colors
                     allImages.forEach(img => {
                         const imageColor = img.getAttribute('data-color');
                         if (imageColor && selectedColors.includes(imageColor)) {
                             img.style.display = 'block';
                         }
                     });
                     
                     // Update color circles to show only selected colors
                     colorCircles.forEach(circle => {
                         const circleColor = circle.getAttribute('data-color');
                         if (selectedColors.includes(circleColor)) {
                             circle.style.display = 'inline-block';
                         } else {
                             circle.style.display = 'none';
                         }
                     });
                     
                     // Set the first available color as active and show its images
                     const firstAvailableColor = selectedColors.find(color => 
                         productColors.includes(color)
                     );
                     if (firstAvailableColor) {
                         // Remove active class from all circles
                         colorCircles.forEach(circle => circle.classList.remove('active'));
                         
                         // Add active class to the first available color
                         const activeCircle = card.querySelector(`[data-color="${firstAvailableColor}"]`);
                         if (activeCircle) {
                             activeCircle.classList.add('active');
                         }
                         
                         // Show only images for the active color
                         allImages.forEach(img => {
                             const imageColor = img.getAttribute('data-color');
                             if (imageColor === firstAvailableColor) {
                                 img.style.display = 'block';
                                 img.classList.add('active');
                             } else {
                                 img.style.display = 'none';
                                 img.classList.remove('active');
                             }
                         });
                     }
                 }
             } else {
                 // If no color filter is applied, show all images and colors
                 const imageSlider = card.querySelector('.image-slider');
                 const allImages = imageSlider.querySelectorAll('img');
                 const colorCircles = card.querySelectorAll('.color-circle');
                 
                 // Show all images
                 allImages.forEach(img => {
                     img.style.display = 'block';
                 });
                 
                 // Show all color circles
                 colorCircles.forEach(circle => {
                     circle.style.display = 'inline-block';
                 });
                 
                 // Reset to first color as active
                 const firstCircle = colorCircles[0];
                 if (firstCircle) {
                     colorCircles.forEach(circle => circle.classList.remove('active'));
                     firstCircle.classList.add('active');
                     
                     // Show only first color images
                     const firstColor = firstCircle.getAttribute('data-color');
                     allImages.forEach(img => {
                         const imageColor = img.getAttribute('data-color');
                         if (imageColor === firstColor) {
                             img.style.display = 'block';
                             img.classList.add('active');
                         } else {
                             img.style.display = 'none';
                             img.classList.remove('active');
                         }
                     });
                 }
             }
             
             // Price filter
             if (selectedPrices.length > 0 && shouldShow) {
                 const productPrice = parseFloat(product.price.replace('$', ''));
                 const matchesPrice = selectedPrices.some(priceRange => {
                     switch(priceRange) {
                         case '0-25':
                             return productPrice >= 0 && productPrice <= 25;
                         case '25-50':
                             return productPrice > 25 && productPrice <= 50;
                         case '50-75':
                             return productPrice > 50 && productPrice <= 75;
                         case '75-100':
                             return productPrice > 75 && productPrice <= 100;
                         case '100+':
                             return productPrice > 100;
                         default:
                             return false;
                     }
                 });
                 
                 if (!matchesPrice) {
                     shouldShow = false;
                 }
             }
             
             // Size filter (check if product has any of the selected sizes available)
             if (selectedSizes.length > 0 && shouldShow) {
                 const productGender = product.gender;
                 let availableSizes = [];
                 
                 if (productGender === 'women' || productGender === 'men') {
                     availableSizes = ["35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46"];
                 } else if (productGender === 'boys' || productGender === 'girls') {
                     availableSizes = ["6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34"];
                 } else if (productGender === 'infant') {
                     availableSizes = ["1-4M", "5-8M", "9-13M"];
                 }
                 
                 // Remove sold out sizes
                 const availableSizesFiltered = availableSizes.filter(size => !product.soldOutSizes.includes(size));
                 
                 const matchesSize = selectedSizes.some(size => availableSizesFiltered.includes(size));
                 
                 if (!matchesSize) {
                     shouldShow = false;
                 }
             }
             
             // Show/hide product card
             if (shouldShow) {
                 card.style.display = 'block';
                 card.style.opacity = '1';
                 card.style.transform = 'scale(1)';
             } else {
                 card.style.display = 'none';
                 card.style.opacity = '0';
                 card.style.transform = 'scale(0.8)';
             }
         });
         
                   // Update style count
          updateStyleCount();
          
                     // Apply current sort order to visible products
           const currentSort = document.getElementById('sort-select').value;
           if (currentSort) {
               sortProducts(currentSort);
           }
      }
     
     function getSelectedValues(name) {
         const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
         return Array.from(checkboxes).map(cb => cb.value);
     }
     
     function updateStyleCount() {
         const visibleProducts = document.querySelectorAll('.product-card[style*="display: block"], .product-card:not([style*="display: none"])');
         const styleCountElement = document.querySelector('.style-count');
         
         if (styleCountElement) {
             styleCountElement.textContent = `${visibleProducts.length} Styles`;
         }
     }
     
           // Clear all filters function
      function clearAllFilters() {
          filterCheckboxes.forEach(checkbox => {
              checkbox.checked = false;
          });
          
          // Reset all product images and color circles to show everything
          document.querySelectorAll('.product-card').forEach(card => {
              const imageSlider = card.querySelector('.image-slider');
              const allImages = imageSlider.querySelectorAll('img');
              const colorCircles = card.querySelectorAll('.color-circle');
              
              // Show all images initially
              allImages.forEach(img => {
                  img.style.display = 'block';
              });
              
              // Show all color circles
              colorCircles.forEach(circle => {
                  circle.style.display = 'inline-block';
              });
              
              // Reset to first color as active
              const firstCircle = colorCircles[0];
              if (firstCircle) {
                  colorCircles.forEach(circle => circle.classList.remove('active'));
                  firstCircle.classList.add('active');
                  
                  // Show only first color images
                  const firstColor = firstCircle.getAttribute('data-color');
                  allImages.forEach(img => {
                      const imageColor = img.getAttribute('data-color');
                      if (imageColor === firstColor) {
                          img.style.display = 'block';
                          img.classList.add('active');
                      } else {
                          img.style.display = 'none';
                          img.classList.remove('active');
                      }
                  });
              }
          });
          
          applyFilters();
      }

      // Sorting functionality
      console.log('Setting up sorting functionality...');
      const sortSelect = document.getElementById('sort-select');
      console.log('Sort select element found:', sortSelect);
      
      if (sortSelect) {
          console.log('Adding event listener to sort select');
          sortSelect.addEventListener('change', function() {
              const sortValue = this.value;
              console.log('Sort value changed to:', sortValue);
              sortProducts(sortValue);
          });
      } else {
          console.error('❌ Sort select element not found!');
      }

      function sortProducts(sortType) {
          console.log('sortProducts called with:', sortType);
          const productGrid = document.querySelector('.product-grid');
          const products = Array.from(productGrid.querySelectorAll('.product-card'));
          
          console.log('Total products found:', products.length);
          
          // Filter out hidden products (those that don't match current filters)
          const visibleProducts = products.filter(product => {
              return product.style.display !== 'none';
          });
          
          console.log('Visible products:', visibleProducts.length);
          
          // Sort the visible products
          visibleProducts.sort((a, b) => {
              const productA = productData[a.getAttribute('data-product-id')];
              const productB = productData[b.getAttribute('data-product-id')];
              
              if (!productA || !productB) return 0;
              
                             switch(sortType) {
                   case 'featured':
                       // Featured order (original order - no sorting)
                       return 0;
                       
                   case 'newest':
                       // Sort by product ID (assuming higher ID = newer)
                       return parseInt(b.getAttribute('data-product-id')) - parseInt(a.getAttribute('data-product-id'));
                       
                   case 'price-low':
                       // Sort by price low to high
                       const priceA = parseFloat(productA.price.replace('$', ''));
                       const priceB = parseFloat(productB.price.replace('$', ''));
                       return priceA - priceB;
                       
                   case 'price-high':
                       // Sort by price high to low
                       const priceHighA = parseFloat(productA.price.replace('$', ''));
                       const priceHighB = parseFloat(productB.price.replace('$', ''));
                       return priceHighB - priceHighA;
                       
                   case 'popular':
                       // Sort by popularity (using product ID as a simple proxy)
                       // In a real app, you'd use actual popularity data
                       return parseInt(a.getAttribute('data-product-id')) - parseInt(b.getAttribute('data-product-id'));
                       
                   default:
                       // Default to featured order (no sorting)
                       return 0;
               }
          });
          
          // Reorder the products in the DOM
          visibleProducts.forEach(product => {
              productGrid.appendChild(product);
          });
          
          console.log(`✅ Products sorted by: ${sortType}`);
      }
      
             // Initialize sort select to show featured order on page load
       setTimeout(() => {
           console.log('Initializing sort select...');
           const sortSelect = document.getElementById('sort-select');
           if (sortSelect) {
               console.log('✅ Sort select initialized');
               // Set to featured (which shows original order) and don't apply sorting
               sortSelect.value = 'featured';
           } else {
               console.error('❌ Sort select not found for initialization');
           }
       }, 100);
     
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
}); 