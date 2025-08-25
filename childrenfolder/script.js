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
    
    // Product data for quick view - Perfumes
    const productData = {
        1: {
            name: "Sauvage dior 100ml",
            price: "$150",
            brand: "Dior",
            gender: "men",
            images: [
                { src: "../img/perfumes/15.jpg", color: "black" },
                { src: "../img/perfumes/15.1.jpg", color: "blue" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Blue", value: "blue", hex: "#0e50f6ff" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Toilette",
            notes: "Bergamot, Pink Pepper, Ambroxan",
            longevity: "6-8 hours",
            sillage: "Moderate",
            season: "All Year"
        },
        2: {
            name: "strong with you Perfume 100ml",
            price: "$220",
            brand: "Other",
            gender: "men",
            images: [
                { src: "../img/perfumes/23.jpg", color: "red" }
            ],
            colors: [
                { name: "Red", value: "red", hex: "#fd0f36ff" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Cardamom, Pink Pepper, Vanilla",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "Fall/Winter"
        },
        
        3: {
            name: "Khamrah 100ml",
            price: "$125",
            brand: "Lattafa",
            gender: "men",
            images: [
                { src: "../img/perfumes/20.jpg", color: "brown" }
            ],
            colors: [
                { name: "Brown", value: "brown", hex: "#8b4513" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Oud, Amber, Vanilla",
            longevity: "10-12 hours",
            sillage: "Strong",
            season: "Fall/Winter"
        },
        4: {
            name: "Mss dior Eau De Parfum 100ml",
            price: "$105",
            brand: "Dior",
            gender: "women",
            images: [
                { src: "../img/perfumes/14.avif", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#eb9abcff" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Rose, Lily-of-the-Valley, Peony",
            longevity: "6-8 hours",
            sillage: "Moderate",
            season: "Spring/Summer"
        },
        5: {
            name: "Valentino Donna Born in Roma 30ml",
            price: "$95",
            brand: "Valentino",
            gender: "men",
            images: [
                { src: "../img/perfumes/7.webp", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ffc0cb" }
            ],
            sizes: ["30ml"],
            concentration: "Eau de Parfum",
            notes: "Bergamot, Jasmine, Vanilla",
            longevity: "6-8 hours",
            sillage: "Moderate",
            season: "All Year"
        },
        6: {
            name: "Gucci Bloom Eau de Parfum 100ml",
            price: "$180",
            brand: "Gucci",
            gender: "men",
            images: [
                { src: "../img/perfumes/22.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#050505ff" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Tuberose, Jasmine, Rangoon Creeper",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "Spring/Summer"
        },
        7: {
            name: "Prada milano 50ml",
            price: "$140",
            brand: "Other",
            gender: "women",
            images: [
                { src: "../img/perfumes/16.png", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#f7a7c2ff" }
            ],
            sizes: ["50ml"],
            concentration: "Eau de Parfum",
            notes: "Iris, Vanilla, Amber",
            longevity: "6-8 hours",
            sillage: "Moderate",
            season: "All Year"
        },
        8: {
            name: "valentino 100ml",
            price: "$200",
            brand: "Valentino",
            gender: "men",
            images: [
                { src: "../img/perfumes/10.webp", color: "black" },
                { src: "../img/perfumes/10.0.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Bergamot, Lavender, Tonka Bean",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "All Year"
        },
        
        9: {
            name: "Born In Roma Extradose Eau De Parfum 30Ml",
            price: "$120",
            brand: "Valentino",
            gender: "women",
            images: [
                { src: "../img/perfumes/4.webp", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ffc0cb" }
            ],
            sizes: ["30ml"],
            concentration: "Eau de Parfum",
            notes: "Vanilla, Bourbon, Jasmine",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "Fall/Winter"
        },
        10: {
            name: "Chanel Coco Mademoiselle",
            price: "$200",
            brand: "Chanel",
            gender: "women",
            images: [
                { src: "../img/perfumes/12.webp", color: "gold" },
                { src: "../img/perfumes/12.0.webp", color: "gold" }
            ],
            colors: [
                { name: "Gold", value: "gold", hex: "#ffd700" }
            ],
            sizes: ["50ml"],
            concentration: "Eau de Parfum",
            notes: "Orange, Jasmine, Patchouli",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "All Year"
        },
        11: {
            name: "Gucci Guilty Pour Homme",
            price: "$160",
            brand: "Gucci",
            gender: "men",
            images: [
                { src: "../img/perfumes/13.webp", color: "black" },
                { src: "../img/perfumes/13.0.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" }
            ],
            sizes: ["100ml"],
            concentration: "Eau de Toilette",
            notes: "Lemon, Lavender, Patchouli",
            longevity: "6-8 hours",
            sillage: "Moderate",
            season: "All Year"
        },
        12: {
            name: "Good girl perfume 100ml",
            price: "$250",
            brand: "Other",
            gender: "women",
            images: [
                { src: "../img/perfumes/24.jpg", color: "blue" },
                { src: "../img/perfumes/17.jpg", color: "navy" }
            ],
            colors: [
                { name: "Blue", value: "blue", hex: "#474eb9ff" },
                { name: "navy", value: "navy", hex: "#1a2145ff" }

            ],
            sizes: ["100ml"],
            concentration: "Eau de Parfum",
            notes: "Jasmine, Cacao, Tonka Bean",
            longevity: "8-10 hours",
            sillage: "Strong",
            season: "Fall/Winter"
        },

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
                alert('Please select a size');
                return;
            }
            
            const productName = document.getElementById('quick-view-title').textContent;
            const selectedColor = document.querySelector('.quick-view-color-circle.active');
            const colorName = selectedColor ? selectedColor.title : '';
            
            console.log(`Added to cart: ${productName} - Size: ${selectedSize.textContent}, Color: ${colorName}`);
            alert(`Added to cart: ${productName}`);
            
            // Update cart count (you can implement this)
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                const currentCount = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = currentCount + 1;
            }
        });
    }

    // Add to wishlist functionality for quick view
    const addToWishlistQuick = document.getElementById('add-to-wishlist-quick');
    if (addToWishlistQuick) {
        addToWishlistQuick.addEventListener('click', function() {
            const productName = document.getElementById('quick-view-title').textContent;
            console.log(`Added to wishlist: ${productName}`);
            alert(`Added to wishlist: ${productName}`);
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
}); 