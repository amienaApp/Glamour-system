<?php
/**
 * Test Cart Clearing
 * Debug tool to test cart clearing functionality
 */

session_start();
require_once 'config1/mongodb.php';
require_once 'models/Cart.php';

// Include cart configuration for consistent user ID
if (file_exists('cart-config.php')) {
    require_once 'cart-config.php';
}

$userId = $_SESSION['user_id'] ?? 'session_' . session_id();
$cartModel = new Cart();

// Get current cart
$cart = $cartModel->getCart($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cart Clearing</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { padding: 10px 20px; margin: 5px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Cart Clearing Test</h1>
    
    <div class="info">
        <h3>Current Status:</h3>
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($userId); ?></p>
        <p><strong>Cart Items:</strong> <?php echo count($cart['items']); ?></p>
        <p><strong>Cart Total:</strong> $<?php echo number_format($cart['total'], 2); ?></p>
    </div>

    <?php if (!empty($cart['items'])): ?>
    <div class="info">
        <h3>Cart Items:</h3>
        <ul>
            <?php foreach ($cart['items'] as $item): ?>
            <li><?php echo htmlspecialchars($item['product']['name']); ?> - Qty: <?php echo $item['quantity']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div>
        <button onclick="testCartClear()">Test Cart Clear</button>
        <button onclick="testForceClear()">Force Clear Cart</button>
        <button onclick="location.reload()">Refresh Page</button>
    </div>

    <div id="result"></div>

    <script>
        function testCartClear() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="info">Testing cart clear...</div>';
            
            fetch('cart-api.php', {
                method: 'POST',
                body: new FormData()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success">Cart cleared successfully!</div>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    resultDiv.innerHTML = '<div class="error">Error: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Network error: ' + error.message + '</div>';
            });
        }

        function testForceClear() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="info">Force clearing cart...</div>';
            
            const formData = new FormData();
            formData.append('action', 'force_clear_cart');
            
            fetch('cart-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success">Cart force cleared successfully!</div>';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    resultDiv.innerHTML = '<div class="error">Error: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Network error: ' + error.message + '</div>';
            });
        }
    </script>
</body>
</html>
