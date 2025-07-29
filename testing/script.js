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
        const passwordInput = container.querySelector('input[type="password"]');
        const showPasswordSpan = container.querySelector('.show-password');

        if (showPasswordSpan) {
            showPasswordSpan.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    showPasswordSpan.textContent = 'Hide';
                } else {
                    passwordInput.type = 'password';
                    showPasswordSpan.textContent = 'Show';
                }
            });
        }
    });

    // Promotional Banner Close
    const promoCloseButton = document.querySelector('.promo-close');
    const promoBanner = document.querySelector('.promo-banner');

    if (promoCloseButton && promoBanner) {
        promoCloseButton.addEventListener('click', function() {
            promoBanner.style.display = 'none';
        });
    }

    // Heart button toggle
    const heartButtons = document.querySelectorAll('.heart-button');
    heartButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });

    // Color circle selection
    const colorCircles = document.querySelectorAll('.color-circle');
    colorCircles.forEach(circle => {
        circle.addEventListener('click', function() {
            const parentCard = this.closest('.product-card');
            if (parentCard) {
                const activeCircle = parentCard.querySelector('.color-circle.active');
                if (activeCircle) {
                    activeCircle.classList.remove('active');
                }
                this.classList.add('active');
            }
        });
    });

    // Filter/Sort/View options (if applicable, based on full design)
    // Add event listeners for filter/sort/view dropdowns or buttons here
    // Example:
    const filterDropdown = document.getElementById('filter-dropdown');
    if (filterDropdown) {
        filterDropdown.addEventListener('change', function() {
            console.log('Filter by:', this.value);
            // Implement filtering logic
        });
    }

    const sortDropdown = document.getElementById('sort-dropdown');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            console.log('Sort by:', this.value);
            // Implement sorting logic
        });
    }

    const viewDropdown = document.getElementById('view-dropdown');
    if (viewDropdown) {
        viewDropdown.addEventListener('change', function() {
            console.log('View as:', this.value);
            // Implement view change logic (e.g., grid vs list)
        });
    }

    // Add to Cart / View More button hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        const buttons = card.querySelector('.product-buttons');
        if (buttons) {
            card.addEventListener('mouseenter', () => {
                buttons.classList.add('active');
            });
            card.addEventListener('mouseleave', () => {
                buttons.classList.remove('active');
            });
        }
    });

    // Add to Cart notification (example)
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            alert('Item added to cart!');
            // In a real application, you would update cart count, etc.
        });
    });
}); 