<?php
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../models/Category.php';

$db = Database::getInstance();
$adminCollection = $db->getCollection('admins');

// Check if admin already exists
$existingAdmin = $adminCollection->findOne(['username' => 'admin']);

if (!$existingAdmin) {
    // Create admin account
    $adminData = [
        'username' => 'admin',
        'email' => 'admin@glamour.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s'),
        'role' => 'admin'
    ];
    
    $adminCollection->insertOne($adminData);
    echo "<h1>✅ Admin Account Created Successfully!</h1>";
    echo "<h2>Login Credentials:</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php'>Click here to login</a></p>";
} else {
    echo "<h1>ℹ️ Admin Account Already Exists</h1>";
    echo "<h2>Login Credentials:</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php'>Click here to login</a></p>";
}
?>



