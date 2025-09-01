<?php
/**
 * Test Cart Flow
 * This page demonstrates the new cart flow: add to cart without auth, redirect to register/login for checkout
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cart Flow - Glamour</title>
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
        .flow-step {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .flow-step.completed {
            border-color: #28a745;
            background: #f8fff9;
        }
        .flow-step.current {
            border-color: #0066cc;
            background: #f0f8ff;
        }
        .step-number {
            background: #6c757d;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .step-number.completed {
            background: #28a745;
        }
        .step-number.current {
            background: #0066cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> New Cart Flow Test</h1>
            <p>Test the updated cart flow: Add to cart without auth → Register → Login → Checkout</p>
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
            <h3><i class="fas fa-route"></i> New Cart Flow</h3>
            <div class="info-box">
                <h4>🔄 Complete Flow:</h4>
                <div class="flow-step">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Add to Cart (No Auth Required)</strong><br>
                        <small>Users can add items to cart without signing in</small>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-number">2</div>
                    <div>
                        <strong>Click "Proceed to Checkout"</strong><br>
                        <small>System redirects to registration form</small>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-number">3</div>
                    <div>
                        <strong>Complete Registration</strong><br>
                        <small>After successful registration, redirects to login</small>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-number">4</div>
                    <div>
                        <strong>Login</strong><br>
                        <small>After successful login, redirects back to cart</small>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-number">5</div>
                    <div>
                        <strong>Proceed to Checkout</strong><br>
                        <small>Now authenticated, user can complete checkout</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-play"></i> Test the Flow</h3>
            <p>Follow these steps to test the complete flow:</p>
            <ol>
                <li><strong>Logout first</strong> (if logged in) to test as unauthenticated user</li>
                <li><strong>Add items to cart</strong> - should work without authentication</li>
                <li><strong>Click "Proceed to Checkout"</strong> - should redirect to registration</li>
                <li><strong>Complete registration</strong> - should redirect to login</li>
                <li><strong>Login</strong> - should redirect back to cart</li>
                <li><strong>Proceed to checkout</strong> - should now work normally</li>
            </ol>
            
            <div style="display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
                <button onclick="goToCart()" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Go to Cart
                </button>
                <button onclick="logout()" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
                <button onclick="goToProducts()" class="btn btn-secondary">
                    <i class="fas fa-shopping-bag"></i> Browse Products
                </button>
            </div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-info-circle"></i> What Changed</h3>
            <div class="info-box">
                <h4>✅ New Features:</h4>
                <ul>
                    <li><strong>Session-Based Cart:</strong> Unauthenticated users can add items to cart</li>
                    <li><strong>Checkout Redirect:</strong> Clicking checkout redirects to registration</li>
                    <li><strong>Registration Flow:</strong> After registration, redirects to login</li>
                    <li><strong>Login Redirect:</strong> After login, redirects back to cart</li>
                    <li><strong>Cart Transfer:</strong> Session cart is transferred to user account on login</li>
                    <li><strong>Seamless Experience:</strong> Users don't lose their cart items</li>
                </ul>
            </div>
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
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Not authenticated (Can still add to cart)';
                }
            } catch (error) {
                statusDiv.className = 'status not-authenticated';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error checking status';
            }
        }

        // Go to cart
        function goToCart() {
            window.location.href = 'cart-unified.php?mode=view';
        }

        // Logout
        function logout() {
            fetch('menfolder/logout-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Logged out successfully!');
                    checkAuthStatus();
                } else {
                    alert('Logout failed: ' + data.message);
                }
            })
            .catch(error => {
                alert('Logout error: ' + error.message);
            });
        }

        // Go to products
        function goToProducts() {
            window.location.href = 'index.php';
        }

        // Check status on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
        });
    </script>
</body>
</html>
