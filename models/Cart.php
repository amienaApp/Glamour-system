<?php
/**
 * Cart Model
 * Handles shopping cart operations
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/Product.php'; // Required for getById in getCart

class Cart {
    private $db;
    private $collection;

    public function __construct() {
        $this->db = MongoDB::getInstance();
        $this->collection = $this->db->getCollection('carts');
    }

    /**
     * Add item to cart
     */
    public function addToCart($userId, $productId, $quantity = 1, $color = '', $size = '', $productData = null) {
        // Convert string ID to ObjectId if needed
        if (is_string($productId)) {
            try {
                $productId = new MongoDB\BSON\ObjectId($productId);
            } catch (Exception $e) {
                // If conversion fails, try with string
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
                $existingCart['items'][] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'color' => $color,
                    'size' => $size,
                    'added_at' => date('Y-m-d H:i:s')
                ];
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
            $cartData = [
                'user_id' => $userId,
                'items' => [
                    [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'color' => $color,
                        'size' => $size,
                        'added_at' => date('Y-m-d H:i:s')
                    ]
                ],
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
        
        // Get product details for each item
        $productModel = new Product();
        $items = [];
        $total = 0;
        $itemCount = 0;
        
        foreach ($cart['items'] as $item) {
            // Convert string ID to ObjectId if needed
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
                $item['product'] = $product;
                $item['subtotal'] = $product['price'] * $item['quantity'];
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
        
        // Find and update the specific item
        $updated = false;
        foreach ($cart['items'] as &$item) {
            // Handle both string and ObjectId comparisons
            $itemProductId = $item['product_id'];
            if (is_object($itemProductId)) {
                $itemProductId = (string)$itemProductId;
            }
            if (is_object($productId)) {
                $productIdStr = (string)$productId;
            } else {
                $productIdStr = $productId;
            }
            
            if ($itemProductId == $productIdStr) {
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
                    'items' => $cart['items'],
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
        // Convert string ID to ObjectId if needed
        if (is_string($productId)) {
            try {
                $productId = new MongoDB\BSON\ObjectId($productId);
            } catch (Exception $e) {
                // If conversion fails, try with string
            }
        }

        // Get the current cart
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart || empty($cart['items'])) {
            return false;
        }
        
        // Remove the specific item
        $cart['items'] = array_filter($cart['items'], function($item) use ($productId) {
            // Handle both string and ObjectId comparisons
            $itemProductId = $item['product_id'];
            if (is_object($itemProductId)) {
                $itemProductId = (string)$itemProductId;
            }
            if (is_object($productId)) {
                $productIdStr = (string)$productId;
            } else {
                $productIdStr = $productId;
            }
            
            return $itemProductId != $productIdStr;
        });
        
        // Update the cart with the modified items
        $result = $this->collection->updateOne(
            ['user_id' => $userId],
            [
                '$set' => [
                    'items' => array_values($cart['items']), // Reindex array
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
        $result = $this->collection->updateOne(
            ['user_id' => $userId],
            [
                '$set' => [
                    'items' => [],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
        
        return $result->getModifiedCount() > 0;
    }

    /**
     * Get cart item count for user
     */
    public function getCartItemCount($userId) {
        $cart = $this->collection->findOne(['user_id' => $userId]);
        
        if (!$cart || empty($cart['items'])) {
            return 0;
        }
        
        $count = 0;
        foreach ($cart['items'] as $item) {
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
}
?>
