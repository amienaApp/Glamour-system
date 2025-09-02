<?php
/**
 * API Endpoints Test Page
 * Tests all authentication and API endpoints to ensure they're working correctly
 */

echo "<h1>🔍 API Endpoints Test</h1>";
echo "<p>Testing all API endpoints to ensure they return proper JSON responses...</p>";

// Test endpoints
$endpoints = [
    'register-handler.php' => 'POST',
    'login-handler.php' => 'POST', 
    'logout-handler.php' => 'POST',
    'filter-api.php' => 'POST'
];

echo "<div style='margin: 20px 0;'>";
foreach ($endpoints as $endpoint => $method) {
    echo "<h3>Testing: {$endpoint}</h3>";
    
    // Test if file exists
    if (file_exists($endpoint)) {
        echo "<p style='color: green;'>✅ File exists</p>";
        
        // Test if it's accessible via HTTP
        $url = "http://localhost/Glamour-system/menfolder/{$endpoint}";
        echo "<p>Testing URL: <code>{$url}</code></p>";
        
        // Test with a simple request
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['test' => 'data'])
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            echo "<p style='color: red;'>❌ Failed to access endpoint</p>";
        } else {
            echo "<p style='color: green;'>✅ Endpoint accessible</p>";
            
            // Check if response is JSON
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<p style='color: green;'>✅ Returns valid JSON</p>";
                echo "<p><strong>Response:</strong> <code>" . htmlspecialchars(substr($response, 0, 200)) . "...</code></p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Response is not valid JSON</p>";
                echo "<p><strong>Response:</strong> <code>" . htmlspecialchars(substr($response, 0, 200)) . "...</code></p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ File does not exist</p>";
    }
    
    echo "<hr>";
}
echo "</div>";

// Test database connection
echo "<h2>🗄️ Database Connection Test</h2>";
try {
    require_once __DIR__ . '/../config1/mongodb.php';
    $db = MongoDB::getInstance();
    
    if ($db->isConnected()) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Test users collection
        $collection = $db->getCollection('users');
        $userCount = $collection->countDocuments();
        echo "<p>📊 Users collection accessible: <strong>{$userCount}</strong> users found</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h2>🧪 Test Results Summary</h2>";
echo "<p>If all endpoints show ✅, your authentication system should work correctly.</p>";
echo "<p>If you see ❌ errors, check the file paths and server configuration.</p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2, h3 {
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
        margin: 20px 0;
    }
    code {
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: monospace;
    }
</style>
