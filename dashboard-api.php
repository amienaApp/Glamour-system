<?php
/**
 * Dashboard API
 * Provides dynamic data for the user dashboard
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include required files
require_once 'vendor/autoload.php';
require_once 'config1/mongodb.php';
require_once 'models/User.php';
require_once 'models/Order.php';
require_once 'models/Product.php';
require_once 'models/Cart.php';

// Get the action from POST data
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $userId = $_SESSION['user_id'];
    $userModel = new User();
    $orderModel = new Order();
    $productModel = new Product();
    $cartModel = new Cart();

    switch ($action) {
        case 'get_stats':
            // Get user statistics
            $orderStats = $orderModel->getOrderStatistics($userId);
            
            // Calculate total spent
            $userOrders = $orderModel->getUserOrders($userId);
            $totalSpent = 0;
            foreach ($userOrders as $order) {
                if ($order['status'] === 'completed') {
                    $totalSpent += floatval($order['total_amount'] ?? 0);
                }
            }
            
            // Get cart count
            $cartData = $cartModel->getCart($userId);
            $cartCount = $cartData['item_count'] ?? 0;
            
            // Get wishlist count from localStorage (handled by frontend)
            $wishlistCount = 0; // This will be updated by JavaScript
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'total_orders' => $orderStats['total'],
                    'pending_orders' => $orderStats['pending'],
                    'completed_orders' => $orderStats['completed'],
                    'cancelled_orders' => $orderStats['cancelled'],
                    'total_spent' => $totalSpent,
                    'cart_count' => $cartCount,
                    'wishlist_count' => $wishlistCount
                ]
            ]);
            break;

        case 'get_recent_orders':
            // Get recent orders
            $limit = intval($_POST['limit'] ?? 5);
            $userOrders = $orderModel->getUserOrders($userId);
            $recentOrders = array_slice($userOrders, 0, $limit);
            
            echo json_encode([
                'success' => true,
                'data' => $recentOrders
            ]);
            break;

        case 'get_recommendations':
            // Get product recommendations
            $limit = intval($_POST['limit'] ?? 6);
            $recommendations = $productModel->getFeatured($limit);
            
            echo json_encode([
                'success' => true,
                'data' => $recommendations
            ]);
            break;

        case 'get_user_info':
            // Get user information
            $user = $userModel->getById($userId);
            if ($user) {
                // Remove sensitive information
                unset($user['password']);
                unset($user['reset_token']);
                unset($user['reset_expires']);
                
                echo json_encode([
                    'success' => true,
                    'data' => $user
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
            break;

        case 'update_profile':
            // Update user profile
            $updateData = [];
            
            if (isset($_POST['username']) && !empty($_POST['username'])) {
                $updateData['username'] = trim($_POST['username']);
            }
            
            if (isset($_POST['email']) && !empty($_POST['email'])) {
                $updateData['email'] = trim($_POST['email']);
            }
            
            if (isset($_POST['contact_number']) && !empty($_POST['contact_number'])) {
                $updateData['contact_number'] = trim($_POST['contact_number']);
            }
            
            if (isset($_POST['gender']) && !empty($_POST['gender'])) {
                $updateData['gender'] = $_POST['gender'];
            }
            
            if (isset($_POST['region']) && !empty($_POST['region'])) {
                $updateData['region'] = $_POST['region'];
            }
            
            if (isset($_POST['city']) && !empty($_POST['city'])) {
                $updateData['city'] = $_POST['city'];
            }
            
            if (empty($updateData)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No data to update'
                ]);
                break;
            }
            
            $success = $userModel->updateProfile($userId, $updateData);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update profile'
                ]);
            }
            break;

        case 'change_password':
            // Change user password
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'All password fields are required'
                ]);
                break;
            }
            
            if ($newPassword !== $confirmPassword) {
                echo json_encode([
                    'success' => false,
                    'message' => 'New passwords do not match'
                ]);
                break;
            }
            
            if (strlen($newPassword) < 6) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Password must be at least 6 characters long'
                ]);
                break;
            }
            
            $success = $userModel->changePassword($userId, $currentPassword, $newPassword);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to change password. Please check your current password.'
                ]);
            }
            break;

        case 'get_order_details':
            // Get detailed order information
            $orderId = $_POST['order_id'] ?? '';
            
            if (empty($orderId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order ID is required'
                ]);
                break;
            }
            
            $order = $orderModel->getOrderById($orderId);
            
            if ($order && $order['user_id'] == $userId) {
                echo json_encode([
                    'success' => true,
                    'data' => $order
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order not found or access denied'
                ]);
            }
            break;

        case 'cancel_order':
            // Cancel an order
            $orderId = $_POST['order_id'] ?? '';
            
            if (empty($orderId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order ID is required'
                ]);
                break;
            }
            
            $success = $orderModel->cancelOrder($orderId, $userId);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Order cancelled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to cancel order. Order may not be cancellable.'
                ]);
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }

} catch (Exception $e) {
    error_log("Dashboard API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
