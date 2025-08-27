<?php
/**
 * Payment API
 * Handles payment processing requests for Somali payment methods
 */

// Disable error reporting for production
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'models/Payment.php';
require_once 'models/Order.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? '';

try {

    
    $paymentModel = new Payment();
    $orderModel = new Order();

    switch ($action) {
        case 'create_payment':
            handleCreatePayment($paymentModel, $input);
            break;
            
        case 'process_payment':
            handleProcessPayment($paymentModel, $input);
            break;
            
        case 'get_payment_status':
            handleGetPaymentStatus($paymentModel, $input);
            break;
            
        case 'get_payment_methods':
            handleGetPaymentMethods();
            break;
            
        case 'validate_phone':
            handleValidatePhone($input);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {

    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Handle payment creation
 */
function handleCreatePayment($paymentModel, $data) {
    $required = ['order_id', 'user_id', 'amount', 'payment_method'];
    
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            return;
        }
    }

    // Get user email from session if available
    session_start();
    $userEmail = '';
    if (isset($_SESSION['email'])) {
        $userEmail = $_SESSION['email'];
    }
    
    $paymentData = [
        'order_id' => $data['order_id'],
        'user_id' => $data['user_id'],
        'user_email' => $userEmail,
        'amount' => floatval($data['amount']),
        'currency' => $data['currency'] ?? 'USD',
        'payment_method' => $data['payment_method'],
        'payment_details' => $data['payment_details'] ?? []
    ];

    $paymentId = $paymentModel->createPayment($paymentData);
    
    if ($paymentId) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment created successfully',
            'payment_id' => (string)$paymentId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create payment']);
    }
}

/**
 * Handle payment processing
 */
function handleProcessPayment($paymentModel, $data) {
    if (!isset($data['payment_id'])) {
        echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
        return;
    }

    $result = $paymentModel->processPayment($data['payment_id'], $data);
    echo json_encode($result);
}

/**
 * Handle payment status check
 */
function handleGetPaymentStatus($paymentModel, $data) {
    if (!isset($data['payment_id'])) {
        echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
        return;
    }

    $payment = $paymentModel->getById($data['payment_id']);
    
    if ($payment) {
        echo json_encode([
            'success' => true,
            'payment' => $payment
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment not found']);
    }
}

/**
 * Handle payment methods list
 */
function handleGetPaymentMethods() {
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

    echo json_encode([
        'success' => true,
        'methods' => $methods
    ]);
}

/**
 * Handle phone number validation
 */
function handleValidatePhone($data) {
    if (!isset($data['phone_number'])) {
        echo json_encode(['success' => false, 'message' => 'Phone number is required']);
        return;
    }

    $phone = $data['phone_number'];
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Somali phone validation
    $isValid = preg_match('/^(252|\\+252)?(090|063|061|066)[0-9]{7}$/', $phone);
    
    echo json_encode([
        'success' => true,
        'is_valid' => $isValid,
        'formatted_number' => $isValid ? formatSomaliPhone($phone) : null
    ]);
}

/**
 * Format Somali phone number
 */
function formatSomaliPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // If starts with 252, format as +252
    if (preg_match('/^252/', $phone)) {
        return '+' . $phone;
    }
    
    // If starts with 090, 063, 061, or 066, add +252
    if (preg_match('/^(090|063|061|066)/', $phone)) {
        return '+252' . $phone;
    }
    
    return $phone;
}
?>
