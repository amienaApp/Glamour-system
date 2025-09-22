/**
 * Mobile Sidebar Toggle Functionality
 * Universal script for all category pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create mobile sidebar toggle button
    createMobileSidebarToggle();
    
    // Initialize mobile sidebar functionality
    initializeMobileSidebar();
});

function createMobileSidebarToggle() {
    // Check if toggle button already exists
    if (document.querySelector('.mobile-sidebar-toggle')) {
        return;
    }
    
    // Create toggle button
    const toggleButton = document.createElement('button');
    toggleButton.className = 'mobile-sidebar-toggle';
    toggleButton.innerHTML = '<i class="fas fa-filter"></i>';
    toggleButton.setAttribute('aria-label', 'Toggle Filters');
    toggleButton.title = 'Show Filters';
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'mobile-sidebar-overlay';
    
    // Add to page
    document.body.appendChild(toggleButton);
    document.body.appendChild(overlay);
}

function initializeMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleButton = document.querySelector('.mobile-sidebar-toggle');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    const body = document.body;
    
    if (!sidebar || !toggleButton || !overlay) {
        console.warn('Mobile sidebar elements not found');
        return;
    }
    
    // Function to open sidebar
    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        body.classList.add('sidebar-open');
        toggleButton.innerHTML = '<i class="fas fa-times"></i>';
        toggleButton.title = 'Hide Filters';
        
        // Prevent body scroll
        body.style.overflow = 'hidden';
    }
    
    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('sidebar-open');
        toggleButton.innerHTML = '<i class="fas fa-filter"></i>';
        toggleButton.title = 'Show Filters';
        
        // Restore body scroll
        body.style.overflow = '';
    }
    
    // Toggle sidebar when button is clicked
    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });
    
    // Close sidebar when overlay is clicked
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeSidebar();
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 767 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(e.target) && 
            !toggleButton.contains(e.target)) {
            closeSidebar();
        }
    });
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 767) {
            // Desktop view - close mobile sidebar
            closeSidebar();
        }
    });
    
    // Close sidebar when filter is applied (optional)
    const filterButtons = document.querySelectorAll('.filter-option input, .clear-all-filters-btn');
    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            // Close sidebar after a short delay to allow filter to process
            setTimeout(() => {
                if (window.innerWidth <= 767) {
                    closeSidebar();
                }
            }, 300);
        });
    });
    
    // Handle clear all filters button
    const clearAllBtn = document.querySelector('.clear-all-filters-btn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            setTimeout(() => {
                if (window.innerWidth <= 767) {
                    closeSidebar();
                }
            }, 300);
        });
    }
}

// Utility function to check if mobile sidebar is open
function isMobileSidebarOpen() {
    const sidebar = document.querySelector('.sidebar');
    return sidebar && sidebar.classList.contains('active');
}

// Utility function to close mobile sidebar programmatically
function closeMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.mobile-sidebar-overlay');
    const toggleButton = document.querySelector('.mobile-sidebar-toggle');
    const body = document.body;
    
    if (sidebar && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('sidebar-open');
        toggleButton.innerHTML = '<i class="fas fa-filter"></i>';
        toggleButton.title = 'Show Filters';
        body.style.overflow = '';
    }
}

// Make functions globally available
window.closeMobileSidebar = closeMobileSidebar;
window.isMobileSidebarOpen = isMobileSidebarOpen;

