<?php
/**
 * Payment Actions Handler
 * Handles view, edit, update, and delete operations for payments
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

require_once '../models/Payment.php';
require_once '../models/Order.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? '';
$paymentId = $input['payment_id'] ?? '';

if (empty($action) || empty($paymentId)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $paymentModel = new Payment();
    $orderModel = new Order();

    switch ($action) {
        case 'view':
            handleViewPayment($paymentModel, $paymentId);
            break;
            
        case 'get':
            handleGetPayment($paymentModel, $paymentId);
            break;
            
        case 'update':
            handleUpdatePayment($paymentModel, $input);
            break;
            
        case 'delete':
            handleDeletePayment($paymentModel, $paymentId);
            break;
            
        case 'bulk_delete':
            handleBulkDeletePayment($paymentModel, $input);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Handle view payment action
 */
function handleViewPayment($paymentModel, $paymentId) {
    $payment = $paymentModel->getById($paymentId);
    
    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Payment not found']);
        return;
    }
    
    // Convert ObjectId to string for JSON
    $payment['_id'] = (string)$payment['_id'];
    
    echo json_encode([
        'success' => true,
        'payment' => $payment
    ]);
}

/**
 * Handle get payment for editing
 */
function handleGetPayment($paymentModel, $paymentId) {
    $payment = $paymentModel->getById($paymentId);
    
    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Payment not found']);
        return;
    }
    
    // Convert ObjectId to string for JSON
    $payment['_id'] = (string)$payment['_id'];
    
    echo json_encode([
        'success' => true,
        'payment' => $payment
    ]);
}

/**
 * Handle update payment action
 */
function handleUpdatePayment($paymentModel, $data) {
    $paymentId = $data['payment_id'] ?? '';
    $status = $data['status'] ?? '';
    $paymentMethod = $data['payment_method'] ?? '';
    $amount = $data['amount'] ?? '';
    
    if (empty($paymentId) || empty($status) || empty($paymentMethod) || empty($amount)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid amount']);
        return;
    }
    
    // Validate status
    $validStatuses = ['pending', 'completed', 'failed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    // Validate payment method
    $validMethods = ['waafi', 'card', 'bank'];
    if (!in_array($paymentMethod, $validMethods)) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
        return;
    }
    
    // Update payment
    $updateData = [
        'status' => $status,
        'payment_method' => $paymentMethod,
        'amount' => (float)$amount,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $success = $paymentModel->updatePaymentStatus($paymentId, $status, $updateData);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment updated successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payment']);
    }
}

/**
 * Handle delete payment action
 */
function handleDeletePayment($paymentModel, $paymentId) {
    $success = $paymentModel->delete($paymentId);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete payment']);
    }
}

/**
 * Handle bulk delete payment action
 */
function handleBulkDeletePayment($paymentModel, $data) {
    $paymentIds = $data['payment_ids'] ?? [];
    
    if (empty($paymentIds)) {
        echo json_encode(['success' => false, 'message' => 'No payment IDs provided']);
        return;
    }
    
    $deletedCount = 0;
    $failedCount = 0;
    
    foreach ($paymentIds as $paymentId) {
        $success = $paymentModel->delete($paymentId);
        if ($success) {
            $deletedCount++;
        } else {
            $failedCount++;
        }
    }
    
    if ($deletedCount > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully deleted $deletedCount payment(s)" . ($failedCount > 0 ? ", failed to delete $failedCount payment(s)" : ""),
            'deleted_count' => $deletedCount,
            'failed_count' => $failedCount
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete any payments']);
    }
}
?>
