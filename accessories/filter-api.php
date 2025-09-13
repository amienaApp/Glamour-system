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
                
                // Base filter - only accessory products
                $filters['category'] = "Accessories";
                
                // Apply subcategory filter if provided (but only if no category filters are selected)
                if (!empty($subcategory) && (empty($input['categories']) || !is_array($input['categories']))) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                
                // Size filter (for accessories)
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
                
                // Color filter with dynamic color mapping
                if (!empty($input['colors']) && is_array($input['colors'])) {
                    // Get dynamic color groups from the database
                    $colorGroupsResponse = file_get_contents('get-colors-api.php');
                    $colorGroupsData = json_decode($colorGroupsResponse, true);
                    
                    if ($colorGroupsData && $colorGroupsData['success']) {
                        $colorGroups = $colorGroupsData['data']['colorGroups'];
                    } else {
                        // Fallback to static color groups if API fails
                        $colorGroups = [
                            'black' => ['#000000', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c', '#2f2a2c', '#0d0f0d', '#1b1b1e', '#222222', '#202020', '#1e1e1e', '#0b0706', '#0f0f10', '#2b2a2d', 'black', 'Black', 'BLACK', 'Black', 'Noir', 'Schwarz'],
                            'beige' => ['#dac0b4', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65', '#e6e8c0', '#d3d4d9', '#c2c2c6', 'beige', 'Beige', 'BEIGE', 'Beige', 'Beige', 'Camel', 'Tan'],
                            'blue' => ['#0a1e3b', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0', '#597783', '#29566b', '#2f4558', '#5c7a7a', '#667eea', 'blue', 'Blue', 'BLUE', 'Blue', 'Bleu', 'Azul'],
                            'brown' => ['#966345', '#8c5738', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460', '#382d29', '#62352b', '#bf8768', 'brown', 'Brown', 'BROWN', 'Brown', 'Brun', 'Marrón'],
                            'gold' => ['#f9d07f', '#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#c89b4c', 'gold', 'Gold', 'GOLD', 'Gold', 'Or', 'Dorado'],
                            'green' => ['#04613f', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32', 'green', 'Green', 'GREEN', 'Green', 'Vert', 'Verde'],
                            'grey' => ['#676b6e', '#6f725f', '#394647', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899', '#e6e7eb', '#dddddb', 'grey', 'gray', 'Grey', 'Gray', 'GREY', 'GRAY', 'Gris', 'Gris'],
                            'orange' => ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347', 'orange', 'Orange', 'ORANGE', 'Orange', 'Orange', 'Naranja'],
                            'pink' => ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4', 'pink', 'Pink', 'PINK', 'Pink', 'Rose', 'Rosa'],
                            'purple' => ['#63678f', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6', 'purple', 'Purple', 'PURPLE', 'Purple', 'Violet', 'Morado'],
                            'red' => ['#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585', 'red', 'Red', 'RED', 'Red', 'Rouge', 'Rojo'],
                            'silver' => ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff', 'silver', 'Silver', 'SILVER', 'Silver', 'Argent', 'Plata'],
                            'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc', 'white', 'White', 'WHITE', 'White', 'Blanc', 'Blanco']
                        ];
                    }
                    
                    // Get all color variations for the selected colors
                    $allColorVariations = [];
                    foreach ($input['colors'] as $colorName) {
                        if (isset($colorGroups[$colorName])) {
                            $allColorVariations = array_merge($allColorVariations, $colorGroups[$colorName]);
                        } else {
                            // If color not found in groups, add the original value
                            $allColorVariations[] = $colorName;
                        }
                    }
                    
                    // Remove duplicates
                    $allColorVariations = array_unique($allColorVariations);
                    
                    $andConditions[] = [
                        '$or' => [
                            ['color' => ['$in' => $allColorVariations]],
                            ['color_variants.color' => ['$in' => $allColorVariations]]
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
                            case '0-100':
                                $priceFilters[] = ['price' => ['$gte' => 0, '$lte' => 100]];
                                break;
                            case '100-200':
                                $priceFilters[] = ['price' => ['$gte' => 100, '$lte' => 200]];
                                break;
                            case '200-500':
                                $priceFilters[] = ['price' => ['$gte' => 200, '$lte' => 500]];
                                break;
                            case '500+':
                                $priceFilters[] = ['price' => ['$gte' => 500]];
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
                
                // Accessory type filter
                if (!empty($input['accessory_types']) && is_array($input['accessory_types'])) {
                    $typeFilters = [];
                    foreach ($input['accessory_types'] as $type) {
                        $typeFilters[] = new MongoDB\BSON\Regex($type, 'i');
                    }
                    $andConditions[] = [
                        '$or' => [
                            ['description' => ['$in' => $typeFilters]],
                            ['name' => ['$in' => $typeFilters]]
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
                // Get all accessory products to extract filter options
                $allProducts = $productModel->getByCategory("Accessories");
                
                $filterOptions = [
                    'sizes' => [],
                    'colors' => [],
                    'categories' => [],
                    'accessory_types' => [],
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

