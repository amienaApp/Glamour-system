<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config1/mongodb.php';
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
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'migrate') {
        $migratedCount = 0;
        $movedFiles = 0;
        
        // First, move all images from img/ to uploads/products/
        $imgDir = '../img/';
        $uploadsDir = '../uploads/products/';
        
        if (is_dir($imgDir)) {
            $migrationResults[] = "📁 Starting image migration from {$imgDir} to {$uploadsDir}";
            
            // Recursively get all image files from img/ directory
            $imageFiles = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($imgDir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'])) {
                    $relativePath = str_replace($imgDir, '', $file->getPathname());
                    $imageFiles[] = $relativePath;
                }
            }
            
            $migrationResults[] = "📊 Found " . count($imageFiles) . " image files to migrate";
            
            // Move each image file
            foreach ($imageFiles as $imagePath) {
                $sourcePath = $imgDir . $imagePath;
                $destPath = $uploadsDir . basename($imagePath);
                
                // Handle filename conflicts by adding a prefix
                $counter = 1;
                $originalDestPath = $destPath;
                while (file_exists($destPath)) {
                    $pathInfo = pathinfo($originalDestPath);
                    $destPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $counter . '.' . $pathInfo['extension'];
                    $counter++;
                }
                
                if (copy($sourcePath, $destPath)) {
                    $movedFiles++;
                    $migrationResults[] = "✅ Moved: {$imagePath} → " . basename($destPath);
                } else {
                    $errors[] = "❌ Failed to move: {$imagePath}";
                }
            }
            
            $migrationResults[] = "📦 Successfully moved {$movedFiles} image files";
        } else {
            $errors[] = "❌ Source directory {$imgDir} not found";
        }
        
        // Now update the database paths
        foreach ($oldPathProducts as $product) {
            $updates = [];
            $productId = $product['_id'];
            
            // Check front image
            if (!empty($product['front_image']) && strpos($product['front_image'], 'img/') === 0) {
                $filename = basename($product['front_image']);
                $newPath = 'uploads/products/' . $filename;
                $updates['front_image'] = $newPath;
            }
            
            // Check back image
            if (!empty($product['back_image']) && strpos($product['back_image'], 'img/') === 0) {
                $filename = basename($product['back_image']);
                $newPath = 'uploads/products/' . $filename;
                $updates['back_image'] = $newPath;
            }
            
            // Check color variants
            if (!empty($product['color_variants'])) {
                $colorVariants = $product['color_variants'];
                $updatedVariants = false;
                
                                 foreach ($colorVariants as $index => $variant) {
                     $variantUpdates = [];
                     
                     // Convert BSON document to array if needed
                     $variantArray = is_object($variant) ? (array)$variant : $variant;
                     
                     if (!empty($variantArray['front_image']) && strpos($variantArray['front_image'], 'img/') === 0) {
                         $filename = basename($variantArray['front_image']);
                         $newPath = 'uploads/products/' . $filename;
                         $variantUpdates['front_image'] = $newPath;
                         $updatedVariants = true;
                     }
                     
                     if (!empty($variantArray['back_image']) && strpos($variantArray['back_image'], 'img/') === 0) {
                         $filename = basename($variantArray['back_image']);
                         $newPath = 'uploads/products/' . $filename;
                         $variantUpdates['back_image'] = $newPath;
                         $updatedVariants = true;
                     }
                     
                     if (!empty($variantUpdates)) {
                         $colorVariants[$index] = array_merge($variantArray, $variantUpdates);
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
                    $migrationResults[] = "✅ Updated database: {$product['name']}";
                } else {
                    $errors[] = "❌ Failed to update database for: {$product['name']}";
                }
            }
        }
        
        $migrationResults[] = "🎉 Migration completed! {$migratedCount} products updated, {$movedFiles} files moved.";
        
        // Optionally remove the old img/ directory
        if (isset($_POST['remove_old_dir']) && $_POST['remove_old_dir'] === '1') {
            if (is_dir($imgDir)) {
                // Remove all files first
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($imgDir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($iterator as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getPathname());
                    } else {
                        unlink($file->getPathname());
                    }
                }
                
                if (rmdir($imgDir)) {
                    $migrationResults[] = "🗑️ Old img/ directory removed successfully";
                } else {
                    $migrationResults[] = "⚠️ Could not remove old img/ directory (may have permissions)";
                }
            }
        }
        
        if (!empty($errors)) {
            $migrationResults[] = "❌ Errors encountered: " . count($errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrate All Images - Glamour Admin</title>
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
        
        .page-title {
            font-size: 2.5rem;
            color: #3E2723;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        
        .page-subtitle {
            color: #8D6E63;
            font-size: 1.1rem;
            margin: 0;
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
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.4);
        }
        
        .btn-secondary {
            background: #6C757D;
            color: white;
        }
        
        .btn-danger {
            background: #DC3545;
            color: white;
        }
        
        .results {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
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
        
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .checkbox-group {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #495057;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Migrate All Images</h1>
            <p class="page-subtitle">Move all images from img/ directory to uploads/products/ and update database</p>
        </div>
        
        <div class="migration-section">
            <div class="info">
                <i class="fas fa-info-circle"></i>
                <strong>What this will do:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Move all images from <code>img/</code> directory to <code>uploads/products/</code></li>
                    <li>Update all database paths from <code>img/...</code> to <code>uploads/products/filename.ext</code></li>
                    <li>Consolidate all images into one system</li>
                    <li>Optionally remove the old <code>img/</code> directory</li>
                </ul>
            </div>
            
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Warning:</strong> This is a major operation that will:
                <ul style="margin: 10px 0 0 20px;">
                    <li>Move all your image files to a new location</li>
                    <li>Update your database with new image paths</li>
                    <li>Potentially remove the old img/ directory</li>
                </ul>
                <strong>Make sure you have a backup before proceeding!</strong>
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
                                             // Convert BSON document to array if needed
                                             $variantArray = is_object($variant) ? (array)$variant : $variant;
                                             
                                             if (!empty($variantArray['front_image']) && strpos($variantArray['front_image'], 'img/') === 0) {
                                                 $oldPaths[] = 'Variant Front: ' . $variantArray['front_image'];
                                             }
                                             if (!empty($variantArray['back_image']) && strpos($variantArray['back_image'], 'img/') === 0) {
                                                 $oldPaths[] = 'Variant Back: ' . $variantArray['back_image'];
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
                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="remove_old_dir" value="1">
                            Remove old img/ directory after migration (recommended)
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you absolutely sure you want to migrate all images? This will move files and update your database. Make sure you have a backup!')">
                        <i class="fas fa-sync-alt"></i> Start Complete Migration
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
                    
                    <?php if (!empty($errors)): ?>
                        <h4 style="color: #dc3545; margin-top: 20px;">Errors:</h4>
                        <?php foreach ($errors as $error): ?>
                            <p style="color: #dc3545;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
