<?php
/**
 * Contact Form Handler
 * Processes contact form submissions and stores them in the database
 */

session_start();
require_once 'config1/mongodb.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
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
    
    // Insert feedback into database
    $result = $feedbackCollection->insertOne($feedbackData);
    
    if ($result->getInsertedId()) {
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
    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
