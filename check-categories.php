<?php
require_once 'models/Category.php';

$category = new Category();
$categories = $category->getAll();

echo "=== CURRENT CATEGORIES IN DATABASE ===\n\n";

foreach ($categories as $cat) {
    echo "Category: " . $cat['name'] . "\n";
    if (!empty($cat['subcategories'])) {
        $subcategories = is_object($cat['subcategories']) ? iterator_to_array($cat['subcategories']) : $cat['subcategories'];
        echo "  Subcategories: " . implode(', ', $subcategories) . "\n";
    }
    echo "\n";
}

echo "Total categories: " . count($categories) . "\n";
?>
