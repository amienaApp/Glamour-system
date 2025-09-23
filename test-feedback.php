<?php
/**
 * Test script to check feedback system
 */

require_once 'config1/mongodb.php';

echo "<h1>Feedback System Test</h1>";

try {
    // Test database connection
    $db = MongoDB::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Test feedback collection
    $feedbackCollection = $db->getCollection('feedback');
    echo "<p>✅ Feedback collection accessed</p>";
    
    // Check if collection exists and count documents
    $count = $feedbackCollection->countDocuments([]);
    echo "<p>📊 Total feedback entries: " . $count . "</p>";
    
    // Get recent feedback
    $recentFeedback = $feedbackCollection->find([], [
        'sort' => ['created_at' => -1],
        'limit' => 5
    ])->toArray();
    
    if (!empty($recentFeedback)) {
        echo "<h2>Recent Feedback:</h2>";
        foreach ($recentFeedback as $feedback) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<strong>Name:</strong> " . htmlspecialchars($feedback['name']) . "<br>";
            echo "<strong>Email:</strong> " . htmlspecialchars($feedback['email']) . "<br>";
            echo "<strong>Message:</strong> " . htmlspecialchars($feedback['message']) . "<br>";
            echo "<strong>Status:</strong> " . htmlspecialchars($feedback['status']) . "<br>";
            echo "<strong>Created:</strong> " . htmlspecialchars($feedback['created_at']) . "<br>";
            echo "</div>";
        }
    } else {
        echo "<p>❌ No feedback found in database</p>";
    }
    
    // Test inserting a sample feedback
    echo "<h2>Testing Insert:</h2>";
    $testData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'message' => 'This is a test message',
        'status' => 'unread',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Script'
    ];
    
    $result = $feedbackCollection->insertOne($testData);
    if ($result->getInsertedId()) {
        echo "<p>✅ Test feedback inserted successfully</p>";
        echo "<p>📝 Inserted ID: " . $result->getInsertedId() . "</p>";
        
        // Clean up test data
        $feedbackCollection->deleteOne(['_id' => $result->getInsertedId()]);
        echo "<p>🧹 Test data cleaned up</p>";
    } else {
        echo "<p>❌ Failed to insert test feedback</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

