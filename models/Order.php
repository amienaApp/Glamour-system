<?php
/**
 * Order Model
 * Handles order operations
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/Cart.php'; // Required for clearing cart after order
require_once __DIR__ . '/Product.php'; // Required for enriching order details

class Order {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = MongoDB::getInstance();
        $this->collection = $this->db->getCollection('orders');
    }

    /**
     * Create a new order from cart
     */
    public function createOrder($userId, $cartData, $orderDetails = []) {
        // Convert cart items to array if needed
        $cartItems = $this->toArray($cartData['items']);
        

        
        // Ensure items have consistent structure
        $processedItems = [];
        foreach ($cartItems as $item) {
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
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')), // 2 hours expiry
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        

        
        $result = $this->collection->insertOne($orderData);
        
        if ($result->getInsertedId()) {
            // DON'T reduce stock here - wait for payment confirmation
            // Stock will be reduced only after successful payment
            return $result->getInsertedId();
        }
        
        return false;
    }

    /**
     * Validate order items before creation
     */
    public function validateOrderItems($cartData) {
        $productModel = new Product();
        $cartItems = $this->toArray($cartData['items']);
        
        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            
            // Convert string ID to ObjectId if needed
            if (is_string($productId)) {
                try {
                    if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
                        $productId = new MongoDB\BSON\ObjectId($productId);
                    }
                } catch (Exception $e) {
                    // Keep as string if conversion fails
                }
            }
            
            $product = $productModel->getById($productId);
            
            // Skip validation for test products (string IDs)
            if (is_string($item['product_id']) && !preg_match('/^[a-f\d]{24}$/i', $item['product_id'])) {
                continue; // Skip validation for test products
            }
            
            if (!$product) {
                throw new Exception("Product {$item['product_id']} no longer exists");
            }
            
            // Check if product is available using your system's logic
            $stock = (int)($product['stock'] ?? 0);
            $available = $product['available'] ?? true;
            
            // Check if product is explicitly marked as unavailable
            if ($available === false) {
                throw new Exception("Product '{$product['name']}' is sold out");
            }
            
            // Check if product is out of stock
            if ($stock <= 0) {
                throw new Exception("Product '{$product['name']}' is sold out");
            }
            
            if (isset($product['stock']) && $product['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for '{$product['name']}'. Available: {$product['stock']}, Requested: {$item['quantity']}");
            }
        }
        
        return true;
    }

    /**
     * Reduce product stock when order is successfully placed
     */
    private function reduceOrderStock($orderId) {
        $order = $this->getById($orderId);
        
        if (!$order || empty($order['items'])) {
            return false;
        }
        
        $productCollection = $this->db->getCollection('products');
        $items = $this->toArray($order['items']);
        
        foreach ($items as $item) {
            $quantity = (int)($item['quantity'] ?? 0);
            if ($quantity > 0) {
                // Convert string ID to ObjectId if needed
                $productId = $item['product_id'];
                if (is_string($productId)) {
                    try {
                        if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
                            $productId = new MongoDB\BSON\ObjectId($productId);
                        }
                    } catch (Exception $e) {
                        // If conversion fails, try with string
                    }
                }
                
                // First check current stock to ensure we don't go below zero
                $product = $productCollection->findOne(['_id' => $productId]);
                if ($product) {
                    $currentStock = (int)($product['stock'] ?? 0);
                    $newStock = max(0, $currentStock - $quantity); // Ensure stock doesn't go below 0
                    
                    // Update stock
                    $productCollection->updateOne(
                        ['_id' => $productId],
                        [
                            '$set' => [
                                'stock' => $newStock,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        ]
                    );
                    
                    // Log the stock reduction for debugging
                    error_log("Stock reduced for product {$item['product_id']}: {$currentStock} -> {$newStock} (reduced by {$quantity})");
                }
            }
        }
        
        return true;
    }

    /**
     * Restore product stock when order is cancelled
     */
    private function restoreOrderStock($orderId) {
        $order = $this->collection->findOne(['_id' => $orderId]);
        
        if (!$order || empty($order['items'])) {
            return false;
        }
        
        $productCollection = $this->db->getCollection('products');
        $items = $this->toArray($order['items']);
        
        foreach ($items as $item) {
            $quantity = (int)($item['quantity'] ?? 0);
            if ($quantity > 0) {
                // Convert string ID to ObjectId if needed
                $productId = $item['product_id'];
                if (is_string($productId)) {
                    try {
                        if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
                            $productId = new MongoDB\BSON\ObjectId($productId);
                        }
                    } catch (Exception $e) {
                        // If conversion fails, try with string
                    }
                }
                
                // Get current stock and restore
                $product = $productCollection->findOne(['_id' => $productId]);
                if ($product) {
                    $currentStock = (int)($product['stock'] ?? 0);
                    $newStock = $currentStock + $quantity;
                    
                    // Update stock
                    $productCollection->updateOne(
                        ['_id' => $productId],
                        [
                            '$set' => [
                                'stock' => $newStock,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        ]
                    );
                    
                    // Log the stock restoration for debugging
                    error_log("Stock restored for product {$item['product_id']}: {$currentStock} -> {$newStock} (restored {$quantity})");
                }
            }
        }
        
        return true;
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
     * Helper function to safely convert MongoDB objects to arrays
     */
    private function toArray($data) {
        if (is_array($data)) {
            return $data;
        }
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                return $data->toArray();
            }
            if (method_exists($data, 'getArrayCopy')) {
                return $data->getArrayCopy();
            }
            if ($data instanceof \Iterator) {
                return iterator_to_array($data);
            }
            return (array) $data;
        }
        return [];
    }

    /**
     * Enrich order with product details
     */
    private function enrichOrderWithProductDetails($order) {
        if (!isset($order['items']) || empty($order['items'])) {
            return $order;
        }
        
        // Convert BSONArray to regular array if needed
        $items = $this->toArray($order['items']);
        if (empty($items)) {
            return $order;
        }
        
        $productModel = new Product();
        $enrichedItems = [];
        
        foreach ($items as $item) {
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
        // Early return if orderId is null or empty
        if (empty($orderId)) {
            return null;
        }
        
        // Handle MongoDB BSONDocument objects
        if (is_object($orderId)) {
            // If it's already an ObjectId, use it directly
            if ($orderId instanceof MongoDB\BSON\ObjectId) {
                $objectId = $orderId;
            } else {
                // Try to extract the ObjectId from BSONDocument
                try {
                    if (method_exists($orderId, 'toArray')) {
                        $array = $orderId->toArray();
                        if (isset($array['$oid'])) {
                            $objectId = new MongoDB\BSON\ObjectId($array['$oid']);
                        } else {
                            return null; // No valid ObjectId found
                        }
                    } else {
                        return null; // Can't convert this object
                    }
                } catch (Exception $e) {
                    return null; // Conversion failed
                }
            }
        } else {
            // Handle string IDs
            try {
                $objectId = new MongoDB\BSON\ObjectId($orderId);
            } catch (Exception $e) {
                return null; // Invalid ObjectId string
            }
        }
        
        // Now we have a valid ObjectId, query the database
        try {
            $order = $this->collection->findOne(['_id' => $objectId]);
            if ($order) {
                return $this->enrichOrderWithProductDetails($order);
            }
            return null;
        } catch (Exception $e) {
            return null; // Database query failed
        }
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
        
        // Restore stock for cancelled order
        $this->restoreOrderStock($orderId);
        
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
            $orderList[] = $this->enrichOrderWithProductDetails($order);
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
     * Check and expire old pending orders
     */
    public function expireOldOrders() {
        $expiredOrders = $this->collection->find([
            'status' => 'pending',
            'expires_at' => ['$lt' => date('Y-m-d H:i:s')]
        ]);
        
        $expiredCount = 0;
        foreach ($expiredOrders as $order) {
            $this->updateOrderStatus($order['_id'], 'expired');
            $expiredCount++;
        }
        
        return $expiredCount;
    }

    /**
     * Check if order is expired
     */
    public function isOrderExpired($orderId) {
        $order = $this->getById($orderId);
        if (!$order) {
            return true;
        }
        
        if ($order['status'] !== 'pending') {
            return false; // Only pending orders can be expired
        }
        
        return isset($order['expires_at']) && $order['expires_at'] < date('Y-m-d H:i:s');
    }

    /**
     * Confirm order and reduce stock (called after successful payment)
     */
    public function confirmOrderAndReduceStock($orderId) {
        try {
            // Check if order is expired
            if ($this->isOrderExpired($orderId)) {
                throw new Exception('Order has expired. Please place a new order.');
            }
            
            // Update order status to confirmed
            $statusUpdated = $this->updateOrderStatus($orderId, 'confirmed');
            if (!$statusUpdated) {
                return false;
            }
            
            // Now reduce stock
            $stockReduced = $this->reduceOrderStock($orderId);
            if (!$stockReduced) {
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in confirmOrderAndReduceStock for order {$orderId}: " . $e->getMessage());
            return false;
        }
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
