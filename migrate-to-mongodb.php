<?php
/**
 * Data Migration Script
 * Migrates data from JSON files to MongoDB
 */

require_once 'config/database.php';

echo "🚀 Starting migration to MongoDB...\n\n";

try {
    $db = Database::getInstance();
    
    // Migrate Categories
    echo "📁 Migrating categories...\n";
    $categoriesFile = 'data/collections/categories.json';
    if (file_exists($categoriesFile)) {
        $categories = json_decode(file_get_contents($categoriesFile), true);
        $categoriesCollection = $db->getCollection('categories');
        
        foreach ($categories as $category) {
            // Remove existing _id if it exists
            unset($category['_id']);
            
            // Check if category already exists
            $existing = $categoriesCollection->findOne(['name' => $category['name']]);
            if (!$existing) {
                $categoriesCollection->insertOne($category);
                echo "  ✅ Added category: " . $category['name'] . "\n";
            } else {
                echo "  ⚠️  Category already exists: " . $category['name'] . "\n";
            }
        }
    }
    
    // Migrate Products
    echo "\n📦 Migrating products...\n";
    $productsFile = 'data/collections/products.json';
    if (file_exists($productsFile)) {
        $products = json_decode(file_get_contents($productsFile), true);
        $productsCollection = $db->getCollection('products');
        
        $count = 0;
        foreach ($products as $product) {
            // Remove existing _id if it exists
            unset($product['_id']);
            
            // Check if product already exists
            $existing = $productsCollection->findOne(['name' => $product['name']]);
            if (!$existing) {
                $productsCollection->insertOne($product);
                $count++;
                if ($count % 10 == 0) {
                    echo "  ✅ Migrated $count products...\n";
                }
            }
        }
        echo "  ✅ Total products migrated: $count\n";
    }
    
    // Migrate Users
    echo "\n👥 Migrating users...\n";
    $usersFile = 'data/collections/users.json';
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        $usersCollection = $db->getCollection('users');
        
        foreach ($users as $user) {
            // Remove existing _id if it exists
            unset($user['_id']);
            
            // Check if user already exists
            $existing = $usersCollection->findOne(['email' => $user['email']]);
            if (!$existing) {
                $usersCollection->insertOne($user);
                echo "  ✅ Added user: " . $user['username'] . "\n";
            } else {
                echo "  ⚠️  User already exists: " . $user['username'] . "\n";
            }
        }
    }
    
    // Migrate Admins
    echo "\n👨‍💼 Migrating admins...\n";
    $adminsFile = 'data/collections/admins.json';
    if (file_exists($adminsFile)) {
        $admins = json_decode(file_get_contents($adminsFile), true);
        $adminsCollection = $db->getCollection('admins');
        
        foreach ($admins as $admin) {
            // Remove existing _id if it exists
            unset($admin['_id']);
            
            // Check if admin already exists
            $existing = $adminsCollection->findOne(['email' => $admin['email']]);
            if (!$existing) {
                $adminsCollection->insertOne($admin);
                echo "  ✅ Added admin: " . $admin['name'] . "\n";
            } else {
                echo "  ⚠️  Admin already exists: " . $admin['name'] . "\n";
            }
        }
    }
    
    echo "\n🎉 Migration completed successfully!\n";
    echo "📊 Database: glamour\n";
    echo "🌐 MongoDB URL: mongodb://localhost:27017/glamour\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "🔧 Please check:\n";
    echo "  1. MongoDB server is running\n";
    echo "  2. MongoDB PHP extension is installed\n";
    echo "  3. Composer dependencies are installed\n";
}
?>

