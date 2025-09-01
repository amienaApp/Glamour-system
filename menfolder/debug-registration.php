<?php
/**
 * Debug Registration Form
 * Simple test page to debug registration issues
 */

echo "<h1>🐛 Debug Registration Form</h1>";
echo "<p>This page helps debug registration issues by testing the form submission directly.</p>";

// Check if form was submitted
if ($_POST) {
    echo "<h2>📤 Form Data Received:</h2>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Test the registration handler
    echo "<h2>🧪 Testing Registration Handler:</h2>";
    
    $postData = json_encode($_POST);
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $response = @file_get_contents('register-handler.php', false, $context);
    
    if ($response === false) {
        echo "<p style='color: red;'>❌ Failed to call registration handler</p>";
    } else {
        echo "<p style='color: green;'>✅ Registration handler called successfully</p>";
        echo "<p><strong>Response:</strong> <code>" . htmlspecialchars($response) . "</code></p>";
        
        // Try to decode JSON
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<p style='color: green;'>✅ Response is valid JSON</p>";
            echo "<pre>" . print_r($decoded, true) . "</pre>";
        } else {
            echo "<p style='color: orange;'>⚠️ Response is not valid JSON</p>";
        }
    }
} else {
    echo "<h2>📝 Test Registration Form:</h2>";
    echo "<form method='POST' action=''>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Username: <input type='text' name='username' value='testuser' required></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Email: <input type='email' name='email' value='test@example.com' required></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Contact Number: <input type='text' name='contact_number' value='+252123456789' required></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Gender: ";
    echo "<select name='gender' required>";
    echo "<option value='male'>Male</option>";
    echo "<option value='female'>Female</option>";
    echo "</select>";
    echo "</label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Region: ";
    echo "<select name='region' required>";
    echo "<option value='banadir'>Banadir</option>";
    echo "<option value='bari'>Bari</option>";
    echo "<option value='bay'>Bay</option>";
    echo "</select>";
    echo "</label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>City: <input type='text' name='city' value='Mogadishu' required></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Password: <input type='password' name='password' value='testpass123' required></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>Confirm Password: <input type='password' name='confirm_password' value='testpass123' required></label>";
    echo "</div>";
    echo "<button type='submit' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Registration</button>";
    echo "</form>";
}

echo "<hr>";
echo "<h2>🔍 Debug Information:</h2>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Request Method:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p><strong>Content Type:</strong> " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "</p>";
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
    pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
        border: 1px solid #ddd;
    }
    code {
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: monospace;
    }
    form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    input, select {
        padding: 8px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 200px;
    }
</style>
