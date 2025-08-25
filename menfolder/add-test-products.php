<?php
/**
 * Add Test Products Script
 * This script adds sample men's clothing products to test the filtering functionality
 */

require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

// Sample test products
$testProducts = [
    [
        'name' => 'Classic White Shirt',
        'price' => 45.99,
        'color' => '#ffffff',
        'category' => "Men's Clothing",
        'subcategory' => 'Shirts',
        'brand' => 'levis',
        'style' => 'formal',
        'material' => 'cotton',
        'fit' => 'regular',
        'stock' => 25,
        'sizes' => ['S', 'M', 'L', 'XL'],
        'front_image' => 'img/men/shirts/shirt1.jpg',
        'back_image' => 'img/men/shirts/shirt2.1.avif',
        'description' => 'Classic white formal shirt for business',
        'featured' => true,
        'sale' => false,
        'available' => true
    ],
    [
        'name' => 'Blue Denim Shirt',
        'price' => 55.99,
        'color' => '#0066cc',
        'category' => "Men's Clothing",
        'subcategory' => 'Shirts',
        'brand' => 'levis',
        'style' => 'casual',
        'material' => 'denim',
        'fit' => 'regular',
        'stock' => 15,
        'sizes' => ['M', 'L', 'XL', 'XXL'],
        'front_image' => 'img/men/shirts/10.0.avif',
        'back_image' => 'img/men/shirts/10.1.0.avif',
        'description' => 'Casual blue denim shirt',
        'featured' => false,
        'sale' => true,
        'salePrice' => 45.99,
        'available' => true
    ],
    [
        'name' => 'Black T-Shirt',
        'price' => 25.99,
        'color' => '#000000',
        'category' => "Men's Clothing",
        'subcategory' => 'T-Shirts',
        'brand' => 'nike',
        'style' => 'casual',
        'material' => 'cotton',
        'fit' => 'slim',
        'stock' => 30,
        'sizes' => ['S', 'M', 'L', 'XL'],
        'front_image' => 'img/men/t-shirts/1.2.jpg',
        'back_image' => 'img/men/t-shirts/1.3.jpg',
        'description' => 'Comfortable black t-shirt',
        'featured' => true,
        'sale' => false,
        'available' => true
    ],
    [
        'name' => 'Gray Hoodie',
        'price' => 75.99,
        'color' => '#808080',
        'category' => "Men's Clothing",
        'subcategory' => 'Hoodies',
        'brand' => 'adidas',
        'style' => 'casual',
        'material' => 'cotton',
        'fit' => 'loose',
        'stock' => 20,
        'sizes' => ['M', 'L', 'XL', 'XXL'],
        'front_image' => 'img/men/hoodie$sweatshirt/1.jpg',
        'back_image' => 'img/men/hoodie$sweatshirt/12.0.jpeg',
        'description' => 'Warm gray hoodie for casual wear',
        'featured' => false,
        'sale' => false,
        'available' => true
    ],
    [
        'name' => 'Black Formal Suit',
        'price' => 299.99,
        'color' => '#000000',
        'category' => "Men's Clothing",
        'subcategory' => 'Suits',
        'brand' => 'calvin-klein',
        'style' => 'formal',
        'material' => 'wool',
        'fit' => 'slim',
        'stock' => 8,
        'sizes' => ['S', 'M', 'L'],
        'front_image' => 'img/men/suits/1.1.avif',
        'back_image' => 'img/men/suits/1.2.avif',
        'description' => 'Elegant black formal suit',
        'featured' => true,
        'sale' => false,
        'available' => true
    ],
    [
        'name' => 'Blue Jeans',
        'price' => 89.99,
        'color' => '#0066cc',
        'category' => "Men's Clothing",
        'subcategory' => 'Pants',
        'brand' => 'levis',
        'style' => 'casual',
        'material' => 'denim',
        'fit' => 'regular',
        'stock' => 18,
        'sizes' => ['30', '32', '34', '36'],
        'front_image' => 'img/men/pants/1.0.avif',
        'back_image' => 'img/men/pants/1.1.0.avif',
        'description' => 'Classic blue jeans',
        'featured' => false,
        'sale' => true,
        'salePrice' => 69.99,
        'available' => true
    ],
    [
        'name' => 'White Polo Shirt',
        'price' => 35.99,
        'color' => '#ffffff',
        'category' => "Men's Clothing",
        'subcategory' => 'Shirts',
        'brand' => 'puma',
        'style' => 'casual',
        'material' => 'cotton',
        'fit' => 'regular',
        'stock' => 22,
        'sizes' => ['S', 'M', 'L', 'XL'],
        'front_image' => 'img/men/shirts/10.1.avif',
        'back_image' => 'img/men/shirts/10.1.avif',
        'description' => 'Comfortable white polo shirt',
        'featured' => false,
        'sale' => false,
        'available' => true
    ],
    [
        'name' => 'Red Sport Shirt',
        'price' => 65.99,
        'color' => '#812d2d',
        'category' => "Men's Clothing",
        'subcategory' => 'Shirts',
        'brand' => 'under-armour',
        'style' => 'sporty',
        'material' => 'polyester',
        'fit' => 'slim',
        'stock' => 12,
        'sizes' => ['S', 'M', 'L'],
        'front_image' => 'img/men/shirts/12.0.jpeg',
        'back_image' => 'img/men/shirts/12.1.1.webp',
        'description' => 'Performance red sport shirt',
        'featured' => true,
        'sale' => false,
        'available' => true
    ]
];

echo "Adding test products to database...\n";

$addedCount = 0;
$existingCount = 0;

foreach ($testProducts as $product) {
    // Check if product already exists
    $existing = $productModel->getByNameAndSubcategory($product['name'], $product['subcategory']);
    
    if (!$existing) {
        $result = $productModel->create($product);
        if ($result) {
            echo "✓ Added: {$product['name']} ({$product['subcategory']})\n";
            $addedCount++;
        } else {
            echo "✗ Failed to add: {$product['name']}\n";
        }
    } else {
        echo "- Already exists: {$product['name']} ({$product['subcategory']})\n";
        $existingCount++;
    }
}

echo "\nSummary:\n";
echo "Added: $addedCount products\n";
echo "Already existed: $existingCount products\n";
echo "Total processed: " . count($testProducts) . " products\n";

echo "\nTest products are now available for filtering!\n";
echo "You can test the filtering by visiting the men's clothing page.\n";
?>
