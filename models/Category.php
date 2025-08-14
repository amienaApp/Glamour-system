<?php
/**
 * Category Model
 * Handles all category-related operations
 */

require_once __DIR__ . '/../config/database.php';

class Category {
    private $collection;

    public function __construct() {
        $db = Database::getInstance();
        $this->collection = $db->getCollection('categories');
    }

    // Basic CRUD Operations
    public function getAll($sort = []) {
        $cursor = $this->collection->find([], ['sort' => $sort ?: ['name' => 1]]);
        return iterator_to_array($cursor);
    }

    public function getById($id) {
        return $this->collection->findOne(['_id' => $id]);
    }

    public function getByName($name) {
        return $this->collection->findOne(['name' => $name]);
    }

    public function create($categoryData) {
        $result = $this->collection->insertOne($categoryData);
        return $result->getInsertedId();
    }

    public function update($id, $updateData) {
        $result = $this->collection->updateOne(
            ['_id' => $id],
            ['$set' => $updateData]
        );
        return $result->getModifiedCount() > 0;
    }

    public function delete($id) {
        $result = $this->collection->deleteOne(['_id' => $id]);
        return $result->getDeletedCount() > 0;
    }

    // Subcategory Operations
    public function getSubcategories($categoryName) {
        $category = $this->getByName($categoryName);
        return $category['subcategories'] ?? [];
    }

    public function addSubcategory($categoryName, $subcategoryName) {
        $result = $this->collection->updateOne(
            ['name' => $categoryName],
            ['$push' => ['subcategories' => $subcategoryName]]
        );
        return $result->getModifiedCount() > 0;
    }

    public function removeSubcategory($categoryName, $subcategoryName) {
        $result = $this->collection->updateOne(
            ['name' => $categoryName],
            ['$pull' => ['subcategories' => $subcategoryName]]
        );
        return $result->getModifiedCount() > 0;
    }

    // Statistics and Analytics
    public function getCount() {
        return $this->collection->countDocuments();
    }

    public function getCategoryStats() {
        $categories = $this->getAll();
        $stats = [];

        foreach ($categories as $category) {
            $subCount = count($category['subcategories'] ?? []);
            $stats[$category['name']] = [
                'total_subcategories' => $subCount,
                'created_at' => $category['createdAt'] ?? null,
                'updated_at' => $category['updatedAt'] ?? null
            ];
        }

        return $stats;
    }

    // Search and Filter Operations
    public function search($query) {
        return $this->collection->find([
            '$text' => ['$search' => $query]
        ]);
    }

    public function getCategoriesWithSubcategoryCount() {
        $categories = $this->getAll();
        $result = [];

        foreach ($categories as $category) {
            $result[] = [
                'id' => $category['_id'],
                'name' => $category['name'],
                'subcategory_count' => count($category['subcategories'] ?? []),
                'subcategories' => $category['subcategories'] ?? [],
                'created_at' => $category['createdAt'] ?? null
            ];
        }

        return $result;
    }

