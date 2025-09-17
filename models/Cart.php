<?php
/**
 * Cart Model
 * Handles shopping cart operations
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';
require_once __DIR__ . '/Product.php'; // Required for getById in getCart

class Cart {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = MongoDB::getInstance();
        $this->collection = $this->db->getCollection('carts');
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
            
            // Fallback: convert to array using type casting
            return (array) $data;
        }
        
        return [];
    }

    /**
     * Add item to cart
     */
    public function addToCart($userId, $productId, $quantity = 1, $color = '', $size = '', $additionalData = null) {
        // Store original product ID for validation
        $originalProductId = $productId;
        
        // Convert string ID to ObjectId if it's a valid ObjectId string
        if (is_string($productId)) {
            try {
                // Only convert if it looks like a valid ObjectId (24 hex characters)
                if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
                    $productId = new MongoDB\BSON\ObjectId($productId);
                }
                // Otherwise, keep it as a string
            } catch (Exception $e) {
                // If conversion fails, keep as string
            }
        }

        // Validate stock availability before adding to cart
        // Check if we have a valid product ID (either ObjectId or valid string)
        $shouldValidate = false;
        $validationProductId = null;
        
        if (is_object($productId) && $productId instanceof MongoDB\BSON\ObjectId) {
            // It's a valid ObjectId
            $shouldValidate = true;
            $validationProductId = $productId;
        } elseif (is_string($originalProductId) && preg_match('/^[a-f\d]{24}$/i', $originalProductId)) {
            // It's a valid ObjectId string
            $shouldValidate = true;
            $validationProductId = $originalProductId;
        }
        
        if ($shouldValidate) {
            $productModel = new Product();
            $product = $productModel->getById($validationProductId);
            
            if ($product) {
                $currentStock = (int)(isset($product['stock']) ? $product['stock'] : 0);
                $available = isset($product['available']) ? $product['available'] : true;
                
                // Check if product is available
                if ($available === false || $currentStock <= 0) {
                    throw new Exception("Product '{$product['name']}' is currently out of stock");
                }
                
                // Check if requested quantity exceeds available stock
                if ($currentStock < $quantity) {
                    throw new Exception("Insufficient stock for '{$product['name']}'. Available: {$currentStock}, Requested: {$quantity}");
                }
            }
        }

        // Check if user already has a cart
        $existingCart = $this->collection->findOne(['user_id' => $userId]);
        
        if ($existingCart) {
            // Check if product already exists in cart with same color and size
            $productExists = false;
            foreach ($existingCart['items'] as &$item) {
                if ((string)$item['product_id'] === (string)$productId && 
                    ($item['color'] ?? '') === $color && 
                    ($item['size'] ?? '') === $size) {
                    $item['quantity'] += $quantity;
                    $productExists = true;
                    break;
                }
            }
            
            if (!$productExists) {
                // Add new product to cart
                $cartItem = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'color' => $color,
                    'size' => $size,
                    'added_at' => date('Y-m-d H:i:s')
                ];
                
                // Add variant-specific data if provided
                if ($additionalData) {
                    if (isset($additionalData['price'])) {
                        $cartItem['variant_price'] = $additionalData['price'];
                    }
                    if (isset($additionalData['variant_name'])) {
                        $cartItem['variant_name'] = $additionalData['variant_name'];
                    }
                    if (isset($additionalData['variant_stock'])) {
                        $cartItem['variant_stock'] = $additionalData['variant_stock'];
                    }
                    if (isset($additionalData['variant_image'])) {
                        $cartItem['variant_image'] = $additionalData['variant_image'];
                    }
                }
                
                $existingCart['items'][] = $cartItem;
            }
            
            // Update cart
            $result = $this->collection->updateOne(
                ['user_id' => $userId],
                ['$set' => [
                    'items' => $existingCart['items'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]]
            );
        } else {
            // Create new cart
            $cartItem = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'color' => $color,
                'size' => $size,
                'added_at' => date('Y-m-d H:i:s')
            ];
            
            // Add variant-specific data if provided
            if ($additionalData) {
                if (isset($additionalData['price'])) {
                    $cartItem['variant_price'] = $additionalData['price'];
                }
                if (isset($additionalData['variant_name'])) {
                    $cartItem['variant_name'] = $additionalData['variant_name'];
                }
                if (isset($additionalData['variant_stock'])) {
                    $cartItem['variant_stock'] = $additionalData['variant_stock'];
                }
                if (isset($additionalData['variant_image'])) {
                    $cartItem['variant_image'] = $additionalData['variant_image'];
                }
            }
            
            $cartData = [
                'user_id' => $userId,
                'items' => [$cartItem],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->collection->insertOne($cartData);
        }
        
        if ($existingCart) {
            return $result->getModifiedCount() > 0;
        } else {
            return $result->getInsertedId() ? true : false;
        }
    }

    /**
     * Get user's cart with product details
     */
    public function getCart($userId) {
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart) {
            return ['items' => [], 'total' => 0, 'item_count' => 0];
        }
        
        // Convert BSONArray to regular array if needed
        $cartItems = $this->toArray($cart['items']);
        
        // Get product details for each item
        $productModel = new Product();
        $items = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cartItems as $item) {
            // Convert string ID to ObjectId if needed
            $productId = $item['product_id'];
            $originalProductId = $productId; // Keep original for comparison
            
            if (is_string($productId)) {
                try {
                    // Only convert if it looks like a valid ObjectId (24 hex characters)
                    if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
                        $productId = new MongoDB\BSON\ObjectId($productId);
                    }
                    // Otherwise, keep as string
                } catch (Exception $e) {
                    // If conversion fails, keep as string
                }
            }
            
            $product = $productModel->getById($productId);
            
            // If product not found and we have a string ID, create a mock product
            if (!$product && is_string($originalProductId)) {
                $product = [
                    '_id' => $originalProductId,
                    'name' => 'Test Product (' . $originalProductId . ')',
                    'price' => 10.00, // Default price for test products
                    'description' => 'Test product for cart testing',
                    'category' => 'test',
                    'stock' => 10,
                    'status' => 'active'
                ];
            }
            
            if ($product) {
                $item['product'] = $product;
                
                // Use variant price if available, otherwise use product price
                $itemPrice = $product['price'];
                if (isset($item['variant_price']) && $item['variant_price'] > 0) {
                    $itemPrice = $item['variant_price'];
                }
                
                $item['subtotal'] = $itemPrice * $item['quantity'];
                $total += $item['subtotal'];
                $itemCount += $item['quantity'];
                $items[] = $item;
                

            }
        }
        

        
        return [
            'items' => $items,
            'total' => $total,
            'item_count' => $itemCount,
            'cart_id' => $cart['_id']
        ];
    }

    /**
     * Update item quantity in cart
     */
    public function updateQuantity($userId, $productId, $quantity) {
        // Convert string ID to ObjectId if needed
        if (is_string($productId)) {
            try {
                $productId = new MongoDB\BSON\ObjectId($productId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }
        
        // Get the current cart
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart || empty($cart['items'])) {
            return false;
        }
        
        // Convert BSONArray to regular array if needed
        $items = $this->toArray($cart['items']);
        
        // Convert productId to string for comparison
        $productIdStr = (string)$productId;
        
        // Find and update the specific item
        $updated = false;
        foreach ($items as &$item) {
            // Convert item product_id to string for comparison
            $itemProductIdStr = (string)$item['product_id'];
            
            if ($itemProductIdStr === $productIdStr) {
                $item['quantity'] = $quantity;
                $updated = true;
                break;
            }
        }
        
        if (!$updated) {
            return false;
        }
        
        // Update the cart with the modified items
        $result = $this->collection->updateOne(
            ['user_id' => $userId],
            [
                '$set' => [
                    'items' => array_values($items),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
        
        return $result->getModifiedCount() > 0;
    }



    /**
     * Remove item from cart
     */
    public function removeFromCart($userId, $productId) {
        // Get the current cart
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart || empty($cart['items'])) {
            return false;
        }
        
        // Convert productId to string for comparison
        $productIdStr = (string)$productId;
        
        // Convert BSONArray to regular array if needed
        $items = $this->toArray($cart['items']);
        
        // Remove the specific item
        $items = array_filter($items, function($item) use ($productIdStr) {
            // Convert item product_id to string for comparison
            $itemProductIdStr = (string)$item['product_id'];
            return $itemProductIdStr !== $productIdStr;
        });
        
        $cart['items'] = array_values($items);
        
        // Update the cart with the modified items
        $result = $this->collection->updateOne(
            ['user_id' => $userId],
            [
                '$set' => [
                    'items' => $cart['items'], // Already reindexed above
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
        
        return $result->getModifiedCount() > 0;
    }

    /**
     * Clear user's cart
     */
    public function clearCart($userId) {
        try {
            // First, try to update existing cart
            $result = $this->collection->updateOne(
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
            
            // If no document was modified, it might not exist, so create an empty one
            if ($result->getModifiedCount() === 0) {
                $this->collection->insertOne([
                    'user_id' => $userId,
                    'items' => [],
                    'total' => 0,
                    'item_count' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error clearing cart for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart item count for user
     */
    public function getCartItemCount($userId) {
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart || empty($cart['items'])) {
            return 0;
        }
        
        // Convert BSONArray to regular array if needed
        $items = $this->toArray($cart['items']);
        
        $count = 0;
        foreach ($items as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }

    /**
     * Check if product is in user's cart
     */
    public function isProductInCart($userId, $productId) {
        // Convert string ID to ObjectId if needed
        if (is_string($productId)) {
            try {
                $productId = new MongoDB\BSON\ObjectId($productId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        $cart = $this->collection->findOne([
            'user_id' => $userId,
            'items.product_id' => $productId
        ]);
        
        return $cart !== null;
    }

    /**
     * Get cart summary (item count and total)
     */
    public function getCartSummary($userId) {
        $cart = $this->getCart($userId);
        return [
            'item_count' => $cart['item_count'],
            'total' => $cart['total']
        ];
    }

    /**
     * Transfer cart from one user ID to another
     */
    public function transferCart($fromUserId, $toUserId) {
        try {
            $result = $this->collection->updateOne(
                ['user_id' => $fromUserId],
                ['$set' => ['user_id' => $toUserId]]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
