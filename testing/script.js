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

    // Quick view functionality
    const quickViewButtons = document.querySelectorAll('.quick-view');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add your quick view functionality here
            console.log('Quick view:', this.closest('.product-card').querySelector('.product-name').textContent);
        });
    });
}); 