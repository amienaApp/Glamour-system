<?php
// Test the API endpoint directly
echo "<h2>Testing API Endpoint</h2>\n";

// Test the get-sub-subcategories.php endpoint
$url = "admin/get-sub-subcategories.php?category=" . urlencode('Beauty & Cosmetics') . "&subcategory=" . urlencode('Makeup');

echo "<p>Testing URL: $url</p>\n";

$response = file_get_contents($url);

if ($response === false) {
    echo "<p>❌ Failed to get response from API</p>\n";
} else {
    echo "<p>✅ Got response from API</p>\n";
    echo "<p>Response:</p>\n";
    echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>✅ JSON decoded successfully</p>\n";
        echo "<pre>" . print_r($data, true) . "</pre>\n";
    } else {
        echo "<p>❌ Failed to decode JSON</p>\n";
    }
}
?>

