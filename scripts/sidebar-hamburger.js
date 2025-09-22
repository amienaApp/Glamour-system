/**
 * Sidebar Hamburger Toggle Functionality
 * Creates a small hamburger menu at the top of the sidebar for collapsing/expanding filters
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Sidebar hamburger script loaded');
    
    // Small delay to ensure DOM is fully ready
    setTimeout(() => {
        // Create sidebar hamburger if it doesn't exist
        createSidebarHamburger();
        
        // Initialize sidebar hamburger functionality
        initializeSidebarHamburger();
    }, 100);
});

function createSidebarHamburger() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) {
        console.warn('Sidebar not found');
        return;
    }
    
    console.log('Sidebar found, creating hamburger');
    
    // Check if hamburger already exists
    if (document.querySelector('.sidebar-hamburger')) {
        console.log('Sidebar hamburger already exists');
        return;
    }
    
    // Create hamburger element
    const hamburger = document.createElement('div');
    hamburger.className = 'sidebar-hamburger';
    hamburger.innerHTML = `
        <div class="sidebar-hamburger-content">
            <h3 class="sidebar-hamburger-title">Filters</h3>
            <button class="sidebar-hamburger-icon" aria-label="Toggle Filters">
                <div class="sidebar-hamburger-line"></div>
                <div class="sidebar-hamburger-line"></div>
                <div class="sidebar-hamburger-line"></div>
            </button>
        </div>
    `;
    
    // Add a temporary border for debugging
    hamburger.style.border = '2px solid red';
    
    // Insert hamburger at the beginning of the sidebar
    sidebar.insertBefore(hamburger, sidebar.firstChild);
    
    // Wrap the rest of the content (everything except the hamburger)
    const sidebarContent = document.createElement('div');
    sidebarContent.className = 'sidebar-content';
    
    // Move all existing sidebar content (except hamburger) into the wrapper
    const existingContent = Array.from(sidebar.children);
    existingContent.forEach(child => {
        if (!child.classList.contains('sidebar-hamburger')) {
            sidebarContent.appendChild(child);
        }
    });
    
    // Add content wrapper after hamburger
    sidebar.appendChild(sidebarContent);
    
    console.log('Sidebar hamburger created successfully');
}

function initializeSidebarHamburger() {
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.sidebar-hamburger');
    const hamburgerIcon = document.querySelector('.sidebar-hamburger-icon');
    const sidebarContent = document.querySelector('.sidebar-content');
    
    if (!sidebar || !hamburger || !hamburgerIcon || !sidebarContent) {
        console.warn('Sidebar hamburger elements not found');
        return;
    }
    
    // Function to toggle sidebar content
    function toggleSidebarContent() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('collapsed');
            hamburger.classList.remove('active');
            hamburgerIcon.setAttribute('aria-label', 'Collapse Filters');
            
            // Update title
            const title = hamburger.querySelector('.sidebar-hamburger-title');
            if (title) {
                title.textContent = 'Filters';
            }
        } else {
            // Collapse sidebar
            sidebar.classList.add('collapsed');
            hamburger.classList.add('active');
            hamburgerIcon.setAttribute('aria-label', 'Expand Filters');
            
            // Update title
            const title = hamburger.querySelector('.sidebar-hamburger-title');
            if (title) {
                title.textContent = 'Show Filters';
            }
        }
    }
    
    // Add click event to hamburger
    hamburger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebarContent();
    });
    
    // Add click event to hamburger icon specifically
    hamburgerIcon.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebarContent();
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // On desktop, always show sidebar content
        if (window.innerWidth > 991) {
            sidebar.classList.remove('collapsed');
            hamburger.classList.remove('active');
            const title = hamburger.querySelector('.sidebar-hamburger-title');
            if (title) {
                title.textContent = 'Filters';
            }
        }
    });
    
    // Initialize state based on screen size
    if (window.innerWidth <= 991) {
        // On tablet/mobile, start collapsed
        sidebar.classList.add('collapsed');
        hamburger.classList.add('active');
        const title = hamburger.querySelector('.sidebar-hamburger-title');
        if (title) {
            title.textContent = 'Show Filters';
        }
    }
}

// Utility function to expand sidebar programmatically
function expandSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.sidebar-hamburger');
    const hamburgerIcon = document.querySelector('.sidebar-hamburger-icon');
    
    if (sidebar && sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
        hamburger.classList.remove('active');
        hamburgerIcon.setAttribute('aria-label', 'Collapse Filters');
        
        const title = hamburger.querySelector('.sidebar-hamburger-title');
        if (title) {
            title.textContent = 'Filters';
        }
    }
}

// Utility function to collapse sidebar programmatically
function collapseSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.sidebar-hamburger');
    const hamburgerIcon = document.querySelector('.sidebar-hamburger-icon');
    
    if (sidebar && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        hamburger.classList.add('active');
        hamburgerIcon.setAttribute('aria-label', 'Expand Filters');
        
        const title = hamburger.querySelector('.sidebar-hamburger-title');
        if (title) {
            title.textContent = 'Show Filters';
        }
    }
}

// Make functions globally available
window.expandSidebar = expandSidebar;
window.collapseSidebar = collapseSidebar;
