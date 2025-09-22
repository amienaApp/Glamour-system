<?php
/**
 * Payment Model
 * Handles payment processing for Somali payment methods
 * Supports: SAHAL, SAAD, EVC, EDAHAB, Card/Bank
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/Order.php';
// Email functionality removed for local system

class Payment {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = MongoDB::getInstance();
        $this->collection = $this->db->getCollection('payments');
    }

    /**
     * Create a new payment record
     */
    public function createPayment($data) {
        // Get user information to store with payment
        $userEmail = '';
        $userName = '';
        
        if (isset($data['user_email'])) {
            $userEmail = $data['user_email'];
        } elseif (isset($data['user_id']) && $data['user_id'] !== 'demo_user_123') {
            // Try to get user info from database
            try {
                require_once __DIR__ . '/User.php';
                $userModel = new User();
                $user = $userModel->getById($data['user_id']);
                if ($user) {
                    $userEmail = $user['email'] ?? '';
                    $userName = $user['username'] ?? '';
                }
            } catch (Exception $e) {
                // Ignore errors, continue with empty values
            }
        }
        
        $paymentData = [
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'user_email' => $userEmail,
            'user_name' => $userName,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'payment_method' => $data['payment_method'],
            'payment_details' => $data['payment_details'] ?? [],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($paymentData);
        return $result->getInsertedId();
    }

    /**
     * Process payment based on method
     */
    public function processPayment($paymentId, $paymentData) {
        $payment = $this->getById($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found'];
        }

        $result = null;
        switch ($payment['payment_method']) {
            case 'waafi':
                $result = $this->processWaafiPayment($payment, $paymentData);
                break;
            case 'card':
                $result = $this->processCardPayment($payment, $paymentData);
                break;
            case 'bank':
                $result = $this->processBankPayment($payment, $paymentData);
                break;
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }

        // Update order status if payment is successful
        if ($result && $result['success']) {
            // Confirm order (stock already reduced when items were added to cart)
            $orderConfirmed = $this->confirmOrder($payment['order_id']);
            if (!$orderConfirmed) {
                // Warning: Failed to confirm order
            }
            
            // Cart will be cleared on orders.php page after redirect
            $result['message'] = "🎉 Congratulations! Your order is successful! You will be redirected to your orders page.";
            $result['order_confirmed'] = $orderConfirmed;
        }

        return $result;
    }

    /**
     * Process Waafi mobile money payment (unified Somali mobile money)
     * Supports: SAHAL, SAAD, EVC Plus, EDAHAB
     */
    private function processWaafiPayment($payment, $data) {
        // Validate phone number format (Somali mobile number)
        if (!isset($data['phone_number']) || !$this->validateSomaliPhone($data['phone_number'])) {
            return ['success' => false, 'message' => 'Invalid Somali phone number'];
        }

        // Detect mobile money service based on phone number
        $mobileService = $this->detectMobileService($data['phone_number']);
        
        // Simulate Waafi API call
        $transactionId = 'WAAFI_' . strtoupper($mobileService) . '_' . time() . '_' . rand(1000, 9999);
        
        // Update payment status
        $this->updatePaymentStatus($payment['_id'], 'completed', [
            'transaction_id' => $transactionId,
            'phone_number' => $data['phone_number'],
            'mobile_service' => $mobileService,
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'message' => "Payment processed successfully via Waafi ($mobileService)",
            'transaction_id' => $transactionId,
            'mobile_service' => $mobileService
        ];
    }

    /**
     * Process card payment
     */
    private function processCardPayment($payment, $data) {
        // Validate card details
        if (!isset($data['card_number']) || !isset($data['expiry']) || !isset($data['cvv'])) {
            return ['success' => false, 'message' => 'Invalid card details'];
        }

        // Simulate card processing
        $transactionId = 'CARD_' . time() . '_' . rand(1000, 9999);
        
        $this->updatePaymentStatus($payment['_id'], 'completed', [
            'transaction_id' => $transactionId,
            'card_last4' => substr($data['card_number'], -4),
            'card_type' => $this->getCardType($data['card_number']),
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'message' => 'Payment processed successfully via card',
            'transaction_id' => $transactionId
        ];
    }

    /**
     * Process bank transfer payment
     */
    private function processBankPayment($payment, $data) {
        if (!isset($data['bank_name']) || !isset($data['account_number'])) {
            return ['success' => false, 'message' => 'Invalid bank details'];
        }

        $transactionId = 'BANK_' . time() . '_' . rand(1000, 9999);
        
        $this->updatePaymentStatus($payment['_id'], 'pending', [
            'transaction_id' => $transactionId,
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'reference' => 'GLAMOUR_' . $payment['order_id'],
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'message' => 'Bank transfer initiated. Please complete the transfer with reference: GLAMOUR_' . $payment['order_id'],
            'transaction_id' => $transactionId,
            'reference' => 'GLAMOUR_' . $payment['order_id']
        ];
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($paymentId, $status, $additionalData = []) {
        // Convert string ID to ObjectId if needed
        if (is_string($paymentId)) {
            try {
                $paymentId = new MongoDB\BSON\ObjectId($paymentId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $updateData = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($additionalData)) {
            $updateData = array_merge($updateData, $additionalData);
        }

        $result = $this->collection->updateOne(
            ['_id' => $paymentId],
            ['$set' => $updateData]
        );

        return $result->getModifiedCount() > 0;
    }

    /**
     * Get payment by ID
     */
    public function getById($paymentId) {
        // Convert string ID to ObjectId if needed
        if (is_string($paymentId)) {
            try {
                $paymentId = new MongoDB\BSON\ObjectId($paymentId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        return $this->collection->findOne(['_id' => $paymentId]);
    }

    /**
     * Get payments by user ID
     */
    public function getByUserId($userId) {
        $payments = $this->collection->find(['user_id' => $userId]);
        $paymentList = [];
        foreach ($payments as $payment) {
            $paymentList[] = $payment;
        }
        return $paymentList;
    }

    /**
     * Get payments by order ID
     */
    public function getByOrderId($orderId) {
        $payments = $this->collection->find(['order_id' => $orderId]);
        $paymentList = [];
        foreach ($payments as $payment) {
            $paymentList[] = $payment;
        }
        return $paymentList;
    }

    /**
     * Validate Somali phone number
     */
    public function validateSomaliPhone($phone) {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Somali phone numbers: +252 or 252 followed by 10 digits
        // Local format: 090, 063, 061, 066 followed by 7 digits (total 10 digits)
        // Examples: +2520902345678, +2520632345678, 0902345678, 0632345678
        return preg_match('/^(252|\\+252)?(090|063|061|066)[0-9]{7}$/', $phone);
    }

    /**
     * Detect mobile money service based on phone number
     */
    private function detectMobileService($phone) {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove country code if present
        if (preg_match('/^252/', $phone)) {
            $phone = substr($phone, 3);
        }
        
        // Detect service based on first 3 digits
        $prefix = substr($phone, 0, 3);
        
        switch ($prefix) {
            case '090':
                return 'sahal'; // SAHAL
            case '063':
                return 'saad';  // SAAD
            case '061':
                return 'evc';   // EVC
            case '066':
                return 'edahab'; // EDAHAB
            default:
                return 'unknown';
        }
    }

    /**
     * Get card type based on number
     */
    private function getCardType($cardNumber) {
        $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);
        
        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]|^2[2-7]|^222[1-9]|^22[3-9]|^2[3-6]|^27[0-1]|^2720/', $cardNumber)) {
            return 'Mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics() {
        $stats = [
            'total_payments' => $this->collection->countDocuments(),
            'completed_payments' => $this->collection->countDocuments(['status' => 'completed']),
            'pending_payments' => $this->collection->countDocuments(['status' => 'pending']),
            'failed_payments' => $this->collection->countDocuments(['status' => 'failed']),
            'by_method' => []
        ];

        // Get counts by payment method
        $methods = ['waafi', 'card', 'bank'];
        foreach ($methods as $method) {
            $stats['by_method'][$method] = $this->collection->countDocuments(['payment_method' => $method]);
        }

        return $stats;
    }
    
    /**
     * Get all payments with pagination and sorting
     */
    public function getAllPayments($options = []) {
        $filter = $options['filter'] ?? [];
        $sort = $options['sort'] ?? ['created_at' => -1];
        $skip = $options['skip'] ?? 0;
        $limit = $options['limit'] ?? 20;
        
        $payments = $this->collection->find($filter, [
            'sort' => $sort,
            'skip' => $skip,
            'limit' => $limit
        ]);
        
        $paymentList = [];
        foreach ($payments as $payment) {
            $paymentList[] = $payment;
        }
        
        return $paymentList;
    }
    
    /**
     * Count total payments
     */
    public function countPayments($filter = []) {
        return $this->collection->countDocuments($filter);
    }
    
    /**
     * Delete payment
     */
    public function delete($paymentId) {
        // Convert string ID to ObjectId if needed
        if (is_string($paymentId)) {
            try {
                $paymentId = new MongoDB\BSON\ObjectId($paymentId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $result = $this->collection->deleteOne(['_id' => $paymentId]);
        return $result->getDeletedCount() > 0;
    }

    /**
     * Update order status
     */
    private function updateOrderStatus($orderId, $status) {
        try {
            $orderModel = new Order();
            return $orderModel->updateOrderStatus($orderId, $status);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Clear user's cart after successful payment
     */
    private function clearUserCart($userId) {
        try {
            require_once __DIR__ . '/Cart.php';
            $cartModel = new Cart();
            
            // Log the attempt
            // Attempting to clear cart
            
            $result = $cartModel->clearCart($userId);
            
            if ($result) {
                // Successfully cleared cart
            } else {
                // Failed to clear cart
            }
            
            return $result;
        } catch (Exception $e) {
            // Exception clearing cart
            return false;
        }
    }

    /**
     * Force clear user's cart using direct database access
     */
    private function forceClearUserCart($userId) {
        try {
            $db = MongoDB::getInstance();
            $cartsCollection = $db->getCollection('carts');
            
            // Force clearing cart
            
            // First try to update the cart to empty
            $updateResult = $cartsCollection->updateOne(
                ['user_id' => $userId],
                [
                    '$set' => [
                        'items' => [],
                        'total' => 0,
                        'item_count' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]
            );
            
            if ($updateResult->getModifiedCount() > 0) {
                // Force clear cart successful via update
                return true;
            }
            
            // If no document was updated, try to delete and recreate
            $deleteResult = $cartsCollection->deleteOne(['user_id' => $userId]);
            
            // Create empty cart
            $cartsCollection->insertOne([
                'user_id' => $userId,
                'items' => [],
                'total' => 0,
                'item_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Force clear cart successful via delete/recreate
            return true;
            
        } catch (Exception $e) {
            // Force clear cart failed
            return false;
        }
    }

    /**
     * Confirm order (stock already reduced when items were added to cart)
     */
    private function confirmOrder($orderId) {
        try {
            $orderModel = new Order();
            return $orderModel->updateOrderStatus($orderId, 'confirmed');
        } catch (Exception $e) {
            // Error confirming order
            return false;
        }
    }

    /**
     * Confirm order and reduce stock (called after successful payment)
     */
    private function confirmOrderAndReduceStock($orderId) {
        try {
            $orderModel = new Order();
            return $orderModel->confirmOrderAndReduceStock($orderId);
        } catch (Exception $e) {
            // Error confirming order
            return false;
        }
    }

    /**
     * Success message for local system
     */
    private function showSuccessMessage($payment, $paymentData, $result) {
        // Simple success message for local development
        return "🎉 Congratulations! Your order is successful!";
    }
}
?>
