<?php
/**
 * Perfume Model
 * Handles perfume-specific operations and extends Product functionality
 */

require_once __DIR__ . '/Product.php';

class Perfume extends Product {
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all perfumes from the database
     */
    public function getAllPerfumes($filters = [], $sort = [], $limit = 0, $skip = 0) {
        // Add perfume category filter
        $filters['category'] = 'Perfumes';
        return $this->getAll($filters, $sort, $limit, $skip);
    }

    /**
     * Get perfumes by gender
     */
    public function getPerfumesByGender($gender) {
        return $this->getAllPerfumes(['gender' => $gender]);
    }

    /**
     * Get perfumes by brand
     */
    public function getPerfumesByBrand($brand) {
        return $this->getAllPerfumes(['brand' => $brand]);
    }

    /**
     * Get perfumes by size
     */
    public function getPerfumesBySize($size) {
        return $this->getAllPerfumes(['size' => $size]);
    }

    /**
     * Get perfumes by price range
     */
    public function getPerfumesByPriceRange($minPrice, $maxPrice) {
        return $this->getAllPerfumes([
            'price' => [
                '$gte' => $minPrice,
                '$lte' => $maxPrice
            ]
        ]);
    }

    /**
     * Get all perfume brands
     */
    public function getPerfumeBrands() {
        $perfumes = $this->getAllPerfumes();
        $brands = [];
        
        foreach ($perfumes as $perfume) {
            if (isset($perfume['brand']) && !in_array($perfume['brand'], $brands)) {
                $brands[] = $perfume['brand'];
            }
        }
        
        return $brands;
    }

    /**
     * Get all perfume sizes
     */
    public function getPerfumeSizes() {
        $perfumes = $this->getAllPerfumes();
        $sizes = [];
        
        foreach ($perfumes as $perfume) {
            if (isset($perfume['size']) && !in_array($perfume['size'], $sizes)) {
                $sizes[] = $perfume['size'];
            }
        }
        
        return $sizes;
    }

    /**
     * Get perfume statistics
     */
    public function getPerfumeStatistics() {
        $totalPerfumes = $this->getCount(['category' => 'Perfumes']);
        $menPerfumes = $this->getCount(['category' => 'Perfumes', 'gender' => 'men']);
        $womenPerfumes = $this->getCount(['category' => 'Perfumes', 'gender' => 'women']);
        $featuredPerfumes = $this->getCount(['category' => 'Perfumes', 'featured' => true]);
        $salePerfumes = $this->getCount(['category' => 'Perfumes', 'sale' => true]);

        return [
            'total_perfumes' => $totalPerfumes,
            'men_perfumes' => $menPerfumes,
            'women_perfumes' => $womenPerfumes,
            'featured_perfumes' => $featuredPerfumes,
            'sale_perfumes' => $salePerfumes
        ];
    }

    /**
     * Create a new perfume product
     */
    public function createPerfume($perfumeData) {
        // Ensure category is set to Perfumes
        $perfumeData['category'] = 'Perfumes';
        
        // Validate perfume-specific fields
        $errors = $this->validatePerfumeData($perfumeData);
        if (!empty($errors)) {
            throw new Exception('Validation errors: ' . implode(', ', $errors));
        }

        return $this->create($perfumeData);
    }

    /**
     * Validate perfume data
     */
    public function validatePerfumeData($data) {
        $errors = parent::validateProductData($data);
        
        // Add perfume-specific validation
        if (empty($data['brand'])) {
            $errors[] = 'Brand is required for perfumes';
        }
        
        if (empty($data['gender'])) {
            $errors[] = 'Gender is required for perfumes';
        }
        
        if (empty($data['size'])) {
            $errors[] = 'Size is required for perfumes';
        }
        
        if (!in_array($data['gender'], ['men', 'women', 'unisex'])) {
            $errors[] = 'Gender must be men, women, or unisex';
        }
        
        if (!in_array($data['size'], ['30ml', '50ml', '100ml', '200ml'])) {
            $errors[] = 'Size must be 30ml, 50ml, 100ml, or 200ml';
        }
        
        return $errors;
    }

