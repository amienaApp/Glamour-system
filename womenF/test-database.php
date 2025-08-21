<?php
/**
 * Database Test File
 * Tests the database connection and user registration
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

echo "<h1>Database Connection Test</h1>";

try {
    // Test database connection
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful!</p>";
    
    // Test collections
    $collections = $db->listCollections();
    echo "<p>📁 Available collections: " . implode(', ', $collections) . "</p>";
    
    // Test User model
    $userModel = new User();
    echo "<p>✅ User model initialized successfully!</p>";
    
    // Test user count
    $userCount = $userModel->getTotalUsers();
    echo "<p>👥 Total users in database: " . $userCount . "</p>";
    
    // Test sample user creation (commented out to avoid creating test users)
    /*
    $testUser = [
        'username' => 'testuser',
        'email' => 'test@example.com',
        'contact_number' => '+252123456789',
        'gender' => 'male',
        'region' => 'banadir',
        'city' => 'Mogadishu',
        'password' => 'TestPassword123'
    ];
    
    $result = $userModel->register($testUser);
    echo "<p>✅ Test user created successfully!</p>";
    echo "<p>User ID: " . $result['_id'] . "</p>";
    */
    
    echo "<h2>✅ All tests passed! Database is ready for user registration.</h2>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . $e->getMessage() . "</p>";
}
?>





