<?php
// Prevent any output before JSON response
ob_start();

// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start session without output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

// Include required files with error handling
try {
    require_once 'config/mongodb.php';
    require_once 'models/Cart.php';
    require_once 'models/Order.php';
    require_once 'models/Product.php';
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'System configuration error: ' . $e->getMessage()
    ]);
    exit();
}

// Include cart configuration for consistent user ID
try {
    if (file_exists('cart-config.php')) {
        require_once 'cart-config.php';
    }
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Cart configuration error: ' . $e->getMessage()
    ]);
    exit();
}

// Get user ID from session or use consistent default
$defaultUserId = $_SESSION['user_id'] ?? $_SESSION['current_cart_user_id'] ?? 'main_user_1756062003';

try {
    $cartModel = new Cart();
    $orderModel = new Order();
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error: ' . $e->getMessage()
    ]);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data (handle both JSON and form data)
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
        switch ($input['action']) {
            case 'add_to_cart':
                $productId = $input['product_id'] ?? '';
                $quantity = intval($input['quantity'] ?? 1);
                $color = $input['color'] ?? '';
                $size = $input['size'] ?? '';
                $returnUrl = $input['return_url'] ?? '';
                
                if (empty($productId)) {
                    throw new Exception('Product ID is required');
                }
                
                // Store return URL in session if provided
                if (!empty($returnUrl)) {
                    $_SESSION['return_url'] = $returnUrl;
                }
                
                $success = $cartModel->addToCart($defaultUserId, $productId, $quantity, $color, $size);
                
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
                    $cartCount = $cartModel->getCartItemCount($defaultUserId);
                    $response = [
                        'success' => true,
                        'message' => 'Quantity updated successfully!',
                        'cart_count' => $cartCount
                    ];
                } else {
                    throw new Exception('Failed to update quantity');
                }
                break;
                
            case 'remove_item':
                $productId = $input['product_id'] ?? '';
                
                if (empty($productId)) {
                    throw new Exception('Product ID is required');
                }
                
                $success = $cartModel->removeFromCart($defaultUserId, $productId);
                
                if ($success) {
                    $cartCount = $cartModel->getCartItemCount($defaultUserId);
                    $response = [
                        'success' => true,
                        'message' => 'Item removed from cart successfully!',
                        'cart_count' => $cartCount
                    ];
                } else {
                    throw new Exception('Failed to remove item from cart');
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
                
                // Debug: Log cart data for order creation
                error_log("API - Cart total: " . ($cart['total'] ?? 'not set'));
                error_log("API - Cart items count: " . count($cart['items'] ?? []));
                
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
                
                // Debug: Log order creation result
                error_log("API - Order created with ID: " . ($orderId ?? 'failed'));
                
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

// Clear any output buffer and send clean JSON response
ob_end_clean();
echo json_encode($response);
exit();
?>
