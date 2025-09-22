/**
 * Filters Hamburger Menu Functionality
 * Creates a hamburger menu next to the sort dropdown that shows all sidebar filters
 */

// Multiple ways to ensure the script runs
document.addEventListener('DOMContentLoaded', function() {
    console.log('Filters hamburger script loaded');
    initializeFiltersHamburgerSystem();
});

// Also try when window loads
window.addEventListener('load', function() {
    console.log('Window loaded, checking filters hamburger');
    if (!document.querySelector('.filters-hamburger')) {
        initializeFiltersHamburgerSystem();
    }
});

// And with a timeout as backup
setTimeout(function() {
    console.log('Timeout check for filters hamburger');
    if (!document.querySelector('.filters-hamburger')) {
        initializeFiltersHamburgerSystem();
    }
}, 1000);

function initializeFiltersHamburgerSystem() {
    console.log('Initializing filters hamburger system');
    
    // Create filters hamburger if it doesn't exist
    createFiltersHamburger();
    
    // Initialize filters hamburger functionality
    initializeFiltersHamburger();
}

function createFiltersHamburger() {
    const contentControls = document.querySelector('.content-controls');
    if (!contentControls) {
        console.warn('Content controls not found');
        return;
    }
    
    // Check if hamburger already exists
    if (document.querySelector('.filters-hamburger')) {
        console.log('Filters hamburger already exists');
        return;
    }
    
    console.log('Creating filters hamburger');
    
    // Create hamburger element
    const hamburger = document.createElement('div');
    hamburger.className = 'filters-hamburger';
    hamburger.innerHTML = `
        <div class="filters-hamburger-icon">
            <div class="filters-hamburger-line"></div>
            <div class="filters-hamburger-line"></div>
            <div class="filters-hamburger-line"></div>
        </div>
        <span class="filters-hamburger-text">Filters</span>
    `;
    
    // Create dropdown panel
    const dropdown = document.createElement('div');
    dropdown.className = 'filters-dropdown';
    dropdown.innerHTML = `
        <div class="filters-dropdown-content">
            <div class="filters-dropdown-header">
                <h3 class="filters-dropdown-title">Filters</h3>
                <button class="filters-dropdown-close" aria-label="Close Filters">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="filters-dropdown-filters">
                <!-- Filters will be populated from sidebar -->
            </div>
        </div>
    `;
    
    // Add to content controls
    contentControls.appendChild(hamburger);
    contentControls.appendChild(dropdown);
    
    console.log('Filters hamburger created successfully');
}

function initializeFiltersHamburger() {
    const hamburger = document.querySelector('.filters-hamburger');
    const dropdown = document.querySelector('.filters-dropdown');
    const closeBtn = document.querySelector('.filters-dropdown-close');
    const filtersContainer = document.querySelector('.filters-dropdown-filters');
    
    if (!hamburger || !dropdown || !closeBtn || !filtersContainer) {
        console.warn('Filters hamburger elements not found');
        return;
    }
    
    // Copy sidebar content to dropdown with a small delay
    setTimeout(() => {
        copySidebarToDropdown(filtersContainer);
    }, 200);
    
    // Function to toggle dropdown
    function toggleDropdown() {
        const isActive = dropdown.classList.contains('active');
        
        if (isActive) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }
    
    // Function to open dropdown
    function openDropdown() {
        dropdown.classList.add('active');
        hamburger.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Function to close dropdown
    function closeDropdown() {
        dropdown.classList.remove('active');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Add click event to hamburger
    hamburger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleDropdown();
    });
    
    // Add click event to close button
    closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeDropdown();
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (dropdown.classList.contains('active') && 
            !hamburger.contains(e.target) && 
            !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });
    
    // Close dropdown on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && dropdown.classList.contains('active')) {
            closeDropdown();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            // Desktop view - close dropdown
            closeDropdown();
        }
    });
    
    console.log('Filters hamburger initialized successfully');
}

function copySidebarToDropdown(container) {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) {
        console.warn('Sidebar not found for copying content');
        createFallbackFilters(container);
        return;
    }
    
    console.log('Copying sidebar content to dropdown');
    
    // Clone the sidebar content
    const sidebarClone = sidebar.cloneNode(true);
    
    // Remove any existing hamburger elements from clone
    const existingHamburgers = sidebarClone.querySelectorAll('.sidebar-hamburger, .mobile-sidebar-toggle');
    existingHamburgers.forEach(el => el.remove());
    
    // Clear the container first
    container.innerHTML = '';
    
    // Add the cloned content to dropdown
    container.appendChild(sidebarClone);
    
    console.log('Sidebar content copied to dropdown successfully');
    console.log('Container children count:', container.children.length);
    
    // If no content was copied, create fallback
    if (container.children.length === 0) {
        console.log('No content copied, creating fallback filters');
        createFallbackFilters(container);
    }
}

function createFallbackFilters(container) {
    console.log('Creating fallback filters');
    
    container.innerHTML = `
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Category</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="handbags">
                        <span>Handbags</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="backpacks">
                        <span>Backpacks</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="clutches">
                        <span>Clutches</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Color</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="black">
                        <span>Black</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="brown">
                        <span>Brown</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="red">
                        <span>Red</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="color[]" value="blue">
                        <span>Blue</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Price</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="0-50">
                        <span>$0 - $50</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="50-100">
                        <span>$50 - $100</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="100-200">
                        <span>$100 - $200</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="200+">
                        <span>$200+</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <div class="filter-header">
                    <h4>Gender</h4>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="women">
                        <span>Women</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="men">
                        <span>Men</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="gender[]" value="unisex">
                        <span>Unisex</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e8e8e8;">
            <button type="button" class="clear-all-filters-btn" style="width: 100%; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; color: #6c757d; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;">
                <i class="fas fa-times"></i>
                Clear All Filters
            </button>
        </div>
    `;
    
    console.log('Fallback filters created successfully');
}

// Utility function to close filters dropdown programmatically
function closeFiltersDropdown() {
    const dropdown = document.querySelector('.filters-dropdown');
    const hamburger = document.querySelector('.filters-hamburger');
    
    if (dropdown && dropdown.classList.contains('active')) {
        dropdown.classList.remove('active');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Make function globally available
window.closeFiltersDropdown = closeFiltersDropdown;
