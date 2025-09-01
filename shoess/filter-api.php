<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../config/mongodb.php';
require_once '../models/Product.php';

$productModel = new Product();

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    // Get POST data (handle both JSON and form data)
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {
        switch ($input['action']) {
            case 'filter_products':
                $filters = [];
                $andConditions = [];
                
                // Get subcategory from URL or input
                $subcategory = $input['subcategory'] ?? '';
                
                // Base filter - only shoe products
                $filters['category'] = "Shoes";
                
                // Apply subcategory filter if provided (but only if no category filters are selected)
                if (!empty($subcategory) && (empty($input['categories']) || !is_array($input['categories']))) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                
                // Size filter
                if (!empty($input['sizes']) && is_array($input['sizes'])) {
                    $sizeFilters = [];
                    foreach ($input['sizes'] as $size) {
                        // Check if the size exists in the sizes array field
                        $sizeFilters[] = ['sizes' => ['$elemMatch' => ['$eq' => $size]]];
                        // Check selected_sizes field (JSON array)
                        $sizeFilters[] = ['selected_sizes' => new MongoDB\BSON\Regex('"' . preg_quote($size, '/') . '"', 'i')];
                        // Also check size_category field
                        $sizeFilters[] = ['size_category' => $size];
                    }
                    $andConditions[] = ['$or' => $sizeFilters];
                }
                
                // Color filter
                if (!empty($input['colors']) && is_array($input['colors'])) {
                    $andConditions[] = [
                        '$or' => [
                            ['color' => ['$in' => $input['colors']]],
                            ['color_variants.color' => ['$in' => $input['colors']]]
                        ]
                    ];
                }
                
                // Price filter
                if (!empty($input['price_ranges']) && is_array($input['price_ranges'])) {
                    $priceFilters = [];
                    foreach ($input['price_ranges'] as $range) {
                        switch ($range) {
                            case 'on-sale':
                                $priceFilters[] = ['sale' => true];
                                break;
                            case '0-25':
                                $priceFilters[] = ['price' => ['$gte' => 0, '$lte' => 25]];
                                break;
                            case '25-50':
                                $priceFilters[] = ['price' => ['$gte' => 25, '$lte' => 50]];
                                break;
                            case '50-75':
                                $priceFilters[] = ['price' => ['$gte' => 50, '$lte' => 75]];
                                break;
                            case '75-100':
                                $priceFilters[] = ['price' => ['$gte' => 75, '$lte' => 100]];
                                break;
                            case '100+':
                                $priceFilters[] = ['price' => ['$gte' => 100]];
                                break;
                        }
                    }
                    if (!empty($priceFilters)) {
                        $andConditions[] = ['$or' => $priceFilters];
                    }
                }
                
                // Category filter (subcategories)
                if (!empty($input['categories']) && is_array($input['categories'])) {
                    $andConditions[] = ['subcategory' => ['$in' => array_map('ucfirst', $input['categories'])]];
                }
                
                // Dress length filter
                if (!empty($input['lengths']) && is_array($input['lengths'])) {
                    $lengthFilters = [];
                    foreach ($input['lengths'] as $length) {
                        $lengthFilters[] = new MongoDB\BSON\Regex($length, 'i');
                    }
                    $andConditions[] = [
                        '$or' => [
                            ['description' => ['$in' => $lengthFilters]],
                            ['name' => ['$in' => $lengthFilters]]
                        ]
                    ];
                }
                
                // Combine all conditions
                if (!empty($andConditions)) {
                    $filters['$and'] = $andConditions;
                }
                
                // Get products with filters
                $products = $productModel->getAll($filters, ['createdAt' => -1]);
                
                // Process products for frontend
                $processedProducts = [];
                foreach ($products as $product) {
                    $processedProduct = [
                        'id' => (string)$product['_id'],
                        'name' => $product['name'] ?? '',
                        'price' => $product['price'] ?? 0,
                        'color' => $product['color'] ?? '',
                        'category' => $product['category'] ?? '',
                        'subcategory' => $product['subcategory'] ?? '',
                        'description' => $product['description'] ?? '',
                        'featured' => $product['featured'] ?? false,
                        'sale' => $product['sale'] ?? false,
                        'salePrice' => $product['salePrice'] ?? null,
                        'available' => $product['available'] ?? true,
                        'stock' => $product['stock'] ?? 0,
                        'front_image' => $product['front_image'] ?? '',
                        'back_image' => $product['back_image'] ?? '',
                        'color_variants' => $product['color_variants'] ?? [],
                        'sizes' => $product['sizes'] ?? [],
                        'selected_sizes' => $product['selected_sizes'] ?? ''
                    ];
                    
                    $processedProducts[] = $processedProduct;
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Products filtered successfully',
                    'data' => [
                        'products' => $processedProducts,
                        'total_count' => count($processedProducts),
                        'filters_applied' => $filters
                    ]
                ];
                break;
                
            case 'get_filter_options':
                // Get all shoe products to extract filter options
                $allProducts = $productModel->getByCategory("Shoes");
                
                $filterOptions = [
                    'sizes' => [],
                    'colors' => [],
                    'categories' => [],
                    'price_ranges' => [
                        'on-sale' => 0,
                        '0-25' => 0,
                        '25-50' => 0,
                        '50-75' => 0,
                        '75-100' => 0,
                        '100+' => 0
                    ]
                ];
                
                foreach ($allProducts as $product) {
                    // Extract sizes
                    if (!empty($product['sizes']) && is_array($product['sizes'])) {
                        foreach ($product['sizes'] as $size) {
                            if (!in_array($size, $filterOptions['sizes'])) {
                                $filterOptions['sizes'][] = $size;
                            }
                        }
                    }
                    
                    // Extract colors
                    if (!empty($product['color'])) {
                        if (!in_array($product['color'], $filterOptions['colors'])) {
                            $filterOptions['colors'][] = $product['color'];
                        }
                    }
                    
                    if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
                        foreach ($product['color_variants'] as $variant) {
                            if (!empty($variant['color']) && !in_array($variant['color'], $filterOptions['colors'])) {
                                $filterOptions['colors'][] = $variant['color'];
                            }
                        }
                    }
                    
                    // Extract categories (subcategories)
                    if (!empty($product['subcategory'])) {
                        if (!in_array($product['subcategory'], $filterOptions['categories'])) {
                            $filterOptions['categories'][] = $product['subcategory'];
                        }
                    }
                    
                    // Count price ranges
                    $price = $product['price'] ?? 0;
                    if ($product['sale'] ?? false) {
                        $filterOptions['price_ranges']['on-sale']++;
                    }
                    if ($price >= 0 && $price <= 25) {
                        $filterOptions['price_ranges']['0-25']++;
                    } elseif ($price > 25 && $price <= 50) {
                        $filterOptions['price_ranges']['25-50']++;
                    } elseif ($price > 50 && $price <= 75) {
                        $filterOptions['price_ranges']['50-75']++;
                    } elseif ($price > 75 && $price <= 100) {
                        $filterOptions['price_ranges']['75-100']++;
                    } elseif ($price > 100) {
                        $filterOptions['price_ranges']['100+']++;
                    }
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Filter options retrieved successfully',
                    'data' => $filterOptions
                ];
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Invalid request method or missing action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Send JSON response
echo json_encode($response);
exit();
?>
