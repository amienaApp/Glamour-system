
   var swiper = new Swiper(".mySwiper", {
    loop: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
    

// Category link toggle
const categoryLink = document.getElementById('categoryLink');
if (categoryLink) {
  categoryLink.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default anchor click behavior
    const categories = document.getElementById('categories');
    if (categories) {
      categories.style.display = categories.style.display === 'none' || categories.style.display === '' ? 'block' : 'none';
    }
  });
}

// Mobile menu toggle
const menuBtn = document.getElementById('menu-btn');
if (menuBtn) {
  menuBtn.addEventListener('click', function() {
    var menu = document.getElementById('mobile-menu');
    if (menu) {
      menu.classList.toggle('hidden');
    }
  });
}



// Color variant switching functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle color circle clicks for product variants
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-circle')) {
            const productCard = e.target.closest('.product-card');
            if (!productCard) return;
            
            const selectedColor = e.target.getAttribute('data-color');
            const imageSlider = productCard.querySelector('.image-slider');
            
            if (imageSlider) {
                // Remove active class from all color circles in this product
                const allColorCircles = productCard.querySelectorAll('.color-circle');
                allColorCircles.forEach(circle => circle.classList.remove('active'));
                
                // Add active class to clicked color circle
                e.target.classList.add('active');
                
                // Hide all images/videos
                const allMedia = imageSlider.querySelectorAll('img, video');
                allMedia.forEach(media => {
                    media.style.display = 'none';
                    media.classList.remove('active');
                });
                
                // Show images/videos for selected color
                const selectedMedia = imageSlider.querySelectorAll(`[data-color="${selectedColor}"]`);
                selectedMedia.forEach(media => {
                    media.style.display = 'block';
                    media.classList.add('active');
                });
                
                // If no media found for selected color, show default
                if (selectedMedia.length === 0) {
                    const defaultMedia = imageSlider.querySelectorAll('[data-color="default"]');
                    defaultMedia.forEach(media => {
                        media.style.display = 'block';
                        media.classList.add('active');
                    });
                }
            }
        }
    });
    
    // Initialize product image hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        const imageSlider = card.querySelector('.image-slider');
        if (!imageSlider) return;
        
        const images = imageSlider.querySelectorAll('img, video');
        if (images.length < 2) return;
        
        // Show first image by default
        images.forEach((img, index) => {
            if (index === 0) {
                img.style.display = 'block';
                img.classList.add('active');
            } else {
                img.style.display = 'none';
            }
        });
        
        // Hover effect to show back image
        card.addEventListener('mouseenter', function() {
            const activeImage = imageSlider.querySelector('.active');
            const nextImage = activeImage.nextElementSibling;
            
            if (nextImage && nextImage.tagName) {
                activeImage.style.display = 'none';
                activeImage.classList.remove('active');
                nextImage.style.display = 'block';
                nextImage.classList.add('active');
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const allImages = imageSlider.querySelectorAll('img, video');
            allImages.forEach((img, index) => {
                if (index === 0) {
                    img.style.display = 'block';
                    img.classList.add('active');
                } else {
                    img.style.display = 'none';
                    img.classList.remove('active');
                }
            });
        });
    });
});

