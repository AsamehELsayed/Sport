<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sportapp.com',
            'password' => Hash::make('password'),
            'phone' => '+1 (555) 123-4567',
            'is_admin' => true,
            'date_of_birth' => '1985-03-15',
            'address_line_1' => '123 Admin Street',
            'address_line_2' => 'Suite 100',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'gender' => 'male',
            'bio' => 'System administrator for SportApp',
            'profile_photo' => null,
            'marketing_emails' => false,
            'order_updates' => true,
            'preferred_sports' => json_encode(['basketball', 'soccer', 'tennis']),
            'email_verified_at' => now(),
        ]);

        // Create Normal Customer User
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@sportapp.com',
            'password' => Hash::make('password'),
            'phone' => '+1 (555) 987-6543',
            'is_admin' => false,
            'date_of_birth' => '1992-07-22',
            'address_line_1' => '456 Customer Avenue',
            'address_line_2' => 'Apt 5B',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90210',
            'country' => 'USA',
            'gender' => 'male',
            'bio' => 'Sports enthusiast and fitness lover',
            'profile_photo' => null,
            'marketing_emails' => true,
            'order_updates' => true,
            'preferred_sports' => json_encode(['basketball', 'running', 'swimming']),
            'email_verified_at' => now(),
        ]);

        // Create Additional Customer Users
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@sportapp.com',
            'password' => Hash::make('password'),
            'phone' => '+1 (555) 456-7890',
            'is_admin' => false,
            'date_of_birth' => '1988-11-08',
            'address_line_1' => '789 Sports Lane',
            'city' => 'Chicago',
            'state' => 'IL',
            'postal_code' => '60601',
            'country' => 'USA',
            'gender' => 'female',
            'bio' => 'Professional tennis player and coach',
            'profile_photo' => null,
            'marketing_emails' => true,
            'order_updates' => true,
            'preferred_sports' => json_encode(['tennis', 'yoga', 'pilates']),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mike Rodriguez',
            'email' => 'mike@sportapp.com',
            'password' => Hash::make('password'),
            'phone' => '+1 (555) 321-0987',
            'is_admin' => false,
            'date_of_birth' => '1995-04-12',
            'address_line_1' => '321 Fitness Road',
            'city' => 'Miami',
            'state' => 'FL',
            'postal_code' => '33101',
            'country' => 'USA',
            'gender' => 'male',
            'bio' => 'Soccer player and fitness trainer',
            'profile_photo' => null,
            'marketing_emails' => false,
            'order_updates' => true,
            'preferred_sports' => json_encode(['soccer', 'weightlifting', 'cycling']),
            'email_verified_at' => now(),
        ]);

        // Create a few more users for variety
        User::factory(10)->create();
    }
}
