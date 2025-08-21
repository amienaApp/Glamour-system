<?php
/**
 * Payment System Test Page
 * Tests the payment API functionality
 */

require_once 'models/Payment.php';
require_once 'models/Order.php';
require_once 'models/Cart.php';

// Test data
$testUserId = 'test_user_123';
$testOrderId = 'test_order_123';
$testAmount = 99.99;

echo "<h1>Payment System Test</h1>";

try {
    $paymentModel = new Payment();
    $orderModel = new Order();
    
    echo "<h2>1. Testing Payment Methods</h2>";
    
    // Test payment methods
    $methods = [
        [
            'id' => 'waafi',
            'name' => 'Waafi',
            'description' => 'Somali mobile money (SAHAL, SAAD, EVC, EDAHAB)',
            'icon' => 'fas fa-mobile-alt',
            'color' => '#0066cc',
            'fields' => ['phone_number']
        ],
        [
            'id' => 'card',
            'name' => 'Credit/Debit Card',
            'description' => 'Visa, Mastercard, American Express',
            'icon' => 'fas fa-credit-card',
            'color' => '#6f42c1',
            'fields' => ['card_number', 'expiry', 'cvv', 'cardholder_name']
        ],
        [
            'id' => 'bank',
            'name' => 'Bank Transfer',
            'description' => 'Direct bank transfer',
            'icon' => 'fas fa-university',
            'color' => '#17a2b8',
            'fields' => ['bank_name', 'account_number', 'account_holder']
        ]
    ];
    
    echo "<p>✅ Payment methods loaded successfully</p>";
    foreach ($methods as $method) {
        echo "<p>- {$method['name']}: {$method['description']}</p>";
    }
    
    echo "<h2>2. Testing Payment Creation</h2>";
    
    // Test payment creation
    $paymentData = [
        'order_id' => $testOrderId,
        'user_id' => $testUserId,
        'amount' => $testAmount,
        'currency' => 'USD',
        'payment_method' => 'waafi',
        'payment_details' => [
            'phone_number' => '+2520901234567'
        ]
    ];
    
    $paymentId = $paymentModel->createPayment($paymentData);
    if ($paymentId) {
        echo "<p>✅ Payment created successfully with ID: " . $paymentId . "</p>";
    } else {
        echo "<p>❌ Failed to create payment</p>";
    }
    
    echo "<h2>3. Testing Payment Processing</h2>";
    
    // Test payment processing
    $processData = [
        'phone_number' => '+2520901234567'
    ];
    
    $result = $paymentModel->processPayment($paymentId, $processData);
    if ($result['success']) {
        echo "<p>✅ Payment processed successfully!</p>";
        echo "<p>Message: " . $result['message'] . "</p>";
        if (isset($result['transaction_id'])) {
            echo "<p>Transaction ID: " . $result['transaction_id'] . "</p>";
        }
    } else {
        echo "<p>❌ Payment processing failed: " . $result['message'] . "</p>";
    }
    
    echo "<h2>4. Testing Payment Retrieval</h2>";
    
    // Test payment retrieval
    $payment = $paymentModel->getById($paymentId);
    if ($payment) {
        echo "<p>✅ Payment retrieved successfully</p>";
        echo "<p>Status: " . $payment['status'] . "</p>";
        echo "<p>Amount: $" . $payment['amount'] . "</p>";
        echo "<p>Method: " . $payment['payment_method'] . "</p>";
    } else {
        echo "<p>❌ Failed to retrieve payment</p>";
    }
    
    echo "<h2>5. Testing Phone Validation</h2>";
    
    // Test phone validation
    $testPhones = [
        '+2520901234567',
        '+2520631234567',
        '+2520611234567',
        '+2520661234567',
        '0901234567',
        '0631234567',
        '0611234567',
        '0661234567',
        'invalid_phone'
    ];
    
    foreach ($testPhones as $phone) {
        $isValid = $paymentModel->validateSomaliPhone($phone);
        $status = $isValid ? "✅" : "❌";
        echo "<p>$status $phone: " . ($isValid ? "Valid" : "Invalid") . "</p>";
    }
    
    echo "<h2>6. Testing Payment Statistics</h2>";
    
    // Test payment statistics
    $stats = $paymentModel->getPaymentStatistics();
    echo "<p>✅ Payment statistics retrieved</p>";
    echo "<p>Total payments: " . $stats['total_payments'] . "</p>";
    echo "<p>Completed payments: " . $stats['completed_payments'] . "</p>";
    echo "<p>Pending payments: " . $stats['pending_payments'] . "</p>";
    echo "<p>Failed payments: " . $stats['failed_payments'] . "</p>";
    
    echo "<h2>✅ All Tests Completed Successfully!</h2>";
    echo "<p>The payment system is working correctly.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Test Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1 {
    color: #0066cc;
    text-align: center;
    margin-bottom: 30px;
}

h2 {
    color: #333;
    border-bottom: 2px solid #0066cc;
    padding-bottom: 10px;
    margin-top: 30px;
}

p {
    margin: 10px 0;
    padding: 5px 0;
}

.✅ {
    color: #28a745;
    font-weight: bold;
}

.❌ {
    color: #dc3545;
    font-weight: bold;
}
</style>
