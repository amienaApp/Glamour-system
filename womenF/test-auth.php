<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userData = null;

if ($isLoggedIn) {
    $userData = [
        'username' => $_SESSION['username'] ?? 'Unknown',
        'email' => $_SESSION['email'] ?? 'Unknown',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Test - Glamour Palace</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #e6f3ff 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 102, 204, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .content {
            padding: 40px 30px;
        }

        .status-card {
            background: #f8fbff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid #0066cc;
        }

        .status-card h3 {
            color: #0066cc;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .user-info {
            background: #d4edda;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid #28a745;
        }

        .user-info h3 {
            color: #155724;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e1e8ed;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #0066cc;
        }

        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #0066cc;
            border: 2px solid #0066cc;
        }

        .btn-secondary:hover {
            background: #0066cc;
            color: white;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: white;
            color: #0066cc;
            border: 2px solid #0066cc;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #0066cc;
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="header">
            <h1>Authentication Test</h1>
            <p>Testing the login and registration system</p>
        </div>

        <div class="content">
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <h3><i class="fas fa-user-check"></i> User Logged In</h3>
                    <div class="info-item">
                        <span class="info-label">Username:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userData['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userData['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Role:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userData['role']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Session ID:</span>
                        <span class="info-value"><?php echo session_id(); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-danger" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Go to Home
                    </a>
                </div>
            <?php else: ?>
                <div class="status-card">
                    <h3><i class="fas fa-user-times"></i> Not Logged In</h3>
                    <p>You are not currently logged in. Please use the login or registration system to access your account.</p>
                </div>

                <div class="actions">
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </a>
                    <a href="register.php" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i>
                        Sign Up
                    </a>
                </div>
            <?php endif; ?>

            <div class="status-card">
                <h3><i class="fas fa-info-circle"></i> System Status</h3>
                <div class="info-item">
                    <span class="info-label">Session Status:</span>
                    <span class="info-value"><?php echo $isLoggedIn ? 'Active' : 'Inactive'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">PHP Version:</span>
                    <span class="info-value"><?php echo PHP_VERSION; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Session Save Path:</span>
                    <span class="info-value"><?php echo session_save_path(); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Current Time:</span>
                    <span class="info-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function logout() {
            try {
                const response = await fetch('logout-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Logged out successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Error during logout. Please try again.');
            }
        }
    </script>
</body>
</html>
