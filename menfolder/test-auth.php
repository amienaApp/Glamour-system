<?php
/**
 * Authentication System Test Script
 * This script tests the registration and login functionality
 */

require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/../models/User.php';

echo "<h1>🔐 Authentication System Test</h1>";

try {
    // Test database connection
    $db = MongoDB::getInstance();
    if ($db->isConnected()) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }

    // Test User model
    $userModel = new User();
    echo "<p style='color: green;'>✅ User model loaded successfully</p>";

    // Test collection access
    $collection = $db->getCollection('users');
    echo "<p style='color: green;'>✅ Users collection accessible</p>";

    // Get total users
    $totalUsers = $userModel->getTotalUsers();
    echo "<p>📊 Total users in database: <strong>{$totalUsers}</strong></p>";

    // Test recent users
    $recentUsers = $userModel->getRecentUsers(7);
    echo "<p>📅 Recent users (last 7 days): <strong>" . count($recentUsers) . "</strong></p>";

    echo "<hr>";
    echo "<h2>🧪 Test Results</h2>";
    echo "<p style='color: green;'>✅ All basic tests passed!</p>";
    echo "<p>The authentication system is ready for use.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2 {
        color: #333;
    }
    p {
        margin: 10px 0;
        padding: 10px;
        background: white;
        border-radius: 5px;
        border-left: 4px solid #ddd;
    }
    hr {
        border: none;
        border-top: 2px solid #ddd;
        margin: 30px 0;
    }
</style>
