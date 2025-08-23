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
        
        // Get the initial color from the active color circle
        const activeColorCircle = card.querySelector('.color-circle.active');
        let currentColor = activeColorCircle ? activeColorCircle.getAttribute('data-color') : 'black';
        let autoSlideInterval;
        let currentImageIndex = 0; // Track current image index for each color
        
        console.log(`Product ${index + 1}: Found ${images.length} images and ${colorCircles.length} color circles`);
        console.log(`Product ${index + 1}: Initial color is ${currentColor}`);
        
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
            name: "shirt",
            price: "$12",
            images: [
                { src: "../img/men/shirts/9.1.png", color: "bluelight" },
                { src: "../img/men/shirts/9.1.1.png", color: "bluelight" },
                { src: "../img/men/shirts/9.png", color: "brown" },
                { src: "../img/men/shirts/9.0.png", color: "brown" }
            ],
            colors: [
                { name: "Bluelight", value: "bluelight", hex: "#96e0f3ff" },
                { name: "brown", value: "brown", hex: "#5f3727ff" }
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
            name: "formal shirt",
            price: "$18",
            images: [
                { src: "../img/men/shirts/14.jpg", color: "navy" },
                { src: "../img/men/shirts/14.0.jpg", color: "navy" },
                { src: "../img/men/shirts/14.1.jpg", color: "lightblue" },
                { src: "../img/men/shirts/14.0.jpg", color: "lightblue" }
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
        

        // Suit Products
        "suit-1": {
            name: "Classic Black Suit",
            price: "$299",
            images: [
                { src: "../img/men/suits/1.avif", color: "romanbrown" },
                { src: "../img/men/suits/1.1.avif", color: "white" },
                { src: "../img/men/suits/1.2.avif", color: "brown" }
            ],
            colors: [
                { name: "Romanbrown", value: "romanbrown", hex: "#794D4C" },
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Brown", value: "brown", hex: "#483131" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "suit-2": {
            name: "Grey Business Suit",
            price: "$259",
            images: [
                { src: "../img/men/suits/4.avif", color: "black" },
                { src: "../img/men/suits/4.2.jpg", color: "black" },
                { src: "../img/men/suits/4.1.jpg", color: "white" },
                { src: "../img/men/suits/4.4.jpg", color: "navy" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Navy", value: "navy", hex: "#202c3fff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "suit-3": {
            name: "Dark Formal Suit",
            price: "$299",
            images: [
                { src: "../img/men/suits/7.jpg", color: "black" },
                { src: "../img/men/suits/7.1.jpg", color: "white" },
                { src: "../img/men/suits/7.2.jpg", color: "silver" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Silver", value: "silver", hex: "#c0c0c0" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "suit-4": {
            name: "Elegant Suit",
            price: "$399",
            images: [
                { src: "../img/men/suits/5.6.jpg", color: "black" },
                { src: "../img/men/suits/5.7.jpg", color: "grey" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Grey", value: "grey", hex: "#808080" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // T-Shirt Products
        
        "tshirt-1": {
            name: "soft T-Shirt",
            price: "$22",
            images: [
                { src: "../img/men/t-shirts/2.jpg", color: "purple" },
                { src: "../img/men/t-shirts/2.4.jpg", color: "maroon" },
                { src: "../img/men/t-shirts/2.3.jpg", color: "brown" }
            ],
            colors: [
                { name: "Purple", value: "purple", hex: "#9560eaff" },
                { name: "Maroon", value: "maroon", hex: "#892e2eff" },
                { name: "Brown", value: "brown", hex: "#6e503fd4" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "tshirt-2": {
            name: "Grey T-Shirt",
            price: "$20",
            images: [
                { src: "../img/men/t-shirts/3.jpg", color: "white" },
                { src: "../img/men/t-shirts/3.1.jpg", color: "white" },
                { src: "../img/men/t-shirts/3.2.jpg", color: "militarygreen" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Militarygreen", value: "militarygreen", hex: "#4B5320" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["L"]
        },
        "tshirt-3": {
            name: "patchwork detail T-Shirt",
            price: "$28",
            images: [
                { src: "../img/men/t-shirts/4.jpg", color: "blue" },
                { src: "../img/men/t-shirts/4.0.jpg", color: "blue" },
                { src: "../img/men/t-shirts/4.1.jpg", color: "yellow" },
                { src: "../img/men/t-shirts/4.1.1.jpg", color: "yellow" }
            ],
            colors: [
                { name: "Blue", value: "blue", hex: "#0066cc" },
                { name: "Yellow", value: "yellow", hex: "#b8a562ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "tshirt-4": {
            name: "Graphic T-Shirt",
            price: "$32",
            images: [
                { src: "../img/men/t-shirts/6.png", color: "lightblue" },
                { src: "../img/men/t-shirts/6.0.png", color: "lightblue" },
                { src: "../img/men/t-shirts/6.1.png", color: "lightgreen" },
                { src: "../img/men/t-shirts/6.1.1.png", color: "lightgreen" },
                { src: "../img/men/t-shirts/6.2.png", color: "black" },
                { src: "../img/men/t-shirts/6.2.1.png", color: "black" },
               
            ],
            colors: [
                { name: "Lightblue", value: "lightblue", hex: "#6976ead6" },
                { name: "Lightgreen", value: "lightgreen", hex: "#6cf3e5ff" },
                { name: "Black", value: "black", hex: "#000000ff" }
            ],
            sizes: ["XS", "S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },

        // Hoodie Products
        "hoodie-1": {
            name: "men's wild Dragon Hoodie",
            price: "$45",
            images: [
                { src: "../img/men/hoodie$sweatshirt/13.2.jpg", color: "white" },
                { src: "../img/men/hoodie$sweatshirt/13.2.0.jpg", color: "white" },
                { src: "../img/men/hoodie$sweatshirt/13.jpg", color: "purple" },
                { src: "../img/men/hoodie$sweatshirt/13.0.jpg", color: "purple" },
                { src: "../img/men/hoodie$sweatshirt/13.1.jpg", color: "red" },
                { src: "../img/men/hoodie$sweatshirt/13.1.0.jpg", color: "red" }
            ],
            colors: [
                { name: "White", value: "white", hex: "#ffffffff" },
                { name: "Purple", value: "purple", hex: "#cc3eb4ff" },
                { name: "Red", value: "red", hex: "#fb3939ff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "hoodie-2": {
            name: "stylish Hoodie",
            price: "$42",
            images: [
                { src: "../img/men/hoodie$sweatshirt/3.jpg", color: "lightgrey" },
                { src: "../img/men/hoodie$sweatshirt/3.0.jpg", color: "lightgrey" },
                { src: "../img/men/hoodie$sweatshirt/3.2.jpg", color: "beige" },
                { src: "../img/men/hoodie$sweatshirt/3.2.0.jpg", color: "beige" },
                { src: "../img/men/hoodie$sweatshirt/3.1.jpg", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/3.1.0.jpg", color: "black" }
            ],
            colors: [
                { name: "Lightgrey", value: "lightgrey", hex: "#808080" },
                { name: "Beige", value: "beige", hex: "#f5f5dc" },
                { name: "Black", value: "black", hex: "#000000" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "hoodie-3": {
            name: "soft Hoodie",
            price: "$48",
            images: [
                { src: "../img/men/hoodie$sweatshirt/12.jpeg", color: "cyan" },
                { src: "../img/men/hoodie$sweatshirt/12.0.jpeg", color: "cyan" },
                { src: "../img/men/hoodie$sweatshirt/12.1.webp", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/12.1.1.webp", color: "black" },
                { src: "../img/men/hoodie$sweatshirt/12.2.webp", color: "white" },
                { src: "../img/men/hoodie$sweatshirt/12.2.1.webp", color: "white" }
            ],
            colors: [
                { name: "Cyan", value: "cyan", hex: "#6ae5f0ff" },
                { name: "Black", value: "black", hex: "#000000" },
                { name: "White", value: "white", hex: "#ffffffff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "hoodie-4": {
            name: "elegent Sweatshirt",
            price: "$38",
            images: [
                { src: "../img/men/hoodie$sweatshirt/7.4.webp", color: "beige" },
                { src: "../img/men/hoodie$sweatshirt/7.4.0.webp", color: "beige" },
                { src: "../img/men/hoodie$sweatshirt/7.2.webp", color: "maroon" },
                { src: "../img/men/hoodie$sweatshirt/7.2.0.webp", color: "maroon" },
                { src: "../img/men/hoodie$sweatshirt/7.5.webp", color: "greyblue" },
                { src: "../img/men/hoodie$sweatshirt/7.5.0.webp", color: "greyblue" }
            ],
            colors: [
                { name: "Beige", value: "beige", hex: "#f5f5dc" },
                { name: "Maroon", value: "maroon", hex: "#ec424dff" },
                { name: "Greyblue", value: "greyblue", hex: "#797F9F" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
       

        // Pants Products
        "pants-1": {
            name: "Classic Pants",
            price: "$65",
            images: [
                { src: "../img/men/pants/1.avif", color: "black" },
                { src: "../img/men/pants/1.0.avif", color: "black" },
                { src: "../img/men/pants/1.1.avif", color: "blue" },
                { src: "../img/men/pants/1.1.0.avif", color: "blue" },
                { src: "../img/men/pants/1.2.avif", color: "lightblue" },
                { src: "../img/men/pants/1.2.0.avif", color: "lightblue" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Blue", value: "blue", hex: "#1a1c4fff" },
                { name: "Lightblue", value: "lightblue", hex: "#99bee8ff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "pants-2": {
            name: "classic baggy jeans",
            price: "$58",
            images: [
                { src: "../img/men/pants/2.webp", color: "black" },
                { src: "../img/men/pants/2.0.webp", color: "black" },
                { src: "../img/men/pants/2.2.webp", color: "blue" },
                { src: "../img/men/pants/2.2.0.webp", color: "blue" },
                { src: "../img/men/pants/2.1.webp", color: "lightblue" },
                { src: "../img/men/pants/2.1.0.webp", color: "lightblue" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Blue", value: "blue", hex: "#1a1c4fff" },
                { name: "Lightblue", value: "lightblue", hex: "#99bee8ff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "pants-3": {
            name: "elegent Pants",
            price: "$62",
            images: [
                { src: "../img/men/pants/5.webp", color: "black" },
                { src: "../img/men/pants/5.0.webp", color: "black" },
                { src: "../img/men/pants/5.1.webp", color: "beige" },
                { src: "../img/men/pants/5.1.0.webp", color: "beige" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#1a1a1a" },
                { name: "Beige", value: "beige", hex: "#f5f5dc" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
       

        // Shorts Products
        "shorts-1": {
            name: "Classic Shorts",
            price: "$35",
            images: [
                { src: "../img/men/shorts/2.jpg", color: "black" },
                { src: "../img/men/shorts/2.1.jpg", color: "lightblue" },
                { src: "../img/men/shorts/2.4.jpg", color: "navy" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000ff" },
                { name: "Lightblue", value: "lightblue", hex: "#b7d0eeff" },
                { name: "Navy", value: "navy", hex: "#171b2aff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["S"]
        },
        "shorts-2": {
            name: "Casual Shorts",
            price: "$32",
            images: [
                { src: "../img/men/shorts/12.avif", color: "black" },
                { src: "../img/men/shorts/12.0.avif", color: "black" },
                { src: "../img/men/shorts/12.1.avif", color: "pacificblue" },
                { src: "../img/men/shorts/12.1.0.avif", color: "pacificblue" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" },
                { name: "Pacificblue", value: "pacificblue", hex: "#6ba9fbff" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: []
        },
        "shorts-3": {
            name: "black stylish Short",
            price: "$38",
            images: [
                { src: "../img/men/shorts/6.jpg", color: "black" }
            ],
            colors: [
                { name: "Black", value: "black", hex: "#000000" }
            ],
            sizes: ["S", "M", "L", "XL", "XXL"],
            soldOutSizes: ["XL"]
        },
        "shorts-4": {
            name: "elegent Shorts",
            price: "$40",
            images: [
                { src: "../img/men/shorts/5.jpg", color: "lightblue" },
                { src: "../img/men/shorts/5.0.jpg", color: "lightblue" },
                { src: "../img/men/shorts/5.1.jpg", color: "black" }
            ],
            colors: [
                { name: "Lightblue", value: "lightblue", hex: "#586cc8ff" },
                { name: "Black", value: "black", hex: "#000000ff" }
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

    // Function to update style count
    function updateStyleCount() {
        const visibleProducts = document.querySelectorAll('.product-grid[style*="display: grid"] .product-card, .product-grid:not([style*="display: none"]) .product-card');
        const styleCountElement = document.getElementById('style-count');
        if (styleCountElement) {
            const count = visibleProducts.length;
            styleCountElement.textContent = `${count} Style${count !== 1 ? 's' : ''}`;
        }
    }

    // Update style count on page load
    updateStyleCount();

    // Category Filtering Functionality
    const imageBarItems = document.querySelectorAll('.image-item');
    const contentHeaders = document.querySelectorAll('.content-header');
    const productGrids = document.querySelectorAll('.product-grid');
    
    // Add click event listeners to image bar items
    imageBarItems.forEach(item => {
        item.addEventListener('click', function() {
            const selectedCategory = this.getAttribute('data-category');
            console.log('Selected category:', selectedCategory);
            
            // Remove active class from all image items
            imageBarItems.forEach(imgItem => imgItem.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Filter content based on selected category
            filterContent(selectedCategory);
        });
    });
    
        // Function to filter content
    function filterContent(category) {
        if (category === 'all') {
            // Show all content
            contentHeaders.forEach(header => {
                header.style.display = 'flex';
            });
            productGrids.forEach(grid => {
                grid.style.display = 'grid';
            });
        } else {
            // Hide all content first
            contentHeaders.forEach(header => {
                header.style.display = 'none';
            });
            productGrids.forEach(grid => {
                grid.style.display = 'none';
            });

            // Show only selected category
            const selectedHeaders = document.querySelectorAll(`[data-category="${category}"]`);
            selectedHeaders.forEach(header => {
                header.style.display = 'flex';
            });

            const selectedGrids = document.querySelectorAll(`[data-category="${category}"]`);
            selectedGrids.forEach(grid => {
                if (grid.classList.contains('product-grid')) {
                    grid.style.display = 'grid';
                }
            });
        }
        
        // Hide empty sections after category filtering
        hideEmptySections();
        
        // Update style count after filtering
        updateStyleCount();
    }
    
    // Set "Shop All" as active by default
    const shopAllItem = document.querySelector('[data-category="all"]');
    if (shopAllItem) {
        shopAllItem.classList.add('active');
    }

    // Sidebar Filter Functionality
    const sidebarCheckboxes = document.querySelectorAll('.sidebar input[type="checkbox"]');
    const categoryCheckboxes = document.querySelectorAll('.sidebar input[name="category[]"]');
    const sizeCheckboxes = document.querySelectorAll('.sidebar input[name="size[]"]');
    const colorCheckboxes = document.querySelectorAll('.sidebar input[name="color[]"]');
    const priceCheckboxes = document.querySelectorAll('.sidebar input[name="price[]"]');
    
    // Category checkboxes work like the image bar
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            applySidebarFilters();
        });
    });
    
    // Size, color, and price checkboxes for filtering products
    sizeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            applySidebarFilters();
        });
    });
    
    colorCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            applySidebarFilters();
        });
    });
    
    priceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            applySidebarFilters();
        });
    });

    // Function to apply sidebar filters (works like Image Bar Section)
    function applySidebarFilters() {
        const selectedCategories = Array.from(document.querySelectorAll('.sidebar input[name="category[]"]:checked'))
            .map(cb => cb.value.toLowerCase());
        const selectedSizes = Array.from(document.querySelectorAll('.sidebar input[name="size[]"]:checked'))
            .map(cb => cb.value.toLowerCase());
        const selectedColors = Array.from(document.querySelectorAll('.sidebar input[name="color[]"]:checked'))
            .map(cb => cb.value.toLowerCase());
        const selectedPrices = Array.from(document.querySelectorAll('.sidebar input[name="price[]"]:checked'))
            .map(cb => cb.value.toLowerCase());

        console.log('Sidebar filters:', {
            categories: selectedCategories,
            sizes: selectedSizes,
            colors: selectedColors,
            prices: selectedPrices
        });

        // First, apply category filtering (section-level)
        if (selectedCategories.length === 0) {
            // No categories selected - show all sections (like "Shop All")
            filterContent('all');
            
            // Update image bar to show "Shop All" as active
            imageBarItems.forEach(item => item.classList.remove('active'));
            const shopAllItem = document.querySelector('[data-category="all"]');
            if (shopAllItem) {
                shopAllItem.classList.add('active');
            }
        } else if (selectedCategories.length === 1) {
            // Single category selected - show only that section
            const category = selectedCategories[0];
            filterContent(category);
            
            // Update image bar to show corresponding category as active
            imageBarItems.forEach(item => item.classList.remove('active'));
            const correspondingImageItem = document.querySelector(`[data-category="${category}"]`);
            if (correspondingImageItem) {
                correspondingImageItem.classList.add('active');
            }
        } else {
            // Multiple categories selected - show all selected sections
            contentHeaders.forEach(header => {
                header.style.display = 'none';
            });
            productGrids.forEach(grid => {
                grid.style.display = 'none';
            });
            
            selectedCategories.forEach(category => {
                const selectedHeaders = document.querySelectorAll(`[data-category="${category}"]`);
                selectedHeaders.forEach(header => {
                    if (header.classList.contains('content-header')) {
                        header.style.display = 'flex';
                    }
                });
                
                const selectedGrids = document.querySelectorAll(`[data-category="${category}"]`);
                selectedGrids.forEach(grid => {
                    if (grid.classList.contains('product-grid')) {
                        grid.style.display = 'grid';
                    }
                });
            });
            
            // Remove active class from all image bar items when multiple selected
            imageBarItems.forEach(item => item.classList.remove('active'));
        }

        // Then apply size, color, and price filtering (product-level)
        applyProductFilters(selectedSizes, selectedColors, selectedPrices);
        
        // Hide empty sections after all filtering is done
        hideEmptySections();
        
        // Update style count after filtering
        updateStyleCount();
    }

    // Function to apply product-level filters (size, color, price)
    function applyProductFilters(selectedSizes, selectedColors, selectedPrices) {
        const allProducts = document.querySelectorAll('.product-card');
        
        allProducts.forEach(product => {
            let shouldShow = true;
            
            // Check size filter
            if (selectedSizes.length > 0) {
                const productSizes = getProductSizes(product);
                const hasMatchingSize = selectedSizes.some(selectedSize => 
                    productSizes.some(productSize => 
                        matchSizes(selectedSize, productSize)
                    )
                );
                if (!hasMatchingSize) {
                    shouldShow = false;
                }
            }
            
            // Check color filter
            if (selectedColors.length > 0 && shouldShow) {
                const productColors = getProductColors(product);
                const hasMatchingColor = selectedColors.some(selectedColor => 
                    productColors.some(productColor => 
                        matchColors(selectedColor, productColor)
                    )
                );
                if (!hasMatchingColor) {
                    shouldShow = false;
                } else {
                    // If color filter is applied, hide non-matching color images and show only matching ones
                    const imageSlider = product.querySelector('.image-slider');
                    if (imageSlider) {
                        const images = imageSlider.querySelectorAll('img');
                        const colorCircles = product.querySelectorAll('.color-circle');
                        
                        // Hide all images first
                        images.forEach(img => {
                            img.style.display = 'none';
                        });
                        
                        // Hide all color circles first
                        colorCircles.forEach(circle => {
                            circle.style.display = 'none';
                        });
                        
                        // Show only images and color circles that match the selected colors
                        selectedColors.forEach(selectedColor => {
                            images.forEach(img => {
                                const imgColor = img.getAttribute('data-color');
                                if (imgColor && matchColors(selectedColor, imgColor)) {
                                    img.style.display = 'block';
                                }
                            });
                            
                            colorCircles.forEach(circle => {
                                const circleColor = circle.getAttribute('data-color');
                                if (circleColor && matchColors(selectedColor, circleColor)) {
                                    circle.style.display = 'inline-block';
                                }
                            });
                        });
                        
                        // Make the first visible image active
                        const visibleImages = imageSlider.querySelectorAll('img[style*="display: block"]');
                        if (visibleImages.length > 0) {
                            images.forEach(img => img.classList.remove('active'));
                            visibleImages[0].classList.add('active');
                        }
                        
                        // Make the first visible color circle active
                        const visibleColorCircles = product.querySelectorAll('.color-circle[style*="display: inline-block"]');
                        if (visibleColorCircles.length > 0) {
                            colorCircles.forEach(circle => circle.classList.remove('active'));
                            visibleColorCircles[0].classList.add('active');
                        }
                    }
                }
            } else {
                // If no color filter is applied, show all images and color circles
                const imageSlider = product.querySelector('.image-slider');
                if (imageSlider) {
                    const images = imageSlider.querySelectorAll('img');
                    const colorCircles = product.querySelectorAll('.color-circle');
                    
                    images.forEach(img => {
                        img.style.display = 'block';
                    });
                    
                    colorCircles.forEach(circle => {
                        circle.style.display = 'inline-block';
                    });
                    
                    // Reset to first image and first color circle
                    if (images.length > 0) {
                        images.forEach(img => img.classList.remove('active'));
                        images[0].classList.add('active');
                    }
                    
                    if (colorCircles.length > 0) {
                        colorCircles.forEach(circle => circle.classList.remove('active'));
                        colorCircles[0].classList.add('active');
                    }
                }
            }
            
            // Check price filter
            if (selectedPrices.length > 0 && shouldShow) {
                const productPrice = getProductPrice(product);
                const hasMatchingPrice = selectedPrices.some(selectedPrice => {
                    return isPriceInRange(productPrice, selectedPrice);
                });
                if (!hasMatchingPrice) {
                    shouldShow = false;
                }
            }
            
            // Show/hide product based on filters
            product.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Hide empty sections (sections with no visible products)
        hideEmptySections();
    }
    
    // Function to hide sections that have no visible products
    function hideEmptySections() {
        const allProductGrids = document.querySelectorAll('.product-grid');
        
        allProductGrids.forEach(grid => {
            const visibleProducts = grid.querySelectorAll('.product-card[style*="display: block"], .product-card:not([style*="display: none"])');
            const sectionHeader = document.querySelector(`.content-header[data-category="${grid.getAttribute('data-category')}"]`);
            
            if (visibleProducts.length === 0) {
                // Hide the entire section (header and grid) if no products are visible
                if (sectionHeader) {
                    sectionHeader.style.display = 'none';
                }
                grid.style.display = 'none';
            } else {
                // Show the section if it has visible products
                if (sectionHeader) {
                    sectionHeader.style.display = 'flex';
                }
                grid.style.display = 'grid';
            }
        });
    }
    
    // Helper function to get product sizes
    function getProductSizes(product) {
        const productId = product.getAttribute('data-product-id');
        if (productId && productData[productId]) {
            return productData[productId].sizes || [];
        }
        return [];
    }
    
    // Helper function to get product colors
    function getProductColors(product) {
        const colorElements = product.querySelectorAll('.color-circle');
        const colors = [];
        colorElements.forEach(colorCircle => {
            const colorName = colorCircle.getAttribute('data-color');
            if (colorName) {
                colors.push(colorName);
            }
        });
        return colors;
    }
    
    // Helper function to get product price
    function getProductPrice(product) {
        const priceElement = product.querySelector('.product-price');
        if (priceElement) {
            return extractPrice(priceElement.textContent);
        }
        return 0;
    }
    
    // Helper function to check if price is in range
    function isPriceInRange(productPrice, priceRange) {
        switch(priceRange) {
            case '0-25':
                return productPrice >= 0 && productPrice <= 25;
            case '25-50':
                return productPrice >= 25 && productPrice <= 50;
            case '50-75':
                return productPrice >= 50 && productPrice <= 75;
            case '75-100':
                return productPrice >= 75 && productPrice <= 100;
            case '100+':
                return productPrice >= 100;
            case 'on-sale':
                // For on-sale, we'll check if there's a sale indicator
                const saleElement = product.querySelector('.sale-badge, .discount');
                return saleElement !== null;
            default:
                return true;
        }
    }
    
    // Helper function to match colors with variations
    function matchColors(selectedColor, productColor) {
        const selected = selectedColor.toLowerCase();
        const product = productColor.toLowerCase();
        
        // Direct match
        if (selected === product) return true;
        
        // Partial matches for common color variations
        const colorVariations = {
            'green': ['green', 'darkgreen', 'lightgreen', 'militarygreen', 'forestgreen', 'olivegreen'],
            'blue': ['blue', 'lightblue', 'navy', 'darkblue', 'skyblue', 'royalblue'],
            'red': ['red', 'darkred', 'lightred', 'crimson', 'maroon'],
            'black': ['black', 'darkblack', 'charcoal'],
            'white': ['white', 'offwhite', 'cream', 'ivory'],
            'brown': ['brown', 'darkbrown', 'lightbrown', 'tan', 'beige'],
            'grey': ['grey', 'gray', 'lightgrey', 'darkgrey', 'silver'],
            'purple': ['purple', 'darkpurple', 'lightpurple', 'violet'],
            'pink': ['pink', 'lightpink', 'darkpink', 'rose'],
            'orange': ['orange', 'lightorange', 'darkorange'],
            'yellow': ['yellow', 'lightyellow', 'gold', 'golden'],
            'beige': ['beige', 'tan', 'cream', 'offwhite']
        };
        
        // Check if selected color has variations
        if (colorVariations[selected]) {
            return colorVariations[selected].includes(product);
        }
        
        // Check if product color has variations
        for (const [baseColor, variations] of Object.entries(colorVariations)) {
            if (variations.includes(product) && baseColor === selected) {
                return true;
            }
        }
        
        // Partial string matching as fallback
        return selected.includes(product) || product.includes(selected);
    }

    // Helper function to match sizes with variations
    function matchSizes(selectedSize, productSize) {
        const selected = selectedSize.toLowerCase();
        const product = productSize.toLowerCase();
        
        // Direct match
        if (selected === product) return true;
        
        // Handle size variations
        const sizeVariations = {
            'xs': ['xs', 'x-small', 'extra small'],
            's': ['s', 'small'],
            'm': ['m', 'medium'],
            'l': ['l', 'large'],
            'xl': ['xl', 'x-large', 'extra large'],
            'xxl': ['xxl', '2xl', '2x-large', '2xl', 'xx-large', 'extra extra large'],
            '2xl': ['2xl', 'xxl', 'xx-large', 'extra extra large'],
            '3xl': ['3xl', 'xxxl', 'xxx-large', 'extra extra extra large'],
            '4xl': ['4xl', 'xxxxl', 'xxxx-large'],
            '5xl': ['5xl', 'xxxxxl', 'xxxxx-large']
        };
        
        // Check if selected size has variations
        if (sizeVariations[selected]) {
            return sizeVariations[selected].includes(product);
        }
        
        // Check if product size has variations
        for (const [baseSize, variations] of Object.entries(sizeVariations)) {
            if (variations.includes(product) && baseSize === selected) {
                return true;
            }
        }
        
        // Partial string matching as fallback
        return selected.includes(product) || product.includes(selected);
    }

    // Function to update product count
    function updateProductCount() {
        const visibleProducts = document.querySelectorAll('.product-card[style*="display: block"], .product-card:not([style*="display: none"])');
        const styleCountElement = document.querySelector('.style-count');
        if (styleCountElement) {
            styleCountElement.textContent = `${visibleProducts.length} Styles`;
        }
    }

    // Clear all filters button functionality
    const clearFiltersBtn = document.querySelector('.clear-filters-btn');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Uncheck all checkboxes
            categoryCheckboxes.forEach(cb => cb.checked = false);
            sizeCheckboxes.forEach(cb => cb.checked = false);
            colorCheckboxes.forEach(cb => cb.checked = false);
            priceCheckboxes.forEach(cb => cb.checked = false);
            // Apply filters (which will show all sections and products)
            applySidebarFilters();
        });
    }

    // Update the filterContent function to reset sort and view when switching categories
    const originalFilterContent = filterContent;
    filterContent = function(category) {
        originalFilterContent(category);
        
        // Reset sort to featured for the new category
        if (category !== 'all') {
            const categoryHeader = document.querySelector(`.content-header[data-category="${category}"]`);
            if (categoryHeader) {
                const sortSelect = categoryHeader.querySelector('.sort-select');
                if (sortSelect) {
                    sortSelect.value = 'featured';
                }
                
                // Reset view to 60
                const viewControl = categoryHeader.querySelector('.view-control');
                if (viewControl) {
                    viewControl.querySelectorAll('.view-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    const defaultView = viewControl.querySelector('.view-option');
                    if (defaultView) {
                        defaultView.classList.add('active');
                    }
                }
                
                // Show all products for the category
                const productGrid = document.querySelector(`.product-grid[data-category="${category}"]`);
                if (productGrid) {
                    const products = productGrid.querySelectorAll('.product-card');
                    products.forEach(product => {
                        product.style.display = 'block';
                    });
                }
            }
        }
    };

    // Global Sort Functionality - Works across all sections
    const sortSelects = document.querySelectorAll('.sort-select');
    
    // Track original grid for each product
    function trackOriginalGrids() {
        const allProductGrids = document.querySelectorAll('.product-grid');
        allProductGrids.forEach(grid => {
            const products = grid.querySelectorAll('.product-card');
            products.forEach(product => {
                const gridSelector = `.product-grid[data-category="${grid.getAttribute('data-category')}"]`;
                product.setAttribute('data-original-grid', gridSelector);
            });
        });
    }
    
    // Initialize tracking
    trackOriginalGrids();
    
    sortSelects.forEach(select => {
        select.addEventListener('change', function() {
            const sortValue = this.value;
            // Sort globally regardless of which category's sort select was changed
            sortProductsGlobally(sortValue);
            
            // Update all sort selects to show the same value
            sortSelects.forEach(otherSelect => {
                otherSelect.value = sortValue;
            });
        });
    });

    // Function to sort products globally across all sections
    function sortProductsGlobally(sortType) {
        // Get all product grids
        const allProductGrids = document.querySelectorAll('.product-grid');
        
        // Collect all products from all grids
        let allProducts = [];
        allProductGrids.forEach(grid => {
            const products = Array.from(grid.querySelectorAll('.product-card'));
            allProducts = allProducts.concat(products);
        });
        
        // Sort all products
        allProducts.sort((a, b) => {
            const priceA = extractPrice(a.querySelector('.product-price').textContent);
            const priceB = extractPrice(b.querySelector('.product-price').textContent);
            const nameA = a.querySelector('.product-name').textContent.toLowerCase();
            const nameB = b.querySelector('.product-name').textContent.toLowerCase();
            
            switch(sortType) {
                case 'price-low':
                    return priceA - priceB;
                case 'price-high':
                    return priceB - priceA;
                case 'newest':
                    // For newest, we'll sort by product ID (assuming higher ID = newer)
                    const idA = parseInt(a.getAttribute('data-product-id')) || 0;
                    const idB = parseInt(b.getAttribute('data-product-id')) || 0;
                    return idB - idA;
                case 'popular':
                    // For popular, we'll sort alphabetically (you can modify this based on your data)
                    return nameA.localeCompare(nameB);
                case 'featured':
                default:
                    // For featured, maintain original order
                    return 0;
            }
        });
        
        // Clear all product grids
        allProductGrids.forEach(grid => {
            grid.innerHTML = '';
        });
        
        // Redistribute sorted products back to their original grids
        allProducts.forEach(product => {
            const originalGrid = product.getAttribute('data-original-grid');
            if (originalGrid) {
                const targetGrid = document.querySelector(originalGrid);
                if (targetGrid) {
                    targetGrid.appendChild(product);
                }
            }
        });
    }

    // Function to extract price from price text
    function extractPrice(priceText) {
        const priceMatch = priceText.match(/\$(\d+(?:\.\d{2})?)/);
        return priceMatch ? parseFloat(priceMatch[1]) : 0;
    }

    // View options functionality
    const viewOptions = document.querySelectorAll('.view-option');
    
    viewOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all view options across all sections
            document.querySelectorAll('.view-option').forEach(opt => {
                opt.classList.remove('active');
            });
            
            // Add active class to clicked option
            this.classList.add('active');
            
            // Get the number of items to show
            const itemsToShow = parseInt(this.textContent);
            
            // Apply view limit globally to all visible categories
            applyViewLimit('all', itemsToShow);
        });
    });

    // Function to apply view limit
    function applyViewLimit(category, limit) {
        let productGrids;
        
        if (category === 'all') {
            productGrids = document.querySelectorAll('.product-grid[style*="display: grid"], .product-grid:not([style*="display: none"])');
        } else {
            const grid = document.querySelector(`.product-grid[data-category="${category}"]`);
            productGrids = grid ? [grid] : [];
        }
        
        productGrids.forEach(grid => {
            const products = grid.querySelectorAll('.product-card');
            
            products.forEach((product, index) => {
                if (index < limit) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    }
}); 