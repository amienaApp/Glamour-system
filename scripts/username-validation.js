/**
 * Username Validation Script
 * Provides client-side validation for username fields to only allow letters A-Z and a-z
 */

// Username validation function
function validateUsername(username) {
    // Remove any non-letter characters
    const cleanUsername = username.replace(/[^a-zA-Z]/g, '');
    
    // Check if the cleaned username is different from original
    if (cleanUsername !== username) {
        return {
            isValid: false,
            message: 'Username can only contain letters (A-Z, a-z). Numbers and symbols are not allowed.',
            cleanValue: cleanUsername
        };
    }
    
    // Check if username is empty after cleaning
    if (cleanUsername.length === 0) {
        return {
            isValid: false,
            message: 'Username is required and can only contain letters (A-Z, a-z).',
            cleanValue: ''
        };
    }
    
    return {
        isValid: true,
        message: '',
        cleanValue: cleanUsername
    };
}

// Real-time username validation for input fields
function setupUsernameValidation(inputElement, showError = true) {
    if (!inputElement) return;
    
    // Create error message element
    let errorElement = null;
    if (showError) {
        errorElement = document.createElement('div');
        errorElement.className = 'username-error-message';
        errorElement.style.cssText = `
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        `;
        inputElement.parentNode.appendChild(errorElement);
    }
    
    // Add input event listener for real-time validation
    inputElement.addEventListener('input', function(e) {
        const validation = validateUsername(e.target.value);
        
        if (!validation.isValid) {
            // Show error message
            if (errorElement && showError) {
                errorElement.textContent = validation.message;
                errorElement.style.display = 'block';
            }
            
            // Update input value to clean version
            e.target.value = validation.cleanValue;
            
            // Add error styling
            inputElement.style.borderColor = '#dc3545';
            inputElement.classList.add('username-error');
        } else {
            // Hide error message
            if (errorElement && showError) {
                errorElement.style.display = 'none';
            }
            
            // Remove error styling
            inputElement.style.borderColor = '';
            inputElement.classList.remove('username-error');
        }
    });
    
    // Add blur event listener for final validation
    inputElement.addEventListener('blur', function(e) {
        const validation = validateUsername(e.target.value);
        
        if (!validation.isValid) {
            if (errorElement && showError) {
                errorElement.textContent = validation.message;
                errorElement.style.display = 'block';
            }
            inputElement.style.borderColor = '#dc3545';
            inputElement.classList.add('username-error');
        }
    });
    
    // Add paste event listener to handle pasted content
    inputElement.addEventListener('paste', function(e) {
        setTimeout(() => {
            const validation = validateUsername(e.target.value);
            if (!validation.isValid) {
                e.target.value = validation.cleanValue;
                if (errorElement && showError) {
                    errorElement.textContent = validation.message;
                    errorElement.style.display = 'block';
                }
                inputElement.style.borderColor = '#dc3545';
                inputElement.classList.add('username-error');
            }
        }, 10);
    });
}

// Form validation function
function validateUsernameInForm(formElement) {
    const usernameInputs = formElement.querySelectorAll('input[name="username"], input[id*="username"], input[placeholder*="Username"]');
    let isValid = true;
    
    usernameInputs.forEach(input => {
        const validation = validateUsername(input.value);
        if (!validation.isValid) {
            isValid = false;
            input.style.borderColor = '#dc3545';
            input.classList.add('username-error');
            
            // Show error message
            let errorElement = input.parentNode.querySelector('.username-error-message');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'username-error-message';
                errorElement.style.cssText = `
                    color: #dc3545;
                    font-size: 12px;
                    margin-top: 5px;
                `;
                input.parentNode.appendChild(errorElement);
            }
            errorElement.textContent = validation.message;
            errorElement.style.display = 'block';
        }
    });
    
    return isValid;
}

// Auto-setup validation for all username fields on page load
document.addEventListener('DOMContentLoaded', function() {
    // Find all username input fields
    const usernameInputs = document.querySelectorAll(`
        input[name="username"],
        input[id*="username"],
        input[placeholder*="Username"],
        input[id="login-username"],
        input[id="checkoutUsername"],
        input[id="checkoutRegUsername"],
        input[id="editUsername"]
    `);
    
    // Setup validation for each field
    usernameInputs.forEach(input => {
        setupUsernameValidation(input, true);
    });
    
    // Setup form validation for forms containing username fields
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const hasUsernameField = form.querySelector('input[name="username"], input[id*="username"], input[placeholder*="Username"]');
        if (hasUsernameField) {
            form.addEventListener('submit', function(e) {
                if (!validateUsernameInForm(form)) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
});

// Export functions for manual use
window.UsernameValidation = {
    validate: validateUsername,
    setup: setupUsernameValidation,
    validateForm: validateUsernameInForm
};
