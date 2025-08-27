<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mongodb.php';

class Category {
    private $collection;

    public function __construct() {
        $db = MongoDB::getInstance();
        $this->collection = $db->getCollection('categories');
    }

    // Get all categories
    public function getAll() {
        try {
            $cursor = $this->collection->find([], ['sort' => ['order' => 1, 'name' => 1]]);
            $categories = iterator_to_array($cursor);
            
            // If no categories in database, return default categories
            if (empty($categories)) {
                return $this->getDefaultCategories();
            }
            
            return $categories;
        } catch (Exception $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return $this->getDefaultCategories();
        }
    }

    // Get category by ID
    public function getById($id) {
        try {
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            return $this->collection->findOne(['_id' => $id]);
        } catch (Exception $e) {
            return null;
        }
    }

    // Get category by name
    public function getByName($name) {
        try {
            return $this->collection->findOne(['name' => $name]);
        } catch (Exception $e) {
            return null;
        }
    }

    // Create new category
    public function create($categoryData) {
        try {
            $categoryData['createdAt'] = new MongoDB\BSON\UTCDateTime();
            $categoryData['updatedAt'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $this->collection->insertOne($categoryData);
            return $result->getInsertedId();
        } catch (Exception $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    // Update category
    public function update($id, $updateData) {
        try {
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            
            $updateData['updatedAt'] = new MongoDB\BSON\UTCDateTime();
            $result = $this->collection->updateOne(['_id' => $id], ['$set' => $updateData]);
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    // Delete category
    public function delete($id) {
        try {
            if (is_string($id) && strlen($id) === 24) {
                $id = new MongoDB\BSON\ObjectId($id);
            }
            $result = $this->collection->deleteOne(['_id' => $id]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    // Get subcategories for a category
    public function getSubcategories($categoryName) {
        try {
            $category = $this->getByName($categoryName);
            return $category['subcategories'] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    // Default categories if database is empty
    private function getDefaultCategories() {
        return [
            [
                'name' => "Women's Clothing",
                'description' => 'Fashionable clothing for women',
                'subcategories' => [
                    'Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Activewear', 
                    'Lingerie', 'Swimwear', 'Wedding Guest', 'Wedding-dress', 
                    'Abaya', 'Summer-dresses', 'Homecoming'
                ],
                'order' => 1
            ],
            [
                'name' => "Men's Clothing",
                'description' => 'Stylish clothing for men',
                'subcategories' => [
                    'Shirts', 'T-Shirts', 'Suits', 'Pants', 'Shorts', 
                    'Hoodies & Sweatshirts', 'Jackets', 'Activewear', 
                    'Underwear', 'Swimwear'
                ],
                'order' => 2
            ],
            [
                'name' => "Kids' Clothing",
                'description' => 'Adorable clothing for children',
                'subcategories' => [
                    'Boys Clothing', 'Girls Clothing', 'Baby Clothing', 
                    'School Uniforms', 'Party Wear'
                ],
                'order' => 3
            ],
            [
                'name' => "Accessories",
                'description' => 'Complete your look with accessories',
                'subcategories' => [
                    'Shoes', 'Bags', 'Jewelry', 'Hats', 'Scarves', 'Belts'
                ],
                'order' => 4
            ],
            [
                'name' => "Home & Living",
                'description' => 'Beautiful home decor and furniture',
                'subcategories' => [
                    'Furniture', 'Decor', 'Kitchen', 'Bathroom', 'Bedding'
                ],
                'order' => 5
            ],
            [
                'name' => "Beauty & Cosmetics",
                'description' => 'Beauty products and cosmetics',
                'subcategories' => [
                    'Fragrances', 'Skincare', 'Makeup', 'Hair Care', 'Tools'
                ],
                'order' => 6
            ],
            [
                'name' => "Sports & Fitness",
                'description' => 'Sports equipment and fitness gear',
                'subcategories' => [
                    'Workout Clothes', 'Sports Equipment', 'Fitness Accessories'
                ],
                'order' => 7
            ],
            [
                'name' => "Perfumes",
                'description' => 'Luxury fragrances for men and women',
                'subcategories' => [
                    "Men's Perfumes", "Women's Perfumes", "Unisex Perfumes"
                ],
                'order' => 8
            ],
            [
                'name' => "Shoes",
                'description' => 'Stylish footwear for all occasions',
                'subcategories' => [
                    "Men's Shoes", "Women's Shoes", "Children's Shoes", 
                    "Sports Shoes", "Formal Shoes", "Casual Shoes"
                ],
                'order' => 9
            ]
        ];
    }
}
