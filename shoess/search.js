// Global Search Functionality
if (typeof GlobalSearch === 'undefined') {
class GlobalSearch {
    constructor() {
        this.searchInput = document.querySelector('.search-input');
        this.searchResults = null;
        this.currentCategory = this.getCurrentCategory();
        this.init();
    }

    getCurrentCategory() {
        const path = window.location.pathname;
        if (path.includes('/perfumes/')) return 'perfumes';
        if (path.includes('/accessories/')) return 'accessories';
        if (path.includes('/bagsfolder/')) return 'bags';
        if (path.includes('/home-decor/')) return 'home-decor';
        if (path.includes('/shoess/')) return 'shoes';
        if (path.includes('/menfolder/')) return 'men';
        if (path.includes('/womenF/')) return 'women';
        if (path.includes('/childrenfolder/')) return 'children';
        return 'all';
    }

    init() {
        if (this.searchInput) {
            this.createSearchResultsContainer();
            this.bindEvents();
        }
    }

    createSearchResultsContainer() {
        // Create search results container
        this.searchResults = document.createElement('div');
        this.searchResults.className = 'search-results-container';
        this.searchResults.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;
        
        // Insert after search container
        const searchContainer = this.searchInput.closest('.search-container');
        if (searchContainer) {
            searchContainer.style.position = 'relative';
            searchContainer.appendChild(this.searchResults);
        }
    }

    bindEvents() {
        // Search input events
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.trim()) {
                this.showResults();
            }
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideResults();
            }
        });

        // Handle search icon click
        const searchIcon = document.querySelector('.search-icon');
        if (searchIcon) {
            searchIcon.addEventListener('click', () => {
                this.performSearch();
            });
        }

        // Handle Enter key
        this.searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.performSearch();
            }
        });
    }

    handleSearch(query) {
        if (query.trim().length < 2) {
            this.hideResults();
            return;
        }

        const results = this.searchProducts(query);
        this.displayResults(results, query);
    }

    searchProducts(query) {
        const searchTerm = query.toLowerCase().trim();
        const results = [];

        // Search in current page products
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const productPrice = card.querySelector('.product-price')?.textContent.toLowerCase() || '';
            const productId = card.getAttribute('data-product-id');
            const productGender = card.getAttribute('data-gender');
            const productCategory = card.getAttribute('data-category');

            if (productName.includes(searchTerm) || 
                productPrice.includes(searchTerm) ||
                productGender?.includes(searchTerm) ||
                productCategory?.includes(searchTerm)) {
                
                results.push({
                    id: productId,
                    name: card.querySelector('.product-name')?.textContent || '',
                    price: card.querySelector('.product-price')?.textContent || '',
                    image: card.querySelector('.product-image img')?.src || '',
                    gender: productGender,
                    category: productCategory,
                    type: 'current-page'
                });
            }
        });

        // Add suggestions for other categories
        const suggestions = this.getSearchSuggestions(searchTerm);
        results.push(...suggestions);

        return results;
    }

    getSearchSuggestions(query) {
        const suggestions = [];
        const searchTerm = query.toLowerCase();

        // Define product categories and their common terms
        const categories = {
            'perfumes': ['perfume', 'cologne', 'fragrance', 'scent', 'aroma', 'eau de toilette', 'parfum'],
            'accessories': ['belt', 'watch', 'sunglasses', 'jewelry', 'necklace', 'bracelet', 'ring', 'earrings', 'socks', 'hat', 'cap', 'tie', 'cufflinks'],
            'bags': ['bag', 'purse', 'handbag', 'tote', 'backpack', 'clutch', 'wallet', 'shoulder bag', 'crossbody'],
            'shoes': ['shoes', 'boots', 'sneakers', 'heels', 'flats', 'sandals', 'loafers', 'pumps'],
            'home-decor': ['decor', 'decoration', 'home', 'furniture', 'lamp', 'vase', 'cushion', 'curtain', 'rug'],
            'clothing': ['dress', 'shirt', 'pants', 'jeans', 'skirt', 'blouse', 'jacket', 'coat', 'sweater']
        };

        // Check if search term matches any category
        Object.entries(categories).forEach(([category, terms]) => {
            if (terms.some(term => term.includes(searchTerm) || searchTerm.includes(term))) {
                suggestions.push({
                    id: `suggestion-${category}`,
                    name: `Search ${category}`,
                    price: '',
                    image: '',
                    gender: '',
                    category: category,
                    type: 'suggestion',
                    url: this.getCategoryUrl(category)
                });
            }
        });

        return suggestions;
    }

    getCategoryUrl(category) {
        const baseUrl = window.location.origin;
        const categoryUrls = {
            'perfumes': '/perfumes/',
            'accessories': '/accessories/',
            'bags': '/bagsfolder/',
            'shoes': '/shoess/',
            'home-decor': '/home-decor/',
            'men': '/menfolder/',
            'women': '/womenF/',
            'children': '/childrenfolder/'
        };
        return baseUrl + (categoryUrls[category] || '/');
    }

    displayResults(results, query) {
        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="search-no-results">
                    <p>No results found for "${query}"</p>
                    <p>Try searching for different keywords</p>
                </div>
            `;
        } else {
            let html = '';
            
            // Group results by type
            const currentPageResults = results.filter(r => r.type === 'current-page');
            const suggestions = results.filter(r => r.type === 'suggestion');

            // Current page results
            if (currentPageResults.length > 0) {
                html += '<div class="search-section"><h4>Products on this page</h4>';
                currentPageResults.forEach(result => {
                    html += this.createResultItem(result);
                });
                html += '</div>';
            }

            // Suggestions
            if (suggestions.length > 0) {
                html += '<div class="search-section"><h4>Search other categories</h4>';
                suggestions.forEach(result => {
                    html += this.createResultItem(result);
                });
                html += '</div>';
            }

            this.searchResults.innerHTML = html;
        }

        this.showResults();
        this.bindResultEvents();
    }

    createResultItem(result) {
        if (result.type === 'suggestion') {
            return `
                <div class="search-result-item suggestion" data-url="${result.url}">
                    <div class="result-content">
                        <div class="result-info">
                            <h5>${result.name}</h5>
                            <p>Browse ${result.category} products</p>
                        </div>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            `;
        }

        return `
            <div class="search-result-item product" data-product-id="${result.id}">
                <div class="result-image">
                    <img src="${result.image}" alt="${result.name}" onerror="this.style.display='none'">
                </div>
                <div class="result-content">
                    <div class="result-info">
                        <h5>${result.name}</h5>
                        <p class="result-price">${result.price}</p>
                        <p class="result-category">${result.category} • ${result.gender}</p>
                    </div>
                    <button class="quick-view-btn" data-product-id="${result.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        `;
    }

    bindResultEvents() {
        // Handle suggestion clicks
        this.searchResults.querySelectorAll('.search-result-item.suggestion').forEach(item => {
            item.addEventListener('click', () => {
                const url = item.getAttribute('data-url');
                window.location.href = url;
            });
        });

        // Handle product quick view
        this.searchResults.querySelectorAll('.quick-view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const productId = btn.getAttribute('data-product-id');
                this.openQuickView(productId);
            });
        });

        // Handle product item clicks
        this.searchResults.querySelectorAll('.search-result-item.product').forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.getAttribute('data-product-id');
                this.openQuickView(productId);
            });
        });
    }

    openQuickView(productId) {
        // Trigger the existing quick view functionality
        const quickViewBtn = document.querySelector(`[data-product-id="${productId}"] .quick-view`);
        if (quickViewBtn) {
            quickViewBtn.click();
        }
        this.hideResults();
    }

    performSearch() {
        const query = this.searchInput.value.trim();
        if (query) {
            // If we're on a specific category page, search within that category
            if (this.currentCategory !== 'all') {
                this.searchInCategory(query);
            } else {
                // Global search - redirect to search results page
                this.redirectToSearch(query);
            }
        }
    }

    searchInCategory(query) {
        // Filter products on current page
        const productCards = document.querySelectorAll('.product-card');
        let hasResults = false;

        productCards.forEach(card => {
            const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const productPrice = card.querySelector('.product-price')?.textContent.toLowerCase() || '';
            const productGender = card.getAttribute('data-gender')?.toLowerCase() || '';
            const productCategory = card.getAttribute('data-category')?.toLowerCase() || '';

            const searchTerm = query.toLowerCase();
            const isMatch = productName.includes(searchTerm) || 
                           productPrice.includes(searchTerm) ||
                           productGender.includes(searchTerm) ||
                           productCategory.includes(searchTerm);

            if (isMatch) {
                card.style.display = 'block';
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        this.showNoResultsMessage(!hasResults, query);
    }

    showNoResultsMessage(show, query) {
        let noResultsMsg = document.querySelector('.no-results-message');
        
        if (show) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.style.cssText = `
                    text-align: center;
                    padding: 40px 20px;
                    color: #666;
                    font-size: 16px;
                `;
                
                const productGrid = document.querySelector('.product-grid');
                if (productGrid) {
                    productGrid.appendChild(noResultsMsg);
                }
            }
            noResultsMsg.innerHTML = `
                <h3>No results found for "${query}"</h3>
                <p>Try adjusting your search terms or browse our categories</p>
                <button onclick="window.location.reload()" style="
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                ">Show All Products</button>
            `;
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    redirectToSearch(query) {
        // For global search, you could redirect to a search results page
        // For now, we'll just show an alert
        alert(`Global search for "${query}" - This would redirect to a search results page`);
    }

    showResults() {
        this.searchResults.style.display = 'block';
    }

    hideResults() {
        this.searchResults.style.display = 'none';
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new GlobalSearch();
});

// Add CSS styles for search results
const searchStyles = `
<style>
.search-results-container {
    font-family: Arial, sans-serif;
}

.search-section {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.search-section:last-child {
    border-bottom: none;
}

.search-section h4 {
    margin: 0 15px 10px;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item.suggestion {
    justify-content: space-between;
}

.search-result-item.product {
    gap: 12px;
}

.result-image {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.result-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.result-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.result-info h5 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.result-price {
    margin: 0 0 2px 0;
    font-size: 13px;
    font-weight: 600;
    color: #007bff;
}

.result-category {
    margin: 0;
    font-size: 11px;
    color: #666;
    text-transform: capitalize;
}

.quick-view-btn {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    padding: 5px;
    border-radius: 3px;
    transition: background-color 0.2s;
}

.quick-view-btn:hover {
    background-color: #e3f2fd;
}

.search-no-results {
    padding: 20px;
    text-align: center;
    color: #666;
}

.search-no-results p {
    margin: 5px 0;
}

.search-results-container::-webkit-scrollbar {
    width: 6px;
}

.search-results-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.search-results-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.search-results-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
`;

// Inject styles into head
document.head.insertAdjacentHTML('beforeend', searchStyles);

} // End of GlobalSearch class conditional declaration
