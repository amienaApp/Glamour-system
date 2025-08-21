<?php
// Disable error reporting and deprecation warnings to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Suppress deprecation warnings
set_error_handler(function($severity, $message, $file, $line) {
    if (error_reporting() === 0) return false;
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return true;
});

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';
require_once 'models/Cart.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

// For demo purposes, we'll use a default user ID
// In a real application, this would come from user authentication
$defaultUserId = 'demo_user_123';

$cartModel = new Cart();
$orderModel = new Order();

$response = ['success' => false, 'message' => ''];

try {
    // Debug: Log the request
    error_log("Cart API called with method: " . $_SERVER['REQUEST_METHOD']);
    
    // Get POST data (handle both JSON and form data)
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Debug: Log the input
    error_log("Cart API input: " . json_encode($input));
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
        switch ($input['action']) {
            case 'add_to_cart':
                $productId = $input['product_id'] ?? '';
                $quantity = intval($input['quantity'] ?? 1);
                
                if (empty($productId)) {
                    throw new Exception('Product ID is required');
                }
                
                $success = $cartModel->addToCart($defaultUserId, $productId, $quantity);
                
                if ($success) {
                    $cartCount = $cartModel->getCartItemCount($defaultUserId);
                    $response = [
                        'success' => true,
                        'message' => 'Product added to cart successfully!',
                        'cart_count' => $cartCount
                    ];
                } else {
                    throw new Exception('Failed to add product to cart');
                }
                break;
                
            case 'get_cart':
                $cart = $cartModel->getCart($defaultUserId);
                $response = [
                    'success' => true,
                    'data' => $cart
                ];
                break;
                
            case 'update_quantity':
                $productId = $input['product_id'] ?? '';
                $quantity = intval($input['quantity'] ?? 1);
                
                if (empty($productId)) {
                    throw new Exception('Product ID is required');
                }
                
                $success = $cartModel->updateQuantity($defaultUserId, $productId, $quantity);
                
                if ($success) {
                    $cart = $cartModel->getCart($defaultUserId);
                    $response = [
                        'success' => true,
                        'message' => 'Cart updated successfully!',
                        'data' => $cart
                    ];
                } else {
                    throw new Exception('Failed to update cart');
                }
                break;
                
            case 'remove_from_cart':
                $productId = $input['product_id'] ?? '';
                
                if (empty($productId)) {
                    throw new Exception('Product ID is required');
                }
                
                $success = $cartModel->removeFromCart($defaultUserId, $productId);
                
                if ($success) {
                    $cart = $cartModel->getCart($defaultUserId);
                    $response = [
                        'success' => true,
                        'message' => 'Product removed from cart successfully!',
                        'data' => $cart
                    ];
                } else {
                    throw new Exception('Failed to remove product from cart');
                }
                break;
                
            case 'clear_cart':
                $success = $cartModel->clearCart($defaultUserId);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Cart cleared successfully!',
                        'data' => ['items' => [], 'total' => 0, 'item_count' => 0]
                    ];
                } else {
                    throw new Exception('Failed to clear cart');
                }
                break;
                
            case 'place_order':
                $cart = $cartModel->getCart($defaultUserId);
                
                if (empty($cart['items'])) {
                    throw new Exception('Cart is empty');
                }
                
                $orderDetails = [
                    'shipping_address' => $input['shipping_address'] ?? '',
                    'billing_address' => $input['billing_address'] ?? '',
                    'payment_method' => $input['payment_method'] ?? 'cash_on_delivery',
                    'notes' => $input['notes'] ?? ''
                ];
                
                $orderId = $orderModel->createOrder($defaultUserId, $cart, $orderDetails);
                
                if ($orderId) {
                    $response = [
                        'success' => true,
                        'message' => 'Order placed successfully!',
                        'order_id' => $orderId
                    ];
                } else {
                    throw new Exception('Failed to place order');
                }
                break;
                
            case 'cancel_order':
                $orderId = $input['order_id'] ?? '';
                
                if (empty($orderId)) {
                    throw new Exception('Order ID is required');
                }
                
                $success = $orderModel->cancelOrder($orderId, $defaultUserId);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Order cancelled successfully!'
                    ];
                } else {
                    throw new Exception('Failed to cancel order');
                }
                break;
                
            case 'get_orders':
                $orders = $orderModel->getUserOrders($defaultUserId);
                $response = [
                    'success' => true,
                    'data' => $orders
                ];
                break;
                
            case 'get_cart_count':
                $cartCount = $cartModel->getCartItemCount($defaultUserId);
                $response = [
                    'success' => true,
                    'cart_count' => $cartCount
                ];
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Invalid request method or missing action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Debug: Log the response
error_log("Cart API response: " . json_encode($response));

echo json_encode($response);
?>
