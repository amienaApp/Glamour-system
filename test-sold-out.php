<?php
/**
 * Test Sold Out Functionality
 * This file tests the sold out functionality across different pages
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sold Out Functionality</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h1>Sold Out Functionality Test</h1>
    
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px;">
        
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
    
    <div style="margin: 20px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
        <h3>Test Controls</h3>
        <button onclick="testSoldOutManager()">Test Sold Out Manager</button>
        <button onclick="testSetSoldOut()">Set Product 1 as Sold Out</button>
        <button onclick="testSetAvailable()">Set Product 2 as Available</button>
        <button onclick="testUpdateStock()">Update Product 3 Stock to 0</button>
        <button onclick="testRefreshAll()">Refresh All Products</button>
    </div>
    
    <div id="test-results" style="margin: 20px; padding: 20px; background: #e8f4fd; border-radius: 8px;">
        <h3>Test Results</h3>
        <div id="results-content">Click a test button to see results...</div>
    </div>
    
    <!-- Include the sold out manager -->
    <script src="scripts/sold-out-manager.js"></script>
    
    <script>
        function logResult(message) {
            const resultsDiv = document.getElementById('results-content');
            const timestamp = new Date().toLocaleTimeString();
            resultsDiv.innerHTML += `<div>[${timestamp}] ${message}</div>`;
        }
        
        function testSoldOutManager() {
            logResult('Testing Sold Out Manager...');
            if (window.soldOutManager) {
                logResult('✅ Sold Out Manager is available');
                logResult(`Found ${document.querySelectorAll('.product-card').length} product cards`);
                
                // Test getting sold out products
                const soldOutProducts = window.soldOutManager.getSoldOutProducts();
                logResult(`Found ${soldOutProducts.length} sold out products: ${soldOutProducts.map(p => p.name).join(', ')}`);
                
                // Test getting available products
                const availableProducts = window.soldOutManager.getAvailableProducts();
                logResult(`Found ${availableProducts.length} available products: ${availableProducts.map(p => p.name).join(', ')}`);
                
            } else {
                logResult('❌ Sold Out Manager is not available');
            }
        }
        
        function testSetSoldOut() {
            logResult('Setting Product 1 as sold out...');
            if (window.soldOutManager) {
                window.soldOutManager.setSoldOut('test1');
                logResult('✅ Product 1 set as sold out');
            } else {
                logResult('❌ Sold Out Manager not available');
            }
        }
        
        function testSetAvailable() {
            logResult('Setting Product 2 as available...');
            if (window.soldOutManager) {
                window.soldOutManager.setAvailable('test2', 5);
                logResult('✅ Product 2 set as available with stock 5');
            } else {
                logResult('❌ Sold Out Manager not available');
            }
        }
        
        function testUpdateStock() {
            logResult('Updating Product 3 stock to 0...');
            if (window.soldOutManager) {
                window.soldOutManager.updateStock('test3', 0);
                logResult('✅ Product 3 stock updated to 0');
            } else {
                logResult('❌ Sold Out Manager not available');
            }
        }
        
        function testRefreshAll() {
            logResult('Refreshing all products...');
            if (window.soldOutManager) {
                window.soldOutManager.refreshAllProducts();
                logResult('✅ All products refreshed');
            } else {
                logResult('❌ Sold Out Manager not available');
            }
        }
        
        // Auto-test when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                logResult('Page loaded, running auto-test...');
                testSoldOutManager();
            }, 1000);
        });
    </script>
</body>
</html>

