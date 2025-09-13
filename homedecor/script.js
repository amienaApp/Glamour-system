// Unified Home Decor Script

// Quick View button click handler - use universal quickview manager
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('quick-view') || e.target.closest('.quick-view')) {
        e.preventDefault();
        const button = e.target.classList.contains('quick-view') ? e.target : e.target.closest('.quick-view');
        const productId = button.getAttribute('data-product-id');
        if (productId && window.quickviewManager) {
            window.quickviewManager.openQuickview(productId);
        } else if (productId) {
            // Fallback to global function
            window.openQuickView(productId);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Home Decor script loaded successfully');
    
    // Initialize all functionality
    initializeCategoryModals();
    initializeHeaderModals();
    initializeFilters();
    
    // Category Modal Functionality
    function initializeCategoryModals() {
        const categoryModals = document.querySelectorAll('.category-modal');
        const modalTriggers = document.querySelectorAll('.modal-trigger');
        
        // Handle subcategory item clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.subcategory-item')) {
                const subcategoryItem = e.target.closest('.subcategory-item');
                const category = subcategoryItem.getAttribute('data-category');
                const subcategory = subcategoryItem.getAttribute('data-subcategory');
                
                if (category && subcategory) {
                    // Redirect to the appropriate page
                    window.location.href = `../${category}/${subcategory}.php`;
                }
            }
        });
        
        // Handle modal triggers
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetModal = this.getAttribute('data-modal');
                const modal = document.getElementById(targetModal);
                
                if (modal) {
                    // Close all other modals first
                    categoryModals.forEach(m => {
                            m.style.opacity = '0';
                            m.style.visibility = 'hidden';
                    });
                    
                    // Show target modal
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                }
            });
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item-modal')) {
                categoryModals.forEach(modal => {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                });
            }
        });
    }
    
    // Header Modals Functionality
    function initializeHeaderModals() {
        // Get modal elements
        const userModal = document.getElementById('user-modal');
        const userIcon = document.querySelector('.user-icon');
        const flagContainer = document.querySelector('.flag-container');
        const closeLoginModal = document.getElementById('close-login-modal');
        const closeRegisterModal = document.getElementById('close-register-modal');
        
        // User Icon Click - Open User Modal
        if (userIcon) {
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(userModal);
            });
        }
        
        // Flag Container Click - Disabled (Coming Soon)
        if (flagContainer) {
            flagContainer.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Region settings coming soon!');
            });
        }
        
        // Close Login Modal
        if (closeLoginModal) {
            closeLoginModal.addEventListener('click', function() {
                closeModal(userModal);
                // Reset to login form when closing
                switchToLoginForm();
            });
        }
        
        // Close Register Modal
        if (closeRegisterModal) {
            closeRegisterModal.addEventListener('click', function() {
                closeModal(userModal);
                // Reset to login form when closing
                switchToLoginForm();
            });
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target);
                // Reset to login form when closing
                switchToLoginForm();
            }
        });
        
        // Show/hide password functionality
        const showPasswordBtns = document.querySelectorAll('.show-password');
        
        showPasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }
    
    // Filter Functionality
    function initializeFilters() {
        // Add any filter-specific initialization here
        console.log('Filters initialized');
    }
});


