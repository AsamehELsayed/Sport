<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Basketball',
                'description' => 'Basketball equipment and gear',
                'image' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&h=400&fit=crop',
                'icon' => '🏀',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Soccer',
                'description' => 'Soccer equipment and gear',
                'image' => 'https://images.unsplash.com/photo-1579952363873-27d3bade8f55?w=400&h=400&fit=crop',
                'icon' => '⚽',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Running',
                'description' => 'Running shoes and accessories',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                'icon' => '🏃',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tennis',
                'description' => 'Tennis rackets and equipment',
                'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=400&fit=crop',
                'icon' => '🎾',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Fitness',
                'description' => 'General fitness equipment',
                'image' => 'https://images.unsplash.com/photo-1571019613452-1cb2f99b2d8b?w=400&h=400&fit=crop',
                'icon' => '💪',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            Category::create($category);
        }
    }
}
