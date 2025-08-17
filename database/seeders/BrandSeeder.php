<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                'description' => 'Just Do It - Premium sports equipment and apparel',
                'logo' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=200&fit=crop',
                'website' => 'https://nike.com',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Adidas',
                'description' => 'Impossible is Nothing - German sportswear manufacturer',
                'logo' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=200&h=200&fit=crop',
                'website' => 'https://adidas.com',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Under Armour',
                'description' => 'The Only Way Is Through - Performance athletic wear',
                'logo' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=200&h=200&fit=crop',
                'website' => 'https://underarmour.com',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Wilson',
                'description' => 'We are Wilson - Tennis and team sports equipment',
                'logo' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=200&h=200&fit=crop',
                'website' => 'https://wilson.com',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Spalding',
                'description' => 'Official basketball of the NBA',
                'logo' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=200&h=200&fit=crop',
                'website' => 'https://spalding.com',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($brands as $brand) {
            $brand['slug'] = Str::slug($brand['name']);
            Brand::create($brand);
        }
    }
}
