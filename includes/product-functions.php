<?php
// Product formatting functions for quick view
// This file contains only functions, no API logic

function formatProductForQuickView($product) {
    $formatted = [
        'id' => (string)$product['_id'],
        'name' => $product['name'] ?? '',
        'price' => $product['price'] ?? 0,
        'salePrice' => $product['salePrice'] ?? null,
        'description' => $product['description'] ?? '',
        'category' => $product['category'] ?? '',
        'subcategory' => $product['subcategory'] ?? '',
        'available' => $product['available'] ?? true,
        'stock' => $product['stock'] ?? 0,
        'featured' => $product['featured'] ?? false,
        'sale' => $product['sale'] ?? false,
        'images' => [],
        'colors' => [],
        'sizes' => [],
        'variants' => []
    ];
    
    // Handle main product images
    if (!empty($product['front_image'])) {
        $formatted['images'][] = [
            'src' => $product['front_image'],
            'type' => 'front',
            'color' => 'main'
        ];
    }
    
    if (!empty($product['back_image'])) {
        $formatted['images'][] = [
            'src' => $product['back_image'],
            'type' => 'back',
            'color' => 'main'
        ];
    }
    
    // Handle main product color
    if (!empty($product['color'])) {
        $formatted['colors'][] = [
            'name' => 'Main',
            'value' => 'main',
            'hex' => $product['color']
        ];
    }
    
    // Handle color variants
    if (!empty($product['color_variants']) && is_array($product['color_variants'])) {
        foreach ($product['color_variants'] as $variant) {
            $variantData = [
                'name' => $variant['name'] ?? '',
                'color' => $variant['color'] ?? '',
                'stock' => $variant['stock'] ?? 0,
                'images' => []
            ];
            
            // Add variant images
            if (!empty($variant['front_image'])) {
                $variantData['images'][] = [
                    'src' => $variant['front_image'],
                    'type' => 'front'
                ];
                $formatted['images'][] = [
                    'src' => $variant['front_image'],
                    'type' => 'front',
                    'color' => $variant['color']
                ];
            }
            
            if (!empty($variant['back_image'])) {
                $variantData['images'][] = [
                    'src' => $variant['back_image'],
                    'type' => 'back'
                ];
                $formatted['images'][] = [
                    'src' => $variant['back_image'],
                    'type' => 'back',
                    'color' => $variant['color']
                ];
            }
            
            // Add variant color to colors array
            if (!empty($variant['color'])) {
                $formatted['colors'][] = [
                    'name' => $variant['name'],
                    'value' => $variant['color'],
                    'hex' => $variant['color']
                ];
            }
            
            $formatted['variants'][] = $variantData;
        }
    }
    
    // Handle sizes
    if (!empty($product['selected_sizes'])) {
        if (is_string($product['selected_sizes'])) {
            $sizes = json_decode($product['selected_sizes'], true);
            if (is_array($sizes)) {
                $formatted['sizes'] = $sizes;
            }
        } elseif (is_array($product['selected_sizes'])) {
            $formatted['sizes'] = $product['selected_sizes'];
        }
    }
    
    // If no sizes specified, add default sizes based on category
    if (empty($formatted['sizes'])) {
        $category = strtolower($product['category'] ?? '');
        if (strpos($category, 'shoes') !== false) {
            $formatted['sizes'] = ['One Size'];
        } elseif (strpos($category, 'perfumes') !== false) {
            $formatted['sizes'] = ['30ml', '50ml', '100ml', '200ml'];
        } elseif (strpos($category, 'accessories') !== false || strpos($category, 'bags') !== false) {
            $formatted['sizes'] = ['One Size'];
        } else {
            $formatted['sizes'] = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        }
    }
    
    // Format price display
    if ($formatted['sale'] && $formatted['salePrice']) {
        $formatted['displayPrice'] = '$' . number_format($formatted['salePrice'], 2);
        $formatted['originalPrice'] = '$' . number_format($formatted['price'], 2);
    } else {
        $formatted['displayPrice'] = '$' . number_format($formatted['price'], 2);
        $formatted['originalPrice'] = null;
    }
    
    return $formatted;
}
?>