    /**
     * Initialize default perfume products
     */
    public function initializeDefaultPerfumes() {
        $defaultPerfumes = [
            [
                'name' => 'Sauvage Dior',
                'brand' => 'Dior',
                'gender' => 'men',
                'size' => '100ml',
                'price' => 150,
                'color' => '#000000',
                'category' => 'Perfumes',
                'subcategory' => 'Men\'s Fragrances',
                'description' => 'A powerful and fresh fragrance with notes of bergamot, pepper, and ambroxan.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/15.jpg',
                'back_image' => 'img/perfumes/15.0.jpg',
                'color_variants' => [
                    [
                        'name' => 'Black',
                        'color' => '#000000',
                        'front_image' => 'img/perfumes/15.jpg',
                        'back_image' => 'img/perfumes/15.0.jpg'
                    ],
                    [
                        'name' => 'Blue',
                        'color' => '#0e50f6ff',
                        'front_image' => 'img/perfumes/15.1.jpg',
                        'back_image' => 'img/perfumes/15.1.0.jpg'
                    ]
                ]
            ],
            [
                'name' => 'Strong With You Perfume',
                'brand' => 'Other',
                'gender' => 'men',
                'size' => '100ml',
                'price' => 220,
                'color' => '#fd0f36ff',
                'category' => 'Perfumes',
                'subcategory' => 'Men\'s Fragrances',
                'description' => 'A bold and masculine fragrance with spicy and woody notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/23.jpg',
                'back_image' => 'img/perfumes/23.1.jpg'
            ],
            [
                'name' => 'Khamrah',
                'brand' => 'Lattafa',
                'gender' => 'men',
                'size' => '100ml',
                'price' => 125,
                'color' => '#8b4513',
                'category' => 'Perfumes',
                'subcategory' => 'Men\'s Fragrances',
                'description' => 'A luxurious Middle Eastern fragrance with oud and oriental notes.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/20.jpg'
            ],
            [
                'name' => 'Miss Dior Eau De Parfum',
                'brand' => 'Dior',
                'gender' => 'women',
                'size' => '100ml',
                'price' => 105,
                'color' => '#eb9abcff',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A romantic and feminine fragrance with floral and fruity notes.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/14.avif'
            ],
            [
                'name' => 'Valentino Donna Born in Roma',
                'brand' => 'Valentino',
                'gender' => 'women',
                'size' => '30ml',
                'price' => 95,
                'color' => '#ffc0cb',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A modern and sophisticated fragrance with vanilla and jasmine notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/7.webp'
            ],
            [
                'name' => 'Gucci Bloom Eau de Parfum',
                'brand' => 'Gucci',
                'gender' => 'women',
                'size' => '100ml',
                'price' => 180,
                'color' => '#050505ff',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A floral and powdery fragrance with tuberose and jasmine notes.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/22.jpg'
            ],
            [
                'name' => 'Prada Milano',
                'brand' => 'Other',
                'gender' => 'women',
                'size' => '50ml',
                'price' => 140,
                'color' => '#f7a7c2ff',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A sophisticated and elegant fragrance with iris and amber notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/16.png'
            ],
            [
                'name' => 'Valentino',
                'brand' => 'Valentino',
                'gender' => 'men',
                'size' => '100ml',
                'price' => 200,
                'color' => '#000000',
                'category' => 'Perfumes',
                'subcategory' => 'Men\'s Fragrances',
                'description' => 'A bold and masculine fragrance with leather and tobacco notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/10.webp',
                'back_image' => 'img/perfumes/10.0.webp'
            ],
            [
                'name' => 'Born In Roma Extradose Eau De Parfum',
                'brand' => 'Valentino',
                'gender' => 'women',
                'size' => '50ml',
                'price' => 120,
                'color' => '#ffc0cb',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'An intense and passionate fragrance with vanilla and jasmine notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/4.webp'
            ],
            [
                'name' => 'Chanel Coco Mademoiselle',
                'brand' => 'Chanel',
                'gender' => 'women',
                'size' => '50ml',
                'price' => 200,
                'color' => '#ff0000ff',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A classic and elegant fragrance with rose and patchouli notes.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/12.webp',
                'back_image' => 'img/perfumes/12.0.webp'
            ],
            [
                'name' => 'Gucci Guilty Pour Homme',
                'brand' => 'Gucci',
                'gender' => 'men',
                'size' => '100ml',
                'price' => 160,
                'color' => '#000000',
                'category' => 'Perfumes',
                'subcategory' => 'Men\'s Fragrances',
                'description' => 'A seductive and masculine fragrance with lavender and amber notes.',
                'featured' => false,
                'sale' => false,
                'front_image' => 'img/perfumes/13.webp',
                'back_image' => 'img/perfumes/13.0.webp'
            ],
            [
                'name' => 'Good Girl Perfume',
                'brand' => 'Other',
                'gender' => 'women',
                'size' => '100ml',
                'price' => 250,
                'color' => '#474eb9ff',
                'category' => 'Perfumes',
                'subcategory' => 'Women\'s Fragrances',
                'description' => 'A mysterious and seductive fragrance with jasmine and cocoa notes.',
                'featured' => true,
                'sale' => false,
                'front_image' => 'img/perfumes/24.jpg',
                'back_image' => 'img/perfumes/17.jpg',
                'color_variants' => [
                    [
                        'name' => 'Blue',
                        'color' => '#474eb9ff',
                        'front_image' => 'img/perfumes/24.jpg',
                        'back_image' => 'img/perfumes/17.jpg'
                    ],
                    [
                        'name' => 'Navy',
                        'color' => '#1a2145ff',
                        'front_image' => 'img/perfumes/17.jpg',
                        'back_image' => 'img/perfumes/24.jpg'
                    ]
                ]
            ]
        ];

        $addedCount = 0;
        foreach ($defaultPerfumes as $perfume) {
            // Check if perfume already exists
            $existing = $this->getAll([
                'name' => $perfume['name'], 
                'category' => 'Perfumes',
                'brand' => $perfume['brand']
            ]);
            
            if (empty($existing)) {
                if ($this->createPerfume($perfume)) {
                    $addedCount++;
                }
            }
        }

        return ['added' => $addedCount, 'total' => count($defaultPerfumes)];
    }
}
?>
