<?php
/**
 * Debug version of Contact Form Handler
 * This will help us see what's happening with form submissions
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all requests
file_put_contents('contact-debug.log', date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);
file_put_contents('contact-debug.log', "Method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents('contact-debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

session_start();
require_once 'config1/mongodb.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents('contact-debug.log', "Error: Method not allowed\n", FILE_APPEND);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    file_put_contents('contact-debug.log', "Form data - Name: $name, Email: $email, Message: " . substr($message, 0, 50) . "...\n", FILE_APPEND);
    
    // Validate required fields
    if (empty($name)) {
        throw new Exception('Name is required');
    }
    
    if (empty($email)) {
        throw new Exception('Email is required');
    }
    
    if (empty($message)) {
        throw new Exception('Message is required');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Get database connection
    $db = MongoDB::getInstance();
    $feedbackCollection = $db->getCollection('feedback');
    
    file_put_contents('contact-debug.log', "Database connection successful\n", FILE_APPEND);
    
    // Prepare feedback data
    $feedbackData = [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'status' => 'unread', // unread, read, replied
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    file_put_contents('contact-debug.log', "Prepared data: " . print_r($feedbackData, true) . "\n", FILE_APPEND);
    
    // Insert feedback into database
    $result = $feedbackCollection->insertOne($feedbackData);
    
    file_put_contents('contact-debug.log', "Insert result: " . print_r($result, true) . "\n", FILE_APPEND);
    
    if ($result->getInsertedId()) {
        file_put_contents('contact-debug.log', "Success: Feedback saved with ID " . $result->getInsertedId() . "\n", FILE_APPEND);
        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your feedback! We will get back to you soon.',
            'feedback_id' => (string)$result->getInsertedId()
        ]);
    } else {
        throw new Exception('Failed to save feedback');
    }
    
} catch (Exception $e) {
    file_put_contents('contact-debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

