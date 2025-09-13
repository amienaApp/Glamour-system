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
require_once '../config1/mongodb.php';
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
                
                // Base filter - only beauty products
                $filters['category'] = "Beauty & Cosmetics";
                
                // Apply subcategory filter if provided (but only if no beauty category filters are selected)
                if (!empty($subcategory) && (empty($input['beauty_categories']) || !is_array($input['beauty_categories']))) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                
                // Beauty category filter
                if (!empty($input['beauty_categories']) && is_array($input['beauty_categories'])) {
                    $beautyCategoryFilters = [];
                    foreach ($input['beauty_categories'] as $category) {
                        switch ($category) {
                            case 'makeup':
                                $beautyCategoryFilters[] = ['subcategory' => 'Makeup'];
                                break;
                            case 'skincare':
                                $beautyCategoryFilters[] = ['subcategory' => 'Skincare'];
                                break;
                            case 'hair':
                                $beautyCategoryFilters[] = ['subcategory' => 'Hair Care'];
                                break;
                            case 'bath-body':
                                $beautyCategoryFilters[] = ['subcategory' => 'Bath & Body'];
                                break;
                            case 'tools':
                                $beautyCategoryFilters[] = ['subcategory' => 'Tools'];
                                break;
                        }
                    }
                    if (!empty($beautyCategoryFilters)) {
                        $andConditions[] = ['$or' => $beautyCategoryFilters];
                    }
                }
                
                // Beauty type filter (replaces makeup_types)
                if (!empty($input['beauty_types']) && is_array($input['beauty_types'])) {
                    $beautyTypeFilters = [];
                    foreach ($input['beauty_types'] as $type) {
                        $beautyTypeFilters[] = new MongoDB\BSON\Regex($type, 'i');
                    }
                    $andConditions[] = [
                        '$or' => [
                            ['sub_subcategory' => ['$in' => $beautyTypeFilters]],
                            ['deeper_sub_subcategory' => ['$in' => $beautyTypeFilters]],
                            ['description' => ['$in' => $beautyTypeFilters]],
                            ['name' => ['$in' => $beautyTypeFilters]]
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
                            case '0-15':
                                $priceFilters[] = ['price' => ['$gte' => 0, '$lte' => 15]];
                                break;
                            case '15-30':
                                $priceFilters[] = ['price' => ['$gte' => 15, '$lte' => 30]];
                                break;
                            case '30-50':
                                $priceFilters[] = ['price' => ['$gte' => 30, '$lte' => 50]];
                                break;
                            case '50-75':
                                $priceFilters[] = ['price' => ['$gte' => 50, '$lte' => 75]];
                                break;
                            case '75+':
                                $priceFilters[] = ['price' => ['$gte' => 75]];
                                break;
                        }
                    }
                    if (!empty($priceFilters)) {
                        $andConditions[] = ['$or' => $priceFilters];
                    }
                }
                
                
                // Sub-subcategory filter
                if (!empty($input['sub_subcategories']) && is_array($input['sub_subcategories'])) {
                    $andConditions[] = ['sub_subcategory' => ['$in' => $input['sub_subcategories']]];
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
                    $processedProducts[] = [
                        'id' => (string)$product['_id'],
                        'name' => $product['name'] ?? '',
                        'price' => floatval($product['price'] ?? 0),
                        'sale_price' => floatval($product['sale_price'] ?? 0),
                        'sale' => $product['sale'] ?? false,
                        'on_sale' => $product['on_sale'] ?? false,
                        'featured' => $product['featured'] ?? false,
                        'available' => $product['available'] ?? true,
                        'stock' => intval($product['stock'] ?? 0),
                        'category' => $product['category'] ?? '',
                        'subcategory' => $product['subcategory'] ?? '',
                        'sub_subcategory' => $product['sub_subcategory'] ?? '',
                        'color' => $product['color'] ?? '',
                        'front_image' => $product['front_image'] ?? $product['image_front'] ?? '',
                        'back_image' => $product['back_image'] ?? $product['image_back'] ?? '',
                        'color_variants' => $product['color_variants'] ?? [],
                        'description' => $product['description'] ?? ''
                    ];
                }
                
                $response = [
                    'success' => true,
                    'data' => [
                        'products' => $processedProducts,
                        'total_count' => count($processedProducts),
                        'filters_applied' => $input
                    ],
                    'message' => count($processedProducts) . ' beauty products found'
                ];
                break;
                
            default:
                $response['message'] = 'Invalid action';
                break;
        }
    } else {
        $response['message'] = 'Invalid request method or missing action';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
?>