// Header Modals Functionality for Index Page
function initializeHeaderModals() {
    // Get modal elements
    const userModal = document.getElementById('user-modal');
    const userIcon = document.getElementById('user-icon');
    const signinBtn = document.getElementById('signin-btn');
    const signupBtn = document.getElementById('signup-btn');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const switchToRegister = document.getElementById('switch-to-register');
    const switchToLogin = document.getElementById('switch-to-login');
    
    // Debug: Log found elements
    console.log('Modal elements found:', {
        userModal: !!userModal,
        userIcon: !!userIcon,
        signinBtn: !!signinBtn,
        signupBtn: !!signupBtn,
        loginForm: !!loginForm,
        registerForm: !!registerForm
    });

    // User icon click functionality
    if (userIcon) {
        userIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown) {
                const isVisible = userDropdown.classList.contains('show');
                
                if (isVisible) {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.visibility = 'hidden';
                    userDropdown.style.transform = 'translateY(-10px)';
                    userDropdown.classList.remove('show');
                } else {
                    userDropdown.style.opacity = '1';
                    userDropdown.style.visibility = 'visible';
                    userDropdown.style.transform = 'translateY(0)';
                    userDropdown.classList.add('show');
                }
            }
        });
    }

    // Sign In button functionality - shows login modal
    if (signinBtn) {
        console.log('Sign in button found, adding event listener');
        signinBtn.addEventListener('click', function(e) {
            console.log('Sign in button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Hide dropdown first
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown) {
                userDropdown.style.opacity = '0';
                userDropdown.style.visibility = 'hidden';
                userDropdown.style.transform = 'translateY(-10px)';
                userDropdown.classList.remove('show');
            }
            
            // Show login modal immediately
            if (userModal) {
                console.log('Showing login modal');
                userModal.style.display = 'flex';
                userModal.classList.add('show');
                if (loginForm) {
                    loginForm.style.display = 'flex';
                    loginForm.classList.add('show');
                }
                if (registerForm) {
                    registerForm.style.display = 'none';
                    registerForm.classList.remove('show');
                }
            } else {
                console.log('User modal not found!');
            }
        });
    }

    // Sign Up button functionality - shows register modal
    if (signupBtn) {
        console.log('Sign up button found, adding event listener');
        signupBtn.addEventListener('click', function(e) {
            console.log('Sign up button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Hide dropdown first
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown) {
                userDropdown.style.opacity = '0';
                userDropdown.style.visibility = 'hidden';
                userDropdown.style.transform = 'translateY(-10px)';
                userDropdown.classList.remove('show');
            }
            
            // Show register modal immediately
            if (userModal) {
                console.log('Showing register modal');
                userModal.style.display = 'flex';
                userModal.classList.add('show');
                if (loginForm) {
                    loginForm.style.display = 'none';
                    loginForm.classList.remove('show');
                }
                if (registerForm) {
                    registerForm.style.display = 'flex';
                    registerForm.classList.add('show');
                }
            } else {
                console.log('User modal not found!');
            }
        });
    }

    // Close modal functionality
    const closeButtons = document.querySelectorAll('.close-btn');
    closeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (userModal) {
                userModal.classList.remove('show');
                userModal.style.display = 'none';
            }
        });
    });

    // Close dropdown and modal when clicking outside
    document.addEventListener('click', function(e) {
        const userDropdown = document.getElementById('user-dropdown');
        const userIcon = document.getElementById('user-icon');
        const userModal = document.getElementById('user-modal');
        
        // Close dropdown if clicking outside
        if (userDropdown && userIcon && !userIcon.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.style.opacity = '0';
            userDropdown.style.visibility = 'hidden';
            userDropdown.style.transform = 'translateY(-10px)';
            userDropdown.classList.remove('show');
        }
        
        // Close modal if clicking outside
        if (userModal && !userModal.contains(e.target)) {
            userModal.classList.remove('show');
            userModal.style.display = 'none';
        }
    });

    // Switch between login and register forms
    if (switchToRegister) {
        switchToRegister.addEventListener('click', function(e) {
            e.preventDefault();
            if (loginForm) {
                loginForm.style.display = 'none';
                loginForm.classList.remove('show');
            }
            if (registerForm) {
                registerForm.style.display = 'flex';
                registerForm.classList.add('show');
            }
        });
    }

    if (switchToLogin) {
        switchToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            if (registerForm) {
                registerForm.style.display = 'none';
                registerForm.classList.remove('show');
            }
            if (loginForm) {
                loginForm.style.display = 'flex';
                loginForm.classList.add('show');
            }
        });
    }
}

// Initialize header modals when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing header modals...');
    initializeHeaderModals();
    console.log('Header modals initialized successfully');
});