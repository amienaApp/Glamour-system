<?php
// Error handling configuration
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Function to ensure JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Function to send error response
function sendErrorResponse($message, $statusCode = 500, $details = null) {
    $response = ['error' => $message];
    if ($details) {
        $response['details'] = $details;
    }
    sendJsonResponse($response, $statusCode);
}

session_start();
header('Content-Type: application/json');

// Session validation

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    error_log('Unauthorized access attempt');
    sendErrorResponse('Unauthorized', 401, ['session_data' => $_SESSION]);
}

// Check if files exist before requiring them
$configPath = '../config1/mongodb.php';
$modelPath = '../models/Product.php';

if (!file_exists($configPath)) {
    error_log('Config file not found: ' . realpath($configPath));
    sendErrorResponse('Configuration file not found', 500, ['path' => $configPath]);
}

if (!file_exists($modelPath)) {
    error_log('Model file not found: ' . realpath($modelPath));
    sendErrorResponse('Model file not found', 500, ['path' => $modelPath]);
}

require_once $configPath;
require_once $modelPath;

// Helper function to properly convert MongoDB objects to arrays
function processMongoObject($value) {
    if (is_object($value)) {
        // Process object type
        
        if (method_exists($value, 'toArray')) {
            $result = $value->toArray();

            
            // Recursively process nested objects
            if (is_array($result)) {
                foreach ($result as $key => $item) {
                    $result[$key] = processMongoObject($item);
                }
                return $result;
            } else {
                return (array)$result;
            }
        } elseif (method_exists($value, 'getArrayCopy')) {
            $result = $value->getArrayCopy();

            
            // Recursively process nested objects
            if (is_array($result)) {
                foreach ($result as $key => $item) {
                    $result[$key] = processMongoObject($item);
                }
                return $result;
            }
            return $result;
        } elseif (method_exists($value, 'count')) {
            // Handle MongoDB BSONArray objects
            $result = [];
            foreach ($value as $item) {
                $result[] = processMongoObject($item);
            }
            return $result;
        } elseif (is_iterable($value)) {
            // Handle any iterable object (including regular arrays and other iterables)
            $result = [];
            foreach ($value as $item) {
                $result[] = processMongoObject($item);
            }
            return $result;
        } elseif (is_array($value)) {
            // Handle regular arrays
            error_log('Processing regular array with ' . count($value) . ' items');
            $result = [];
            foreach ($value as $item) {
                $result[] = processMongoObject($item);
            }
            return $result;
        } else {
            // Try to convert object to array
            return (array)$value;
        }
    } elseif (is_array($value)) {
        // Recursively process arrays
        foreach ($value as $key => $item) {
            $value[$key] = processMongoObject($item);
        }
        return $value;
    }
    return $value;
}

try {
    $productModel = new Product();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $productId = $_GET['id'] ?? '';
        
        // Process request
        
        if (empty($productId)) {
            sendErrorResponse('Product ID is required', 400);
        }
        
        // Get the product with all its data
        $product = $productModel->getById($productId);
        
        if (!$product) {
            error_log('Product not found for ID: ' . $productId);
            sendErrorResponse('Product not found', 404, ['product_id' => $productId]);
        }
        

        
        // Process the product data to handle MongoDB objects
        $processedProduct = processMongoObject($product);
        
        // Ensure color_variants is properly processed
        if (isset($processedProduct['color_variants'])) {
            $processedProduct['color_variants'] = processMongoObject($processedProduct['color_variants']);
            
            // Handle empty color variants gracefully
            if (empty($processedProduct['color_variants'])) {
                $processedProduct['color_variants'] = [];
            }
        }
        
        // Convert MongoDB ObjectId to string for JSON serialization
        if (isset($processedProduct['_id'])) {
            $processedProduct['_id'] = (string)$processedProduct['_id'];
        }
        

        
        sendJsonResponse([
            'success' => true,
            'product' => $processedProduct
        ]);
        
    } else {
        sendErrorResponse('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendErrorResponse('Internal server error', 500, [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    error_log('Fatal Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    sendErrorResponse('Fatal error', 500, ['message' => $e->getMessage()]);
}
?>
