<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';

class Product {
    private $collection;

    public function __construct() {
        $db = MongoDB::getInstance();
        $this->collection = $db->getCollection('products');
    }

    // Basic CRUD Operations
    public function getAll($filters = [], $sort = [], $limit = 0, $skip = 0) {
        $options = [];
        if (!empty($sort)) $options['sort'] = $sort;
        if ($limit > 0) $options['limit'] = $limit;
        if ($skip > 0) $options['skip'] = $skip;

            // Handle regex search for name field
    if (isset($filters['name']) && is_array($filters['name']) && isset($filters['name']['$regex'])) {
        // MongoDB handles regex natively, so we can use it directly
        $searchTerm = $filters['name']['$regex'];
        $caseInsensitive = isset($filters['name']['$options']) && strpos($filters['name']['$options'], 'i') !== false;
        
        // Convert to MongoDB regex format
        $filters['name'] = new MongoDB\BSON\Regex($searchTerm, $caseInsensitive ? 'i' : '');
    }

        $cursor = $this->collection->find($filters, $options);
        return iterator_to_array($cursor);
    }

    public function getById($id) {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            return $this->collection->findOne(['_id' => $id]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get multiple products by IDs (OPTIMIZED for cart loading)
     */
    public function getByIds($ids) {
        try {
            // Convert string IDs to ObjectIds if needed
            $objectIds = [];
            foreach ($ids as $id) {
                if (is_string($id) && strlen($id) === 24) {
                    try {
                        $objectIds[] = new MongoDB\BSON\ObjectId($id);
                    } catch (Exception $e) {
                        // If conversion fails, keep as string
                        $objectIds[] = $id;
                    }
                } else {
                    $objectIds[] = $id;
                }
            }
            
            // Single query to get all products
            $cursor = $this->collection->find(['_id' => ['$in' => $objectIds]]);
            return iterator_to_array($cursor);
        } catch (Exception $e) {
            return [];
        }
    }

    public function create($productData) {
        // Convert color_variants to MongoDB BSONArray if it's a regular array
        if (isset($productData['color_variants']) && is_array($productData['color_variants'])) {
            // MongoDB will automatically convert arrays to BSONArray when inserting
            // No need to manually convert - just let MongoDB handle it
        }
        
        // Add timestamps
        $productData['createdAt'] = new MongoDB\BSON\UTCDateTime();
        $productData['updatedAt'] = new MongoDB\BSON\UTCDateTime();
        
        $result = $this->collection->insertOne($productData);
        return $result->getInsertedId();
    }

    public function update($id, $updateData) {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            
            // MongoDB will automatically convert arrays to BSONArray when updating
            // No need to manually convert - just let MongoDB handle it
            
            // Add updatedAt timestamp
            $updateData['updatedAt'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $this->collection->updateOne(['_id' => $id], ['$set' => $updateData]);
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            $result = $this->collection->deleteOne(['_id' => $id]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Category Operations
    public function getByCategory($category, $sort = ['_id' => -1]) {
        return $this->getAll(['category' => $category], $sort);
    }

    public function getBySubcategory($subcategory, $sort = ['_id' => -1]) {
        return $this->getAll(['subcategory' => $subcategory], $sort);
    }

    // Special Types
    public function getFeatured($sort = ['_id' => -1]) {
        return $this->getAll(['featured' => true], $sort);
    }

    public function getByCategoryAndFeatured($category, $featured = true, $sort = ['_id' => -1]) {
        return $this->getAll(['category' => $category, 'featured' => $featured], $sort);
    }

    public function getOnSale($sort = ['_id' => -1]) {
        return $this->getAll(['sale' => true], $sort);
    }

    public function getAvailable($sort = ['_id' => -1]) {
        return $this->getAll(['available' => true], $sort);
    }

    public function getByCategoryAvailable($category, $sort = ['_id' => -1]) {
        return $this->getAll(['category' => $category, 'available' => true], $sort);
    }

    public function getFeaturedAvailable($sort = ['_id' => -1]) {
        return $this->getAll(['featured' => true, 'available' => true], $sort);
    }

    public function getNewArrivals($limit = 8) {
        return $this->getAll([], ['createdAt' => -1], $limit);
    }

    // Statistics
    public function getCount($filters = []) {
        return $this->collection->countDocuments($filters);
    }

    public function getCategories() {
        return $this->collection->distinct('category');
    }

    public function getSubcategories() {
        return $this->collection->distinct('subcategory');
    }

    // Pagination
    public function getPaginated($page = 1, $perPage = 12, $filters = [], $sort = []) {
        $skip = ($page - 1) * $perPage;
        $products = $this->getAll($filters, $sort, $perPage, $skip);
        $total = $this->getCount($filters);
        
        return [
            'products' => $products,
            'total' => $total,
            'pages' => ceil($total / $perPage),
            'currentPage' => $page,
            'perPage' => $perPage
        ];
    }

    // Data Initialization
    public function initializeDefaultProducts() {
        $defaultProducts = [
            [
                'name' => 'Elegant Summer Dress',
                'price' => 89.99,
                'color' => '#FF6B6B',
                'category' => "Women's Clothing",
                'subcategory' => 'Dresses',
                'images' => ['front' => 'img/women/dresses/1.webp', 'back' => 'img/women/dresses/1.1.webp'],
                'description' => 'Beautiful summer dress perfect for any occasion',
                'featured' => true,
                'sale' => false
            ],
            [
                'name' => 'Classic Black Dress',
                'price' => 129.99,
                'color' => '#2C3E50',
                'category' => "Women's Clothing",
                'subcategory' => 'Dresses',
                'images' => ['front' => 'img/women/dresses/2.webp', 'back' => 'img/women/dresses/2.1.webp'],
                'description' => 'Timeless black dress for formal events',
                'featured' => true,
                'sale' => true,
                'salePrice' => 99.99
            ],
            [
                'name' => 'Formal White Shirt',
                'price' => 45.99,
                'color' => '#ECF0F1',
                'category' => "Men's Clothing",
                'subcategory' => 'Shirts',
                'images' => ['front' => 'img/men/shirts/shirt1.jpg', 'back' => 'img/men/shirts/shirt2.1.avif'],
                'description' => 'Classic white formal shirt for business',
                'featured' => true,
                'sale' => false
            ],
            [
                'name' => 'Classic Cotton T-Shirt',
                'price' => 24.99,
                'color' => '#FFFFFF',
                'category' => "Men's Clothing",
                'subcategory' => 'T-Shirts',
                'images' => ['front' => 'img/men/t-shirts/1.jpg', 'back' => 'img/men/t-shirts/1.1.jpg'],
                'description' => 'Comfortable cotton t-shirt perfect for everyday wear',
                'featured' => true,
                'sale' => false,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'available' => true,
                'stock' => 50
            ],
            [
                'name' => 'Graphic Print T-Shirt',
                'price' => 29.99,
                'color' => '#000000',
                'category' => "Men's Clothing",
                'subcategory' => 'T-Shirts',
                'images' => ['front' => 'img/men/t-shirts/2.jpg', 'back' => 'img/men/t-shirts/2.1.jpg'],
                'description' => 'Stylish graphic print t-shirt with modern design',
                'featured' => false,
                'sale' => true,
                'salePrice' => 19.99,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'available' => true,
                'stock' => 25
            ],
            [
                'name' => 'V-Neck T-Shirt',
                'price' => 22.99,
                'color' => '#2E86AB',
                'category' => "Men's Clothing",
                'subcategory' => 'T-Shirts',
                'images' => ['front' => 'img/men/t-shirts/3.jpg', 'back' => 'img/men/t-shirts/3.1.jpg'],
                'description' => 'Elegant v-neck t-shirt in navy blue',
                'featured' => false,
                'sale' => false,
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'available' => true,
                'stock' => 30
            ],
            [
                'name' => 'Leather Handbag',
                'price' => 149.99,
                'color' => '#8B4513',
                'category' => 'Accessories',
                'subcategory' => 'Bags',
                'images' => ['front' => 'img/bags/1.avif', 'back' => 'img/category/bags1.jpg'],
                'description' => 'Premium leather handbag for everyday use',
                'featured' => true,
                'sale' => false
            ],
        ];

        $addedCount = 0;
        foreach ($defaultProducts as $product) {
            $existing = $this->collection->findOne(['name' => $product['name'], 'category' => $product['category']]);
            if (!$existing) {
                if ($this->create($product)) {
                    $addedCount++;
                }
            }
        }

        return ['added' => $addedCount, 'total' => count($defaultProducts)];
    }

    // Utility Methods
    public function validateProductData($data) {
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Product name is required';
        if (!isset($data['price']) || $data['price'] <= 0) $errors[] = 'Valid price is required';
        if (empty($data['category'])) $errors[] = 'Category is required';
        
        // Validate stock and availability
        if (isset($data['stock']) && $data['stock'] < 0) {
            $errors[] = 'Stock quantity cannot be negative';
        }
        if (isset($data['available']) && !is_bool($data['available'])) {
            $errors[] = 'Availability must be a boolean value';
        }
        
        // Validate color variants if present
        if (isset($data['color_variants']) && is_array($data['color_variants'])) {
            foreach ($data['color_variants'] as $index => $variant) {
                if (empty($variant['name'])) {
                    $errors[] = "Color variant #" . ($index + 1) . " name is required";
                }
                if (empty($variant['color'])) {
                    $errors[] = "Color variant #" . ($index + 1) . " color is required";
                }
                if (isset($variant['stock']) && $variant['stock'] < 0) {
                    $errors[] = "Color variant #" . ($index + 1) . " stock cannot be negative";
                }
            }
        }
        
        return $errors;
    }

    public function getProductSummary() {
        $total = $this->getCount();
        $featured = $this->getCount(['featured' => true]);
        $onSale = $this->getCount(['sale' => true]);
        $categories = count($this->getCategories());
        $subcategories = count($this->getSubcategories());

        return [
            'total_products' => $total,
            'featured_products' => $featured,
            'products_on_sale' => $onSale,
            'categories' => $categories,
            'subcategories' => $subcategories
        ];
    }

    public function getByNameAndSubcategory($name, $subcategory) {
        return $this->collection->findOne(['name' => $name, 'subcategory' => $subcategory]);

    }
}
?>
