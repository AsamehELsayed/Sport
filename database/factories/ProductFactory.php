<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'images' => ['products/placeholder.jpg'],
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'discount' => fake()->randomFloat(2, 0, 50),
        ];
    }
}
