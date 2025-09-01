<?php
/**
 * Test Login Debug
 * This page helps debug login issues
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Debug Test - Glamour</title>
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
        .result {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bug"></i> Login Debug Test</h1>
            <p>Debug login issues and test cart transfer functionality</p>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-cog"></i> Test Cart Model Loading</h3>
            <p>Test if the Cart model can be loaded and instantiated:</p>
            <button onclick="testCartModel()" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Test Cart Model
            </button>
            <div id="cartModelResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-database"></i> Test MongoDB Connection</h3>
            <p>Test if MongoDB connection is working:</p>
            <button onclick="testMongoDB()" class="btn btn-primary">
                <i class="fas fa-database"></i> Test MongoDB
            </button>
            <div id="mongoResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-sign-in-alt"></i> Test Simple Login</h3>
            <p>Test basic login functionality:</p>
            <button onclick="testSimpleLogin()" class="btn btn-primary">
                <i class="fas fa-user"></i> Test Login
            </button>
            <div id="loginResult" class="result" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h3><i class="fas fa-info-circle"></i> Debug Information</h3>
            <div class="result">
                <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
                <strong>Session ID:</strong> <?php echo session_id(); ?><br>
                <strong>Current Directory:</strong> <?php echo __DIR__; ?><br>
                <strong>Cart Model Path:</strong> <?php echo __DIR__ . '/models/Cart.php'; ?><br>
                <strong>Cart Model Exists:</strong> <?php echo file_exists(__DIR__ . '/models/Cart.php') ? 'Yes' : 'No'; ?><br>
                <strong>MongoDB Config Exists:</strong> <?php echo file_exists(__DIR__ . '/config/mongodb.php') ? 'Yes' : 'No'; ?><br>
            </div>
        </div>
    </div>

    <script>
        // Test Cart Model
        async function testCartModel() {
            const resultDiv = document.getElementById('cartModelResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing Cart Model...';
            
            try {
                const response = await fetch('test-cart-model.php');
                const text = await response.text();
                resultDiv.innerHTML = text;
                resultDiv.className = 'result success';
            } catch (error) {
                resultDiv.innerHTML = 'Error testing Cart Model: ' + error.message;
                resultDiv.className = 'result error';
            }
        }

        // Test MongoDB
        async function testMongoDB() {
            const resultDiv = document.getElementById('mongoResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing MongoDB...';
            
            try {
                const response = await fetch('test-mongodb.php');
                const text = await response.text();
                resultDiv.innerHTML = text;
                resultDiv.className = 'result success';
            } catch (error) {
                resultDiv.innerHTML = 'Error testing MongoDB: ' + error.message;
                resultDiv.className = 'result error';
            }
        }

        // Test Simple Login
        async function testSimpleLogin() {
            const resultDiv = document.getElementById('loginResult');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing Login...';
            
            try {
                const response = await fetch('test-simple-login.php');
                const text = await response.text();
                resultDiv.innerHTML = text;
                resultDiv.className = 'result success';
            } catch (error) {
                resultDiv.innerHTML = 'Error testing Login: ' + error.message;
                resultDiv.className = 'result error';
            }
        }
    </script>
</body>
</html>
