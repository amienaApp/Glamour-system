<?php
/**
 * Beauty System Test Page
 * This page tests the beauty system functionality
 */

$page_title = 'Beauty System Test - Glamour Palace';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styles/sidebar.css?v=<?php echo time(); ?>">
    <style>
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-result {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .test-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .test-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .test-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fas fa-palette"></i> Beauty System Test</h1>
        
        <!-- Test 1: MongoDB Connection -->
        <div class="test-section">
            <h2>1. MongoDB Connection Test</h2>
            <?php
            try {
                require_once '../config1/mongodb.php';
                $mongo = MongoDB::getInstance();
                if ($mongo->isConnected()) {
                    echo '<div class="test-result test-success">✅ MongoDB connection successful!</div>';
                } else {
                    echo '<div class="test-result test-error">❌ MongoDB connection failed</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-result test-error">❌ MongoDB error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                
                // Try fallback
                try {
                    require_once '../config1/mongodb-fallback.php';
                    $fallback = MongoDB_Fallback::getInstance();
                    if ($fallback->isConnected()) {
                        echo '<div class="test-result test-info">ℹ️ Using MongoDB fallback mode</div>';
                    } else {
                        echo '<div class="test-result test-info">ℹ️ Using mock data mode</div>';
                    }
                } catch (Exception $e2) {
                    echo '<div class="test-result test-error">❌ Fallback also failed: ' . htmlspecialchars($e2->getMessage()) . '</div>';
                }
            }
            ?>
        </div>
        
        <!-- Test 2: Models -->
        <div class="test-section">
            <h2>2. Model Classes Test</h2>
            <?php
            $models = ['Product', 'Category', 'User', 'Cart'];
            foreach ($models as $model) {
                try {
                    require_once "../models/{$model}.php";
                    $modelClass = new $model();
                    echo '<div class="test-result test-success">✅ ' . $model . ' model loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="test-result test-error">❌ ' . $model . ' model error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            ?>
        </div>
        
        <!-- Test 3: API Endpoints -->
        <div class="test-section">
            <h2>3. API Endpoints Test</h2>
            <div id="api-tests">
                <button onclick="testAPI('get_products')" class="test-btn">Test Get Products</button>
                <button onclick="testAPI('search')" class="test-btn">Test Search</button>
                <button onclick="testAPI('get_suggestions')" class="test-btn">Test Suggestions</button>
                <div id="api-results"></div>
            </div>
        </div>
        
        <!-- Test 4: File Structure -->
        <div class="test-section">
            <h2>4. File Structure Test</h2>
            <?php
            $requiredFiles = [
                'beauty.php',
                'filter-api.php',
                'includes/sidebar.php',
                'includes/main-content.php',
                'styles/main.css',
                'styles/sidebar.css',
                'js/script.js',
                'js/search.js'
            ];
            
            foreach ($requiredFiles as $file) {
                if (file_exists($file)) {
                    echo '<div class="test-result test-success">✅ ' . $file . ' exists</div>';
                } else {
                    echo '<div class="test-result test-error">❌ ' . $file . ' missing</div>';
                }
            }
            ?>
        </div>
        
        <!-- Test 5: CSS and JS -->
        <div class="test-section">
            <h2>5. CSS and JavaScript Test</h2>
            <div id="css-js-test">
                <div class="test-beauty-card">
                    <div class="test-beauty-image">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="test-beauty-info">
                        <h3>Test Beauty Product</h3>
                        <div class="test-beauty-price">$29.99</div>
                        <button class="test-beauty-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test 6: Navigation -->
        <div class="test-section">
            <h2>6. Navigation Test</h2>
            <div class="beauty-categories">
                <a href="beauty.php?subcategory=makeup" class="beauty-category-link">
                    <i class="fas fa-palette"></i>
                    <span>Makeup</span>
                </a>
                <a href="beauty.php?subcategory=skincare" class="beauty-category-link">
                    <i class="fas fa-spa"></i>
                    <span>Skincare</span>
                </a>
                <a href="beauty.php?subcategory=hair" class="beauty-category-link">
                    <i class="fas fa-cut"></i>
                    <span>Hair</span>
                </a>
                <a href="beauty.php?subcategory=bath-body" class="beauty-category-link">
                    <i class="fas fa-bath"></i>
                    <span>Bath & Body</span>
                </a>
            </div>
        </div>
        
        <!-- Test 7: Go to Beauty Page -->
        <div class="test-section">
            <h2>7. Go to Beauty Page</h2>
            <a href="beauty.php" class="test-beauty-btn" style="display: inline-block; padding: 15px 30px; text-decoration: none;">
                <i class="fas fa-palette"></i> Go to Beauty Page
            </a>
        </div>
    </div>
    
    <script>
        function testAPI(action) {
            const resultsDiv = document.getElementById('api-results');
            resultsDiv.innerHTML = '<div class="test-result test-info">Testing ' + action + '...</div>';
            
            const formData = new FormData();
            formData.append('action', action);
            if (action === 'search') {
                formData.append('query', 'test');
            }
            formData.append('category', 'Beauty & Cosmetics');
            
            fetch('filter-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultsDiv.innerHTML = '<div class="test-result test-success">✅ ' + action + ' API working: ' + JSON.stringify(data).substring(0, 100) + '...</div>';
                } else {
                    resultsDiv.innerHTML = '<div class="test-result test-error">❌ ' + action + ' API error: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = '<div class="test-result test-error">❌ ' + action + ' API failed: ' + error.message + '</div>';
            });
        }
    </script>
    
    <style>
        .test-btn {
            background: #ff6b9d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .test-btn:hover {
            background: #c44569;
        }
        .test-beauty-card {
            display: flex;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .test-beauty-image {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff6b9d, #c44569);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-right: 20px;
        }
        .test-beauty-info {
            flex: 1;
        }
        .test-beauty-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b9d;
            margin: 10px 0;
        }
        .test-beauty-btn {
            background: linear-gradient(135deg, #ff6b9d, #c44569);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .test-beauty-btn:hover {
            background: linear-gradient(135deg, #c44569, #ff6b9d);
        }
    </style>
</body>
</html>




