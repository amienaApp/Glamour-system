/**
 * Glamour Admin Sidebar JavaScript
 * Handles mobile menu toggle and sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create mobile menu toggle button
    createMobileMenuToggle();
    
    // Handle mobile menu toggle
    handleMobileMenuToggle();
    
    // Handle sidebar interactions
    handleSidebarInteractions();
});

/**
 * Create mobile menu toggle button
 */
function createMobileMenuToggle() {
    // Check if button already exists
    if (document.querySelector('.mobile-menu-toggle')) {
        return;
    }
    
    const toggleButton = document.createElement('button');
    toggleButton.className = 'mobile-menu-toggle';
    toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
    toggleButton.setAttribute('aria-label', 'Toggle mobile menu');
    
    document.body.appendChild(toggleButton);
}

/**
 * Handle mobile menu toggle functionality
 */
function handleMobileMenuToggle() {
    const toggleButton = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (!toggleButton || !sidebar) return;
    
    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        
        // Update button icon
        const icon = toggleButton.querySelector('i');
        if (sidebar.classList.contains('active')) {
            icon.className = 'fas fa-times';
        } else {
            icon.className = 'fas fa-bars';
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggleButton.contains(e.target)) {
                sidebar.classList.remove('active');
                toggleButton.querySelector('i').className = 'fas fa-bars';
            }
        }
    });
}

/**
 * Handle sidebar interactions and animations
 */
function handleSidebarInteractions() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        // Add ripple effect on click
        item.addEventListener('click', function(e) {
            if (!this.href || this.href === '#') {
                e.preventDefault();
                return;
            }
            
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 107, 157, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add CSS for ripple animation
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * Update active navigation item based on current page
 */
function updateActiveNavItem() {
    const currentPage = window.location.pathname.split('/').pop();
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// Update active nav item when page loads
document.addEventListener('DOMContentLoaded', updateActiveNavItem);
