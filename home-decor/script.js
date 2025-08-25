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
    
    // Product data for quick view - Home Decor Products
    const productData = {
        1: {
            name: "Luxury Bedroom Furniture Set",
            price: "$899",
            brand: "Luxury Home",
            category: "bedroom",
            images: [
                { src: "../img/home-decor/bedroom/1.avif", color: "purple" },
                
            ],
            colors: [
                { name: "purple", value: "purple", hex: "#8b136fff" }
            ],
            sizes: ["Queen", "King", "California King"],
            dimensions: "72\" W x 84\" L x 42\" H",
            material: "Solid Wood",
            style: "Traditional",
            room: "Bedroom",
            assembly: "Required",
            weight: "85 lbs",
            warranty: "5 Years",
            care: "Wipe with damp cloth"
        },
        2: {
            name: "Elegant Bedroom Decor Set",
            price: "$245",
            brand: "Elegant Home",
            category: "bedroom",
            images: [
                { src: "../img/home-decor/bedroom/7.jpg", color: "white" }
                
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "24\" W x 18\" L x 12\" H",
            material: "Premium Fabric",
            style: "Contemporary",
            room: "Bedroom",
            assembly: "No Assembly Required",
            weight: "8 lbs",
            warranty: "1 Year",
            care: "Spot clean only"
        },
        3: {
            name: "Cozy Bedroom Accessories",
            price: "$89",
            brand: "Cozy Home",
            category: "bedroom",
            images: [
                { src: "../img/home-decor/bedroom/6.jpg", color: "brown" }
                
            ],
            colors: [
                { name: "brown", value: "brown", hex: " #964B00" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "16\" W x 12\" L x 8\" H",
            material: "Soft Textile",
            style: "Cozy",
            room: "Bedroom",
            assembly: "No Assembly Required",
            weight: "3 lbs",
            warranty: "6 Months",
            care: "Machine washable"
        },
        4: {
            name: "Modern Living Room Furniture Set",
            price: "$1,299",
            brand: "Modern Home",
            category: "livingroom",
            images: [
                { src: "../img/home-decor/livingroom/1.avif", color: "beige" }
                
            ],
            colors: [
                { name: "Beige", value: "beige", hex: "#f5f5dc" }
            ],
            sizes: ["3-Seater", "5-Seater", "7-Seater"],
            dimensions: "84\" W x 35\" L x 32\" H",
            material: "Premium Fabric & Wood",
            style: "Modern",
            room: "Living Room",
            assembly: "Professional Assembly Required",
            weight: "120 lbs",
            warranty: "3 Years",
            care: "Professional cleaning recommended"
        },
        5: {
            name: "Stylish Living Room",
            price: "$600",
            brand: "Stylish Home",
            category: "livingroom",
            images: [
                { src: "../img/home-decor/livingroom/3.jpg", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ff69B4" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "28\" W x 20\" L x 15\" H",
            material: "Premium Materials",
            style: "Contemporary",
            room: "Living Room",
            assembly: "Minimal Assembly Required",
            weight: "12 lbs",
            warranty: "1 Year",
            care: "Dust regularly"
        },
        6: {
            name: "Elegant Living Room",
            price: "$600",
            brand: "Elegant Home",
            category: "livingroom",
            images: [
                { src: "../img/home-decor/livingroom/5.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "32\" W x 24\" L x 18\" H",
            material: "Premium Fabric",
            style: "Elegant",
            room: "Living Room",
            assembly: "No Assembly Required",
            weight: "15 lbs",
            warranty: "1 Year",
            care: "Professional cleaning recommended"
        },
        7: {
            name: "Classic Dining Room Furniture Set",
            price: "$899",
            brand: "Classic Home",
            category: "diningroom",
            images: [
                { src: "../img/home-decor/diningarea/1.jpg", color: "beige" }
            ],
            colors: [
                { name: "Beige", value: "beige", hex: "#bc9f8f" }
            ],
            sizes: ["4-Seater", "6-Seater", "8-Seater"],
            dimensions: "60\" W x 36\" L x 30\" H",
            material: "Solid Wood",
            style: "Classic",
            room: "Dining Room",
            assembly: "Professional Assembly Required",
            weight: "95 lbs",
            warranty: "5 Years",
            care: "Wipe with damp cloth"
        },
        8: {
            name: "Elegant Dining Room Decor",
            price: "$750",
            brand: "Elegant Home",
            category: "diningroom",
            images: [
                { src: "../img/home-decor/diningarea/3.jpg", color: "silver" },
                { src: "../img/home-decor/diningarea/4.jpg", color: "silver" }
            ],
            colors: [
                { name: "Silver", value: "silver", hex: "#c0c0c0" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "20\" W x 16\" L x 12\" H",
            material: "Premium Metal",
            style: "Elegant",
            room: "Dining Room",
            assembly: "No Assembly Required",
            weight: "8 lbs",
            warranty: "2 Years",
            care: "Polish with soft cloth"
        },
        9: {
            name: "Luxury Crystal Chandelier",
            price: "$80",
            brand: "Luxury Lights",
            category: "lighting",
            images: [
                { src: "../img/home-decor/light/1.avif", color: "crystal" }
            ],
            colors: [
                { name: "Crystal", value: "crystal", hex: "#e6e6fa" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "24\" Diameter x 36\" Height",
            material: "Crystal & Metal",
            style: "Luxury",
            room: "Any Room",
            assembly: "Professional Installation Required",
            weight: "25 lbs",
            warranty: "3 Years",
            care: "Dust regularly"
        },
        10: {
            name: "Modern Table Lamp",
            price: "$89",
            brand: "Modern Lights",
            category: "lighting",
            images: [
                { src: "../img/home-decor/light/3.webp", color: "white" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#f8f5f5ff" }
            ],
            sizes: ["Standard"],
            dimensions: "12\" W x 12\" L x 24\" H",
            material: "Metal & Fabric",
            style: "Modern",
            room: "Any Room",
            assembly: "No Assembly Required",
            weight: "5 lbs",
            warranty: "1 Year",
            care: "Wipe with damp cloth"
        },
        11: {
            name: "Elegant Wall Sconce",
            price: "$67",
            brand: "Elegant Lights",
            category: "lighting",
            images: [
                { src: "../img/home-decor/light/4.webp", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ff69B4" }
            ],
            sizes: ["Standard"],
            dimensions: "8\" W x 6\" L x 12\" H",
            material: "Metal & Glass",
            style: "Elegant",
            room: "Any Room",
            assembly: "Professional Installation Required",
            weight: "3 lbs",
            warranty: "1 Year",
            care: "Dust regularly"
        },
        12: {
            name: "Abstract Modern Painting",
            price: "$234",
            brand: "Art Gallery",
            category: "artwork",
            images: [
                { src: "../img/home-decor/artwork/1.jpg", color: "multicolor" }
            ],
            colors: [
                { name: "Multicolor", value: "multicolor", hex: "linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4)" }
            ],
            sizes: ["24\" x 36\"", "36\" x 48\"", "48\" x 60\""],
            dimensions: "24\" W x 36\" L x 2\" D",
            material: "Canvas & Acrylic",
            style: "Abstract",
            room: "Any Room",
            assembly: "No Assembly Required",
            weight: "8 lbs",
            warranty: "1 Year",
            care: "Dust with soft brush"
        },
        13: {
            name: "Serene Landscape Artwork",
            price: "$189",
            brand: "Nature Art",
            category: "artwork",
            images: [
                { src: "../img/home-decor/artwork/3.jpg", color: "lightgrey" }
            ],
            colors: [
                { name: "Light Grey", value: "lightgrey", hex: "#787b78ff" }
            ],
            sizes: ["20\" x 30\"", "30\" x 40\"", "40\" x 50\""],
            dimensions: "20\" W x 30\" L x 2\" D",
            material: "Canvas & Oil Paint",
            style: "Landscape",
            room: "Any Room",
            assembly: "No Assembly Required",
            weight: "6 lbs",
            warranty: "1 Year",
            care: "Dust with soft brush"
        },
        14: {
            name: "Contemporary Art Piece",
            price: "$312",
            brand: "Modern Art",
            category: "artwork",
            images: [
                { src: "../img/home-decor/artwork/5.jpg", color: "beige" }
            ],
            colors: [
                { name: "Beige", value: "beige", hex: "#dfd7c4ff" }
            ],
            sizes: ["28\" x 40\"", "40\" x 56\"", "56\" x 72\""],
            dimensions: "28\" W x 40\" L x 2\" D",
            material: "Canvas & Mixed Media",
            style: "Contemporary",
            room: "Any Room",
            assembly: "No Assembly Required",
            weight: "10 lbs",
            warranty: "1 Year",
            care: "Dust with soft brush"
        },
        15: {
            name: "Modern Kitchen Decor Set",
            price: "$400",
            brand: "Kitchen Style",
            category: "kitchen",
            images: [
                { src: "../img/home-decor/kitchen/1.avif", color: "beige" }
            ],
            colors: [
                { name: "Beige", value: "beige", hex: "#dfd7c4ff" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "18\" W x 14\" L x 10\" H",
            material: "Stainless Steel & Wood",
            style: "Modern",
            room: "Kitchen",
            assembly: "Minimal Assembly Required",
            weight: "12 lbs",
            warranty: "2 Years",
            care: "Wipe with damp cloth"
        },
        16: {
            name: "Culinary Kitchen Decor",
            price: "$600",
            brand: "Culinary Home",
            category: "kitchen",
            images: [
                { src: "../img/home-decor/kitchen/5.jpg", color: "white" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["Standard", "Large"],
            dimensions: "24\" W x 18\" L x 12\" H",
            material: "Premium Materials",
            style: "Culinary",
            room: "Kitchen",
            assembly: "No Assembly Required",
            weight: "15 lbs",
            warranty: "2 Years",
            care: "Wipe with damp cloth"
        },
        17: {
            name: "Comfortable Living Room Sofa",
            price: "$789",
            brand: "Comfort Home",
            category: "livingroom",
            images: [
                { src: "../img/home-decor/livingroom/7.jpg", color: "pink" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#ff69B4" }
            ],
            sizes: ["3-Seater", "5-Seater"],
            dimensions: "84\" W x 35\" L x 32\" H",
            material: "Premium Fabric & Wood",
            style: "Comfortable",
            room: "Living Room",
            assembly: "Professional Assembly Required",
            weight: "110 lbs",
            warranty: "3 Years",
            care: "Professional cleaning recommended"
        },
        18: {
            name: "Elegant Bedroom Light Fixture",
            price: "$123",
            brand: "Bedroom Lights",
            category: "lighting",
            images: [
                { src: "../img/home-decor/light/6.0.webp", color: "green" }
            ],
            colors: [
                { name: "Green", value: "green", hex: "#2d5d5cff" }
            ],
            sizes: ["Standard"],
            dimensions: "16\" Diameter x 24\" Height",
            material: "Metal & Glass",
            style: "Elegant",
            room: "Bedroom",
            assembly: "Professional Installation Required",
            weight: "8 lbs",
            warranty: "2 Years",
            care: "Dust regularly"
        },
        19: {
            name: "Elegant Dining Room Chairs Set",
            price: "$900",
            brand: "Dining Elegance",
            category: "diningroom",
            images: [
                { src: "../img/home-decor/diningarea/5.jpg", color: "walnut" }
            ],
            colors: [
                { name: "Walnut", value: "walnut", hex: "#795c79ff" }
            ],
            sizes: ["4-Chair Set", "6-Chair Set", "8-Chair Set"],
            dimensions: "18\" W x 20\" L x 36\" H per chair",
            material: "Solid Wood",
            style: "Elegant",
            room: "Dining Room",
            assembly: "Minimal Assembly Required",
            weight: "120 lbs",
            warranty: "5 Years",
            care: "Wipe with damp cloth"
        },
        20: {
            name: "Modern Wall Art Collection",
            price: "$178",
            brand: "Wall Art",
            category: "artwork",
            images: [
                { src: "../img/home-decor/artwork/7.jpg", color: "white" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#d0c3c0ff" }
            ],
            sizes: ["16\" x 24\"", "24\" x 36\"", "36\" x 48\""],
            dimensions: "16\" W x 24\" L x 1\" D",
            material: "Canvas & Acrylic",
            style: "Modern",
            room: "Any Room",
            assembly: "No Assembly Required",
            weight: "4 lbs",
            warranty: "1 Year",
            care: "Dust with soft brush"
        }
    };

    // Open Quick View
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
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
        if (!product) return;

        // Populate product data
        document.getElementById('quick-view-title').textContent = product.name;
        document.getElementById('quick-view-price').textContent = product.price;
        
        // Populate additional product details if elements exist
        const brandElement = document.getElementById('quick-view-brand');
        if (brandElement && product.brand) {
            brandElement.textContent = product.brand;
        }
        
        // Populate product details
        const materialElement = document.getElementById('quick-view-material');
        if (materialElement && product.material) {
            materialElement.textContent = product.material;
        }
        
        const styleElement = document.getElementById('quick-view-style');
        if (styleElement && product.style) {
            styleElement.textContent = product.style;
        }
        
        const roomElement = document.getElementById('quick-view-room');
        if (roomElement && product.room) {
            roomElement.textContent = product.room;
        }
        
        const assemblyElement = document.getElementById('quick-view-assembly');
        if (assemblyElement && product.assembly) {
            assemblyElement.textContent = product.assembly;
        }
        
        const weightElement = document.getElementById('quick-view-weight');
        if (weightElement && product.weight) {
            weightElement.textContent = product.weight;
        }
        
        const warrantyElement = document.getElementById('quick-view-warranty');
        if (warrantyElement && product.warranty) {
            warrantyElement.textContent = product.warranty;
        }
        
        const careElement = document.getElementById('quick-view-care');
        if (careElement && product.care) {
            careElement.textContent = product.care;
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

        // Populate sizes/dimensions
        const sizeSelection = document.getElementById('quick-view-size-selection');
        if (sizeSelection) {
        sizeSelection.innerHTML = '';
            
            if (product.dimensions) {
                // For home decor, show dimensions
                const dimensionDiv = document.createElement('div');
                dimensionDiv.className = 'dimension-info';
                dimensionDiv.innerHTML = `
                    <div class="dimension-item">
                        <span class="dimension-label">Dimensions:</span>
                        <span class="dimension-value">${product.dimensions}</span>
                    </div>
                `;
                sizeSelection.appendChild(dimensionDiv);
            }
            
            // Also show available sizes if they exist
            if (product.sizes && product.sizes.length > 0) {
                const sizeLabel = document.createElement('div');
                sizeLabel.className = 'size-label';
                sizeLabel.textContent = 'Available Options:';
                sizeSelection.appendChild(sizeLabel);
        
        product.sizes.forEach(size => {
            const sizeBtn = document.createElement('button');
            sizeBtn.className = 'quick-view-size-btn';
            sizeBtn.textContent = size;
            
                    if (product.soldOutSizes && product.soldOutSizes.includes(size)) {
                sizeBtn.classList.add('sold-out');
            } else {
                sizeBtn.addEventListener('click', () => {
                    sizeSelection.querySelectorAll('.quick-view-size-btn').forEach(s => s.classList.remove('active'));
                    sizeBtn.classList.add('active');
                });
            }
            
            sizeSelection.appendChild(sizeBtn);
        });
            }
        }

        // Show sidebar
        quickViewSidebar.classList.add('active');
        quickViewOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
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
            const productName = document.getElementById('quick-view-title').textContent;
            const selectedColor = document.querySelector('.quick-view-color-circle.active');
            const colorName = selectedColor ? selectedColor.title : '';
            
            // For home decor, size selection is optional
            const sizeInfo = selectedSize ? selectedSize.textContent : 'Standard';
            
            console.log(`Added to cart: ${productName} - Size: ${sizeInfo}, Color: ${colorName}`);
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
    
    console.log('Found filter checkboxes:', filterCheckboxes.length);
    
    filterCheckboxes.forEach(checkbox => {
        console.log('Checkbox:', checkbox.name, checkbox.value);
        checkbox.addEventListener('change', function() {
            console.log('Checkbox changed:', this.name, this.value, this.checked);
            applyFilters();
        });
    });
    
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
    
    function applyFilters() {
        const productGrid = document.querySelector('.product-grid');
        const products = Array.from(productGrid.querySelectorAll('.product-card'));
        const selectedCategories = getSelectedValues('category');
        const selectedPrices = getSelectedValues('price');
        
        console.log('Selected categories:', selectedCategories);
        console.log('Selected prices:', selectedPrices);
        
        // If no filters are selected, show all products
        if (selectedCategories.length === 0 && selectedPrices.length === 0) {
            showAllProducts();
            return;
        }
        
        products.forEach(product => {
            let shouldShow = true;
            
            // Check category filter
            if (selectedCategories.length > 0) {
                const productCategory = product.getAttribute('data-category');
                console.log(`Product ${product.getAttribute('data-product-id')} category: ${productCategory}`);
                if (!selectedCategories.includes(productCategory)) {
                    shouldShow = false;
                }
            }
            
            // Check price filter
            if (selectedPrices.length > 0 && shouldShow) {
                const productPrice = parseInt(product.getAttribute('data-price'));
                let priceInRange = false;
                
                selectedPrices.forEach(priceRange => {
                    switch(priceRange) {
                        case '0-300':
                            if (productPrice >= 0 && productPrice <= 300) priceInRange = true;
                            break;
                        case '300-600':
                            if (productPrice >= 300 && productPrice <= 600) priceInRange = true;
                            break;
                        case '600-900':
                            if (productPrice >= 600 && productPrice <= 900) priceInRange = true;
                            break;
                        case '900+':
                            if (productPrice >= 900) priceInRange = true;
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
    
    function updateFilterIndicators() {
        const selectedCategories = getSelectedValues('category');
        const selectedPrices = getSelectedValues('price');
        
        // Update sidebar header to show active filters
        const sidebarHeader = document.querySelector('.sidebar-header h3');
        if (sidebarHeader) {
            if (selectedCategories.length > 0 || selectedPrices.length > 0) {
                const activeFilters = [];
                if (selectedCategories.length > 0) {
                    activeFilters.push(`${selectedCategories.length} category`);
                }
                if (selectedPrices.length > 0) {
                    activeFilters.push(`${selectedPrices.length} price`);
                }
                sidebarHeader.textContent = `Refine By (${activeFilters.join(', ')} active)`;
            } else {
                sidebarHeader.textContent = 'Refine By';
            }
        }
    }
    
    function updateStyleCount() {
        const visibleProducts = document.querySelectorAll('.product-card[style*="block"], .product-card:not([style*="none"])');
        const styleCountElement = document.querySelector('.style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${visibleProducts.length} Styles`;
        }
    }
    
    function getSelectedValues(name) {
        const checkboxes = document.querySelectorAll(`input[name="${name}[]"]:checked`);
        return Array.from(checkboxes).map(cb => cb.value);
    }
    
    // Initialize the clear all button
    addClearAllButton();
    
    // Initialize filters on page load
    applyFilters();
}); 