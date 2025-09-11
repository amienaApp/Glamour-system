/**
 * Reviews Manager
 * Handles product reviews functionality
 */

class ReviewsManager {
    constructor() {
        this.reviews = new Map();
        this.initialized = false;
    }

    /**
     * Initialize the reviews manager
     */
    init() {
        if (this.initialized) return;
        
        console.log('Reviews Manager initialized');
        this.initialized = true;
    }

    /**
     * Load reviews for a product
     * @param {string} productId - The product ID
     * @returns {Promise<Array>} Array of reviews
     */
    async loadReviews(productId) {
        try {
            // For now, return mock data
            // In a real implementation, this would fetch from an API
            const mockReviews = [
                {
                    id: 1,
                    productId: productId,
                    rating: 4,
                    comment: "Great product!",
                    author: "John Doe",
                    date: new Date().toISOString()
                },
                {
                    id: 2,
                    productId: productId,
                    rating: 5,
                    comment: "Love it!",
                    author: "Jane Smith",
                    date: new Date().toISOString()
                }
            ];

            this.reviews.set(productId, mockReviews);
            return mockReviews;
        } catch (error) {
            console.error('Error loading reviews:', error);
            return [];
        }
    }

    /**
     * Get average rating for a product
     * @param {string} productId - The product ID
     * @returns {number} Average rating
     */
    getAverageRating(productId) {
        const reviews = this.reviews.get(productId) || [];
        if (reviews.length === 0) return 0;
        
        const sum = reviews.reduce((total, review) => total + review.rating, 0);
        return sum / reviews.length;
    }

    /**
     * Get review count for a product
     * @param {string} productId - The product ID
     * @returns {number} Number of reviews
     */
    getReviewCount(productId) {
        const reviews = this.reviews.get(productId) || [];
        return reviews.length;
    }

    /**
     * Add a new review
     * @param {string} productId - The product ID
     * @param {Object} review - Review data
     */
    async addReview(productId, review) {
        try {
            // In a real implementation, this would save to a database
            const reviews = this.reviews.get(productId) || [];
            const newReview = {
                id: Date.now(),
                productId: productId,
                rating: review.rating,
                comment: review.comment,
                author: review.author || 'Anonymous',
                date: new Date().toISOString()
            };
            
            reviews.push(newReview);
            this.reviews.set(productId, reviews);
            
            return newReview;
        } catch (error) {
            console.error('Error adding review:', error);
            throw error;
        }
    }
}

// Create global instance
window.reviewsManager = new ReviewsManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.reviewsManager.init();
});



