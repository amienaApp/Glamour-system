<?php
/**
 * Order Model
 * Handles order operations
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Cart.php'; // Required for clearing cart after order
require_once __DIR__ . '/Product.php'; // Required for enriching order details

class Order {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->collection = $this->db->getCollection('orders');
    }

    /**
     * Create a new order from cart
     */
    public function createOrder($userId, $cartData, $orderDetails = []) {
        // Ensure items have consistent structure
        $processedItems = [];
        foreach ($cartData['items'] as $item) {
            $processedItem = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'added_at' => $item['added_at'] ?? date('Y-m-d H:i:s')
            ];
            
            // Include product details if available
            if (isset($item['product'])) {
                $processedItem['product'] = $item['product'];
            }
            
            // Include direct price if available
            if (isset($item['price'])) {
                $processedItem['price'] = $item['price'];
            }
            
            // Include subtotal if available
            if (isset($item['subtotal'])) {
                $processedItem['subtotal'] = $item['subtotal'];
            }
            
            $processedItems[] = $processedItem;
        }
        
        $orderData = [
            'user_id' => $userId,
            'items' => $processedItems,
            'total_amount' => $cartData['total'],
            'item_count' => $cartData['item_count'],
            'status' => 'pending',
            'order_number' => $this->generateOrderNumber(),
            'shipping_address' => $orderDetails['shipping_address'] ?? '',
            'billing_address' => $orderDetails['billing_address'] ?? '',
            'payment_method' => $orderDetails['payment_method'] ?? 'cash_on_delivery',
            'notes' => $orderDetails['notes'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->collection->insertOne($orderData);
        
        if ($result->getInsertedId()) {
            // Clear the user's cart after successful order
            $cartModel = new Cart();
            $cartModel->clearCart($userId);
            
            return $result->getInsertedId();
        }
        
        return false;
    }

    /**
     * Get user's orders
     */
    public function getUserOrders($userId) {
        $orders = $this->collection->find(
            ['user_id' => $userId],
            ['sort' => ['created_at' => -1]]
        );
        
        $orderList = [];
        foreach ($orders as $order) {
            $orderList[] = $this->enrichOrderWithProductDetails($order);
        }
        
        return $orderList;
    }

    /**
     * Enrich order with product details
     */
    private function enrichOrderWithProductDetails($order) {
        if (!isset($order['items']) || empty($order['items'])) {
            return $order;
        }
        
        $productModel = new Product();
        $enrichedItems = [];
        
        foreach ($order['items'] as $item) {
            $enrichedItem = $item;
            
            // If product details are missing, try to get them from the product ID
            if (!isset($item['product']) && isset($item['product_id'])) {
                $productId = $item['product_id'];
                if (is_string($productId)) {
                    try {
                        $productId = new MongoDB\BSON\ObjectId($productId);
                    } catch (Exception $e) {
                        // If conversion fails, try with string
                    }
                }
                
                $product = $productModel->getById($productId);
                if ($product) {
                    $enrichedItem['product'] = $product;
                }
            }
            
            $enrichedItems[] = $enrichedItem;
        }
        
        $order['items'] = $enrichedItems;
        return $order;
    }

    /**
     * Get order by ID
     */
    public function getOrderById($orderId) {
        // Convert string ID to ObjectId if needed
        if (is_string($orderId)) {
            try {
                $orderId = new MongoDB\BSON\ObjectId($orderId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $order = $this->collection->findOne(['_id' => $orderId]);
        if ($order) {
            return $this->enrichOrderWithProductDetails($order);
        }
        
        return $order;
    }

    /**
     * Get order by ID (alias for getOrderById)
     */
    public function getById($orderId) {
        return $this->getOrderById($orderId);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status) {
        // Convert string ID to ObjectId if needed
        if (is_string($orderId)) {
            try {
                $orderId = new MongoDB\BSON\ObjectId($orderId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $result = $this->collection->updateOne(
            ['_id' => $orderId],
            [
                '$set' => [
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
        
        return $result->getModifiedCount() > 0;
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $userId) {
        // Convert string ID to ObjectId if needed
        if (is_string($orderId)) {
            try {
                $orderId = new MongoDB\BSON\ObjectId($orderId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        // Check if order belongs to user and is cancellable
        $order = $this->collection->findOne([
            '_id' => $orderId,
            'user_id' => $userId,
            'status' => ['$in' => ['pending', 'confirmed']]
        ]);
        
        if (!$order) {
            return false;
        }
        
        $result = $this->collection->updateOne(
            ['_id' => $orderId],
            [
                '$set' => [
                    'status' => 'cancelled',
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
        
        return $result->getModifiedCount() > 0;
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics($userId = null) {
        $filter = $userId ? ['user_id' => $userId] : [];
        
        $totalOrders = $this->collection->countDocuments($filter);
        $pendingOrders = $this->collection->countDocuments(array_merge($filter, ['status' => 'pending']));
        $completedOrders = $this->collection->countDocuments(array_merge($filter, ['status' => 'completed']));
        $cancelledOrders = $this->collection->countDocuments(array_merge($filter, ['status' => 'cancelled']));
        
        return [
            'total' => $totalOrders,
            'pending' => $pendingOrders,
            'completed' => $completedOrders,
            'cancelled' => $cancelledOrders
        ];
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders($limit = 10) {
        $orders = $this->collection->find(
            [],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit
            ]
        );
        
        $orderList = [];
        foreach ($orders as $order) {
            $orderList[] = $order;
        }
        
        return $orderList;
    }

    /**
     * Get all orders with filters (for admin)
     */
    public function getAllOrders($filters = [], $searchQuery = '') {
        $filter = [];
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $filter['status'] = $filters['status'];
        }
        
        // Apply date filter
        if (!empty($filters['date'])) {
            $startDate = $filters['date'] . ' 00:00:00';
            $endDate = $filters['date'] . ' 23:59:59';
            $filter['created_at'] = [
                '$gte' => $startDate,
                '$lte' => $endDate
            ];
        }
        

        
        $orders = $this->collection->find(
            $filter,
            ['sort' => ['created_at' => -1]]
        );
        
        $orderList = [];
        foreach ($orders as $order) {
            $orderList[] = $order;
        }
        
        return $orderList;
    }

    /**
     * Update order status (alias for updateOrderStatus)
     */
    public function updateStatus($orderId, $status) {
        return $this->updateOrderStatus($orderId, $status);
    }

    /**
     * Delete order (for admin)
     */
    public function delete($orderId) {
        // Convert string ID to ObjectId if needed
        if (is_string($orderId)) {
            try {
                $orderId = new MongoDB\BSON\ObjectId($orderId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }
        
        $result = $this->collection->deleteOne(['_id' => $orderId]);
        return $result->getDeletedCount() > 0;
    }

    /**
     * Get order summary statistics (for admin dashboard)
     * Updated to work with custom Collection class
     */
    public function getOrderSummary() {
        try {
            // Count total orders
            $totalOrders = $this->collection->countDocuments([]);
            
            // Calculate total revenue by iterating through all orders
            $totalRevenue = 0;
            $orders = $this->collection->find([]);
            foreach ($orders as $order) {
                if (isset($order['total_amount'])) {
                    $totalRevenue += floatval($order['total_amount']);
                }
            }
            
            // Count orders by status
            $pendingOrders = $this->collection->countDocuments(['status' => 'pending']);
            $completedOrders = $this->collection->countDocuments(['status' => 'completed']);
            
            return [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders
            ];
        } catch (Exception $e) {
            // Log error and return default values
            error_log("Order summary error: " . $e->getMessage());
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0
            ];
        }
    }
}
?>
