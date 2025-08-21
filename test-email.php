<?php
/**
 * Test Email Functionality
 * This script tests the email service to ensure it works correctly
 */

require_once 'models/EmailService.php';

echo "<h1>📧 Email Service Test</h1>";
echo "<p>Testing email functionality for order and payment confirmations...</p><hr>";

try {
    $emailService = new EmailService();
    
    // Test data
    $orderData = [
        'order_id' => 'TEST_ORDER_123',
        'full_name' => 'Test Customer',
        'email' => 'test@example.com', // Change this to your email for testing
        'phone' => '+2520901234567',
        'shipping_address' => '123 Test Street, Mogadishu, Somalia',
        'total' => 150.00
    ];
    
    $paymentData = [
        'payment_method' => 'waafi',
        'amount' => 150.00,
        'transaction_id' => 'WAAFI_SAHAL_1234567890_1234',
        'mobile_service' => 'sahal'
    ];
    
    echo "<h2>Testing Order Confirmation Email</h2>";
    $orderResult = $emailService->sendOrderConfirmation($orderData, $paymentData);
    
    if ($orderResult['success']) {
        echo "<p style='color: green;'>✅ Order confirmation email processed successfully!</p>";
        if ($orderResult['method'] === 'file_save') {
            echo "<p style='color: blue;'>📁 Email saved to file: <strong>{$orderResult['file_path']}</strong></p>";
            echo "<p><a href='view-emails.php' style='color: #0066cc; text-decoration: none;'>📧 View all saved emails</a></p>";
        } else {
            echo "<p>Check your email at: <strong>{$orderData['email']}</strong></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to send order confirmation email: {$orderResult['message']}</p>";
    }
    
    echo "<hr>";
    
    echo "<h2>Testing Payment Confirmation Email</h2>";
    $paymentResult = $emailService->sendPaymentConfirmation($orderData, $paymentData);
    
    if ($paymentResult['success']) {
        echo "<p style='color: green;'>✅ Payment confirmation email processed successfully!</p>";
        if ($paymentResult['method'] === 'file_save') {
            echo "<p style='color: blue;'>📁 Email saved to file: <strong>{$paymentResult['file_path']}</strong></p>";
            echo "<p><a href='view-emails.php' style='color: #0066cc; text-decoration: none;'>📧 View all saved emails</a></p>";
        } else {
            echo "<p>Check your email at: <strong>{$orderData['email']}</strong></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to send payment confirmation email: {$paymentResult['message']}</p>";
    }
    
    echo "<hr>";
    
    echo "<h2>Email Preview</h2>";
    echo "<p>Here's what the emails look like:</p>";
    
    // Generate email content for preview
    $orderEmailContent = $emailService->sendOrderConfirmation($orderData, $paymentData);
    $paymentEmailContent = $emailService->sendPaymentConfirmation($orderData, $paymentData);
    
    echo "<details>";
    echo "<summary><strong>📧 Order Confirmation Email Preview</strong></summary>";
    echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 10px 0; background: white;'>";
    echo $orderEmailContent;
    echo "</div>";
    echo "</details>";
    
    echo "<details>";
    echo "<summary><strong>💳 Payment Confirmation Email Preview</strong></summary>";
    echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 10px 0; background: white;'>";
    echo $paymentEmailContent;
    echo "</div>";
    echo "</details>";
    
    echo "<hr>";
    
    echo "<h2>📝 Important Notes:</h2>";
    echo "<ul>";
    echo "<li><strong>Email Configuration:</strong> This test uses PHP's built-in mail() function</li>";
    echo "<li><strong>Local Testing:</strong> For local development, you may need to configure a local SMTP server</li>";
    echo "<li><strong>Production:</strong> In production, use a proper SMTP service like Gmail, SendGrid, or AWS SES</li>";
    echo "<li><strong>Email Address:</strong> Change the email address in the test data to receive actual emails</li>";
    echo "</ul>";
    
    echo "<h3>🔧 To configure email in production:</h3>";
    echo "<ol>";
    echo "<li>Install PHPMailer: <code>composer require phpmailer/phpmailer</code></li>";
    echo "<li>Configure SMTP settings in EmailService.php</li>";
    echo "<li>Update the fromEmail and fromName in EmailService.php</li>";
    echo "<li>Test with a real email address</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='place-order.php'>← Back to Place Order</a></p>";
?>
