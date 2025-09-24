<?php
/**
 * Feedback System Setup Script
 * Run this to test your feedback system setup
 */

echo "=== FEEDBACK SYSTEM SETUP TEST ===\n\n";

// Test MongoDB connection
try {
    require_once "mongodb.php";
    $db = MongoDB::getInstance();
    echo "✅ MongoDB connection successful!\n";
    
    // Test feedback collection
    $feedbackCollection = $db->getCollection("feedback");
    $count = $feedbackCollection->countDocuments([]);
    echo "✅ Feedback collection accessible! ($count records)\n";
    
    echo "\n🎉 Setup completed successfully!\n";
    echo "You can now use the feedback management system.\n";
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MongoDB connection settings\n";
    echo "2. IP whitelist configuration\n";
    echo "3. Network connectivity\n";
}
?>