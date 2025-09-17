<?php
/**
 * Test Cart Fix
 * This page tests the cart functionality fixes
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cart Fix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #0056b3;
        }
        .result {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <h1>🛒 Cart Functionality Test</h1>
    
    <div class="test-section">
        <h2>1. JavaScript Function Tests</h2>
        <button class="test-button" onclick="testToggleCartDropdown()">Test toggleCartDropdown()</button>
        <button class="test-button" onclick="testAddToCart()">Test addToCart()</button>
        <button class="test-button" onclick="testCartAPI()">Test Cart API</button>
        <div id="js-results" class="result info">Click buttons above to test JavaScript functions...</div>
    </div>
    
    <div class="test-section">
        <h2>2. Cart API Tests</h2>
        <button class="test-button" onclick="testCartCount()">Test Cart Count</button>
        <button class="test-button" onclick="testCartSummary()">Test Cart Summary</button>
        <div id="api-results" class="result info">Click buttons above to test Cart API...</div>
    </div>
    
    <div class="test-section">
        <h2>3. Debug Information</h2>
        <div id="debug-info" class="result info">Loading debug information...</div>
    </div>
    
    <!-- Include cart notification system -->
    <?php include 'includes/cart-notification-include.php'; ?>
    
    <script>
        function logResult(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
            element.innerHTML += `<div class="${className}">[${timestamp}] ${message}</div>`;
        }
        
        function testToggleCartDropdown() {
            logResult('js-results', 'Testing toggleCartDropdown function...');
            
            if (typeof window.toggleCartDropdown === 'function') {
                logResult('js-results', '✅ toggleCartDropdown function is available', 'success');
                try {
                    window.toggleCartDropdown();
                    logResult('js-results', '✅ toggleCartDropdown executed successfully', 'success');
                } catch (e) {
                    logResult('js-results', '❌ Error executing toggleCartDropdown: ' + e.message, 'error');
                }
            } else {
                logResult('js-results', '❌ toggleCartDropdown function is not available', 'error');
            }
        }
        
        function testAddToCart() {
            logResult('js-results', 'Testing addToCart function...');
            
            if (typeof window.addToCart === 'function') {
                logResult('js-results', '✅ addToCart function is available', 'success');
                try {
                    const result = window.addToCart('test123', 1, 'red', 'M', 29.99);
                    logResult('js-results', '✅ addToCart executed successfully, result: ' + result, 'success');
                } catch (e) {
                    logResult('js-results', '❌ Error executing addToCart: ' + e.message, 'error');
                }
            } else {
                logResult('js-results', '❌ addToCart function is not available', 'error');
            }
        }
        
        function testCartAPI() {
            logResult('js-results', 'Testing Cart API connection...');
            
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_count'
            })
            .then(response => {
                logResult('js-results', '✅ Cart API responded with status: ' + response.status, 'success');
                return response.json();
            })
            .then(data => {
                logResult('js-results', '✅ Cart API response: ' + JSON.stringify(data), 'success');
            })
            .catch(error => {
                logResult('js-results', '❌ Cart API error: ' + error.message, 'error');
            });
        }
        
        function testCartCount() {
            logResult('api-results', 'Testing cart count API...');
            
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_count'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    logResult('api-results', '✅ Cart count: ' + data.cart_count, 'success');
                } else {
                    logResult('api-results', '❌ Cart count error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                logResult('api-results', '❌ Cart count fetch error: ' + error.message, 'error');
            });
        }
        
        function testCartSummary() {
            logResult('api-results', 'Testing cart summary API...');
            
            fetch('cart-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart_summary'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    logResult('api-results', '✅ Cart summary: ' + JSON.stringify(data), 'success');
                } else {
                    logResult('api-results', '❌ Cart summary error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                logResult('api-results', '❌ Cart summary fetch error: ' + error.message, 'error');
            });
        }
        
        // Load debug information
        document.addEventListener('DOMContentLoaded', function() {
            const debugInfo = document.getElementById('debug-info');
            debugInfo.innerHTML = `
                <div><strong>Page URL:</strong> ${window.location.href}</div>
                <div><strong>User Agent:</strong> ${navigator.userAgent}</div>
                <div><strong>Cart Notification Manager:</strong> ${typeof window.cartNotificationManager !== 'undefined' ? 'Available' : 'Not Available'}</div>
                <div><strong>toggleCartDropdown:</strong> ${typeof window.toggleCartDropdown !== 'undefined' ? 'Available' : 'Not Available'}</div>
                <div><strong>addToCart:</strong> ${typeof window.addToCart !== 'undefined' ? 'Available' : 'Not Available'}</div>
                <div><strong>jQuery:</strong> ${typeof $ !== 'undefined' ? 'Available' : 'Not Available'}</div>
            `;
            
            // Auto-test after 2 seconds
            setTimeout(() => {
                logResult('js-results', 'Running auto-tests...');
                testToggleCartDropdown();
                testAddToCart();
                testCartAPI();
            }, 2000);
        });
    </script>
</body>
</html>

