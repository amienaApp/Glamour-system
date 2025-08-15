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
    
    // Product data for quick view
    const productData = {
        1: {
            name: "Popcorn Fabric Shirt",
            price: "$15",
            images: [
                { src: "../img/men/shirts/9.1.png", color: "lightblue" },
                { src: "../img/men/shirts/9.1.1.png", color: "lightblue" },
                { src: "../img/men/shirts/9.png", color: "brown" },
                { src: "../img/men/shirts/9.0.png", color: "brown" }
            ],
            colors: [
                { name: "lightblue", value: "lightblue", hex: "#add8e6" },
                { name: "brown", value: "brown", hex: "#634145ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL","XXL"],
            soldOutSizes: ["XS"]
        },
        2: {
            name: "casual shirt",
            price: "$12",
            images: [
                { src: "../img/men/shirts/4.3.jpg", color: "maroon" },
                { src: "../img/men/shirts/4.3.1.webp", color: "maroon" },
                { src: "../img/men/shirts/4.1.jpg", color: "black" },
                { src: "../img/men/shirts/4.1.1.jpg", color: "black" },
                { src: "../img/men/shirts/4.0.jpg", color: "lightyellow" },
                { src: "../img/men/shirts/4.0.1.jpg", color: "lightyellow" }
            ],
            colors: [
                { name: "Maroon", value: "maroon", hex: "#812d2dff" },
                { name: "Black", value: "black", hex: "#060606ff" },
                { name: "lightyellow", value: "lightyellow", hex: "#ffffe0" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        3: {
            name: "short Sleeve Crocheted Shirt",
            price: "$10",
            images: [
                { src: "../img/men/shirts/10.1.avif", color: "black" },
                
                { src: "../img/men/shirts/10.avif", color: "white" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000" },
                { name: "white", value: "white", hex: "#faf7f4ff "}
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["L"]
        },
        4: {
            name: "Formal shirts",
            price: "$18",
            images: [
                { src: "../img/men/shirts/14.jpg", color: "navy" },
                { src: "../img/men/shirts/14.0.jpg", color: "navy" },
                { src: "../img/men/shirts/14.1.jpg", color: "lightblue" },
                { src: "../img/men/shirts/14.1.jpg", color: "lightblue" }
            ],
            colors: [
                { name: "navy", value: "navy", hex: "#001f3f" },
                { name: "lightblue", value: "lightblue", hex: "#78bce1ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["M"]
        },
        5: {
            name: "short sleeve shirt",
            price: "$10",
            images: [
                { src: "../img/men/shirts/12.2.jpg", color: "Purplishbrown" },
                { src: "../img/men/shirts/12.jpg", color: "navy" },
                { src: "../img/men/shirts/12.3.1.jpg", color: "lightblue" },
                { src: "../img/men/shirts/12.1.0.jpg", color: "morningblue" }
            ],
            colors: [
                { name: "purplishbrown", value: "purplishbrown", hex: "#63503f" },
                { name: "navy", value: "navy", hex: "#001f3f" },
                { name: "lightblue", value: "lightblue", hex: "#78bce1ff" },
                { name: "morningblue", value: "morningblue", hex: "#8ce792ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        6: {
            name: "crochet shirts",
            price: "$11",
            images: [
                { src: "../img/men/shirts/15.webp", color: "black" },
                { src: "../img/men/shirts/15.1.webp", color: "cyan" }
            ],
            colors: [
                { name: "black", value: "black", hex: "#060606ff;" },
                { name: "cyan", value: "cyan", hex: "#4c92b2ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["S"]
        },
        7: {
            name: "polo shirt",
            price: "$11",
            images: [
                { src: "../img/men/shirts/13.2.jpg", color: "white" },
                { src: "../img/men/shirts/13.1.jpg", color: "navy" },
                { src: "../img/men/shirts/13.jpg", color: "bluegrey" },
                { src: "../img/men/shirts/13.3.0.jpg", color: "cyan" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "navy", value: "black", hex: "#001f3f" },
                { name: "bluegrey", value: "brown", hex: "#455a64" },
                { name: "cyan", value: "blue", hex: "#4c92b2ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["M"]
        },
        8: {
            name: "Formal shirts",
            price: "$12",
            images: [
                { src: "../img/men/shirts/8.avif", color: "darkgreen" },
                { src: "../img/men/shirts/8.avif", color: "darkgreen" },
                { src: "../img/men/shirts/8.1.jpg", color: "khaki" },
                { src: "../img/men/shirts/8.1.jpg", color: "khaki" }
            ],
            colors: [
                { name: "Pink", value: "pink", hex: "#14220cff" },
                { name: "Blue", value: "blue", hex: "#c9c7ab" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        9: {
            name: "Orange Floral Summer Dress",
            price: "$78",
            images: [
                { src: "../img/women/NEW/1.webp", color: "orange" },
                { src: "../img/women/NEW/1.1.webp", color: "orange" },
                { src: "../img/women/NEW/1.2.webp", color: "orange" },
                { src: "../img/women/NEW/1.3.webp", color: "orange" }
            ],
            colors: [
                { name: "Orange", value: "orange", hex: "#e66909ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        10: {
            name: "Purple Evening Gown",
            price: "$120",
            images: [
                { src: "../img/women/NEW/7.1.webp", color: "purple" },
                { src: "../img/women/NEW/7.webp", color: "purple" },
                { src: "../img/women/NEW/7.3.jpg", color: "purple" }
            ],
            colors: [
                { name: "Purple", value: "purple", hex: "#541654ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["XS"]
        },
        11: {
            name: "Maroon Cocktail Dress",
            price: "$95",
            images: [
                { src: "../img/women/NEW/2.1.webp", color: "maroon" },
                { src: "../img/women/NEW/2.3.webp", color: "maroon" },
                { src: "../img/women/NEW/2.webp", color: "maroon" }
            ],
            colors: [
                { name: "Maroon", value: "maroon", hex: "#54162bff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        12: {
            name: "Multi-Color Party Dress",
            price: "$88",
            images: [
                { src: "../img/women/NEW/3.webp", color: "apricot" },
                { src: "../img/women/NEW/3.1.webp", color: "pink" },
                { src: "../img/women/NEW/3.2.webp", color: "black" }
            ],
            colors: [
                { name: "Apricot", value: "apricot", hex: "#db8e09ff" },
                { name: "Pink", value: "pink", hex: "#e683a4ff" },
                { name: "Black", value: "black", hex: "#050505ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["L"]
        },
        13: {
            name: "Grey Casual Dress",
            price: "$65",
            images: [
                { src: "../img/women/NEW/10.1.jpg", color: "grey" },
                { src: "../img/women/NEW/10.webp", color: "brown" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#d2cfcdeb" },
                { name: "Brown", value: "brown", hex: "#54162bff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: []
        },
        14: {
            name: "Classic Black Dress",
            price: "$110",
            images: [
                { src: "../img/women/NEW/11.webp", color: "black" },
                { src: "../img/women/NEW/11.2.jpg", color: "grey" },
                { src: "../img/women/NEW/11.4.jpg", color: "beige" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "Grey", value: "grey", hex: "#cec6c9ff" },
                { name: "Beige", value: "beige", hex: "#e3c880ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["M"]
        },
        15: {
            name: "Maroon Evening Dress",
            price: "$135",
            images: [
                { src: "../img/women/NEW/8.1.webp", color: "maroon" },
                { src: "../img/women/NEW/8.webp", color: "black" }
            ],
            colors: [
                { name: "Maroon", value: "maroon", hex: "#54162bff" },
                { name: "Black", value: "black", hex: "#020202ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL"],
            soldOutSizes: ["XL"]
        },

        // Suit Products
        "suit-1": {
            name: "Classic Black Suit",
            price: "$299",
            images: [
                { src: "../img/men/suits/1.avif", color: "black" },
                { src: "../img/men/suits/1.1.avif", color: "black" },
                { src: "../img/men/suits/1.2.avif", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Charcoal", value: "charcoal", hex: "#2c2c2c" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "suit-2": {
            name: "Navy Blue Suit",
            price: "$349",
            images: [
                { src: "../img/men/suits/2.avif", color: "navy" },
                { src: "../img/men/suits/2.1.avif", color: "cyan" }
                
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" }
                
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "suit-3": {
            name: "Charcoal Suit",
            price: "$279",
            images: [
                { src: "../img/men/suits/3.avif", color: "charcoal" },
                { src: "../img/men/suits/3.1.avif", color: "charcoal" },
                { src: "../img/men/suits/3.2.avif", color: "charcoal" }
            ],
            colors: [
                { name: "Charcoal", value: "charcoal", hex: "#2c2c2c" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "suit-4": {
            name: "Grey Business Suit",
            price: "$259",
            images: [
                { src: "../img/men/suits/4.avif", color: "grey" },
                { src: "../img/men/suits/4.1.avif", color: "grey" },
                { src: "../img/men/suits/4.2.avif", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "suit-5": {
            name: "Brown Formal Suit",
            price: "$329",
            images: [
                { src: "../img/men/suits/5.avif", color: "brown" },
                { src: "../img/men/suits/5.1.avif", color: "brown" },
                { src: "../img/men/suits/5.2.avif", color: "brown" }
            ],
            colors: [
                { name: "Brown", value: "brown", hex: "#8B4513" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["M"]
        },
        "suit-6": {
            name: "Blue Business Suit",
            price: "$379",
            images: [
                { src: "../img/men/suits/6.jpg", color: "blue" },
                { src: "../img/men/suits/6.1.jpg", color: "blue" },
                { src: "../img/men/suits/6.4.jpg", color: "blue" }
            ],
            colors: [
                { name: "Blue", value: "blue", hex: "#0066cc" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "suit-7": {
            name: "Dark Formal Suit",
            price: "$299",
            images: [
                { src: "../img/men/suits/7.jpg", color: "dark" },
                { src: "../img/men/suits/7.1.jpg", color: "dark" },
                { src: "../img/men/suits/7.2.jpg", color: "dark" }
            ],
            colors: [
                { name: "Dark", value: "dark", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Charcoal", value: "charcoal", hex: "#2c2c2c" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "suit-8": {
            name: "Elegant Black Suit",
            price: "$399",
            images: [
                { src: "../img/men/suits/5.6.jpg", color: "black" },
                { src: "../img/men/suits/5.7.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Charcoal", value: "charcoal", hex: "#2c2c2c" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // T-Shirt Products
        "tshirt-1": {
            name: "Classic White T-Shirt",
            price: "$25",
            images: [
                { src: "../img/men/t-shirts/1.jpg", color: "white" },
                { src: "../img/men/t-shirts/1.2.jpg", color: "white" },
                { src: "../img/men/t-shirts/1.3.jpg", color: "white" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffff" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XS"]
        },
        "tshirt-2": {
            name: "Black T-Shirt",
            price: "$22",
            images: [
                { src: "../img/men/t-shirts/2.jpg", color: "black" },
                { src: "../img/men/t-shirts/2.1.jpg", color: "black" },
                { src: "../img/men/t-shirts/2.2.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "tshirt-3": {
            name: "Grey T-Shirt",
            price: "$20",
            images: [
                { src: "../img/men/t-shirts/3.jpg", color: "grey" },
                { src: "../img/men/t-shirts/3.1.jpg", color: "grey" },
                { src: "../img/men/t-shirts/3.2.jpg", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "tshirt-4": {
            name: "Blue T-Shirt",
            price: "$28",
            images: [
                { src: "../img/men/t-shirts/4.jpg", color: "blue" },
                { src: "../img/men/t-shirts/4.0.jpg", color: "blue" },
                { src: "../img/men/t-shirts/4.1.jpg", color: "blue" }
            ],
            colors: [
                { name: "Blue", value: "blue", hex: "#0066cc" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "tshirt-5": {
            name: "Red T-Shirt",
            price: "$26",
            images: [
                { src: "../img/men/t-shirts/5.jpg", color: "red" },
                { src: "../img/men/t-shirts/5.0.jpg", color: "red" },
                { src: "../img/men/t-shirts/5.1.jpg", color: "red" }
            ],
            colors: [
                { name: "Red", value: "red", hex: "#cc0000" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["M"]
        },
        "tshirt-6": {
            name: "Graphic T-Shirt",
            price: "$32",
            images: [
                { src: "../img/men/t-shirts/6.png", color: "black" },
                { src: "../img/men/t-shirts/6.0.png", color: "black" },
                { src: "../img/men/t-shirts/6.1.png", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "tshirt-7": {
            name: "Striped T-Shirt",
            price: "$35",
            images: [
                { src: "../img/men/t-shirts/9.avif", color: "navy" },
                { src: "../img/men/t-shirts/9.1.avif", color: "navy" },
                { src: "../img/men/t-shirts/9.2.avif", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "White", value: "white", hex: "#ffffff" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "tshirt-8": {
            name: "Premium T-Shirt",
            price: "$40",
            images: [
                { src: "../img/men/t-shirts/10.webp", color: "white" },
                { src: "../img/men/t-shirts/10.0.webp", color: "white" },
                { src: "../img/men/t-shirts/10.1.webp", color: "white" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffff" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // Hoodie Products
        "hoodie-1": {
            name: "Classic Grey Hoodie",
            price: "$45",
            images: [
                { src: "../img/men/hoodie$sweatshirt/1.jpg", color: "grey" },
                { src: "../img/men/hoodie$sweatshirt/2.jpg", color: "grey" },
                { src: "../img/men/hoodie$sweatshirt/3.1.jpg", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "hoodie-2": {
            name: "Black Hoodie",
            price: "$42",
            images: [
                { src: "../img/men/hoodie$sweatshirt/3.jpg", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/4.jpg", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/5.1.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "hoodie-3": {
            name: "Navy Blue Hoodie",
            price: "$48",
            images: [
                { src: "../img/men/hoodie$sweatshirt/5.jpg", color: "navy" },
                { src: "../img/men/hoodie$sweatshirt/6.jpg", color: "navy" },
                { src: "../img/men/hoodie$sweatshirt/7.1.0.webp", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "hoodie-4": {
            name: "Grey Sweatshirt",
            price: "$38",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.1.webp", color: "grey" },
                { src: "../img/men/hoodie$sweatshirt/7.2.0.webp", color: "grey" },
                { src: "../img/men/hoodie$sweatshirt/7.3", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "hoodie-5": {
            name: "Black Sweatshirt",
            price: "$35",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.4.0.webp", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/7.5.0.webp", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/7.o.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["M"]
        },
        "hoodie-6": {
            name: "Classic Navy Hoodie",
            price: "$45",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.webp", color: "navy" },
                { src: "../img/men/hoodie$sweatshirt/7.1.webp", color: "navy" },
                { src: "../img/men/hoodie$sweatshirt/7.2.webp", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "hoodie-7": {
            name: "Grey Hoodie",
            price: "$42",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.4.webp", color: "grey" },
                { src: "../img/men/hoodie$sweatshirt/7.5.webp", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "hoodie-8": {
            name: "Premium Black Hoodie",
            price: "$55",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.2.webp", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/7.3.1.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "White", value: "white", hex: "#ffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // Pants Products
        "pants-1": {
            name: "Classic Black Pants",
            price: "$65",
            images: [
                { src: "../img/men/pants/1.avif", color: "black" },
                { src: "../img/men/pants/1.1.avif", color: "black" },
                { src: "../img/men/pants/1.2.avif", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "pants-2": {
            name: "Grey Formal Pants",
            price: "$58",
            images: [
                { src: "../img/men/pants/2.webp", color: "grey" },
                { src: "../img/men/pants/2.1.webp", color: "grey" },
                { src: "../img/men/pants/2.2.webp", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "pants-3": {
            name: "Navy Blue Pants",
            price: "$62",
            images: [
                { src: "../img/men/pants/3.webp", color: "navy" },
                { src: "../img/men/pants/3.1.webp", color: "navy" },
                { src: "../img/men/pants/3.2.webp", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "pants-4": {
            name: "Black Business Pants",
            price: "$68",
            images: [
                { src: "../img/men/pants/4.webp", color: "black" },
                { src: "../img/men/pants/4.1.webp", color: "black" },
                { src: "../img/men/pants/4.2.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "pants-5": {
            name: "Grey Casual Pants",
            price: "$55",
            images: [
                { src: "../img/men/pants/5.webp", color: "grey" },
                { src: "../img/men/pants/5.1.webp", color: "grey" },
                { src: "../img/men/pants/5.2.webp", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["M"]
        },
        "pants-6": {
            name: "Navy Casual Pants",
            price: "$60",
            images: [
                { src: "../img/men/pants/6.webp", color: "navy" },
                { src: "../img/men/pants/6.1.webp", color: "navy" },
                { src: "../img/men/pants/6.2.webp", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "pants-7": {
            name: "Black Formal Pants",
            price: "$72",
            images: [
                { src: "../img/men/pants/7.webp", color: "black" },
                { src: "../img/men/pants/7.1.webp", color: "black" },
                { src: "../img/men/pants/7.2.webp", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "pants-8": {
            name: "Grey Business Pants",
            price: "$65",
            images: [
                { src: "../img/men/pants/8.webp", color: "grey" },
                { src: "../img/men/pants/8.1.webp", color: "grey" },
                { src: "../img/men/pants/8.2.webp", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // Shorts Products
        "shorts-1": {
            name: "Classic Black Shorts",
            price: "$35",
            images: [
                { src: "../img/men/shorts/1.jpg", color: "black" },
                { src: "../img/men/shorts/2.1.jpg", color: "black" },
                { src: "../img/men/shorts/2.3", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "shorts-2": {
            name: "Grey Casual Shorts",
            price: "$32",
            images: [
                { src: "../img/men/shorts/2.jpg", color: "grey" },
                { src: "../img/men/shorts/2.4.jpg", color: "grey" },
                { src: "../img/men/shorts/3.jpg", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "shorts-3": {
            name: "Navy Blue Shorts",
            price: "$38",
            images: [
                { src: "../img/men/shorts/4.jpg", color: "navy" },
                { src: "../img/men/shorts/5.jpg", color: "navy" },
                { src: "../img/men/shorts/6.jpg", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "shorts-4": {
            name: "Black Athletic Shorts",
            price: "$40",
            images: [
                { src: "../img/men/shorts/7.1.jpg", color: "black" },
                { src: "../img/men/shorts/7.2.jpg", color: "black" },
                { src: "../img/men/shorts/7.3", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "shorts-5": {
            name: "Grey Athletic Shorts",
            price: "$38",
            images: [
                { src: "../img/men/shorts/7.4.jpg", color: "grey" },
                { src: "../img/men/shorts/8.1.jpg", color: "grey" },
                { src: "../img/men/shorts/8.4.jpg", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["M"]
        },
        "shorts-6": {
            name: "Navy Casual Shorts",
            price: "$35",
            images: [
                { src: "../img/men/shorts/8.5.jpg", color: "navy" },
                { src: "../img/men/shorts/9.1.jpg", color: "navy" },
                { src: "../img/men/shorts/9.jpg", color: "navy" }
            ],
            colors: [
                { name: "Navy", value: "navy", hex: "#1a1a1a" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "shorts-7": {
            name: "Black Casual Shorts",
            price: "$33",
            images: [
                { src: "../img/men/shorts/10.jpg", color: "black" },
                { src: "../img/men/shorts/2.1.jpg", color: "black" },
                { src: "../img/men/shorts/2.3", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "shorts-8": {
            name: "Grey Summer Shorts",
            price: "$30",
            images: [
                { src: "../img/men/shorts/2.4.jpg", color: "grey" },
                { src: "../img/men/shorts/3.jpg", color: "grey" },
                { src: "../img/men/shorts/4.jpg", color: "grey" }
            ],
            colors: [
                { name: "Grey", value: "grey", hex: "#808080" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Navy", value: "navy", hex: "#1a1a1a" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
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
}); 