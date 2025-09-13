<?php
// Test the real clearAllFilters function with actual shoes page
$page_title = 'Test Clear All Filters';

// Simulate different URL scenarios
$test_scenarios = [
    'No filters' => '',
    'With subcategory only' => '?subcategory=womenshoes',
    'With all filters' => '?subcategory=womenshoes&gender=women&size=36&color=black&price_range=0-25',
    'With gender filter' => '?gender=men',
    'With size filter' => '?size=38',
    'With price filter' => '?price_range=25-50'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/sidebar.css">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .test-container { max-width: 1200px; margin: 0 auto; }
        .test-header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .test-scenarios { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0; }
        .scenario-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #007bff; }
        .scenario-card h4 { margin: 0 0 10px 0; color: #333; }
        .scenario-url { background: #f8f9fa; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 12px; margin: 10px 0; word-break: break-all; }
        .test-button { background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin: 5px 0; }
        .test-button:hover { background: #0056b3; }
        .test-results { background: #f0f8ff; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 4px solid #007bff; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🧪 Real Clear All Filters Test</h1>
            <p>This test uses the actual shoes page with the real clearAllFilters function.</p>
            <p><strong>Current URL:</strong> <code><?php echo $_SERVER['REQUEST_URI']; ?></code></p>
        </div>

        <div class="test-scenarios">
            <?php foreach ($test_scenarios as $name => $url): ?>
            <div class="scenario-card">
                <h4><?php echo $name; ?></h4>
                <div class="scenario-url">shoes.php<?php echo $url; ?></div>
                <a href="shoes.php<?php echo $url; ?>" class="test-button" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Test This Scenario
                </a>
                <p><small>Click to test the clearAllFilters function with this URL</small></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="test-results">
            <h3>📋 Test Instructions</h3>
            <ol>
                <li>Click on any scenario above to open the shoes page with that URL</li>
                <li>Observe the current filtered state (checkboxes, dropdowns, products shown)</li>
                <li>Click the "Clear All Filters" button in the sidebar</li>
                <li>Verify that:
                    <ul>
                        <li>All checkboxes are unchecked</li>
                        <li>Dropdowns are reset to default</li>
                        <li>URL is cleaned (only subcategory preserved if any)</li>
                        <li>All available products are shown</li>
                    </ul>
                </li>
            </ol>
        </div>

        <div class="test-results">
            <h3>🔍 Expected Behavior</h3>
            <p><strong>For each scenario, the clearAllFilters function should:</strong></p>
            <ul>
                <li>✅ Uncheck all filter checkboxes</li>
                <li>✅ Reset all dropdowns to default values</li>
                <li>✅ Clean the URL (remove all filter parameters except subcategory)</li>
                <li>✅ Show all available products in the current category/subcategory</li>
                <li>✅ Preserve subcategory navigation if present</li>
            </ul>
        </div>

        <div class="test-results">
            <h3>🎯 Test Results</h3>
            <p>After testing each scenario, you should see:</p>
            <ul>
                <li><strong>No filters scenario:</strong> Should stay on shoes.php with all products</li>
                <li><strong>Subcategory only:</strong> Should stay on shoes.php?subcategory=womenshoes with all women's shoes</li>
                <li><strong>All filters:</strong> Should clean to shoes.php?subcategory=womenshoes with all women's shoes</li>
                <li><strong>Gender filter:</strong> Should clean to shoes.php with all shoes</li>
                <li><strong>Size filter:</strong> Should clean to shoes.php with all shoes</li>
                <li><strong>Price filter:</strong> Should clean to shoes.php with all shoes</li>
            </ul>
        </div>
    </div>

    <script>
        // Add some interactive testing
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Real test page loaded. Click on any scenario to test the clearAllFilters function.');
            
            // Add click tracking
            document.querySelectorAll('.test-button').forEach(button => {
                button.addEventListener('click', function() {
                    console.log('Testing scenario:', this.closest('.scenario-card').querySelector('h4').textContent);
                });
            });
        });
    </script>
</body>
</html>

