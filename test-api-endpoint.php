<?php
// Test the API endpoint
echo "Testing API endpoint...\n";

// Simulate the API call by including the file directly
$_GET['category'] = 'Beauty & Cosmetics';
$_GET['subcategory'] = 'Makeup';

// Capture output
ob_start();
include 'admin/get-sub-subcategories.php';
$output = ob_get_clean();

echo "API Response:\n";
echo $output . "\n";

// Try to decode JSON
$data = json_decode($output, true);
if ($data) {
    echo "✅ JSON decoded successfully\n";
    echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    if (isset($data['sub_subcategories'])) {
        echo "Sub-subcategories count: " . count($data['sub_subcategories']) . "\n";
        echo "First 5 items:\n";
        foreach (array_slice($data['sub_subcategories'], 0, 5) as $item) {
            echo "  - " . $item . "\n";
        }
    }
} else {
    echo "❌ Failed to decode JSON\n";
}

echo "Done.\n";
?>
