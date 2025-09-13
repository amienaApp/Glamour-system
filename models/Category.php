<?php
/**
 * Category Model
 * Handles all category-related operations
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config1/mongodb.php';

class Category {
    private $collection;

    public function __construct() {
        $db = MongoDB::getInstance();
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
        if (!$category || !isset($category['subcategories'])) {
            return [];
        }
        
        // Handle both old format (array of strings) and new format (array of objects/BSONDocument)
        $subcategories = [];
        foreach ($category['subcategories'] as $sub) {
            if (is_array($sub) && isset($sub['name'])) {
                // New format: array with name and sub_subcategories
                $subcategories[] = $sub['name'];
            } elseif (is_object($sub) && isset($sub['name'])) {
                // New format: BSONDocument with name and sub_subcategories
                $subcategories[] = $sub['name'];
            } else {
                // Old format: just a string
                $subcategories[] = $sub;
            }
        }
        
        return $subcategories;
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

    // Sub-subcategory Operations (for Beauty & Cosmetics)
    public function getSubSubcategories($categoryName, $subcategoryName) {
        $category = $this->getByName($categoryName);
        if (!$category || !isset($category['subcategories'])) {
        return [];
    }
        
        // Find the subcategory and return its sub-subcategories
        foreach ($category['subcategories'] as $sub) {
            // Handle both arrays and BSONDocument objects
            $subName = null;
            $subSubcategories = null;
            
            if (is_array($sub)) {
                $subName = $sub['name'] ?? null;
                $subSubcategories = $sub['sub_subcategories'] ?? null;
            } elseif (is_object($sub)) {
                $subName = $sub['name'] ?? null;
                $subSubcategories = $sub['sub_subcategories'] ?? null;
            }
            
            if ($subName === $subcategoryName && $subSubcategories) {
                
                // Convert BSONDocument/BSONArray to array if needed
                if (is_object($subSubcategories)) {
                    // For BSONDocument/BSONArray, convert to regular array
                    $subSubcategories = iterator_to_array($subSubcategories);
                }
                
                // Handle nested structure (like Kids' Clothing) vs flat structure (like Beauty)
                $flattenedSubSubcategories = [];
                
                if (is_array($subSubcategories)) {
                    foreach ($subSubcategories as $key => $value) {
                        // Convert BSONArray to regular array if needed
                        if (is_object($value)) {
                            $value = iterator_to_array($value);
                        }
                        
                        if (is_array($value)) {
                            // Nested structure: ['Category Name' => ['item1', 'item2', ...]]
                            foreach ($value as $item) {
                                // Convert BSONString to regular string if needed
                                if (is_object($item)) {
                                    $item = (string) $item;
                                }
                                $flattenedSubSubcategories[] = $item;
                            }
                        } else {
                            // Flat structure: ['item1', 'item2', ...]
                            // Convert BSONString to regular string if needed
                            if (is_object($value)) {
                                $value = (string) $value;
                            }
                            $flattenedSubSubcategories[] = $value;
                        }
                    }
                }
                
                return $flattenedSubSubcategories;
            }
        }
        
        return [];
    }

    public function addSubSubcategory($categoryName, $subcategoryName, $subSubcategoryName) {
        $result = $this->collection->updateOne(
            [
                'name' => $categoryName,
                'subcategories.name' => $subcategoryName
            ],
            ['$push' => ['subcategories.$.sub_subcategories' => $subSubcategoryName]]
        );
        return $result->getModifiedCount() > 0;
    }

    public function removeSubSubcategory($categoryName, $subcategoryName, $subSubcategoryName) {
        $result = $this->collection->updateOne(
            [
                'name' => $categoryName,
                'subcategories.name' => $subcategoryName
            ],
            ['$pull' => ['subcategories.$.sub_subcategories' => $subSubcategoryName]]
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
                'subcategories' => ['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Activewear', 'Wedding Dress', 'Bridesmaid Wear'],
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
                'subcategories' => [
                    [
                        'name' => 'Boys',
                        'sub_subcategories' => [
                            'T-Shirts', 'Polo Shirts', 'Button-Down Shirts', 'Tank Tops', 'Long Sleeve Tees',
                            'Jeans', 'Cargo Pants', 'Dress Pants', 'Shorts', 'Sweatpants'
                        ]
                    ],
                    [
                        'name' => 'Girls',
                        'sub_subcategories' => [
                            'Dresses', 'Tops', 'Blouses', 'T-Shirts', 'Tank Tops',
                            'Jeans', 'Leggings', 'Skirts', 'Shorts', 'Pants'
                        ]
                    ],
                    [
                        'name' => 'Toddlers',
                        'sub_subcategories' => [
                            'T-Shirts', 'Polo Shirts', 'Long Sleeve Tops', 'Tank Tops', 'Hoodies',
                            'Pants', 'Shorts', 'Dresses', 'Pajamas', 'Sweaters'
                        ]
                    ],
                    [
                        'name' => 'Baby',
                        'sub_subcategories' => [
                            'Onesies', 'Bodysuits', 'Sleepwear', 'Tops', 'T-Shirts',
                            'Pants', 'Dresses', 'Rompers', 'Jumpsuits', 'Sleep Gowns'
                        ]
                    ]
                ],
                'description' => 'Professional kids clothing for all occasions',
                'icon' => 'fa-child',
                'age_groups' => ['0-3 Months', '3-6 Months', '6-9 Months', '9-12 Months', '12-18 Months', '18-24 Months', '2-4 Years', '4-6 Years', '6-8 Years', '8-10 Years', '10-12 Years', '12-14 Years'],
                'sizes' => ['Preemie', 'Newborn', '0-3M', '3-6M', '6-9M', '9-12M', '12-18M', '18-24M', '2T', '3T', '4T', '5', '6', '7', '8', '10', '12', '14'],
                'genders' => ['Boys', 'Girls', 'Unisex'],
                'brands' => ['Carter\'s', 'Gap Kids', 'Old Navy', 'H&M Kids', 'Zara Kids', 'Uniqlo Kids'],
                'price_ranges' => ['$0-$10', '$10-$20', '$20-$30', '$30-$40', '$40+'],
                'colors' => ['Blue', 'Red', 'Green', 'Yellow', 'Pink', 'Purple', 'Orange', 'Black', 'White', 'Gray']
            ],
            [
                'name' => "Accessories",
                'subcategories' => ['Bags', 'Jewelry', 'Shoes', 'Hats', 'Scarves', 'Belts'],
                'description' => 'Complete your look with our accessories',
                'icon' => 'fa-diamond'
            ],
            [
                'name' => "Home & Living",
                'subcategories' => ['Bedding', 'living room', 'Kitchen', 'artwork', 'dinning room' , 'lighting'],
                'description' => 'Beautiful items for your home',
                'icon' => 'fa-home'
            ],
            [
                'name' => "Beauty & Cosmetics",
                'subcategories' => [
                    [
                        'name' => 'Makeup',
                        'sub_subcategories' => [
                            'Face' => ['Foundation', 'Concealer', 'Powder', 'Blush', 'Highlighter', 'Bronzer & Contour', 'Face Primer', 'Setting Spray'],
                            'Eye' => ['Mascara', 'Eyeliner', 'Eyeshadow', 'Eyebrow Pencils/Gels', 'False Lashes', 'Eye Primer'],
                            'Lip' => ['Lipstick', 'Lip Gloss', 'Lip Liner', 'Lip Stain', 'Lip Balm'],
                            'Nails' => ['Nail Polish', 'Nail Care & Treatments', 'Nail Tools'],
                            'Tools' => ['Brushes (Face, Eye, Lip)', 'Makeup Removers']
                        ]
                    ],
                    [
                        'name' => 'Skincare',
                        'sub_subcategories' => [
                            'Moisturizers' => ['Face Moisturizer', 'Body Lotion', 'Eye Cream', 'Night Cream'],
                            'Cleansers' => ['Face Wash', 'Cleansing Oil', 'Micellar Water', 'Exfoliating Scrub'],
                            'Masks' => ['Face Masks', 'Sheet Masks', 'Clay Masks', 'Peel-off Masks'],
                            'Call Who' => ['Serums', 'Toners', 'Essences', 'Spot Treatments'],
                            'cream' => ['Day Cream', 'Night Cream', 'Eye Cream', 'Hand Cream']
                        ]
                    ],
                    [
                        'name' => 'Hair',
                        'sub_subcategories' => [
                            'Shampoo' => ['Daily Shampoo', 'Clarifying Shampoo', 'Color-Safe Shampoo', 'Anti-Dandruff'],
                            'Conditioner' => ['Daily Conditioner', 'Deep Conditioner', 'Leave-in Conditioner', 'Hair Mask'],
                            'Tools' => ['Hair Dryer', 'Straightener', 'Curling Iron', 'Hair Brush']
                        ]
                    ],
                    [
                        'name' => 'Bath & Body',
                        'sub_subcategories' => [
                            'Shower gel' => ['Body Wash', 'Shower Gel', 'Shower Oil', 'Body Scrub'],
                            'Scrubs' => ['Body Scrub', 'Face Scrub', 'Foot Scrub', 'Hand Scrub'],
                            'soap' => ['Bar Soap', 'Liquid Soap', 'Antibacterial Soap', 'Natural Soap']
                        ]
                    ]
                ],
                'description' => 'Beauty products for everyone',
                'icon' => 'fa-magic'
            ],
          
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





