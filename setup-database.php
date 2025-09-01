<?php
/**
 * Database Setup Script
 * Initializes the database with sample data
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Glamour System - Database Setup</h2>";

try {
    // Include required files
    require_once 'config/mongodb.php';
    require_once 'models/Product.php';
    require_once 'models/Category.php';
    require_once 'models/User.php';
    
    $db = MongoDB::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception("Cannot connect to MongoDB");
    }
    
    echo "✅ Connected to MongoDB successfully<br>";
    
    // Initialize models
    $productModel = new Product();
    $categoryModel = new Category();
    $userModel = new User();
    
    echo "<h3>1. Initializing Categories...</h3>";
    
    // Create default categories
    $categories = [
        [
            'name' => "Women's Clothing",
            'slug' => 'womens-clothing',
            'description' => 'Fashionable clothing for women',
            'image' => 'img/category/women.jpg',
            'subcategories' => ['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Lingerie']
        ],
        [
            'name' => "Men's Clothing", 
            'slug' => 'mens-clothing',
            'description' => 'Stylish clothing for men',
            'image' => 'img/category/men.jpg',
            'subcategories' => ['Shirts', 'Pants', 'Suits', 'Casual Wear', 'Formal Wear']
        ],
        [
            'name' => "Children's Clothing",
            'slug' => 'childrens-clothing', 
            'description' => 'Cute and comfortable clothing for children',
            'image' => 'img/category/children.jpg',
            'subcategories' => ['Boys', 'Girls', 'Babies', 'School Uniforms']
        ],
        [
            'name' => 'Shoes',
            'slug' => 'shoes',
            'description' => 'Footwear for all ages and occasions',
            'image' => 'img/category/shoes.jpg',
            'subcategories' => ['Men\'s Shoes', 'Women\'s Shoes', 'Children\'s Shoes', 'Sports Shoes']
        ],
        [
            'name' => 'Bags',
            'slug' => 'bags',
            'description' => 'Handbags, backpacks, and luggage',
            'image' => 'img/category/bags.jpg',
            'subcategories' => ['Handbags', 'Backpacks', 'Luggage', 'Wallets']
        ],
        [
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Jewelry, watches, and other accessories',
            'image' => 'img/category/accessories.jpg',
            'subcategories' => ['Jewelry', 'Watches', 'Sunglasses', 'Belts', 'Scarves']
        ],
        [
            'name' => 'Perfumes',
            'slug' => 'perfumes',
            'description' => 'Fragrances for men and women',
            'image' => 'img/category/perfumes.jpg',
            'subcategories' => ['Men\'s Fragrances', 'Women\'s Fragrances', 'Unisex Fragrances']
        ]
    ];
    
    $categoryCount = 0;
    foreach ($categories as $categoryData) {
        $existing = $categoryModel->getBySlug($categoryData['slug']);
        if (!$existing) {
            $categoryModel->create($categoryData);
            $categoryCount++;
            echo "✅ Created category: " . $categoryData['name'] . "<br>";
        } else {
            echo "ℹ️ Category already exists: " . $categoryData['name'] . "<br>";
        }
    }
    
    echo "<h3>2. Initializing Sample Products...</h3>";
    
    // Initialize default products
    $result = $productModel->initializeDefaultProducts();
    echo "✅ Added " . $result['added'] . " new products<br>";
    echo "ℹ️ Total products in database: " . $result['total'] . "<br>";
    
    echo "<h3>3. Creating Admin User...</h3>";
    
    // Create default admin user
    $adminData = [
        'username' => 'admin',
        'email' => 'admin@glamour.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $existingAdmin = $userModel->getByUsername('admin');
    if (!$existingAdmin) {
        $userModel->create($adminData);
        echo "✅ Created admin user<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "ℹ️ Admin user already exists<br>";
    }
    
    echo "<h3>4. Database Setup Complete!</h3>";
    echo "✅ All components initialized successfully<br>";
    echo "✅ You can now access the website with sample data<br>";
    
    // Show summary
    $productSummary = $productModel->getProductSummary();
    $categorySummary = $categoryModel->getCategorySummary();
    
    echo "<h3>5. Database Summary</h3>";
    echo "📦 Total Products: " . $productSummary['total_products'] . "<br>";
    echo "🏷️ Total Categories: " . $categorySummary['total_categories'] . "<br>";
    echo "⭐ Featured Products: " . $productSummary['featured_products'] . "<br>";
    echo "💰 Products on Sale: " . $productSummary['products_on_sale'] . "<br>";
    
    echo "<h3>6. Next Steps</h3>";
    echo "🌐 <a href='http://localhost/Glamour-system/'>Visit Main Website</a><br>";
    echo "🔧 <a href='http://localhost/Glamour-system/admin/'>Access Admin Panel</a> (admin/admin123)<br>";
    echo "📝 <a href='http://localhost/Glamour-system/admin/add-product.php'>Add More Products</a><br>";
    
} catch (Exception $e) {
    echo "❌ Error during setup: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>

