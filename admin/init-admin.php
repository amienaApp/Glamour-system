<?php
require_once '../config/database.php';

// Check if admins collection exists and has any data
$db = Database::getInstance();
$adminCollection = $db->getCollection('admins');

$existingAdmins = $adminCollection->find([]);
$adminCount = iterator_count($existingAdmins);

if ($adminCount > 0) {
    echo "Admin accounts already exist. This script is only for initial setup.";
    exit;
}

// Create default admin account
$defaultAdmin = [
    'name' => 'Admin',
    'email' => 'admin@glamour.com',
    'password' => password_hash('admin123', PASSWORD_DEFAULT),
    'created_at' => date('Y-m-d H:i:s'),
    'role' => 'admin'
];

$result = $adminCollection->insertOne($defaultAdmin);

if ($result) {
    echo "✅ Default admin account created successfully!<br>";
    echo "Email: admin@glamour.com<br>";
    echo "Password: admin123<br><br>";
    echo "⚠️ Please change these credentials after first login for security.<br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "❌ Failed to create admin account.";
}
?>
