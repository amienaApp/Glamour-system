<?php
/**
 * Perfumes API
 * Handles perfume-specific API requests
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config1/mongodb.php';
require_once 'models/Perfume.php';

$perfumeModel = new Perfume();
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'get_perfumes';
        
        switch ($action) {
            case 'get_perfumes':
                // Get query parameters
                $gender = $_GET['gender'] ?? null;
                $brand = $_GET['brand'] ?? null;
                $size = $_GET['size'] ?? null;
                $minPrice = $_GET['min_price'] ?? null;
                $maxPrice = $_GET['max_price'] ?? null;
                $sort = $_GET['sort'] ?? 'featured';
                $limit = intval($_GET['limit'] ?? 12);
                $skip = intval($_GET['skip'] ?? 0);
                
                // Build filters
                $filters = [];
                if ($gender) $filters['gender'] = $gender;
                if ($brand) $filters['brand'] = $brand;
                if ($size) $filters['size'] = $size;
                if ($minPrice !== null && $maxPrice !== null) {
                    $filters['price'] = [
                        '$gte' => floatval($minPrice),
                        '$lte' => floatval($maxPrice)
                    ];
                }
                
                // Build sort options
                $sortOptions = [];
                switch ($sort) {
                    case 'newest':
                        $sortOptions = ['createdAt' => -1];
                        break;
                    case 'price-low':
                        $sortOptions = ['price' => 1];
                        break;
                    case 'price-high':
                        $sortOptions = ['price' => -1];
                        break;
                    case 'popular':
                        $sortOptions = ['featured' => -1, 'createdAt' => -1];
                        break;
                    default: // featured
                        $sortOptions = ['featured' => -1, 'createdAt' => -1];
                        break;
                }
                
                $perfumes = $perfumeModel->getAllPerfumes($filters, $sortOptions, $limit, $skip);
                $total = $perfumeModel->getCount(array_merge($filters, ['category' => 'Perfumes']));
                
                $response = [
                    'success' => true,
                    'data' => [
                        'perfumes' => $perfumes,
                        'total' => $total,
                        'filters' => [
                            'gender' => $gender,
                            'brand' => $brand,
                            'size' => $size,
                            'price_range' => $minPrice && $maxPrice ? [$minPrice, $maxPrice] : null
                        ],
                        'sort' => $sort,
                        'pagination' => [
                            'limit' => $limit,
                            'skip' => $skip,
                            'total_pages' => ceil($total / $limit)
                        ]
                    ]
                ];
                break;
                
            case 'get_brands':
                $brands = $perfumeModel->getPerfumeBrands();
                $response = [
                    'success' => true,
                    'data' => $brands
                ];
                break;
                
            case 'get_sizes':
                $sizes = $perfumeModel->getPerfumeSizes();
                $response = [
                    'success' => true,
                    'data' => $sizes
                ];
                break;
                
            case 'get_statistics':
                $stats = $perfumeModel->getPerfumeStatistics();
                $response = [
                    'success' => true,
                    'data' => $stats
                ];
                break;
                
            case 'initialize_perfumes':
                // This should be protected in production
                $result = $perfumeModel->initializeDefaultPerfumes();
                $response = [
                    'success' => true,
                    'message' => "Initialized {$result['added']} perfumes out of {$result['total']}",
                    'data' => $result
                ];
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
