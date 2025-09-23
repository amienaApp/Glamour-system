/**
 * Username Validation Script
 * Provides real-time validation for username fields with dynamic title messages
 */

function initializeUsernameValidation() {
    // Find all username input fields
    const usernameInputs = document.querySelectorAll('input[name="username"], input[id="username"]');
    
    usernameInputs.forEach(function(input) {
        // Add event listeners for real-time validation
        input.addEventListener('input', function() {
            validateUsernameInput(this);
        });
        
        input.addEventListener('keypress', function(e) {
            // Prevent non-letter characters from being typed
            const char = String.fromCharCode(e.which);
            if (!/[a-zA-Z]/.test(char)) {
                e.preventDefault();
                showUsernameError(this, 'Only letters allowed');
                return false;
            }
        });
        
        input.addEventListener('paste', function(e) {
            // Handle paste events
            setTimeout(() => {
                validateUsernameInput(this);
            }, 10);
        });
        
        // Initial validation
        validateUsernameInput(input);
    });
}

function validateUsernameInput(input) {
    const value = input.value;
    const originalTitle = input.getAttribute('data-original-title') || 'Username must contain only letters (a-z, A-Z)';
    
    // Check if value contains only letters
    if (value.length === 0) {
        input.title = originalTitle;
        input.style.borderColor = '';
        removeUsernameError(input);
        return true;
    }
    
    // Check for invalid characters
    const invalidChars = value.match(/[^a-zA-Z]/g);
    if (invalidChars) {
        const uniqueInvalidChars = [...new Set(invalidChars)];
        let message = 'Only letters allowed';
        let title = 'Only letters allowed';
        
        if (uniqueInvalidChars.length <= 3) {
            message = `Invalid characters: ${uniqueInvalidChars.join(', ')}`;
            title = `Only letters allowed - Found: ${uniqueInvalidChars.join(', ')}`;
        }
        
        input.title = title;
        input.style.borderColor = '#e74c3c';
        showUsernameError(input, message);
        return false;
    } else if (value.length < 3) {
        // Too short
        const remaining = 3 - value.length;
        input.title = `Username must be at least 3 characters (${remaining} more needed)`;
        input.style.borderColor = '#f39c12';
        showUsernameError(input, `Username must be at least 3 characters (${remaining} more needed)`);
        return false;
    } else if (value.length > 20) {
        // Too long
        const excess = value.length - 20;
        input.title = `Username must be less than 20 characters (${excess} too many)`;
        input.style.borderColor = '#f39c12';
        showUsernameError(input, `Username must be less than 20 characters (${excess} too many)`);
        return false;
    } else {
        // Valid
        input.title = originalTitle;
        input.style.borderColor = '#27ae60';
        removeUsernameError(input);
        return true;
    }
}

function showUsernameError(input, message) {
    // Remove existing error message
    removeUsernameError(input);
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'username-error-message';
    errorDiv.style.cssText = `
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
        display: block;
        animation: fadeIn 0.3s ease-in;
    `;
    errorDiv.textContent = message;
    
    // Insert after the input field
    input.parentNode.insertBefore(errorDiv, input.nextSibling);
    
    // Add CSS animation if not already added
    if (!document.getElementById('username-validation-styles')) {
        const style = document.createElement('style');
        style.id = 'username-validation-styles';
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-5px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .username-error-message {
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }
}

function removeUsernameError(input) {
    const errorMessage = input.parentNode.querySelector('.username-error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeUsernameValidation);
} else {
    initializeUsernameValidation();
}

// Also initialize when the script is loaded (for dynamic content)
initializeUsernameValidation();
