<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get categories and brands
        $runningCategory = Category::where('name', 'Running')->first();
        $basketballCategory = Category::where('name', 'Basketball')->first();
        $soccerCategory = Category::where('name', 'Soccer')->first();
        $fitnessCategory = Category::where('name', 'Fitness')->first();

        $nikeBrand = Brand::where('name', 'Nike')->first();
        $adidasBrand = Brand::where('name', 'Adidas')->first();
        $wilsonBrand = Brand::where('name', 'Wilson')->first();
        $spaldingBrand = Brand::where('name', 'Spalding')->first();

        // Create sample products with color groups and multiple sizes
        $products = [
            [
                'name' => 'Nike Air Zoom Pegasus 40',
                'description' => 'The Nike Air Zoom Pegasus 40 delivers a smooth, responsive ride with enhanced cushioning and breathable mesh upper.',
                'price' => 129.99,
                'category_id' => $runningCategory?->id,
                'brand_id' => $nikeBrand?->id,
                'sku' => 'NK-PEG40-001',
                'is_active' => true,
                'is_featured' => true,
                'discount' => 0,
                'colorGroups' => [
                    [
                        'color' => 'Classic Black',
                        'sizes' => [
                            ['size' => '7', 'stock' => 25, 'price_adjustment' => 0],
                            ['size' => '8', 'stock' => 30, 'price_adjustment' => 0],
                            ['size' => '9', 'stock' => 28, 'price_adjustment' => 0],
                            ['size' => '10', 'stock' => 22, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/nike-pegasus-black-1.jpg', 'products/nike-pegasus-black-2.jpg']
                    ],
                    [
                        'color' => 'Pure White',
                        'sizes' => [
                            ['size' => '7', 'stock' => 20, 'price_adjustment' => 0],
                            ['size' => '8', 'stock' => 25, 'price_adjustment' => 0],
                            ['size' => '9', 'stock' => 23, 'price_adjustment' => 0],
                            ['size' => '10', 'stock' => 18, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/nike-pegasus-white-1.jpg', 'products/nike-pegasus-white-2.jpg']
                    ],
                    [
                        'color' => 'Royal Blue',
                        'sizes' => [
                            ['size' => '8', 'stock' => 15, 'price_adjustment' => 5.00],
                            ['size' => '9', 'stock' => 18, 'price_adjustment' => 5.00],
                            ['size' => '10', 'stock' => 12, 'price_adjustment' => 5.00],
                        ],
                        'images' => ['products/nike-pegasus-blue-1.jpg']
                    ]
                ]
            ],
            [
                'name' => 'Wilson Evolution Basketball',
                'description' => 'The Wilson Evolution Basketball is the official game ball of many high school and college leagues. Premium composite leather construction provides exceptional grip and feel.',
                'price' => 64.99,
                'category_id' => $basketballCategory?->id,
                'brand_id' => $wilsonBrand?->id,
                'sku' => 'WL-EVO-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 0,
                'colorGroups' => [
                    [
                        'color' => 'Amber',
                        'sizes' => [
                            ['size' => 'Official Size', 'stock' => 15, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/wilson-evolution-amber-1.jpg']
                    ]
                ]
            ],
            [
                'name' => 'Adidas Tango Soccer Ball',
                'description' => 'Professional grade soccer ball with premium synthetic leather cover and butyl bladder for optimal performance.',
                'price' => 44.99,
                'category_id' => $soccerCategory?->id,
                'brand_id' => $adidasBrand?->id,
                'sku' => 'AD-TANGO-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 10.00,
                'colorGroups' => [
                    [
                        'color' => 'Pure White',
                        'sizes' => [
                            ['size' => 'Size 5', 'stock' => 20, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/adidas-tango-white-1.jpg', 'products/adidas-tango-white-2.jpg']
                    ]
                ]
            ],
            [
                'name' => 'Spalding NBA Official Basketball',
                'description' => 'Official NBA game ball with premium composite leather and deep channel design for superior grip and control.',
                'price' => 109.99,
                'category_id' => $basketballCategory?->id,
                'brand_id' => $spaldingBrand?->id,
                'sku' => 'SP-NBA-001',
                'is_active' => true,
                'is_featured' => true,
                'discount' => 20.00,
                'colorGroups' => [
                    [
                        'color' => 'Amber',
                        'sizes' => [
                            ['size' => 'Official Size', 'stock' => 12, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/spalding-nba-amber-1.jpg']
                    ]
                ]
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Iconic Air Max design with visible Air unit and breathable mesh upper for all-day comfort.',
                'price' => 149.99,
                'category_id' => $runningCategory?->id,
                'brand_id' => $nikeBrand?->id,
                'sku' => 'NK-AM270-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 0,
                'colorGroups' => [
                    [
                        'color' => 'Pure White',
                        'sizes' => [
                            ['size' => '8', 'stock' => 18, 'price_adjustment' => 0],
                            ['size' => '9', 'stock' => 22, 'price_adjustment' => 0],
                            ['size' => '10', 'stock' => 20, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/nike-airmax-white-1.jpg', 'products/nike-airmax-white-2.jpg']
                    ],
                    [
                        'color' => 'Crimson',
                        'sizes' => [
                            ['size' => '8', 'stock' => 12, 'price_adjustment' => 0],
                            ['size' => '9', 'stock' => 15, 'price_adjustment' => 0],
                            ['size' => '10', 'stock' => 13, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/nike-airmax-red-1.jpg']
                    ]
                ]
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Revolutionary running shoe with responsive Boost midsole and Primeknit upper for ultimate comfort and performance.',
                'price' => 189.99,
                'category_id' => $runningCategory?->id,
                'brand_id' => $adidasBrand?->id,
                'sku' => 'AD-UB22-001',
                'is_active' => true,
                'is_featured' => true,
                'discount' => 15.00,
                'colorGroups' => [
                    [
                        'color' => 'Classic Black',
                        'sizes' => [
                            ['size' => '8', 'stock' => 16, 'price_adjustment' => 0],
                            ['size' => '9', 'stock' => 19, 'price_adjustment' => 0],
                            ['size' => '10', 'stock' => 17, 'price_adjustment' => 0],
                        ],
                        'images' => ['products/adidas-ultraboost-black-1.jpg', 'products/adidas-ultraboost-black-2.jpg']
                    ],
                    [
                        'color' => 'Sky Blue',
                        'sizes' => [
                            ['size' => '8', 'stock' => 10, 'price_adjustment' => 10.00],
                            ['size' => '9', 'stock' => 12, 'price_adjustment' => 10.00],
                            ['size' => '10', 'stock' => 11, 'price_adjustment' => 10.00],
                        ],
                        'images' => ['products/adidas-ultraboost-blue-1.jpg']
                    ]
                ]
            ],
        ];

        foreach ($products as $productData) {
            $colorGroups = $productData['colorGroups'];
            unset($productData['colorGroups']);

            $product = Product::create($productData);

            // Create variants from color groups
            $hasDefault = false;
            foreach ($colorGroups as $colorGroupIndex => $colorGroup) {
                foreach ($colorGroup['sizes'] as $sizeIndex => $sizeData) {
                    // Set first variant as default
                    $isDefault = !$hasDefault && $colorGroupIndex === 0 && $sizeIndex === 0;
                    if ($isDefault) {
                        $hasDefault = true;
                    }

                    $product->variants()->create([
                        'size' => $sizeData['size'],
                        'color' => $colorGroup['color'],
                        'images' => $colorGroup['images'],
                        'stock' => $sizeData['stock'],
                        'sku' => $product->sku . '-' . $sizeData['size'] . '-' . str_replace(' ', '-', $colorGroup['color']),
                        'price_adjustment' => $sizeData['price_adjustment'],
                        'is_active' => true,
                        'is_default' => $isDefault,
                    ]);
                }
            }
        }
    }
}