    // Data Initialization
    public function initializeDefaultCategories() {
        $defaultCategories = [
            [
                'name' => "Women's Clothing",
                'subcategories' => ['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Activewear', 'Lingerie', 'Swimwear'],
                'description' => 'Fashionable clothing for women of all ages',
                'icon' => 'fa-female'
            ],
            [
                'name' => "Men's Clothing",
                'subcategories' => ['Shirts', 'Pants', 'Jackets', 'Activewear', 'Underwear', 'Swimwear'],
                'description' => 'Stylish clothing for men',
                'icon' => 'fa-male'
            ],
            [
                'name' => "Kids' Clothing",
                'subcategories' => ['Boys', 'Girls', 'Baby', 'Toddler'],
                'description' => 'Adorable clothing for children',
                'icon' => 'fa-child'
            ],
            [
                'name' => "Accessories",
                'subcategories' => ['Bags', 'Jewelry', 'Shoes', 'Hats', 'Scarves', 'Belts'],
                'description' => 'Complete your look with our accessories',
                'icon' => 'fa-diamond'
            ],
            [
                'name' => "Home & Living",
                'subcategories' => ['Bedding', 'Bath', 'Kitchen', 'Decor', 'Furniture'],
                'description' => 'Beautiful items for your home',
                'icon' => 'fa-home'
            ],
            [
                'name' => "Beauty & Cosmetics",
                'subcategories' => ['Skincare', 'Makeup', 'Hair Care', 'Fragrances', 'Tools'],
                'description' => 'Beauty products for everyone',
                'icon' => 'fa-magic'
            ],
            [
                'name' => "Electronics",
                'subcategories' => ['Phones', 'Laptops', 'Tablets', 'Accessories', 'Smart Watches'],
                'description' => 'Latest technology and gadgets',
                'icon' => 'fa-mobile'
            ],
            [
                'name' => "Sports & Fitness",
                'subcategories' => ['Athletic Wear', 'Sports Equipment', 'Fitness Accessories', 'Outdoor Gear'],
                'description' => 'Everything for an active lifestyle',
                'icon' => 'fa-futbol-o'
            ]
        ];

        $addedCount = 0;
        $existingCount = 0;

        foreach ($defaultCategories as $category) {
            $existing = $this->getByName($category['name']);
            if (!$existing) {
                if ($this->create($category)) {
                    $addedCount++;
                }
            } else {
                $existingCount++;
            }
        }

        return [
            'added' => $addedCount,
            'existing' => $existingCount,
            'total' => count($defaultCategories)
        ];
    }

    // Utility Methods
    public function validateCategoryData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Category name is required';
        }
        
        if (strlen($data['name']) < 2) {
            $errors[] = 'Category name must be at least 2 characters long';
        }
        
        if (strlen($data['name']) > 50) {
            $errors[] = 'Category name must be less than 50 characters';
        }
        
        return $errors;
    }

    public function validateSubcategoryData($categoryName, $subcategoryName) {
        $errors = [];
        
        if (empty($subcategoryName)) {
            $errors[] = 'Subcategory name is required';
        }
        
        if (strlen($subcategoryName) < 2) {
            $errors[] = 'Subcategory name must be at least 2 characters long';
        }
        
        if (strlen($subcategoryName) > 50) {
            $errors[] = 'Subcategory name must be less than 50 characters';
        }
        
        // Check if subcategory already exists
        $category = $this->getByName($categoryName);
        if ($category && in_array($subcategoryName, $category['subcategories'] ?? [])) {
            $errors[] = 'Subcategory already exists in this category';
        }
        
        return $errors;
    }

    public function getCategorySummary() {
        $total = $this->getCount();
        $categories = $this->getAll();
        $totalSubcategories = 0;
        
        foreach ($categories as $category) {
            $totalSubcategories += count($category['subcategories'] ?? []);
        }

        return [
            'total_categories' => $total,
            'total_subcategories' => $totalSubcategories,
            'average_subcategories_per_category' => $total > 0 ? round($totalSubcategories / $total, 2) : 0
        ];
    }

    // Bulk Operations
    public function bulkCreate($categories) {
        $results = [];
        foreach ($categories as $category) {
            $errors = $this->validateCategoryData($category);
            if (empty($errors)) {
                $id = $this->create($category);
                $results[] = ['success' => true, 'id' => $id, 'name' => $category['name']];
            } else {
                $results[] = ['success' => false, 'name' => $category['name'], 'errors' => $errors];
            }
        }
        return $results;
    }

    public function bulkAddSubcategories($categoryName, $subcategories) {
        $results = [];
        foreach ($subcategories as $subcategory) {
            $errors = $this->validateSubcategoryData($categoryName, $subcategory);
            if (empty($errors)) {
                $success = $this->addSubcategory($categoryName, $subcategory);
                $results[] = ['success' => $success, 'subcategory' => $subcategory];
            } else {
                $results[] = ['success' => false, 'subcategory' => $subcategory, 'errors' => $errors];
            }
        }
        return $results;
    }
}
?>





