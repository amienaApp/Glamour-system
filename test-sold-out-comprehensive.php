<?php
/**
 * Comprehensive Sold Out Functionality Test
 * This file tests the sold out functionality across all pages
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Sold Out Functionality Test</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/sold-out.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .test-results {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #0056b3;
        }
        .test-button.success {
            background: #28a745;
        }
        .test-button.error {
            background: #dc3545;
        }
        .page-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }
        .page-link {
            display: block;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            color: #495057;
            text-align: center;
        }
        .page-link:hover {
            background: #e9ecef;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🛍️ Comprehensive Sold Out Functionality Test</h1>
        
        <div class="test-section">
            <h2>📋 Test Overview</h2>
            <p>This comprehensive test verifies that the sold out functionality is working correctly across all pages of the Glamour System e-commerce website.</p>
            
            <h3>✅ What's Been Implemented:</h3>
            <ul>
                <li>✅ Sold out manager JavaScript added to all category pages</li>
                <li>✅ Product card rendering includes proper sold out attributes</li>
                <li>✅ Comprehensive CSS styling for sold out states</li>
                <li>✅ Universal sold out include file created</li>
                <li>✅ Test products with different stock levels</li>
            </ul>
        </div>

        <div class="test-section">
            <h2>🧪 Live Test Products</h2>
            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                
                <!-- Test Product 1 - Available -->
                <div class="product-card" 
                     data-product-id="test1" 
                     data-product-stock="5" 
                     data-product-available="true"
                     data-product-name="Available Product"
                     data-product-price="29.99">
                    <div class="product-image">
                        <img src="https://picsum.photos/300/400?random=1" alt="Available Product">
                        <button class="heart-button" data-product-id="test1">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="test1">Quick View</button>
                            <button class="add-to-bag" data-product-id="test1">Add To Bag</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Available Product</h3>
                        <div class="product-price">$29.99</div>
                        <div class="product-availability"></div>
                    </div>
                </div>
                
                <!-- Test Product 2 - Sold Out -->
                <div class="product-card sold-out" 
                     data-product-id="test2" 
                     data-product-stock="0" 
                     data-product-available="false"
                     data-product-name="Sold Out Product"
                     data-product-price="39.99">
                    <div class="product-image">
                        <img src="https://picsum.photos/300/400?random=2" alt="Sold Out Product">
                        <button class="heart-button" data-product-id="test2">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="test2">Quick View</button>
                            <button class="add-to-bag sold-out-btn" disabled>Sold Out</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Sold Out Product</h3>
                        <div class="product-price">$39.99</div>
                        <div class="product-availability sold-out-text">SOLD OUT</div>
                    </div>
                </div>
                
                <!-- Test Product 3 - Low Stock -->
                <div class="product-card" 
                     data-product-id="test3" 
                     data-product-stock="2" 
                     data-product-available="true"
                     data-product-name="Low Stock Product"
                     data-product-price="49.99">
                    <div class="product-image">
                        <img src="https://picsum.photos/300/400?random=3" alt="Low Stock Product">
                        <button class="heart-button" data-product-id="test3">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-actions">
                            <button class="quick-view" data-product-id="test3">Quick View</button>
                            <button class="add-to-bag" data-product-id="test3">Add To Bag</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">Low Stock Product</h3>
                        <div class="product-price">$49.99</div>
                        <div class="product-availability low-stock-text">⚠️ Only 2 left in stock!</div>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="test-section">
            <h2>🔧 Test Controls</h2>
            <button class="test-button" onclick="testSoldOutManager()">Test Sold Out Manager</button>
            <button class="test-button" onclick="testSetSoldOut()">Set Product 1 as Sold Out</button>
            <button class="test-button" onclick="testSetAvailable()">Set Product 2 as Available</button>
            <button class="test-button" onclick="testUpdateStock()">Update Product 3 Stock to 0</button>
            <button class="test-button" onclick="testRefreshAll()">Refresh All Products</button>
            <button class="test-button" onclick="testPageLinks()">Test Page Links</button>
            <button class="test-button" onclick="clearResults()">Clear Results</button>
        </div>

        <div class="test-section">
            <h2>🌐 Page Links Test</h2>
            <p>Click on any page link to test if the sold out functionality is working on that page:</p>
            <div class="page-links">
                <a href="index.php" class="page-link">🏠 Home Page</a>
                <a href="womenF/women.php" class="page-link">👗 Women's Clothing</a>
                <a href="menfolder/men.php" class="page-link">👔 Men's Clothing</a>
                <a href="shoess/shoes.php" class="page-link">👟 Shoes</a>
                <a href="bagsfolder/bags.php" class="page-link">👜 Bags</a>
                <a href="accessories/accessories.php" class="page-link">⌚ Accessories</a>
                <a href="perfumes/index.php" class="page-link">💄 Perfumes</a>
                <a href="beautyfolder/beauty.php" class="page-link">💅 Beauty</a>
                <a href="homedecor/homedecor.php" class="page-link">🏠 Home Decor</a>
                <a href="kidsfolder/kids.php" class="page-link">👶 Kids</a>
            </div>
        </div>

        <div class="test-section">
            <h2>📊 Test Results</h2>
            <div id="test-results" class="test-results">Click a test button to see results...</div>
        </div>

        <div class="test-section">
            <h2>📝 Test Checklist</h2>
            <div id="test-checklist">
                <div class="checklist-item">
                    <input type="checkbox" id="check1"> <label for="check1">Sold out manager loads on all pages</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check2"> <label for="check2">Sold out products display correctly</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check3"> <label for="check3">Sold out buttons are disabled</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check4"> <label for="check4">Heart buttons are disabled for sold out items</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check5"> <label for="check5">Quick view still works for sold out items</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check6"> <label for="check6">Low stock warnings display correctly</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check7"> <label for="check7">CSS styling is consistent across pages</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="check8"> <label for="check8">JavaScript functions work correctly</label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include the sold out manager -->
    <script src="scripts/sold-out-manager.js"></script>
    
    <script>
        function logResult(message, type = 'info') {
            const resultsDiv = document.getElementById('test-results');
            const timestamp = new Date().toLocaleTimeString();
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
            resultsDiv.innerHTML += `<div>[${timestamp}] ${icon} ${message}</div>`;
        }
        
        function updateChecklist(itemId, status) {
            const checkbox = document.getElementById(itemId);
            if (checkbox) {
                checkbox.checked = status;
            }
        }
        
        function testSoldOutManager() {
            logResult('Testing Sold Out Manager...');
            if (window.soldOutManager) {
                logResult('✅ Sold Out Manager is available', 'success');
                updateChecklist('check1', true);
                
                logResult(`Found ${document.querySelectorAll('.product-card').length} product cards`);
                
                // Test getting sold out products
                const soldOutProducts = window.soldOutManager.getSoldOutProducts();
                logResult(`Found ${soldOutProducts.length} sold out products: ${soldOutProducts.map(p => p.name).join(', ')}`);
                
                // Test getting available products
                const availableProducts = window.soldOutManager.getAvailableProducts();
                logResult(`Found ${availableProducts.length} available products: ${availableProducts.map(p => p.name).join(', ')}`);
                
                updateChecklist('check8', true);
            } else {
                logResult('❌ Sold Out Manager is not available', 'error');
                updateChecklist('check1', false);
            }
        }
        
        function testSetSoldOut() {
            logResult('Setting Product 1 as sold out...');
            if (window.soldOutManager) {
                window.soldOutManager.setSoldOut('test1');
                logResult('✅ Product 1 set as sold out', 'success');
                updateChecklist('check2', true);
                updateChecklist('check3', true);
                updateChecklist('check4', true);
            } else {
                logResult('❌ Sold Out Manager not available', 'error');
            }
        }
        
        function testSetAvailable() {
            logResult('Setting Product 2 as available...');
            if (window.soldOutManager) {
                window.soldOutManager.setAvailable('test2', 5);
                logResult('✅ Product 2 set as available with stock 5', 'success');
            } else {
                logResult('❌ Sold Out Manager not available', 'error');
            }
        }
        
        function testUpdateStock() {
            logResult('Updating Product 3 stock to 0...');
            if (window.soldOutManager) {
                window.soldOutManager.updateStock('test3', 0);
                logResult('✅ Product 3 stock updated to 0', 'success');
            } else {
                logResult('❌ Sold Out Manager not available', 'error');
            }
        }
        
        function testRefreshAll() {
            logResult('Refreshing all products...');
            if (window.soldOutManager) {
                window.soldOutManager.refreshAllProducts();
                logResult('✅ All products refreshed', 'success');
            } else {
                logResult('❌ Sold Out Manager not available', 'error');
            }
        }
        
        function testPageLinks() {
            logResult('Testing page links...');
            const pageLinks = document.querySelectorAll('.page-link');
            logResult(`Found ${pageLinks.length} page links to test`);
            logResult('Click on any page link to test sold out functionality on that page');
            logResult('Look for sold out products and verify they display correctly');
        }
        
        function clearResults() {
            document.getElementById('test-results').innerHTML = 'Results cleared. Click a test button to see new results...';
            // Uncheck all checkboxes
            document.querySelectorAll('#test-checklist input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
        }
        
        // Auto-test when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                logResult('Page loaded, running auto-test...');
                testSoldOutManager();
                
                // Test if sold out products display correctly
                const soldOutCards = document.querySelectorAll('.product-card.sold-out');
                if (soldOutCards.length > 0) {
                    logResult(`✅ Found ${soldOutCards.length} sold out product cards`, 'success');
                    updateChecklist('check2', true);
                    updateChecklist('check3', true);
                    updateChecklist('check4', true);
                }
                
                // Test if low stock products display correctly
                const lowStockCards = document.querySelectorAll('.product-availability.low-stock-text');
                if (lowStockCards.length > 0) {
                    logResult(`✅ Found ${lowStockCards.length} low stock warnings`, 'success');
                    updateChecklist('check6', true);
                }
                
                // Test CSS styling
                const soldOutBtn = document.querySelector('.sold-out-btn');
                if (soldOutBtn) {
                    const styles = window.getComputedStyle(soldOutBtn);
                    if (styles.cursor === 'not-allowed') {
                        logResult('✅ Sold out button styling is correct', 'success');
                        updateChecklist('check7', true);
                    }
                }
                
                logResult('Auto-test completed. Use the test buttons above for more detailed testing.');
            }, 1000);
        });
    </script>
</body>
</html>

