<?php
// Universal Filter API for All Category Pages
// This API handles filtering for all product categories

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
                
                // Determine category from the calling page
                $category = $this->detectCategory();
                
                // Base filter - set category
                $filters['category'] = $category;
                
                // Get subcategory from URL or input
                $subcategory = $input['subcategory'] ?? '';
                
                // Apply subcategory filter if provided (but only if no category filters are selected)
                if (!empty($subcategory) && (empty($input['categories']) || !is_array($input['categories']))) {
                    $filters['subcategory'] = ucfirst($subcategory);
                }
                
                // Gender filter
                if (!empty($input['gender'])) {
                    $filters['gender'] = $input['gender'];
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
                    // Define color groups - map color names to hex codes
                    $colorGroups = [
                        'black' => ['#000000', '#181a1a', '#0a0a0a', '#111218', '#1a1a1a', '#333333', '#2c2c2c'],
                        'beige' => ['#e1c9c9', '#f5f5dc', '#f0e68c', '#d2b48c', '#deb887', '#f4a460', '#b38f65'],
                        'blue' => ['#414c61', '#0066cc', '#0000ff', '#4169e1', '#1e90ff', '#00bfff', '#87ceeb', '#4682b4', '#5f9ea0'],
                        'brown' => ['#8b4f33', '#5d3c3c', '#a52a2a', '#d2691e', '#cd853f', '#bc8f8f', '#d2b48c', '#deb887', '#f4a460'],
                        'gold' => ['#ffd700', '#ffb347', '#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500'],
                        'green' => ['#82ff4d', '#228b22', '#32cd32', '#00ff00', '#008000', '#00ff7f', '#7fff00', '#adff2f', '#9acd32'],
                        'grey' => ['#575759', '#4a4142', '#808080', '#a9a9a9', '#c0c0c0', '#d3d3d3', '#dcdcdc', '#f5f5f5', '#696969', '#778899'],
                        'orange' => ['#ffa500', '#ff8c00', '#ff7f50', '#ff6347', '#ff4500', '#ffd700', '#ffb347'],
                        'pink' => ['#ffc0cb', '#ff69b4', '#ff1493', '#dc143c', '#ffb6c1', '#ffa0b4', '#ff91a4'],
                        'purple' => ['#373645', '#800080', '#4b0082', '#6a5acd', '#8a2be2', '#9932cc', '#ba55d3', '#da70d6'],
                        'red' => ['#5a2b34', '#ff0000', '#dc143c', '#b22222', '#8b0000', '#ff6347', '#ff4500', '#ff1493', '#c71585'],
                        'silver' => ['#c0c0c0', '#d3d3d3', '#a9a9a9', '#dcdcdc', '#f5f5f5', '#e6e6fa', '#f0f8ff'],
                        'taupe' => ['#b38f65', '#483c32', '#8b7355', '#a0956b', '#d2b48c', '#deb887', '#f4a460', '#cd853f'],
                        'white' => ['#ffffff', '#fff', '#f5f5f5', '#fafafa', '#f8f8ff', '#f0f8ff', '#e6e6fa', '#fff8dc'],
                        'yellow' => ['#ffff00', '#ffd700', '#ffeb3b', '#ffc107', '#ffa000', '#ff8f00', '#ff6f00', '#ffea00']
                    ];
                    
                    $colorFilters = [];
                    foreach ($input['colors'] as $colorName) {
                        if (isset($colorGroups[$colorName])) {
                            // Use the color group hex codes
                            $colorFilters[] = ['color' => ['$in' => $colorGroups[$colorName]]];
                            $colorFilters[] = ['color_variants.color' => ['$in' => $colorGroups[$colorName]]];
                        } else {
                            // Use the color name directly
                            $colorFilters[] = ['color' => $colorName];
                            $colorFilters[] = ['color_variants.color' => $colorName];
                        }
                    }
                    
                    if (!empty($colorFilters)) {
                        $andConditions[] = ['$or' => $colorFilters];
                    }
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
                            case '200-400':
                                $priceFilters[] = ['price' => ['$gte' => 200, '$lte' => 400]];
                                break;
                            case '400+':
                                $priceFilters[] = ['price' => ['$gte' => 400]];
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
                
                // Dress length filter (for clothing categories)
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
                // Get all products for the current category to extract filter options
                $category = $this->detectCategory();
                $allProducts = $productModel->getByCategory($category);
                
                $filterOptions = [
                    'sizes' => [],
                    'colors' => [],
                    'categories' => [],
                    'price_ranges' => [
                        'on-sale' => 0,
                        '0-100' => 0,
                        '100-200' => 0,
                        '200-400' => 0,
                        '400+' => 0
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
                    if ($price >= 0 && $price <= 100) {
                        $filterOptions['price_ranges']['0-100']++;
                    } elseif ($price > 100 && $price <= 200) {
                        $filterOptions['price_ranges']['100-200']++;
                    } elseif ($price > 200 && $price <= 400) {
                        $filterOptions['price_ranges']['200-400']++;
                    } elseif ($price > 400) {
                        $filterOptions['price_ranges']['400+']++;
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

// Function to detect category from the calling page
function detectCategory() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $path = $_SERVER['REQUEST_URI'] ?? '';
    
    if (strpos($referer, '/bagsfolder/') !== false || strpos($path, '/bagsfolder/') !== false) {
        return 'Bags';
    } elseif (strpos($referer, '/beautyfolder/') !== false || strpos($path, '/beautyfolder/') !== false) {
        return 'Beauty & Cosmetics';
    } elseif (strpos($referer, '/shoess/') !== false || strpos($path, '/shoess/') !== false) {
        return 'Shoes';
    } elseif (strpos($referer, '/menfolder/') !== false || strpos($path, '/menfolder/') !== false) {
        return 'Men';
    } elseif (strpos($referer, '/kidsfolder/') !== false || strpos($path, '/kidsfolder/') !== false) {
        return 'Kids';
    } elseif (strpos($referer, '/womenF/') !== false || strpos($path, '/womenF/') !== false) {
        return 'Women';
    } elseif (strpos($referer, '/perfumes/') !== false || strpos($path, '/perfumes/') !== false) {
        return 'Perfumes';
    } elseif (strpos($referer, '/homedecor/') !== false || strpos($path, '/homedecor/') !== false) {
        return 'Home Decoration';
    } elseif (strpos($referer, '/accessories/') !== false || strpos($path, '/accessories/') !== false) {
        return 'Accessories';
    } else {
        return 'Products'; // Default fallback
    }
}

// Send JSON response
echo json_encode($response);
exit();
?>

