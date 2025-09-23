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
            case 'get_all_products':
                $subcategory = $input['subcategory'] ?? '';
                $filters = ['category' => "Kids' Clothing"];
                if ($subcategory) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                $products = $productModel->getAll($filters);
                $response = ['success' => true, 'products' => $products];
                break;
                
            case 'filter_products':
                $filters = [];
                $andConditions = [];
                
                // Get subcategory from URL or input
                $subcategory = $input['subcategory'] ?? '';
                
                // Base filter - only kids clothing products
                $filters['category'] = "Kids' Clothing";
                
                // Apply subcategory filter if provided (but only if no kids category filters are selected)
                if (!empty($subcategory) && (empty($input['kids_categories']) || !is_array($input['kids_categories']))) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                
                // Kids category filter
                if (!empty($input['kids_categories']) && is_array($input['kids_categories'])) {
                    $kidsCategoryFilters = [];
                    foreach ($input['kids_categories'] as $category) {
                        switch ($category) {
                            case 'shirts':
                                $kidsCategoryFilters[] = ['subcategory' => 'Boys'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Baby'];
                                break;
                            case 'dresses':
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Baby'];
                                break;
                            case 'pants':
                                $kidsCategoryFilters[] = ['subcategory' => 'Boys'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Baby'];
                                break;
                            case 'shorts':
                                $kidsCategoryFilters[] = ['subcategory' => 'Boys'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                break;
                            case 'skirts':
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                break;
                            case 'tops':
                                $kidsCategoryFilters[] = ['subcategory' => 'Boys'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Baby'];
                                break;
                            case 'accessories':
                                $kidsCategoryFilters[] = ['subcategory' => 'Boys'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Girls'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Toddlers'];
                                $kidsCategoryFilters[] = ['subcategory' => 'Baby'];
                                break;
                        }
                    }
                    if (!empty($kidsCategoryFilters)) {
                        $andConditions[] = ['$or' => $kidsCategoryFilters];
                    }
                }
                
                // Gender filter
                if (!empty($input['genders']) && is_array($input['genders'])) {
                    $andConditions[] = ['gender' => ['$in' => $input['genders']]];
                }
                
                // Age group filter
                if (!empty($input['age_groups']) && is_array($input['age_groups'])) {
                    $ageGroupFilters = [];
                    foreach ($input['age_groups'] as $ageGroup) {
                        switch ($ageGroup) {
                            case '2-4':
                                $ageGroupFilters[] = ['age_range' => '2-4'];
                                $ageGroupFilters[] = ['age_range' => '2-4 years'];
                                break;
                            case '4-6':
                                $ageGroupFilters[] = ['age_range' => '4-6'];
                                $ageGroupFilters[] = ['age_range' => '4-6 years'];
                                break;
                            case '6-8':
                                $ageGroupFilters[] = ['age_range' => '6-8'];
                                $ageGroupFilters[] = ['age_range' => '6-8 years'];
                                break;
                            case '8-10':
                                $ageGroupFilters[] = ['age_range' => '8-10'];
                                $ageGroupFilters[] = ['age_range' => '8-10 years'];
                                break;
                            case '10-12':
                                $ageGroupFilters[] = ['age_range' => '10-12'];
                                $ageGroupFilters[] = ['age_range' => '10-12 years'];
                                break;
                            case '12-14':
                                $ageGroupFilters[] = ['age_range' => '12-14'];
                                $ageGroupFilters[] = ['age_range' => '12-14 years'];
                                break;
                        }
                    }
                    if (!empty($ageGroupFilters)) {
                        $andConditions[] = ['$or' => $ageGroupFilters];
                    }
                }
                
                // Brand filter
                if (!empty($input['brands']) && is_array($input['brands'])) {
                    $andConditions[] = ['brand' => ['$in' => $input['brands']]];
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
                            case '0-10':
                                $priceFilters[] = ['price' => ['$gte' => 0, '$lte' => 10]];
                                break;
                            case '10-20':
                                $priceFilters[] = ['price' => ['$gte' => 10, '$lte' => 20]];
                                break;
                            case '20-30':
                                $priceFilters[] = ['price' => ['$gte' => 20, '$lte' => 30]];
                                break;
                            case '30-40':
                                $priceFilters[] = ['price' => ['$gte' => 30, '$lte' => 40]];
                                break;
                            case '40+':
                                $priceFilters[] = ['price' => ['$gte' => 40]];
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
                    'message' => count($processedProducts) . ' kids clothing products found'
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