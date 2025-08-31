<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

// Get all products with old static paths
$oldPathProducts = $productModel->getAll([
    '$or' => [
        ['front_image' => ['$regex' => '^img/']],
        ['back_image' => ['$regex' => '^img/']],
        ['color_variants.front_image' => ['$regex' => '^img/']],
        ['color_variants.back_image' => ['$regex' => '^img/']]
    ]
]);

$migrationResults = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'migrate') {
        $migratedCount = 0;
        $errors = [];
        
        foreach ($oldPathProducts as $product) {
            $updates = [];
            $productId = $product['_id'];
            
            // Check front image
            if (!empty($product['front_image']) && strpos($product['front_image'], 'img/') === 0) {
                $filename = basename($product['front_image']);
                $newPath = 'uploads/products/' . $filename;
                
                // Verify file exists in uploads/products/
                if (file_exists('../uploads/products/' . $filename)) {
                    $updates['front_image'] = $newPath;
                } else {
                    $errors[] = "File not found for {$product['name']}: {$filename}";
                }
            }
            
            // Check back image
            if (!empty($product['back_image']) && strpos($product['back_image'], 'img/') === 0) {
                $filename = basename($product['back_image']);
                $newPath = 'uploads/products/' . $filename;
                
                if (file_exists('../uploads/products/' . $filename)) {
                    $updates['back_image'] = $newPath;
                } else {
                    $errors[] = "File not found for {$product['name']}: {$filename}";
                }
            }
            
            // Check color variants
            if (!empty($product['color_variants'])) {
                $colorVariants = $product['color_variants'];
                $updatedVariants = false;
                
                foreach ($colorVariants as $index => $variant) {
                    $variantUpdates = [];
                    
                    if (!empty($variant['front_image']) && strpos($variant['front_image'], 'img/') === 0) {
                        $filename = basename($variant['front_image']);
                        $newPath = 'uploads/products/' . $filename;
                        
                        if (file_exists('../uploads/products/' . $filename)) {
                            $variantUpdates['front_image'] = $newPath;
                            $updatedVariants = true;
                        } else {
                            $errors[] = "File not found for {$product['name']} variant {$index}: {$filename}";
                        }
                    }
                    
                    if (!empty($variant['back_image']) && strpos($variant['back_image'], 'img/') === 0) {
                        $filename = basename($variant['back_image']);
                        $newPath = 'uploads/products/' . $filename;
                        
                        if (file_exists('../uploads/products/' . $filename)) {
                            $variantUpdates['back_image'] = $newPath;
                            $updatedVariants = true;
                        } else {
                            $errors[] = "File not found for {$product['name']} variant {$index}: {$filename}";
                        }
                    }
                    
                    if (!empty($variantUpdates)) {
                        $colorVariants[$index] = array_merge($variant, $variantUpdates);
                    }
                }
                
                if ($updatedVariants) {
                    $updates['color_variants'] = $colorVariants;
                }
            }
            
            // Update product if there are changes
            if (!empty($updates)) {
                if ($productModel->update($productId, $updates)) {
                    $migratedCount++;
                    $migrationResults[] = "✅ Updated: {$product['name']}";
                } else {
                    $errors[] = "Failed to update {$product['name']}";
                }
            }
        }
        
        $migrationResults[] = "Migration completed! {$migratedCount} products updated.";
        if (!empty($errors)) {
            $migrationResults[] = "Errors: " . implode(', ', $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrate Image Paths - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .migration-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF6B9D 0%, #FF8E9E 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #6C757D;
            color: white;
        }
        
        .results {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .product-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background: white;
        }
        
        .product-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Migrate Image Paths</h1>
            <p class="page-subtitle">Convert old static image paths to the new upload system</p>
        </div>
        
        <div class="migration-section">
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Warning:</strong> This will update your database to convert old static image paths (img/...) to the new upload system paths (uploads/products/...). 
                Make sure you have a backup before proceeding.
            </div>
            
            <h3>Products with Old Static Paths: <?php echo count($oldPathProducts); ?></h3>
            
            <?php if (!empty($oldPathProducts)): ?>
                <div class="product-list">
                    <?php foreach ($oldPathProducts as $product): ?>
                        <div class="product-item">
                            <div>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                <br>
                                <small>
                                    <?php 
                                    $oldPaths = [];
                                    if (!empty($product['front_image']) && strpos($product['front_image'], 'img/') === 0) {
                                        $oldPaths[] = 'Front: ' . $product['front_image'];
                                    }
                                    if (!empty($product['back_image']) && strpos($product['back_image'], 'img/') === 0) {
                                        $oldPaths[] = 'Back: ' . $product['back_image'];
                                    }
                                    if (!empty($product['color_variants'])) {
                                        foreach ($product['color_variants'] as $variant) {
                                            if (!empty($variant['front_image']) && strpos($variant['front_image'], 'img/') === 0) {
                                                $oldPaths[] = 'Variant Front: ' . $variant['front_image'];
                                            }
                                            if (!empty($variant['back_image']) && strpos($variant['back_image'], 'img/') === 0) {
                                                $oldPaths[] = 'Variant Back: ' . $variant['back_image'];
                                            }
                                        }
                                    }
                                    echo implode(', ', $oldPaths);
                                    ?>
                                </small>
                            </div>
                            <div>
                                <small>ID: <?php echo $product['_id']; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <form method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="migrate">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to migrate these image paths? This will update your database.')">
                        <i class="fas fa-sync-alt"></i> Migrate All Paths
                    </button>
                </form>
            <?php else: ?>
                <p>No products with old static paths found. All products are using the new upload system!</p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($migrationResults)): ?>
            <div class="migration-section">
                <h3>Migration Results</h3>
                <div class="results">
                    <?php foreach ($migrationResults as $result): ?>
                        <p><?php echo htmlspecialchars($result); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="view-products.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to View Products
            </a>
        </div>
    </div>
</body>
</html>
