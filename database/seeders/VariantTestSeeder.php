<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;

class VariantTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test product with variants
        $category = Category::firstOrCreate(['name' => 'Test Category'], [
            'slug' => 'test-category',
            'description' => 'Test category for variant testing',
            'is_active' => true,
        ]);

        $brand = Brand::firstOrCreate(['name' => 'Test Brand'], [
            'slug' => 'test-brand',
            'description' => 'Test brand for variant testing',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Test Product with Multi-Size Color Variants',
            'description' => 'This is a test product to demonstrate the new multi-size color variant system with images and default variants.',
            'price' => 99.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'sku' => 'TEST-VAR-001',
            'is_active' => true,
            'is_featured' => true,
            'discount' => 10,
        ]);

        // Define color groups with multiple sizes
        $colorGroups = [
            [
                'color' => 'Crimson',
                'sizes' => [
                    ['size' => 'S', 'stock' => 10, 'price_adjustment' => 0],
                    ['size' => 'M', 'stock' => 15, 'price_adjustment' => 5.00],
                    ['size' => 'L', 'stock' => 8, 'price_adjustment' => 10.00],
                ],
                'images' => ['product-variants/test-red-1.jpg', 'product-variants/test-red-2.jpg']
            ],
            [
                'color' => 'Royal Blue',
                'sizes' => [
                    ['size' => 'S', 'stock' => 12, 'price_adjustment' => 0],
                    ['size' => 'M', 'stock' => 20, 'price_adjustment' => 5.00],
                    ['size' => 'L', 'stock' => 14, 'price_adjustment' => 10.00],
                ],
                'images' => ['product-variants/test-blue-1.jpg']
            ],
            [
                'color' => 'Forest Green',
                'sizes' => [
                    ['size' => 'M', 'stock' => 18, 'price_adjustment' => 0],
                    ['size' => 'L', 'stock' => 16, 'price_adjustment' => 5.00],
                ],
                'images' => ['product-variants/test-green-1.jpg', 'product-variants/test-green-2.jpg']
            ]
        ];

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

        $this->command->info('Test product with multi-size color variants created successfully!');
        $this->command->info('Product ID: ' . $product->id);
        $this->command->info('Default variant: ' . $product->defaultVariant()->first()->display_name);
        $this->command->info('Total variants created: ' . $product->variants()->count());
    }
}
