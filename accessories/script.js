// E-commerce Page JavaScript
document.addEventListener('DOMContentLoaded', function() {

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

    
    productCards.forEach((card, index) => {
        const imageSlider = card.querySelector('.image-slider');
        const images = imageSlider ? imageSlider.querySelectorAll('img') : [];
        const colorCircles = card.querySelectorAll('.color-circle');
        let currentColor = 'black'; // Default color
        let autoSlideInterval;
        let currentImageIndex = 0; // Track current image index for each color
        
        // Only initialize if we have images
        if (images.length > 0) {
            
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
                    
                    // Restart auto-sliding timer
                    restartAutoSlide();
                }
            }
            
            // Color circle click functionality
            colorCircles.forEach(circle => {
                circle.addEventListener('click', function() {
                    const selectedColor = this.getAttribute('data-color');
                    
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
                }
            }, 3000); // Switch every 3 seconds
        }
        
        // Pause auto-sliding on hover
        card.addEventListener('mouseenter', () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        });
        
        // Resume auto-sliding when mouse leaves
        card.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    });

    // Quick View Sidebar Functionality
    const quickViewSidebar = document.getElementById('quick-view-sidebar');
    const quickViewOverlay = document.getElementById('quick-view-overlay');
    const closeQuickView = document.getElementById('close-quick-view');
    const quickViewButtons = document.querySelectorAll('.quick-view');
    

    
    // Product data for quick view - Accessories
    const productData = {
        1: {
            name: "Elegant Leather Belt",
            price: "$45",
            originalPrice: "$60",
            onSale: true,
            brand: "Fashion Brand",
            gender: "women",
            category: "belts",
            images: [
                { src: "../img/accessories/women/belts/1.avif", color: "brown" },
                { src: "../img/accessories/women/belts/1.0.avif", color: "brown" }
            ],
            colors: [
                { name: "Brown", value: "brown", hex: "#8b4513" },
                { name: "Black", value: "black", hex: "#000" }
            ],
            sizes: ["small", "medium", "large"],
            material: "Genuine Leather",
            dimensions: "Adjustable length",
            capacity: "Fits waist sizes 24-36 inches",
            style: "Casual to Semi-formal"
        },
        2: {
            name: "Classic Women's Watch",
            price: "$120",
            originalPrice: "$150",
            onSale: true,
            brand: "Premium Brand",
            gender: "women",
            category: "watches",
            images: [
                { src: "../img/accessories/women/watches/1.jpg", color: "silver" }
            ],
            colors: [
                { name: "Silver", value: "silver", hex: "#c0c0c0" },
                { name: "Gold", value: "gold", hex: "#ffd700" }
            ],
            sizes: ["small", "medium", "large"],
            material: "Stainless Steel",
            dimensions: "38mm case diameter",
            capacity: "Water resistant up to 30m",
            style: "Classic & Elegant"
        },
        3: {
            name: "Men's Leather Belt",
            price: "$55",
            originalPrice: "$70",
            onSale: true,
            brand: "Designer Brand",
            gender: "men",
            category: "belts",
            images: [
                { src: "../img/accessories/men/belts/1.webp", color: "brown" }
            ],
            colors: [
                { name: "Brown", value: "brown", hex: "#8b4513" },
                { name: "Black", value: "black", hex: "#000" }
            ],
            sizes: ["medium", "large", "extra-large"],
            material: "Genuine Leather",
            dimensions: "Adjustable length",
            capacity: "Fits waist sizes 28-42 inches",
            style: "Classic & Professional"
        },
        4: {
            name: "Stylish Sunglasses",
            price: "$85",
            originalPrice: "$100",
            onSale: true,
            brand: "Fashion Brand",
            gender: "women",
            category: "sunglasses",
            images: [
                { src: "../img/accessories/women/sunglassess/1.avif", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Silver", value: "silver", hex: "#c0c0c0" }
            ],
            sizes: ["small", "medium", "large"],
            material: "High-quality Plastic & Metal",
            dimensions: "One size fits most",
            capacity: "UV400 protection",
            style: "Trendy & Fashionable"
        },
        5: {
            name: "Men's Aviator Sunglasses",
            price: "$95",
            originalPrice: "$120",
            onSale: true,
            brand: "Outdoor Brand",
            gender: "men",
            category: "sunglasses",
            images: [
                { src: "../img/accessories/men/sunglasses/1.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["medium", "large"],
            material: "Metal Frame & Glass Lenses",
            dimensions: "One size fits most",
            capacity: "UV400 protection, Polarized",
            style: "Classic Aviator Style"
        },
        6: {
            name: "Elegant Necklace",
            price: "$75",
            originalPrice: "$90",
            onSale: true,
            brand: "Casual Brand",
            gender: "women",
            category: "Jewelry",
            images: [
                { src: "../img/accessories/women/jewelery/1.webp", color: "gold" }
            ],
            colors: [
                { name: "Gold", value: "gold", hex: "#ffd700" },
                { name: "Silver", value: "silver", hex: "#c0c0c0" }
            ],
            sizes: ["adjustable"],
            material: "Sterling Silver & Gold Plated",
            dimensions: "18\" chain length",
            capacity: "Adjustable length",
            style: "Elegant & Sophisticated"
        },
        7: {
            name: "Men's Sport Watch",
            price: "$150",
            originalPrice: "$180",
            onSale: true,
            brand: "Evening Brand",
            gender: "men",
            category: "watches",
            images: [
                { src: "../img/accessories/men/watches/1.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "Silver", value: "silver", hex: "#c0c0c0" }
            ],
            sizes: ["large"],
            material: "Stainless Steel & Silicone",
            dimensions: "42mm case diameter",
            capacity: "Water resistant up to 100m",
            style: "Sporty & Durable"
        },
        8: {
            name: "Hair Accessories Set",
            price: "$25",
            originalPrice: "$30",
            onSale: true,
            brand: "Professional Brand",
            gender: "women",
            category: "hairaceesories",
            images: [
                { src: "../img/accessories/women/hair access/1.jpg", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ffc0cb" },
                { name: "White", value: "white", hex: "#fff" }
            ],
            sizes: ["one size"],
            material: "Plastic & Fabric",
            dimensions: "Various sizes included",
            capacity: "Set of 10 pieces",
            style: "Cute & Versatile"
        },
                 9: {
             name: "Men's Hair Band",
             price: "$18",
             originalPrice: "$25",
             onSale: true,
             brand: "Style Brand",
             gender: "men",
             category: "hairaceesories",
             images: [
                 { src: "../img/accessories/men/hair access/2.jpg", color: "black" },
                 { src: "../img/accessories/men/hair access/2.0.jpg", color: "black" }
             ],
             colors: [
                 { name: "Black", value: "black", hex: "#000" },
                 { name: "Brown", value: "brown", hex: "#8b4513" }
             ],
             sizes: ["one size"],
             material: "Elastic Fabric",
             dimensions: "Adjustable fit",
             capacity: "One size fits most",
             style: "Sporty & Practical"
         },
                 10: {
             name: "Men's Baseball Cap",
             price: "$25",
             originalPrice: "$30",
             onSale: true,
             brand: "Sports Brand",
             gender: "men",
             category: "hatscaps",
             images: [
                 { src: "../img/accessories/men/hats $caps/caps/1.webp", color: "black" },
                 { src: "../img/accessories/men/hats $caps/caps/1.0.webp", color: "black" }
             ],
             colors: [
                 { name: "Black", value: "black", hex: "#000" },
                 { name: "Grey", value: "grey", hex: "#808080" }
             ],
             sizes: ["one size"],
             material: "Cotton Blend",
             dimensions: "Adjustable fit",
             capacity: "One size fits most",
             style: "Casual & Sporty"
         },
                 11: {
             name: "Men's Comfort Socks",
             price: "$15",
             originalPrice: "$20",
             onSale: true,
             brand: "Comfort Brand",
             gender: "men",
             category: "socks",
             images: [
                 { src: "../img/accessories/men/socks/1.avif", color: "black" },
                 { src: "../img/accessories/men/socks/1.0.avif", color: "black" }
             ],
             colors: [
                 { name: "Black", value: "black", hex: "#000" },
                 { name: "White", value: "white", hex: "#fff" }
             ],
             sizes: ["medium", "large", "extra-large"],
             material: "Cotton Blend",
             dimensions: "Standard sock size",
             capacity: "Fits shoe sizes 8-12",
             style: "Comfortable & Durable"
         },
                 12: {
             name: "Women's Fashion Socks",
             price: "$12",
             originalPrice: "$15",
             onSale: true,
             brand: "Fashion Brand",
             gender: "women",
             category: "socks",
             images: [
                 { src: "../img/accessories/women/socks/1.avif", color: "pink" },
                 { src: "../img/accessories/women/socks/1.0.avif", color: "pink" }
             ],
             colors: [
                 { name: "Pink", value: "pink", hex: "#ffc0cb" },
                 { name: "White", value: "white", hex: "#fff" }
             ],
             sizes: ["small", "medium", "large"],
             material: "Cotton Blend",
             dimensions: "Standard sock size",
             capacity: "Fits shoe sizes 5-9",
             style: "Fashionable & Comfortable"
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
        const product = productData[productId];
        if (!product) {
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
        
        // Add accessory-specific information
        const descriptionElement = document.querySelector('.quick-view-description p');
        if (descriptionElement && product.material) {
            let description = `A beautiful ${product.material.toLowerCase()} accessory. `;
            if (product.dimensions) {
                description += `Dimensions: ${product.dimensions}. `;
            }
            if (product.capacity) {
                description += `${product.capacity}. `;
            }
            if (product.style) {
                description += `Style: ${product.style}.`;
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
        });
    });

    // Quick view functionality (old version - keeping for compatibility)
    const quickViewButtonsOld = document.querySelectorAll('.quick-view');
    
    quickViewButtonsOld.forEach(button => {
        button.addEventListener('click', function() {
            // Add your quick view functionality here
        });
    });

    // Sorting Functionality
    const sortSelect = document.getElementById('sort-select');
    const productGrid = document.querySelector('.product-grid');
    
    if (sortSelect && productGrid) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const products = Array.from(productGrid.querySelectorAll('.product-card'));
            
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
        const selectedCategories = getSelectedValues('category'); // category is used for bag types
        const selectedPrices = getSelectedValues('price');
        

        
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
            
            // Check category filter
            if (selectedCategories.length > 0 && shouldShow) {
                const productCategory = product.getAttribute('data-category');
                if (!selectedCategories.includes(productCategory)) {
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
                        case '0-100':
                            if (productPrice >= 0 && productPrice <= 100) priceInRange = true;
                            break;
                        case '100-200':
                            if (productPrice >= 100 && productPrice <= 200) priceInRange = true;
                            break;
                        case '200-400':
                            if (productPrice >= 200 && productPrice <= 400) priceInRange = true;
                            break;
                        case '400+':
                            if (productPrice >= 400) priceInRange = true;
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
        const selectedCategories = getSelectedValues('category');
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
            if (selectedCategories.length > 0) {
                activeFilters.push(`${selectedCategories.length} category`);
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