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

        // Create sample products with placeholder images
        $products = [
            [
                'name' => 'Nike Air Zoom Pegasus 40',
                'description' => 'The Nike Air Zoom Pegasus 40 delivers a smooth, responsive ride with enhanced cushioning and breathable mesh upper.',
                'price' => 129.99,
                'images' => [], // Empty array for now - will use placeholder
                'category_id' => $runningCategory?->id,
                'brand_id' => $nikeBrand?->id,
                'sku' => 'NK-PEG40-001',
                'is_active' => true,
                'is_featured' => true,
                'discount' => 0,
                'variants' => [
                    ['size' => '7', 'color' => 'Black', 'stock' => 25, 'sku' => 'NK-PEG40-001-7', 'price_adjustment' => 0],
                    ['size' => '8', 'color' => 'Black', 'stock' => 30, 'sku' => 'NK-PEG40-001-8', 'price_adjustment' => 0],
                    ['size' => '9', 'color' => 'Black', 'stock' => 28, 'sku' => 'NK-PEG40-001-9', 'price_adjustment' => 0],
                    ['size' => '10', 'color' => 'Black', 'stock' => 22, 'sku' => 'NK-PEG40-001-10', 'price_adjustment' => 0],
                ]
            ],
            [
                'name' => 'Wilson Evolution Basketball',
                'description' => 'The Wilson Evolution Basketball is the official game ball of many high school and college leagues. Premium composite leather construction provides exceptional grip and feel.',
                'price' => 64.99,
                'images' => [], // Empty array for now - will use placeholder
                'category_id' => $basketballCategory?->id,
                'brand_id' => $wilsonBrand?->id,
                'sku' => 'WL-EVO-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 0,
                'variants' => [
                    ['size' => 'Official Size', 'color' => 'Orange', 'stock' => 15, 'sku' => 'WL-EVO-001-OS', 'price_adjustment' => 0],
                ]
            ],
            [
                'name' => 'Adidas Tango Soccer Ball',
                'description' => 'Professional grade soccer ball with premium synthetic leather cover and butyl bladder for optimal performance.',
                'price' => 44.99,
                'images' => [], // Empty array for now - will use placeholder
                'category_id' => $soccerCategory?->id,
                'brand_id' => $adidasBrand?->id,
                'sku' => 'AD-TANGO-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 10.00,
                'variants' => [
                    ['size' => 'Size 5', 'color' => 'White/Black', 'stock' => 20, 'sku' => 'AD-TANGO-001-5', 'price_adjustment' => 0],
                ]
            ],
            [
                'name' => 'Spalding NBA Official Basketball',
                'description' => 'Official NBA game ball with premium composite leather and deep channel design for superior grip and control.',
                'price' => 109.99,
                'images' => [], // Empty array for now - will use placeholder
                'category_id' => $basketballCategory?->id,
                'brand_id' => $spaldingBrand?->id,
                'sku' => 'SP-NBA-001',
                'is_active' => true,
                'is_featured' => true,
                'discount' => 20.00,
                'variants' => [
                    ['size' => 'Official Size', 'color' => 'Orange', 'stock' => 12, 'sku' => 'SP-NBA-001-OS', 'price_adjustment' => 0],
                ]
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Iconic Air Max design with visible Air unit and breathable mesh upper for all-day comfort.',
                'price' => 149.99,
                'images' => [], // Empty array for now - will use placeholder
                'category_id' => $runningCategory?->id,
                'brand_id' => $nikeBrand?->id,
                'sku' => 'NK-AM270-001',
                'is_active' => true,
                'is_featured' => false,
                'discount' => 0,
                'variants' => [
                    ['size' => '8', 'color' => 'White', 'stock' => 18, 'sku' => 'NK-AM270-001-8', 'price_adjustment' => 0],
                    ['size' => '9', 'color' => 'White', 'stock' => 22, 'sku' => 'NK-AM270-001-9', 'price_adjustment' => 0],
                    ['size' => '10', 'color' => 'White', 'stock' => 20, 'sku' => 'NK-AM270-001-10', 'price_adjustment' => 0],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);

            $product = Product::create($productData);

            // Create variants
            foreach ($variants as $variantData) {
                $product->variants()->create([
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'stock' => $variantData['stock'],
                    'sku' => $variantData['sku'],
                    'price_adjustment' => $variantData['price_adjustment'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
