<?php
/**
 * Test Cart Authentication System
 * This page tests the updated cart authentication system
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cart Authentication - Glamour</title>
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
        .test-result {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Cart Authentication Test</h1>
            <p>Test the updated cart authentication system (no default users)</p>
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
            <h3><i class="fas fa-cog"></i> Test Cart API Authentication</h3>
            <p>Test cart operations without authentication:</p>
            <button onclick="testCartAPI()" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Test Cart API
            </button>
            <div id="cartTestResult" class="test-result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-info-circle"></i> What Changed</h3>
            <div class="info-box">
                <h4>✅ Fixed Issues:</h4>
                <ul>
                    <li><strong>No More Default Users:</strong> Removed automatic creation of 'main_user_1756062003'</li>
                    <li><strong>Authentication Required:</strong> All cart operations now require user authentication</li>
                    <li><strong>Proper Error Handling:</strong> Shows authentication modal when needed</li>
                    <li><strong>Clean Cart State:</strong> Unauthenticated users see empty cart with sign-in prompt</li>
                </ul>
            </div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-play"></i> Test Actions</h3>
            <button onclick="goToCart()" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Go to Cart
            </button>
            <button onclick="testCheckoutAuth()" class="btn btn-secondary">
                <i class="fas fa-credit-card"></i> Test Checkout Auth
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
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Not authenticated (No default user)';
                }
            } catch (error) {
                statusDiv.className = 'status not-authenticated';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error checking status';
            }
        }

        // Test cart API without authentication
        async function testCartAPI() {
            const resultDiv = document.getElementById('cartTestResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing cart API...';
            
            try {
                const formData = new FormData();
                formData.append('action', 'get_cart');
                
                const response = await fetch('cart-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.requires_auth) {
                    resultDiv.innerHTML = `✅ SUCCESS: Cart API correctly requires authentication\nResponse: ${JSON.stringify(data, null, 2)}`;
                    resultDiv.style.background = '#d4edda';
                    resultDiv.style.color = '#155724';
                } else {
                    resultDiv.innerHTML = `❌ FAILED: Cart API should require authentication\nResponse: ${JSON.stringify(data, null, 2)}`;
                    resultDiv.style.background = '#f8d7da';
                    resultDiv.style.color = '#721c24';
                }
            } catch (error) {
                resultDiv.innerHTML = `❌ ERROR: ${error.message}`;
                resultDiv.style.background = '#f8d7da';
                resultDiv.style.color = '#721c24';
            }
        }

        // Go to cart
        function goToCart() {
            window.location.href = 'cart-unified.php?mode=view';
        }

        // Test checkout authentication
        function testCheckoutAuth() {
            window.location.href = 'test-checkout-auth.php';
        }

        // Check status on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
        });
    </script>
</body>
</html>
