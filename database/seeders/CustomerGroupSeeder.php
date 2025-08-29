<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'VIP Customers',
                'description' => 'High-value customers with premium support',
                'color' => '#FFD700', // Gold
                'is_active' => true,
            ],
            [
                'name' => 'New Customers',
                'description' => 'Recently registered customers',
                'color' => '#10B981', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Sports Enthusiasts',
                'description' => 'Customers interested in multiple sports',
                'color' => '#3B82F6', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Basketball Fans',
                'description' => 'Customers who primarily buy basketball equipment',
                'color' => '#F59E0B', // Orange
                'is_active' => true,
            ],
            [
                'name' => 'Soccer Players',
                'description' => 'Customers who primarily buy soccer equipment',
                'color' => '#10B981', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Fitness Enthusiasts',
                'description' => 'Customers focused on fitness and training',
                'color' => '#8B5CF6', // Purple
                'is_active' => true,
            ],
            [
                'name' => 'Inactive Customers',
                'description' => 'Customers who haven\'t made purchases recently',
                'color' => '#6B7280', // Gray
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            CustomerGroup::create($group);
        }
    }
}
