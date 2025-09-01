<?php
/**
 * Test Checkout Authentication Flow
 * This page demonstrates the checkout authentication system
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Checkout Authentication - Glamour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 102, 204, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0066cc;
            margin-bottom: 10px;
        }
        .test-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .test-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: #0066cc;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .status.authenticated {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.not-authenticated {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: #e6f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .info-box h4 {
            color: #0066cc;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Checkout Authentication Test</h1>
            <p>Test the authentication flow for checkout process</p>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-user"></i> Current Authentication Status</h3>
            <div id="authStatus" class="status">
                <i class="fas fa-spinner fa-spin"></i> Checking...
            </div>
            <button onclick="checkAuthStatus()" class="btn btn-secondary">
                <i class="fas fa-refresh"></i> Refresh Status
            </button>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-credit-card"></i> Test Checkout Process</h3>
            <p>Click the button below to test the checkout authentication flow:</p>
            <button onclick="testCheckout()" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Test Checkout
            </button>
            <p><small>This will simulate clicking "Proceed to Checkout" in the cart</small></p>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-info-circle"></i> How It Works</h3>
            <div class="info-box">
                <h4>Authentication Flow:</h4>
                <ol>
                    <li>User clicks "Proceed to Checkout"</li>
                    <li>System checks if user is authenticated</li>
                    <li>If authenticated: Proceeds to checkout</li>
                    <li>If not authenticated: Shows login/register modal</li>
                    <li>After successful authentication: Proceeds to checkout</li>
                </ol>
            </div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-cog"></i> Test Actions</h3>
            <button onclick="simulateLogin()" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Simulate Login
            </button>
            <button onclick="simulateLogout()" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i> Simulate Logout
            </button>
            <button onclick="goToCart()" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Go to Cart
            </button>
        </div>
    </div>

    <script>
        // Check authentication status
        async function checkAuthStatus() {
            const statusDiv = document.getElementById('authStatus');
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
            
            try {
                const response = await fetch('auth-check.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.authenticated) {
                    statusDiv.className = 'status authenticated';
                    statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> Authenticated as: ${data.username || 'User'}`;
                } else {
                    statusDiv.className = 'status not-authenticated';
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Not authenticated';
                }
            } catch (error) {
                statusDiv.className = 'status not-authenticated';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error checking status';
            }
        }

        // Test checkout process
        function testCheckout() {
            // This simulates the checkout process from cart-unified.php
            checkAuthenticationStatus()
                .then(isAuthenticated => {
                    if (isAuthenticated) {
                        alert('✅ User is authenticated! Proceeding to checkout...');
                        // In real scenario, this would redirect to payment
                        console.log('Would redirect to payment page');
                    } else {
                        alert('❌ User is not authenticated! Would show login/register modal.');
                        // In real scenario, this would show the auth modal
                        console.log('Would show authentication modal');
                    }
                })
                .catch(error => {
                    console.error('Error checking authentication:', error);
                    alert('Error checking authentication status');
                });
        }

        // Check authentication status (same as in cart-unified.php)
        function checkAuthenticationStatus() {
            return fetch('auth-check.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                return data.authenticated === true;
            })
            .catch(error => {
                console.error('Auth check failed:', error);
                return false;
            });
        }

        // Simulate login (for testing)
        function simulateLogin() {
            alert('To test login, please:\n1. Go to the cart page\n2. Add items to cart\n3. Click "Proceed to Checkout"\n4. Use the authentication modal to login/register');
        }

        // Simulate logout (for testing)
        function simulateLogout() {
            alert('To test logout, please:\n1. Login first using the authentication system\n2. Then test the checkout flow again');
        }

        // Go to cart
        function goToCart() {
            window.location.href = 'cart-unified.php?mode=view';
        }

        // Check status on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
        });
    </script>
</body>
</html>
